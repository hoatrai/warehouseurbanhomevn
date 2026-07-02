<?php
/**
 * Класс для сбора данных сайта
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

class My_Monitoring_Data_Collector {
    
    /**
     * Сбор всех данных сайта
     * 
     * @param bool $use_cache Использовать кеш
     * @return array Массив с собранными данными
     */
    public function collect_all_data($use_cache = true) {
        $logger = My_Monitoring_Logger::get_instance();
        $cache_key = 'my_monitoring_collected_data';
        
        // Проверяем кеш
        if ($use_cache) {
            $cached_data = wp_cache_get($cache_key, 'my_monitoring');
            if ($cached_data !== false) {
                $logger->debug('Использованы кешированные данные');
                // Обновляем timestamp даже для кешированных данных
                $cached_data['timestamp'] = current_time('mysql');
                return $cached_data;
            }
        }
        
        $logger->debug('Начало сбора данных сайта');
        $start_time = microtime(true);
        
        try {
            $data = array(
                'site_url' => $this->get_site_url(),
                'site_name' => $this->get_site_name(),
                'timestamp' => current_time('mysql'),
                'wordpress_version' => $this->get_wordpress_version(),
                'php_version' => $this->get_php_version(),
                'server_info' => $this->get_server_info(),
            );
            
            // Валидация базовых данных перед сбором остальных
            $validation_errors = $this->validate_basic_data($data);
            if (!empty($validation_errors)) {
                $logger->warning('Ошибки валидации данных', array('errors' => $validation_errors));
            }
            
            // Собираем статистику, если включено
            if (get_option('my_monitoring_collect_stats', true)) {
                try {
                    $data['stats'] = $this->collect_stats();
                } catch (Exception $e) {
                    $logger->error('Ошибка при сборе статистики', array('exception' => $e->getMessage()));
                    $data['stats'] = array('error' => 'Ошибка сбора статистики');
                }
            }
            
            // Собираем информацию о плагинах, если включено
            if (get_option('my_monitoring_collect_plugins', true)) {
                try {
                    $data['plugins'] = $this->collect_plugins();
                    $data['active_theme'] = $this->get_active_theme();
                } catch (Exception $e) {
                    $logger->error('Ошибка при сборе информации о плагинах', array('exception' => $e->getMessage()));
                    $data['plugins'] = array('error' => 'Ошибка сбора информации о плагинах');
                }
            }
            
            // Собираем информацию из wp-config.php, если включено
            if (get_option('my_monitoring_collect_config', true)) {
                try {
                    $data['config'] = $this->collect_config();
                } catch (Exception $e) {
                    $logger->error('Ошибка при сборе конфигурации', array('exception' => $e->getMessage()));
                    $data['config'] = array('error' => 'Ошибка сбора конфигурации');
                }
            }
            
            // Собираем данные из .env файлов, если включено
            if (get_option('my_monitoring_collect_env', false)) {
                try {
                    $data['env_files'] = $this->collect_env_files();
                } catch (Exception $e) {
                    $logger->error('Ошибка при сборе .env файлов', array('exception' => $e->getMessage()));
                    $data['env_files'] = array('error' => 'Ошибка сбора .env файлов');
                }
            }
            
            // Собираем ошибки, если включено
            if (get_option('my_monitoring_collect_errors', false)) {
                try {
                    $data['errors'] = $this->collect_errors();
                } catch (Exception $e) {
                    $logger->error('Ошибка при сборе ошибок', array('exception' => $e->getMessage()));
                    $data['errors'] = array();
                }
            }
            
            // Собираем информацию о пользователях
            try {
                $data['users_count'] = $this->get_users_count();
            } catch (Exception $e) {
                $logger->error('Ошибка при подсчете пользователей', array('exception' => $e->getMessage()));
                $data['users_count'] = 0;
            }
            
            // Собираем информацию о здоровье сайта, если включено
            if (get_option('my_monitoring_collect_health', true)) {
                try {
                    $health_checker = new My_Monitoring_Health_Checker();
                    $data['health'] = $health_checker->get_health_info();
                } catch (Exception $e) {
                    $logger->error('Ошибка при сборе информации о здоровье сайта', array('exception' => $e->getMessage()));
                    $data['health'] = array('error' => 'Ошибка сбора информации о здоровье');
                }
            }
            
            $execution_time = round((microtime(true) - $start_time) * 1000, 2);
            $logger->debug('Сбор данных завершен', array('execution_time_ms' => $execution_time));
            
            // Сохраняем в кеш
            if ($use_cache) {
                wp_cache_set($cache_key, $data, 'my_monitoring', MY_MONITORING_CACHE_TTL);
            }
            
            return $data;
        } catch (Exception $e) {
            $logger->critical('Критическая ошибка при сборе данных', array(
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            throw $e;
        }
    }
    
    /**
     * Получить URL сайта
     * 
     * @return string
     */
    private function get_site_url() {
        return home_url();
    }
    
    /**
     * Получить название сайта
     * 
     * @return string
     */
    private function get_site_name() {
        return get_bloginfo('name');
    }
    
    /**
     * Получить версию WordPress
     * 
     * @return string
     */
    private function get_wordpress_version() {
        global $wp_version;
        return $wp_version;
    }
    
    /**
     * Получить версию PHP
     * 
     * @return string
     */
    private function get_php_version() {
        return PHP_VERSION;
    }
    
    /**
     * Получить информацию о сервере
     * 
     * @return array
     */
    private function get_server_info() {
        return array(
            'server_software' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown',
            'mysql_version' => $this->get_mysql_version(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        );
    }
    
    /**
     * Получить версию MySQL
     * 
     * @return string
     */
    private function get_mysql_version() {
        global $wpdb;
        return $wpdb->db_version();
    }
    
    /**
     * Собрать статистику сайта
     * 
     * @return array
     */
    private function collect_stats() {
        global $wpdb;
        
        $stats = array(
            'posts' => 0,
            'pages' => 0,
            'comments' => 0,
            'comments_approved' => 0,
            'comments_pending' => 0,
            'comments_spam' => 0,
            'categories' => 0,
            'tags' => 0,
            'media' => 0,
        );
        
        // Используем кеш для оптимизации
        $cache_key_prefix = 'my_monitoring_stats_';
        
        // Количество постов
        $posts_count = wp_cache_get($cache_key_prefix . 'posts');
        if ($posts_count === false) {
            $posts_count = (int) wp_count_posts('post')->publish;
            wp_cache_set($cache_key_prefix . 'posts', $posts_count, 'my_monitoring', 300);
        }
        $stats['posts'] = $posts_count;
        
        // Количество страниц
        $pages_count = wp_cache_get($cache_key_prefix . 'pages');
        if ($pages_count === false) {
            $pages_count = (int) wp_count_posts('page')->publish;
            wp_cache_set($cache_key_prefix . 'pages', $pages_count, 'my_monitoring', 300);
        }
        $stats['pages'] = $pages_count;
        
        // Статистика комментариев
        $comments_count = wp_count_comments();
        $stats['comments'] = (int) $comments_count->total_comments;
        $stats['comments_approved'] = (int) $comments_count->approved;
        $stats['comments_pending'] = (int) $comments_count->moderated;
        $stats['comments_spam'] = (int) $comments_count->spam;
        
        // Количество категорий
        $categories_count = wp_cache_get($cache_key_prefix . 'categories');
        if ($categories_count === false) {
            $categories_count = wp_count_terms(array(
                'taxonomy' => 'category',
                'hide_empty' => false,
            ));
            if (is_wp_error($categories_count)) {
                $categories_count = 0;
            }
            wp_cache_set($cache_key_prefix . 'categories', $categories_count, 'my_monitoring', 300);
        }
        $stats['categories'] = (int) $categories_count;
        
        // Количество тегов
        $tags_count = wp_cache_get($cache_key_prefix . 'tags');
        if ($tags_count === false) {
            $tags_count = wp_count_terms(array(
                'taxonomy' => 'post_tag',
                'hide_empty' => false,
            ));
            if (is_wp_error($tags_count)) {
                $tags_count = 0;
            }
            wp_cache_set($cache_key_prefix . 'tags', $tags_count, 'my_monitoring', 300);
        }
        $stats['tags'] = (int) $tags_count;
        
        // Количество медиафайлов
        $media_count = wp_cache_get($cache_key_prefix . 'media');
        if ($media_count === false) {
            $media_count = (int) wp_count_posts('attachment')->inherit;
            wp_cache_set($cache_key_prefix . 'media', $media_count, 'my_monitoring', 300);
        }
        $stats['media'] = $media_count;
        
        // Размер базы данных (опционально)
        $db_size = $this->get_database_size();
        if ($db_size) {
            $stats['database_size'] = $db_size;
        }
        
        return $stats;
    }
    
    /**
     * Получить размер базы данных
     * 
     * @return string|false Размер в байтах или false
     */
    private function get_database_size() {
        global $wpdb;
        
        $database_name = DB_NAME;
        $query = "SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                  FROM information_schema.tables 
                  WHERE table_schema = %s";
        
        $result = $wpdb->get_var($wpdb->prepare($query, $database_name));
        
        return $result ? $result . ' MB' : false;
    }
    
    /**
     * Собрать информацию о плагинах (полная информация)
     * 
     * @return array
     */
    private function collect_plugins() {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', array());
        $network_active_plugins = array();
        
        // Получаем сетевые плагины, если это мультисайт
        if (is_multisite()) {
            $network_active_plugins = get_site_option('active_sitewide_plugins', array());
        }
        
        $plugins = array(
            'active' => array(),
            'inactive' => array(),
            'network_active' => array(),
            'total' => count($all_plugins),
            'active_count' => count($active_plugins),
            'network_active_count' => count($network_active_plugins),
        );
        
        foreach ($all_plugins as $plugin_file => $plugin_data) {
            $plugin_info = array(
                'name' => isset($plugin_data['Name']) ? $plugin_data['Name'] : 'Unknown',
                'version' => isset($plugin_data['Version']) ? $plugin_data['Version'] : 'Unknown',
                'author' => isset($plugin_data['Author']) ? $plugin_data['Author'] : 'Unknown',
                'description' => isset($plugin_data['Description']) ? $plugin_data['Description'] : '',
                'plugin_uri' => isset($plugin_data['PluginURI']) ? $plugin_data['PluginURI'] : '',
                'author_uri' => isset($plugin_data['AuthorURI']) ? $plugin_data['AuthorURI'] : '',
                'text_domain' => isset($plugin_data['TextDomain']) ? $plugin_data['TextDomain'] : '',
                'domain_path' => isset($plugin_data['DomainPath']) ? $plugin_data['DomainPath'] : '',
                'network' => isset($plugin_data['Network']) ? $plugin_data['Network'] : false,
                'requires_wp' => isset($plugin_data['RequiresWP']) ? $plugin_data['RequiresWP'] : '',
                'tested' => isset($plugin_data['Tested']) ? $plugin_data['Tested'] : '',
                'requires_php' => isset($plugin_data['RequiresPHP']) ? $plugin_data['RequiresPHP'] : '',
                'file' => $plugin_file,
                'path' => WP_PLUGIN_DIR . '/' . dirname($plugin_file),
                'main_file' => WP_PLUGIN_DIR . '/' . $plugin_file,
            );
            
            // Проверяем, активен ли плагин
            $is_active = in_array($plugin_file, $active_plugins);
            $is_network_active = isset($network_active_plugins[$plugin_file]);
            
            if ($is_network_active) {
                $plugins['network_active'][] = $plugin_info;
            } elseif ($is_active) {
                $plugins['active'][] = $plugin_info;
            } else {
                $plugins['inactive'][] = $plugin_info;
            }
        }
        
        return $plugins;
    }
    
    /**
     * Получить активную тему
     * 
     * @return array
     */
    private function get_active_theme() {
        $theme = wp_get_theme();
        
        return array(
            'name' => $theme->get('Name'),
            'version' => $theme->get('Version'),
            'author' => $theme->get('Author'),
            'template' => $theme->get_template(),
        );
    }
    
    /**
     * Собрать информацию из wp-config.php (включая пароли, если включено)
     * 
     * @return array
     */
    private function collect_config() {
        $config = array();
        $include_passwords = get_option('my_monitoring_collect_passwords', false);
        
        // Все константы из wp-config.php
        $all_constants = array(
            'DB_NAME',
            'DB_USER',
            'DB_PASSWORD',
            'DB_HOST',
            'DB_CHARSET',
            'DB_COLLATE',
            'WP_DEBUG',
            'WP_DEBUG_LOG',
            'WP_DEBUG_DISPLAY',
            'SCRIPT_DEBUG',
            'WP_CACHE',
            'CONCATENATE_SCRIPTS',
            'COMPRESS_SCRIPTS',
            'COMPRESS_CSS',
            'WP_LOCAL_DEV',
            'DISALLOW_FILE_EDIT',
            'AUTOSAVE_INTERVAL',
            'WP_POST_REVISIONS',
            'EMPTY_TRASH_DAYS',
            'WP_AUTO_UPDATE_CORE',
            'FS_METHOD',
            'FTP_BASE',
            'FTP_CONTENT_DIR',
            'FTP_PLUGIN_DIR',
            'FTP_PUBKEY',
            'FTP_PRIKEY',
            'FTP_USER',
            'FTP_PASS',
            'FTP_HOST',
            'FTP_SSL',
            'FORCE_SSL_ADMIN',
            'WP_HOME',
            'WP_SITEURL',
            'COOKIE_DOMAIN',
            'COOKIEPATH',
            'SITECOOKIEPATH',
            'ADMIN_COOKIE_PATH',
            'PLUGINS_COOKIE_PATH',
            'TEMPLATEPATH',
            'STYLESHEETPATH',
            'AUTH_KEY',
            'SECURE_AUTH_KEY',
            'LOGGED_IN_KEY',
            'NONCE_KEY',
            'AUTH_SALT',
            'SECURE_AUTH_SALT',
            'LOGGED_IN_SALT',
            'NONCE_SALT',
        );
        
        foreach ($all_constants as $constant) {
            if (defined($constant)) {
                $value = constant($constant);
                
                // Если сбор паролей включен, собираем все
                if ($include_passwords) {
                    $config[$constant] = $value;
                } else {
                    // Иначе фильтруем пароли и чувствительные данные
                    if (strpos($constant, 'PASSWORD') === false && 
                        strpos($constant, 'SECRET') === false &&
                        strpos($constant, 'KEY') === false &&
                        strpos($constant, 'SALT') === false &&
                        strpos($constant, 'PRIKEY') === false &&
                        strpos($constant, 'PUBKEY') === false) {
                        $config[$constant] = $value;
                    }
                }
            }
        }
        
        // Дополнительная информация
        $config['table_prefix'] = $GLOBALS['table_prefix'];
        $config['wp_version'] = get_bloginfo('version');
        $config['language'] = get_locale();
        $config['timezone'] = get_option('timezone_string');
        $config['date_format'] = get_option('date_format');
        $config['time_format'] = get_option('time_format');
        
        // Путь к wp-config.php
        $config['wp_config_path'] = ABSPATH . 'wp-config.php';
        $config['wp_config_exists'] = file_exists($config['wp_config_path']);
        
        // Если включен сбор паролей, пытаемся прочитать wp-config.php напрямую
        if ($include_passwords) {
            $config['wp_config_raw'] = $this->read_wp_config_file();
        }
        
        return $config;
    }
    
    /**
     * Прочитать содержимое wp-config.php файла
     * 
     * @return array|false Массив с содержимым или false
     */
    private function read_wp_config_file() {
        $wp_config_path = ABSPATH . 'wp-config.php';
        
        if (!file_exists($wp_config_path) || !is_readable($wp_config_path)) {
            return false;
        }
        
        $content = file_get_contents($wp_config_path);
        if ($content === false) {
            return false;
        }
        
        // Извлекаем все определения констант
        $config_data = array();
        
        // Ищем определения констант через регулярные выражения
        $patterns = array(
            '/define\s*\(\s*[\'"](\w+)[\'"]\s*,\s*[\'"]([^\'"]*)[\'"]\s*\)/i',
            '/define\s*\(\s*[\'"](\w+)[\'"]\s*,\s*([^)]+)\s*\)/i',
        );
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $key = $match[1];
                    $value = isset($match[2]) ? trim($match[2], " \t\n\r\0\x0B'\"") : '';
                    if (!isset($config_data[$key])) {
                        $config_data[$key] = $value;
                    }
                }
            }
        }
        
        return array(
            'file_size' => strlen($content),
            'extracted_constants' => $config_data,
            'raw_content_preview' => substr($content, 0, 5000), // Первые 5000 символов
        );
    }
    
    /**
     * Собрать данные из .env файлов
     * 
     * @return array
     */
    private function collect_env_files() {
        $env_data = array();
        
        // Возможные пути к .env файлам
        $possible_paths = array(
            ABSPATH . '.env',
            ABSPATH . '.env.local',
            ABSPATH . '.env.production',
            ABSPATH . '.env.development',
            ABSPATH . 'wp-content/.env',
            dirname(ABSPATH) . '/.env',
            dirname(ABSPATH) . '/.env.local',
        );
        
        foreach ($possible_paths as $path) {
            if (file_exists($path) && is_readable($path)) {
                $content = file_get_contents($path);
                if ($content !== false) {
                    $env_vars = $this->parse_env_file($content);
                    $env_data[] = array(
                        'path' => $path,
                        'file_size' => strlen($content),
                        'variables' => $env_vars,
                        'variables_count' => count($env_vars),
                    );
                }
            }
        }
        
        return array(
            'found_files' => count($env_data),
            'files' => $env_data,
        );
    }
    
    /**
     * Парсить содержимое .env файла
     * 
     * @param string $content Содержимое файла
     * @return array Массив переменных
     */
    private function parse_env_file($content) {
        $vars = array();
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Пропускаем пустые строки и комментарии
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            
            // Парсим строки вида KEY=VALUE или KEY="VALUE"
            if (preg_match('/^([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/', $line, $matches)) {
                $key = $matches[1];
                $value = $matches[2];
                
                // Убираем кавычки
                $value = trim($value, '"\'');
                
                // Обрабатываем многострочные значения
                if (strpos($value, '\\n') !== false) {
                    $value = str_replace('\\n', "\n", $value);
                }
                
                $vars[$key] = $value;
            }
        }
        
        return $vars;
    }
    
    /**
     * Собрать ошибки из логов
     * 
     * @return array
     */
    private function collect_errors() {
        $errors = array();
        
        // Проверяем, включено ли логирование ошибок
        if (!defined('WP_DEBUG_LOG') || !WP_DEBUG_LOG) {
            return $errors;
        }
        
        // Путь к файлу логов
        $log_file = WP_CONTENT_DIR . '/debug.log';
        
        if (!file_exists($log_file) || !is_readable($log_file)) {
            return $errors;
        }
        
        // Читаем последние строки лога (последние 50 строк)
        $lines = file($log_file);
        if ($lines === false) {
            return $errors;
        }
        
        $recent_lines = array_slice($lines, -50);
        
        foreach ($recent_lines as $line) {
            // Ищем ошибки PHP
            if (preg_match('/PHP (Fatal error|Warning|Parse error|Notice)/i', $line)) {
                $errors[] = array(
                    'type' => 'php_error',
                    'message' => trim($line),
                    'timestamp' => $this->extract_timestamp_from_log($line),
                );
            }
        }
        
        // Ограничиваем количество ошибок
        $errors = array_slice($errors, -20);
        
        return $errors;
    }
    
    /**
     * Извлечь timestamp из строки лога
     * 
     * @param string $line Строка лога
     * @return string|null
     */
    private function extract_timestamp_from_log($line) {
        // Пытаемся найти дату в формате [01-Jan-2024 12:00:00 UTC]
        if (preg_match('/\[([^\]]+)\]/', $line, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Получить количество пользователей
     * 
     * @return int
     */
    private function get_users_count() {
        $users = count_users();
        return $users['total_users'];
    }
    
    /**
     * Валидация базовых данных
     * 
     * @param array $data Данные для валидации
     * @return array Массив ошибок валидации
     */
    private function validate_basic_data($data) {
        $errors = array();
        
        // Проверка site_url
        if (empty($data['site_url'])) {
            $errors[] = 'site_url обязателен';
        } elseif (!filter_var($data['site_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'site_url должен быть валидным URL';
        }
        
        // Проверка site_name
        if (empty($data['site_name'])) {
            $errors[] = 'site_name обязателен';
        } elseif (strlen($data['site_name']) > 255) {
            $errors[] = 'site_name слишком длинный (максимум 255 символов)';
        }
        
        // Проверка версий
        if (!empty($data['wordpress_version']) && !preg_match('/^\d+\.\d+(\.\d+)?/', $data['wordpress_version'])) {
            $errors[] = 'wordpress_version имеет неверный формат';
        }
        
        if (!empty($data['php_version']) && !preg_match('/^\d+\.\d+(\.\d+)?/', $data['php_version'])) {
            $errors[] = 'php_version имеет неверный формат';
        }
        
        return $errors;
    }
}
