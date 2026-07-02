<?php
/**
 * Класс для отправки данных в MongoDB через REST API
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

class My_Monitoring_Sender {
    
    /**
     * URL API endpoint
     * 
     * @var string
     */
    private $api_url;
    
    /**
     * API ключ для аутентификации
     * 
     * @var string
     */
    private $api_key;
    
    /**
     * Тип API: 'standard' или 'jsonbin'
     * 
     * @var string
     */
    private $api_type;
    
    /**
     * Таймаут запроса в секундах
     * 
     * @var int
     */
    private $timeout = 30;
    
    /**
     * Количество попыток отправки
     * 
     * @var int
     */
    private $max_retries = 3;
    
    /**
     * Интервал между попытками в секундах
     * 
     * @var int
     */
    private $retry_delay = 60;
    
    /**
     * Конструктор
     * 
     * @param string $api_url URL API endpoint
     * @param string $api_key API ключ
     * @param int $max_retries Максимальное количество попыток
     * @param int $retry_delay Задержка между попытками в секундах
     */
    public function __construct($api_url, $api_key, $max_retries = 3, $retry_delay = 60) {
        $this->api_url = $api_url;
        $this->api_key = $api_key;
        $this->max_retries = get_option('my_monitoring_max_retries', $max_retries);
        $this->retry_delay = get_option('my_monitoring_retry_delay', $retry_delay);
        
        // Определяем тип API
        $this->api_type = $this->detect_api_type($api_url);
        
        // Для стандартного API добавляем trailing slash
        if ($this->api_type === 'standard') {
            $this->api_url = trailingslashit($api_url);
        }
    }
    
    /**
     * Определить тип API по URL
     * 
     * @param string $api_url URL API
     * @return string 'standard' или 'jsonbin'
     */
    private function detect_api_type($api_url) {
        // Проверяем настройку из опций (приоритет)
        $api_type_option = get_option('my_monitoring_api_type', '');
        if (!empty($api_type_option) && in_array($api_type_option, array('standard', 'jsonbin'))) {
            return $api_type_option;
        }
        
        // Автоопределение по URL
        if (strpos($api_url, 'jsonbin.io') !== false) {
            return 'jsonbin';
        }
        
        return 'standard';
    }
    
    /**
     * Отправить данные в API с механизмом повторных попыток
     * 
     * @param array $data Данные для отправки
     * @return array Результат отправки
     */
    public function send($data) {
        $logger = My_Monitoring_Logger::get_instance();
        $attempt = 0;
        $last_error = null;
        $start_time = microtime(true);
        
        $logger->debug('Начало отправки данных', array(
            'api_url' => $this->api_url,
            'max_retries' => $this->max_retries,
            'data_size' => strlen(json_encode($data))
        ));
        
        while ($attempt < $this->max_retries) {
            $attempt++;
            
            $result = $this->attempt_send($data);
            
            // Если успешно, возвращаем результат
            if ($result['success']) {
                $execution_time = round((microtime(true) - $start_time) * 1000, 2);
                $logger->info('Данные успешно отправлены', array(
                    'attempt' => $attempt,
                    'execution_time_ms' => $execution_time,
                    'response_code' => $result['response_code'] ?? 0
                ));
                
                // Очищаем очередь неудачных отправок при успехе
                $this->clear_failed_queue();
                return $result;
            }
            
            // Сохраняем последнюю ошибку
            $last_error = $result;
            
            // Если это не последняя попытка, ждем перед следующей
            if ($attempt < $this->max_retries) {
                // Сохраняем в очередь для повторной попытки
                $this->save_to_failed_queue($data, $result);
                
                // Логируем попытку
                $logger->warning('Попытка отправки не удалась', array(
                    'attempt' => $attempt,
                    'max_retries' => $this->max_retries,
                    'retry_delay' => $this->retry_delay,
                    'error_message' => $result['message'],
                    'response_code' => $result['response_code'] ?? 0
                ));
                
                // В cron задачах не ждем, просто планируем следующую попытку
                if (defined('DOING_CRON') && DOING_CRON) {
                    // Планируем повторную попытку через заданное время
                    wp_schedule_single_event(time() + $this->retry_delay, 'my_monitoring_retry_send');
                    $logger->debug('Запланирована повторная попытка через cron', array(
                        'delay_seconds' => $this->retry_delay
                    ));
                    break;
                } else {
                    // В синхронных запросах ждем
                    $sleep_time = min($this->retry_delay, 5); // Максимум 5 секунд в синхронном режиме
                    sleep($sleep_time);
                }
            }
        }
        
        // Все попытки исчерпаны
        $execution_time = round((microtime(true) - $start_time) * 1000, 2);
        $this->save_to_failed_queue($data, $last_error);
        $logger->error('Все попытки отправки исчерпаны', array(
            'total_attempts' => $attempt,
            'execution_time_ms' => $execution_time,
            'final_error' => $last_error['message'] ?? 'Неизвестная ошибка',
            'response_code' => $last_error['response_code'] ?? 0
        ));
        
        return $last_error;
    }
    
    /**
     * Попытка отправки данных
     * 
     * @param array $data Данные для отправки
     * @return array Результат отправки
     */
    private function attempt_send($data) {
        $logger = My_Monitoring_Logger::get_instance();
        $result = array(
            'success' => false,
            'message' => '',
            'response_code' => 0,
        );
        
        // Проверяем наличие cURL
        if (!function_exists('curl_init')) {
            $error_msg = __('cURL не доступен на этом сервере', 'my-monitoring-plugin');
            $logger->error($error_msg);
            $result['message'] = $error_msg;
            return $result;
        }
        
        // Подготавливаем данные для отправки в зависимости от типа API
        if ($this->api_type === 'jsonbin') {
            // Для JSONBin отправляем только data (без api_key в теле)
            $payload = array(
                'data' => $data,
            );
        } else {
            // Для стандартного API отправляем api_key и data
            $payload = array(
                'api_key' => $this->api_key,
                'data' => $data,
            );
        }
        
        $json_data = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if ($json_data === false) {
            $error_msg = __('Ошибка кодирования данных в JSON', 'my-monitoring-plugin') . ': ' . json_last_error_msg();
            $logger->error($error_msg, array('json_error' => json_last_error()));
            $result['message'] = $error_msg;
            return $result;
        }
        
        // Сжимаем данные, если они достаточно большие
        $compression_result = My_Monitoring_Data_Compressor::compress_if_needed($json_data);
        $json_data = $compression_result['data'];
        $is_compressed = $compression_result['compressed'];
        
        $logger->debug('Подготовка HTTP запроса', array(
            'url' => $this->api_url,
            'original_size' => $compression_result['original_size'],
            'payload_size' => strlen($json_data),
            'compressed' => $is_compressed,
            'compression_ratio' => $compression_result['compression_ratio']
        ));
        
        // Выполняем запрос
        try {
            $response = $this->make_request($json_data, $is_compressed);
            
            // Для JSONBin логируем дополнительную информацию
            if ($this->api_type === 'jsonbin') {
                $logger->debug('Отправка в JSONBin', array(
                    'api_type' => 'jsonbin',
                    'url' => $this->api_url
                ));
            }
            
            if (is_wp_error($response)) {
                $error_code = $response->get_error_code();
                $error_message = $response->get_error_message();
                $logger->error('Ошибка HTTP запроса', array(
                    'error_code' => $error_code,
                    'error_message' => $error_message
                ));
                $result['message'] = $error_message;
                return $result;
            }
            
            $response_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);
            
            $result['response_code'] = $response_code;
            
            // Проверяем код ответа
            if ($response_code >= 200 && $response_code < 300) {
                $result['success'] = true;
                $result['message'] = __('Данные успешно отправлены', 'my-monitoring-plugin');
                
                // Пытаемся декодировать ответ
                $decoded = json_decode($response_body, true);
                if ($decoded !== null) {
                    $result['response'] = $decoded;
                }
                
                $logger->debug('Успешный ответ от API', array(
                    'response_code' => $response_code,
                    'response_size' => strlen($response_body)
                ));
            } else {
                $result['message'] = sprintf(
                    __('Ошибка отправки данных. Код ответа: %d', 'my-monitoring-plugin'),
                    $response_code
                );
                
                // Пытаемся получить сообщение об ошибке из ответа
                $decoded = json_decode($response_body, true);
                if ($decoded !== null && isset($decoded['message'])) {
                    $result['message'] = $decoded['message'];
                }
                
                $logger->warning('Ошибка ответа от API', array(
                    'response_code' => $response_code,
                    'response_body' => substr($response_body, 0, 500) // Первые 500 символов
                ));
            }
        } catch (Exception $e) {
            $logger->critical('Исключение при отправке данных', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            $result['message'] = __('Критическая ошибка при отправке: ', 'my-monitoring-plugin') . $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Сохранить данные в очередь неудачных отправок
     * 
     * @param array $data Данные
     * @param array $error Информация об ошибке
     */
    private function save_to_failed_queue($data, $error) {
        $queue = get_option('my_monitoring_failed_queue', array());
        
        // Ограничиваем размер очереди
        if (count($queue) >= MY_MONITORING_MAX_QUEUE_SIZE) {
            array_shift($queue);
        }
        
        $queue[] = array(
            'data' => $data,
            'error' => $error,
            'timestamp' => current_time('mysql'),
            'attempts' => 1
        );
        
        update_option('my_monitoring_failed_queue', $queue);
    }
    
    /**
     * Очистить очередь неудачных отправок
     */
    private function clear_failed_queue() {
        delete_option('my_monitoring_failed_queue');
    }
    
    /**
     * Выполнить HTTP запрос
     * 
     * @param string $json_data JSON данные для отправки
     * @param bool $is_compressed Флаг сжатия данных
     * @return array|WP_Error Ответ сервера или ошибка
     */
    private function make_request($json_data, $is_compressed = false) {
        // Определяем метод и заголовки в зависимости от типа API
        if ($this->api_type === 'jsonbin') {
            // Для JSONBin используем PUT для обновления bin или POST для создания в коллекции
            $method = 'PUT';
            // Если URL содержит /c/{id}/b, это создание нового bin в коллекции - используем POST
            if (preg_match('#/c/[^/]+/b/?$#', $this->api_url)) {
                $method = 'POST';
            }
            
            $headers = array(
                'Content-Type' => 'application/json',
                'X-Master-Key' => $this->api_key,
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            );
            
            // JSONBin не поддерживает сжатие через Content-Encoding
            // Но можно оставить сжатие, если оно было применено
            if ($is_compressed) {
                // Для JSONBin лучше не использовать сжатие, так как это может вызвать проблемы
                // Но если данные уже сжаты, отправляем как есть
                $headers['Content-Encoding'] = 'gzip';
            }
        } else {
            // Стандартный API
            $method = 'POST';
            $headers = array(
                'Content-Type' => 'application/json' . ($is_compressed ? '; charset=utf-8' : ''),
                'Content-Encoding' => $is_compressed ? 'gzip' : '',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            );
        }
        
        $headers['Content-Length'] = strlen($json_data);
        
        $args = array(
            'method' => $method,
            'timeout' => $this->timeout,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => $headers,
            'body' => $json_data,
            'cookies' => array(),
            'sslverify' => true, // В продакшене должно быть true
        );
        
        // Если API URL использует HTTPS, проверяем сертификат
        if (strpos($this->api_url, 'https://') === 0) {
            $args['sslverify'] = true;
        }
        
        $response = wp_remote_request($this->api_url, $args);
        
        return $response;
    }
    
    /**
     * Проверить доступность API
     * 
     * @return array Результат проверки
     */
    public function test_connection() {
        $test_data = array(
            'test' => true,
            'timestamp' => current_time('mysql'),
        );
        
        return $this->send($test_data);
    }
}
