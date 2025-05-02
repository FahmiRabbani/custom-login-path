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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1) Tambah menu Settings
add_action( 'admin_menu', function () {
    add_options_page(
        'Custom Login Path Settings',
        'Custom Login Path',
        'manage_options',
        'custom-login-path',
        'clp_settings_page'
    );
});

// 2) Daftarkan setting dengan sanitasi
add_action( 'admin_init', function () {
    register_setting( 'clp_settings_group', 'clp_custom_slug', 'sanitize_text_field' );
    register_setting( 'clp_settings_group', 'clp_plugin_enabled', 'clp_sanitize_checkbox' );
});

// Fungsi sanitasi untuk checkbox
function clp_sanitize_checkbox( $input ) {
    return ( isset( $input ) && '1' === $input ) ? 1 : 0;
}

// 3) Halaman Settings
function clp_settings_page() {
    ?>
    <div class="wrap">
        <h1>Pengaturan Custom Login Path</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'clp_settings_group' );
            do_settings_sections( 'clp_settings_group' );
            ?>
            <table class="form-table">
                <tr>
                    <th><label for="clp_custom_slug">URL Login Kustom</label></th>
                    <td>
                        <input type="text" id="clp_custom_slug" name="clp_custom_slug"
                               value="<?php echo esc_attr( get_option( 'clp_custom_slug', 'login-user' ) ); ?>" />
                        <p class="description">Tanpa tanda garis miring (/), contoh: <code>masuk</code></p>
                    </td>
                </tr>
                <tr>
                    <th>Aktifkan Plugin</th>
                    <td>
                        <input type="checkbox" name="clp_plugin_enabled" value="1"
                            <?php checked( 1, get_option( 'clp_plugin_enabled', 1 ) ); ?> />
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Simpan Pengaturan' ); ?>
        </form>
    </div>
    <?php
}

// 4) Logic utama: rewrite dan proteksi akses
function clp_rewrite_and_block() {
    if ( ! get_option( 'clp_plugin_enabled', 1 ) ) {
        return;
    }

    $slug   = trim( sanitize_text_field( get_option( 'clp_custom_slug', 'login-user' ) ), '/' );
    $uri    = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    $uri    = trim( $uri, '/' );
    $method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';

    // Blokir akses langsung ke wp-login.php atau wp-admin (jika belum login)
    if ( 'GET' === $method && ! is_user_logged_in() &&
        ( str_contains( $uri, 'wp-login.php' ) || str_contains( $uri, 'wp-admin' ) ) ) {
        wp_redirect( home_url() );
        exit;
    }

    // Jika URL sama dengan slug custom, muat form login
    if ( $uri === $slug ) {
        require_once ABSPATH . 'wp-login.php';
        exit;
    }

    // Tambahkan rewrite rule
    add_rewrite_rule( "^{$slug}/?$", 'index.php?pagename=' . $slug, 'top' );
}
add_action( 'init', 'clp_rewrite_and_block', 1 );

// 5) Redirect setelah login sukses
add_filter( 'login_redirect', function ( $redirect_to, $request, $user ) {
    return is_wp_error( $user ) ? $redirect_to : admin_url();
}, 10, 3 );

// 6) Flush rewrite rules saat aktif/deaktif
function clp_activate() {
    clp_rewrite_and_block();
    flush_rewrite_rules();
}
function clp_deactivate() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'clp_activate' );
register_deactivation_hook( __FILE__, 'clp_deactivate' );
