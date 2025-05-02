<?php
/*
Plugin Name: Custom Login Path
Plugin URI:  https://wafiqdigital.com/custom-login-path
Support URI: https://github.com/FahmiRabbani/custom-login-path
Description: Ubah URL login WordPress standar menjadi path kustom yang lebih aman dan tersembunyi.
Version:     1.0.0
Author:      Fahmi M. Rabbani
Author URI:  https://wafiqdigital.com
Donate link: https://wafiqdigital.com/donate
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: custom-login-path
*/


if ( ! defined('ABSPATH') ) exit;

// 1) Tambahkan menu Settings
add_action('admin_menu', function(){
    add_options_page(
        'Hide WP Login URL',
        'Hide WP Login URL',
        'manage_options',
        'hide-wp-login-url',
        'hwlu_settings_page'
    );
});

// 2) Daftarkan setting
add_action('admin_init', function(){
    register_setting('hwlu_settings_group', 'hwlu_custom_slug');
    register_setting('hwlu_settings_group', 'hwlu_plugin_enabled');
});

// 3) Halaman Settings
function hwlu_settings_page(){
    ?>
    <div class="wrap">
      <h1>Hide WP Login URL Settings</h1>
      <form method="post" action="options.php">
        <?php
          settings_fields('hwlu_settings_group');
          do_settings_sections('hwlu_settings_group');
        ?>
        <table class="form-table">
          <tr>
            <th><label for="hwlu_custom_slug">URL Login Kustom</label></th>
            <td>
              <input type="text" id="hwlu_custom_slug" name="hwlu_custom_slug"
                     value="<?php echo esc_attr(get_option('hwlu_custom_slug', 'login-user')); ?>" />
              <p class="description">Tanpa slash (/), misal: <code>users</code></p>
            </td>
          </tr>
          <tr>
            <th>Aktifkan Plugin</th>
            <td>
              <input type="checkbox" name="hwlu_plugin_enabled" value="1"
                     <?php checked(1, get_option('hwlu_plugin_enabled', 1)); ?> />
            </td>
          </tr>
        </table>
        <?php submit_button('Simpan Pengaturan'); ?>
      </form>
    </div>
    <?php
}

// 4) Daftarkan rewrite rule & blocking
function hwlu_rewrite_and_block(){
    // hanya jika plugin aktif
    if ( ! get_option('hwlu_plugin_enabled', 1) ) {
        return;
    }

    $slug = trim(get_option('hwlu_custom_slug', 'login-user'), '/');
    $uri  = trim($_SERVER['REQUEST_URI'], '/');

    // blok akses langsung wp-login.php (GET) dan wp-admin (GET) bagi yang belum login
    if ( $_SERVER['REQUEST_METHOD'] === 'GET'
      && ! is_user_logged_in()
      && ( strpos($uri, 'wp-login.php') !== false || strpos($uri, 'wp-admin') !== false )
    ) {
        wp_redirect(home_url());
        exit;
    }

    // jika URL sama dengan slug kustom, muat login
    if ( $uri === $slug ) {
        require_once ABSPATH . 'wp-login.php';
        exit;
    }

    // tambahkan rewrite rule agar WP mengenali /{slug}
    add_rewrite_rule("^{$slug}/?$", 'index.php?pagename=' . $slug, 'top');
}
add_action('init', 'hwlu_rewrite_and_block', 1);

// 5) Redirect setelah login sukses
add_filter('login_redirect', function($redirect_to, $request, $user){
    return is_wp_error($user) ? $redirect_to : admin_url();
}, 10, 3);

// 6) Flush rewrite rules saat activate/deactivate
function hwlu_activate(){
    hwlu_rewrite_and_block();
    flush_rewrite_rules();
}
function hwlu_deactivate(){
    flush_rewrite_rules();
}
register_activation_hook(__FILE__,   'hwlu_activate');
register_deactivation_hook(__FILE__, 'hwlu_deactivate');
