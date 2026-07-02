<?php
/**
 * Класс для проверки здоровья сайта и сбора дополнительных метрик
 * 
 * @package My_Monitoring_Plugin
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для проверки здоровья сайта
 */
class My_Monitoring_Health_Checker {
    
    /**
     * Получить информацию о здоровье сайта
     * 
     * @return array Массив с информацией о здоровье
     */
    public function get_health_info() {
        $logger = My_Monitoring_Logger::get_instance();
        $health = array(
            'site_health' => $this->get_site_health_status(),
            'disk_usage' => $this->get_disk_usage(),
            'performance' => $this->get_performance_metrics(),
            'security' => $this->get_security_info(),
            'updates' => $this->get_updates_status(),
        );
        
        $logger->debug('Информация о здоровье сайта собрана');
        return $health;
    }
    
    /**
     * Получить статус здоровья сайта через WordPress Site Health API
     * 
     * @return array
     */
    private function get_site_health_status() {
        $status = array(
            'score' => null,
            'status' => 'unknown',
            'issues' => array(),
        );
        
        // Используем WordPress Site Health API, если доступен (WordPress 5.2+)
        if (class_exists('WP_Site_Health')) {
            $site_health = WP_Site_Health::get_instance();
            
            // Получаем общий статус
            $test_results = $site_health->get_tests();
            
            // Получаем результаты тестов
            if (method_exists($site_health, 'get_test_results')) {
                $results = $site_health->get_test_results();
                $status['score'] = isset($results['status']) ? $results['status'] : null;
            }
        }
        
        // Альтернативный способ через опции
        $health_status = get_option('health-check-site-status-result', array());
        if (!empty($health_status) && isset($health_status['status'])) {
            $status['status'] = $health_status['status'];
            $status['score'] = $health_status['status'] === 'good' ? 100 : ($health_status['status'] === 'recommended' ? 75 : 50);
        }
        
        return $status;
    }
    
    /**
     * Получить информацию об использовании дискового пространства
     * 
     * @return array
     */
    private function get_disk_usage() {
        $usage = array(
            'available' => null,
            'total' => null,
            'used_percent' => null,
            'wp_content_size' => null,
        );
        
        // Получаем информацию о диске
        if (function_exists('disk_free_space') && function_exists('disk_total_space')) {
            $upload_dir = wp_upload_dir();
            $base_dir = $upload_dir['basedir'];
            
            // Получаем свободное и общее пространство
            $free_space = @disk_free_space($base_dir);
            $total_space = @disk_total_space($base_dir);
            
            if ($free_space !== false && $total_space !== false) {
                $used_space = $total_space - $free_space;
                $usage['available'] = $this->format_bytes($free_space);
                $usage['total'] = $this->format_bytes($total_space);
                $usage['used_percent'] = round(($used_space / $total_space) * 100, 2);
            }
        }
        
        // Размер директории wp-content
        $wp_content_size = $this->get_directory_size(WP_CONTENT_DIR);
        if ($wp_content_size !== false) {
            $usage['wp_content_size'] = $this->format_bytes($wp_content_size);
        }
        
        return $usage;
    }
    
    /**
     * Получить метрики производительности
     * 
     * @return array
     */
    private function get_performance_metrics() {
        $metrics = array(
            'memory_limit' => ini_get('memory_limit'),
            'memory_usage' => $this->format_bytes(memory_get_usage(true)),
            'memory_peak' => $this->format_bytes(memory_get_peak_usage(true)),
            'max_execution_time' => ini_get('max_execution_time'),
            'execution_time' => null,
        );
        
        // Время выполнения можно измерить при сборе данных
        if (defined('MY_MONITORING_START_TIME')) {
            $metrics['execution_time'] = round((microtime(true) - MY_MONITORING_START_TIME) * 1000, 2);
        }
        
        return $metrics;
    }
    
    /**
     * Получить информацию о безопасности
     * 
     * @return array
     */
    private function get_security_info() {
        $security = array(
            'wp_debug' => defined('WP_DEBUG') && WP_DEBUG,
            'wp_debug_log' => defined('WP_DEBUG_LOG') && WP_DEBUG_LOG,
            'wp_debug_display' => defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY,
            'file_edit_disabled' => defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT,
            'outdated_plugins' => $this->get_outdated_plugins_count(),
            'outdated_themes' => $this->get_outdated_themes_count(),
            'ssl_enabled' => is_ssl(),
        );
        
        return $security;
    }
    
    /**
     * Получить статус обновлений
     * 
     * @return array
     */
    private function get_updates_status() {
        $updates = array(
            'core' => array(
                'available' => false,
                'version' => get_bloginfo('version'),
            ),
            'plugins' => array(
                'available' => false,
                'count' => 0,
            ),
            'themes' => array(
                'available' => false,
                'count' => 0,
            ),
        );
        
        // Проверяем обновления ядра
        $core_updates = get_core_updates();
        if (!empty($core_updates)) {
            $updates['core']['available'] = true;
            $updates['core']['latest_version'] = $core_updates[0]->current;
        }
        
        // Проверяем обновления плагинов
        $plugin_updates = get_plugin_updates();
        if (!empty($plugin_updates)) {
            $updates['plugins']['available'] = true;
            $updates['plugins']['count'] = count($plugin_updates);
        }
        
        // Проверяем обновления тем
        $theme_updates = get_theme_updates();
        if (!empty($theme_updates)) {
            $updates['themes']['available'] = true;
            $updates['themes']['count'] = count($theme_updates);
        }
        
        return $updates;
    }
    
    /**
     * Получить количество устаревших плагинов
     * 
     * @return int
     */
    private function get_outdated_plugins_count() {
        $plugin_updates = get_plugin_updates();
        return !empty($plugin_updates) ? count($plugin_updates) : 0;
    }
    
    /**
     * Получить количество устаревших тем
     * 
     * @return int
     */
    private function get_outdated_themes_count() {
        $theme_updates = get_theme_updates();
        return !empty($theme_updates) ? count($theme_updates) : 0;
    }
    
    /**
     * Получить размер директории
     * 
     * @param string $directory Путь к директории
     * @return int|false Размер в байтах или false
     */
    private function get_directory_size($directory) {
        if (!is_dir($directory)) {
            return false;
        }
        
        $size = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    /**
     * Форматировать байты в читаемый формат
     * 
     * @param int $bytes Байты
     * @return string
     */
    private function format_bytes($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}
