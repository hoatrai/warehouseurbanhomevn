<?php
/**
 * Класс для сжатия данных перед отправкой
 * 
 * @package My_Monitoring_Plugin
 */

// Запрещаем прямой доступ к файлу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для сжатия данных
 */
class My_Monitoring_Data_Compressor {
    
    /**
     * Минимальный размер данных для сжатия (100KB)
     * 
     * @var int
     */
    const MIN_SIZE_FOR_COMPRESSION = 102400;
    
    /**
     * Сжать данные, если они достаточно большие
     * 
     * @param string $data JSON строка данных
     * @return array Массив с данными и информацией о сжатии
     */
    public static function compress_if_needed($data) {
        $data_size = strlen($data);
        $compressed = false;
        $compression_ratio = 1.0;
        
        // Сжимаем только если данные больше минимального размера
        if ($data_size > self::MIN_SIZE_FOR_COMPRESSION && function_exists('gzencode')) {
            $compressed_data = @gzencode($data, 6); // Уровень сжатия 6 (баланс скорости и размера)
            
            if ($compressed_data !== false) {
                $compressed_size = strlen($compressed_data);
                $compression_ratio = $compressed_size / $data_size;
                
                // Используем сжатие только если оно дает выигрыш более 10%
                if ($compression_ratio < 0.9) {
                    $data = $compressed_data;
                    $compressed = true;
                }
            }
        }
        
        return array(
            'data' => $data,
            'compressed' => $compressed,
            'original_size' => $data_size,
            'compressed_size' => $compressed ? strlen($data) : $data_size,
            'compression_ratio' => $compression_ratio,
        );
    }
    
    /**
     * Распаковать данные, если они сжаты
     * 
     * @param string $data Данные (возможно сжатые)
     * @param bool $is_compressed Флаг сжатия
     * @return string|false Распакованные данные или false при ошибке
     */
    public static function decompress_if_needed($data, $is_compressed) {
        if (!$is_compressed) {
            return $data;
        }
        
        if (!function_exists('gzdecode')) {
            return false;
        }
        
        $decompressed = @gzdecode($data);
        return $decompressed !== false ? $decompressed : false;
    }
}
