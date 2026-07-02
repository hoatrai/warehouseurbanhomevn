<?php
/**
 * Класс для шифрования и дешифрования данных
 * 
 * @package My_Monitoring_Plugin
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для работы с шифрованием
 */
class My_Monitoring_Encryption {
    
    /**
     * Шифровать данные
     * 
     * @param string $data Данные для шифрования
     * @return string|false Зашифрованные данные или false при ошибке
     */
    public static function encrypt($data) {
        if (empty($data)) {
            return false;
        }
        
        // Используем WordPress salt для создания ключа
        $key = wp_salt('auth');
        
        // Проверяем доступность OpenSSL
        if (!function_exists('openssl_encrypt')) {
            // Fallback: простое кодирование, если OpenSSL недоступен
            return base64_encode($data);
        }
        
        $method = 'AES-256-CBC';
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        
        $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
        
        if ($encrypted === false) {
            return false;
        }
        
        // Сохраняем IV вместе с зашифрованными данными
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Дешифровать данные
     * 
     * @param string $encrypted_data Зашифрованные данные
     * @return string|false Расшифрованные данные или false при ошибке
     */
    public static function decrypt($encrypted_data) {
        if (empty($encrypted_data)) {
            return false;
        }
        
        // Используем WordPress salt для создания ключа
        $key = wp_salt('auth');
        
        // Проверяем доступность OpenSSL
        if (!function_exists('openssl_decrypt')) {
            // Fallback: простое декодирование, если OpenSSL недоступен
            return base64_decode($encrypted_data);
        }
        
        $method = 'AES-256-CBC';
        $data = base64_decode($encrypted_data);
        
        if ($data === false) {
            return false;
        }
        
        $iv_length = openssl_cipher_iv_length($method);
        $iv = substr($data, 0, $iv_length);
        $encrypted = substr($data, $iv_length);
        
        $decrypted = openssl_decrypt($encrypted, $method, $key, 0, $iv);
        
        return $decrypted !== false ? $decrypted : false;
    }
    
    /**
     * Сохранить зашифрованный API ключ
     * 
     * @param string $api_key API ключ для сохранения
     * @return bool Успешность операции
     */
    public static function save_api_key($api_key) {
        if (empty($api_key)) {
            delete_option('_my_monitoring_api_key_encrypted');
            delete_option('my_monitoring_api_key'); // Удаляем старую незашифрованную версию
            return true;
        }
        
        $encrypted = self::encrypt($api_key);
        if ($encrypted === false) {
            return false;
        }
        
        // Сохраняем зашифрованную версию в приватной опции
        update_option('_my_monitoring_api_key_encrypted', $encrypted);
        
        // Удаляем старую незашифрованную версию, если она существует
        delete_option('my_monitoring_api_key');
        
        return true;
    }
    
    /**
     * Получить расшифрованный API ключ
     * 
     * @return string|false API ключ или false при ошибке
     */
    public static function get_api_key() {
        // Сначала пробуем получить зашифрованную версию
        $encrypted = get_option('_my_monitoring_api_key_encrypted');
        
        if (!empty($encrypted)) {
            $decrypted = self::decrypt($encrypted);
            if ($decrypted !== false) {
                return $decrypted;
            }
        }
        
        // Fallback: пробуем получить старую незашифрованную версию (для миграции)
        $old_key = get_option('my_monitoring_api_key');
        if (!empty($old_key)) {
            // Мигрируем на зашифрованную версию
            self::save_api_key($old_key);
            return $old_key;
        }
        
        return false;
    }
}
