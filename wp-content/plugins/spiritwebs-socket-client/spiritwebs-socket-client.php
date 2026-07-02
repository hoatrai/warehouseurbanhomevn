<?php
/*
Plugin Name: SpiritWebs Socket Client
Description: Gửi sự kiện real-time về SpiritWebs khi người dùng đăng nhập (cả frontend lẫn admin), hiển thị user online trên admin bar.
Version: 1.4
Author: SpiritWebs
*/

// --- Hiển thị script JS ở cả frontend và admin ---
add_action('admin_footer', 'spiritwebs_socket_client_script');
add_action('wp_footer', 'spiritwebs_socket_client_script');

function spiritwebs_socket_client_script() {
    if (!is_user_logged_in()) return;

    $user = wp_get_current_user();
    $username = esc_js($user->user_login);
    $email = esc_js($user->user_email);
    $plugin_url = plugin_dir_url(__FILE__);

    echo <<<HTML
<script>
window.spiritwebs_user_data = {
    username: "{$username}",
    email: "{$email}"
};
console.log("👤 SpiritWebs user (from plugin):", window.spiritwebs_user_data);
</script>
<script src="{$plugin_url}spiritwebs-socket-client.js" defer></script>
HTML;
}

// --- Thêm admin bar hiển thị số user online và submenu ---
add_action('admin_bar_menu', function($wp_admin_bar){
    if (!current_user_can('administrator')) return;

    // Node chính hiển thị số user online
    $wp_admin_bar->add_node([
        'id' => 'online-users',
        'title' => '👥 Online: <span id="sw-online-count">0</span>',
        'href' => false
    ]);

    // Node con rỗng → JS sẽ append <li> trực tiếp
    $wp_admin_bar->add_node([
        'id' => 'online-users-submenu',
        'parent' => 'online-users',
        'title' => 'Người dùng online', // tạm, sẽ bị JS override
        'href' => false
    ]);
}, 100);

// --- Hàm gửi broadcast tới Phoenix server ---
function send_broadcast_to_phoenix($row){
    $payload = json_encode($row);
    $ch = curl_init("https://socket.okinawanew.com/api/broadcast");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if(curl_errno($ch)){
        error_log('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

// --- Hook khi có post mới (broadcast dữ liệu) ---
add_action('save_post', function($post_id){
    if(wp_is_post_revision($post_id)) return;

    $post = get_post($post_id);
    if(!$post) return;

    $row = [
        'id' => $post_id,
        'name' => $post->post_title,
        'price' => get_post_meta($post_id,'price',true) ?: 0
    ];

    send_broadcast_to_phoenix($row);
});
