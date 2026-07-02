<?php
require_once("wp-load.php"); // chạy trong WordPress context

global $wpdb;

// Lấy tất cả record trong bảng history
$rows = $wpdb->get_results("SELECT id, data FROM wp_dulieunhadat_history");

foreach ($rows as $row) {
    $data = maybe_unserialize($row->data);

    if (is_array($data) && !empty($data['userupdate'])) {
        // userupdate có thể là ID hoặc username
        if (is_numeric($data['userupdate'])) {
            $user = get_user_by('id', intval($data['userupdate']));
        } else {
            $user = get_user_by('login', $data['userupdate']);
        }

        if ($user) {
            // Avatar xử lý theo plugin simple_local_avatar
            $avatar_meta = get_user_meta($user->ID, 'simple_local_avatar', true);
            if (!empty($avatar_meta) && !empty($avatar_meta['full'])) {
                $user_avatar_url = $avatar_meta['full'];
            } else {
                $user_avatar_url = get_avatar_url($user->ID, ['size' => 96]);
            }

            // Cập nhật lại data
            $data['userupdate']      = $user->ID; // giữ nguyên
            $data['user_login']      = $user->user_login;
            $data['first_name']      = $user->first_name;
            $data['last_name']       = $user->last_name;
            $data['phone']           = get_user_meta($user->ID, 'dien_thoai', true);
            $data['user_avatar_url'] = $user_avatar_url;
            $data['dateupdate']      = $data['dateupdate']; // giữ nguyên
        }
    }

    // Serialize lại
    $new_data = maybe_serialize($data);

    // Debug
    echo "------ RECORD #{$row->id} ------\n";
    echo "\n";

    // Update DB (khi test xong thì bỏ comment)
    $wpdb->update(
        'wp_dulieunhadat_history',
        ['data' => $new_data],
        ['id' => $row->id]
    );

}

echo "✅ Done update tất cả record";
