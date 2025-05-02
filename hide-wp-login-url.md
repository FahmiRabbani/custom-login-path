=== Hide WP Login URL ===
Contributors: fahmirabbani
Tags: hide login, custom login URL, wp-admin, security
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin ini menyembunyikan halaman login standar WordPress (`wp-login.php` dan `wp-admin`) dan menggantinya dengan URL login kustom.

== Description ==

**Hide WP Login URL** adalah solusi ringan dan efisien untuk mengamankan halaman login WordPress Anda. Dengan plugin ini, Anda dapat menyembunyikan `wp-login.php` dan `wp-admin`, lalu menggantinya dengan URL kustom pilihan Anda. Tersedia pengaturan sederhana di admin panel dengan fitur:

- Form pengaturan slug URL login baru
- Toggle aktif/nonaktif plugin
- Redirect otomatis dari wp-login.php/wp-admin ke homepage

Sangat cocok bagi pemilik situs yang menginginkan keamanan ekstra dari serangan brute-force tanpa memodifikasi file inti.

== Installation ==

1. Upload folder plugin ke direktori `/wp-content/plugins/`
2. Aktifkan plugin melalui menu **Plugins** di WordPress
3. Masuk ke **Settings > Hide WP Login URL**
4. Tentukan slug URL login kustom Anda (misalnya: `login-user`)
5. Aktifkan plugin dan simpan pengaturan

== Frequently Asked Questions ==

= Bagaimana jika saya lupa URL login saya? =
Masuk ke database (phpMyAdmin) dan cari opsi `hide_wp_login_slug` di tabel `wp_options`.

== Screenshots ==

1. Halaman pengaturan plugin
2. Contoh pengalihan otomatis dari `wp-login.php` ke homepage

== Changelog ==

= 1.1 =
* Menambahkan opsi pengaturan dan toggle aktif/nonaktif

= 1.0 =
* Rilis awal plugin

== Upgrade Notice ==

= 1.1 =
Disarankan update karena adanya sistem toggle dan pengaturan yang lebih stabil.

== Credits ==
Developed by [Fahmi M. Rabbani](https://wafiqdigital.com/) – Wafiq Digital
