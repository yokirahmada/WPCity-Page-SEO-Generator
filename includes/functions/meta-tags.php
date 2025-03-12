<?php
// Add Meta Tags to <head>
function city_page_seo_add_meta_tags() {
    if (is_singular()) {
        global $post;
        $seo_title = get_post_meta($post->ID, '_city_seo_title', true);
        $seo_desc = get_post_meta($post->ID, '_city_seo_desc', true);

        if (!empty($seo_title)) {
            echo '<meta property="og:title" content="' . esc_attr($seo_title) . '" />' . "\n";
        }
        if (!empty($seo_desc)) {
            echo '<meta property="og:description" content="' . esc_attr($seo_desc) . '" />' . "\n";
        }
    }
}
add_action('wp_head', 'city_page_seo_add_meta_tags', 1); // Priority 1 untuk memastikan meta tags di atas