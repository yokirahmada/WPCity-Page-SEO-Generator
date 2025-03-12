<?php
// Add Meta Box untuk SEO di Custom Post Types, Pages, dan Posts
function city_page_seo_add_meta_box() {
    $post_types = get_post_types(array('public' => true)); // Dapatkan semua post types yang public
    foreach ($post_types as $post_type) {
        add_meta_box(
            'city_page_seo_meta_box', // ID meta box
            'City Page SEO Settings', // Judul meta box
            'city_page_seo_meta_box_callback', // Callback function
            $post_type, // Post type
            'normal', // Context
            'high' // Priority
        );
    }
}
add_action('add_meta_boxes', 'city_page_seo_add_meta_box');

// Callback function untuk menampilkan meta box
function city_page_seo_meta_box_callback($post) {
    wp_nonce_field('city_page_seo_meta_box_action', 'city_page_seo_meta_box_nonce');

    $seo_title = get_post_meta($post->ID, '_city_seo_title', true);
    $seo_desc = get_post_meta($post->ID, '_city_seo_desc', true);

    echo '<div class="city-seo-meta-box">
            <label for="city_seo_title">SEO Title:</label>
            <input type="text" id="city_seo_title" name="city_seo_title" value="' . esc_attr($seo_title) . '" style="width:100%;" />
            <br><br>
            <label for="city_seo_desc">SEO Description:</label>
            <textarea id="city_seo_desc" name="city_seo_desc" style="width:100%; height: 100px;" maxlength="160">' . esc_textarea($seo_desc) . '</textarea>
            <small class="char-count">Karakter tersisa: <span id="desc-char-count">160</span></small>
            <div class="warning" id="desc-warning" style="color: red; display: none;">Deskripsi SEO melebihi 160 karakter!</div>
          </div>';
}

// Save Meta Box Data
function city_page_seo_save_meta_box_data($post_id) {
    if (!isset($_POST['city_page_seo_meta_box_nonce']) || !wp_verify_nonce($_POST['city_page_seo_meta_box_nonce'], 'city_page_seo_meta_box_action')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['city_seo_title'])) {
        update_post_meta($post_id, '_city_seo_title', sanitize_text_field($_POST['city_seo_title']));
    }

    if (isset($_POST['city_seo_desc'])) {
        update_post_meta($post_id, '_city_seo_desc', wp_kses_post($_POST['city_seo_desc']));
    }
}
add_action('save_post', 'city_page_seo_save_meta_box_data');