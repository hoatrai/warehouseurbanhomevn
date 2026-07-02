/**
 * JavaScript для админ-панели плагина мониторинга
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Обработчик кнопки "Отправить данные сейчас"
        $('#my-monitoring-send-now').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $status = $('#my-monitoring-send-status');
            
            // Блокируем кнопку
            $button.prop('disabled', true).text(myMonitoring.strings.sending);
            $status.html('');
            
            // Отправляем AJAX запрос
            $.ajax({
                url: myMonitoring.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'my_monitoring_send_now',
                    nonce: myMonitoring.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $status.html('<span style="color: green;">✓ ' + myMonitoring.strings.success + '</span>');
                        // Перезагружаем страницу через 2 секунды для обновления информации
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $status.html('<span style="color: red;">✗ ' + (response.data.message || myMonitoring.strings.error) + '</span>');
                    }
                },
                error: function() {
                    $status.html('<span style="color: red;">✗ ' + myMonitoring.strings.error + '</span>');
                },
                complete: function() {
                    // Разблокируем кнопку
                    $button.prop('disabled', false).text('Отправить данные сейчас');
                }
            });
        });
        
        // Обработчик кнопки "Тест подключения"
        $('#my-monitoring-test-connection').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $status = $('#my-monitoring-send-status');
            
            // Блокируем кнопку
            $button.prop('disabled', true).text('Проверка...');
            $status.html('<span style="color: blue;">Проверка подключения...</span>');
            
            // Отправляем AJAX запрос
            $.ajax({
                url: myMonitoring.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'my_monitoring_test_connection',
                    nonce: myMonitoring.testNonce
                },
                success: function(response) {
                    if (response.success) {
                        $status.html('<span style="color: green;">✓ Подключение успешно</span>');
                    } else {
                        $status.html('<span style="color: red;">✗ ' + (response.data.message || 'Ошибка подключения') + '</span>');
                    }
                },
                error: function() {
                    $status.html('<span style="color: red;">✗ Ошибка проверки подключения</span>');
                },
                complete: function() {
                    // Разблокируем кнопку
                    $button.prop('disabled', false).text('Тест подключения');
                }
            });
        });
    });
})(jQuery);
