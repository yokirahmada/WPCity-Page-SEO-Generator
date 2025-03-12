<?php
// Shortcode to Insert City Name in Content
function city_page_seo_shortcode($atts) {
    $atts = shortcode_atts(['city' => ''], $atts);
    return esc_html($atts['city']);
}
add_shortcode('city_name', 'city_page_seo_shortcode');