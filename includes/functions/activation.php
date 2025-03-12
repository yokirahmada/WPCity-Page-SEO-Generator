<?php
// Activation Hook: Buat tabel kustom saat plugin diaktifkan
function city_page_seo_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'city_page_seo'; // Nama tabel dengan prefix
    $charset_collate = $wpdb->get_charset_collate(); // Gunakan charset dan collation database

    // Debugging: Tampilkan pesan saat hook dijalankan
    error_log('Plugin City Page SEO diaktifkan. Membuat tabel...');

    // SQL untuk membuat tabel
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        city_name varchar(255) NOT NULL,
        seo_title varchar(255) NOT NULL,
        seo_desc text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Memuat file dbDelta
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Eksekusi SQL
    dbDelta($sql);

    // Debugging: Tampilkan pesan setelah membuat tabel
    error_log('Tabel ' . $table_name . ' berhasil dibuat atau sudah ada.');

    // Tambahkan opsi plugin jika diperlukan
    add_option('city_page_seo_options', []);
}
register_activation_hook(__FILE__, 'city_page_seo_activate');