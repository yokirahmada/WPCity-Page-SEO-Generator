<?php
// Admin Menu Setup
function city_page_seo_admin_menu() {
    add_menu_page('City Page SEO', 'City Page SEO', 'manage_options', 'city_page_seo', 'city_page_seo_admin_page');
}
add_action('admin_menu', 'city_page_seo_admin_menu');

// Admin Page UI
function city_page_seo_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'city_page_seo';

    // Open main container with class "city-page-seo"
    echo '<div class="wrap city-page-seo">';

    // Handle Add City
    if (isset($_POST['city_page_seo_save']) && check_admin_referer('city_page_seo_save_action', 'city_page_seo_nonce')) {
        $cities = explode(",", sanitize_text_field($_POST['city_page_seo_cities']));
        $cities = array_filter(array_map('trim', $cities)); // Remove empty cities

        // Get SEO Title and SEO Description templates from input
        $seo_title_template = sanitize_text_field($_POST['seo_title_template']);
        $seo_desc_template = wp_kses_post($_POST['seo_desc_template']);

        if (empty($cities)) {
            echo '<div class="error"><p>Please input city name!</p></div>';
        } else {
            // Get page content template
            $page_content_template = wp_kses_post($_POST['page_content_template']);
        
            foreach ($cities as $city) {
                if (!empty($city)) {
                    // Replace {city} with the city name
                    $seo_title = str_replace('{city}', $city, $seo_title_template);
                    $seo_desc = str_replace('{city}', $city, $seo_desc_template);
                    $page_content = str_replace('{city}', $city, $page_content_template);
        
                    $wpdb->insert(
                        $table_name,
                        array(
                            'city_name' => $city,
                            'seo_title' => $seo_title,
                            'seo_desc'  => $seo_desc,
                        )
                    );
        
                    // Automatically create a new page for the city
                    $post_data = array(
                        'post_title'    => "Best Services in $city",
                        'post_content'  => $page_content, // Dynamic page content
                        'post_status'   => 'publish',
                        'post_type'     => 'page',
                        'meta_input'    => [
                            '_city_seo_title' => $seo_title,
                            '_city_seo_desc'  => $seo_desc,
                        ]
                    );
                    wp_insert_post($post_data);
                }
            }
            echo '<div class="updated"><p>Cities added successfully!</p></div>';
        }
    }

    // Handle Delete City
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']) && isset($_GET['_wpnonce'])) {
        if (!wp_verify_nonce($_GET['_wpnonce'], 'city_page_seo_delete_' . $_GET['id'])) {
            echo '<div class="error"><p>Security check failed. Please try again.</p></div>';
        } else {
            $wpdb->delete($table_name, array('id' => intval($_GET['id'])));
            echo '<div class="updated"><p>City deleted successfully!</p></div>';
        }
    }

    // Handle Bulk Delete
    if (isset($_POST['city_page_seo_bulk_delete']) && check_admin_referer('city_page_seo_bulk_delete_action', 'city_page_seo_bulk_delete_nonce')) {
        if (!empty($_POST['city_ids'])) {
            $city_ids = array_map('intval', $_POST['city_ids']); // Ensure IDs are integers
            $placeholders = implode(',', array_fill(0, count($city_ids), '%d')); // Create placeholders for the query

            // Delete selected data from the database
            $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($placeholders)", $city_ids));

            echo '<div class="updated"><p>Selected cities deleted successfully!</p></div>';
        } else {
            echo '<div class="error"><p>No cities selected for deletion.</p></div>';
        }
    }

    // Handle Edit City
    if (isset($_POST['city_page_seo_edit']) && check_admin_referer('city_page_seo_edit_action', 'city_page_seo_nonce')) {
        $id = intval($_POST['city_id']);
        $city_name = sanitize_text_field($_POST['city_name']);
        $seo_title = sanitize_text_field($_POST['seo_title']);
        $seo_desc = wp_kses_post($_POST['seo_desc']);

        // Validate input during edit
        if (empty($city_name) || empty($seo_title) || empty($seo_desc)) {
            echo '<div class="error"><p>All fields are required!</p></div>';
        } else {
            $wpdb->update(
                $table_name,
                array(
                    'city_name' => $city_name,
                    'seo_title' => $seo_title,
                    'seo_desc'  => $seo_desc,
                ),
                array('id' => $id)
            );
            echo '<div class="updated"><p>City updated successfully!</p></div>';
        }
    }

    // Fetch All Cities with Search and Pagination
    $search_city = isset($_GET['search_city']) ? sanitize_text_field($_GET['search_city']) : '';
    $per_page = 25; // Number of items per page
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1; // Current page
    $offset = ($current_page - 1) * $per_page; // Calculate offset

    // Query to fetch city data
    $query = "SELECT * FROM $table_name";
    if (!empty($search_city)) {
        $query .= $wpdb->prepare(" WHERE city_name LIKE %s", '%' . $wpdb->esc_like($search_city) . '%');
    }
    $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);
    $cities = $wpdb->get_results($query);

    // Calculate total number of cities
    $total_cities = $wpdb->get_var("SELECT COUNT(*) FROM $table_name" . (!empty($search_city) ? $wpdb->prepare(" WHERE city_name LIKE %s", '%' . $wpdb->esc_like($search_city) . '%') : ''));
    $total_pages = ceil($total_cities / $per_page); // Calculate total pages

    // Display Edit Form if Editing
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $city_id = intval($_GET['id']);
        $city = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $city_id));

        if ($city) {
            echo '<h1>Edit City</h1>
                  <form method="post">
                  ' . wp_nonce_field('city_page_seo_edit_action', 'city_page_seo_nonce', true, false) . '
                  <input type="hidden" name="city_id" value="' . esc_attr($city->id) . '" />
                  <label>City Name:</label>
                  <input type="text" name="city_name" value="' . esc_attr($city->city_name) . '" style="width:100%;" required />
                  <br><br>
                  <label>SEO Title:</label>
                  <input type="text" name="seo_title" value="' . esc_attr($city->seo_title) . '" style="width:100%;" required />
                  <br><br>
                  <label>SEO Description:</label>';
            wp_editor($city->seo_desc, 'seo_desc', array('textarea_name' => 'seo_desc', 'media_buttons' => false, 'textarea_rows' => 10));
            echo '<br><br>
                  <input type="submit" name="city_page_seo_edit" value="Update City" class="button-primary" />
                  <a href="?page=city_page_seo" class="custom-button-danger">Cancel</a>
                  </form>';
            echo '</div>'; 
            return;
        }
    }

    // Display main page
    echo '<h1>City Page Customization SEO</h1>
    <form method="post">
    ' . wp_nonce_field('city_page_seo_save_action', 'city_page_seo_nonce', true, false) . '
    <label>Enter Cities (comma-separated):</label>
    <input type="text" name="city_page_seo_cities" value="" style="width:100%;" required />
    <br><br>
    <label>SEO Title Template:</label>
    <input type="text" name="seo_title_template" value="Top Services in {city}" style="width:100%;" required />
    <small>Use <code>{city}</code> to display the city name.</small>
    <br><br>
    <label>SEO Description Template:</label>
    <textarea name="seo_desc_template" style="width:100%; height: 100px;" required>Find the best services available in {city} today!</textarea>
    <small>Use <code>{city}</code> to display the city name.</small>
    <br><br>
    <label>Page Content Template:</label>
    <textarea name="page_content_template" style="width:100%; height: 200px;" required>Welcome to our services in {city}! We offer the best services in {city} to meet your needs.</textarea>
    <small>Use <code>{city}</code> to display the city name.</small>
    <br><br>
    <input type="submit" name="city_page_seo_save" value="Add Cities" class="button-primary" />
    </form>
    <br><br><br>';

    // Search Form
    echo '<form method="get" action="">
          <input type="hidden" name="page" value="city_page_seo" />
          <label>Search City:</label>
          <input type="text" name="search_city" value="' . (isset($_GET['search_city']) ? esc_attr($_GET['search_city']) : '') . '" style="width:300px;" placeholder="Enter city name" />
          <input type="submit" value="Search" class="button-primary" />
          <a href="?page=city_page_seo" class="custom-button-danger">Reset</a>
          </form>
          <br>';

    // Display city list table with checkboxes
    echo '<h2>List of Cities</h2>
          <form method="post" action="">
          ' . wp_nonce_field('city_page_seo_bulk_delete_action', 'city_page_seo_bulk_delete_nonce', true, false) . '
          <input type="submit" name="city_page_seo_bulk_delete" value="Delete Selected" class="button" id="delete-selected" onclick="return confirm(\'Are you sure you want to delete the selected cities?\')" />
          <table class="wp-list-table widefat fixed striped">
          <thead>
              <tr>
                  <th><input type="checkbox" id="select-all" /></th>
                  <th>City</th>
                  <th>SEO Title</th>
                  <th>SEO Description</th>
                  <th>Actions</th>
              </tr>
          </thead>
          <tbody>';

    if ($cities) {
        foreach ($cities as $city) {
            $delete_url = wp_nonce_url(
                admin_url('admin.php?page=city_page_seo&action=delete&id=' . $city->id),
                'city_page_seo_delete_' . $city->id
            );

            echo '<tr>
                  <td><input type="checkbox" name="city_ids[]" value="' . esc_attr($city->id) . '" /></td>
                  <td>' . esc_html($city->city_name) . '</td>
                  <td>' . esc_html($city->seo_title) . '</td>
                  <td>' . esc_html($city->seo_desc) . '</td>
                  <td>
                      <a href="?page=city_page_seo&action=edit&id=' . esc_attr($city->id) . '">Edit</a> | 
                      <a href="' . esc_url($delete_url) . '" onclick="return confirm(\'Are you sure you want to delete this city?\')">Delete</a>
                  </td>
              </tr>';
        }
    } else {
        echo '<tr><td colspan="6">No cities found.</td></tr>';
    }

    echo '</tbody></table>
          </form>';

    // Display pagination
    echo '<div class="tablenav-pages">';
    echo paginate_links(array(
        'base' => add_query_arg('paged', '%#%'),
        'format' => '',
        'prev_text' => __('&laquo; Previous'),
        'next_text' => __('Next &raquo;'),
        'total' => $total_pages,
        'current' => $current_page,
    ));
    echo '</div>';

    echo '</div>'; // Close main container
}