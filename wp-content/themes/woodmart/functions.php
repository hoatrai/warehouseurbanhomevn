<?php
/**
 *
 * The framework's functions and definitions
 */

define( 'WOODMART_THEME_DIR', get_template_directory_uri() );
define( 'WOODMART_THEMEROOT', get_template_directory() );
define( 'WOODMART_IMAGES', WOODMART_THEME_DIR . '/images' );
define( 'WOODMART_SCRIPTS', WOODMART_THEME_DIR . '/js' );
define( 'WOODMART_STYLES', WOODMART_THEME_DIR . '/css' );
define( 'WOODMART_FRAMEWORK', '/inc' );
define( 'WOODMART_DUMMY', WOODMART_THEME_DIR . '/inc/dummy-content' );
define( 'WOODMART_CLASSES', WOODMART_THEMEROOT . '/inc/classes' );
define( 'WOODMART_CONFIGS', WOODMART_THEMEROOT . '/inc/configs' );
define( 'WOODMART_HEADER_BUILDER', WOODMART_THEME_DIR . '/inc/header-builder' );
define( 'WOODMART_ASSETS', WOODMART_THEME_DIR . '/inc/admin/assets' );
define( 'WOODMART_ASSETS_IMAGES', WOODMART_ASSETS . '/images' );
define( 'WOODMART_API_URL', 'https://xtemos.com/licenses/api/' );
define( 'WOODMART_DEMO_URL', 'https://woodmart.xtemos.com/' );
define( 'WOODMART_PLUGINS_URL', WOODMART_DEMO_URL . 'plugins/' );
define( 'WOODMART_DUMMY_URL', WOODMART_DEMO_URL . 'dummy-content-new/' );
define( 'WOODMART_TOOLTIP_URL', WOODMART_DEMO_URL . 'theme-settings-tooltips/' );
define( 'WOODMART_SLUG', 'woodmart' );
define( 'WOODMART_CORE_VERSION', '1.0.36' );
define( 'WOODMART_WPB_CSS_VERSION', '1.0.2' );

if ( ! function_exists( 'woodmart_load_classes' ) ) {
    function woodmart_load_classes() {
        $classes = array(
            'Singleton.php',
            'Api.php',
            'Googlefonts.php',
            'Config.php',
            'Layout.php',
            'License.php',
            'Notices.php',
            'Options.php',
            'Stylesstorage.php',
            'Theme.php',
            'Themesettingscss.php',
            'Vctemplates.php',
            'Wpbcssgenerator.php',
            'Registry.php',
            'Pagecssfiles.php',
        );

        foreach ( $classes as $class ) {
            require WOODMART_CLASSES . DIRECTORY_SEPARATOR . $class;
        }
    }
}

woodmart_load_classes();

new WOODMART_Theme();

define( 'WOODMART_VERSION', woodmart_get_theme_info( 'Version' ) );



//change logo admin
// Change Admin Bar Logo
function custom_admin_logo() {
    echo '
    <style>
        #wp-admin-bar-wp-logo > .ab-item .ab-icon {
            background-image: url("https://warehouse.urbanhome.vn/wp-content/uploads/2025/03/LogoHead.svg") !important;
            background-size: cover !important;
            background-position: center !important;
            width: 20px !important;
            height: 20px !important;
        }
    </style>';
}
add_action("admin_head", "custom_admin_logo");

// Change Login Page Logo
function custom_login_logo() {
    echo '
    <style>
        #login h1 a {
            background-image: url("https://warehouse.urbanhome.vn/wp-content/uploads/2025/03/LogoHead.svg") !important;
            background-size: contain !important;
            width: 300px !important;
            height: 80px !important;
            display: block;
        }
    </style>';
}
add_action("login_head", "custom_login_logo");


/*function load_fontawesome_cdn() {
    wp_enqueue_style(
        'fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        array(),
        '4.7.0'
    );
}
add_action('wp_enqueue_scripts', 'load_fontawesome_cdn');*/

// Change Login Logo Link
function custom_login_logo_url() {
    return home_url(); // Redirect to your site
}
add_filter("login_headerurl", "custom_login_logo_url");

add_action('admin_menu', function() {
    // Ẩn các mục con của menu Dashboard
    remove_submenu_page('index.php', 'update-core.php'); // Ẩn mục cập nhật
    remove_submenu_page('index.php', 'wp-admin/admin.php?page=wp-logo'); // Ví dụ ẩn một mục con khác (nếu có)
    // Thêm các dòng remove_submenu_page khác để ẩn các mục con khác nếu cần.
});

//custom menu admin
function my_custom_admin_menu() {
    /*if (current_user_can('administrator') || current_user_can('giamdoc')) {
        add_menu_page(
            'Tin thị trường',       // Page title
            '<span class="nhadat">Tin</span> <span class="dulieu">thị trường</span>',              // Menu title
            'manage_options',       // Capability
            'tin_thi_truong',       // Menu slug
            'my_custom_page_content_wp_tin_thi_truong', // Function to display content
            'dashicons-format-aside', // Dashicon icon
            4                    // Position in menu order
        );
    }*/
    add_menu_page(
        'Dự án',       // Page title
        '<span class="nhadat">Dự</span> <span class="dulieu">án</span>',              // Menu title
        'manage_options',       // Capability
        'wp_duan',       // Menu slug
        'my_custom_page_content_wp_duan', // Function to display content
        'dashicons-building', // Dashicon icon
        5                     // Position in menu order
    );
    add_menu_page(
        'Dữ liệu nhà đất',
        '<span class="dulieu">Dữ liệu</span> <span class="nhadat">nhà đất</span>',
        'manage_options',
        'wp_dulieunhadat',
        'my_custom_page_content_wp_dulieunhadat',
        'dashicons-admin-home', // Dashicon icon
        6
    );

    add_menu_page(
        'Tin nội bộ',
        '<span class="nhadat">Tin</span> <span class="dulieu">nội bộ</span>',
        'manage_options',
        'tin_noi_bo_page',
        'my_custom_page_content_wp_tin_noi_bo',
        'dashicons-format-status',

        7
    );
    add_menu_page(
        'Khách hàng của bạn',
        '<span class="dulieu">Khách</span> <span class="nhadat">hàng</span>',
        'manage_options',
        'wp_khachhang',
        'my_custom_page_content_wp_khachhang',
        'dashicons-businessperson',

        7
    );


    if (current_user_can('administrator')) {
        add_menu_page(
            'Phân Quyền Vị Trí Chức Vụ',   // Tiêu đề trang
            '<span class="nhadat">Phân</span> <span class="dulieu">quyền</span>',           // Tên menu
            'manage_options',              // Quyền truy cập
            'role_caps_manager',           // slug
            'render_role_caps_page',       // hàm hiển thị nội dung
            'dashicons-shield-alt',        // icon menu
            8                           // vị trí menu
        );
    }
    /*add_menu_page(
        'Quản lý nhân sự',
        'Nhân Sự',
        'edit_users',
        'nhansu-main',
        '__return_null', // Or your custom function
        'dashicons-groups',
        7
    );

    // Manually add link to User Role Editor (must be active)
    add_submenu_page(
        'nhansu-main',
        'Phân quyền',
        'Phân quyền',
        'edit_users',
        'users.php?page=users-user-role-editor.php'
    );*/
}
add_action('admin_footer', 'customize_menu_titles_html');

function customize_menu_titles_html() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            // Menu "Nhân sự"
            $('#adminmenu a.menu-top[href="users.php"] .wp-menu-name').html('<span class="dulieu">Nhân</span>' +
                ' <span class="nhadat">sự</span>');
        });
    </script>
    <?php
}

// hide language in user
add_action('admin_head', function () {
    global $pagenow;

    if (in_array($pagenow, ['user-edit.php', 'profile.php', 'user-new.php'])) {
        ?>
        <style>
            /* Hide the Language field row */
            tr.user-language-wrap {
                display: none !important;
            }

        </style>
        <?php
    }
});



// neu khong tim thay trang thì redirect ve trang chu
add_action('template_redirect', 'redirect_404_to_homepage');
function redirect_404_to_homepage() {
    if (is_404()) {
        wp_redirect(home_url());
        exit;
    }
}


// Xóa các role mặc định và ẩn các role khác
function remove_default_user_roles() {
    if( current_user_can( 'administrator' ) ) {
        // Xóa các role mặc định
        remove_role( 'subscriber' );
        remove_role( 'contributor' );

        // Vô hiệu hóa quyền cho Biên Tập Viên (Editor) và Tác Giả (Author)
        $editor = get_role('editor');
        if ($editor) {
            $editor->remove_cap('edit_posts'); // Không cho phép biên tập bài viết
            $editor->remove_cap('publish_posts'); // Không cho phép xuất bản bài viết
        }

        $author = get_role('author');
        if ($author) {
            $author->remove_cap('edit_posts'); // Không cho phép biên tập bài viết
            $author->remove_cap('publish_posts'); // Không cho phép xuất bản bài viết
        }
    }
}
add_action('admin_init', 'remove_default_user_roles');

// Ẩn role "Editor" và "Author" khỏi danh sách có thể chọn trong phần quản lý người dùng
function hide_roles_from_users( $roles ) {
    unset( $roles['editor'] ); // Xóa role Editor
    unset( $roles['author'] );  // Xóa role Author
    unset( $roles['shop_manager'] );
    return $roles;
}
add_filter( 'editable_roles', 'hide_roles_from_users' );

/*// Thay đổi tên hiển thị của vai trò "Administrator" thành "Quản trị cấp cao"
function custom_admin_role_name($role_names) {
    if (isset($role_names['administrator'])) {
        $role_names['administrator'] = 'Quản trị cấp cao'; // Đổi tên hiển thị vai trò Administrator
    }
    return $role_names;
}
add_filter('editable_roles', 'custom_admin_role_name');*/



// ẩn mật khẩu ứng dụng trong trang edit user
// Ẩn các trường không cần thiết trong trang chỉnh user
add_action('admin_head', function () {
    $screen = get_current_screen();
    if ($screen && in_array($screen->id, ['user-edit', 'profile'])) {
        echo '<style>
            /* Ẩn Phím tắt bình luận */
            tr.user-comment-shortcuts-wrap,
            /* Ẩn Trình soạn thảo nâng cao */
            tr.user-rich-editing-wrap,
            /* Ẩn Thanh công cụ (admin bar) */
            tr.show-admin-bar,
            /* Ẩn Mật khẩu ứng dụng */
            #application-passwords-section,
            tr.user-application-passwords-wrap {
                display: none !important;
            }
        </style>';
    }
});


function hide_default_admin_bar_items_except_logo($wp_admin_bar) {
    // Danh sách node cần ẩn, TRỪ logo
    $nodes_to_remove = [
        'about',
        'wporg',
        'documentation',
        'support-forums',
        'feedback',
        'wp-logo',
        'updates',
        'comments',
        'new-content',
        'my-account',
        'languages',
        'xts_dashboard'
    ];

    foreach ($nodes_to_remove as $node) {
        $wp_admin_bar->remove_node($node);
    }

    // Không ẩn 'wp-logo' để giữ lại logo
}
add_action('admin_bar_menu', 'hide_default_admin_bar_items_except_logo', 999);

function replace_admin_bar_site_name_with_logo() {
    ?>
    <style>

        #wp-admin-bar-site-name > .ab-item {
            background-image: url('https://warehouse.urbanhome.vn/wp-content/uploads/2025/07/LogoHead.svg');
            background-repeat: no-repeat;
            background-position: center left;
            background-size: contain;
            text-indent: -9999px;
            width: 160px;
            background-color: transparent !important;
        }

        #wp-admin-bar-site-name > .ab-item:hover {
            background-image: url('https://warehouse.urbanhome.vn/wp-content/uploads/2025/07/LogoHead.svg') !important;
            background-repeat: no-repeat !important;
            background-position: center left !important;
            background-size: contain !important;
            background-color: transparent !important;
        }
    </style>
    <?php
}
add_action('admin_head', 'replace_admin_bar_site_name_with_logo');
add_action('wp_before_admin_bar_render', 'replace_admin_bar_site_name_with_logo');

function remove_footer_admin_text() {
    echo '<style>
        #footer-thankyou,#footer-upgrade{ display: none !important; }
    </style>';
}
add_action('admin_head', 'remove_footer_admin_text');


function my_custom_admin_scripts($hook) {
    /*if ($hook !== 'toplevel_page_wp_dulieunhadat' || $hook !== 'toplevel_page_wp_duan') {
        return;
    }*/

    // Bootstrap 4 or 5
    wp_enqueue_style('bootstrap-css', get_template_directory_uri() . '/custom/css/bootstrap.min.css',
        array(),
        null
    );
    wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/custom/js/bootstrap.bundle.min.js', array('jquery'), null, true);


    // Ensure jQuery UI CSS and JS are loaded
    wp_enqueue_style(
        'jquery-ui-css',
        'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'
    );
    /*   wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/custom/css/font-awesome.min.css',
           array(),
           null
       );*/
    wp_enqueue_script('jquery-ui', get_template_directory_uri() . '/custom/js/jquery-ui.min.js', array('jquery'), null, true);
    wp_enqueue_style( 'lightbox2-css', get_template_directory_uri() . '/custom/css/lightbox.min.css',
        array(),
        null
    );
    wp_enqueue_style( 'jqgrid-css', get_template_directory_uri() . '/custom/css/ui.jqgrid.min.css',
        array(),
        null
    );

    wp_enqueue_script( 'lightbox2-js', get_template_directory_uri() . '/custom/js/lightbox.min.js', array('jquery'), null, true);
    wp_enqueue_script( 'jqgrid-js', get_template_directory_uri() . '/custom/js/jquery.jqgrid.min.js', array('jquery'), null, true);
    wp_enqueue_style(
        'custom-css',
        get_template_directory_uri() . '/custom/css/custom.css',
        array(),
        null
    );
    // jqGrid CSS & JS
    /* wp_enqueue_style(
         'jqgrid-css',
         get_template_directory_uri() . '/custom/css/ui.jqgrid.min.css',
         array(),
         null
     );
     // jqGrid
     wp_enqueue_script(
         'jqgrid-js',
         get_template_directory_uri() . '/custom/js/jquery.jqgrid.min.js',
         array('jquery', 'jquery-ui'),
         null,
         true
     );*/
    wp_enqueue_script(
        'jqgrid-locale-vi',
        get_template_directory_uri() . '/custom/js/i18n/grid.locale-vi.js',
        array('jqgrid-js'),
        null,
        true
    );

    wp_enqueue_style('font-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
    //wp_enqueue_script('jqgrid-js', 'https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/js/jquery.jqgrid.min.js', array('jquery', 'jquery-ui'), null, true);

    // Enqueue Select2 if needed
    wp_enqueue_style('select2-css', get_template_directory_uri() . '/custom/css/select2.min.css',
        array(),
        null
    );
    wp_enqueue_script('select2-js', get_template_directory_uri() . '/custom/js/select2.min.js', array('jquery'), null, true);

    // Load media uploader của WP để dùng trong upload ảnh
    wp_enqueue_media();

    // Enqueue custom script for jqGrid
    wp_enqueue_script('my-admin-jqgrid', get_template_directory_uri() . '/admin-jqgrid.js', array('jquery', 'jqgrid-js', 'jquery-ui'), null, true);

    wp_enqueue_script(
        'chart',
        get_template_directory_uri() . '/custom/js/chart.js',
        array('jquery'),
        null,
        true
    );

    // Localize script for AJAX if needed
    wp_localize_script('my-admin-jqgrid', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'can_edit'  => function_exists('spiritwebs_user_can') && spiritwebs_user_can('edit_data'),
        'can_delete'  => function_exists('spiritwebs_user_can') && spiritwebs_user_can('delete_data'),
        'can_view'  => function_exists('spiritwebs_user_can') && spiritwebs_user_can('view_data'),
        'is_admin' => current_user_can('administrator'),
        'current_user_id' => get_current_user_id(), // 👈 thêm dòng này
    ));
}

add_action('admin_menu', 'my_custom_admin_menu');
add_action('admin_enqueue_scripts', 'my_custom_admin_scripts');


function add_custom_class_to_admin_menu() {
    global $menu;

    foreach ($menu as $key => $value) {
        if ($value[2] === 'wp_dulieunhadat') { // Match menu slug
            $menu[$key][4] .= ' wp_dulieunhadat'; // Append custom class
        }

    }
}
add_action('admin_menu', 'add_custom_class_to_admin_menu', 999);

function doi_ten_menu_user_thanh_nhan_su() {
    global $menu, $submenu;

    // Đổi menu chính
    foreach ($menu as $key => $item) {
        if ($item[2] == 'users.php') {
            $menu[$key][0] = 'Nhân sự';
        }
    }

    // Đổi submenu (cẩn thận với key vì có thể khác ngôn ngữ)
    if (isset($submenu['users.php'])) {
        foreach ($submenu['users.php'] as $key => $item) {
            // Đổi "All Users"
            if ($item[2] === 'users.php') {
                $submenu['users.php'][$key][0] = 'Danh sách nhân sự';
            }
            // Đổi "Add New"
            if ($item[2] === 'user-new.php') {
                //if (function_exists('spiritwebs_user_can') && spiritwebs_user_can('add_user')) {
                $submenu['users.php'][$key][0] = 'Thêm nhân sự';
                //}
            }
            // Đổi "Profile"
            if ($item[2] === 'profile.php') {
                $submenu['users.php'][$key][0] = 'Thông tin cá nhân';
            }
        }
    }
}
add_action('admin_menu', 'doi_ten_menu_user_thanh_nhan_su', 999);


add_filter('manage_users_custom_column', function ($value, $column_name, $user_id) {

    if ($column_name === 'trang_thai_lam_viec') {
        $status = get_user_meta($user_id, 'trang_thai_lam_viec', true);

        if ($status === 'da_nghi') {
            return '<span style="color:#c0392b;font-weight:600;">Đã nghỉ</span>';
        }

        return '<span style="color:#27ae60;font-weight:600;">Còn làm</span>';
    }

    return $value;

}, 10, 3);


add_filter('manage_users_custom_column', function ($value, $column_name, $user_id) {

    if ($column_name === 'quan_ly_id') {
        $manager_id = get_user_meta($user_id, 'quan_ly_id', true);

        if (!$manager_id) {
            return '<em>—</em>';
        }

        $manager = get_user_by('ID', $manager_id);

        return $manager ? esc_html($manager->display_name) : '<em>—</em>';
    }

    return $value;

}, 10, 3);


// Đổi tên và sắp xếp lại các cột trong bảng Users
/*function doi_ten_cot_users($columns) {
    $new_columns = [];
// Giữ lại cột checkbox
    if (isset($columns['cb'])) {
        $new_columns['cb'] = $columns['cb'];
    }
    // Tạo lại thứ tự cột theo mong muốn
    $new_columns['username'] = 'Thông tin';
    $new_columns['name']     = 'Nhân sự';
    $new_columns['email']    = 'Email';
    $new_columns['sinh_nhat'] = 'Năm sinh';
    $new_columns['role']     = 'Chức vụ';
    $new_columns['quan_ly_id'] = 'Thuộc quản lý';   // 👈 CỘT MỚI
    $new_columns['ngay_vao_lam'] = 'Ngày vào làm';
    $new_columns['trang_thai_lam_viec'] = 'Trạng thái';


    // Loại bỏ cột bài viết
    // Không thêm lại cột 'posts' nữa

    return $new_columns;
}
add_filter('manage_users_columns', 'doi_ten_cot_users');*/
function doi_ten_cot_users($columns) {
    $new_columns = [];

    if (isset($columns['cb'])) {
        $new_columns['cb'] = $columns['cb'];
    }

    $new_columns['username'] = 'Thông tin';
    $new_columns['name']     = 'Nhân sự';
    $new_columns['email']    = 'Email';
    $new_columns['sinh_nhat'] = 'Năm sinh';
    $new_columns['role']     = 'Chức vụ';
    $new_columns['quan_ly_id'] = 'Thuộc quản lý';
    $new_columns['ngay_vao_lam'] = 'Ngày vào làm';
    $new_columns['trang_thai_lam_viec'] = 'Trạng thái';

    // 👉 CỘT ON / OFF
    $new_columns['account_enabled'] = 'Kích hoạt';

    return $new_columns;
}
add_filter('manage_users_columns', 'doi_ten_cot_users');
add_filter('manage_users_custom_column', function($value, $column_name, $user_id) {

    if ($column_name !== 'account_enabled') return $value;

    $enabled = get_user_meta($user_id, 'account_enabled', true);
    if ($enabled === '') {
        $enabled = 1;
        update_user_meta($user_id, 'account_enabled', 1);
    }

    $checked = $enabled ? 'checked' : '';
    $status_class = $enabled ? 'on' : 'off';
    $status_text  = $enabled ? 'ON' : 'OFF';

    return '
        <div style="display:flex;align-items:center;">
            <label class="sw-switch">
                <input type="checkbox" class="sw-toggle-user" data-user="'.$user_id.'" '.$checked.'>
                <span class="sw-slider"></span>
            </label>
            <span class="sw-status '.$status_class.'">'.$status_text.'</span>
        </div>
    ';
}, 10, 3);
add_action('admin_footer', function () {
    $ajax_url = admin_url('admin-ajax.php');
    ?>
    <script>
        const SW_AJAX_URL = "<?php echo esc_url($ajax_url); ?>";

        document.addEventListener('change', function(e){
            if (!e.target.classList.contains('sw-toggle-user')) return;

            const wrapper = e.target.closest('div');
            const status  = wrapper.querySelector('.sw-status');

            const enabled = e.target.checked ? 1 : 0;

            // Update UI trước
            status.textContent = enabled ? 'ON' : 'OFF';
            status.classList.toggle('on', enabled);
            status.classList.toggle('off', !enabled);

            // Gửi AJAX
            fetch(SW_AJAX_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'sw_toggle_user',
                    user_id: e.target.dataset.user,
                    enabled: enabled
                })
            })
                .then(r => r.text())
            .then(res => console.log('AJAX:', res))
            .catch(err => alert('AJAX Error: ' + err));
        });
    </script>
    <?php
});
add_action('wp_ajax_sw_toggle_user', function () {

    if (!current_user_can('administrator')) {
        wp_die('Permission denied');
    }

    $user_id = intval($_POST['user_id'] ?? 0);
    $enabled = intval($_POST['enabled'] ?? 0);

    if (!$user_id) {
        wp_die('Invalid user');
    }

    update_user_meta($user_id, 'account_enabled', $enabled);

    echo 'OK';
    wp_die();
});
add_filter('authenticate', function($user, $username, $password) {

    if (is_wp_error($user)) return $user;
    if (!$user instanceof WP_User) return $user;

    $enabled = get_user_meta($user->ID, 'account_enabled', true);

    if ((int)$enabled === 0) {
        return new WP_Error(
            'account_disabled',
            'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị.'
        );
    }

    return $user;

}, 30, 3);

add_action('admin_head', function () {
    ?>
    <style>
        /* Switch UI */
        .sw-switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .sw-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .sw-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #ccc;
            transition: .25s;
            border-radius: 24px;
        }

        .sw-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .25s;
            border-radius: 50%;
        }

        .sw-switch input:checked + .sw-slider {
            background-color: #22c55e; /* xanh */
        }

        .sw-switch input:checked + .sw-slider:before {
            transform: translateX(22px);
        }

        .sw-switch:hover .sw-slider {
            box-shadow: 0 0 0 2px rgba(34,197,94,.2);
        }

        /* Badge text */
        .sw-status {
            font-size: 11px;
            font-weight: 600;
            margin-left: 6px;
        }

        .sw-status.on {
            color: #16a34a;
        }

        .sw-status.off {
            color: #dc2626;
        }
    </style>
    <?php
});

// Hiển thị dữ liệu "Năm sinh" trong cột tương ứng
function hien_thi_sinh_nhat($value, $column_name, $user_id) {
    if ($column_name === 'sinh_nhat') {
        $sinh_nhat = get_user_meta($user_id, 'sinh_nhat', true);
        return $sinh_nhat ? date('d/m/Y', strtotime($sinh_nhat)) : '-';
    }
    if ($column_name === 'ngay_vao_lam') {
        $ngay_vao_lam = get_user_meta($user_id, 'ngay_vao_lam', true);
        return $ngay_vao_lam ? date('d/m/Y', strtotime($ngay_vao_lam)) : '-';
    }
    return $value;
}
add_filter('manage_users_custom_column', 'hien_thi_sinh_nhat', 10, 3);


// Đổi tên chữ quản lý thành quản trị cấp cao
function spiritwebs_rename_admin_role() {
    global $wp_roles;
    if ( ! isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }

    // Kiểm tra vai trò 'administrator' có tồn tại không
    if ( isset( $wp_roles->roles['administrator'] ) ) {
        // Đổi tên vai trò 'administrator' thành 'Quản Trị Cấp Cao'
        $wp_roles->roles['administrator']['name'] = 'Quản Trị Cấp Cao';
        $wp_roles->role_names['administrator'] = 'Quản Trị Cấp Cao';
    }
}
add_action('init', 'spiritwebs_rename_admin_role');




// Thêm nút xuất CSV vào trang quản trị người dùng
function them_nut_xuat_csv() {
    // Kiểm tra quyền theo hệ thống phân quyền tùy chỉnh của bạn
    if (function_exists('spiritwebs_user_can') && spiritwebs_user_can('export_user')) {
        echo '<div class="alignleft actions">';
        echo '<a href="' . admin_url('admin-ajax.php?action=export_users_csv') . '" class="button button-primary">Xuất CSV</a>';
        echo '</div>';
    }
}
add_action('restrict_manage_users', 'them_nut_xuat_csv');


function xuat_du_lieu_users_csv() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền thực hiện thao tác này!');
    }

    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=nhan_su_' . date('Ymd_His') . '.csv');

    $output = fopen('php://output', 'w');

    // BOM UTF-8 cho Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Header
    fputcsv($output, [
        'STT',
        'Mã Nhân Viên',
        'Giới tính',
        'Tên',
        'Điện thoại',
        'Email',
        'Chi Nhánh',
        'Phòng Ban',
        'Chức vụ',
        'Ngày Vào Làm',
        'Loại Công Việc',
        'Tình Trạng',
        'CMND/CCCD',
        'Số TK Ngân Hàng',
        'Chi Nhánh Ngân Hàng',
        'Birthday',
        'Nơi Sinh',
        'Địa Chỉ Thường Trú',
        'Nguồn Tuyển',
        'Người Giới Thiệu',
    ]);

    // Mapping hiển thị
    $map_trang_thai = [
        'dang_lam' => 'Đang làm',
        'da_nghi'  => 'Đã nghỉ',
    ];

    $map_nguon_tuyen = [
        'tuyen_dung'  => 'Tuyển dụng',
        'gioi_thieu'  => 'Giới thiệu',
    ];

    $users = get_users();
    $stt = 1;

    foreach ($users as $user) {

        $gioi_tinh_raw   = get_user_meta($user->ID, 'gioi_tinh', true);
        $nguon_raw       = get_user_meta($user->ID, 'nguon_tuyen', true);
        $tinh_trang_raw  = get_user_meta($user->ID, 'trang_thai_lam_viec', true);

        // Chuẩn hóa hiển thị
        $gioi_tinh = match($gioi_tinh_raw) {
        'nam' => 'Nam',
            'nu'  => 'Nữ',
            default => $gioi_tinh_raw,
        };

        $nguon_tuyen = $map_nguon_tuyen[$nguon_raw] ?? $nguon_raw;
        $tinh_trang  = $map_trang_thai[$tinh_trang_raw] ?? $tinh_trang_raw;

        $row = [
            $stt++,
            $user->ID,
            $gioi_tinh,
            $user->display_name,
            get_user_meta($user->ID, 'dien_thoai', true),
            $user->user_email,
            get_user_meta($user->ID, 'chi_nhanh', true),
            get_user_meta($user->ID, 'phong_ban', true),
            implode(', ', $user->roles),
            get_user_meta($user->ID, 'ngay_vao_lam', true),
            get_user_meta($user->ID, 'loai_cong_viec', true),
            $tinh_trang,
            get_user_meta($user->ID, 'cccd', true),
            get_user_meta($user->ID, 'so_tk_ngan_hang', true),
            get_user_meta($user->ID, 'chi_nhanh_ngan_hang', true),
            get_user_meta($user->ID, 'sinh_nhat', true),
            get_user_meta($user->ID, 'noi_sinh', true),
            get_user_meta($user->ID, 'dia_chi_thuong_tru', true),
            $nguon_tuyen,
            get_user_meta($user->ID, 'nguoi_gioi_thieu', true),
        ];

        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
add_action('wp_ajax_export_users_csv', 'xuat_du_lieu_users_csv');


// Hàm xuất CSV cho người dùng
/*function xuat_du_lieu_users_csv() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền thực hiện thao tác này!');
    }

    // Thiết lập tiêu đề để trình duyệt tải xuống file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users_export.csv"');

    // Mở file CSV
    $output = fopen('php://output', 'w');

    // Viết tiêu đề cột (dòng đầu tiên)
    fputcsv($output, array('Tài khoản', 'Nhân sự', 'Email', 'Năm sinh', 'Ngày làm', 'Chức vụ'));

    // Lấy tất cả người dùng từ WordPress
    $users = get_users();

    // Lặp qua từng user và xuất dữ liệu vào CSV
    foreach ($users as $user) {
        $username = $user->user_login;
        $name = $user->display_name;
        $email = $user->user_email;
        $sinh_nhat = get_user_meta($user->ID, 'sinh_nhat', true);
        $ngay_lam = get_user_meta($user->ID, 'ngay_lam', true);
        $role = implode(', ', $user->roles); // Nếu người dùng có nhiều vai trò

        // Dữ liệu dòng này sẽ được xuất ra
        fputcsv($output, array($username, $name, $email, $sinh_nhat, $ngay_lam, $role));
    }

    // Đóng file
    fclose($output);
    exit;
}
add_action('wp_ajax_export_users_csv', 'xuat_du_lieu_users_csv');*/

// Ẩn tab "Trợ giúp" (Help tab) ở góc phải trên trong admin
function an_tab_ho_tro_trong_admin() {
    $screen = get_current_screen();
    $screen->remove_help_tabs(); // Xóa tất cả tab trợ giúp
}
add_action('admin_head', 'an_tab_ho_tro_trong_admin');


//collapse menu admin
/*function collapse_admin_menu_script() {
    wp_enqueue_script('collapse-menu', get_template_directory_uri() . '/collapse-menu.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'collapse_admin_menu_script');*/


function my_custom_admin_styles() {
    echo '<style>
        #adminmenu .menu-top .dulieu {
            color: green !important;
            font-weight: bold;
        }
        #adminmenu .menu-top .nhadat {
            color: orange !important;
            font-weight: bold;
        }
        /* Make the menu icon orange */
        #toplevel_page_wp_dulieunhadat .wp-menu-image img {
            filter: invert(35%) sepia(94%) saturate(560%) hue-rotate(3deg) brightness(98%) contrast(99%);
        }
        #toplevel_page_wp_duan .wp-menu-image img {
            filter: invert(35%) sepia(94%) saturate(560%) hue-rotate(3deg) brightness(98%) contrast(99%);
        }
    </style>';
}
add_action('admin_head', 'my_custom_admin_styles');

function my_custom_page_content_wp_tin_thi_truong() {
    ?>
    <div class="wrap">

    </div>
    <?php
}


function my_custom_page_content_wp_duan() {
    ?>
    <div class="wrap">
        <div class="header-container">
            <h1  style="padding:10px 0px 10px 0px;">
                <span class="dashicons dashicons-admin-home" style="font-size: 24px; vertical-align: middle; margin-right: 8px; padding:0px 0px 10px 0px"></span>
                <span style="color: #FFA500;  font-weight:bold;">Dự</span>
                <span style="color: #4CAF50; font-weight:bold;">Án</span>

            </h1>
            <div class="button-group">
                <button id="createNewBtn">➕ Tạo Mới</button>

                <button id="deleteBtn">🗑️ Thùng Rác</button>

            </div>
            <select id="tableSelector"></select>
        </div>
        <table id="jqGrid"></table>
        <div id="jqGridPager"></div>
    </div>
    <?php
}


function my_custom_page_content_wp_dulieunhadat() {
    ?>
    <div class="wrap">
        <div class="header-container">
            <h1  style="padding:10px 0px 10px 0px;">
                <span class="dashicons dashicons-admin-home" style="font-size: 24px; vertical-align: middle; margin-right: 8px; padding:0px 0px 10px 0px"></span>
                <span style="color: #4CAF50; font-weight:bold;">Dữ Liệu</span>
                <span style="color: #FFA500;  font-weight:bold;">Nhà Đất</span>

            </h1>
            <div class="divnhanvien" style=" width:44%;">
                <select id="filter-usernhanvien"
                        style="display:none; width: 200px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                background-size: 12px; box-sizing: border-box;" >
                </select>
            </div>

            <div class="button-group">
                <?php if (function_exists('spiritwebs_user_can') && spiritwebs_user_can('add_data')) : ?>
                    <button id="createNewBtn">➕ Tạo Mới</button>
                <?php endif; ?>
                <?php if (function_exists('spiritwebs_user_can') && spiritwebs_user_can('export_data')) : ?>
                    <form method="post" action="" id="exportForm" class="export-form" style="display:inline;">
                        <?php
                        submit_button(
                            '📤 Xuất CSV',
                            'primary',
                            'spiritwebs_export_csv',
                            false,
                            array(
                                'id' => 'exportCsv',
                                'class' => 'button export-button'
                            )
                        );
                        ?>
                    </form>
                <?php endif; ?>

                <?php
                $user = wp_get_current_user();
                if (in_array('administrator', $user->roles)) :
                    ?>
                    <button id="deleteBtn">🗑️ Thùng Rác</button>
                <?php endif; ?>



            </div>
            <select id="tableSelector"></select>
        </div>
        <!-- Filter Input (Above Grid) -->
        <div class="filter-wrapper" style="margin-bottom: 10px; display: flex; gap: 8px; flex-wrap: wrap; box-sizing: border-box;">
            <button id="toggle-filter-btn" style="display:none; margin-bottom: 10px;">Hiện/Ẩn bộ lọc</button>
            <div id="filter-container" style="width: 100%;">
                <!-- Hàng 1 -->
                <div style="display: flex; gap: 8px; flex-wrap: nowrap; flex-grow: 1; min-width: 0; align-items: center;">
                    <input type="text" id="filter-all" placeholder="Tìm theo SĐT, số nhà, đường, khu vực..."
                           style="flex-grow: 1; min-width: 200px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                  box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; outline: none; height: 40px; box-sizing: border-box;">



                    <div style="display: flex; height: 40px;">

                        <input type="text" id="filter-soto" placeholder="Số Tờ"
                               style="width: 90px; padding: 8px 12px; border: 1px solid #ccc;
                border-right: none; border-radius: 6px 0 0 6px;
                font-size: 14px; outline: none; box-sizing: border-box;">

                        <input type="text" id="filter-sothua" placeholder="Số Thửa"
                               style="width: 90px; padding: 8px 12px; border: 1px solid #ccc;
                border-left: none; border-radius: 0 6px 6px 0;
                font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>





                    <select id="filter-province"
                            style="display:none;width: 180px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                    background-size: 12px; box-sizing: border-box;">
                    </select>
                    <select id="filter-ttk_huong"
                            style="display:none; width: 180px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                    background-size: 12px; box-sizing: border-box;">
                    </select>
                    <select id="filter-vitri"
                            style="display:none; width: 180px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                    background-size: 12px; box-sizing: border-box;">
                    </select>

                    <select id="filter-producttype"
                            style="display:none; width: 180px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                    background-size: 12px; box-sizing: border-box;">
                    </select>



                </div>

                <!-- Hàng 2 -->
                <div style="display: flex; gap: 8px; flex-wrap: nowrap; flex-grow: 1; min-width: 0; align-items: center; margin-top: 8px;">

                    <select id="filter-tenduan"
                            style="display:none; width: 120px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"16\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                    background-size: 12px; box-sizing: border-box;">
                    </select>


                    <div style="display: flex; height: 40px;">

                        <input type="text" id="filter-width-from" placeholder="Rộng từ"
                               style="width: 80px; padding: 8px 12px; border: 1px solid #ccc;
                border-right: none; border-radius: 6px 0 0 6px;
                font-size: 14px; outline: none; box-sizing: border-box;">

                        <input type="text" id="filter-width-to" placeholder="đến | m"
                               style="width: 80px; padding: 8px 12px; border: 1px solid #ccc;
                border-left: none; border-radius: 0 6px 6px 0;
                font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>


                    <div style="display: flex; height: 40px;">

                        <input type="text" id="filter-long-from" placeholder="Dài từ"
                               style="width: 80px; padding: 8px 12px; border: 1px solid #ccc;
                border-right: none; border-radius: 6px 0 0 6px;
                font-size: 14px; outline: none; box-sizing: border-box;">

                        <input type="text" id="filter-long-to" placeholder="đến | m"
                               style="width: 80px; padding: 8px 12px; border: 1px solid #ccc;
                border-left: none; border-radius: 0 6px 6px 0;
                font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>

                    <div style="display: flex; height: 40px;">

                        <input type="text" id="filter-giaban-from" placeholder="Giá từ"
                               style="width: 120px; padding: 8px 12px; border: 1px solid #ccc;
                border-right: none; border-radius: 6px 0 0 6px;
                font-size: 14px; outline: none; box-sizing: border-box;">

                        <input type="text" id="filter-giaban-to" placeholder="đến | triệu,tỷ"
                               style="width: 120px; padding: 8px 12px; border: 1px solid #ccc;
                border-left: none; border-radius: 0 6px 6px 0;
                font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>


                    <!--<div style="display: flex; height: 40px;">

                        <input type="text" id="filter-dtdcongnhan-from" placeholder="Dài từ"
                               style="width: 80px; padding: 8px 12px; border: 1px solid #ccc;
                border-right: none; border-radius: 6px 0 0 6px;
                font-size: 14px; outline: none; box-sizing: border-box;">

                        <input type="text" id="filter-dtdcongnhan-to" placeholder="đến | m2"
                               style="width: 80px; padding: 8px 12px; border: 1px solid #ccc;
                border-left: none; border-radius: 0 6px 6px 0;
                font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>-->
                    <select id="filter-propertytype"
                            style="display:none; width: 180px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                    background-size: 12px; box-sizing: border-box;">
                    </select>


                    <button id="btn-filter"
                            style="background-color: red; color: white; padding: 8px 16px; border: none;
                   border-radius: 5px; box-shadow: 2px 2px 5px rgba(0,0,0,0.3); cursor: pointer; height: 40px; white-space: nowrap; box-sizing: border-box;">
                        <i class="fa fa-search" style="margin-right:6px;"></i> Lọc
                    </button>

                    <button id="btn-reset"
                            style="background-color: #555; color: white; padding: 8px 16px; border: none;
                   border-radius: 5px; box-shadow: 2px 2px 5px rgba(0,0,0,0.3); cursor: pointer; height: 40px; white-space: nowrap; box-sizing: border-box;">
                        <i class="fa fa-rotate-left" style="margin-right:6px;"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <!-- Hàng 3 -->
        <div style="display: flex; gap: 8px; flex-wrap: nowrap; flex-grow: 1; min-width: 0; align-items: center; margin-top: 0px; margin-bottom:12px;">

            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin: 0px 0;">



                <!-- Nút Sản phẩm mới -->
                <button class="sanphammoiBtn" value="1"
                        style="background-color:#ff9800; color:#fff; padding:6px 10px; border:none; border-radius:10px; cursor:pointer; font-size:13px; display:inline-flex; align-items:center; gap:6px; opacity:0.85; transition:opacity 0.3s ease;"
                        onmouseover="this.style.opacity='1';"
                        onmouseout="this.style.opacity='0.85';"
                >
                    <svg
                            viewBox="0 0 400 300"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            style="width:48px; height:20px; vertical-align:middle; display:inline-block;"
                    >
                        <style>
                            @keyframes rotateInfinite {
                                from { transform: rotate(0deg); }
                                to { transform: rotate(360deg); }
                            }
                            .IconNewLine2 {
                                transform-origin: center;
                                animation: rotateInfinite 1s linear infinite;
                            }
                        </style>
                        <g id="Layer_1" >
                            <path class="IconNewLine1" fill="white" d="M51.19,145.7c18.38-24.75,36.54-49.68,55.52-73.99,12.75-16.34,30.83-24.51,51.22-28.06,1.3-.23,2.55-.67,3.82-1.01h244.53c4.4,1.01,8.84,1.89,13.2,3.04,33.75,8.91,59.27,37.85,63.28,71.65.17,1.45.63,2.87.95,4.31v98c-.75,4.06-1.38,8.15-2.28,12.18-7.7,34.42-36.75,60.83-72.38,65.9-.95.14-1.85.6-2.78.92h-244.53c-4.09-.94-8.19-1.79-12.25-2.83-19.23-4.91-35.15-14.98-47.02-30.58-17.56-23.09-34.85-46.38-51.93-69.81-11.47-15.74-11.07-33.95.64-49.72ZM118.96,212.64c25.32,0,45.85-20.15,45.85-45s-20.53-45-45.85-45-45.85,20.15-45.85,45,20.53,45,45.85,45Z" />
                            <path fill="white" d="M32.5,115.49c10.9,17.64,24.43,32.54,41.44,43.59,1.43-7.29,4.65-13.96,9.2-19.54-18.94-12.9-32.85-30.32-40.87-52.81-1.48-4.15-2.14-8.71-2.44-13.12-.86-12.41,7-19.56,19.4-16.51,8.71,2.15,17.15,5.91,25.19,9.95,5.77,2.9,10.68,7.45,16.47,11.63,4.09-4.94,8.2-9.92,12.84-15.53-3.11-2.57-5.93-5.08-8.94-7.34-12.65-9.49-26.42-16.46-42.35-19.2-24.35-4.19-42.71,10.05-43.51,34.26-.54,16.34,5.12,30.97,13.56,44.62ZM99.48,148.99c4.75,2.31,10.17,3.69,15.44,4.46,11.55,1.68,18.17-4.26,18.19-15.66,0-1.97-.26-3.97-.63-5.92-.78-4.19,1.15-5.7,5.02-4.61,1.32.37,2.63.76,3.94,1.17,4.78,2.65,9.03,6.11,12.54,10.19-.11,14.89-7.46,30.34-26.51,34.6-11.59,2.59-22.97.71-33.76-3.74-7.08-2.92-13.65-6.41-19.78-10.39,1.43-7.29,4.65-13.96,9.2-19.54,5.09,3.47,10.53,6.63,16.33,9.44Z" />
                            <g>
                                <path class="IconNewLine2" fill="white" d="M195.97,128.65h20.39l26.61,50.34v-50.34h20.58v90.98h-20.58l-26.46-49.96v49.96h-20.53v-90.98Z" />
                                <path class="IconNewLine2" fill="white" d="M278.01,128.65h58.52v19.43h-36.63v14.46h33.98v18.56h-33.98v17.94h37.69v20.6h-59.58v-90.98Z" />
                                <path class="IconNewLine2" fill="white" d="M342.07,128.65h20.74l7.47,50.93,10.93-50.93h20.66l10.96,50.87,7.48-50.87h20.63l-15.58,90.98h-21.41l-12.39-57.28-12.35,57.28h-21.41l-15.72-90.98Z" />
                            </g>
                        </g>
                    </svg>
                    New
                </button>
                <button class="sanphamhotBtn" value="679" style="background-color: rgb(244, 67, 54); color: rgb(255, 255, 255); padding: 6px 10px; border: none; border-radius: 10px; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; opacity: 0.85; transition: opacity 0.3s;"
                        onmouseover="this.style.opacity='1';" onmouseout="this.style.opacity='0.85';">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:20px; height:20px; vertical-align:middle; display:inline-block;">
                        <path fill="white" d="M12 2L15 8H21L16.5 12L18 18L12 14L6 18L7.5 12L3 8H9L12 2Z"/>
                    </svg>
                    Hot
                </button>


                <button class="statusBtn" value="675"
                        style="background-color: #e5e5e5; color: #555; padding: 8px 14px; border: none; border-radius: 10px;
           cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px;
           opacity: 0.9; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <i class="fas fa-check-circle" style="font-size: 14px;"></i> Đang GDịch
                </button>

                <button class="statusBtn" value="676"
                        style="background-color: #e5e5e5; color: #555; padding: 8px 14px; border: none; border-radius: 10px;
           cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px;
           opacity: 0.9; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <i class="fas fa-ban" style="font-size: 14px;"></i> Ngưng GDịch
                </button>

                <button class="statusBtn" value="677"
                        style="background-color: #e5e5e5; color: #555; padding: 8px 14px; border: none; border-radius: 10px;
           cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px;
           opacity: 0.9; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <i class="fas fa-dollar-sign" style="font-size: 14px;"></i> Đã GDịch
                </button>

                <button class="statusBtn" value="681"
                        style="background-color: #e5e5e5; color: #555; padding: 8px 14px; border: none; border-radius: 10px;
           cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px;
           opacity: 0.9; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <i class="fas fa-dollar-sign" style="font-size: 14px;"></i> Đã Cọc
                </button>

                <button class="statusBtn" value=""
                        style="background-color: #e5e5e5; color: #555; padding: 8px 14px; border: none; border-radius: 10px;
           cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px;
           opacity: 0.9; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <i class="fas fa-dollar-sign" style="font-size: 14px;"></i> Có HĐ Thuê
                </button>

                <!-- Giá Tăng -->
                <button class="statusGiatang" value="asc"
                        style="background-color: #d1f7d6; color: #228c22; padding: 8px 14px; border: none; border-radius: 10px;
        cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px;
        opacity: 0.95; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <i class="fa fa-arrow-up"></i>
                    <span class="text-pc">Giá Tăng</span>
                    <span class="text-mobile">Giá</span>
                </button>

                <!-- Giá Giảm -->
                <button class="statusGiagiam" value="desc"
                        style="background-color: #ffe0e0; color: #c0392b; padding: 8px 14px; border: none; border-radius: 10px;
        cursor: pointer; font-size: 13px; display: flex; align-items: center; gap: 6px;
        opacity: 0.95; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <i class="fa fa-arrow-down"></i>
                    <span class="text-pc">Giá Giảm</span>
                    <span class="text-mobile">Giá</span>
                </button>

                <button class="thangmayBtn"
                        value="683"
                        style="
            background: linear-gradient(135deg, #a6f40a, #8bd600);
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 0px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            opacity: 0.9;
        "
                        onmouseover="this.style.opacity='1'; this.style.transform='scale(1.03)'"
                        onmouseout="this.style.opacity='0.9'; this.style.transform='scale(1)'">

                    <svg viewBox="0 0 24 24" fill="none"
                         xmlns="http://www.w3.org/2000/svg"
                         style="width:20px; height:20px; vertical-align:middle; display:inline-block;">
                        <path fill="white" d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z"/>
                    </svg>
                    Thang máy
                </button>







            </div>

        </div>


    </div>


    <?php if (function_exists('spiritwebs_user_can') && spiritwebs_user_can('view_data')) : ?>
        <table id="jqGrid"></table>
        <div id="jqGridPager"></div>
        </div>
    <?php endif; ?>
    <?php
}
function my_custom_page_content_wp_khachhang() {
    ?>
    <div class="wrap">
        <div class="header-container">
            <h1  style="padding:10px 0px 10px 0px;">
                <span class="dashicons dashicons-admin-home" style="font-size: 24px; vertical-align: middle; margin-right: 8px; padding:0px 0px 10px 0px"></span>
                <span style="color: #FFA500;  font-weight:bold;">Khách Hàng</span>
                <span style="color: #4CAF50; font-weight:bold;">Của Bạn</span>

            </h1>
            <div class="button-group">
                <button id="createNewBtn">➕ Tạo Mới</button>

                <button id="deleteBtn">🗑️ Thùng Rác</button>

            </div>
            <select id="tableSelector"></select>
        </div>

        <!-- Filter Input (Above Grid) -->
        <div class="filter-wrapper" style="margin-bottom: 10px; display: flex; gap: 8px; flex-wrap: wrap; box-sizing: border-box;">
            <button id="toggle-filter-btn" style="display:none; margin-bottom: 10px;">Hiện/Ẩn bộ lọc</button>
            <div id="filter-container" style="width: 100%;">
                <!-- Hàng 1 -->
                <div style="display: flex; gap: 8px; flex-wrap: nowrap; flex-grow: 1; min-width: 0; align-items: center;">
                    <input type="text" id="filter-ten" placeholder="Tên khách hàng"
                           style="flex-grow: 1; min-width: 200px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                  box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; outline: none; height: 40px; box-sizing: border-box;">

                    <input type="text" id="filter-sodt" placeholder="Điện thoại"
                           style="flex-grow: 1; min-width: 200px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                  box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; outline: none; height: 40px; box-sizing: border-box;">
                    <input type="text" id="filter-ghichu" placeholder="Ghi chú"
                           style="flex-grow: 1; min-width: 200px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                  box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; outline: none; height: 40px; box-sizing: border-box;">



                </div>

                <!-- Hàng 2 -->
                <div style="display: flex; gap: 8px; flex-wrap: nowrap; flex-grow: 1; min-width: 0; align-items: center; margin-top: 8px;">

                    <select id="filter-loai"
                            style="display:none;width: 250px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                    background-size: 12px; box-sizing: border-box;">
                    </select>
                    <select id="filter-trangthai"
                            style="display:none; width: 250px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                    background-size: 12px; box-sizing: border-box;">
                    </select>
                    <?php if ( current_user_can( 'administrator' ) ) : ?>
                        <select id="filter-user"
                                style="display:none; width: 250px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
                   box-shadow: 1px 1px 4px rgba(0,0,0,0.1); font-size: 14px; height: 40px; outline: none; appearance: none;
                   background: white url('data:image/svg+xml;utf8,<svg fill=\"gray\" height=\"16\" viewBox=\"0 0 24 24\" width=\"14\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>') no-repeat right 12px center;
                                                                                                                                                                                                                                                    background-size: 12px; box-sizing: border-box;">
                        </select>

                    <?php endif; ?>
                    <button id="btn-filter"
                            style="background-color: red; color: white; padding: 8px 16px; border: none;
                   border-radius: 5px; box-shadow: 2px 2px 5px rgba(0,0,0,0.3); cursor: pointer; height: 40px; white-space: nowrap; box-sizing: border-box;">
                        <i class="fa fa-search" style="margin-right:6px;"></i> Lọc
                    </button>

                    <button id="btn-reset"
                            style="background-color: #555; color: white; padding: 8px 16px; border: none;
                   border-radius: 5px; box-shadow: 2px 2px 5px rgba(0,0,0,0.3); cursor: pointer; height: 40px; white-space: nowrap; box-sizing: border-box;">
                        <i class="fa fa-rotate-left" style="margin-right:6px;"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <table id="jqGrid"></table>
        <div id="jqGridPager"></div>
    </div>
    <?php
}


function remove_admin_notices_on_custom_page() {
    $screen = get_current_screen();

    if ($screen && $screen->id === 'toplevel_page_wp_dulieunhadat') {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }
    if ($screen && $screen->id === 'toplevel_page_wp_duan') {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }
}

add_action('admin_head', 'remove_admin_notices_on_custom_page');


//hide select
function hide_table_selector() {
    echo '<style>
#tableSelector { display: none !important;}
 .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        #exportCsv{
            height:44px;
        }
        .header-container button,#exportCsv {
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        #createNewBtn, #deleteBtn, #exportCsv {
            background: linear-gradient(145deg, #0073aa, #005e8b);
            box-shadow: 3px 3px 6px rgba(0, 0, 0, 0.2), 
                        -3px -3px 6px rgba(255, 255, 255, 0.2);
                        margin: 13px 0px 18px 8px;
        }
        #createNewBtn:hover, #deleteBtn:hover ,#exportCsv:hover{
            background: linear-gradient(145deg, #005e8b, #00496d);
            margin: 13px 0px 18px 8px;
        }
        #createNewBtn:active, #deleteBtn:active, #exportCsv:active {
            transform: translateY(3px);
            box-shadow: inset 3px 3px 6px rgba(0, 0, 0, 0.2);
            margin: 13px 0px 18px 8px;
        }
</style>';
}
add_action('admin_head', 'hide_table_selector');

// hide notification
add_action('admin_head', function () {
    echo '<style>.notice, .update-nag, .error, .is-dismissible { display: none !important; }</style>';
});


function add_ajaxurl_script() {
    ?>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}
add_action('wp_head', 'add_ajaxurl_script');

function get_jqgrid_tables() {
    global $wpdb;
    $table_name = "wp_jq_sys_format";

    // Fetch unique table names from the 'tables' column
    $tables = $wpdb->get_col("SELECT DISTINCT `tables` FROM $table_name");

    if (!$tables) {
        wp_send_json_error(["message" => "No tables found"]);
    }

    wp_send_json(["success" => true, "tables" => $tables]);
}
add_action("wp_ajax_get_jqgrid_tables", "get_jqgrid_tables");
add_action("wp_ajax_nopriv_get_jqgrid_tables", "get_jqgrid_tables");

function format_number_to_vietnamese($number) {
    // Remove commas or non-numeric characters
    $number = str_replace(',', '', $number);

    if ($number >= 1000000000) {
        // Convert to billion (tỷ)
        return number_format((float)$number / 1000000000, 1) . ' tỷ';
    } elseif ($number >= 1000000) {
        // Convert to million (triệu)
        return number_format((float)$number / 1000000, 1) . ' triệu';
    } elseif ($number >= 1000) {
        // Convert to thousand (nghìn)
        return number_format((float)$number / 1000, 1) . ' nghìn';
    } else {
        // Return the number as is if it's smaller than 1000
        return number_format((float) $number, 0, ',', '.');
    }
}
function normalize_price_input($value) {
    $value = str_replace(',', '.', trim($value));
    $value = floatval($value);

    if ($value <= 0) return 0;

    // < 100 => TỶ | >=100 => TRIỆU
    if ($value < 100) {
        return $value * 1_000_000_000;
    } else {
        return $value * 1_000_000;
    }
}
// load content data
function load_jqgrid_data() {
    global $wpdb;

    if (!isset($_POST['table']) || empty($_POST['table'])) {
        wp_send_json_error(["message" => "No table specified"]);
    }

    $selected_table = esc_sql($_POST['table']); // Sanitize input

    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
    $soto = isset($_POST['soto']) ? sanitize_text_field($_POST['soto']) : '';
    $sothua = isset($_POST['sothua']) ? sanitize_text_field($_POST['sothua']) : '';
    $province_id = isset($_POST['province_id']) ? intval($_POST['province_id']) : 0; // lấy id province, ép kiểu int

    $vitri = isset($_POST['vitri']) ? intval($_POST['vitri']) : 0; // lấy id vitri, ép kiểu int
    $ttk_huong = isset($_POST['ttk_huong']) ? intval($_POST['ttk_huong']) : 0; // lấy id ttk_huong, ép kiểu int

    $product_type = isset($_POST['product_type']) ? intval($_POST['product_type']) : 0; // lấy id vitri, ép kiểu int
    $property_type = isset($_POST['property_type']) ? intval($_POST['property_type']) : 0; // lấy id vitri, ép kiểu int

    $usernhanvien = isset($_POST['usernhanvien']) ? intval($_POST['usernhanvien']) : 0; // lấy id vitri, ép kiểu int

    //$tenduan = isset($_POST['tenduan']) ? intval($_POST['tenduan']) : 0; // lấy id province, ép kiểu int
    $tenduan = isset($_POST['tenduan']) && is_array($_POST['tenduan'])
        ? array_map('intval', $_POST['tenduan'])
        : [];

    $width_from = isset($_POST['widthfrom']) ? floatval($_POST['widthfrom']) : 0;
    $width_to   = isset($_POST['widthto']) ? floatval($_POST['widthto']) : 0;

    $long_from = isset($_POST['longfrom']) ? floatval($_POST['longfrom']) : 0;
    $long_to   = isset($_POST['longto']) ? floatval($_POST['longto']) : 0;

    $giaban_from = isset($_POST['giabanfrom']) ? floatval($_POST['giabanfrom']) : 0;
    $giaban_to   = isset($_POST['giabanto']) ? floatval($_POST['giabanto']) : 0;

    $dtdcongnhan_from = isset($_POST['dtdcongnhanfrom']) ? floatval($_POST['dtdcongnhanfrom']) : 0;
    $dtdcongnhan_to   = isset($_POST['dtdcongnhanto']) ? floatval($_POST['dtdcongnhanto']) : 0;


    $trangthaigiaodich  = isset($_POST['trangthaigiaodich']) ? floatval($_POST['trangthaigiaodich']) : 0;
    $sanphamhot  = isset($_POST['sanphamhot']) ? floatval($_POST['sanphamhot']) : 0;
    $thangmay  = isset($_POST['thangmay']) ? floatval($_POST['thangmay']) : 0;
    $sanphammoi = isset($_POST['sanphammoi']) && $_POST['sanphammoi'] == 1 ? 1 : 0;






// Nếu là wp_khachhang thì lấy thêm các trường
    if (isset($_POST['table']) && $_POST['table'] === 'wp_khachhang') {
        $khten = isset($_POST['khten']) ? sanitize_text_field($_POST['khten']) : '';
        $khsodt = isset($_POST['khsodt']) ? sanitize_text_field($_POST['khsodt']) : '';
        $khghichu = isset($_POST['khghichu']) ? sanitize_textarea_field($_POST['khghichu']) : '';
        $khloai = isset($_POST['khloai']) ? sanitize_text_field($_POST['khloai']) : '';
        $khtrangthai = isset($_POST['khtrangthai']) ? sanitize_text_field($_POST['khtrangthai']) : '';
        $khuser = isset($_POST['khuser']) ? intval($_POST['khuser']) : 0;
    }



    // ✅ Thêm 2 dòng dưới để lấy thông tin sắp xếp từ JS gửi lên:
    $sort_field = isset($_POST['sort_field']) ? sanitize_text_field($_POST['sort_field']) : 'id';
    $sort_order = isset($_POST['sort_order']) ? strtoupper($_POST['sort_order']) : 'DESC';



    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $limit = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
    $start = ($page - 1) * $limit;

    // Handle sorting
    $sort_field = isset($_POST['sidx']) && !empty($_POST['sidx']) ? esc_sql($_POST['sidx']) : "id";
    $sort_order = isset($_POST['sord']) && !empty($_POST['sord']) ? esc_sql($_POST['sord']) : "asc";

    // Initialize filtering
    $deleted = isset($_POST['deleted']) ? intval($_POST['deleted']) : 0;

    // Xử lý filter_id (id cụ thể)
    $filter_id = isset($_POST['filter_id']) ? intval($_POST['filter_id']) : 0;
    $where = "WHERE 1=1 AND deleted = {$deleted} "; // Always true condition to append filters

    // Nếu là wp_khachhang thì lấy thêm các trường
    if (isset($_POST['table']) && $_POST['table'] === 'wp_khachhang') {
        $khten = isset($_POST['khten']) ? sanitize_text_field($_POST['khten']) : '';
        $khsodt = isset($_POST['khsodt']) ? sanitize_text_field($_POST['khsodt']) : '';
        $khghichu = isset($_POST['khghichu']) ? sanitize_textarea_field($_POST['khghichu']) : '';
        $khloai = isset($_POST['khloai']) ? sanitize_text_field($_POST['khloai']) : '';
        $khtrangthai = isset($_POST['khtrangthai']) ? sanitize_text_field($_POST['khtrangthai']) : '';
        $khuser = isset($_POST['khuser']) ? intval($_POST['khuser']) : 0;
        if ($khten > 0) {
            $khten = sanitize_text_field($khten);  // Xử lý bảo mật cho keyword
            $khten = '%' . $wpdb->esc_like($khten) . '%';  // Thêm dấu '%' để tìm kiếm phần từ
            $where .= " AND (
            LOWER(p.ten) LIKE LOWER('$khten'))";
        }if ($khsodt > 0) {
            $khsodt = sanitize_text_field($khsodt);  // Xử lý bảo mật cho keyword
            $khsodt = '%' . $wpdb->esc_like($khsodt) . '%';  // Thêm dấu '%' để tìm kiếm phần từ
            $where .= " AND (
            LOWER(p.sodt) LIKE LOWER('$khsodt'))";
        }if ($khghichu > 0) {
            $khghichu = sanitize_text_field($khghichu);  // Xử lý bảo mật cho keyword
            $khghichu = '%' . $wpdb->esc_like($khghichu) . '%';  // Thêm dấu '%' để tìm kiếm phần từ
            $where .= " AND (
            LOWER(p.ghichu) LIKE LOWER('$khghichu'))";
        }if ($khloai > 0) {
            // Giả sử trường trong bảng là p.province_id, thay tên trường nếu khác
            $where .= " AND p.loai = $khloai ";
        }if ($khtrangthai > 0) {
            // Giả sử trường trong bảng là p.province_id, thay tên trường nếu khác
            $where .= " AND p.trangthai = $khtrangthai ";
        }if ($khuser > 0) {
            // Giả sử trường trong bảng là p.province_id, thay tên trường nếu khác
            $where .= " AND p.user = $khuser ";
        }
    }

    if ($filter_id > 0) {
        // Nếu có filter_id thì chỉ lấy bản ghi đó thôi
        $where .= " AND p.id = {$filter_id}";
    }

    // Xử lý tìm kiếm với keyword
    /*if (isset($_POST['keyword']) && !empty($_POST['keyword'])) {
        $keyword = sanitize_text_field($_POST['keyword']);  // Xử lý bảo mật cho keyword
        $keyword_like = '%' . $wpdb->esc_like($keyword) . '%';  // Thêm dấu '%' để tìm kiếm phần từ
        $where .= " AND (
            LOWER(p.dienthoaididong) LIKE LOWER('$keyword_like')
            OR LOWER(p.sonha) LIKE LOWER('$keyword_like')
            OR LOWER(p.road) LIKE LOWER('$keyword_like')
            OR LOWER(p.khuvuc) LIKE LOWER('$keyword_like')
            OR LOWER(p.code_product) LIKE LOWER('$keyword_like')
        )";
    }*/
    // Xử lý tìm kiếm với keyword (multi word search)
    if (!empty($_POST['keyword'])) {

        $keyword = sanitize_text_field($_POST['keyword']);

        // Tách keyword thành từng từ (theo khoảng trắng)
        $keywords = preg_split('/\s+/', trim($keyword));

        $subWhere = [];

        foreach ($keywords as $word) {
            if (strlen($word) < 1) continue;

            $like = '%' . $wpdb->esc_like($word) . '%';

            $subWhere[] = $wpdb->prepare("
            (
                LOWER(p.dienthoaididong) LIKE LOWER(%s)
                OR LOWER(p.sonha) LIKE LOWER(%s)
                OR LOWER(p.road) LIKE LOWER(%s)
                OR LOWER(p.khuvuc) LIKE LOWER(%s)
                OR LOWER(p.code_product) LIKE LOWER(%s)
            )
        ", $like, $like, $like, $like, $like);
        }

        if (!empty($subWhere)) {
            // Mỗi từ đều phải đúng (AND)
            $where .= " AND (" . implode(" AND ", $subWhere) . ")";
        }
    }

    // Xử lý tìm kiếm với soto
    if (isset($_POST['soto']) && !empty($_POST['soto'])) {
        $soto = sanitize_text_field($_POST['soto']);  // Xử lý bảo mật cho soto
        $soto_like = '%' . $wpdb->esc_like($soto) . '%';  // Thêm dấu '%' để tìm kiếm phần từ
        $where .= " AND (
            LOWER(p.soto) LIKE LOWER('$soto_like')
        )";
    }
    // Xử lý tìm kiếm với soto
    if (isset($_POST['sothua']) && !empty($_POST['sothua'])) {
        $sothua = sanitize_text_field($_POST['sothua']);  // Xử lý bảo mật cho sothua
        $sothua_like = '%' . $wpdb->esc_like($sothua) . '%';  // Thêm dấu '%' để tìm kiếm phần từ
        $where .= " AND (
            LOWER(p.sothua) LIKE LOWER('$sothua_like')
        )";
    }

    if ($province_id > 0) {
        // Giả sử trường trong bảng là p.province_id, thay tên trường nếu khác
        $where .= " AND p.wp_province = $province_id ";
    }
    if ($vitri > 0) {
        // Giả sử trường trong bảng là p.vitri, thay tên trường nếu khác
        $where .= " AND p.vitri = $vitri ";
    }
    if ($ttk_huong > 0) {
        // Giả sử trường trong bảng là p.ttk_huong, thay tên trường nếu khác
        $where .= " AND p.ttk_huong = $ttk_huong ";
    }
    if ($product_type > 0) {
        // Giả sử trường trong bảng là p.product_type, thay tên trường nếu khác
        $where .= " AND p.product_type = $product_type ";
    }
    if ($property_type > 0) {
        // Giả sử trường trong bảng là p.property_type, thay tên trường nếu khác
        $where .= " AND p.property_type = $property_type ";
    }
    if ($usernhanvien > 0) {
        // Giả sử trường trong bảng là p.product_type, thay tên trường nếu khác
        $where .= " AND p.user = $usernhanvien ";
    }

    /*if ($tenduan > 0) {
        // Giả sử trường trong bảng là p.province_id, thay tên trường nếu khác
        $where .= " AND p.name_area = $tenduan ";
    }*/
    if (!empty($tenduan)) {
        $ids = implode(',', $tenduan);
        $where .= " AND p.name_area IN ($ids) ";
    }


    if ($width_from > 0) {
        $where .= " AND p.dtd_ngang >= $width_from";
    }
    if ($width_to > 0) {
        $where .= " AND p.dtd_ngang <= $width_to";
    }

    if ($long_from > 0) {
        $where .= " AND p.dtd_dai >= $long_from";
    }
    if ($long_to > 0) {
        $where .= " AND p.dtd_dai <= $long_to";
    }

    /* $giaban_from *= 1000000000;
     $giaban_to   *= 1000000000;

     if ($giaban_from > 0) {
         $where .= " AND p.giaban >= $giaban_from";
     }
     if ($giaban_to > 0) {
         $where .= " AND p.giaban <= $giaban_to";
     }*/
    $giaban_from = normalize_price_input($_POST['giabanfrom'] ?? 0);
    $giaban_to   = normalize_price_input($_POST['giabanto'] ?? 0);

    if ($giaban_from > 0 || $giaban_to > 0) {

        $priceWhere = [];

        // Lọc giá bán
        $subBan = [];
        if ($giaban_from > 0) {
            $subBan[] = "p.giaban >= $giaban_from";
        }
        if ($giaban_to > 0) {
            $subBan[] = "p.giaban <= $giaban_to";
        }
        if (!empty($subBan)) {
            $priceWhere[] = "(" . implode(" AND ", $subBan) . ")";
        }

        // Lọc giá thuê
        $subThue = [];
        if ($giaban_from > 0) {
            $subThue[] = "p.giathue >= $giaban_from";
        }
        if ($giaban_to > 0) {
            $subThue[] = "p.giathue <= $giaban_to";
        }
        if (!empty($subThue)) {
            $priceWhere[] = "(" . implode(" AND ", $subThue) . ")";
        }

        if (!empty($priceWhere)) {
            // Giá bán HOẶC giá thuê match
            $where .= " AND (" . implode(" OR ", $priceWhere) . ")";
        }
    }



    if ($dtdcongnhan_from > 0) {
        $where .= " AND p.dtd_dai >= $dtdcongnhan_from";
    }
    if ($dtdcongnhan_to > 0) {
        $where .= " AND p.dtd_dai <= $dtdcongnhan_to";
    }


    if ($trangthaigiaodich > 0) {
        // Giả sử trường trong bảng là p.product_type, thay tên trường nếu khác
        $where .= " AND p.tinhtranggiaodich = $trangthaigiaodich ";
    }
    if ($sanphamhot > 0) {
        // Giả sử trường trong bảng là p.product_type, thay tên trường nếu khác
        $where .= " AND p.sanphamhot = $sanphamhot ";
    }if ($thangmay > 0) {
        // Giả sử trường trong bảng là p.product_type, thay tên trường nếu khác
        $where .= " AND p.thangmay = $thangmay ";
    }
    if ($sanphammoi == 1) {
        $one_week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        $where .= " AND p.datecreate >= '$one_week_ago'";
    }

    $is_sort_price_up   = isset($_POST['sort_field']) && $_POST['sort_field'] === 'giaban' && $_POST['sort_order'] === 'asc';
    $is_sort_price_down = isset($_POST['sort_field']) && $_POST['sort_field'] === 'giaban' && $_POST['sort_order'] === 'desc';


    if ($is_sort_price_up) {
        $where .= " AND p.giaban > g.giaban";
    } elseif ($is_sort_price_down) {
        $where .= " AND p.giaban < g.giaban";
    }


    if (!empty($_POST['filters'])) {
        //wp_die('<pre>' . print_r($_POST, true) . '</pre>');

        $filters = json_decode(stripslashes($_POST['filters']), true);
        if ($filters && isset($filters['rules'])) {
            foreach ($filters['rules'] as $filter) {
                $field = esc_sql($filter['field']);
                $value = esc_sql($filter['data']);
                // Kiểm tra bộ lọc "getname_column_first"
                if ($field === 'getname_column_first') {
                    if ($filter['op'] === 'cn') {
                        $like_value = '%' . $wpdb->esc_like($filter['data']) . '%';
                        $where .= " AND (
                (SELECT name FROM {$wpdb->terms} WHERE term_id = p.transaction_type) LIKE '$like_value' 
                OR
                (SELECT name FROM {$wpdb->terms} WHERE term_id = p.property_type) LIKE '$like_value'
            )";
                    } elseif ($filter['op'] === 'eq') {
                        $eq_value = $wpdb->esc_sql($filter['data']);
                        $where .= " AND (
                (SELECT name FROM {$wpdb->terms} WHERE term_id = p.transaction_type) = '$eq_value' 
                OR
                (SELECT name FROM {$wpdb->terms} WHERE term_id = p.property_type) = '$eq_value'
            )";
                    }
                }else if ($field === 'getname_column_two') {

                    $search = $filter['data'];

                    if ($filter['op'] === 'cn') {
                        $like_value = '%' . $wpdb->esc_like($search) . '%';

                        $where .= " AND (
        LOWER((SELECT name FROM {$wpdb->terms} WHERE term_id = p.vitri)) LIKE LOWER('$like_value')
        OR LOWER((SELECT TenLoai FROM wp_duan WHERE id = p.name_area)) LIKE LOWER('$like_value')
        OR LOWER(COALESCE(NULLIF(p.sonha, ''), p.code_product)) LIKE LOWER('$like_value')
    )";

                    } elseif ($filter['op'] === 'eq') {
                        $eq_value = $wpdb->esc_sql($search);

                        $where .= " AND (
        LOWER((SELECT name FROM {$wpdb->terms} WHERE term_id = p.vitri)) = LOWER('$eq_value')
        OR LOWER((SELECT TenLoai FROM wp_duan WHERE id = p.name_area)) = LOWER('$eq_value')
        OR LOWER(COALESCE(NULLIF(p.sonha, ''), p.code_product)) = LOWER('$eq_value')
    )";
                    }



                    //echo "<pre>"; print_r("SELECT * FROM `$selected_table` AS p $where"); exit;

                }
                else if ($field === 'getname_column_duongxakhuvuc') {

                    $search = $filter['data'];

                    if ($filter['op'] === 'cn') {
                        $like_value = '%' . $wpdb->esc_like($search) . '%';

                        $where .= " AND (
            LOWER(p.road) LIKE LOWER('$like_value')
            OR LOWER(p.khuvuc) LIKE LOWER('$like_value')
        )";

                    } elseif ($filter['op'] === 'eq') {
                        $eq_value = $wpdb->esc_sql($search);

                        $where .= " AND (
            LOWER(p.road) = LOWER('$eq_value')
            OR LOWER(p.khuvuc) = LOWER('$eq_value')
        )";
                    }

                    // echo "<pre>"; print_r("SELECT * FROM `$selected_table` AS p $where"); exit;
                }

                else if ($field === 'wp_district_name') {
                    $search = $filter['data'];

                    if ($filter['op'] === 'cn') { // contains
                        $like_value = '%' . $wpdb->esc_like($search) . '%';

                        $where .= " AND p.wp_district IN (
            SELECT id FROM wp_district
            WHERE LOWER(name) LIKE LOWER('$like_value')
        )";

                    } elseif ($filter['op'] === 'eq') { // equals
                        $eq_value = $wpdb->esc_sql($search);

                        $where .= " AND p.wp_district IN (
            SELECT id FROM wp_district
            WHERE LOWER(name) = LOWER('$eq_value')
        )";
                    }

                    // echo "<pre>"; print_r("SELECT * FROM `$selected_table` AS p $where"); exit;
                }

                else if ($field === 'wp_ward_name') {
                    $search = $filter['data'];

                    if ($filter['op'] === 'cn') { // contains
                        $like_value = '%' . $wpdb->esc_like($search) . '%';

                        $where .= " AND p.wp_wards IN (
            SELECT id FROM wp_wards
            WHERE LOWER(name) LIKE LOWER('$like_value')
        )";

                    } elseif ($filter['op'] === 'eq') { // equals
                        $eq_value = $wpdb->esc_sql($search);

                        $where .= " AND p.wp_wards IN (
            SELECT id FROM wp_wards
            WHERE LOWER(name) = LOWER('$eq_value')
        )";
                    }

                    // echo "<pre>"; print_r("SELECT * FROM `$selected_table` AS p $where"); exit;
                }
                else if ($field === 'getname_column_three') {
                    $search = $filter['data'];

                    if ($filter['op'] === 'cn') { // contains
                        $like_value = '%' . $wpdb->esc_like($search) . '%';

                        $where .= " AND (
            p.dtd_congnhan LIKE '$like_value'
            OR p.dtd_dai LIKE '$like_value'
            OR p.dtd_ngang LIKE '$like_value'
        )";

                    } elseif ($filter['op'] === 'eq') { // equals
                        $eq_value = $wpdb->esc_sql($search);

                        $where .= " AND (
            p.dtd_congnhan = '$eq_value'
            OR p.dtd_dai = '$eq_value'
            OR p.dtd_ngang = '$eq_value'
        )";
                    }


                }
                else if ($field === 'capnhat') {

                    $statusId = intval($filter['data']);

                    // jqGrid đang gửi op = cn → cho phép luôn
                    if ($filter['op'] === 'eq' || $filter['op'] === 'cn') {
                        $where .= " AND p.tinhtranggiaodich = $statusId ";
                    }
                    elseif ($filter['op'] === 'ne') {
                        $where .= " AND p.tinhtranggiaodich != $statusId ";
                    }
                }



                else {
                    $value = '%' . $wpdb->esc_like($filter['data']) . '%';
                    $where .= " AND LOWER(p.$field) LIKE LOWER('$value')";
                }
                //echo "<pre>"; print_r("SELECT * FROM `$selected_table` as p $where"); exit;



            }
        }
    }

    // Get total records count with filters
    $total_records = $wpdb->get_var("SELECT COUNT(*) FROM `$selected_table` as p $where");
    //$total_records = $wpdb->get_var("SELECT COUNT(*) FROM `$selected_table`");
    $total_pages = ($total_records > 0) ? ceil($total_records / $limit) : 1;

    // Fetch paginated and sorted data

    if($selected_table == "wp_dulieunhadat"){
        $rows = $wpdb->get_results("
        SELECT 
            p.*, 
            g.giaban AS giaban_goc,
            g.giathue AS giathue_goc
        FROM `$selected_table` AS p
        LEFT JOIN wp_dulieunhadat_goc AS g ON p.id = g.id
        $where
        ORDER BY p.`$sort_field` $sort_order
        LIMIT $start, $limit
    ", ARRAY_A);
    }else if($selected_table == "wp_khachhang"){
        $current_user = wp_get_current_user();
        $user_id      = get_current_user_id();
        // Nếu không phải admin thì chỉ lấy theo user_id
        if ( ! in_array( 'administrator', (array) $current_user->roles ) ) {
            $where .= $wpdb->prepare(" AND p.user = %d", $user_id);
        }

        $query = $wpdb->prepare(
            "SELECT * FROM `$selected_table` as p 
     $where 
     ORDER BY `$sort_field` $sort_order 
     LIMIT %d, %d",
            $start,
            $limit
        );

        $rows = $wpdb->get_results($query, ARRAY_A);

    }
    else{
        $rows = $wpdb->get_results("SELECT * FROM `$selected_table` as p $where ORDER BY `$sort_field` $sort_order LIMIT $start, $limit", ARRAY_A);

    }



    //echo "<pre";print_r("");exit;






    // Post-process: Attach category name
    $transactionTypeCache = [];
    $propertyTypeCache = [];
    $vitriCache = [];

    if($selected_table == "wp_khachhang"){
        foreach ($rows as &$row) {
            // Process transaction_type
            if (isset($row['loai'])) {
                $term_id = $row['loai'];

                if (!isset($transactionTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $transactionTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['loai_name'] = $transactionTypeCache[$term_id];
            } else {
                $row['loai_name'] = null;
            }
            if (isset($row['trangthai'])) {
                $term_id = $row['trangthai'];

                if (!isset($transactionTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $transactionTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['trangthai_name'] = $transactionTypeCache[$term_id];
            } else {
                $row['trangthai_name'] = null;
            }
            $userCache = []; // khai báo cache trước vòng lặp
            if (!empty($row['user'])) {
                $user_id = intval($row['user']); // đảm bảo là số nguyên

                if (!isset($userCache[$user_id])) {
                    $user = get_userdata($user_id);
                    $userCache[$user_id] = ($user) ? $user->display_name : "";
                }

                $row['user_name'] = $userCache[$user_id];
            } else {
                $row['user_name'] = null;
            }


        }
        unset($row);
    }

    if($selected_table == "wp_dulieunhadat"){
        foreach ($rows as &$row) {
            // Process transaction_type
            if (isset($row['transaction_type'])) {
                $term_id = $row['transaction_type'];

                if (!isset($transactionTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $transactionTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['transaction_type_name'] = $transactionTypeCache[$term_id];
            } else {
                $row['transaction_type_name'] = null;
            }

            // Process property_type
            if (isset($row['property_type'])) {
                $term_id = $row['property_type'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['property_type_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['property_type_name'] = null;
            }
            // Process vitri
            if (isset($row['vitri'])) {
                $term_id = $row['vitri'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['vitri_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['vitri_name'] = null;
            }

            // Combine the two into one label
            $row['getname_column_first'] = '<span><b style="    background: #ffa500;
    padding: 5px;
    color: #fff;
    border-radius: 5px;">'.$row['transaction_type_name'].' ' . '</b><br><hr style="margin: 3px; 
width: 80%; border: none; border-top: 1px solid #ccc; ">' . $row['property_type_name'].'</span>';
// Nếu có code_product thì dùng, không thì dùng sonha
// Lấy ID dự án
            $duan_id = $row['name_area'];

// Truy vấn TenLoai từ bảng wp_duan
            $ten_loai = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT TenLoai FROM wp_duan WHERE ID = %d",
                    $duan_id
                )
            );


            $main_value = !empty($row['code_product']) ? $row['code_product'] : $row['sonha'];
            $row['getname_column_two'] = '<p style="
    font-size: 10px;
    background: #fff;
    padding: 2px 5px;
    width: max-content;
    color: #00974c;
    border-radius: 3px;
    text-transform: uppercase;
    box-shadow: 0px 0px 2px #ccc;
    margin-bottom: 2px;">'.$row['vitri_name'] .'</p><div style="color: #0000ca;
    font-style: italic;">'.$main_value.'</div><p style="margin-top: 5px; background: #aeaeae; color: #fff; 
    padding: 2px; width: max-content; border-radius: 5px;">'.$ten_loai.'</p>';

// Get wp_district name based on wp_district_id (assuming wp_district_id is stored in your table)
            if (isset($row['wp_district'])) {
                $wp_district_id = $row['wp_district'];
                $wp_district_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_district WHERE id = %d", $wp_district_id));

                if ($wp_district_name) {
                    $row['wp_district_name'] = mb_strtoupper($wp_district_name, 'UTF-8'); // viết hoa
                } else {
                    $row['wp_district_name'] = "NOT FOUND";
                }
            } else {
                $row['wp_district_name'] = "NO WP_DISTRICT ID";
            }

            if (isset($row['wp_wards'])) {
                $wp_ward_id = $row['wp_wards'];
                $wp_ward_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_wards WHERE id = %d", $wp_ward_id));

                if ($wp_ward_name) {
                    $row['wp_ward_name'] = mb_strtoupper($wp_ward_name, 'UTF-8'); // viết hoa
                } else {
                    $row['wp_ward_name'] = "NOT FOUND";
                }
            } else {
                $row['wp_ward_name'] = "NO WP_WARDS ID";
            }



            $get_khuvuc_html = '';
            if (!empty($row['khuvuc'])) {
                $get_khuvuc_html = '<p>'.mb_strtoupper($row['khuvuc'], 'UTF-8').'</p>';
            }

            $get_road_html = '<p style="margin:0px;">'.mb_strtoupper($row['road'], 'UTF-8').'</p>';

            $row['getname_column_duongxakhuvuc'] = $get_khuvuc_html . $get_road_html;




            if (isset($row['donvi_giaban'])) {
                $term_id = $row['donvi_giaban'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['donvi_giaban_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['donvi_giaban_name'] = null;
            }
            if (isset($row['donvi_giathue'])) {
                $term_id = $row['donvi_giathue'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['donvi_giathue_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['donvi_giathue_name'] = null;
            }
            if (isset($row['donvi_thoigiangia'])) {
                $term_id = $row['donvi_thoigiangia'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['donvi_thoigiangia_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['donvi_thoigiangia_name'] = null;
            }
            if (isset($row['tinhtranggiaodich'])) {
                $term_id = $row['tinhtranggiaodich'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['tinhtranggiaodich_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['tinhtranggiaodich_name'] = null;
            }


            $giaban_moi = (int) $row['giaban'];
            $giaban_goc = (int) $row['giaban_goc'];  // từ JOIN
            $giathue_moi = (int) $row['giathue'];
            $giathue_goc = (int) $row['giathue_goc'];  // cần JOIN thêm nếu chưa có

            $tinhtrang_gia = '';
            $color = '#000';

// Xác định nên hiển thị giá nào
            if ($giaban_moi == 0.00 && $giathue_moi != 0.00) {
                $giatri = format_number_to_vietnamese($giathue_moi) . ' ' . $row['donvi_giathue_name'];
                $color = '#007b00'; // Màu xanh cho thuê

                // So sánh giá thuê mới và gốc
                if ($giathue_goc > 0) {
                    if ($giathue_moi > $giathue_goc) {
                        $tinhtrang_gia = '<i class="fa fa-arrow-up" style="color: #28a745;"></i>';
                    } elseif ($giathue_moi < $giathue_goc) {
                        $tinhtrang_gia = '<i class="fa fa-arrow-down" style="color: #dc3545;"></i>';
                    } else {
                        $tinhtrang_gia = '<i class="fa fa-circle" style="color: #ffc107;"></i>';
                    }
                }

            } else {
                $giatri = format_number_to_vietnamese($giaban_moi) . ' ' . $row['donvi_giaban_name'];
                $color = '#ff0000'; // Màu đỏ cho bán

                // So sánh giá bán mới và gốc
                if ($giaban_goc > 0) {
                    if ($giaban_moi > $giaban_goc) {
                        $tinhtrang_gia = '<i class="fa fa-arrow-up" style="color: #28a745;"></i>';
                    } elseif ($giaban_moi < $giaban_goc) {
                        $tinhtrang_gia = '<i class="fa fa-arrow-down" style="color: #dc3545;"></i>';
                    } else {
                        $tinhtrang_gia = '<i class="fa fa-circle" style="color: #ffc107;"></i>';
                    }
                }
            }

            $row['giaban_view'] = '<b style="color: ' . $color . '; font-weight:bold;">' . $giatri . '</br>' . $tinhtrang_gia . '</b>';

            if ((float)$row['giaban'] != 0.00 && (float)$row['giathue'] != 0.00) {
                $row['giathue_view'] = '<b style="color: #ff0000; font-weight:bold;">'
                    . format_number_to_vietnamese($row['giathue'])
                    . '</b> ' . $row['donvi_giathue_name'] . '</br>'
                    . $row['donvi_thoigiangia_name'];
            } else {
                $row['giathue_view'] = ''; // Không hiển thị gì nếu có giaban
            }



// Giả sử $row['dateupdate'] là dạng 'Y-m-d' hoặc 'Y-m-d H:i:s'
            // Nếu dateupdate null hoặc rỗng thì lấy datecreate
            $ngayNguon = !empty($row['dateupdate']) ? $row['dateupdate'] : $row['datecreate'];

// Chuyển sang timestamp
            $timestamp = strtotime($ngayNguon);

// Tính số ngày đã trôi qua
            $soNgay = (time() - $timestamp) / (60 * 60 * 24);

            /*if ($soNgay <= 30) {
                // Còn hạn
                // Map trạng thái & màu
                $statusMap = [
                    "681" => [ "name" => "Đã cọc", "color" => "#42a795" ],
                    "677" => [ "name" => "Đã giao dịch", "color" => "#1621dc" ],
                    "675" => [ "name" => "Đang giao dịch", "color" => "#28a745" ],
                    "676" => [ "name" => "Ngưng giao dịch", "color" => "#6c757d" ],
                    // thêm các trạng thái khác nếu có
                ];

// Lấy màu theo trạng thái
                $color = $statusMap[$row['tinhtranggiaodich']]['color'] ?? '#6c757d'; // mặc định xám nếu ko có

// Render view
                $row['capnhat_view'] = '<span style="margin-top: 5px; background: ' . $color . '; color: #fff;
    padding: 2px; width: max-content; border-radius: 5px;">'
                    . ($statusMap[$row['tinhtranggiaodich']]['name'] ?? $row['tinhtranggiaodich_name'])
                    . '</span>';

            } else {
                // Hết hạn
                $row['capnhat_view'] = '<span style="margin-top: 5px; background: #f44336; color: #fff;
        padding: 2px; width: max-content; border-radius: 5px;">Hết hạn</span>';
            }*/

            // Còn hạn
            // Map trạng thái & màu
            $statusMap = [
                "681" => [ "name" => "Đã cọc", "color" => "#42a795" ],
                "677" => [ "name" => "Đã giao dịch", "color" => "#1621dc" ],
                "675" => [ "name" => "Đang giao dịch", "color" => "#28a745" ],
                "676" => [ "name" => "Ngưng giao dịch", "color" => "#6c757d" ],
                // thêm các trạng thái khác nếu có
            ];

// Lấy màu theo trạng thái
            $color = $statusMap[$row['tinhtranggiaodich']]['color'] ?? '#6c757d'; // mặc định xám nếu ko có

// Render view
            $row['capnhat_view'] = '<span style="margin-top: 5px; background: ' . $color . '; color: #fff;
    padding: 2px; width: max-content; border-radius: 5px;">'
                . ($statusMap[$row['tinhtranggiaodich']]['name'] ?? $row['tinhtranggiaodich_name'])
                . '</span>';



            /*$row['capnhat_view'] = '<span style="margin-top: 5px; background: #4caf50; color: #fff;
    padding: 2px; width: max-content; border-radius: 5px;">' .$row['tinhtranggiaodich_name'].'</span>';*/

            $row['getname_column_three'] = '<p style="
        font-size: 14px;
    background: #fff;
    padding: 2px 5px;
    width: max-content;
    color: #ff0000;
    border-radius: 3px;
    box-shadow: 0px 0px 2px #ccc;
    margin-bottom: 2px;
    font-weight: bold;">'.$row['dtd_congnhan'] .' m²</p><p style="margin-top: 5px; background: #4caf50; color: #fff; 
    padding: 2px; width: max-content; border-radius: 5px;">('.$row['dtd_ngang'].'m x '.$row['dtd_dai'].'m )</p>';

//echo "<pre>";print_r($row);exit;
            //  xử lý phần view
            if (isset($row['product_type'])) {
                $term_id = $row['product_type'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['product_type_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['product_type_name'] = null;
            }
            if (isset($row['wp_province'])) {
                $wp_province_id = $row['wp_province']; // The ID of the wp_province stored in the row
                $wp_province_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_province WHERE id = %d", $wp_province_id));

                if ($wp_province_name) {
                    $row['wp_province_name'] = $wp_province_name;
                } else {
                    $row['wp_province_name'] = "Not Found"; // Default message if no wp_province is found
                }
            } else {
                $row['wp_province_name'] = "No wp_province ID"; // Default message if no wp_province_id is set
            }
            if (isset($row['name_area'])) {
                $name_area_id = $row['name_area']; // The ID of the wp_province stored in the row
                $name_area_name = $wpdb->get_var($wpdb->prepare("SELECT TenLoai FROM wp_duan WHERE id = %d", $name_area_id));

                if ($name_area_name) {
                    $row['name_area_name'] = $name_area_name;
                } else {
                    $row['name_area_name'] = "Not Found"; // Default message if no name_area_name is found
                }
            } else {
                $row['name_area_name'] = "No wp_province ID"; // Default message if no name_area_name is set
            }
            if (isset($row['vaitro'])) {
                $term_id = $row['vaitro'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['vaitro_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['vaitro_name'] = null;
            }
            if (!empty($row['contact_info'])) {
                $contactInfo = is_array($row['contact_info']) ? $row['contact_info'] : json_decode($row['contact_info'], true);

                foreach ($contactInfo as &$contact) {
                    if (isset($contact['vaitro_more'])) {
                        $term_id = $contact['vaitro_more'];
                        if (!isset($propertyTypeCache[$term_id])) {
                            $term = get_term($term_id, 'category');
                            $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                        }
                        $contact['vaitro_more_name'] = $propertyTypeCache[$term_id];
                    } else {
                        $contact['vaitro_more_name'] = null;
                    }
                }

                // Gán lại JSON vào $row để đẩy sang JS
                $row['contact_info'] = json_encode($contactInfo, JSON_UNESCAPED_UNICODE);
            }

            if (isset($row['loaitaisan'])) {
                $term_id = $row['loaitaisan'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['loaitaisan_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['loaitaisan_name'] = null;
            }

            if (isset($row['tinhtranggiaodich'])) {
                $term_id = $row['tinhtranggiaodich'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['tinhtranggiaodich_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['tinhtranggiaodich_name'] = null;
            }
            if (isset($row['gioitinh'])) {
                $term_id = $row['gioitinh'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['gioitinh_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['gioitinh_name'] = null;
            }
            if (isset($row['ttk_loaidat'])) {
                $term_id = $row['ttk_loaidat'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['ttk_loaidat_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['ttk_loaidat_name'] = null;
            }
            if (isset($row['ttk_huong'])) {
                $term_id = $row['ttk_huong'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['ttk_huong_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['ttk_huong_name'] = null;
            }
            if (isset($row['loaihoahong'])) {
                $term_id = $row['loaihoahong'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['loaihoahong_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['loaihoahong_name'] = null;
            }
            if (isset($row['donvi_thoigianthue'])) {
                $term_id = $row['donvi_thoigianthue'];

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category');
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : "";
                }

                $row['donvi_thoigianthue_name'] = $propertyTypeCache[$term_id];
            } else {
                $row['donvi_thoigianthue_name'] = null;
            }


            // view hinh anh
            if (isset($row['sohong_image_id'])) {
                $image_url = wp_get_attachment_image_url($row['sohong_image_id'], 'medium'); // hoặc 'full'
                $row['sohong_image_id_link'] = $image_url;
            } else {
                $row['sohong_image_id_link'] = null;
            }
            if (isset($row['bosung_image_id'])) {
                $image_url = wp_get_attachment_image_url($row['bosung_image_id'], 'medium'); // hoặc 'full'
                $row['bosung_image_id_link'] = $image_url;
            } else {
                $row['bosung_image_id_link'] = null;
            }
            if (!empty($row['sohong_image_multi'])) {
                // Giữ nguyên chuỗi ID gốc (nếu bạn cần)

                // Gán mảng URL từ hàm bạn viết
                $row['sohong_image_multi_urls'] = get_image_urls_from_ids($row['sohong_image_multi']);
            } else {
                $row['sohong_image_multi_urls'] = [];
            }


            // view phone
            /*  if (function_exists('spiritwebs_user_can') && spiritwebs_user_can('view_phone')){
                  $row['dienthoaididong'] = $row['dienthoaididong'];
              }else{
                  $row['dienthoaididong'] = "*********";
              }*/

            $tang = trim($row['ttk_sotang']);
            $ham = trim($row['ttk_sotangham']);

            if (!empty($tang) || !empty($ham)) {
                $row['getname_column_four'] = '<span>'
                    . (!empty($tang) ? '<b>' . $tang . ' tầng</b>' : '')
                    . (!empty($ham) ? '<br>' . $ham . ' hầm' : '')
                    . '</span>';
            } else {
                $row['getname_column_four'] = '';
            }



            if (isset($row['user'])) {
                $user_id = $row['user'];

                if (!isset($userCache[$user_id])) {
                    $user_info = get_userdata($user_id);
                    $first_name = get_user_meta($user_id, 'first_name', true);
                    $last_name = get_user_meta($user_id, 'last_name', true);
                    $phone = get_user_meta($user_id, 'dien_thoai', true); // hoặc thay bằng key thực tế của bạn
                    $ngayvaolam = get_user_meta($user_id, 'ngay_vao_lam', true); // hoặc thay bằng key thực tế của bạn
                    // Lấy avatar
                    $user_avatar_url = get_avatar_url($user_id, ['size' => 96]);

                    if ($user_info) {
                        $userCache[$user_id] = [
                            'display_name' => $user_info->display_name,
                            'first_name' => $first_name ?: '',
                            'last_name' => $last_name ?: '',
                            'phone' => $phone ?: '',
                            'ngay_vao_lam' => $ngayvaolam ?: '',
                            'user_avatar_url' => $user_avatar_url
                        ];
                    } else {
                        $userCache[$user_id] = [
                            'display_name' => '',
                            'first_name' => '',
                            'last_name' => '',
                            'phone' => '',
                            'ngay_vao_lam' => '',
                            'user_avatar_url' => ''
                        ];
                    }
                }

                $row['user_display_name'] = $userCache[$user_id]['display_name'];
                $row['user_first_name'] = $userCache[$user_id]['first_name'];
                $row['user_last_name'] = $userCache[$user_id]['last_name'];
                $row['user_phone'] = $userCache[$user_id]['phone'];
                $row['ngay_vao_lam'] = $userCache[$user_id]['ngay_vao_lam'];
                $row['user_avatar_url']   = $userCache[$user_id]['user_avatar_url']; // ← thêm dòng này
            } else {
                $row['user_display_name'] = null;
                $row['user_first_name'] = null;
                $row['user_last_name'] = null;
                $row['user_phone'] = null;
                $row['ngay_vao_lam'] = null;
                $row['user_avatar_url']   = null;
            }



            // xu ly phan quyen so lan xem cua role
            // Lấy user hiện tại và role
            $current_user = wp_get_current_user();
            $user_role = $current_user->roles[0] ?? '';

// Nếu là admin, luôn được xem
            if ($user_role === 'administrator') {
                $row["can_view_phone"] = true;
            } else {
                // Lấy giới hạn lượt xem từ option
                $role_caps = get_option('company_roles_caps', []);
                $limit_view = isset($role_caps[$user_role]['number_view_phone'])
                    ? intval($role_caps[$user_role]['number_view_phone'])
                    : 0;

                if ($limit_view > 0) {
                    // Lấy ngày hôm nay theo múi giờ WordPress
                    $today = current_time('Y-m-d');

                    $table_view = $wpdb->prefix . 'luot_xem_sdt';

                    // Đếm số lượt xem hôm nay
                    $so_luot_xem = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*)  FROM {$table_view} WHERE nguoidung_id = %d AND DATE(thoi_gian) = %s",
                        $current_user->ID,
                        $today
                    ));

                    $row["can_view_phone"] = $so_luot_xem < $limit_view;
                } else {
                    $row["can_view_phone"] = true; // không giới hạn
                }
            }






        }
        unset($row);
    }
//echo "<pre";print_r($rows);exit;




    wp_send_json([
        "page" => $page,
        "total" => $total_pages,
        "records" => $total_records,
        "rows" => $rows
    ]);
}

add_action("wp_ajax_load_jqgrid_data", "load_jqgrid_data");
add_action("wp_ajax_nopriv_load_jqgrid_data", "load_jqgrid_data");

add_action('wp_ajax_get_province_list', 'ajax_get_province_list');
add_action('wp_ajax_nopriv_get_province_list', 'ajax_get_province_list');

function ajax_get_province_list() {
    // Lấy từ khóa tìm kiếm từ Ajax
    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';

    // Truyền keyword vào hàm lấy dữ liệu
    $provinces = get_wp_provinces($keyword);

    $formatted = [];
    foreach ($provinces as $id => $name) {
        $formatted[] = [
            'id' => $id,
            'text' => $name
        ];
    }

    wp_send_json_success($formatted);
}

function get_wp_provinces($keyword = '') {
    global $wpdb;

    // Câu truy vấn có lọc nếu có keyword
    if (!empty($keyword)) {
        $sql = $wpdb->prepare("SELECT id, `name` FROM wp_province WHERE `name` LIKE %s", '%' . $wpdb->esc_like($keyword) . '%');
    } else {
        $sql = "SELECT id, `name` FROM wp_province ";
    }

    $results = $wpdb->get_results($sql);
    $options = [];

    if ($results) {
        foreach ($results as $row) {
            $options[$row->id] = $row->name;
        }
    }

    return $options;
}
function get_nhanvien_list() {
    global $wpdb;

    $sql = "SELECT ID, display_name FROM $wpdb->users ORDER BY display_name ASC";
    $results = $wpdb->get_results($sql);
    $options = [];

    if (!empty($results)) {
        foreach ($results as $user) {
            $options[$user->ID] = $user->display_name;
        }
    }

    return $options;
}




add_action('wp_ajax_get_duan_list', 'ajax_get_duan_list');
add_action('wp_ajax_nopriv_get_duan_list', 'ajax_get_duan_list');

function ajax_get_duan_list() {
    global $wpdb;

    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';

    $sql = "SELECT id, `TenLoai` FROM wp_duan";
    if (!empty($keyword)) {
        $sql .= $wpdb->prepare(" WHERE `TenLoai` LIKE %s", '%' . $wpdb->esc_like($keyword) . '%');
    }

    $results = $wpdb->get_results($sql);

    $formatted = [];
    if ($results) {
        foreach ($results as $row) {
            $formatted[] = [
                'id' => $row->id,
                'text' => $row->TenLoai
            ];
        }
    }

    wp_send_json_success($formatted);
}

function get_wp_duan() {
    global $wpdb;

    // Query to get id and name from the wp_province table
    $sql = "SELECT id, `TenLoai` FROM wp_duan";  // Replace 'wp_province' with your actual table name

    // Execute the query and fetch the results
    $results = $wpdb->get_results($sql);

    // Initialize an array to store the options
    $options = [];

    // Loop through the results and store them in the options array
    if ($results) {
        foreach ($results as $row) {
            // Use the id as the key and the name as the value
            $options[$row->id] = $row->TenLoai;
        }
    }

    return $options;
}

function parseEditRules($rulesString) {
    $rulesArray = [];
    $rules = explode(";", $rulesString);

    foreach ($rules as $rule) {
        $parts = explode(":", $rule);
        if (count($parts) == 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);

            // Convert string "true"/"false" to boolean
            $rulesArray[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
    }
    return $rulesArray;
}
// get colModel
//xulyselectedit
function get_jqgrid_colmodel() {
    global $wpdb;

    // Ensure a table is specified
    if (!isset($_POST['table']) || empty($_POST['table'])) {
        wp_send_json_error(["message" => "No table specified"]);
    }

    $selected_table = esc_sql($_POST['table']); // Sanitize table name

    //xu ly table khach hang


    // Fetch column details from wp_jq_sys_format
    $columns = $wpdb->get_results("SELECT tables, `name` AS field, label, width, align, searchoptions, editable, editrules,  edittype,`key`, hidden, `order`,`active`
FROM wp_jq_sys_format WHERE active = 1 AND tables = '$selected_table'  ORDER BY `order` ASC", ARRAY_A);

    if (!$columns) {
        wp_send_json_error(["message" => "No column definitions found in wp_jq_sys_format for table: $selected_table"]);
    }

    $colModel = array_map(function ($col) use ($wpdb, $selected_table) {
        $column_name = esc_sql($col["field"]);
        $search_options = isset($col["searchoptions"]) ? $col["searchoptions"] : "";


        // xử lý phần sort
        $index = $column_name;
        $sorttype = 'text';

// Xử lý đặc biệt cho một số cột
        if ($column_name === 'giaban_view') {
            $index = 'giaban';
            $sorttype = 'number';
        }
        if ($column_name === 'getname_column_three') {
            $index = 'dtd_congnhan';
            $sorttype = 'number';
        }
        $colConfig = [
            "tables"     => $col["tables"],
            "name"       => $column_name,
            "index"      => $index,
            "key"        => filter_var($col["key"], FILTER_VALIDATE_BOOLEAN),
            "hidden"     => filter_var($col["hidden"], FILTER_VALIDATE_BOOLEAN),
            "sortable"   => true,
            "sorttype"   => $sorttype,
            "width"      => intval($col["width"]) ?: 100,
            "align"      => !empty($col["align"]) ? $col["align"] : "left",
            "label"      => $col["label"],
            "order"      => intval($col["order"]),
            "active"     => $col["active"],
            "editable"   => filter_var($col["editable"], FILTER_VALIDATE_BOOLEAN),
            "edittype"   => "text",
            "editrules"  => parseEditRules($col['editrules']),
            "editoptions"=> [],
        ];

        if (str_ends_with($column_name, '_view')) {
            $colConfig['index'] = str_replace('_view', '', $column_name);
            $colConfig['sorttype'] = 'number';
        }
        /* if ($column_name === 'id') {
             $colConfig["editable"] = false;
             $colConfig["hidden"] = true;
             $colConfig["editoptions"] = [
                 "readonly" => true
             ];
         }*/
        // For date input
        if ($column_name === 'created_at') {
            $colConfig["edittype"] = "text";
            $colConfig["editoptions"] = [
                "dataInit" => "function(el) { $(el).datepicker({ dateFormat: 'yy-mm-dd' }); }"
            ];
        }
        if ($col['edittype'] === 'textarea') {
            $colConfig["edittype"] = "textarea";
            $colConfig["editoptions"] = [
                "rows" => 4,
                "cols" => 40
            ];
        }


        if ($column_name === 'getname_column_first' || $column_name === 'getname_column_two' || $column_name === 'getname_column_duongxakhuvuc'
            || $column_name === 'getname_column_three' || $column_name === 'giaban_view' || $column_name === 'giathue_view' || $column_name === 'capnhat_view'|| $column_name === 'getname_column_four') {
            $colConfig["formatter"] = "html";
        }

        if ($column_name === 'dtd_dai' || $column_name === 'dtd_ngang' || $column_name === 'dtd_mathau' ||
            $column_name === 'dtqh_dai' || $column_name === 'dtqh_ngang' || $column_name === 'dtqh_mathau'
            || $column_name === 'ttk_duongrong') {
            $colConfig["editoptions"] = [
                "placeholder" => "m",
                "title" => "Giá trị nhập: m"
            ];
        }
        if ($column_name === 'dtqh_xaydung' || $column_name === 'dtd_congnhan' || $column_name === 'ttk_dtsd') {
            $colConfig["editoptions"] = [
                "placeholder" => "m²",
                "title" => "Giá trị nhập: m²"
            ];
        }

        if (!empty($col["editrules"]) && strpos($col["editrules"], "required:true") !== false) {
            $colConfig["label"] = "<i class='fa fa-asterisk' style='color:red; margin-right:5px;'></i>" . $colConfig["label"];
        }
        // If the column has searchoptions set to 'select', fetch distinct values
        if ($search_options === "select") {
            if ($column_name == 'capnhat_view') {

                $statusMap = [
                    "681" => [ "name" => "Đã cọc" ],
                    "677" => [ "name" => "Đã giao dịch" ],
                    "675" => [ "name" => "Đang giao dịch" ],
                    "676" => [ "name" => "Ngưng giao dịch" ],
                ];

                if (!empty($statusMap)) {

                    // Build dropdown options: value:label
                    $options = [];

                    foreach ($statusMap as $statusId => $item) {
                        $name = $item['name'];
                        $options[] = $statusId . ":" . $name;
                    }

                    // Option tất cả
                    array_unshift($options, ":Tất cả");

                    $colConfig["stype"] = "select";
                    $colConfig["searchoptions"] = [
                        "value" => implode(";", $options),
                        "sopt" => ["eq", "ne"]
                    ];
                }
            }

            if($column_name == 'wp_district_name'){
                // Custom logic for wp_district table
                $query = "SELECT DISTINCT `name` FROM `wp_district` WHERE `name` IS NOT NULL ORDER BY `name` ASC";
                $distinct_values = $wpdb->get_col($query);

                if ($distinct_values) {
                    // Example: capitalize wp_district names, or add "All" option, etc.
                    $options = array_map(fn($val) => ucfirst($val) . ":" . ucfirst($val), $distinct_values);
                    array_unshift($options, ":Tất cả"); // Optional blank/default option

                    $colConfig["stype"] = "select";
                    $colConfig["searchoptions"] = [
                        "value" => implode(";", $options),
                        "sopt" => ["eq", "ne"]
                    ];
                }
            }

            if($column_name == 'ward_name'){
                // Custom logic for wp_district table
                $query = "SELECT DISTINCT `name` FROM `wp_wards` WHERE `name` IS NOT NULL ORDER BY `name` ASC";
                $distinct_values = $wpdb->get_col($query);

                if ($distinct_values) {
                    // Example: capitalize wp_district names, or add "All" option, etc.
                    $options = array_map(fn($val) => ucfirst($val) . ":" . ucfirst($val), $distinct_values);
                    array_unshift($options, ":Tất cả"); // Optional blank/default option

                    $colConfig["stype"] = "select";
                    $colConfig["searchoptions"] = [
                        "value" => implode(";", $options),
                        "sopt" => ["eq", "ne"]
                    ];
                }
            }
            else{
                $query = "SELECT DISTINCT `$column_name` FROM `$selected_table` WHERE `$column_name` IS NOT NULL ORDER BY `$column_name` ASC";
                $distinct_values = $wpdb->get_col($query);

                if ($distinct_values) {
                    $options = array_map(fn($val) => "$val:$val", $distinct_values);
                    $colConfig["stype"] = "select";
                    $colConfig["searchoptions"] = [
                        "value" => implode(";", $options),
                        "sopt" => ["eq", "ne"] // Equals, Not Equals
                    ];
                }
            }

        }

        // form edit
        if ($col["edittype"] === "select") {
            $categories = get_the_category();

            // Example static values for select options
            if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "transaction_type"){
                $parent_id = 431; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "product_type"){
                $parent_id = 513; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "property_type"){
                $parent_id = 519; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "vitri"){
                $parent_id = 557; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "ttk_huong"){
                $parent_id = 567; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "ttk_loaidat"){
                $parent_id = 595; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "loaitaisan"){
                $parent_id = 609; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "vaitro"){
                $parent_id = 625; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "gioitinh"){
                $parent_id = 641; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && ($col["field"] == "donvi_giathue" || $col["field"] == "donvi_giaban")){
                $parent_id = 655; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "donvi_thoigiangia"){
                $parent_id = 661; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "donvi_thoigianthue"){
                $parent_id = 667; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "loaihoahong"){
                $parent_id = 647; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "tinhtranggiaodich"){
                $parent_id = 674; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "wp_province"){
                $options = get_wp_provinces();
            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "name_area"){
                $options = get_wp_duan();
            }
            else if($col["tables"] == 'wp_dulieunhadat' && ($col["field"] == "ttk_sotang" || $col["field"] == "ttk_sophongngu" || $col["field"] == "ttk_swc"
                    || $col["field"] == "ttk_sotangham")){
                $options = [];
                for ($i = 0; $i <= 50; $i++) {
                    $options[$i] = $i;
                }
            }
            //xulyphanselectedit
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "sanphamhot"){
                $parent_id = 678; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_dulieunhadat' && $col["field"] == "thangmay"){
                $parent_id = 682; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_khachhang' && $col["field"] == "loai"){
                $parent_id = 688; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }
            else if($col["tables"] == 'wp_khachhang' && $col["field"] == "trangthai"){
                $parent_id = 692; // Replace with the actual parent category ID
                $categories = get_categories([
                    'taxonomy'   => 'category',  // WordPress category taxonomy
                    'hide_empty' => false,       // Show even empty categories
                    'parent'     => $parent_id,  // Get child categories of this parent
                ]);

                $options = [];
                foreach ($categories as $category) {
                    $options[$category->term_id] = $category->name;
                }

            }else if($col["tables"] == 'wp_khachhang' && $col["field"] == "user"){
                $options = get_nhanvien_list();
            }
            else{

            }


            $colConfig["edittype"] = "select"; // Set the edit type to select

            // Map the options for the select input
            $optionString = '';
            foreach ($options as $value => $label) {
                $optionString .= "$value:$label;";
            }

            // Set editoptions to include the select options
            $colConfig["editoptions"] = [
                "value" => ":;" . rtrim($optionString, ';'), // Create a string like "value1:label1;value2:label2;"
            ];
        }
        return $colConfig;
    }, $columns);

    wp_send_json(["success" => true, "colModel" => $colModel]);
}

add_action("wp_ajax_get_jqgrid_colmodel", "get_jqgrid_colmodel");
add_action("wp_ajax_nopriv_get_jqgrid_colmodel", "get_jqgrid_colmodel");

// function add data
// Function to add data
/*function add_jqgrid_data() {
    global $wpdb;

    if (!isset($_POST['table']) || empty($_POST['table'])) {
        wp_send_json_error(["message" => "No table specified"]);
    }
    echo "<pre";print_r($_POST);exit;

    // Get the current logged-in user
    $current_user = wp_get_current_user();

    // Check if the user is logged in
    if (!$current_user->ID) {
        wp_send_json_error(["message" => "User not logged in"]);
    }

    // Sanitize the table name from the POST data
    $selected_table = esc_sql($_POST['table']);

    // Get data from the POST request
    $data = $_POST;


    if($selected_table == 'wp_dulieunhadat'){
        // Bỏ các field không cần thiết
        unset($data['action'], $data['table'], $data['id'], $data['oper'], $data['pll_ajax_backend']);

// Thêm thông tin người dùng và thời gian tạo
        $data['user'] = $current_user->ID;
        $data['datecreate'] = date('Y-m-d H:i:s');

// Tên bảng gốc
        $selected_table_goc = 'wp_dulieunhadat_goc';

// Insert vào bảng chính
        $wpdb->insert($selected_table, $data);

// Lấy ID vừa được insert
        $inserted_id = $wpdb->insert_id;

// Thêm ID đó vào mảng $data để dùng cho bảng gốc
        $data['ID'] = $inserted_id; // Đảm bảo bảng gốc có cột 'ID' và KHÔNG có AUTO_INCREMENT

// Insert vào bảng gốc với ID giống bảng chính
        $wpdb->insert($selected_table_goc, $data);

    }else{
        // Remove unnecessary fields from the data array
        unset($data['action'], $data['table'], $data['id'], $data['oper'], $data['pll_ajax_backend']);

        // Add user information to the data (e.g., user ID)
        $data['user'] = $current_user->ID; // You can add more user-related info as needed
        $data['datecreate'] = date('Y-m-d H:i:s'); // Lấy thời điểm hiện tại
        $wpdb->insert($selected_table, $data);
    }

    // Check for any errors after the insert
    if ($wpdb->last_error) {
        wp_send_json_error(["message" => $wpdb->last_error]);
    } else {
        wp_send_json_success(["message" => "Record added successfully"]);
    }
}*/
//formadd
function add_jqgrid_data() {
    global $wpdb;

    if (!isset($_POST['table']) || empty($_POST['table'])) {
        wp_send_json_error(["message" => "No table specified"]);
    }

    // echo "<pre>"; print_r($_POST); exit; // Gỡ bỏ để không bị ngắt hàm

    $current_user = wp_get_current_user();
    if (!$current_user->ID) {
        wp_send_json_error(["message" => "User not logged in"]);
    }

    $selected_table = esc_sql($_POST['table']);
    $data = $_POST;

    // 👉 Gom người liên hệ nếu có
    $contactList = [];

    $vaitroArr = $data['vaitro_more'] ?? [];
    $nameArr = $data['name_more'] ?? [];
    $phoneArr = $data['dienthoaididong_more'] ?? [];
    $genderArr = $data['gioitinh_more'] ?? [];

    foreach ($nameArr as $i => $name) {
        $name = trim($name);
        $phone = $phoneArr[$i] ?? '';
        if ($name === '' && $phone === '') continue; // bỏ dòng rỗng

        $contactList[] = [
            'vaitro_more' => $vaitroArr[$i] ?? '',
            'name_more' => $name,
            'phone_more' => $phone,
            'gender_more' => $genderArr[$i] ?? '',

        ];
    }

    // 👉 Gán vào 1 field duy nhất
    if ($selected_table == 'wp_dulieunhadat') {
        $data['contact_info'] = json_encode($contactList, JSON_UNESCAPED_UNICODE);
        $data['tinhtranggiaodich_old'] = $data['tinhtranggiaodich'];
        // contact_all
        $contactAll = [
            'tinhtranggiaodich' => $data['tinhtranggiaodich'] ?? '0',
            'contacts_primary' => [
                'name'            => $data['name'] ?? '',
                'dienthoaididong' => $data['dienthoaididong'] ?? '',
                'vaitro'          => $data['vaitro'] ?? '',
                'gioitinh'        => $data['gioitinh'] ?? '',
            ],
            'contacts_info' => $contactList
        ];

        $data['contact_all'] = json_encode($contactAll, JSON_UNESCAPED_UNICODE);
    }

    // ❌ Xoá các field lẻ không còn dùng
    unset($data['vaitro_more'], $data['name_more'], $data['dienthoaididong_more'], $data['gioitinh_more']);

    // 🔁 Xử lý insert như cũ
    unset($data['action'], $data['table'], $data['id'], $data['oper'], $data['pll_ajax_backend']);

    $data['user'] = $current_user->ID;
    $data['datecreate'] = date('Y-m-d H:i:s');

    if ($selected_table == 'wp_dulieunhadat') {
        $selected_table_goc = 'wp_dulieunhadat_goc';
        $data['tinhtranggiaodich_old'] = $data['tinhtranggiaodich'];

        $wpdb->insert($selected_table, $data);
        $inserted_id = $wpdb->insert_id;

        $data['ID'] = $inserted_id;
        $wpdb->insert($selected_table_goc, $data);
    } else {
        $wpdb->insert($selected_table, $data);
    }

    if ($wpdb->last_error) {
        wp_send_json_error(["message" => $wpdb->last_error]);
    } else {

        // --- Gọi broadcast Phoenix để client reload ---
        wp_remote_post('https://socket.okinawanew.com/api/broadcast', [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode(['message' => 'new_data']), // chỉ gửi thông báo
            'method' => 'POST',
            'timeout' => 5,
        ]);

        wp_send_json_success(["message" => "Record added successfully"]);
    }
}


add_action("wp_ajax_add_jqgrid_data", "add_jqgrid_data");


// function edit data
/*function edit_jqgrid_data() {
    global $wpdb;
    error_log("Received POST data: " . print_r($_POST, true));

    if (!isset($_POST['table']) || empty($_POST['table'])) {
        wp_send_json_error(["message" => "No table specified"]);
    }

    $selected_table = esc_sql($_POST['table']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        wp_send_json_error(["message" => "Invalid ID: $id"]);
    }

    // Lấy dữ liệu cũ từ DB
    $oldData = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$selected_table` WHERE id = %d", $id), ARRAY_A);
    if (!$oldData) {
        wp_send_json_error(["message" => "Old record not found"]);
    }

    $data = $_POST;
    unset($data['action'], $data['table'], $data['id'], $data['oper']); // loại bỏ trường không cần

    // Lấy danh sách cột trong bảng
    $columns = $wpdb->get_col("SHOW COLUMNS FROM `$selected_table`");

    // Loại bỏ những trường không nằm trong bảng
    foreach ($data as $key => $value) {
        if (!in_array($key, $columns)) {
            unset($data[$key]);
        }
    }

    // So sánh dữ liệu mới với dữ liệu cũ, trim để tránh khác biệt do khoảng trắng
    $exclude_from_history = ['sohong_image_id', 'bosung_image_id'];

    $changedFields = [];
    foreach ($data as $key => $value) {
        $oldValue = isset($oldData[$key]) ? $oldData[$key] : null;
        if (trim((string)$value) !== trim((string)$oldValue) && !in_array($key, $exclude_from_history)) {
            $changedFields[] = $key;
        }
    }

    // Nếu không có trường thay đổi thì trả về lỗi


    // Thêm userupdate và dateupdate
    $current_user = wp_get_current_user();
    $data['userupdate'] = $current_user->user_login;
    $data['dateupdate'] = current_time('mysql');

    // Thực hiện cập nhật
    $result = $wpdb->update($selected_table, $data, ["id" => $id]);

    if ($result === false) {
        error_log("SQL ERROR: " . $wpdb->last_error);
        wp_send_json_error(["message" => "DB Error: " . $wpdb->last_error]);
    }

    // Chỉ lưu những trường thay đổi vào lịch sử
    $changedData = [];
    foreach ($changedFields as $field) {
        $changedData[$field] = $data[$field];
    }

    $changedData['userupdate'] = $data['userupdate'];
    $changedData['dateupdate'] = $data['dateupdate'];

    $history_data = [
        'record_id' => $id,
        'changed_fields' => implode(',', $changedFields),
        'data' => maybe_serialize($changedData),
        'userupdate' => $data['userupdate'],
        'dateupdate' => $data['dateupdate'],
    ];

    $wpdb->insert('wp_dulieunhadat_history', $history_data);

    error_log("SQL QUERY SUCCESS: " . $wpdb->last_query);
    wp_send_json_success(["message" => "Record updated and history saved successfully"]);
}*/

/*function edit_jqgrid_data() {
    global $wpdb;
    error_log("Received POST data: " . print_r($_POST, true));

    if (!isset($_POST['table']) || empty($_POST['table'])) {
        wp_send_json_error(["message" => "No table specified"]);
    }

    $selected_table = esc_sql($_POST['table']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        wp_send_json_error(["message" => "Invalid ID: $id"]);
    }

    // Lấy dữ liệu cũ từ DB
    $oldData = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$selected_table` WHERE id = %d", $id), ARRAY_A);
    if (!$oldData) {
        wp_send_json_error(["message" => "Old record not found"]);
    }

    $data = $_POST;
    unset($data['action'], $data['table'], $data['id'], $data['oper']);

    // Gộp dữ liệu liên hệ lại thành JSON
    $contact_names  = isset($_POST['name_more']) ? $_POST['name_more'] : [];
    $contact_phones = isset($_POST['dienthoaididong_more']) ? $_POST['dienthoaididong_more'] : [];
    $contact_roles  = isset($_POST['vaitro_more']) ? $_POST['vaitro_more'] : [];
    $contact_genders = isset($_POST['gioitinh_more']) ? $_POST['gioitinh_more'] : [];

    $contact_info = [];

    $maxCount = max(count($contact_names), count($contact_phones), count($contact_roles), count($contact_genders));
    for ($i = 0; $i < $maxCount; $i++) {
        $contact_info[] = [
            'name_more'  => $contact_names[$i] ?? '',
            'phone_more' => $contact_phones[$i] ?? '',
            'vaitro_more' => $contact_roles[$i] ?? '',
            'gender_more' => $contact_genders[$i] ?? '',
        ];
    }

    $data['contact_info'] = json_encode($contact_info, JSON_UNESCAPED_UNICODE);

    // Lấy danh sách cột trong bảng
    $columns = $wpdb->get_col("SHOW COLUMNS FROM `$selected_table`");

    // Loại bỏ những trường không nằm trong bảng
    foreach ($data as $key => $value) {
        if (!in_array($key, $columns)) {
            unset($data[$key]);
        }
    }

    // So sánh dữ liệu mới với dữ liệu cũ
    $exclude_from_history = ['sohong_image_id', 'bosung_image_id'];

    $changedFields = [];
    foreach ($data as $key => $value) {
        $oldValue = isset($oldData[$key]) ? $oldData[$key] : null;
        if (trim((string)$value) !== trim((string)$oldValue) && !in_array($key, $exclude_from_history)) {
            $changedFields[] = $key;
        }
    }

    // Thêm thông tin cập nhật
    $current_user = wp_get_current_user();
    //$data['userupdate'] = $current_user->user_login;
    $data['userupdate'] = $current_user->ID;
    $data['dateupdate'] = current_time('mysql');

    if ($selected_table == 'wp_dulieunhadat') {

        // Sau khi update, lấy lại full row từ DB
        $rowPrimary = $wpdb->get_row(
            $wpdb->prepare("SELECT name, dienthoaididong, vaitro, gioitinh, tinhtranggiaodich 
                    FROM {$selected_table} 
                    WHERE id = %d", $id),
            ARRAY_A
        );
        // contact_all
        $contactAll = [
            'tinhtranggiaodich' => $_POST['tinhtranggiaodich'] ?? '0',
            'contacts_primary' => [
                'name'            => $rowPrimary['name'] ?? '',
                'dienthoaididong' => $rowPrimary['dienthoaididong'] ?? '',
                'vaitro'          => $rowPrimary['vaitro'] ?? '',
                'gioitinh'        => $rowPrimary['gioitinh'] ?? '',
            ],
            'contacts_info' => $contact_info
        ];

        $data["contact_all"] = json_encode($contactAll, JSON_UNESCAPED_UNICODE);

        $contactAllHistory = [
            'tinhtranggiaodich' => $rowPrimary['tinhtranggiaodich'] ?? '0',
            'contacts_primary' => [
                'name'            => $rowPrimary['name'] ?? '',
                'dienthoaididong' => $rowPrimary['dienthoaididong'] ?? '',
                'vaitro'          => $rowPrimary['vaitro'] ?? '',
                'gioitinh'        => $rowPrimary['gioitinh'] ?? '',
            ],
            'contacts_info' => $contact_info
        ];
    }

    // Thực hiện cập nhật
    $result = $wpdb->update($selected_table, $data, ["id" => $id]);

    if ($result === false) {
        error_log("SQL ERROR: " . $wpdb->last_error);
        wp_send_json_error(["message" => "DB Error: " . $wpdb->last_error]);
    }

    // Lưu lịch sử nếu có thay đổi
    $changedData = [];
    foreach ($changedFields as $field) {
        $changedData[$field] = $data[$field];
    }

    $avatar_meta_his = get_user_meta($current_user->ID, 'simple_local_avatar', true);

    if (!empty($avatar_meta_his) && !empty($avatar_meta_his['full'])) {
        $user_avatar_url = $avatar_meta_his['full'];
    } else {
        $user_avatar_url = get_avatar_url($current_user->ID, ['size' => 96]);
    }

    $changedData['userupdate'] = $data['userupdate'];
    $changedData['user_login'] = $current_user->user_login;
    $changedData['first_name'] = $current_user->first_name;
    $changedData['last_name'] = $current_user->last_name;
    $changedData['phone'] = get_user_meta($current_user->ID, 'dien_thoai', true); // Số điện thoại lưu trong user meta
    $changedData['user_avatar_url'] = $user_avatar_url;
    $changedData['dateupdate'] = $data['dateupdate'];




    $history_data = [
        'record_id' => $id,
        'changed_fields' => implode(',', $changedFields),
        'data' => maybe_serialize($changedData),
        'userupdate' => $data['userupdate'],
        'dateupdate' => $data['dateupdate'],
        'contact_all' => $contactAllHistory
    ];

    $wpdb->insert('wp_dulieunhadat_history', $history_data);

    error_log("SQL QUERY SUCCESS: " . $wpdb->last_query);
    wp_send_json_success(["message" => "Record updated and history saved successfully"]);
}*/

function edit_jqgrid_data() {
    global $wpdb;
    error_log("Received POST data: " . print_r($_POST, true));

    if (!isset($_POST['table']) || empty($_POST['table'])) {
        wp_send_json_error(["message" => "No table specified"]);
    }

    $selected_table = esc_sql($_POST['table']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        wp_send_json_error(["message" => "Invalid ID: $id"]);
    }

    // Lấy dữ liệu cũ từ DB để phục vụ lưu history
    $oldData = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM `$selected_table` WHERE id = %d", $id),
        ARRAY_A
    );
    if (!$oldData) {
        wp_send_json_error(["message" => "Old record not found"]);
    }

    $data = $_POST;
    unset($data['action'], $data['table'], $data['id'], $data['oper']);

    // Gộp dữ liệu liên hệ lại thành JSON
    $contact_names   = isset($_POST['name_more']) ? $_POST['name_more'] : [];
    $contact_phones  = isset($_POST['dienthoaididong_more']) ? $_POST['dienthoaididong_more'] : [];
    $contact_roles   = isset($_POST['vaitro_more']) ? $_POST['vaitro_more'] : [];
    $contact_genders = isset($_POST['gioitinh_more']) ? $_POST['gioitinh_more'] : [];

    $contact_info = [];
    $maxCount = max(count($contact_names), count($contact_phones), count($contact_roles), count($contact_genders));
    for ($i = 0; $i < $maxCount; $i++) {
        $contact_info[] = [
            'name_more'   => $contact_names[$i]   ?? '',
            'phone_more'  => $contact_phones[$i]  ?? '',
            'vaitro_more' => $contact_roles[$i]   ?? '',
            'gender_more' => $contact_genders[$i] ?? '',
        ];
    }

    $data['contact_info'] = json_encode($contact_info, JSON_UNESCAPED_UNICODE);

    // Lấy danh sách cột trong bảng
    $columns = $wpdb->get_col("SHOW COLUMNS FROM `$selected_table`");

    // Loại bỏ những trường không nằm trong bảng
    foreach ($data as $key => $value) {
        if (!in_array($key, $columns)) {
            unset($data[$key]);
        }
    }

    // So sánh dữ liệu mới với dữ liệu cũ
    $exclude_from_history = ['sohong_image_id', 'bosung_image_id'];
    $changedFields = [];
    foreach ($data as $key => $value) {
        $oldValue = isset($oldData[$key]) ? $oldData[$key] : null;
        if (trim((string)$value) !== trim((string)$oldValue) && !in_array($key, $exclude_from_history)) {
            $changedFields[] = $key;
        }
    }

    // Thêm thông tin cập nhật
    $current_user = wp_get_current_user();
    $data['userupdate'] = $current_user->ID;
    $data['dateupdate'] = current_time('mysql');

    if ($selected_table == 'wp_dulieunhadat') {

        // contact_all cho bảng chính (dùng giá trị mới)
        $contactAll = [
            'tinhtranggiaodich' => $_POST['tinhtranggiaodich'] ?? '0', // giá trị mới
            'contacts_primary' => [
                'name'            => $_POST['name'] ?? '',
                'dienthoaididong' => $_POST['dienthoaididong'] ?? '',
                'vaitro'          => $_POST['vaitro'] ?? '',
                'gioitinh'        => $_POST['gioitinh'] ?? '',
            ],
            'contacts_info' => $contact_info
        ];
        $data["contact_all"] = json_encode($contactAll, JSON_UNESCAPED_UNICODE);

        // contact_all cho history (dùng giá trị cũ trong $oldData)

        $contactAllHistory = [
            'tinhtranggiaodich' => $oldData['tinhtranggiaodich'] ?? '0', // giá trị cũ
            'contacts_primary'  => [
                'name'            => $oldData['name'] ?? '',
                'dienthoaididong' => $oldData['dienthoaididong'] ?? '',
                'vaitro'          => $oldData['vaitro'] ?? '',
                'gioitinh'        => $oldData['gioitinh'] ?? '',
            ],
        ];

// Nếu là admin thì mới lưu contacts_info
        if ( current_user_can('administrator') ) {
            $contactAllHistory['contacts_info'] = $contact_info;
        }

    }

    // Thực hiện cập nhật bảng chính
    $result = $wpdb->update($selected_table, $data, ["id" => $id]);

    if ($result === false) {
        error_log("SQL ERROR: " . $wpdb->last_error);
        wp_send_json_error(["message" => "DB Error: " . $wpdb->last_error]);
    }

    // Chuẩn bị dữ liệu thay đổi để lưu history
    $changedData = [];
    foreach ($changedFields as $field) {
        $changedData[$field] = $data[$field];
    }

    // Lưu thêm giá trị CŨ/MỚI của từng trường thay đổi để hiển thị chi tiết sau này
    $fieldChanges = [];
    foreach ($changedFields as $field) {
        $fieldChanges[$field] = [
            'old' => $oldData[$field] ?? '',
            'new' => $data[$field] ?? '',
        ];
    }
    $changedData['field_changes'] = $fieldChanges;

    $avatar_meta_his = get_user_meta($current_user->ID, 'simple_local_avatar', true);
    $user_avatar_url = !empty($avatar_meta_his['full'])
        ? $avatar_meta_his['full']
        : get_avatar_url($current_user->ID, ['size' => 96]);

    $changedData['userupdate'] = $data['userupdate'];
    $changedData['user_login'] = $current_user->user_login;
    $changedData['first_name'] = $current_user->first_name;
    $changedData['last_name'] = $current_user->last_name;
    $changedData['phone'] = get_user_meta($current_user->ID, 'dien_thoai', true);
    $changedData['user_avatar_url'] = $user_avatar_url;
    $changedData['dateupdate'] = $data['dateupdate'];

    // Lưu history
    $history_data = [
        'record_id'      => $id,
        'changed_fields' => implode(',', $changedFields),
        'data'           => maybe_serialize($changedData),
        'userupdate'     => $data['userupdate'],
        'dateupdate'     => $data['dateupdate'],
        'contact_all'    => json_encode($contactAllHistory, JSON_UNESCAPED_UNICODE)
    ];


    $wpdb->insert('wp_dulieunhadat_history', $history_data);

    error_log("SQL QUERY SUCCESS: " . $wpdb->last_query);
    wp_send_json_success(["message" => "Record updated and history saved successfully"]);
}


add_action("wp_ajax_edit_jqgrid_data", "edit_jqgrid_data");

// function delete data
add_action('wp_ajax_delete_jqgrid_data', 'delete_jqgrid_data_callback');
function delete_jqgrid_data_callback() {
    if ( !isset($_POST['table']) || !isset($_POST['id']) ) {
        wp_send_json_error("Thiếu thông tin bảng hoặc ID");
        return;
    }

    global $wpdb;

    $raw_table = sanitize_text_field($_POST['table']);
    $id = intval($_POST['id']);
    $force_delete = isset($_POST['force_delete']) ? intval($_POST['force_delete']) : 0;

    // Đảm bảo bảng có prefix đúng
    $table = strpos($raw_table, $wpdb->prefix) === 0 ? $raw_table : $wpdb->prefix . $raw_table;

    if (!$force_delete) {
        // ✅ Mặc định: cập nhật cột deleted = 1
        $result = $wpdb->update($table, ['deleted' => 1], ['id' => $id]);
        if ($result !== false) {
            wp_send_json_success("Đã chuyển vào thùng rác.");
        } else {
            wp_send_json_error("Không thể cập nhật bản ghi.");
        }
    } else {
        // ✅ Nếu force_delete = 1: xoá vĩnh viễn
        $result = $wpdb->delete($table, ['id' => $id]);
        if ($result !== false) {
            wp_send_json_success("Đã xoá vĩnh viễn.");
        } else {
            wp_send_json_error("Xoá thất bại.");
        }
    }
}







// ajax wp_province, wp_district, wp_wards
// Hook for AJAX request
add_action('wp_ajax_load_wp_districts', 'load_wp_districts_callback');
add_action('wp_ajax_nopriv_load_wp_districts', 'load_wp_districts_callback');

function load_wp_districts_callback() {
    // Check for the wp_province_id passed from the AJAX request
    if (isset($_POST['wp_province_id'])) {
        global $wpdb;

        // Sanitize the input
        $wp_province_id = intval($_POST['wp_province_id']);

        // Query to get the wp_districts related to the selected wp_province
        $sql = "
            SELECT id, `name` 
            FROM wp_district  -- Replace with your actual wp_districts table
            WHERE wp_province_id = %d
        ";

        // Prepare and execute the query
        $wp_districts = $wpdb->get_results($wpdb->prepare($sql, $wp_province_id));

        // Prepare the response
        if ($wp_districts) {
            $data = [];
            foreach ($wp_districts as $wp_district) {
                $data[] = [
                    'id' => $wp_district->id,
                    'name' => $wp_district->name
                ];
            }

            // Send the response back to the AJAX call
            wp_send_json_success(['data' => $data]);
        } else {
            wp_send_json_success(['data' => []]);
        }
    } else {
        wp_send_json_error(['message' => 'wp_province ID not provided']);
    }

    // Always die in AJAX functions to terminate correctly
    wp_die();
}


add_action('wp_ajax_load_wp_wards', 'load_wp_wards_callback');
add_action('wp_ajax_nopriv_load_wp_wards', 'load_wp_wards_callback');

function load_wp_wards_callback() {
    // Check for the wp_province_id passed from the AJAX request
    if (isset($_POST['wp_district_id'])) {
        global $wpdb;

        // Sanitize the input
        $wp_district_id = intval($_POST['wp_district_id']);

        // Query to get the wp_wards related to the selected wp_district
        $sql = "
            SELECT id, `name` 
            FROM wp_wards  -- Replace with your actual wp_wards table
            WHERE wp_district_id = %d
        ";

        // Prepare and execute the query
        $wp_wards = $wpdb->get_results($wpdb->prepare($sql, $wp_district_id));

        // Prepare the response
        if ($wp_wards) {
            $data = [];
            foreach ($wp_wards as $ward) {
                $data[] = [
                    'id' => $ward->id,
                    'name' => $ward->name
                ];
            }

            // Send the response back to the AJAX call
            wp_send_json_success(['data' => $data]);
        } else {
            wp_send_json_success(['data' => []]);
        }
    } else {
        wp_send_json_error(['message' => 'wp_province ID not provided']);
    }

    // Always die in AJAX functions to terminate correctly
    wp_die();
}






// relate USER
// Get wp_provinces from DB
function uh_get_wp_province_options() {
    global $wpdb;
    $results = $wpdb->get_results("SELECT id, name FROM wp_province ORDER BY name ASC");
    $options = [];
    foreach ($results as $row) {
        $options[$row->id] = $row->name;
    }
    return $options;
}

// Get relationships from DB
function uh_get_relationship_options() {
    global $wpdb;
    $results = $wpdb->get_results("SELECT id, name FROM wp_relationship ORDER BY name ASC");
    $options = [];
    foreach ($results as $row) {
        $options[$row->id] = $row->name;
    }
    return $options;
}



function uh_render_text_input($label, $name, $value = '') {
    echo "<tr><th><label for='{$name}'>{$label}</label></th><td><input type='text' name='{$name}' value='" . esc_attr($value) . "' class='regular-text' /></td></tr>";
}

function uh_render_date_input($label, $name, $value = '') {
    echo "<tr><th><label for='{$name}'>{$label}</label></th><td><input type='date' name='{$name}' value='" . esc_attr($value) . "' /></td></tr>";
}

function uh_render_select($label, $name, $options, $selected_value = '') {
    echo "<tr><th><label for='{$name}'>{$label}</label></th><td><select name='{$name}'><option value=''>— Chọn —</option>";
    foreach ($options as $val => $text) {
        $selected = selected($selected_value, $val, false);
        echo "<option value='" . esc_attr($val) . "' {$selected}>" . esc_html($text) . "</option>";
    }
    echo "</select></td></tr>";
}

function uh_render_radio_group($label, $name, $options, $selected_value = '') {
    echo "<tr><th>{$label}</th><td>";
    foreach ($options as $val => $text) {
        $checked = checked($selected_value, $val, false);
        echo "<label><input type='radio' name='{$name}' value='{$val}' {$checked}> {$text}</label><br>";
    }
    echo "</td></tr>";
}

function uh_render_file_upload($label, $name, $current_url = '') {
    echo "<tr><th><label for='{$name}'>{$label}</label></th><td>";
    if (!empty($current_url)) {
        echo "<p><img src='" . esc_url($current_url) . "' style='max-width:200px;' /></p>";
    }
    echo "<input type='file' name='{$name}' accept='image/*' /></td></tr>";
}

function uh_get_business_managers() {
    return get_users([
        'role'    => 'quanlykinhdoanh',
        'orderby' => 'display_name',
        'order'   => 'ASC',
        'fields'  => ['ID', 'display_name']
    ]);
}


function uh_show_custom_user_fields($user) {
    $fields = [
        'ngay_vao_lam', 'nguon_tuyen', 'dien_thoai', 'hon_nhan',
        'sinh_nhat', 'dan_toc', 'ton_giao', 'noi_sinh', 'gioi_tinh',
        'cccd', 'ngay_cap', 'noi_cap',
        'dia_chi_thuong_tru', 'dia_chi_tam_tru', 'facebook', 'zalo',
        'so_tk_ngan_hang', 'chi_nhanh_ngan_hang',
        'ten_nguoi_phu_thuoc', 'dt_nguoi_phu_thuoc', 'quan_he', 'so_nguoi_phu_thuoc',
        'cccd_mat_truoc', 'cccd_mat_sau',
        'anh_so_ho_khau', 'anh_so_yeu_ly_lich', 'anh_bang_cap', 'giay_kham_suc_khoe', 'anh_khac'
    ];
    foreach ($fields as $key) {
        $$key = isset($user->ID) ? get_the_author_meta($key, $user->ID) : '';
    }

    echo '<table class="form-table">';

// ===== Thuộc quản lý kinh doanh =====
    $quan_ly_id = isset($user->ID) ? get_user_meta($user->ID, 'quan_ly_id', true) : '';
    $managers   = uh_get_business_managers();

    echo '<tr>';
    echo '<th><label for="quan_ly_id">Thuộc quản lý kinh doanh</label></th>';
    echo '<td>';
    echo '<select name="quan_ly_id" id="quan_ly_id">';
    echo '<option value="">— Chọn quản lý —</option>';

    foreach ($managers as $manager) {

        // Không cho chọn chính mình (optional)
        if (!empty($user->ID) && $manager->ID == $user->ID) continue;

        echo '<option value="' . esc_attr($manager->ID) . '" '
            . selected($quan_ly_id, $manager->ID, false) . '>'
            . esc_html($manager->display_name)
            . '</option>';
    }

    echo '</select>';
    echo '</td>';
    echo '</tr>';


    // ===== Trạng thái làm việc =====
    $trang_thai_lam_viec = isset($user->ID)
        ? get_user_meta($user->ID, 'trang_thai_lam_viec', true)
        : 'dang_lam';

    echo '<tr>';
    echo '<th><label for="trang_thai_lam_viec">Trạng thái làm việc</label></th>';
    echo '<td>';
    echo '<select name="trang_thai_lam_viec" id="trang_thai_lam_viec">';
    echo '<option value="dang_lam" ' . selected($trang_thai_lam_viec, 'dang_lam', false) . '>Còn làm</option>';
    echo '<option value="da_nghi" ' . selected($trang_thai_lam_viec, 'da_nghi', false) . '>Đã nghỉ</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';


// ===== Các field cũ =====
    uh_render_date_input('Ngày vào làm', 'ngay_vao_lam', $ngay_vao_lam);

    uh_render_radio_group('Nguồn tuyển', 'nguon_tuyen', [
        'tuyen_dung' => 'Tuyển dụng',
        'gioi_thieu' => 'Giới thiệu',
        'khong_xac_dinh' => 'Không xác định'
    ], $nguon_tuyen);
    uh_render_text_input('Điện thoại', 'dien_thoai', $dien_thoai);
    uh_render_select('Hôn nhân', 'hon_nhan', [
        'doc_than' => 'Độc thân',
        'ket_hon' => 'Kết hôn',
        'ly_hon' => 'Ly hôn'
    ], $hon_nhan);
    uh_render_date_input('Sinh nhật', 'sinh_nhat', $sinh_nhat);
    uh_render_select('Giới tính', 'gioi_tinh', ['nam' => 'Nam', 'nu' => 'Nữ'], $gioi_tinh);
    uh_render_text_input('Dân tộc', 'dan_toc', $dan_toc);
    uh_render_text_input('Tôn giáo', 'ton_giao', $ton_giao);

    echo '<tr><th><label for="noi_sinh">Nơi sinh</label></th><td><select name="noi_sinh"><option value="">— Chọn nơi sinh —</option>';
    foreach (uh_get_wp_province_options() as $id => $name) {
        echo '<option value="' . esc_attr($name) . '" ' . selected($noi_sinh, $name, false) . '>' . esc_html($name) . '</option>';
    }
    echo '</select></td></tr>';

    uh_render_text_input('CCCD', 'cccd', $cccd);
    uh_render_date_input('Ngày cấp', 'ngay_cap', $ngay_cap);
    uh_render_text_input('Nơi cấp', 'noi_cap', $noi_cap);
    uh_render_text_input('Địa chỉ thường trú', 'dia_chi_thuong_tru', $dia_chi_thuong_tru);
    uh_render_text_input('Địa chỉ tạm trú', 'dia_chi_tam_tru', $dia_chi_tam_tru);
    uh_render_text_input('Facebook', 'facebook', $facebook);
    uh_render_text_input('Zalo', 'zalo', $zalo);
    uh_render_text_input('Số TK Ngân Hàng', 'so_tk_ngan_hang', $so_tk_ngan_hang);
    uh_render_text_input('Chi nhánh Ngân Hàng', 'chi_nhanh_ngan_hang', $chi_nhanh_ngan_hang);
    uh_render_text_input('Tên người phụ thuộc', 'ten_nguoi_phu_thuoc', $ten_nguoi_phu_thuoc);
    uh_render_text_input('Điện thoại người phụ thuộc', 'dt_nguoi_phu_thuoc', $dt_nguoi_phu_thuoc);
    uh_render_select('Quan hệ', 'quan_he', uh_get_relationship_options(), $quan_he);
    uh_render_text_input('Số người phụ thuộc', 'so_nguoi_phu_thuoc', $so_nguoi_phu_thuoc);

    // Uploads
    foreach ([
                 'cccd_mat_truoc' => 'CCCD Mặt Trước',
                 'cccd_mat_sau' => 'CCCD Mặt Sau',
                 'anh_so_ho_khau' => 'Ảnh Sổ Hộ Khẩu',
                 'anh_so_yeu_ly_lich' => 'Ảnh Sơ Yếu Lý Lịch',
                 'anh_bang_cap' => 'Ảnh Bằng Cấp',
                 'giay_kham_suc_khoe' => 'Giấy Khám Sức Khỏe',
                 'anh_khac' => 'Ảnh Khác'
             ] as $field_key => $label) {
        uh_render_file_upload($label, $field_key, $$field_key);
    }

    echo '</table>';
}

add_action('show_user_profile', 'uh_show_custom_user_fields');
add_action('edit_user_profile', 'uh_show_custom_user_fields');

function uh_save_custom_user_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;

    $text_fields = [
        'ngay_vao_lam', 'nguon_tuyen', 'dien_thoai', 'hon_nhan',
        'sinh_nhat', 'dan_toc', 'ton_giao', 'noi_sinh', 'gioi_tinh',
        'cccd', 'ngay_cap', 'noi_cap',
        'dia_chi_thuong_tru', 'dia_chi_tam_tru', 'facebook', 'zalo',
        'so_tk_ngan_hang', 'chi_nhanh_ngan_hang',
        'ten_nguoi_phu_thuoc', 'dt_nguoi_phu_thuoc', 'quan_he', 'so_nguoi_phu_thuoc'
    ];

    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
    // ===== Lưu quản lý kinh doanh =====
    if (isset($_POST['quan_ly_id'])) {
        update_user_meta(
            $user_id,
            'quan_ly_id',
            intval($_POST['quan_ly_id'])
        );
    }

    // ===== Lưu trạng thái làm việc =====
    if (isset($_POST['trang_thai_lam_viec'])) {
        update_user_meta(
            $user_id,
            'trang_thai_lam_viec',
            sanitize_text_field($_POST['trang_thai_lam_viec'])
        );
    }



    $file_fields = [
        'cccd_mat_truoc', 'cccd_mat_sau',
        'anh_so_ho_khau', 'anh_so_yeu_ly_lich', 'anh_bang_cap', 'giay_kham_suc_khoe', 'anh_khac'
    ];
    foreach ($file_fields as $file_key) {
        if (!empty($_FILES[$file_key]['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $uploaded = wp_handle_upload($_FILES[$file_key], ['test_form' => false]);
            if (!isset($uploaded['error'])) {
                update_user_meta($user_id, $file_key, esc_url_raw($uploaded['url']));
            }
        }
    }
}

add_action('personal_options_update', 'uh_save_custom_user_fields');
add_action('edit_user_profile_update', 'uh_save_custom_user_fields');

function uh_show_custom_fields_on_new_user($operation) {
    uh_show_custom_user_fields((object)[]);
}

add_action('user_new_form', 'uh_show_custom_fields_on_new_user');

function uh_save_custom_fields_on_register($user_id) {
    uh_save_custom_user_fields($user_id);
}

add_action('user_register', 'uh_save_custom_fields_on_register');

add_action('user_edit_form_tag', 'uh_add_enctype_to_user_form');
add_action('show_user_profile', 'uh_add_enctype_to_user_form'); // For own profile

function uh_add_enctype_to_user_form() {
    echo ' enctype="multipart/form-data"';
}

// upload avatar
function uh_show_avatar_upload_field($user) {
    $avatar_url = get_user_meta($user->ID, 'custom_avatar', true);
    echo '<h3>Ảnh đại diện</h3><table class="form-table">';
    echo '<tr><th><label for="custom_avatar">Tải ảnh đại diện</label></th><td>';
    if ($avatar_url) {
        echo '<img src="' . esc_url($avatar_url) . '" style="max-width:100px;"><br>';
    }
    echo '<input type="file" name="custom_avatar" accept="image/*" /></td></tr></table>';
}
add_action('show_user_profile', 'uh_show_avatar_upload_field');
add_action('edit_user_profile', 'uh_show_avatar_upload_field');
function uh_save_avatar_upload($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;

    if (!empty($_FILES['custom_avatar']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $upload = wp_handle_upload($_FILES['custom_avatar'], ['test_form' => false]);
        if (!isset($upload['error'])) {
            update_user_meta($user_id, 'custom_avatar', esc_url_raw($upload['url']));
        }
    }
}
add_action('personal_options_update', 'uh_save_avatar_upload');
add_action('edit_user_profile_update', 'uh_save_avatar_upload');
function uh_custom_avatar($avatar, $id_or_email) {
    $user = false;

    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', $id_or_email);
    } elseif (is_object($id_or_email) && isset($id_or_email->user_id)) {
        $user = get_user_by('id', $id_or_email->user_id);
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
    }

    if ($user) {
        $custom_avatar = get_user_meta($user->ID, 'custom_avatar', true);
        if ($custom_avatar) {
            $avatar = "<img alt='' src='" . esc_url($custom_avatar) . "' class='avatar avatar-96 photo' height='96' width='96' />";
        }
    }

    return $avatar;
}
add_filter('get_avatar', 'uh_custom_avatar', 10, 5);

// hide gavatar
add_action('admin_head-user-edit.php', 'uh_hide_default_avatar');
add_action('admin_head-profile.php', 'uh_hide_default_avatar');

function uh_hide_default_avatar() {
    echo '<style>
        .user-profile-picture { display: none !important; }
    </style>';
}


//hoat dong user trong dashboard
// Lưu số lần đăng nhập mỗi khi người dùng đăng nhập
function track_user_login( $user_login, $user ) {
    $user_id = $user->ID;
    $last_login = get_user_meta( $user_id, 'last_login', true );
    $login_count = get_user_meta( $user_id, 'login_count', true );

    // Nếu chưa từng có login_count, khởi tạo là 0
    if ( empty($login_count) || !is_numeric($login_count) ) {
        $login_count = 0;
    } else {

        $login_count = (int) $login_count;
    }

    // Lấy thời gian hiện tại và xác định tuần hiện tại
    $current_time = current_time( 'timestamp' );
    $week_start = strtotime( 'last sunday midnight', $current_time );

    // Nếu lần đăng nhập trước không thuộc tuần này => reset login_count
    if ( !empty($last_login) && $last_login < $week_start ) {
        $login_count = 1;
    } else {
        $login_count++;
    }

    // Lưu số lần đăng nhập và thời gian đăng nhập
    update_user_meta( $user_id, 'login_count', $login_count );
    update_user_meta( $user_id, 'last_login', $current_time );
}
add_action( 'wp_login', 'track_user_login', 10, 2 );



// Tạo biểu đồ trên dashboard
function render_user_login_activity_chart() {
    global $wpdb;

    // Lấy dữ liệu người dùng đăng nhập trong tuần
    $results = $wpdb->get_results( "
        SELECT user_id, meta_value AS login_count
        FROM {$wpdb->prefix}usermeta
        WHERE meta_key = 'login_count'
    " );

    $user_data = [];
    foreach ( $results as $result ) {
        $user_info = get_userdata( $result->user_id );
        $user_data[] = [
            'label' => $user_info->user_login,
            'data' => $result->login_count,
        ];
    }

    // Nếu không có dữ liệu, không vẽ biểu đồ
    if ( empty( $user_data ) ) {
        echo '<p>Không tìm thấy người dùng nào đăng nhập trong tuần.</p>';
        return;
    }

    // Hiển thị biểu đồ với kiểu giao diện đẹp hơn
    ?>
    <div id="user_login_activity_chart" style="background: #f9f9f9; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

        <canvas id="user-login-chart" width="100%"></canvas>
    </div>

    <style>
        /* CSS nâng cao cho widget */
        #user_login_activity_chart {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        #user-login-chart {
            margin-top: 20px;
        }

        .wrap h2 {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }
    </style>

    <!--<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('user-login-chart').getContext('2d');

            var gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(75,192,192,0.6)');
            gradient.addColorStop(1, 'rgba(75,192,192,0)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode( array_column( $user_data, 'label' ) ); ?>,
                    datasets: [{
                        label: 'Số lần đăng nhập',
                        data: <?php echo json_encode( array_column( $user_data, 'data' ) ); ?>,
                        backgroundColor: gradient,
                        borderColor: 'rgba(75,192,192,1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: 'rgba(75,192,192,0.8)',
                        hoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: { color: '#333', font: { size: 14 } }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#333',
                            bodyColor: '#000',
                            borderColor: '#ccc',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#333' },
                            grid: { color: '#eee' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#333' },
                            grid: { color: '#eee' }
                        }
                    }
                }
            });
        });
    </script>
    <?php
}

// Đăng widget trên dashboard
function add_user_login_activity_chart_to_dashboard() {
    wp_add_dashboard_widget(
        'user_login_activity_chart',
        'Biểu Đồ Hoạt Động Đăng Nhập Của Nhân Viên Trong Tuần',
        'render_user_login_activity_chart'
    );
}
add_action( 'wp_dashboard_setup', 'add_user_login_activity_chart_to_dashboard' );


// chart thong ke nhan vien nhap hang
function render_user_data_entry_chart() {
    global $wpdb;

    // Truy vấn: Join wp_users và wp_dulieunhadat, đếm số lượng hàng theo user
    $results = $wpdb->get_results("
        SELECT u.ID, u.display_name, COUNT(d.id) AS total
        FROM {$wpdb->prefix}users u
        INNER JOIN {$wpdb->prefix}dulieunhadat d ON u.ID = d.user
        GROUP BY u.ID, u.display_name
    ");

    $user_data = [];
    foreach ( $results as $row ) {
        $user_data[] = [
            'label' => $row->display_name,
            'data'  => (int) $row->total,
        ];
    }

    if ( empty( $user_data ) ) {
        echo '<p>Không có dữ liệu hàng nhập để hiển thị.</p>';
        return;
    }
    ?>

    <form method="get" action="<?php echo admin_url('admin-post.php'); ?>"
          style="margin-bottom:12px;display:flex;gap:8px;align-items:center;">
        <input type="hidden" name="action" value="export_dashboard_stats">
        <input type="hidden" name="type" value="hangnhap">
        <select name="month">
            <?php $currentMonth = date('n'); for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php selected($m, $currentMonth); ?>>Tháng <?php echo $m; ?></option>
            <?php endfor; ?>
        </select>
        <select name="year">
            <?php $currentYear = date('Y'); for ($y = $currentYear; $y >= $currentYear - 3; $y--): ?>
                <option value="<?php echo $y; ?>" <?php selected($y, $currentYear); ?>><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>
        <button class="button button-primary">⬇ Xuất CSV</button>
    </form>

    <div id="user_data_entry_chart" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <canvas id="user-entry-chart" width="100%"></canvas>
    </div>

    <style>
        #user_data_entry_chart {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }
        #user-entry-chart {
            margin-top: 20px;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('user-entry-chart').getContext('2d');

            var gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(54, 162, 235, 0.6)');
            gradient.addColorStop(1, 'rgba(54, 162, 235, 0)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode( array_column( $user_data, 'label' ) ); ?>,
                    datasets: [{
                        label: 'Số lượng hàng nhập',
                        data: <?php echo json_encode( array_column( $user_data, 'data' ) ); ?>,
                        backgroundColor: gradient,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: 'rgba(54, 162, 235, 0.8)',
                        hoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: { color: '#333', font: { size: 14 } }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#333',
                            bodyColor: '#000',
                            borderColor: '#ccc',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#333' },
                            grid: { color: '#eee' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#333' },
                            grid: { color: '#eee' }
                        }
                    }
                }
            });
        });
    </script>
    <?php
}
function add_user_data_entry_chart_to_dashboard() {
    wp_add_dashboard_widget(
        'user_data_entry_chart',
        'Biểu Đồ Thống Kê Hàng Nhập Theo Nhân Viên',
        'render_user_data_entry_chart'
    );
}
add_action( 'wp_dashboard_setup', 'add_user_data_entry_chart_to_dashboard' );


// chart thống kê nhân viên nhập hàng TRONG TUẦN - tự động tính lại từ đầu mỗi khi sang tuần mới
function render_user_data_entry_chart_weekly() {
    global $wpdb;

    // YEARWEEK(created_at, 1) = YEARWEEK(NOW(), 1) => chỉ lấy dữ liệu của tuần hiện tại (tuần bắt đầu từ Thứ Hai)
    // Hết tuần, YEARWEEK(NOW(),1) tự đổi giá trị nên số liệu tự "tính lại từ đầu" mà không cần cron/reset thủ công.
    $results = $wpdb->get_results("
        SELECT u.ID, u.display_name, COUNT(d.id) AS total
        FROM {$wpdb->prefix}users u
        INNER JOIN {$wpdb->prefix}dulieunhadat d ON u.ID = d.user
        WHERE YEARWEEK(d.datecreate, 1) = YEARWEEK(NOW(), 1)
        GROUP BY u.ID, u.display_name
        ORDER BY total DESC
    ");

    $user_data = [];
    foreach ( $results as $row ) {
        $user_data[] = [
            'label' => $row->display_name,
            'data'  => (int) $row->total,
        ];
    }

    // Xác định khoảng ngày của tuần hiện tại (Thứ Hai -> Chủ Nhật) để hiển thị cho rõ
    $week_start = date('d/m/Y', strtotime('monday this week'));
    $week_end   = date('d/m/Y', strtotime('sunday this week'));

    ?>
    <p style="margin:0 0 10px;color:#666;">Tuần này: <strong><?php echo esc_html($week_start . ' - ' . $week_end); ?></strong> (tự động tính lại khi sang tuần mới)</p>
    <?php

    if ( empty( $user_data ) ) {
        echo '<p>Chưa có dữ liệu hàng nhập trong tuần này.</p>';
        return;
    }
    ?>

    <div id="user_data_entry_chart_weekly" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <canvas id="user-entry-chart-weekly" width="100%"></canvas>
    </div>

    <style>
        #user_data_entry_chart_weekly {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }
        #user-entry-chart-weekly {
            margin-top: 20px;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('user-entry-chart-weekly').getContext('2d');

            var gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(46, 204, 113, 0.6)');
            gradient.addColorStop(1, 'rgba(46, 204, 113, 0)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode( array_column( $user_data, 'label' ) ); ?>,
                    datasets: [{
                        label: 'Số lượng hàng nhập (tuần này)',
                        data: <?php echo json_encode( array_column( $user_data, 'data' ) ); ?>,
                        backgroundColor: gradient,
                        borderColor: 'rgba(46, 204, 113, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: 'rgba(46, 204, 113, 0.8)',
                        hoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: { color: '#333', font: { size: 14 } }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#333',
                            bodyColor: '#000',
                            borderColor: '#ccc',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#333' },
                            grid: { color: '#eee' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#333', precision: 0 },
                            grid: { color: '#eee' }
                        }
                    }
                }
            });
        });
    </script>
    <?php
}
function add_user_data_entry_chart_weekly_to_dashboard() {
    wp_add_dashboard_widget(
        'user_data_entry_chart_weekly',
        'Biểu Đồ Thống Kê Hàng Nhập Theo Nhân Viên (Trong Tuần)',
        'render_user_data_entry_chart_weekly'
    );
}
add_action( 'wp_dashboard_setup', 'add_user_data_entry_chart_weekly_to_dashboard' );


// chart thống kê

function render_user_view_property_chart() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'luot_xem_sdt';
    $can_view_phone_stat = current_user_can('administrator');

    echo '<style>
        #user_view_property_chart {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }
    </style>';

    $raw_results = $wpdb->get_results("
        SELECT 
            nguoidung_id, 
            nhadat_id,
            COUNT(*) AS tong_xem,
            SUM(CASE WHEN phone_status IS NOT NULL THEN 1 ELSE 0 END) AS da_xem_so
        FROM {$table_name}
        WHERE YEARWEEK(thoi_gian, 1) = YEARWEEK(NOW(), 1)
        GROUP BY nguoidung_id, nhadat_id
    ");

    // Chi tiết từng lượt cập nhật tình trạng số điện thoại (cho tooltip)
    $status_labels = [
        1 => 'Đúng Thông Tin',
        2 => 'Sai Thông Tin',
        3 => 'Không Liên Lạc Được',
        4 => 'Số Môi Giới',
    ];

    $phone_detail_raw = $wpdb->get_results("
        SELECT l.nguoidung_id, l.nhadat_id, l.thoi_gian, l.phone_status, l.note, u.user_login
        FROM {$table_name} l
        LEFT JOIN {$wpdb->users} u ON u.ID = l.nguoidung_id
        WHERE l.phone_status IS NOT NULL
          AND YEARWEEK(l.thoi_gian, 1) = YEARWEEK(NOW(), 1)
        ORDER BY l.thoi_gian DESC
    ");

    $phone_update_detail = [];
    foreach ($phone_detail_raw as $row) {
        $uid = $row->nguoidung_id;
        $status_text = $status_labels[(int) $row->phone_status] ?? 'Không xác định';
        $account = $row->user_login ?: "NV#{$uid}";
        $time_text = date('d/m H:i', strtotime($row->thoi_gian));
        $line = "{$time_text} - {$account} - NĐ#{$row->nhadat_id}: {$status_text}";
        $note_text = trim((string) $row->note);
        if ($note_text !== '') {
            $line .= " ({$note_text})";
        }
        $phone_update_detail[$uid][] = $line;
    }

    // Bổ xung thông tin nhà: lấy từ lịch sử chỉnh sửa dữ liệu nhà đất (wp_dulieunhadat_history)
    $history_table = $wpdb->prefix . 'dulieunhadat_history';

    $dulieunha_raw = $wpdb->get_results("
        SELECT record_id, changed_fields, userupdate, dateupdate, data
        FROM {$history_table}
        WHERE YEARWEEK(dateupdate, 1) = YEARWEEK(NOW(), 1)
        ORDER BY dateupdate DESC
    ");

    // Tên trường hiển thị thân thiện (fallback: tự format từ tên cột)
    $field_labels = [
        'tieu_de'            => 'Tiêu đề',
        'tom_tat'            => 'Mô tả',
        'gia'                => 'Giá',
        'dien_tich'          => 'Diện tích',
        'diachi'             => 'Địa chỉ',
        'dia_chi'            => 'Địa chỉ',
        'tinhtranggiaodich'  => 'Tình trạng giao dịch',
        'name'               => 'Tên liên hệ',
        'dienthoaididong'    => 'Số điện thoại',
        'vaitro'             => 'Vai trò',
        'gioitinh'           => 'Giới tính',
    ];

    // Format giá trị hiển thị: số thì thêm dấu chấm ngăn cách, chuỗi dài thì cắt bớt
    $format_value = function ($val) {
        $val = trim((string) $val);
        if ($val === '') return '(trống)';
        if (is_numeric($val)) return number_format((float) $val, 0, ',', '.');
        return mb_strlen($val) > 40 ? mb_substr($val, 0, 40) . '...' : $val;
    };

    $note_count_by_user = [];
    $note_ids_by_user = [];
    $note_detail_by_user = [];

    foreach ($dulieunha_raw as $row) {
        $uid = (int) $row->userupdate;
        $note_count_by_user[$uid] = ($note_count_by_user[$uid] ?? 0) + 1;
        $note_ids_by_user[$uid][] = $row->record_id;

        $time_text = date('d/m H:i', strtotime($row->dateupdate));

        $unserialized = !empty($row->data) ? maybe_unserialize($row->data) : [];
        $field_changes = (is_array($unserialized) && !empty($unserialized['field_changes']))
            ? $unserialized['field_changes']
            : [];

        if (!empty($field_changes)) {
            $change_lines = [];
            foreach ($field_changes as $field => $vals) {
                // Bỏ qua các field lưu dữ liệu JSON thô, không cần hiện trong tooltip
                if (in_array($field, ['contact_all', 'contact_info'], true)) {
                    continue;
                }
                $label = $field_labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                $old_val = $format_value($vals['old'] ?? '');
                $new_val = $format_value($vals['new'] ?? '');
                $change_lines[] = "{$label}: {$old_val} → {$new_val}";
            }
            if (empty($change_lines)) {
                $change_lines[] = 'Cập nhật thông tin liên hệ';
            }
            $note_detail_by_user[$uid][] = "{$time_text} - NĐ#{$row->record_id}:\n   " . implode("\n   ", $change_lines);
        } else {
            // Dữ liệu lịch sử cũ (trước khi có field_changes) chỉ có tên trường, không có giá trị cũ/mới
            $fields = trim((string) $row->changed_fields) !== ''
                ? str_replace(',', ', ', $row->changed_fields)
                : 'không rõ trường';
            $note_detail_by_user[$uid][] = "{$time_text} - NĐ#{$row->record_id}: sửa {$fields}";
        }
    }

    // Gộp danh sách nhân viên có sửa dữ liệu nhà (nhưng chưa có trong $user_data) vào chung
    $user_data = [];
    foreach ($raw_results as $row) {
        $user_id = $row->nguoidung_id;
        $nhadat_id = $row->nhadat_id;
        $tong_xem = (int) $row->tong_xem;
        $phone_count = (int) $row->da_xem_so;

        if (!isset($user_data[$user_id])) {
            $user_data[$user_id] = [
                'view' => 0,
                'phone' => 0,
                'nhadat_ids' => [
                    'view' => [],
                    'phone' => [],
                ],
            ];
        }

        $user_data[$user_id]['view'] += $tong_xem;
        $user_data[$user_id]['phone'] += $phone_count;

        $view_thuong = $tong_xem - $phone_count;
        if ($view_thuong > 0) $user_data[$user_id]['nhadat_ids']['view'][] = $nhadat_id;
        if ($phone_count > 0) $user_data[$user_id]['nhadat_ids']['phone'][] = $nhadat_id;
    }

    foreach ($note_count_by_user as $uid => $cnt) {
        if (!isset($user_data[$uid])) {
            $user_data[$uid] = [
                'view' => 0,
                'phone' => 0,
                'nhadat_ids' => [
                    'view' => [],
                    'phone' => [],
                ],
            ];
        }
        $user_data[$uid]['note'] = $cnt;
    }

    if (empty($user_data)) {
        echo '<p>Không có dữ liệu để hiển thị.</p>';
        return;
    }

    $labels = [];
    $views = [];
    $phones = [];
    $notes = [];
    $view_percentages = [];
    $phone_percentages = [];
    $note_percentages = [];
    $products_view = [];
    $products_phone = [];
    $products_phone_detail = [];
    $products_note_detail = [];

    foreach ($user_data as $user_id => $data) {
        $user_info = get_userdata($user_id);
        if ($user_info) {
            $label = "{$user_info->user_login} (NV: {$user_info->ID})";
            $labels[] = $label;

            $note_count = $data['note'] ?? 0;

            $xem_thuong = max(0, $data['view'] - $data['phone']);
            $views[] = $xem_thuong;
            $phones[] = $data['phone'];
            $notes[] = $note_count;

            $tong = max($xem_thuong + $data['phone'] + $note_count, 1);
            $view_percentages[] = round(($xem_thuong / $tong) * 100, 1);
            $phone_percentages[] = round(($data['phone'] / $tong) * 100, 1);
            $note_percentages[] = round(($note_count / $tong) * 100, 1);

            $products_view[] = implode(', ', array_unique($data['nhadat_ids']['view']));
            $products_phone[] = implode(', ', array_unique($data['nhadat_ids']['phone']));
            $products_phone_detail[] = isset($phone_update_detail[$user_id])
                ? implode("\n", $phone_update_detail[$user_id])
                : '';
            $products_note_detail[] = isset($note_detail_by_user[$user_id])
                ? implode("\n", $note_detail_by_user[$user_id])
                : '';
        }
    }
    $chart_datasets = [];
    if ($can_view_phone_stat) {
        $chart_datasets[] = [
            'label' => 'Xem số điện thoại',
            'data' => $views,
            'backgroundColor' => 'rgba(239, 68, 68, 0.8)'
        ];
    }
    $chart_datasets[] = [
        'label' => 'Cập nhật tình trạng số điện thoại',
        'data' => $phones,
        'backgroundColor' => 'rgba(75, 192, 192, 0.8)'
    ];
    $chart_datasets[] = [
        'label' => 'Bổ xung thông tin nhà',
        'data' => $notes,
        'backgroundColor' => 'rgba(255, 159, 64, 0.8)'
    ];
    ?>


    <form method="get" action="<?php echo admin_url('admin-post.php'); ?>"
          style="margin-bottom:12px;display:flex;gap:8px;align-items:center;">

        <input type="hidden" name="action" value="export_luotxem_sdt">

        <select name="month">
            <?php
            $currentMonth = date('n');
            for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php selected($m, $currentMonth); ?>>
                    Tháng <?php echo $m; ?>
                </option>
            <?php endfor; ?>
        </select>

        <select name="year">
            <?php
            $currentYear = date('Y');
            for ($y = $currentYear; $y >= $currentYear - 3; $y--): ?>
                <option value="<?php echo $y; ?>" <?php selected($y, $currentYear); ?>>
                    <?php echo $y; ?>
                </option>
            <?php endfor; ?>
        </select>

        <button class="button button-primary">
            ⬇ Xuất CSV
        </button>

    </form>
    <div id="user_view_property_chart">


        <canvas id="user-view-chart" width="100%" height="288"></canvas>
    </div>

    <style>
        #chartjs-tooltip-user-view {
            position: fixed;
            pointer-events: none;
            z-index: 99999;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            padding: 12px 14px;
            max-width: 380px;
            max-height: 320px;
            overflow-y: auto;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            white-space: pre-wrap;
            opacity: 0;
            transition: opacity .1s ease;
        }
        #chartjs-tooltip-user-view.is-visible {
            opacity: 1;
            pointer-events: auto; /* cho phép cuộn chuột khi cần */
        }
        #chartjs-tooltip-user-view .tt-title {
            font-weight: bold;
            font-size: 14px;
            color: #000;
            margin-bottom: 6px;
        }
        #chartjs-tooltip-user-view .tt-body {
            margin-bottom: 2px;
        }
        #chartjs-tooltip-user-view .tt-after {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px dashed #ddd;
            color: #444;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('user-view-chart').getContext('2d');

            const viewPercents = <?php echo json_encode($can_view_phone_stat ? $view_percentages : []); ?>;
            const phonePercents = <?php echo json_encode($phone_percentages); ?>;
            const notePercents = <?php echo json_encode($note_percentages); ?>;

            const productsView = <?php echo json_encode($can_view_phone_stat ? $products_view : []); ?>;
            const phoneUpdateDetail = <?php echo json_encode($products_phone_detail); ?>;
            const noteUpdateDetail = <?php echo json_encode($products_note_detail); ?>;

            // ===== Custom HTML tooltip (không bị widget dashboard che/cắt khi nhiều dữ liệu) =====
            function getOrCreateTooltip() {
                let tooltipEl = document.getElementById('chartjs-tooltip-user-view');
                if (!tooltipEl) {
                    tooltipEl = document.createElement('div');
                    tooltipEl.id = 'chartjs-tooltip-user-view';
                    document.body.appendChild(tooltipEl);
                }
                return tooltipEl;
            }

            function externalTooltipHandler(context) {
                const { chart, tooltip } = context;
                const tooltipEl = getOrCreateTooltip();

                if (tooltip.opacity === 0) {
                    tooltipEl.classList.remove('is-visible');
                    return;
                }

                // Build nội dung HTML
                let html = '';
                if (tooltip.title && tooltip.title.length) {
                    html += '<div class="tt-title">' + tooltip.title.map(escapeHtml).join('<br>') + '</div>';
                }
                (tooltip.body || []).forEach(b => {
                (b.lines || []).forEach(line => {
                    html += '<div class="tt-body">' + escapeHtml(line) + '</div>';
            });
            });
                if (tooltip.afterBody && tooltip.afterBody.length) {
                    html += '<div class="tt-after">' + tooltip.afterBody.map(escapeHtml).join('<br>') + '</div>';
                }
                tooltipEl.innerHTML = html;

                // Định vị theo vị trí con trỏ trên canvas, có giới hạn trong viewport để không bị tràn/che
                const canvasRect = chart.canvas.getBoundingClientRect();
                let left = canvasRect.left + tooltip.caretX + 12;
                let top = canvasRect.top + tooltip.caretY + 12;

                tooltipEl.classList.add('is-visible');
                // Đo kích thước sau khi có nội dung để canh lại cho không tràn màn hình
                requestAnimationFrame(() => {
                    const ttRect = tooltipEl.getBoundingClientRect();
                if (left + ttRect.width > window.innerWidth - 8) {
                    left = canvasRect.left + tooltip.caretX - ttRect.width - 12;
                }
                if (top + ttRect.height > window.innerHeight - 8) {
                    top = window.innerHeight - ttRect.height - 8;
                }
                if (top < 8) top = 8;
                if (left < 8) left = 8;
                tooltipEl.style.left = left + 'px';
                tooltipEl.style.top = top + 'px';
            });
            }

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            // Ẩn tooltip khi cuộn trang / rời khỏi biểu đồ
            window.addEventListener('scroll', () => {
                const el = document.getElementById('chartjs-tooltip-user-view');
            if (el) el.classList.remove('is-visible');
        }, true);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: <?php echo json_encode($chart_datasets); ?>
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            enabled: false,
                            external: externalTooltipHandler,
                            callbacks: {
                                title: ctx => ctx[0].label,
                            label: ctx => {
                            const i = ctx.dataIndex;
            const label = ctx.dataset.label;
            const val = ctx.formattedValue;
            let percent = 0;

            if (label === 'Xem số điện thoại') percent = viewPercents[i];
            else if (label === 'Cập nhật tình trạng số điện thoại') percent = phonePercents[i];
            else if (label === 'Bổ xung thông tin nhà') percent = notePercents[i];

            return `${label}: ${val} lượt (${percent}%)`;
        },
            afterBody: ctx => {
                const i = ctx[0].dataIndex;
                const label = ctx[0].dataset.label;

                if (label === 'Cập nhật tình trạng số điện thoại') {
                    const detail = phoneUpdateDetail[i];
                    if (!detail) return ['Chưa có cập nhật nào'];
                    return ['Chi tiết cập nhật:', ...detail.split('\n')];
                }

                if (label === 'Bổ xung thông tin nhà') {
                    const detail = noteUpdateDetail[i];
                    if (!detail) return ['Chưa có chỉnh sửa nào'];
                    return ['Chi tiết chỉnh sửa:', ...detail.split('\n')];
                }

                const products = productsView[i];
                const wrapped = products?.match(/.{1,40}/g) || ['Không có sản phẩm'];
                return ['Sản phẩm:', ...wrapped];
            }
        }
        },
            legend: {
                position: 'top',
                    labels: { color: '#333' }
            }
        },
            scales: {
                x: {
                    stacked: true,
                        ticks: { color: '#333' },
                    grid: { color: '#eee' }
                },
                y: {
                    beginAtZero: true,
                        stacked: true,
                        ticks: { color: '#333' },
                    grid: { color: '#eee' }
                }
            }
        }
        });
        });
    </script>
    <?php
}




add_action('wp_dashboard_setup', 'custom_dashboard_product_stats');

function custom_dashboard_product_stats() {
    global $wp_meta_boxes;

    // Widget 1 - Loại sản phẩm
    wp_add_dashboard_widget(
        'custom_product_stats_widget',
        'Thống kê Sản phẩm theo Loại',
        'render_custom_product_stats'
    );

    // Widget 2 - Loại bất động sản
    wp_add_dashboard_widget(
        'custom_property_stats_widget',
        'Thống kê Bất động sản theo Loại',
        'render_property_type_stats_widget'
    );

    // Widget 3 - Loại giao dịch (tạm add vào normal để sau chuyển sang side)
    wp_add_dashboard_widget(
        'custom_transaction_stats_widget',
        'Thống kê Giao dịch theo Loại',
        'render_transaction_type_stats_widget'
    );
    wp_add_dashboard_widget(
        'user_view_property_chart',
        'Thống Kê Xem Số Điện Thoại Trong Tuần Này',
        'render_user_view_property_chart'
    );

    // Di chuyển widget giao dịch sang cột bên phải (side)
    if (isset($wp_meta_boxes['dashboard']['normal']['core']['custom_transaction_stats_widget'])) {
        $widget = $wp_meta_boxes['dashboard']['normal']['core']['custom_transaction_stats_widget'];
        unset($wp_meta_boxes['dashboard']['normal']['core']['custom_transaction_stats_widget']);
        $wp_meta_boxes['dashboard']['side']['core']['custom_transaction_stats_widget'] = $widget;
    }
}

/*add_action('wp_dashboard_setup', function() {
    global $wp_meta_boxes;
    echo '<pre>';
    print_r($wp_meta_boxes['dashboard']);
    echo '</pre>';
});*/


// ==========================================================
// CSV Export dùng chung cho các biểu đồ thống kê trên Dashboard
// (Sản phẩm theo loại / BĐS theo loại / Giao dịch theo loại / Hàng nhập theo NV)
// ==========================================================

// In ra form chọn tháng/năm + nút Xuất CSV cho 1 loại biểu đồ (type)
function render_dashboard_csv_export_form( $type ) {
    ob_start();
    ?>
    <form method="get" action="<?php echo admin_url('admin-post.php'); ?>"
          style="margin-bottom:12px;display:flex;gap:8px;align-items:center;">
        <input type="hidden" name="action" value="export_dashboard_stats">
        <input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>">
        <select name="month">
            <?php $currentMonth = date('n'); for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php selected($m, $currentMonth); ?>>Tháng <?php echo $m; ?></option>
            <?php endfor; ?>
        </select>
        <select name="year">
            <?php $currentYear = date('Y'); for ($y = $currentYear; $y >= $currentYear - 3; $y--): ?>
                <option value="<?php echo $y; ?>" <?php selected($y, $currentYear); ?>><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>
        <button class="button button-primary">⬇ Xuất CSV</button>
    </form>
    <?php
    return ob_get_clean();
}

add_action('admin_post_export_dashboard_stats', 'handle_export_dashboard_stats_csv');
function handle_export_dashboard_stats_csv() {

    if ( ! current_user_can('manage_options') ) {
        wp_die('Không có quyền export');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'dulieunhadat';

    $type  = isset($_GET['type']) ? sanitize_key($_GET['type']) : '';
    $month = intval($_GET['month'] ?? date('m'));
    $year  = intval($_GET['year'] ?? date('Y'));

    if ($month < 1 || $month > 12) wp_die('Month invalid');
    if ($year < 2000) wp_die('Year invalid');

    $allowed_types = ['product', 'property', 'transaction', 'hangnhap'];
    if ( ! in_array($type, $allowed_types, true) ) {
        wp_die('Loại biểu đồ không hợp lệ');
    }

    $filename = "thongke_{$type}_{$year}_{$month}.csv";

    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // UTF-8 BOM cho Excel
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    switch ( $type ) {

        case 'product':
            $rows = $wpdb->get_results( $wpdb->prepare("
                SELECT id, product_type, giaban, datecreate
                FROM {$table}
                WHERE product_type IN ('517','515')
                  AND MONTH(datecreate) = %d AND YEAR(datecreate) = %d
                ORDER BY datecreate DESC
            ", $month, $year) );

            $type_labels = ['517' => 'Dự án', '515' => 'Nhà ở riêng lẻ'];

            fputcsv($output, ['ID', 'Loại sản phẩm', 'Giá bán', 'Ngày tạo']);
            foreach ( $rows as $row ) {
                fputcsv($output, [
                    $row->id,
                    $type_labels[$row->product_type] ?? $row->product_type,
                    $row->giaban,
                    $row->datecreate ? date('d/m/Y H:i', strtotime($row->datecreate)) : '',
                ]);
            }
            break;

        case 'property':
            $rows = $wpdb->get_results( $wpdb->prepare("
                SELECT id, property_type, datecreate
                FROM {$table}
                WHERE MONTH(datecreate) = %d AND YEAR(datecreate) = %d
                ORDER BY datecreate DESC
            ", $month, $year) );

            fputcsv($output, ['ID', 'Loại BĐS', 'Ngày tạo']);
            foreach ( $rows as $row ) {
                $term = get_term($row->property_type, 'category');
                $label = ($term && !is_wp_error($term)) ? $term->name : 'Không rõ';
                fputcsv($output, [$row->id, $label, $row->datecreate ? date('d/m/Y H:i', strtotime($row->datecreate)) : '']);
            }
            break;

        case 'transaction':
            $rows = $wpdb->get_results( $wpdb->prepare("
                SELECT id, transaction_type, datecreate
                FROM {$table}
                WHERE MONTH(datecreate) = %d AND YEAR(datecreate) = %d
                ORDER BY datecreate DESC
            ", $month, $year) );

            fputcsv($output, ['ID', 'Loại giao dịch', 'Ngày tạo']);
            foreach ( $rows as $row ) {
                $term = get_term($row->transaction_type, 'category');
                $label = ($term && !is_wp_error($term)) ? $term->name : 'Không rõ';
                fputcsv($output, [$row->id, $label, $row->datecreate ? date('d/m/Y H:i', strtotime($row->datecreate)) : '']);
            }
            break;

        case 'hangnhap':
            $rows = $wpdb->get_results( $wpdb->prepare("
                SELECT d.id, d.user AS nhanvien_id, u.display_name, d.datecreate
                FROM {$table} d
                LEFT JOIN {$wpdb->prefix}users u ON u.ID = d.user
                WHERE MONTH(d.datecreate) = %d AND YEAR(d.datecreate) = %d
                ORDER BY d.datecreate DESC
            ", $month, $year) );

            fputcsv($output, ['ID', 'ID Nhân Viên', 'Tên Nhân Viên', 'Ngày tạo']);
            foreach ( $rows as $row ) {
                fputcsv($output, [
                    $row->id,
                    $row->nhanvien_id,
                    $row->display_name ?: "NV#{$row->nhanvien_id}",
                    $row->datecreate ? date('d/m/Y H:i', strtotime($row->datecreate)) : '',
                ]);
            }
            break;
    }

    fclose($output);
    exit;
}


function render_custom_product_stats() {
    global $wpdb;

    // Thêm CSS cho widget
    echo '<style>
        #custom_product_stats_widget {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }
    </style>';

    // Lấy dữ liệu từ database
    $results = $wpdb->get_results("
        SELECT product_type, giaban
        FROM wp_dulieunhadat
        WHERE product_type IN ('517', '515')
    ");

    $stats = [
        '517' => ['label' => 'Dự án', 'count' => 0, 'total_price' => 0],
        '515' => ['label' => 'Nhà ở riêng lẻ', 'count' => 0, 'total_price' => 0],
    ];

    foreach ($results as $row) {
        $product_type = $row->product_type;
        $price_str = trim($row->giaban);
        $price = 0;

        if (is_numeric($price_str)) {
            $price = floatval($price_str) / 1000000; // chuyển về triệu
        } else {
            $price_str = strtolower($price_str);
            if (strpos($price_str, 'tỷ') !== false) {
                $price = floatval(str_replace(',', '.', str_replace('tỷ', '', $price_str))) * 1000;
            } elseif (strpos($price_str, 'triệu') !== false) {
                $price = floatval(str_replace(',', '.', str_replace('triệu', '', $price_str)));
            }
        }

        $stats[$product_type]['count']++;
        $stats[$product_type]['total_price'] += $price;
    }

    // Hiển thị bảng
    echo '<div id="custom_product_stats_widget">';

    echo render_dashboard_csv_export_form( 'product' );

    echo '<table class="widefat striped">';
    echo '<thead><tr><th>Loại sản phẩm</th><th>Số lượng</th><th>Tổng giá bán</th></tr></thead><tbody>';

    $labels = [];
    $counts = [];
    $prices = [];
    $price_labels = []; // dùng để hiển thị giá trị trên biểu đồ

    foreach ($stats as $type => $data) {
        echo '<tr>';
        echo '<td>' . esc_html($data['label']) . '</td>';
        echo '<td>' . esc_html($data['count']) . '</td>';

        if ($data['total_price'] >= 1000) {
            $display = number_format($data['total_price'] / 1000, 2, ',', '.') . ' tỷ';
            $price_labels[] = round($data['total_price'] / 1000, 2);
        } else {
            $display = number_format($data['total_price'], 0, ',', '.') . ' triệu';
            $price_labels[] = round($data['total_price'], 0);
        }

        echo '<td>' . $display . '</td>';
        echo '</tr>';

        $labels[] = $data['label'];
        $counts[] = $data['count'];
        $prices[] = $data['total_price'];
    }

    echo '</tbody></table>';

    // Vẽ biểu đồ
    echo '<canvas id="product-stats-chart" height="100" style="margin-top:20px;"></canvas>';
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<script>
        const ctx = document.getElementById("product-stats-chart").getContext("2d");
        const rawPrices = ' . json_encode($prices) . ';
        const adjustedPrices = rawPrices.map(p => p >= 1000 ? (p / 1000).toFixed(2) : p);
        const priceUnit = rawPrices.some(p => p >= 1000) ? "tỷ" : "triệu";

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ' . json_encode($labels) . ',
                datasets: [{
                    label: "Số lượng sản phẩm",
                    data: ' . json_encode($counts) . ',
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: "rgba(75,192,192,0.8)",
                    hoverBorderWidth: 3
                }, {
                    label: "Tổng giá bán (" + priceUnit + ")",
                    data: adjustedPrices,
                    backgroundColor: "rgba(255, 99, 132, 0.6)",
                    borderColor: "rgba(255, 99, 132, 1)",
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: "rgba(255, 99, 132, 0.8)",
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + " " + priceUnit;
                            }
                        }
                    }
                }
            }
        });
    </script>';
    echo '</div>';
}


function render_property_type_stats_widget() {
    global $wpdb;


    // Thêm CSS cho widget
    echo '<style>
        #custom_property_stats_widget {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }
    </style>';

    // Lấy dữ liệu từ bảng wp_dulieunhadat với property_type
    $results = $wpdb->get_results("SELECT property_type FROM wp_dulieunhadat");

    // Khởi tạo mảng thống kê cho các loại bất động sản
    $stats = [];

    // Duyệt qua các bản ghi trả về và tính toán số lượng
    foreach ($results as $row) {
        $type = $row->property_type;

        // Kiểm tra nếu loại bất động sản chưa có trong mảng thống kê
        if (!isset($stats[$type])) {
            // Lấy tên loại bất động sản từ taxonomy (category) dựa trên ID
            $term = get_term($type, 'category');  // 'property_type' là tên taxonomy

            // Nếu term không có giá trị (ví dụ không có loại bất động sản trong taxonomy), gán tên là "Không rõ"
            $stats[$type] = [
                'label' => $term && !is_wp_error($term) ? $term->name : 'Không rõ', // Tên loại bất động sản
                'count' => 0 // Số lượng
            ];
        }

        // Cập nhật số lượng
        $stats[$type]['count']++;
    }

    // Hiển thị widget
    echo '<div id="custom_property_stats_widget" class="postbox">';
    echo '<div class="inside">';

    echo render_dashboard_csv_export_form( 'property' );

    echo '<table class="widefat striped"><thead><tr><th>Loại BĐS</th><th>Số lượng</th></tr></thead><tbody>';

    // Mảng để vẽ biểu đồ
    $labels = $counts = [];

    // Duyệt qua thống kê và hiển thị bảng
    foreach ($stats as $data) {
        echo '<tr><td>' . esc_html($data['label']) . '</td><td>' . $data['count'] . '</td></tr>';
        $labels[] = $data['label'];
        $counts[] = $data['count'];
    }

    echo '</tbody></table>';

    // Vẽ biểu đồ ngay trong cùng hàm
    echo '<canvas id="property_type_chart" height="100" style="margin-top:20px;"></canvas>';
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<script>
        new Chart(document.getElementById("property_type_chart").getContext("2d"), {
            type: "bar",
            data: {
                labels: ' . json_encode($labels) . ',
                datasets: [{
                    label: "Số lượng sản phẩm",
                    data: ' . json_encode($counts) . ',
                    backgroundColor: "rgba(255, 159, 64, 0.6)", /* Màu cam */
                    borderColor: "rgba(255, 159, 64, 1)", /* Màu cam */
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>';

    echo '</div></div>';
}

function render_transaction_type_stats_widget() {
    global $wpdb;
    // Thêm CSS cho widget
    echo '<style>
        #custom_transaction_stats_widget {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }
    </style>';
    // Lấy dữ liệu từ bảng wp_dulieunhadat với transaction_type
    $results = $wpdb->get_results("SELECT transaction_type FROM wp_dulieunhadat");

    // Khởi tạo mảng thống kê
    $stats = [];

    // Duyệt qua các bản ghi trả về và tính toán số lượng
    foreach ($results as $row) {
        $type = $row->transaction_type;

        if (!isset($stats[$type])) {
            $term = get_term($type, 'category'); // Lấy tên từ taxonomy 'category'
            $stats[$type] = [
                'label' => $term && !is_wp_error($term) ? $term->name : 'Không rõ',
                'count' => 0
            ];
        }

        $stats[$type]['count']++;
    }

    // Hiển thị widget
    echo '<div id="custom_transaction_stats_widget" class="postbox">';
    echo '<div class="inside">';

    echo render_dashboard_csv_export_form( 'transaction' );

    echo '<table class="widefat striped"><thead><tr><th>Loại giao dịch</th><th>Số lượng</th></tr></thead><tbody>';

    $labels = $counts = [];

    foreach ($stats as $data) {
        echo '<tr><td>' . esc_html($data['label']) . '</td><td>' . $data['count'] . '</td></tr>';
        $labels[] = $data['label'];
        $counts[] = $data['count'];
    }

    echo '</tbody></table>';

    echo '<canvas id="transaction_type_chart" height="100" style="margin-top:20px;"></canvas>';
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<script>
        new Chart(document.getElementById("transaction_type_chart").getContext("2d"), {
            type: "bar",
            data: {
                labels: ' . json_encode($labels) . ',
                datasets: [{
                    label: "Số lượng giao dịch",
                    data: ' . json_encode($counts) . ',
                    backgroundColor: "rgba(255, 99, 132, 0.6)", // Màu hồng đỏ
                    borderColor: "rgba(255, 99, 132, 1)",
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>';

    echo '</div></div>';
}



// Ẩn bảng Welcome Panel và xóa checkbox trong Screen Options
function hide_welcome_panel_for_all_users() {
    // Ẩn bảng chào mừng
    remove_action('welcome_panel', 'wp_welcome_panel');

    // Ẩn checkbox trong screen options
    echo '<style>
        #welcome-panel { display: none !important; }
        input[name="wp_welcome_panel"] { display: none !important; }
        label[for="wp_welcome_panel"] { display: none !important; }
    </style>';
}
add_action('admin_head-index.php', 'hide_welcome_panel_for_all_users');




// ẩn tất cả dashboard
function hide_all_dashboard_widgets_except_custom_ones() {
    global $wp_meta_boxes;

    foreach ( $wp_meta_boxes['dashboard'] as $context => $widgets ) {
        foreach ( $widgets as $priority => $widget_group ) {
            foreach ( $widget_group as $widget_id => $widget ) {
                if ( !in_array( $widget_id, [
                    'custom_product_stats_widget',
                    'user_login_activity_chart',
                    'custom_property_stats_widget',
                    'custom_transaction_stats_widget',
                    'user_data_entry_chart',
                    'user_data_entry_chart_weekly', // ← Chart hàng nhập theo NV trong tuần
                    'user_view_property_chart' // ← Thêm dòng này
                ] ) ) {
                    unset( $wp_meta_boxes['dashboard'][$context][$priority][$widget_id] );
                }

            }
        }
    }
}
add_action( 'wp_dashboard_setup', 'hide_all_dashboard_widgets_except_custom_ones', 100 );

//hide language select in login
// Ẩn dropdown chọn ngôn ngữ
add_action('login_head', function () {
    ?>
    <style>
        .language-switcher {
            display: none !important;
        }
    </style>
    <?php
});

// Ẩn liên kết "← Quay lại trang chủ"
add_filter('login_footer', function () {
    ?>
    <style>
        #backtoblog {
            display: none !important;
        }
    </style>
    <?php
});

// Ép ngôn ngữ mặc định là tiếng Việt
add_action('init', function () {
    if (!is_user_logged_in()) {
        switch_to_locale('vi');
    }
});

add_action('login_enqueue_scripts', function () {
    ?>
    <style>
        /* Nút đăng nhập */
        #wp-submit {
            background-color: #008000 !important; /* Màu xanh dương */
            border-color: #008000 !important;
            color: #fff !important;
            font-weight: bold;
        }

        #wp-submit:hover {
            background-color: #ffa500 !important;
            border-color: #ffa500 !important;
        }
    </style>
    <?php
});

//hide menu left something
add_action('admin_menu', function () {
    // Danh sách các mục muốn ẩn
    remove_menu_page('edit.php');                   // Bài viết (Posts)
    remove_menu_page('edit-comments.php');          // Bình luận (Comments)
    remove_menu_page('plugins.php');                // Plugin
    remove_menu_page('tools.php');                  // Công cụ
    remove_menu_page('themes.php');                 // Giao diện
    remove_menu_page('options-general.php');        // Cài đặt
    remove_menu_page('edit.php?post_type=page');
    // Ẩn mục Woodmart
    remove_menu_page('xts_dashboard'); // Đây là slug của Woodmart nếu menu có slug này.
    // Ẩn mục Theme Settings
    remove_menu_page('xts_theme_settings'); // Tên slug của mục Theme Settings
});
add_action('admin_menu', function () {
    global $menu;

    foreach ($menu as $key => $item) {
        if ($item[2] === 'upload.php') {
            $menu[$key][0] = 'Hình sổ hồng'; // Đổi tên tại đây
        }
    }
});

add_action('admin_head', function() {
    echo '
    <style>
        /* Chỉnh sửa font cho menu trong trang quản trị */
        #adminmenu, #adminmenu .wp-submenu a {
            font-family: "Arial", sans-serif; /* Chọn font dễ đọc và chuyên nghiệp */
            font-size: 14px; /* Cỡ chữ vừa phải */
            font-weight: 600; /* Làm chữ đậm để dễ nhìn */
            line-height: 1.6; /* Tăng chiều cao dòng để dễ đọc */
            color: #333; /* Màu chữ đậm, dễ nhìn */
        }

        /* Chỉnh sửa menu con */
        #adminmenu .wp-submenu a {
            padding-left: 20px; /* Thêm khoảng cách giữa icon và chữ */
            color: #555; /* Màu chữ menu con nhạt hơn */
        }

        #adminmenu li:hover, #adminmenu .wp-submenu a:hover {
            background-color: #f1f1f1; /* Nền sáng khi hover */
            color: #0073aa; /* Màu chữ khi hover */
        }

        /* Chỉnh sửa menu chính khi chọn */
        #adminmenu li.menu-top.current {
            background-color: #0073aa; /* Nền màu xanh khi chọn */
        }

        #adminmenu li.menu-top.current a {
            color: #fff; /* Màu chữ khi chọn */
        }

        /* Thêm một chút hiệu ứng chuyển động khi hover */
        #adminmenu li:hover {
            transform: translateX(4px); /* Dịch chuyển nhẹ sang phải */
            transition: transform 0.2s ease; /* Hiệu ứng mượt mà */
        }
    </style>';
});


add_action('admin_head', function() {
    echo '
    <style>
        /* Font chữ đẹp, size vừa */
        #adminmenu, #adminmenu a {
            font-family: "Inter", "Segoe UI", "Roboto", sans-serif !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #2c3e50 !important;
            line-height: 1.5 !important;
            white-space: nowrap !important; /* Không xuống dòng */
        }

        /* Bo góc và padding vừa phải */
        #adminmenu li.menu-top > a {
            padding: 5px 5px;
            border-top-left-radius: 6px !important;
            border-bottom-left-radius: 6px !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            transition: background 0.2s ease, color 0.2s ease;
        }

        /* Màu khi hover */
        #adminmenu li.menu-top:hover > a {
            background-color: #fff3e0 !important;
            color: #ef6c00 !important;
        }

        /* Màu khi active */
        #adminmenu li.menu-top.current > a,
        #adminmenu li.menu-top.wp-has-current-submenu > a {
            background-color: #e8f5e9 !important;
            color: #2e7d32 !important;
            font-weight: 600 !important;
        }

        /* Tăng độ mượt cho icon */
        #adminmenu .wp-menu-image {
            opacity: 0.9;
        }
    </style>';
});
add_action('admin_head', function() {
    echo '
    <style>
        /* Ẩn separator giữa các menu */
        #adminmenu .wp-menu-separator {
            display: none !important;
        }
    </style>';
});

// fix css page user list

add_action('admin_head-users.php', function() {
    echo '
    <style>
        /* Tổng thể tablenav top */
        .users-php .tablenav.top {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex; /* Dùng flexbox để căn chỉnh */
            justify-content: space-between; /* Căn giữa các phần tử */
            align-items: center; /* Căn chỉnh theo chiều dọc */
            flex-wrap: wrap; /* Cho phép phần tử wrap nếu cần */
            height:70px;
        }

        /* Bộ lọc trên cùng (tạo khoảng cách cho nó đẹp) */
        .users-php .subsubsub {
            margin: 0;
            font-size: 14px;
            flex-grow: 1; /* Làm bộ lọc chiếm không gian còn lại */
        }

        /* Tùy chỉnh vị trí ô tìm kiếm */
        .users-php .search-box {
            display: flex;
            align-items: center;
            justify-content: flex-end; /* Đặt tìm kiếm ở bên phải */
            flex-grow: 2; /* Chiếm không gian còn lại */
            gap: 15px; /* Thêm khoảng cách giữa các input */
            flex-wrap: wrap; /* Cho phép các phần tử không bị chồng lên nhau */
        }

        .users-php .search-box input[name="s"] {
            padding: 6px 10px;
            font-size: 13px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-right: 10px;
            min-width: 180px; /* Tăng độ rộng của input */
        }

        .users-php .search-box .button {
            padding: 6px 14px;
            background-color: #007cba;
            border: none;
            color: #fff;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }

        .users-php .search-box .button:hover {
            background-color: #006799;
        }

        /* Bảng danh sách */
        .users-php table.wp-list-table {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        }

        .users-php table.wp-list-table thead th {
            background: #fafafa;
            font-weight: bold;
            font-size: 13px;
            color: #333;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .users-php table.wp-list-table tbody td {
            font-size: 13px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        /* Tăng padding từng hàng */
        .users-php table.wp-list-table tbody tr {
            transition: background 0.2s ease;
        }

        .users-php table.wp-list-table tbody tr:hover {
            background: #fefefe;
        }
    </style>';
});
add_action('admin_head-users.php', function() {
    echo '
    <style>
        /* Tổng thể tablenav bottom */
        .users-php .tablenav.bottom {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px 12px;
            border-radius: 8px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between; /* Căn giữa các phần tử */
            align-items: center; /* Căn chỉnh theo chiều dọc */
            flex-wrap: wrap; /* Cho phép các phần tử không bị chồng lên nhau */
            height:70px;
        }

        /* Các phần tử trong phân trang */
        .users-php .tablenav.bottom .tablenav-pages {
            font-size: 14px;
            margin: 0;
        }

        /* Căn chỉnh các trang phân trang */
        .users-php .tablenav.bottom .tablenav-pages a {
            padding: 6px 12px;
            background-color: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 5px;
            font-size: 13px;
            transition: background-color 0.3s ease;
        }

        /* Hover trên các trang phân trang */
        .users-php .tablenav.bottom .tablenav-pages a:hover {
            background-color: #006799;
        }

        /* Phần trạng thái số lượng người dùng ( ví dụ: 1-20 of 100 ) */
        .users-php .tablenav.bottom .tablenav-pages span {
            font-size: 14px;
            color: #333;
        }

        /* Tùy chỉnh khoảng cách giữa các phần tử trong tablenav */
        .users-php .tablenav.bottom .tablenav-pages {
            display: flex;
            justify-content: flex-end; /* Đặt phân trang ở bên phải */
            flex-grow: 1;
            gap: 10px; /* Tăng khoảng cách giữa các trang */
        }

        /* Phần phân trang bên trái */
        .users-php .tablenav.bottom .tablenav-pages .total-pages {
            font-weight: bold;
            color: #333;
            padding: 6px 12px;
            background-color: #e0e0e0;
            border-radius: 4px;
        }

        /* Tạo khoảng cách giữa các phần tử khác */
        .users-php .tablenav.bottom .actions {
            font-size: 14px;
        }

    </style>';
});

add_action('admin_head', function() {
    echo '
    <style>
        /* Nút với nền xám nhạt, viền nhẹ */
        .wp-admin input[type="submit"], 
        .wp-admin .button, 
        .wp-admin .tablenav-pages a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 0 16px;
            font-size: 14px;
            height: 32px; /* Chiều cao nút bằng input */
            line-height: 1; /* Đảm bảo chữ không bị lệch */
            color: #495057; /* Màu chữ xám đậm */
            background-color: #f8f9fa; /* Màu nền xám nhạt */
            border: 1px solid #ced4da; /* Viền xám sáng */
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            text-decoration: none; /* Bỏ underline cho <a> */
        }

        /* Hiệu ứng hover - nền sẽ đổi màu nhẹ */
        .wp-admin input[type="submit"]:hover,
        .wp-admin .button:hover,
        .wp-admin .tablenav-pages a:hover {
            background-color: #d1e7dd; /* Nền chuyển sang xanh pastel */
            border-color: #bcd0d2; /* Viền chuyển sang pastel nhạt */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px); /* Nâng lên khi hover */
        }

        /* Hiệu ứng khi nhấn */
        .wp-admin input[type="submit"]:active,
        .wp-admin .button:active,
        .wp-admin .tablenav-pages a:active {
            background-color: #a2d2c7; /* Nền chuyển sang xanh đậm hơn khi nhấn */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transform: translateY(1px); /* Lún xuống khi nhấn */
        }

        /* Điều chỉnh margin và padding nếu cần */
        .wp-admin .tablenav-pages a {
            margin: 0 5px;
        }
    </style>';
});

// chỉnh phần dưới phân trang to lên
add_action('admin_head', function () {
    echo '<style>
        /* Tăng kích thước chữ cho bảng jqGrid */
        .ui-jqgrid {
            font-size: 14px; /* Chỉnh chữ trong bảng */
        }

        /* Tăng kích thước chữ trong phần phân trang */
        .ui-jqgrid .ui-jqgrid-pager {
            font-size: 14px;
        }

        /* Chỉnh kích thước các ô input trong phân trang */
        .ui-jqgrid .ui-pg-input,
        .ui-jqgrid .ui-pg-selbox {
            height: 30px;
            font-size: 14px;
            padding: 2px 6px;
        }

        /* Chỉnh kích thước các nút trong phân trang */
        .ui-jqgrid .ui-jqgrid-pager .ui-pg-button {
            font-size: 16px;
        }

        /* Tăng khoảng cách padding trong bảng */
        .ui-jqgrid tr.jqgrow td {
            padding: 8px 6px;
        }

        /* Tăng kích thước chữ của tiêu đề cột */
        .ui-jqgrid th.ui-state-default {
            font-size: 15px;
        }
    </style>';
});

add_action('admin_head', function () {
    echo '<style>
        /* Đảm bảo màu trắng cho tất cả các icon trong phân trang */
        .ui-jqgrid .ui-jqgrid-pager .ui-pg-button .ui-icon {
            color: white !important; /* Đảm bảo màu trắng cho các icon */
        }

        /* Hiệu ứng hover cho các icon phân trang */
        .ui-jqgrid .ui-jqgrid-pager .ui-pg-button:hover .ui-icon {
            color: white !important; /* Giữ màu trắng cho icon khi hover */
        }

        /* Cải tiến cho phần phân trang, nút và hiệu ứng 3D */
        .ui-jqgrid .ui-jqgrid-pager .ui-pg-button {
            font-size: 16px;
            padding: 8px 16px;
            margin-right: 5px;
            border-radius: 5px;
            background: linear-gradient(145deg, #0073aa, #005e8b); /* Gradient xanh dương */
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.2); /* Hiệu ứng bóng đổ */
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
        }

        /* Hiệu ứng hover cho nút phân trang */
        .ui-jqgrid .ui-jqgrid-pager .ui-pg-button:hover {
            background: linear-gradient(145deg, #005e8b, #0073aa); /* Đảo ngược gradient khi hover */
            transform: translateY(-3px); /* Di chuyển nút lên trên để tạo hiệu ứng 3D */
            box-shadow: 3px 6px 10px rgba(0, 0, 0, 0.3); /* Tăng hiệu ứng bóng đổ */
        }

        /* Phần input và ô lựa chọn phân trang */
        .ui-jqgrid .ui-pg-input,
        .ui-jqgrid .ui-pg-selbox {
            height: 32px;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            background-color: #fff;
            margin-right: 10px;
        }

        .ui-jqgrid .ui-pg-input:focus,
        .ui-jqgrid .ui-pg-selbox:focus {
            border-color: #007bff;
            outline: none;
        }
    </style>';
});



function custom_jqgrid_icons() {
    if (is_admin()) {
        ?>
        <style>
            .ui-jqgrid .ui-icon {
                background-image: url('https://warehouse.urbanhome.vn/wp-content/uploads/2025/04/ui-icons_ffffff_256x240.png') !important;
                background-size: 256px 240px !important;
                width: 16px !important;
                height: 16px !important;
                display: inline-block !important;
            }

            /* Icon chính */
            .ui-icon.ui-icon-plus        { background-position: -32px -128px !important; }
            .ui-icon.ui-icon-pencil      { background-position: -64px -112px !important; }
            .ui-icon.ui-icon-trash       { background-position: -176px -96px !important; }
            .ui-icon.ui-icon-search      { background-position: -96px -96px !important; }
            .ui-icon.ui-icon-refresh     { background-position: -64px -80px !important; }
            .ui-icon.ui-icon-disk        { background-position: -96px -112px !important; }
            .ui-icon.ui-icon-cancel      { background-position: -32px -112px !important; }

            /* Phân trang */
            .ui-icon.ui-icon-seek-first  { background-position:  -80px -160px !important; }
            .ui-icon.ui-icon-seek-prev   { background-position:  -50px -160px !important; }
            .ui-icon.ui-icon-seek-next   { background-position: -32px -160px !important; }
            .ui-icon.ui-icon-seek-end    { background-position: -64px -160px !important; }
        </style>
        <?php
    }
}
add_action('admin_head', 'custom_jqgrid_icons');

//redirect page home to admin
add_action('template_redirect', 'redirect_homepage_to_admin');
function redirect_homepage_to_admin() {
    if (is_front_page()) {
        // Optional: Only redirect non-admins
        if (is_user_logged_in()) {
            wp_redirect(admin_url());
        } else {
            wp_redirect(wp_login_url());
        }
        exit;
    }
}


add_action('template_redirect', function () {
    if (is_author()) {
        wp_redirect(home_url('/')); // hoặc chuyển hướng về trang tùy chọn
        exit;
    }
});


add_filter('gettext', function ($translated_text, $text, $domain) {
    // Đổi nhãn "Username" hoặc "Tên người dùng (bắt buộc)" thành "Thêm nhân sự"
    if (in_array($text, ['Username', 'Tên người dùng (bắt buộc)'])) {
        return 'Thêm nhân sự';
    }
    return $translated_text;
}, 10, 3);


add_action('wp_ajax_get_child_category_list', 'get_child_category_list_callback');
add_action('wp_ajax_nopriv_get_child_category_list', 'get_child_category_list_callback');

function get_child_category_list_callback() {
    // Kiểm tra nonce hoặc quyền nếu cần

    // Lấy parent_id từ ajax request
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
    $search_term = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';

    if (!$parent_id) {
        wp_send_json_error(['message' => 'Missing parent_id']);
        wp_die();
    }

    // Truy vấn các category con theo parent_id
    $args = [
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'parent'     => $parent_id,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'number'     => 20,
    ];

    if ($search_term) {
        $args['search'] = $search_term;
    }

    $categories = get_terms($args);

    $results = [];
    if (!is_wp_error($categories)) {
        foreach ($categories as $cat) {
            $results[] = [
                'id' => $cat->term_id,
                'text' => $cat->name,
            ];
        }
    }

    wp_send_json(['data' => $results]);
    wp_die();
}




add_action('wp_ajax_get_user_list', 'get_user_list_callback');
add_action('wp_ajax_nopriv_get_user_list', 'get_user_list_callback');

function get_user_list_callback() {
    global $wpdb; // cần để truy vấn số lượng sản phẩm
    $current_user = wp_get_current_user();
    $is_admin = current_user_can('administrator');
    $search_term = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
    $role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : '';

    if ($is_admin) {
        // Admin: lấy hết user không giới hạn số lượng
        $args = [
            'orderby' => 'display_name',
            'order' => 'ASC',
            'fields' => ['ID', 'display_name'],
            'number' => 0, // 0 = lấy tất cả
        ];

        if ($search_term) {
            $args['search'] = '*' . $search_term . '*';
            $args['search_columns'] = ['user_login', 'user_nicename', 'user_email', 'display_name'];
        }

        if ($role) {
            $args['role'] = $role;
        }

        $users = get_users($args);
    } else {
        // User thường: chỉ trả về chính họ
        $users = [$current_user];
    }

    $results = [];
    $table = $wpdb->prefix . 'dulieunhadat';

    foreach ($users as $user) {
        // Đếm số sản phẩm của user
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE user = %d AND deleted = 0",
                $user->ID
            )
        );

        $results[] = [
            'id' => $user->ID,
            'text' => $user->display_name , // hiển thị số sản phẩm kế bên
            'count' => intval($count) // dùng để sort
        ];
    }

    // Sắp xếp giảm dần theo số sản phẩm
    usort($results, function($a, $b) {
        return $b['count'] - $a['count'];
    });

    // Xóa trường 'count' trước khi trả về nếu muốn
    foreach ($results as &$item) {
        unset($item['count']);
    }

    wp_send_json(['data' => $results]);
    wp_die();
}


add_action('wp_ajax_get_user_list_khohang', 'get_user_list_khohang_callback');
add_action('wp_ajax_nopriv_get_user_khohang_list', 'get_user_list_khohang_callback');

function get_user_list_khohang_callback() {
    global $wpdb;
    $current_user = wp_get_current_user();
    $is_admin = current_user_can('administrator');
    $search_term = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
    $role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : '';

    if ($is_admin) {
        // Admin: lấy tất cả user
        $args = [
            'orderby' => 'display_name',
            'order'   => 'ASC',
            'fields'  => ['ID', 'display_name'],
            'number'  => 0,
        ];

        if ($search_term) {
            $args['search'] = '*' . $search_term . '*';
            $args['search_columns'] = ['user_login', 'user_nicename', 'user_email', 'display_name'];
        }

        if ($role) {
            $args['role'] = $role;
        }

        $users = get_users($args);
    } else {
        // User thường: chỉ lấy chính họ
        $users = [$current_user];
    }

    $results = [];
    $table   = $wpdb->prefix . 'dulieunhadat';
    $today   = time();

    foreach ($users as $user) {
        // Lấy tất cả sản phẩm của user
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT dateupdate, datecreate 
                 FROM $table 
                 WHERE user = %d AND deleted = 0",
                $user->ID
            ),
            ARRAY_A
        );

        $expired_count = 0;

        foreach ($rows as $row) {
            $ngayNguon = (!empty($row['dateupdate']) && $row['dateupdate'] !== '0000-00-00 00:00:00')
                ? $row['dateupdate']
                : $row['datecreate'];

            $timestamp = strtotime($ngayNguon);
            if (!$timestamp) continue;

            $soNgay = ($today - $timestamp) / (60 * 60 * 24);
            if ($soNgay > 30) {
                $expired_count++;
            }
        }

        $results[] = [
            'id'   => $user->ID,
            //'text' => $user->display_name . ' (' . intval($expired_count) . ' hết hạn)',
            'text' => $user->display_name ,
            'count'=> intval($expired_count)
        ];
    }

    // Sắp xếp giảm dần theo số sản phẩm hết hạn
    usort($results, function($a, $b) {
        return $b['count'] - $a['count'];
    });

    // Xóa trường count trước khi trả về
    foreach ($results as &$item) {
        unset($item['count']);
    }

    wp_send_json(['data' => $results]);
    wp_die();
}




//Get image wordpress to form edit
add_action('wp_ajax_get_image_url', 'get_image_url_callback');
function get_image_url_callback() {
    if (!isset($_POST['image_id'])) {
        wp_send_json_error(['message' => 'No image ID provided']);
    }

    $image_id = intval($_POST['image_id']);
    $image_url = wp_get_attachment_url($image_id);

    if ($image_url) {
        wp_send_json_success(['url' => $image_url]);
    } else {
        wp_send_json_error(['message' => 'Image not found']);
    }
}



//Phan quyen

function render_role_caps_page() {
    $roles = array(
        'giamdoc' => 'Giám Đốc',
        'quanlykinhdoanh' => 'Quản Lý Kinh Doanh',
        'nhanvienkinhdoanh' => 'Nhân Viên Kinh Doanh',
    );

    $capabilities_data = array(
        'limit_ip' => 'Giới Hạn IP nội bộ',
        'export_data' => 'Xuất File Dữ Liệu',
        'view_data' => 'Xem Dữ Liệu',
        'add_data' => 'Thêm Mới Dữ Liệu',
        'edit_data' => 'Sửa Dữ Liệu',
        'delete_data' => 'Xoá Dữ Liệu',
        'view_phone' => 'Xem Số Điện Thoại',
        'number_view_phone' => 'Lần xem DT trong ngày',
    );

    $capabilities_user = array(
        'export_user' => 'Xuất File Dữ Liệu',
        'view_user' => 'Xem Dữ Liệu',
        'add_user' => 'Thêm Mới Dữ Liệu',
        'edit_user' => 'Sửa Dữ Liệu',
        'delete_user' => 'Xoá Dữ Liệu',
    );

    $saved_caps = get_option('company_roles_caps', array());
    $saved_ips = get_option('company_allowed_ips', []);

    if (isset($_POST['submit_role_caps']) && check_admin_referer('save_role_caps_nonce')) {
        $new_caps = array();
        foreach ($roles as $role_key => $role_name) {
            foreach (array_merge($capabilities_data, $capabilities_user) as $cap_key => $cap_name) {
                if ($cap_key === 'number_view_phone') {
                    // Lưu giá trị số từ select
                    $new_caps[$role_key][$cap_key] = intval($_POST[$role_key . '_' . $cap_key] ?? 0);
                } else {
                    // Lưu checkbox như cũ
                    $new_caps[$role_key][$cap_key] = isset($_POST[$role_key . '_' . $cap_key]) ? true : false;
                }
            }
        }
        update_option('company_roles_caps', $new_caps);

        $ip_list_raw = sanitize_textarea_field($_POST['internal_ip_list'] ?? '');
        $ip_array = array_filter(array_map('trim', explode("\n", $ip_list_raw)));
        update_option('company_allowed_ips', $ip_array);

        echo '<div class="notice notice-success is-dismissible"><p>Đã lưu phân quyền và IP nội bộ!</p></div>';
        $saved_caps = $new_caps;
        $saved_ips = $ip_array;
    }


    ?>

    <style>
        .role-section {
            margin-bottom: 40px;
        }
        .role-caps-table-container {
            overflow-x: auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 24px;
            font-family: "Segoe UI", sans-serif;
        }
        .role-caps-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }
        .role-caps-table thead th {
            background: linear-gradient(to right, #673ab7, #4caf50);
            color: #fff;
            padding: 14px 16px;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
        }
        .role-caps-table thead th:first-child {
            text-align: left;
        }
        .role-caps-table tbody tr:hover {
            background-color: #f9f9f9;
        }
        .role-caps-table td {
            padding: 10px 14px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .role-caps-table td:first-child {
            text-align: left;
        }
        .role-caps-table input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4caf50;
        }
        .submit-btn {
            margin-top: 20px;
            padding: 12px 28px;
            background: #f8b400;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
        }
    </style>

    <div class="wrap">
        <h1 style="margin-bottom: 30px;">Quản lý phân quyền</h1>
        <form method="post">
            <?php wp_nonce_field('save_role_caps_nonce'); ?>

            <!-- SECTION 1: PHÂN QUYỀN DỮ LIỆU -->
            <div class="role-section">
                <h2 style="margin-bottom: 10px;">📁 Phân quyền DỮ LIỆU</h2>
                <div class="role-caps-table-container">
                    <table class="role-caps-table">
                        <thead>
                        <tr>
                            <th>Vị trí chức vụ</th>
                            <?php foreach ($capabilities_data as $cap_name): ?>
                                <th><?php echo esc_html($cap_name); ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($roles as $role_key => $role_name): ?>
                            <tr>
                                <td><?php echo esc_html($role_name); ?></td>
                                <?php foreach ($capabilities_data as $cap_key => $cap_name): ?>
                                    <td>
                                        <?php if ($cap_key === 'number_view_phone'): ?>
                                            <?php
                                            // Các giá trị tùy chọn
                                            $options = [5, 10, 15, 20, 25,30,35,40,50,100,100000];
                                            $selected_val = !empty($saved_caps[$role_key][$cap_key]) ? $saved_caps[$role_key][$cap_key] : '';
                                            ?>
                                            <select name="<?php echo esc_attr($role_key . '_' . $cap_key); ?>">
                                                <?php foreach ($options as $val): ?>
                                                    <option value="<?php echo $val; ?>" <?php selected($selected_val, $val); ?>>
                                                        <?php echo $val; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="checkbox"
                                                   name="<?php echo esc_attr($role_key . '_' . $cap_key); ?>"
                                                <?php if (!empty($saved_caps[$role_key][$cap_key])) echo 'checked'; ?>>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>

            <!-- SECTION 2: PHÂN QUYỀN NGƯỜI DÙNG -->
            <div class="role-section">
                <h2 style="margin-bottom: 10px;">👤 Phân quyền NGƯỜI DÙNG</h2>
                <div class="role-caps-table-container">
                    <table class="role-caps-table">
                        <thead>
                        <tr>
                            <th>Vị trí chức vụ</th>
                            <?php foreach ($capabilities_user as $cap_name): ?>
                                <th><?php echo esc_html($cap_name); ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($roles as $role_key => $role_name): ?>
                            <tr>
                                <td><?php echo esc_html($role_name); ?></td>
                                <?php foreach ($capabilities_user as $cap_key => $cap_name): ?>
                                    <td><input type="checkbox" name="<?php echo esc_attr($role_key . '_' . $cap_key); ?>" <?php if (!empty($saved_caps[$role_key][$cap_key])) echo 'checked'; ?>></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DANH SÁCH IP -->
            <div style="margin-bottom: 30px;">
                <h2>🌐 Giới hạn IP nội bộ</h2>
                <textarea name="internal_ip_list" rows="5" style="width: 100%; max-width: 600px;"><?php echo esc_textarea(implode("\n", $saved_ips)); ?></textarea>
            </div>

            <input type="submit" name="submit_role_caps" class="submit-btn" value="Lưu thay đổi">
        </form>
    </div>
    <?php
}



add_action('admin_init', 'spiritwebs_save_role_caps');
function spiritwebs_save_role_caps() {
    if (!current_user_can('manage_options')) return;
    if (!isset($_POST['submit_role_caps']) || !check_admin_referer('save_role_caps_nonce')) return;

    $capabilities = ['limit_ip', 'export_data', 'view_data', 'add_data', 'edit_data','delete_data', 'view_phone',
        'export_user', 'view_user', 'add_user', 'edit_user','delete_user'];
    $roles = ['admin', 'director', 'manager', 'sales'];
    // Role tiếng Việt
    /* $roles = [
         'giamdoc' => 'Giám Đốc',
         'quanlykinhdoanh' => 'Quản Lý Kinh Doanh',
         'nhanvienkinhdoanh' => 'Nhân Viên Kinh Doanh'
     ];*/

    $data = [];

    foreach ($roles as $role) {
        foreach ($capabilities as $cap) {
            $key = $role . '_' . $cap;
            $data[$role][$cap] = isset($_POST[$key]) ? 1 : 0;
        }
    }

    update_option('spiritwebs_role_caps', $data);
}


function spiritwebs_user_can($capability) {
    $user = wp_get_current_user();
    if (!$user || empty($user->roles)) return false;

    // Nếu user là administrator thì luôn trả về true
    if (in_array('administrator', $user->roles)) {
        return true;
    }

    $saved = get_option('company_roles_caps', []);

    foreach ($user->roles as $role) {
        if (!empty($saved[$role][$capability])) {
            return true;
        }
    }

    return false;
}

// phan nay lien quan den ip neu không co quyen truy cap xóa ham nay
/*add_action('init', function () {
    // Kiểm tra domain warehouse
    if (strpos($_SERVER['HTTP_HOST'], 'warehouse.urbanhome.vn') !== false) {

        // Lấy IP hiện tại
        $current_ip = $_SERVER['REMOTE_ADDR'];

        // Lấy danh sách IP được phép từ Option
        $allowed_ips = get_option('company_allowed_ips', []);
        error_log('Allowed IPs: ' . print_r($allowed_ips, true));

        // Nếu người dùng đã đăng nhập và có quyền 'limit_ip' → cho qua
        if (is_user_logged_in() && function_exists('spiritwebs_user_can') && spiritwebs_user_can('limit_ip')) {
            return; // Bỏ qua giới hạn IP nếu có quyền
        }

        // Nếu danh sách IP có phần tử và IP hiện tại không nằm trong danh sách → chặn truy cập
        if (!empty($allowed_ips) && !in_array($current_ip, $allowed_ips)) {
            wp_die(
                'Trang này chỉ dành cho IP nội bộ. Nếu bạn là nhân viên, vui lòng liên hệ quản trị viên để cấp quyền.',
                'Truy cập bị giới hạn', // Tiêu đề
                array('response' => 403) // HTTP status code
            );
        }
    }
});*/
add_action('admin_init', function () {
    //wp_die('<pre>' . print_r($_GET, true) . '</pre>');
    // Trang mục tiêu cần kiểm tra
    $target_page = 'wp_dulieunhadat';
    if (!isset($_GET['page']) || $_GET['page'] !== $target_page) {
        return; // Không phải trang mục tiêu
    }

    // Chưa đăng nhập -> về trang login
    if (!is_user_logged_in()) {
        auth_redirect();
    }

    // Thông tin user
    $user = wp_get_current_user();
    $role = $user->roles[0] ?? '';

    // Nếu là admin thật -> bỏ qua kiểm tra
    if (in_array('administrator', (array) $user->roles, true)) {
        return;
    }

    // Lấy quyền role từ option
    $caps = get_option('company_roles_caps', []);
    $allowed_ips = get_option('company_allowed_ips', []);

    // Nếu role không có trong config -> chặn
    if (!isset($caps[$role])) {
        wp_die('Vai trò của bạn không hợp lệ.', 'Truy cập bị giới hạn', ['response' => 403]);
    }

    // Nếu role bật limit_ip -> kiểm tra IP
    if (!empty($caps[$role]['limit_ip'])) {
        $user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        //wp_die('IP SERVER THẤY = ' . ($_SERVER['REMOTE_ADDR'] ?? 'NULL'));

        //wp_die('ROLE=' . $role . ' | IP=' . $user_ip);
        if (!in_array($user_ip, $allowed_ips, true)) {
            wp_die(
                'Trang này chỉ dành cho IP nội bộ. Nếu bạn là nhân viên, vui lòng liên hệ quản trị viên để cấp quyền.',
                'Truy cập bị giới hạn',
                ['response' => 403]
            );
        }
    }
});


//xuat csv
/*add_action('admin_init', 'spiritwebs_handle_csv_export');

function spiritwebs_handle_csv_export() {
    if (isset($_POST['spiritwebs_export_csv'])) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'dulieunhadat';

        // Khai báo các field cần xuất và tên hiển thị tương ứng
        $fields = [
            'id' => 'ID',
            'name' => 'Tên',
            'code_product' => 'Mã Căn',
            'giaban' => 'Giá Bán',
            'giathue' => 'Giá Thuê',
            'ghichu' => 'Ghi Chú',
            'datecreate' => 'Ngày Tạo',
            'userupdate' => 'Nhân Viên',
            'road' => 'Đường Xá',
            'dtd_congnhan' => 'Diện Tích Công Nhận',
            'sonha' => 'Số Nhà'
            // thêm field khác nếu cần
        ];

        $field_keys = array_keys($fields);
        $fields_sql = implode(',', array_map(function ($field) {
            return '`' . esc_sql($field) . '`';
        }, $field_keys));

        $results = $wpdb->get_results("SELECT $fields_sql FROM $table_name", ARRAY_A);

        if (!empty($results)) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=export_' . date('Ymd_His') . '.csv');

            $output = fopen('php://output', 'w');

            // Ghi BOM cho UTF-8
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Ghi dòng tiêu đề (label hiển thị)
            fputcsv($output, array_values($fields));

            // Ghi dữ liệu
            foreach ($results as $row) {
                $line = [];
                foreach ($field_keys as $field) {
                    $line[] = $row[$field] ?? '';
                }
                fputcsv($output, $line);
            }

            fclose($output);
            exit;
        } else {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-warning is-dismissible"><p>Không có dữ liệu để xuất.</p></div>';
            });
        }
    }
}*/

add_action('admin_init', 'spiritwebs_handle_csv_export');
function spiritwebs_clean_csv_text($text) {
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = strip_tags($text);                     // bỏ <img>, <br>...
    $text = preg_replace("/\r|\n/", ' ', $text);   // xoá xuống dòng
    $text = preg_replace('/\s+/', ' ', $text);     // gom khoảng trắng
    return trim($text);
}

// 🆕 Định dạng cột "Bổ Sung Thông Tin" từ JSON contact_info (name_more, phone_more, vaitro_more, gender_more)
function spiritwebs_format_contact_info_csv($contact_info_raw) {
    if (empty($contact_info_raw)) return '';

    $contacts = is_array($contact_info_raw) ? $contact_info_raw : json_decode($contact_info_raw, true);
    if (empty($contacts) || !is_array($contacts)) return '';

    $parts = [];
    foreach ($contacts as $c) {
        $name = trim($c['name_more'] ?? '');
        $phone = trim($c['phone_more'] ?? '');
        if ($name === '' && $phone === '') continue;

        $item = trim($name . ($phone !== '' ? ' - ' . $phone : ''));
        $parts[] = $item;
    }

    return spiritwebs_clean_csv_text(implode('; ', $parts));
}

function spiritwebs_handle_csv_export() {
    if (!isset($_POST['spiritwebs_export_csv'])) return;

    global $wpdb;

    $table = $wpdb->prefix . 'dulieunhadat';
    $table_luotxem = $wpdb->prefix . 'luot_xem_sdt';
    $table_users = $wpdb->users;
    $province = $wpdb->prefix . 'province';
    $district = $wpdb->prefix . 'district';
    $wards = $wpdb->prefix . 'wards';
    $term_taxonomy = $wpdb->prefix . 'term_taxonomy';
    $terms = $wpdb->prefix . 'terms';

    $sql = "
        SELECT 
            d.id AS stt,
            d.code_product,
            
            t_bds.name AS loai_bds,
            t_gd.name AS loai_gd,
            d.tenduan,

            CONCAT(d.code_product, ' - ', d.sonha) AS ma_can_sonha,
            d.road,

            w.name AS xa,
            dist.name AS quan,
            p.name AS tinh,

            CONCAT(d.dtd_ngang, ' x ', d.dtd_dai) AS dt_ngang_dai,
            d.dtd_congnhan,

            t_huong.name AS huong,
            d.ttk_sotang,
            d.thangmay,
            d.ttk_sotangham,

            d.name,
            d.dienthoaididong,

            t_ttgd.name AS tinhtrang_gd,
            COALESCE(d.dateupdate, d.datecreate) AS dateupdate,
            d.ghichu,
            d.contact_info,

            (
                SELECT COUNT(*) FROM $table_luotxem lx
                WHERE lx.nhadat_id = d.id AND lx.phone_status IS NULL
            ) AS luot_xem_sdt,

            (
                SELECT GROUP_CONCAT(CONCAT(u.display_name, ' (', cnt_tbl.cnt, ')') ORDER BY cnt_tbl.cnt DESC SEPARATOR ', ')
                FROM (
                    SELECT nhadat_id, nguoidung_id, COUNT(*) AS cnt
                    FROM $table_luotxem
                    WHERE phone_status IS NULL
                    GROUP BY nhadat_id, nguoidung_id
                ) cnt_tbl
                LEFT JOIN $table_users u ON u.ID = cnt_tbl.nguoidung_id
                WHERE cnt_tbl.nhadat_id = d.id
            ) AS nguoi_xem_sdt

        FROM $table d

        LEFT JOIN $province p ON p.id = d.wp_province
        LEFT JOIN $district dist ON dist.id = d.wp_district
        LEFT JOIN $wards w ON w.id = d.wp_wards

        LEFT JOIN $term_taxonomy tt_bds ON tt_bds.term_taxonomy_id = d.property_type
        LEFT JOIN $terms t_bds ON t_bds.term_id = tt_bds.term_id

        LEFT JOIN $term_taxonomy tt_gd ON tt_gd.term_taxonomy_id = d.transaction_type
        LEFT JOIN $terms t_gd ON t_gd.term_id = tt_gd.term_id

        LEFT JOIN $term_taxonomy tt_huong ON tt_huong.term_taxonomy_id = d.ttk_huong
        LEFT JOIN $terms t_huong ON t_huong.term_id = tt_huong.term_id

        LEFT JOIN $term_taxonomy tt_ttgd ON tt_ttgd.term_taxonomy_id = d.tinhtranggiaodich
        LEFT JOIN $terms t_ttgd ON t_ttgd.term_id = tt_ttgd.term_id

        ORDER BY d.id DESC
    ";

    $results = $wpdb->get_results($sql, ARRAY_A);

    if ($wpdb->last_error) {
        add_action('admin_notices', function () use ($wpdb) {
            echo '<div class="notice notice-error is-dismissible"><p>Lỗi xuất CSV: ' . esc_html($wpdb->last_error) . '</p></div>';
        });
        return;
    }

    if (empty($results)) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>Không có dữ liệu để xuất CSV.</p></div>';
        });
        return;
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=dulieunhadat_' . date('Ymd_His') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, [
        'STT',
        'Mã SP',
        'Loại BĐS',
        'Loại Giao Dịch',
        'Tên Dự Án',
        'Mã Căn - Số Nhà',
        'Đường',
        'Xã',
        'Quận',
        'Tỉnh',
        'DT Ngang Dài',
        'DT Công Nhận',
        'Hướng',
        'Tầng',
        'Thang Máy',
        'Hầm',
        'Tên',
        'Số Điện Thoại',
        'Tình Trạng Giao Dịch',
        'Ngày Update',
        'Ghi Chú',
        'Lượt Xem SĐT',
        'Người Xem SĐT',
        'Bổ Sung Thông Tin'
    ]);

    foreach ($results as $row) {
        fputcsv($output, [
            $row['stt'],
            $row['code_product'],
            $row['loai_bds'],
            $row['loai_gd'],
            $row['tenduan'],
            $row['ma_can_sonha'],
            $row['road'],
            $row['xa'],
            $row['quan'],
            $row['tinh'],
            $row['dt_ngang_dai'],
            $row['dtd_congnhan'],
            $row['huong'],
            $row['ttk_sotang'],
            $row['thangmay'],
            $row['ttk_sotangham'],
            $row['name'],
            $row['dienthoaididong'],
            $row['tinhtrang_gd'],
            $row['dateupdate'],
            spiritwebs_clean_csv_text($row['ghichu']), // ✅ FIX TẠI ĐÂY
            $row['luot_xem_sdt'],
            $row['nguoi_xem_sdt'],
            spiritwebs_format_contact_info_csv($row['contact_info']),
        ]);
    }

    fclose($output);
    exit;
}



// ẩn nút add user trong wodpress
add_action('admin_head-users.php', function () {
    // Kiểm tra quyền add_user (tùy bạn định nghĩa)
    if (function_exists('spiritwebs_user_can') && !spiritwebs_user_can('add_user')) {
        echo '<style>
            .wrap .page-title-action { display: none !important; }
        </style>';
    }
});

// chặng truy cập trang add user
add_action('admin_init', function () {
    if (is_admin() && isset($_GET['page']) === false && strpos($_SERVER['REQUEST_URI'], 'user-new.php') !== false) {
        if (function_exists('spiritwebs_user_can') && !spiritwebs_user_can('add_user')) {
            wp_die('Bạn không có quyền thêm thành viên mới.', 'Truy cập bị từ chối', array('response' => 403));
        }
    }
});

// ẩn nút xóa (Bulk Actions & Hành động nhanh) user trong wodpress
add_action('admin_head-users.php', function () {
    if (function_exists('spiritwebs_user_can') && !spiritwebs_user_can('delete_user')) {
        echo '<style>
            /* Ẩn lựa chọn Bulk Delete */
            select[name="action"] option[value="delete"],
            select[name="action2"] option[value="delete"] {
                display: none !important;
            }

            /* Ẩn checkbox chọn user */
            .users-php .check-column input[type="checkbox"] {
                display: none !important;
            }

            /* Ẩn hành động “Xóa” ở hành động nhanh (hover) */
            .row-actions .delete,
            .row-actions .submitdelete {
                display: none !important;
            }
        </style>';
    }
});

// ngăn không cho cố tình truy cập action xóa user
add_action('load-users.php', function () {
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        if (function_exists('spiritwebs_user_can') && !spiritwebs_user_can('delete_user')) {
            wp_die('Bạn không có quyền xóa người dùng.', 'Truy cập bị từ chối', ['response' => 403]);
        }
    }
});


// Ẩn menu cho người không có quyền
add_action('admin_menu', 'spiritwebs_custom_hide_menus', 999);
function spiritwebs_custom_hide_menus() {
    // Nếu không phải Administrator, ẩn Dashboard
    if (!current_user_can('administrator')) {
        remove_menu_page('index.php'); // Dashboard
    }

    // Nếu không phải Administrator hoặc Giám đốc, ẩn Media và Users
    if (!current_user_can('administrator') && !current_user_can('giamdoc')) {
        remove_menu_page('upload.php'); // Media
        remove_menu_page('users.php');  // Users
    }
}

// Chặn truy cập trực tiếp vào các trang admin nếu không đủ quyền
add_action('admin_init', 'spiritwebs_block_direct_admin_pages');
function spiritwebs_block_direct_admin_pages() {
    // Bỏ qua các request nội bộ
    if (defined('DOING_AJAX') && DOING_AJAX) return;
    if (defined('REST_REQUEST') && REST_REQUEST) return;

    global $pagenow;

    // Chặn hoàn toàn Dashboard nếu không phải admin
    if (!current_user_can('administrator') && $pagenow === 'index.php') {
        wp_safe_redirect(admin_url('profile.php')); // Hoặc home_url()
        exit;
    }

    // Chặn Media và Users nếu không phải admin hoặc giám đốc
    if (!current_user_can('administrator') && !current_user_can('giamdoc')) {
        $restricted_pages = ['upload.php', 'users.php'];

        if (in_array($pagenow, $restricted_pages)) {
            wp_safe_redirect(admin_url('profile.php')); // Hoặc home_url()
            exit;
        }
    }
}


// hide text in media-new.php
function remove_upload_notice_text_except_link() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const p = document.querySelector('p.upload-html-bypass.hide-if-no-js');
            if (p) {
                const aTag = p.querySelector('a');
                if (aTag) {
                    p.innerHTML = '';
                    p.appendChild(aTag);
                    // Nếu bạn muốn giữ dấu chấm cuối câu:
                    p.innerHTML += '.';
                }
            }
        });
    </script>
    <?php
}
add_action('admin_footer', 'remove_upload_notice_text_except_link');

// Handle hinh anh
add_action('wp_ajax_upload_watermarked_image', function () {
    if (empty($_FILES['file'])) {
        wp_send_json_error(['msg' => 'No file']);
    }

    $file = $_FILES['file'];
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php'); // Cần cho attachment

    $upload = wp_handle_upload($file, ['test_form' => false]);

    if (!empty($upload['url']) && !empty($upload['file'])) {
        // ✅ Tạo attachment
        $attachment = [
            'guid'           => $upload['url'],
            'post_mime_type' => $upload['type'],
            'post_title'     => sanitize_file_name($file['name']),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];

        $attach_id = wp_insert_attachment($attachment, $upload['file']);

        // ✅ Tạo metadata cho ảnh (bắt buộc)
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);

        wp_send_json_success([
            'url' => $upload['url'],
            'id'  => $attach_id // 👉 Gửi ID ảnh về client
        ]);
    }

    wp_send_json_error(['msg' => 'Upload failed']);
});



function get_image_urls_from_ids($id_string) {
    $ids = array_filter(array_map('intval', explode(',', $id_string)));
    $urls = [];

    foreach ($ids as $id) {
        $url = wp_get_attachment_url($id);
        if ($url) {
            $urls[] = $url;
        }
    }

    return $urls;
}

// lay du lieu tu bang dulieunhadat_goc
// Đăng ký AJAX handler cho user đã đăng nhập
add_action('wp_ajax_get_dulieunhadat_goc_by_id', 'get_dulieunhadat_goc_by_id_callback');
// Nếu muốn hỗ trợ cả user chưa đăng nhập, thêm:
// add_action('wp_ajax_nopriv_get_dulieunhadat_goc_by_id', 'get_dulieunhadat_goc_by_id_callback');
function get_dulieunhadat_goc_by_id_callback() {
    global $wpdb;

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        wp_send_json_error('ID không hợp lệ');
        wp_die();
    }

    $table = $wpdb->prefix . 'dulieunhadat_goc';
    $query = $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id);
    $data = $wpdb->get_row($query);

    if (empty($data)) {
        wp_send_json_error('Không tìm thấy dữ liệu');
        wp_die();
    }


    // Lấy user hiện tại và role
    $current_user = wp_get_current_user();
    $user_role = $current_user->roles[0] ?? '';

// Nếu là admin, luôn được xem
    if ($user_role === 'administrator') {
        $data->can_view_phone = true;
    } else {
        // Lấy giới hạn lượt xem từ option
        $role_caps = get_option('company_roles_caps', []);
        $limit_view = isset($role_caps[$user_role]['number_view_phone'])
            ? intval($role_caps[$user_role]['number_view_phone'])
            : 0;

        if ($limit_view > 0) {
            // Lấy ngày hôm nay theo múi giờ WordPress
            $today = current_time('Y-m-d');

            $table_view = $wpdb->prefix . 'luot_xem_sdt';

            // Đếm số lượt xem hôm nay
            $so_luot_xem = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*)  FROM {$table_view} WHERE nguoidung_id = %d AND DATE(thoi_gian) = %s",
                $current_user->ID,
                $today
            ));

            $data->can_view_phone = $so_luot_xem < $limit_view;
        } else {
            $data->can_view_phone = true; // không giới hạn
        }
    }







    // Xử lý contact_info nếu có
    if (!empty($data->contact_info)) {
        $contact_info = is_array($data->contact_info)
            ? $data->contact_info
            : json_decode($data->contact_info, true);

        $propertyTypeCache = [];

        foreach ($contact_info as &$contact) {
            if (isset($contact['vaitro_more'])) {
                $term_id = intval($contact['vaitro_more']);

                if (!isset($propertyTypeCache[$term_id])) {
                    $term = get_term($term_id, 'category'); // Đổi 'category' nếu taxonomy khác
                    $propertyTypeCache[$term_id] = (!is_wp_error($term) && !empty($term)) ? $term->name : '';
                }

                $contact['vaitro_more_name'] = $propertyTypeCache[$term_id];
            } else {
                $contact['vaitro_more_name'] = null;
            }
        }

        $data->contact_info = $contact_info;
    }


    // ======= LẤY USER INFO =======
    if (!empty($data->user)) {
        $user_id = intval($data->user);
        $data->user_first_name = get_user_meta($user_id, 'first_name', true);
        $data->user_last_name  = get_user_meta($user_id, 'last_name', true);
        $data->user_phone      = get_user_meta($user_id, 'dien_thoai', true);
        $avatar_meta = get_user_meta($user_id, 'simple_local_avatar', true);

        if (!empty($avatar_meta) && !empty($avatar_meta['full'])) {
            $data->user_avatar_url = $avatar_meta['full'];
        } else {
            $data->user_avatar_url = get_avatar_url($user_id, ['size' => 96]);
        }
    }
//contact from detail for location declare function
    // ======= CÁC TRƯỜNG DẠNG CATEGORY / TAXONOMY =======
    $category_fields = [
        'loaitaisan',
        'product_type',
        'transaction_type',
        'property_type',
        'tinhtranggiaodich',
        'hientrangsanpham',
        'sanphamhot',
        'vaitro',
        'ttk_huong',
        'ttk_loaidat',
        'gioitinh',
        'loaihoahong',
        'ttk_sophongngu',
        'ttk_sotang',
        'ttk_sotangham',
        'ttk_swc',
        'donvi_giaban',
        'donvi_giathue',
        'donvi_thoigiangia',
        'donvi_thoigianthue',
        'vitri',
        'donvigiathue',
    ];

    foreach ($category_fields as $field) {
        if (!empty($data->$field)) {
            $term = get_term($data->$field);
            $data->{$field . '_name'} = $term && !is_wp_error($term) ? $term->name : '';
        }
    }

    // ======= CÁC TRƯỜNG LẤY TỪ BẢNG TỈNH/QUẬN/XÃ =======
    $location_fields = [
        'wp_province' => 'province',
        'wp_district' => 'district',
        'wp_wards'    => 'wards',
    ];

    foreach ($location_fields as $field => $table_suffix) {
        if (!empty($data->$field)) {
            $location_table = $wpdb->prefix . $table_suffix;
            $location_name = $wpdb->get_var($wpdb->prepare(
                "SELECT name FROM {$location_table} WHERE id = %d",
                $data->$field
            ));
            $data->{$field . '_name'} = $location_name ?: '';
        }
    }

    // Lấy đường dẫn ảnh từ sohong_image_id và bosung_image_id nếu có
    $image_fields = ['sohong_image_id', 'bosung_image_id'];

    foreach ($image_fields as $img_field) {
        if (!empty($data->$img_field)) {
            $attachment_id = intval($data->$img_field);
            $image_url = wp_get_attachment_url($attachment_id);
            $data->{$img_field . '_url'} = $image_url ? $image_url : '';
        } else {
            $data->{$img_field . '_url'} = '';
        }
    }

    // Lấy danh sách ảnh sổ hồng (đa ảnh)
    $data->sohong_image_multi_urls = !empty($data->sohong_image_multi)
        ? get_image_urls_from_ids($data->sohong_image_multi)
        : [];


// Lấy dữ liệu lịch sử liên quan đến $id
    $history_table = $wpdb->prefix . 'dulieunhadat_history'; // tên bảng lịch sử
    $history_records = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$history_table} WHERE record_id = %d ORDER BY dateupdate DESC", $id)
    );

    foreach ($history_records as &$record) {
        // Xử lý dữ liệu trong trường 'data'
        if (!empty($record->data)) {
            $record->data = maybe_unserialize($record->data);
            // Lấy danh sách ảnh sổ hồng (đa ảnh) từ lịch sử
            $record->data['sohong_image_multi_urls'] = !empty($record->data['sohong_image_multi'])
                ? get_image_urls_from_ids($record->data['sohong_image_multi'])
                : [];

            if (is_object($record->data)) {
                $record->data = (array) $record->data;
            }

        } else {
            $record->data = [];
        }

        // Xử lý trường 'changed_fields' thành mảng
        if (!empty($record->changed_fields)) {
            $record->changed_fields = array_map('trim', explode(',', $record->changed_fields));
        } else {
            $record->changed_fields = [];
        }

        // Các trường taxonomy dạng term (ngoại trừ 'name_area')
        $term_fields = [
            'product_type',
            'transaction_type',
            'property_type',
            'donvi_giaban',
            'hoahong_tyle',
            'tinhtranggiaodich',
            'gioitinh',
            'ttk_huong',
            'ttk_loaidat',
            'sanphamhot',
            'thangmay',
            'vaitro',
            'loaihoahong',
            'donvi_thoigiangia',
            'donvi_thoigianthue',
            'donvi_giathue',
            'vitri',
            'loaitaisan',
            // 'name_area' không đưa vào đây
        ];

        foreach ($term_fields as $field) {
            if (!empty($record->data[$field])) {
                $term_id = intval($record->data[$field]);
                $term = get_term($term_id);
                if ($term && !is_wp_error($term)) {
                    $record->data[$field . '_text'] = $term->name;
                } else {
                    $record->data[$field . '_text'] = '—';
                }
            } else {
                $record->data[$field . '_text'] = '—';
            }
        }

        // Xử lý trường 'name_area' lấy từ bảng wp_duan
        if (!empty($record->data['name_area'])) {
            $name_area_id = intval($record->data['name_area']);
            $name_area_name = $wpdb->get_var(
                $wpdb->prepare("SELECT TenLoai FROM {$wpdb->prefix}duan WHERE id = %d", $name_area_id)
            );
            $record->data['name_area_text'] = $name_area_name ?: '—';
        } else {
            $record->data['name_area_text'] = '—';
        }

        // Xử lý địa lý: tỉnh, huyện, xã
        $geo_fields = [
            'wp_province' => $wpdb->prefix . 'province',
            'wp_district' => $wpdb->prefix . 'district',
            'wp_wards'   => $wpdb->prefix . 'wards',
        ];

        foreach ($geo_fields as $field => $table) {
            if (!empty($record->data[$field])) {
                $term_id = intval($record->data[$field]);
                $name = $wpdb->get_var(
                    $wpdb->prepare("SELECT name FROM {$table} WHERE id = %d", $term_id)
                );
                $record->data[$field . '_text'] = $name ?: '—';
            } else {
                $record->data[$field . '_text'] = '—';
            }
        }
        if (isset($record->data['contact_info'])) {
            // unset($record->data['contact_info']);
        }
    }

    $data->history = $history_records ?: [];



    wp_send_json_success($data);
    wp_die();
}


//tin noi bo


function my_custom_page_content_wp_tin_noi_bo() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tin_noi_bo';

    // Biến mặc định để lưu ID tin đang sửa
    $editing_id = 0;
    $edit_item = null;

    // Xử lý xóa tin
    if (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $wpdb->delete($table_name, ['id' => $delete_id]);
        echo '<div class="notice notice-success"><p>✅ Đã xóa tin #' . $delete_id . ' thành công!</p></div>';
    }

    // Xử lý chỉnh sửa: lấy dữ liệu tin cần sửa
    if (isset($_GET['edit_id'])) {
        $editing_id = intval($_GET['edit_id']);
        $edit_item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $editing_id));
    }

    // Xử lý lưu form (thêm mới hoặc cập nhật)
    if (isset($_POST['save_tin'])) {
        $tieu_de = sanitize_text_field($_POST['tieu_de']);
        $tom_tat = sanitize_textarea_field($_POST['tom_tat']);
        $edit_id = intval($_POST['edit_id']);
        $anh_url = '';

        // Nếu đang sửa, lấy ảnh hiện tại để dùng nếu không upload ảnh mới
        if ($edit_id > 0) {
            $current_item = $wpdb->get_row($wpdb->prepare("SELECT anh_url FROM $table_name WHERE id = %d", $edit_id));
            if ($current_item) {
                $anh_url = $current_item->anh_url;
            }
        }

        // Xử lý upload ảnh đính kèm (nếu có file mới)
        if (!empty($_FILES['anh']['name'])) {
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            $uploadedfile = $_FILES['anh'];
            $upload_overrides = ['test_form' => false];
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                $anh_url = esc_url($movefile['url']);
            } else {
                echo '<div class="notice notice-error"><p>Lỗi upload ảnh: ' . esc_html($movefile['error']) . '</p></div>';
            }
        }

        if ($edit_id > 0) {
            // Cập nhật tin
            $data_update = [
                'tieu_de' => $tieu_de,
                'tom_tat' => $tom_tat,
                'anh_url' => $anh_url
            ];
            $wpdb->update($table_name, $data_update, ['id' => $edit_id]);
            echo '<div class="notice notice-success"><p>✅ Đã cập nhật tin #' . $edit_id . ' thành công!</p></div>';
            $editing_id = 0; // reset trạng thái sửa
            $edit_item = null;
        } else {
            // Thêm tin mới
            $wpdb->insert($table_name, [
                'tieu_de' => $tieu_de,
                'tom_tat' => $tom_tat,
                'anh_url' => $anh_url
            ]);
            echo '<div class="notice notice-success"><p>✅ Đã lưu tin thành công!</p></div>';
        }
    }

    // Lấy danh sách tin hiện có
    $items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");

    // Dữ liệu điền vào form khi sửa
    $form_tieu_de = $editing_id && $edit_item ? esc_attr($edit_item->tieu_de) : '';
    $form_tom_tat = $editing_id && $edit_item ? esc_textarea($edit_item->tom_tat) : '';
    $form_anh_url = $editing_id && $edit_item ? esc_url($edit_item->anh_url) : '';
    ?>
    <style>
        /* CSS giống như phần trước */
        .wrap h1 {
            font-size: 28px;
            margin-bottom: 25px;
            color: #23282d;
            font-weight: 700;
        }
        form {
            max-width: 100%;
            background: #fff;
            padding: 25px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-bottom: 40px;
        }
        table.form-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 20px;
        }
        table.form-table th {
            text-align: left;
            vertical-align: top;
            padding-right: 20px;
            font-weight: 600;
            color: #555;
            width: 130px;
            font-size: 16px;
        }
        table.form-table td {
            vertical-align: top;
        }
        input.regular-text, textarea.large-text {
            width: 100%;
            padding: 10px 15px;
            font-size: 16px;
            border: 1.8px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }
        input.regular-text:focus, textarea.large-text:focus {
            border-color: #0073aa;
            outline: none;
        }
        input[type="file"] {
            font-size: 15px;
            padding: 5px 0;
        }
        .notice-success {
            background: #dff0d8;
            border-left: 5px solid #3c763d;
            color: #3c763d;
            padding: 10px 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            font-size: 16px;
        }
        .notice-error {
            background: #f2dede;
            border-left: 5px solid #a94442;
            color: #a94442;
            padding: 10px 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            font-size: 16px;
        }
        .submit .button-primary {
            background: #0073aa;
            border-color: #006799;
            box-shadow: none;
            text-shadow: none;
            padding: 10px 28px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit .button-primary:hover {
            background: #005177;
            border-color: #004165;
        }
        /* Bảng danh sách tin */
        table.list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table.list-table th, table.list-table td {
            border: 1px solid #ddd;
            padding: 10px 15px;
            text-align: left;
            vertical-align: middle;
        }
        table.list-table th {
            background-color: #f7f7f7;
            font-weight: 600;
        }
        table.list-table tr:hover {
            background-color: #f1f1f1;
        }
        .list-table img {
            max-width: 120px;
            height: auto;
            border-radius: 4px;
        }
        .action-links a {
            margin-right: 10px;
            color: #0073aa;
            text-decoration: none;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        /* Ảnh hiện tại khi sửa */
        .current-image {
            margin-top: 10px;
        }
        .current-image img {
            max-width: 200px;
            height: auto;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
    </style>

    <div class="wrap">
        <!-- <h1><?php /*echo $editing_id ? 'Chỉnh sửa tin nội bộ #' . $editing_id : 'Thêm tin nội bộ'; */?></h1>-->
        <h1 style="padding:10px 0px 10px 0px;">
            <span class="dashicons dashicons-email-alt" style="font-size: 24px; vertical-align: middle; margin-right: 8px; padding:0px 0px 10px 0px"></span>
            <span style="color: #4CAF50; font-weight:bold;">Tin</span>
            <span style="color: #FFA500;  font-weight:bold;">Nội Bộ</span>
        </h1>
        <?php
        $current_user = wp_get_current_user();
        $allowed_roles = ['administrator', 'giamdoc']; // liệt kê các role được phép

        if (array_intersect($allowed_roles, $current_user->roles)) :
            ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo esc_attr($editing_id); ?>">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="tieu_de">Tiêu đề</label></th>
                        <td><input type="text" name="tieu_de" id="tieu_de" class="regular-text" required value="<?php echo esc_attr($form_tieu_de); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tom_tat">Tóm tắt</label></th>
                        <td><textarea name="tom_tat" id="tom_tat" rows="5" class="large-text"><?php echo esc_textarea($form_tom_tat); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="anh">Ảnh đính kèm</label></th>
                        <td>
                            <input type="file" name="anh" id="anh">
                            <?php if ($form_anh_url): ?>
                                <div class="current-image">
                                    <p>Ảnh hiện tại:</p>
                                    <img src="<?php echo esc_url($form_anh_url); ?>" alt="Ảnh tin nội bộ">
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button($editing_id ? 'Cập nhật tin' : 'Lưu tin', 'primary', 'save_tin'); ?>
            </form>
        <?php endif; ?>


        <h2>Danh sách tin nội bộ</h2>
        <?php if (count($items) > 0): ?>
            <table class="list-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Tóm tắt</th>
                    <th>Ảnh</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item->id; ?></td>
                        <td><?php echo esc_html($item->tieu_de); ?></td>
                        <td><?php echo nl2br(esc_html($item->tom_tat)); ?></td>
                        <td>
                            <?php
                            $file_url = $item->anh_url;
                            $file_ext = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));
                            $is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            ?>

                            <?php if ($file_url): ?>
                                <?php if ($is_image): ?>
                                    <a href="<?php echo esc_url($file_url); ?>" data-lightbox="image-<?php echo $item->id; ?>">
                                        <img src="<?php echo esc_url($file_url); ?>" alt="" style="max-width: 100px;" />
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo esc_url($file_url); ?>" download style="color: #28a745; text-decoration: underline;">
                                        Tải file về
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="/path/to/sample.jpg" data-lightbox="image-default-<?php echo $item->id; ?>" style="color: #007bff; text-decoration: underline;">
                                    Click để xem ảnh mẫu
                                </a>
                            <?php endif; ?>
                        </td>


                        <td class="action-links">
                            <?php if (function_exists('spiritwebs_user_can') && spiritwebs_user_can('edit_data')) : ?>
                                <a href="<?php echo admin_url('admin.php?page=tin_noi_bo_page&edit_id=' . $item->id); ?>">Sửa</a>
                            <?php endif; ?>
                            <?php if (function_exists('spiritwebs_user_can') && spiritwebs_user_can('delete_data')) : ?>
                            <a href="<?php echo admin_url('admin.php?page=tin_noi_bo_page&delete_id=' . $item->id); ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa tin này?');" style="color:#a00;">Xóa</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Chưa có tin nội bộ nào.</p>
        <?php endif; ?>
    </div>
    <?php
}

// luu luot xem so dien thoai
function luu_luot_xem_sdt($nhadat_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'luot_xem_sdt';
    $nguoidung_id = get_current_user_id();
    $ip = $_SERVER['REMOTE_ADDR'];

    $wpdb->insert($table, [
        'nguoidung_id' => $nguoidung_id,
        'nhadat_id'    => $nhadat_id,
        'ip'           => $ip,
        'thoi_gian'    => current_time('mysql'),
    ]);
}
add_action('wp_ajax_luu_luot_xem_sdt', 'ajax_luu_luot_xem_sdt');
add_action('wp_ajax_nopriv_luu_luot_xem_sdt', 'ajax_luu_luot_xem_sdt');

function ajax_luu_luot_xem_sdt() {
    if (isset($_POST['nhadat_id'])) {
        luu_luot_xem_sdt(intval($_POST['nhadat_id']));
    }
    wp_die(); // Kết thúc Ajax
}
// hide rating trong profile
// Ẩn phần "Rating" trong profile người dùng (Simple Local Avatars)
add_action('admin_head', function () {
    echo '<style>
        tr.ratings-row { display: none !important; }
    </style>';
});


// chuyên delete do thung rac
add_action('wp_ajax_soft_delete_jqgrid_data', 'soft_delete_jqgrid_data_callback');

function soft_delete_jqgrid_data_callback() {
    global $wpdb;

    $table = sanitize_text_field($_POST['table']);
    $id = intval($_POST['id']);

    if (empty($table) || empty($id)) {
        wp_send_json_error("Thiếu dữ liệu");
    }

    // Kiểm tra bảng hợp lệ nếu cần (bảo mật)

    $result = $wpdb->update(
        $table,
        ['deleted' => 1],
        ['id' => $id],
        ['%d'],
        ['%d']
    );

    if ($result !== false) {
        wp_send_json_success("Đã chuyển vào thùng rác");
    } else {
        wp_send_json_error("Xóa thất bại");
    }
}

// phuc hoi lai du lieu da xoa
add_action('wp_ajax_restore_jqgrid_data', 'restore_jqgrid_data_callback');
function restore_jqgrid_data_callback() {
    if ( !isset($_POST['table']) || !isset($_POST['id']) ) {
        wp_send_json_error("Thiếu thông tin");
        return;
    }

    global $wpdb;
    $table = sanitize_text_field($_POST['table']);
    $id = intval($_POST['id']);

    $table = strpos($table, $wpdb->prefix) === 0 ? $table : $wpdb->prefix . $table;

    // Khôi phục = set deleted = 0
    $result = $wpdb->update($table, ['deleted' => 0], ['id' => $id]);

    if ($result !== false) {
        wp_send_json_success("Khôi phục thành công");
    } else {
        wp_send_json_error("Không thể khôi phục bản ghi");
    }
}

// Ẩn menu WPForms và WP Mail SMTP trong admin
function custom_remove_admin_menus() {
    // Kiểm tra quyền, chỉ ẩn với non-admin hoặc theo nhu cầu
    if (!current_user_can('manage_options')) return;

    remove_menu_page('wpforms-overview'); // WPForms
    remove_menu_page('wp-mail-smtp');     // WP Mail SMTP
}
add_action('admin_menu', 'custom_remove_admin_menus', 999);
function hide_wpforms_admin_bar_icon() {
    echo '<style>
        #wp-admin-bar-wpforms-menu { display: none !important; }
    </style>';
}
add_action('admin_head', 'hide_wpforms_admin_bar_icon');
add_action('wp_head', 'hide_wpforms_admin_bar_icon'); // nếu muốn ẩn ở cả front-end


add_action('wp_ajax_get_properties_by_phone', 'get_properties_by_phone');
add_action('wp_ajax_nopriv_get_properties_by_phone', 'get_properties_by_phone');

function get_properties_by_phone() {
    global $wpdb;

    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

    if (!$phone) {
        wp_send_json_error('Thiếu số điện thoại');
    }

    $table = $wpdb->prefix . 'dulieunhadat';

    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table WHERE dienthoaididong = %s LIMIT 10", $phone)
    );

    if (!$results) {
        wp_send_json_success([]);
    }

    $data = [];

    foreach ($results as $item) {
        // Lấy tên term từ ID
        $transaction_term = get_term($item->transaction_type, 'category');
        $property_term    = get_term($item->property_type, 'category');

        $transaction_type_name = $transaction_term && !is_wp_error($transaction_term) ? $transaction_term->name : '';
        $property_type_name    = $property_term && !is_wp_error($property_term) ? $property_term->name : '';


        // Lấy địa danh từ bảng riêng
        $province_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_province WHERE id = %d", $item->wp_province));
        $district_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_district WHERE id = %d", $item->wp_district));
        $ward_name     = $wpdb->get_var($wpdb->prepare("SELECT name FROM wp_wards WHERE id = %d", $item->wp_wards));

        $data[] = [
            'id'                => $item->id,
            'post_id'           => $item->post_id,
            'khuvuc'            => $item->khuvuc,
            'transaction_type'  => $transaction_type_name,
            'property_type'     => $property_type_name,
            'province'          => $province_name,
            'district'          => $district_name,
            'ward'              => $ward_name,
        ];
    }

    wp_send_json_success($data);
}



add_action('wp_ajax_save_phone_status', 'handle_save_phone_status');
add_action('wp_ajax_nopriv_save_phone_status', 'handle_save_phone_status'); // nếu cho user chưa đăng nhập cũng được

function handle_save_phone_status() {
    global $wpdb;

    // Kiểm tra và lấy dữ liệu POST
    $nhadat_id = isset($_POST['nhadat_id']) ? intval($_POST['nhadat_id']) : 0;
    // ⚠️ KHÔNG dùng nguoidung_id do client gửi lên (dễ bị sai/cũ) — luôn lấy trực tiếp
    // từ phiên đăng nhập hiện tại trên server để đảm bảo report đúng người thao tác.
    $nguoidung_id = get_current_user_id();
    $phone_status = isset($_POST['phonestatus']) ? intval($_POST['phonestatus']) : 0;
    $note = isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '';
    $ip = $_SERVER['REMOTE_ADDR'];
    $thoi_gian = current_time('mysql');

    if ($nhadat_id && $nguoidung_id && $phone_status) {
        $table = $wpdb->prefix . 'luot_xem_sdt';

        $data = [
            'nhadat_id' => $nhadat_id,
            'nguoidung_id' => $nguoidung_id,
            'phone_status' => $phone_status,
            'note' => $note,
            'ip' => $ip,
            'thoi_gian' => $thoi_gian,
        ];

        $format = ['%d', '%d', '%d', '%s', '%s', '%s'];

        $inserted = $wpdb->insert($table, $data, $format);

        if ($inserted) {
            wp_send_json_success(['message' => 'Cập nhật thành công!']);
        } else {
            wp_send_json_error(['message' => 'Lỗi khi lưu dữ liệu!']);
        }
    } else {
        wp_send_json_error(['message' => 'Thiếu dữ liệu bắt buộc!']);
    }

    wp_die();
}




add_action('wp_ajax_get_report_html', 'get_report_html_by_nhadat');
function get_report_html_by_nhadat() {
    global $wpdb;

    $nhadat_id = isset($_POST['nhadat_id']) ? intval($_POST['nhadat_id']) : 0;
    $table = $wpdb->prefix . 'luot_xem_sdt';

    // Truy vấn có thêm tổng lượt xem số (có phone_status)
    $data = $wpdb->get_row("
        SELECT 
            COUNT(*) AS tong,
            SUM(CASE WHEN phone_status = 1 THEN 1 ELSE 0 END) AS dung,
            SUM(CASE WHEN phone_status = 2 THEN 1 ELSE 0 END) AS sai,
            SUM(CASE WHEN phone_status = 3 THEN 1 ELSE 0 END) AS khong,
            SUM(CASE WHEN phone_status = 4 THEN 1 ELSE 0 END) AS moi
        FROM $table
        WHERE nhadat_id = $nhadat_id
          AND phone_status IS NOT NULL
    ");
    $data2 = $wpdb->get_row("
        SELECT 
            COUNT(*) AS tong,
            SUM(CASE WHEN phone_status = 1 THEN 1 ELSE 0 END) AS dung,
            SUM(CASE WHEN phone_status = 2 THEN 1 ELSE 0 END) AS sai,
            SUM(CASE WHEN phone_status = 3 THEN 1 ELSE 0 END) AS khong,
            SUM(CASE WHEN phone_status = 4 THEN 1 ELSE 0 END) AS moi
        FROM $table
        WHERE nhadat_id = $nhadat_id
          AND phone_status IS NULL
    ");

    // 🆕 Lấy chi tiết từng report (ai report + ghi chú kèm theo) để hiện ngay trong từng khối
    $detailRows = $wpdb->get_results($wpdb->prepare("
        SELECT nguoidung_id, phone_status, note, thoi_gian
        FROM $table
        WHERE nhadat_id = %d
          AND phone_status IS NOT NULL
        ORDER BY thoi_gian DESC
    ", $nhadat_id));

    // Gom nhóm chi tiết theo từng loại phone_status
    $grouped = [1 => [], 2 => [], 3 => [], 4 => []];
    foreach ($detailRows as $row) {
        if (!isset($grouped[$row->phone_status])) continue;
        $user = get_userdata($row->nguoidung_id);
        $grouped[$row->phone_status][] = [
            'user'      => $user ? $user->display_name : 'Không rõ',
            'note'      => trim((string) $row->note),
            'thoi_gian' => $row->thoi_gian,
        ];
    }

    // Render danh sách nhỏ (tên người report + ghi chú) cho 1 loại report
    $render_report_detail_list = function ($items) {
        if (empty($items)) return '';
        $html = '<ul style="margin:8px 0 0; padding-left:18px; font-size:12px; color:#555; font-weight:normal; list-style:disc;">';
        foreach ($items as $item) {
            $time = mysql2date('d/m/Y H:i', $item['thoi_gian']);
            $noteHtml = $item['note'] !== ''
                ? ' - "' . esc_html($item['note']) . '"'
                : ' <i style="color:#999;">(không có ghi chú)</i>';
            $html .= '<li style="margin-bottom:4px;">'
                . '<b>' . esc_html($item['user']) . '</b>' . $noteHtml
                . ' <span style="color:#999;">(' . esc_html($time) . ')</span>'
                . '</li>';
        }
        $html .= '</ul>';
        return $html;
    };


    ob_start(); ?>
    <div style="border:1px solid #ddd; border-radius:8px; padding:15px; background:#f9f9f9;">
        <h2 style="margin:0 0 15px; font-size:18px; color:#2c3e50;">Report:</h2>



        <div style="display:flex; flex-direction:column; gap:10px;">
            <div style="background:#eafaf1; border-radius:12px; padding:10px;">
                <div style="display:flex; gap:10px; align-items:center;">
                    <i class="fas fa-check-circle" style="color:#27ae60; font-size:20px;"></i>
                    <div style="color:#27ae60; font-weight:bold;">
                        <?= intval($data->dung) ?> <span style="font-size:12px;">Report</span><br>
                        <span style="color:#333; font-weight:normal;">Đúng Thông Tin</span>
                    </div>
                </div>
                <?= $render_report_detail_list($grouped[1]) ?>
            </div>
            <div style="background:#fdecea; border-radius:12px; padding:10px;">
                <div style="display:flex; gap:10px; align-items:center;">
                    <i class="fas fa-times-circle" style="color:#e74c3c; font-size:20px;"></i>
                    <div style="color:#e74c3c; font-weight:bold;">
                        <?= intval($data->sai) ?> <span style="font-size:12px;">Report</span><br>
                        <span style="color:#333; font-weight:normal;">Sai Thông Tin</span>
                    </div>
                </div>
                <?= $render_report_detail_list($grouped[2]) ?>
            </div>
            <div style="background:#fff8e1; border-radius:12px; padding:10px;">
                <div style="display:flex; gap:10px; align-items:center;">
                    <i class="fas fa-phone-slash" style="color:#f39c12; font-size:20px;"></i>
                    <div style="color:#f39c12; font-weight:bold;">
                        <?= intval($data->khong) ?> <span style="font-size:12px;">Report</span><br>
                        <span style="color:#333; font-weight:normal;">Không Liên Lạc Được</span>
                    </div>
                </div>
                <?= $render_report_detail_list($grouped[3]) ?>
            </div>
            <div style="background:#f3eafc; border-radius:12px; padding:10px;">
                <div style="display:flex; gap:10px; align-items:center;">
                    <i class="fas fa-user-tie" style="color:#8e44ad; font-size:20px;"></i>
                    <div style="color:#8e44ad; font-weight:bold;">
                        <?= intval($data->moi) ?> <span style="font-size:12px;">Report</span><br>
                        <span style="color:#333; font-weight:normal;">Số Môi Giới</span>
                    </div>
                </div>
                <?= $render_report_detail_list($grouped[4]) ?>
            </div>
        </div>

    </div>

    <span style="font-weight:bold; display:block; margin-top:30px;">
            Bao gồm <?= intval($data2->tong) ?> lượt xem số
        </span>

    <?php

    wp_send_json_success([
        'html' => ob_get_clean()
    ]);
}

add_action('wp_ajax_get_phone_comments', 'get_phone_comments_by_nhadat');

function get_phone_comments_by_nhadat() {
    global $wpdb;

    $nhadat_id = isset($_POST['nhadat_id']) ? intval($_POST['nhadat_id']) : 0;
    $show_all = isset($_POST['show_all']) ? intval($_POST['show_all']) : 0;

    if (!$nhadat_id) {
        wp_send_json_error(['message' => 'Thiếu nhadat_id']);
    }

    $table = $wpdb->prefix . 'luot_xem_sdt';

    $where = $wpdb->prepare("WHERE nhadat_id = %d", $nhadat_id);
    if (!$show_all) {
        $where .= " AND phone_status IS NOT NULL";
    }

    $rows = $wpdb->get_results("
        SELECT 
            nguoidung_id,
            phone_status,
            note,
            thoi_gian
        FROM $table
        $where
        ORDER BY thoi_gian DESC
        LIMIT 50
    ");

    $results = [];

    foreach ($rows as $row) {
        $user_info = get_userdata($row->nguoidung_id);
        if (!$user_info) continue;

        $time_diff = human_time_diff(strtotime($row->thoi_gian), current_time('timestamp'));
        $time_label = $time_diff ? "$time_diff trước" : "Vừa xong";

        $results[] = [
            'user_id'      => $row->nguoidung_id,
            'user_name'    => $user_info->display_name,
            'user_code'    => 'NV'.get_current_user_id(),
            'avatar'       => get_avatar_url($row->nguoidung_id, ['size' => 96]) ?: 'https://app.urbanhome.vn/refer/ast/img/Avatar/men.png',
            'note'         => $row->note,
            'phone_status' => is_null($row->phone_status) ? null : intval($row->phone_status),
            'time_label'   => $time_label
        ];
    }

    wp_send_json_success($results);
}
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'wp-mail-smtp-menu' );
}, 999 );

// an plugin WP Hide & Security Enhancer - Rewrites sidebar
add_action('admin_menu', function () {
    remove_menu_page('wp-hide');
}, 999);
add_action('wp_dashboard_setup', function () {
    remove_meta_box('wp-hide-overview', 'dashboard', 'normal');
});

add_action('admin_head', function () {
    echo '<style>#wp-hide-overview { display: none !important; }</style>';
});

// xoa footer
add_action('admin_init', function () {
    ob_start(function ($buffer) {
        // Loại bỏ toàn bộ đoạn wpfooter
        return preg_replace('/<div id="wpfooter".*?<\/div>\s*/is', '', $buffer);
    });
});
// xoa tat ca acac tu wordpress
add_action('admin_init', function () {
    ob_start(function ($buffer) {
        // Xóa chữ WordPress (không phân biệt hoa thường)
        $buffer = str_ireplace('WordPress', '', $buffer);
        // Xóa đường link wordpress.org
        $buffer = preg_replace('/https?:\/\/[^\s"]*wordpress\.org[^\s"]*/i', '', $buffer);
        return $buffer;
    });
});

// 1. Ghi lại thời gian hoạt động
add_action('init', function () {
    if (is_user_logged_in()) {
        update_user_meta(get_current_user_id(), 'last_seen', time());
    }
});

// 2. Hàm lấy user online trong X giây gần nhất
function get_online_users($seconds = 300) {
    $online_users = [];
    $now = time();
    $users = get_users();

    foreach ($users as $user) {
        $last_seen = get_user_meta($user->ID, 'last_seen', true);
        if ($last_seen && ($now - $last_seen) <= $seconds) {
            $user->online_minutes = floor(($now - $last_seen) / 60); // thời gian online
            $online_users[] = $user;
        }
    }

    return $online_users;
}

// 3. Thêm vào admin bar
/*add_action('admin_bar_menu', function ($wp_admin_bar) {
    $userRole = wp_get_current_user();
    if (!in_array('administrator', (array) $userRole->roles)) {
        return; // không phải admin thì return
    }
    $online_users = get_online_users();
    $count = count($online_users);

    // Thêm menu chính
    // ✅ Bằng đoạn này:
    $wp_admin_bar->add_node([
        'id' => 'online-users',
        'title' => '👥 Online: ' . $count,
        'href' => false,
        'meta' => [
            'class' => 'show-on-mobile' // <- thêm class này để target bằng CSS
        ]
    ]);

    // Thêm submenu từng user với thời gian online
    foreach ($online_users as $user) {
        $minutes = $user->online_minutes;
        $label = esc_html($user->display_name) . " ({$minutes} phút trước)";

        $wp_admin_bar->add_node([
            'id' => 'online-user-' . $user->ID,
            'parent' => 'online-users',
            'title' => $label,
            'href' => get_edit_user_link($user->ID),
        ]);
    }
}, 100);*/
add_action('admin_head', function () {
    ?>
    <style>
        @media screen and (max-width: 782px) {
            #wp-admin-bar-online-users {
                display: block !important;
            }

            #wp-admin-bar-online-users .ab-submenu {
                display: block !important;
                background: #23282d;
                padding: 4px 0;
            }

            #wp-admin-bar-online-users .ab-submenu .ab-item {
                color: orange !important;
                font-size: 13px;
            }
        }
        /* Mobile: submenu block dọc nhưng width vừa nội dung */
        @media screen and (max-width: 782px) {
            #wp-admin-bar-expired-dulieu {
                display: block !important;
            }

            #wp-admin-bar-expired-dulieu .ab-submenu {
                display: block !important;
                background: #23282d;
                padding: 4px 0;
                width: auto;
                max-width: 250px;
                overflow-x: auto; /* bật scroll ngang nếu cần */
            }

            #wp-admin-bar-expired-dulieu .ab-submenu .ab-item {
                color: orange !important;
                font-size: 13px;
                display: block; /* mỗi item trên 1 dòng */
                white-space: normal; /* xuống dòng khi quá dài */
            }
        }

        /* Màu menu chính */
        #wp-admin-bar-online-users > .ab-item {
            font-weight: bold;
            color: green !important; /* Xanh dương đậm */
        }

        /* Màu submenu user */
        #wp-admin-bar-online-users .ab-submenu .ab-item {
            padding-left: 12px;
            font-size: 13px;
            color: orange !important; /* Gần như trắng */
            display: flex;
            align-items: center;
            gap: 6px;
        }

        #wp-admin-bar-online-users .ab-submenu .ab-item:hover {
            color: green !important; /* Khi hover */
        }

        /* Chấm tròn online */
        #wp-admin-bar-online-users .ab-submenu .ab-item::before {
            content: "🟢";
            display: inline-block;
            margin-right: 6px;
            font-size: 10px;
            line-height: 1;
            transform: translateY(1px); /* can chỉnh nếu bị lệch */
        }
    </style>
    <?php
});

add_filter('xmlrpc_enabled', '__return_false');
remove_action('wp_head', 'wp_generator');

function set_one_column_dashboard() {
    return 1;
}
add_filter('screen_layout_dashboard', 'set_one_column_dashboard');

function force_one_column_dashboard() {
    global $current_user;
    update_user_meta($current_user->ID, 'screen_layout_dashboard', 1);
}
add_action('admin_init', 'force_one_column_dashboard');
function custom_admin_css() {
    echo '<style>
        #postbox-container-1,#postbox-container-2{
            width: 100% !important;
            max-width: 100% !important;
            float: none !important;
            clear: both !important;
        }
    </style>';
}









add_action('wp_ajax_check_duplicate_property', 'check_duplicate_property_callback');
add_action('wp_ajax_nopriv_check_duplicate_property', 'check_duplicate_property_callback');

function check_duplicate_property_callback() {
    global $wpdb;

    $data = $_POST['data'];

    $product_type = sanitize_text_field($data['product_type']);

    $transaction_type = sanitize_text_field($data['transaction_type']);
    $property_type    = sanitize_text_field($data['property_type']);
    $wp_province      = sanitize_text_field($data['wp_province']);
    $wp_district      = sanitize_text_field($data['wp_district']);
    $wp_wards         = sanitize_text_field($data['wp_wards']);
    $road             = sanitize_text_field($data['road']);

    // Nếu là nhà lẻ (ví dụ $product_type = 515)
    if ($product_type == '515') {
        $sonha = sanitize_text_field($data['sonha']);

        $exists = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM wp_dulieunhadat 
            WHERE transaction_type = %s 
              AND property_type = %s
              AND wp_province = %s
              AND wp_district = %s
              AND wp_wards = %s
              AND road = %s
              AND sonha = %s AND deleted = 0
        ", $transaction_type, $property_type, $wp_province, $wp_district, $wp_wards, $road, $sonha));

        if ($exists > 0) {
            wp_send_json([
                'duplicate' => true,
                'message' => '+ Nhà ở riêng lẻ có thể bị trùng: Loại Giao Dịch, Loại BĐS, Tỉnh, Quận, Phường, Đường, Số nhà!'
            ]);
        }
    }

    // Nếu là nhà dự án
    if ($product_type == '517') {
        $name_area = sanitize_text_field($data['name_area']);
        $code_product = sanitize_text_field($data['code_product']);

        $exists = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM wp_dulieunhadat 
            WHERE transaction_type = %s 
              AND property_type = %s
              AND wp_province = %s
              AND wp_district = %s
              AND wp_wards = %s
              AND road = %s
              AND name_area = %s
              AND code_product = %s AND deleted = 0
        ", $transaction_type, $property_type, $wp_province, $wp_district, $wp_wards, $road, $name_area, $code_product));

        if ($exists > 0) {
            wp_send_json([
                'duplicate' => true,
                'message' => '+ Nhà dự án có thể bị trùng: Loại Giao Dịch, Loại BĐS, Tỉnh, Quận, Phường, Đường, Tên dự án, Mã căn!'
            ]);
        }
    }

    wp_send_json(['duplicate' => false]);
}


add_action('wp_ajax_check_update_contact_all', 'check_update_contact_all_callback');
function check_update_contact_all_callback() {
    global $wpdb;
    $table = $wpdb->prefix . 'dulieunhadat';
    $row_id = intval($_POST['row_id']);

    // Lấy dữ liệu record
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $row_id));
    if (!$row) {
        wp_send_json_error("Không tìm thấy record");
    }

    // Nếu contact_all rỗng
    if (empty($row->contact_all)) {
        // Build JSON
        $contact_all = [
            "tinhtranggiaodich" => $row->tinhtranggiaodich ?: "675",
            "contacts_primary" => [
                "name"             => $row->name,
                "dienthoaididong"  => $row->dienthoaididong,
                "vaitro"           => $row->vaitro,
                "gioitinh"         => $row->gioitinh
            ],
            "contacts_info" => !empty($row->contact_info)
                ? json_decode($row->contact_info, true)
                : []
        ];

        // Update vào DB
        $wpdb->update(
            $table,
            [ "contact_all" => wp_json_encode($contact_all, JSON_UNESCAPED_UNICODE) ],
            [ "id" => $row_id ],
            [ "%s" ],
            [ "%d" ]
        );

        wp_send_json_success("Đã update contact_all");
    } else {
        wp_send_json_error("contact_all đã có dữ liệu");
    }
}







// Thêm style để node con có scroll
function spiritwebs_adminbar_expired_dulieu_style() {
    echo '<style>
        #wp-admin-bar-expired-dulieu .ab-submenu {
            max-height: 300px; /* Giới hạn chiều cao menu con */
            overflow-y: auto;   /* Cuộn dọc khi vượt quá */
        }
    </style>';
}
add_action('admin_head', 'spiritwebs_adminbar_expired_dulieu_style');


// xu ly phan san pham het han tren menu
// 📌 Thêm menu sản phẩm hết hạn (theo user hiện tại) vào admin bar
function spiritwebs_adminbar_expired_dulieu($wp_admin_bar) {
    if (!is_user_logged_in()) return;

    global $wpdb;
    $table = $wpdb->prefix . 'dulieunhadat';
    $current_user_id = get_current_user_id();

    // Lấy dữ liệu
    if (user_can($current_user_id, 'administrator')) {
        $rows = $wpdb->get_results(
            "SELECT id, user, dateupdate, datecreate FROM $table WHERE deleted = 0 ORDER BY id DESC",
            ARRAY_A
        );
    } else {
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, user, dateupdate, datecreate FROM $table WHERE user = %d AND deleted = 0 ORDER BY id DESC",
                $current_user_id
            ),
            ARRAY_A
        );
    }

    $expired_items = [];
    $today = time();

    foreach ($rows as $row) {
        $ngayNguon = (!empty($row['dateupdate']) && $row['dateupdate'] !== '0000-00-00 00:00:00')
            ? $row['dateupdate']
            : $row['datecreate'];

        $timestamp = strtotime($ngayNguon);
        if (!$timestamp) continue;

        $soNgay = ($today - $timestamp) / (60 * 60 * 24);

        if ($soNgay > 30) {
            // Lấy thông tin user để hiển thị tên
            $user_info = get_userdata($row['user']);
            $username = $user_info ? $user_info->display_name : 'Unknown';

            $expired_items[] = [
                'id'   => $row['id'],
                'user' => $username,
                'date' => $ngayNguon,
            ];
        }
    }

    // Sắp xếp theo tên nhân viên alphabet
    usort($expired_items, function($a, $b){
        return strcmp($a['user'], $b['user']);
    });

    // Node chính
    $wp_admin_bar->add_node([
        'id'    => 'expired-dulieu',
        //'title' => '⏳ Sản phẩm hết hạn: ' . count($expired_items),
        'title' => '⏳ Sản phẩm hết hạn: 0',
        'href'  => false
    ]);

    // Node con
    /*if ($expired_items) {
        foreach ($expired_items as $item) {
            $wp_admin_bar->add_node([
                'id'     => 'expired-dulieu-' . $item['id'],
                'parent' => 'expired-dulieu',
                'title'  => 'ID: ' . $item['id'] . ' | ' . esc_html($item['user']) . ' | Ngày: ' . esc_html($item['date']) . " ❌",
                'href'   => admin_url('admin.php?page=wp_dulieunhadat&id=' . intval($item['id']))
            ]);
        }
    } else {
        $wp_admin_bar->add_node([
            'id'     => 'expired-dulieu-none',
            'parent' => 'expired-dulieu',
            'title'  => '✅ Không có sản phẩm hết hạn',
            'href'   => false
        ]);
    }*/
}

add_action('admin_bar_menu', 'spiritwebs_adminbar_expired_dulieu', 120);



add_action('admin_head', 'custom_admin_css');
//hungtes
//an meta
//remove_action('wp_head', 'wp_generator');
// bỏ giới hạn dộ rộng up hình
// Tắt resize ảnh lớn
add_filter('big_image_size_threshold', '__return_false');

// Tắt tạo các ảnh trung gian
add_filter('intermediate_image_sizes_advanced', '__return_empty_array');


//chan truy cap link admin
add_action('admin_init', function () {
    // Nếu đang truy cập trang plugin
    if (is_admin() && isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], 'plugins.php') !== false) {
        wp_die('Trang này không tồn tại.');
    }
});




// xu lý lượt xem số điện thoại cho từng user

add_action('show_user_profile', 'spiritwebs_render_user_phone_limit');
add_action('edit_user_profile', 'spiritwebs_render_user_phone_limit');
add_action('user_new_form', 'spiritwebs_render_user_phone_limit');

function spiritwebs_render_user_phone_limit($user) {
    // ❌ Không phải admin thì khỏi render
    if (!current_user_can('administrator')) {
        return;
    }
    $value = is_object($user)
        ? get_user_meta($user->ID, 'number_view_phone', true)
        : '';
    ?>
    <h3>📞 Giới hạn xem số điện thoại</h3>
    <table class="form-table">
        <tr>
            <th>Số lượt / ngày</th>
            <td>
                <input type="number" name="number_view_phone"
                       value="<?php echo esc_attr($value); ?>" min="0">
                <p class="description">
                    Để trống = lấy theo role
                </p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('personal_options_update', 'spiritwebs_save_user_phone_limit');
add_action('edit_user_profile_update', 'spiritwebs_save_user_phone_limit');
add_action('user_register', 'spiritwebs_save_user_phone_limit');

function spiritwebs_save_user_phone_limit($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;

    if (!empty($_POST['number_view_phone'])) {
        update_user_meta($user_id, 'number_view_phone', intval($_POST['number_view_phone']));
    } else {
        delete_user_meta($user_id, 'number_view_phone'); // fallback về role
    }
}
function spiritwebs_get_user_phone_limit($user_id = null) {
    if (!$user_id) $user_id = get_current_user_id();

    // ưu tiên user
    $user_limit = get_user_meta($user_id, 'number_view_phone', true);
    if ($user_limit !== '') return (int)$user_limit;

    // fallback role
    $user = get_userdata($user_id);
    $roles_caps = get_option('company_roles_caps', []);

    foreach ($user->roles as $role) {
        if (!empty($roles_caps[$role]['number_view_phone'])) {
            return (int)$roles_caps[$role]['number_view_phone'];
        }
    }

    return 0;
}
// goi so gioi hạn : $limit = spiritwebs_get_user_phone_limit();

add_filter('manage_users_columns', 'spiritwebs_add_phone_limit_column');

add_filter('manage_users_columns', function ($columns) {

    // Nếu chưa có thì add
    if (!isset($columns['account_enabled'])) {
        $columns['account_enabled'] = 'Kích hoạt';
    }

    // Ép đưa về cuối mảng
    $toggle = $columns['account_enabled'];
    unset($columns['account_enabled']);
    $columns['account_enabled'] = $toggle;

    return $columns;
}, 999); // priority cao để chạy sau plugin khác

function spiritwebs_add_phone_limit_column($columns) {

    // Chỉ admin mới thấy
    if (!current_user_can('manage_options')) {
        return $columns;
    }

    $columns['number_view_phone'] = 'Limit xem SĐT/ngày';
    return $columns;
}
add_filter('manage_users_custom_column', 'spiritwebs_render_phone_limit_column', 10, 3);
function spiritwebs_render_phone_limit_column($value, $column_name, $user_id) {

    if ($column_name !== 'number_view_phone') return $value;

    // Không phải admin → không render gì
    if (!current_user_can('manage_options')) {
        return '';
    }

    // Ưu tiên lấy user meta
    $user_limit = get_user_meta($user_id, 'number_view_phone', true);
    if ($user_limit !== '') {
        return intval($user_limit);
    }

    // Fallback theo role
    $user = get_userdata($user_id);
    $caps = get_option('company_roles_caps', []);

    foreach ($user->roles as $role) {
        if (!empty($caps[$role]['number_view_phone'])) {
            return intval($caps[$role]['number_view_phone']);
        }
    }

    return '—';
}

// export so luot xem so dien thoai:
add_action('admin_post_export_luotxem_sdt', 'export_luotxem_sdt_csv');
add_action('admin_post_nopriv_export_luotxem_sdt', 'export_luotxem_sdt_csv');

function export_luotxem_sdt_csv() {

    if (!current_user_can('manage_options')) {
        wp_die('Không có quyền export');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'luot_xem_sdt';
    $users = $wpdb->users; // wp_users
    $history_table = $wpdb->prefix . 'dulieunhadat_history';

    $month = intval($_GET['month'] ?? date('m'));
    $year  = intval($_GET['year'] ?? date('Y'));

    if ($month < 1 || $month > 12) wp_die('Month invalid');
    if ($year < 2000) wp_die('Year invalid');

    $status_labels = [
        1 => 'Đúng Thông Tin',
        2 => 'Sai Thông Tin',
        3 => 'Không Liên Lạc Được',
        4 => 'Số Môi Giới',
    ];

    // ✅ Query join user - lượt xem / cập nhật tình trạng số điện thoại
    $sql = $wpdb->prepare("
        SELECT 
            l.id,
            l.nguoidung_id,
            u.user_login AS ten_nguoidung,
            l.nhadat_id,
            l.thoi_gian,
            l.ip,
            l.phone_status,
            l.note
        FROM {$table} l
        LEFT JOIN {$users} u 
            ON u.ID = l.nguoidung_id
        WHERE YEAR(l.thoi_gian) = %d
          AND MONTH(l.thoi_gian) = %d
        ORDER BY l.thoi_gian DESC
    ", $year, $month);

    $phone_rows = $wpdb->get_results($sql, ARRAY_A);

    // ✅ Bổ sung thông tin nhà - lấy từ lịch sử chỉnh sửa dữ liệu nhà đất
    $history_sql = $wpdb->prepare("
        SELECT record_id, changed_fields, userupdate, dateupdate, data
        FROM {$history_table}
        WHERE YEAR(dateupdate) = %d
          AND MONTH(dateupdate) = %d
        ORDER BY dateupdate DESC
    ", $year, $month);

    $history_rows = $wpdb->get_results($history_sql, ARRAY_A);

    if (empty($phone_rows) && empty($history_rows)) {
        wp_die("Không có dữ liệu tháng {$month}/{$year}");
    }

    // Tên trường hiển thị thân thiện cho phần "Bổ sung thông tin nhà" (giống chart trên dashboard)
    $field_labels = [
        'tieu_de'            => 'Tiêu đề',
        'tom_tat'            => 'Mô tả',
        'gia'                => 'Giá',
        'dien_tich'          => 'Diện tích',
        'diachi'             => 'Địa chỉ',
        'dia_chi'            => 'Địa chỉ',
        'tinhtranggiaodich'  => 'Tình trạng giao dịch',
        'name'               => 'Tên liên hệ',
        'dienthoaididong'    => 'Số điện thoại',
        'vaitro'             => 'Vai trò',
        'gioitinh'           => 'Giới tính',
    ];

    $format_value = function ($val) {
        $val = trim((string) $val);
        if ($val === '') return '(trống)';
        if (is_numeric($val)) return number_format((float) $val, 0, ',', '.');
        return mb_strlen($val) > 60 ? mb_substr($val, 0, 60) . '...' : $val;
    };

    $filename = "luotxem_sdt_{$year}_{$month}.csv";

    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // UTF-8 BOM cho Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Header CSV (Tiếng Việt) - dùng chung 1 bảng, phân biệt bằng cột "Loại hoạt động"
    fputcsv($output, [
        'Loại hoạt động',
        'ID Nhân Viên',
        'Tên Nhân Viên',
        'ID Nhà đất',
        'Thời gian',
        'Chi tiết',
        'IP'
    ]);

    // Gộp dữ liệu 2 nguồn lại rồi sắp xếp theo thời gian giảm dần
    $combined = [];

    // 1) Xem số điện thoại / Cập nhật tình trạng số điện thoại
    foreach ($phone_rows as $row) {
        $has_status = !empty($row['phone_status']);
        $loai = $has_status ? 'Cập nhật tình trạng số điện thoại' : 'Xem số điện thoại';

        $chi_tiet = '';
        if ($has_status) {
            $chi_tiet = $status_labels[(int) $row['phone_status']] ?? 'Không xác định';
            $note_text = trim((string) $row['note']);
            if ($note_text !== '') {
                $chi_tiet .= ' - Ghi chú: ' . $note_text;
            }
        }

        $combined[] = [
            'time'   => $row['thoi_gian'],
            'fields' => [
                $loai,
                $row['nguoidung_id'],
                $row['ten_nguoidung'],
                $row['nhadat_id'],
                date('d/m/Y H:i', strtotime($row['thoi_gian'])),
                $chi_tiet,
                $row['ip'],
            ],
        ];
    }

    // 2) Bổ sung thông tin nhà
    foreach ($history_rows as $row) {
        $uid = (int) $row['userupdate'];
        $user_info = get_userdata($uid);
        $ten_nv = $user_info ? $user_info->user_login : "NV#{$uid}";

        $unserialized = !empty($row['data']) ? maybe_unserialize($row['data']) : [];
        $field_changes = (is_array($unserialized) && !empty($unserialized['field_changes']))
            ? $unserialized['field_changes']
            : [];

        $change_lines = [];
        if (!empty($field_changes)) {
            foreach ($field_changes as $field => $vals) {
                if (in_array($field, ['contact_all', 'contact_info'], true)) {
                    continue;
                }
                $label = $field_labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                $old_val = $format_value($vals['old'] ?? '');
                $new_val = $format_value($vals['new'] ?? '');
                $change_lines[] = "{$label}: {$old_val} -> {$new_val}";
            }
        }
        if (empty($change_lines)) {
            $fields = trim((string) $row['changed_fields']) !== ''
                ? str_replace(',', ', ', $row['changed_fields'])
                : 'Cập nhật thông tin liên hệ';
            $change_lines[] = $fields;
        }

        $combined[] = [
            'time'   => $row['dateupdate'],
            'fields' => [
                'Bổ sung thông tin nhà',
                $uid,
                $ten_nv,
                $row['record_id'],
                date('d/m/Y H:i', strtotime($row['dateupdate'])),
                implode(' | ', $change_lines),
                '',
            ],
        ];
    }

    // Sắp xếp toàn bộ theo thời gian giảm dần
    usort($combined, function ($a, $b) {
        return strtotime($b['time']) <=> strtotime($a['time']);
    });

    foreach ($combined as $item) {
        fputcsv($output, $item['fields']);
    }

    fclose($output);
    exit;
}

// ẩn phân trang phía trên màn hình list user
add_action('admin_head-users.php', function () {
    echo '<style>
        .tablenav.top .tablenav-pages {
            display: none !important;
        }
    </style>';
});





// hook và thanh filter user
add_action('restrict_manage_users', function () {
    global $pagenow;
    if ($pagenow !== 'users.php') return;

    // ✅ Chặn render trùng
    static $rendered = false;
    if ($rendered) return;
    $rendered = true;

    $selected = $_GET['manager_id'] ?? '';

    $managers = get_users([
        'role'    => 'quanlykinhdoanh',
        'orderby' => 'display_name',
        'number'  => 200,
    ]);
    ?>
    <div class="manager-filter-wrap" id="manager-filter-wrap">
        <select name="manager_id" id="manager-filter">
            <option value="">-- Quản Lý KD --</option>
            <?php foreach ($managers as $m): ?>
                <option value="<?= esc_attr($m->ID) ?>" <?= selected($selected, $m->ID, false) ?>>
                    <?= esc_html($m->display_name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
});

add_action('admin_head-users.php', function () {
    echo '<style>
        .tablenav.top {
            position: relative;
        }

        .manager-filter-wrap {
            position: absolute;
            right: 5px;
            top: 19px;
            z-index: 5;
        }

        .manager-filter-wrap select {
            min-width: 220px;
        }

        /* Ẩn pagination phía trên nếu cần */
        .tablenav.top .tablenav-pages {
            display: none !important;
        }
    </style>';
});
add_action('admin_footer', function () {
    ?>
    <script>
        (function(){
            const select = document.getElementById('manager-filter');
            if(!select) return;

            // ✅ Tìm form cha gần nhất thay vì hardcode ID
            let form = select.closest('form');

            // Nếu không tìm được → fallback: lấy form đầu tiên trong trang
            if(!form){
                form = document.querySelector('form');
            }

            if(!form){
                console.error('❌ Không tìm thấy form submit');
                return;
            }

            // ✅ Inject hidden input
            let hidden = form.querySelector('input[name="manager_id"]');
            if(!hidden){
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'manager_id';
                form.appendChild(hidden);
            }

            // ✅ Change → submit
            select.addEventListener('change', function(){
                hidden.value = this.value;
                form.submit();
            });

        })();
    </script>
    <?php
});
add_action('pre_get_users', function ($query) {

    // ✅ Chỉ áp dụng cho query bảng Users chính
    if (
        empty($_GET['manager_id']) ||
        empty($query->query_vars['fields']) ||
        $query->query_vars['fields'] !== 'all_with_meta'
    ) {
        return;
    }

    $manager_id = (string) intval($_GET['manager_id']);

    $query->query_vars['meta_key']     = 'quan_ly_id';
    $query->query_vars['meta_value']   = $manager_id;
    $query->query_vars['meta_compare'] = '=';

}, 999);
add_action('admin_footer', function () {
    ?>
    <script>
        (function(){

            // ✅ Đổi nút "Add New"
            const addBtn = document.querySelector('.wrap .page-title-action');
            if(addBtn){
                addBtn.textContent = 'Thêm nhân viên';
            }

            // ✅ Đổi nút "Search Users"
            const searchBtn = document.querySelector('#search-submit');
            if(searchBtn){
                searchBtn.value = 'Tìm kiếm nhân viên';
            }

        })();
    </script>
    <?php
});
add_action('admin_head', function () {
    ?>
    <style>
        /* ✅ Căn hàng search users */
        #user-search-form,
        .search-box {
            display: flex !important;
            align-items: center !important;
            gap: 6px;
        }

        /* ✅ Input search */
        #user-search-input {
            height: 32px !important;
            line-height: 32px !important;
        }

        /* ✅ Button search */
        #search-submit {
            height: 32px !important;
            margin: 0 !important;
            padding: 0 12px !important;
        }
    </style>
    <?php
});
add_action('admin_head', function () {
    ?>
    <style>

        /* ✅ Table layout ổn định */
        .wp-list-table.users {
            table-layout: fixed !important;
        }

        /* ✅ Cột Username (cột đầu) */
        .wp-list-table.users th.column-username,
        .wp-list-table.users td.column-username {
            width: 180px !important;     /* 👈 chỉnh số này nếu muốn rộng hơn */
            max-width: 180px !important;
            white-space: normal !important;
            word-break: break-word;
        }

        /* ✅ Checkbox giữ nhỏ lại cho gọn */
        .wp-list-table.users th.check-column,
        .wp-list-table.users td.check-column {
            width: 36px !important;
        }
        /* ✅ Avatar trong bảng users */
        .wp-list-table.users .avatar {
            width: 24px !important;     /* 👈 chỉnh size ở đây */
            height: 24px !important;
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 6px;
        }

        /* ✅ Canh text thẳng hàng với avatar */
        .wp-list-table.users .column-username strong,
        .wp-list-table.users .column-username .username {
            line-height: 24px;
        }
        /* ✅ Viết hoa chữ cái đầu cho tên user */
        .wp-list-table.users .column-username strong,
        .wp-list-table.users .column-username .username {
            text-transform: capitalize;
        }



    </style>
    <?php
});

// chặn https://warehouse.urbanhome.vn/wp-json/wp/v2/users/


add_filter('rest_endpoints', function ($endpoints) {
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    return $endpoints;
});


// gỡ ra thì mọi user truy cập bình thường
/*function restrict_admin_access_strict() {

    $allowed_user = 'urbanowner';
    $current_user = wp_get_current_user();

    // Nếu đã login nhưng KHÔNG phải urbanowner → logout ngay
    if (is_user_logged_in() && $current_user->user_login !== $allowed_user) {
        wp_logout();

        wp_die(
            '<h1>🚧 Website đang bảo trì</h1>
            <p>Phiên đăng nhập của bạn đã bị đóng.</p>',
            'Bảo trì',
            array('response' => 503)
        );
    }

    // CHẶN TRANG LOGIN
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        if (!is_user_logged_in()) {
            wp_die('🚧 Website đang bảo trì');
        }
    }

    // CHẶN ADMIN
    if (is_admin() && !defined('DOING_AJAX')) {
        if (!is_user_logged_in()) {
            wp_die('🚧 Website đang bảo trì');
        }
    }
}

add_action('init', 'restrict_admin_access_strict');// demo
 */

// asign cho usser khac khi xoa usser

/**
 * Thêm UI chọn user khi delete user
 */
add_action('delete_user_form', function($current_user, $user_ids) {

    global $wpdb;

    if (!is_array($user_ids)) {
        $user_ids = [$user_ids];
    }

    foreach ($user_ids as $user_id) {

        $count1 = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM wp_dulieunhadat
            WHERE user = %d
        ", $user_id));

        $count2 = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM wp_dulieunhadat_goc
            WHERE user = %d
        ", $user_id));

        $total = $count1;

        if ($total > 0) {

            $users = get_users([
                'exclude' => [$user_id]
            ]);

            echo '<h3>Chuyển dữ liệu nhà đất</h3>';
            echo '<p>User này có <strong>' . $total . '</strong> dữ liệu liên quan.</p>';

            echo '<select name="reassign_nhadat_user" required>';
            echo '<option value="">-- Chọn user nhận dữ liệu --</option>';

            foreach ($users as $u) {
                echo '<option value="' . $u->ID . '">';
                echo esc_html($u->user_login . ' (#' . $u->ID . ')');
                echo '</option>';
            }

            echo '</select>';
        }
    }

}, 10, 2);


/**
 * Reassign dữ liệu trước khi xóa user
 */
add_action('delete_user', function($user_id) {

    global $wpdb;

    if (empty($_POST['reassign_nhadat_user'])) {
        return;
    }

    $new_user_id = intval($_POST['reassign_nhadat_user']);

    if (!$new_user_id) {
        return;
    }

    $wpdb->update(
        'wp_dulieunhadat',
        ['user' => $new_user_id],
        ['user' => $user_id]
    );

    $wpdb->update(
        'wp_dulieunhadat_goc',
        ['user' => $new_user_id],
        ['user' => $user_id]
    );

}, 10, 1);