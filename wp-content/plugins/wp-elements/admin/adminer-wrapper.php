<?php
/**
 * Обертка для безопасного доступа к Adminer
 * 
 * Проверяет права доступа перед подключением Adminer
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

// Проверяем права доступа (уже проверено в render_adminer_page, но для безопасности проверяем еще раз)
if (!current_user_can('manage_options')) {
    wp_die(__('У вас нет прав для доступа к Adminer.', 'my-monitoring-plugin'));
}

// Путь к Adminer
$adminer_path = MY_MONITORING_PLUGIN_DIR . 'adminer.php';

if (!file_exists($adminer_path)) {
    wp_die(__('Файл Adminer не найден.', 'my-monitoring-plugin'));
}

// Включаем Adminer
include $adminer_path;
exit;
