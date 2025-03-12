<?php
/**
 * Plugin Name: City Page Customization SEO
 * Description: Creates SEO-optimized pages dynamically from a list of cities or topics.
 * Version: 1.0
 * Author: Yoki Rahmada
 */

if (!defined('ABSPATH')) {
    exit; 
}

require_once plugin_dir_path(__FILE__) . 'includes/functions/activation.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/meta-box.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions/meta-tags.php';
register_activation_hook(__FILE__, 'city_page_seo_activate');


function city_page_seo_enqueue_styles($hook) {

    if ($hook != 'toplevel_page_city_page_seo') {
        return;
    }
    wp_enqueue_style(
        'city-page-seo-style',
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        array(),
        '1.0'
    );
}
add_action('admin_enqueue_scripts', 'city_page_seo_enqueue_styles');


function city_page_seo_enqueue_scripts($hook) {
    if (in_array($hook, ['post.php', 'post-new.php', 'toplevel_page_city_page_seo'])) {
        wp_enqueue_script(
            'city-page-seo-script',
            plugin_dir_url(__FILE__) . 'assets/js/script.js',
            array('jquery'),
            '1.0',
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'city_page_seo_enqueue_scripts');
