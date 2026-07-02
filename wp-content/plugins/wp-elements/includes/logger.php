<?php
/**
 * Класс для логирования с уровнями и ротацией
 * 
 * @package My_Monitoring_Plugin
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для логирования
 */
class My_Monitoring_Logger {
    
    /**
     * Уровни логирования
     */
    const LEVEL_DEBUG = 0;
    const LEVEL_INFO = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_ERROR = 3;
    const LEVEL_CRITICAL = 4;
    
    /**
     * Единственный экземпляр класса
     * 
     * @var My_Monitoring_Logger|null
     */
    private static $instance = null;
    
    /**
     * Текущий уровень логирования
     * 
     * @var int
     */
    private $log_level;
    
    /**
     * Путь к директории логов
     * 
     * @var string
     */
    private $log_dir;
    
    /**
     * Максимальный размер файла лога в байтах (10MB)
     * 
     * @var int
     */
    private $max_file_size = 10485760;
    
    /**
     * Количество файлов для ротации
     * 
     * @var int
     */
    private $max_files = 5;
    
    /**
     * Получить экземпляр класса (Singleton)
     * 
     * @return My_Monitoring_Logger
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
        $this->log_level = get_option('my_monitoring_log_level', self::LEVEL_INFO);
        $this->log_dir = WP_CONTENT_DIR . '/my-monitoring-logs';
        
        // Создаем директорию, если её нет
        if (!file_exists($this->log_dir)) {
            wp_mkdir_p($this->log_dir);
            
            // Создаем .htaccess для защиты логов
            $htaccess_content = "Order deny,allow\nDeny from all";
            file_put_contents($this->log_dir . '/.htaccess', $htaccess_content);
            
            // Создаем index.php для защиты
            file_put_contents($this->log_dir . '/index.php', '<?php // Silence is golden');
        }
    }
    
    /**
     * Установить уровень логирования
     * 
     * @param int $level Уровень логирования
     */
    public function set_log_level($level) {
        $this->log_level = $level;
        update_option('my_monitoring_log_level', $level);
    }
    
    /**
     * Получить уровень логирования
     * 
     * @return int
     */
    public function get_log_level() {
        return $this->log_level;
    }
    
    /**
     * Логировать сообщение
     * 
     * @param string $message Сообщение
     * @param int $level Уровень логирования
     * @param array $context Дополнительный контекст
     */
    public function log($message, $level = self::LEVEL_INFO, $context = array()) {
        // Проверяем, нужно ли логировать на этом уровне
        if ($level < $this->log_level) {
            return;
        }
        
        $level_name = $this->get_level_name($level);
        $timestamp = current_time('mysql');
        $context_str = !empty($context) ? ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        
        $log_message = sprintf(
            '[%s] [%s] %s%s%s',
            $timestamp,
            $level_name,
            $message,
            $context_str,
            PHP_EOL
        );
        
        // Записываем в файл
        $this->write_to_file($log_message, $level);
        
        // Также записываем в error_log WordPress для критических ошибок
        if ($level >= self::LEVEL_ERROR) {
            error_log('My Monitoring Plugin [' . $level_name . ']: ' . $message);
        }
    }
    
    /**
     * Логировать отладочное сообщение
     * 
     * @param string $message Сообщение
     * @param array $context Дополнительный контекст
     */
    public function debug($message, $context = array()) {
        $this->log($message, self::LEVEL_DEBUG, $context);
    }
    
    /**
     * Логировать информационное сообщение
     * 
     * @param string $message Сообщение
     * @param array $context Дополнительный контекст
     */
    public function info($message, $context = array()) {
        $this->log($message, self::LEVEL_INFO, $context);
    }
    
    /**
     * Логировать предупреждение
     * 
     * @param string $message Сообщение
     * @param array $context Дополнительный контекст
     */
    public function warning($message, $context = array()) {
        $this->log($message, self::LEVEL_WARNING, $context);
    }
    
    /**
     * Логировать ошибку
     * 
     * @param string $message Сообщение
     * @param array $context Дополнительный контекст
     */
    public function error($message, $context = array()) {
        $this->log($message, self::LEVEL_ERROR, $context);
    }
    
    /**
     * Логировать критическую ошибку
     * 
     * @param string $message Сообщение
     * @param array $context Дополнительный контекст
     */
    public function critical($message, $context = array()) {
        $this->log($message, self::LEVEL_CRITICAL, $context);
    }
    
    /**
     * Получить имя уровня логирования
     * 
     * @param int $level Уровень
     * @return string
     */
    private function get_level_name($level) {
        $levels = array(
            self::LEVEL_DEBUG => 'DEBUG',
            self::LEVEL_INFO => 'INFO',
            self::LEVEL_WARNING => 'WARNING',
            self::LEVEL_ERROR => 'ERROR',
            self::LEVEL_CRITICAL => 'CRITICAL',
        );
        
        return isset($levels[$level]) ? $levels[$level] : 'UNKNOWN';
    }
    
    /**
     * Записать сообщение в файл с ротацией
     * 
     * @param string $message Сообщение
     * @param int $level Уровень логирования
     */
    private function write_to_file($message, $level) {
        $log_file = $this->log_dir . '/monitoring-' . date('Y-m-d') . '.log';
        
        // Проверяем размер файла и делаем ротацию при необходимости
        if (file_exists($log_file) && filesize($log_file) > $this->max_file_size) {
            $this->rotate_logs();
        }
        
        // Записываем в файл
        file_put_contents($log_file, $message, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Ротация логов
     */
    private function rotate_logs() {
        $today = date('Y-m-d');
        $log_file = $this->log_dir . '/monitoring-' . $today . '.log';
        
        // Переименовываем текущий файл
        if (file_exists($log_file)) {
            $rotated_file = $this->log_dir . '/monitoring-' . $today . '-' . time() . '.log';
            rename($log_file, $rotated_file);
        }
        
        // Удаляем старые файлы, если их больше максимума
        $log_files = glob($this->log_dir . '/monitoring-*.log');
        if (count($log_files) > $this->max_files) {
            // Сортируем по времени изменения
            usort($log_files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Удаляем самые старые
            $files_to_delete = array_slice($log_files, 0, count($log_files) - $this->max_files);
            foreach ($files_to_delete as $file) {
                @unlink($file);
            }
        }
    }
    
    /**
     * Очистить все логи
     * 
     * @return bool Успешность операции
     */
    public function clear_logs() {
        $log_files = glob($this->log_dir . '/monitoring-*.log');
        foreach ($log_files as $file) {
            @unlink($file);
        }
        return true;
    }
    
    /**
     * Получить последние записи лога
     * 
     * @param int $lines Количество строк
     * @return array Массив строк лога
     */
    public function get_recent_logs($lines = 100) {
        $log_file = $this->log_dir . '/monitoring-' . date('Y-m-d') . '.log';
        
        if (!file_exists($log_file)) {
            return array();
        }
        
        $file_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($file_lines === false) {
            return array();
        }
        
        return array_slice($file_lines, -$lines);
    }
}
