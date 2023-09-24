<?php
// Function to update product descriptions by category
function update_product_descriptions_by_category($category_id, $new_description) {
    // Query arguments
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        ),
    );

    // WP Query
    $loop = new WP_Query($args);
    if ($loop->have_posts()) {
        while ($loop->have_posts()) {
            $loop->the_post();
            global $product;

            // Set and save the new short description
            $product->set_short_description($new_description);
            $product->save();
        }
    }
    // Reset WP Query
    wp_reset_query();
}

// Function to update static product descriptions by category
function update_static_product_descriptions_by_category($category_id, $new_description) {
    update_product_descriptions_by_category($category_id, $new_description);
}

// Function to update temporary product descriptions by category
function update_temp_product_descriptions_by_category($category_id, $new_description_temp, $start_date, $end_date) {
    // Logging for debugging
    error_log("Function update_temp_product_descriptions_by_category is running.");
    error_log("Received description: " . $new_description_temp);
    error_log("Received start date: " . $start_date);
    error_log("Received end date: " . $end_date);

    // Query arguments
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        ),
    );

    // WP Query
    $loop = new WP_Query($args);
    if ($loop->have_posts()) {
        while ($loop->have_posts()) {
            $loop->the_post();
            global $product;
            
            // Get the current short description
            $current_short_description = $product->get_short_description();
            
            // Add the temporary description to the short description
            $new_short_description = $current_short_description . "<br><div class='temp-description'>{$new_description_temp}</div>";
            
            // Set and save the new short description
            $product->set_short_description($new_short_description);
            $product->save();
        }
    } else {
        // Log if no products are found in the specified category
        error_log("No products found in the specified category.");
    }
    // Reset WP Query
    wp_reset_query();
}

// Function to show temporary or regular description based on date
function show_temp_description() {
    global $product;

    // Get product ID and meta data
    $product_id = $product->get_id();
    $temp_description = get_post_meta($product_id, 'temp_description', true);
    $start_date = get_post_meta($product_id, 'temp_description_start', true);
    $end_date = get_post_meta($product_id, 'temp_description_end', true);
    $current_date = date('Y-m-d');

    // Show temporary or regular description based on date
    if ($current_date >= $start_date && $current_date <= $end_date) {
        echo htmlspecialchars_decode($temp_description);
    } else {
        echo $product->get_short_description();
    }
}

// Function to handle AJAX request for adding timed banners
add_action('wp_ajax_add_massive_timed_banners', function() {
    // Verify nonce
    if (!isset($_POST['massive_banner_editor_nonce_field']) || !wp_verify_nonce($_POST['massive_banner_editor_nonce_field'], 'massive_banner_editor_nonce')) {
        die('Security check failed');
    }

    // Extract POST data
    $category_id = $_POST['category_id'];
    $new_description_temp = urldecode($_POST['bannercontent']);
    $start_date = $_POST['bannerStart'];
    $end_date = $_POST['bannerExpiry'];

    // Update temporary product descriptions
    update_temp_product_descriptions_by_category($category_id, $new_description_temp, $start_date, $end_date);

    // End AJAX request
    wp_die();
});

// Function to enqueue admin scripts
function enqueue_my_admin_scripts() {
    // Enqueue SweetAlert2 and custom JS
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@10', array('jquery'), null, true);
    wp_enqueue_script(
        'massive-description-editor-js',
        plugin_dir_url(dirname(__FILE__)) . 'assets/js/massive-description-editor-js.js',
        array('jquery')
    );
    // Localize script for AJAX
    wp_localize_script('massive-description-editor-js', 'frontendajax', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}

// Hook to enqueue admin scripts
add_action('admin_enqueue_scripts', 'enqueue_my_admin_scripts');

// Function to verify nonce in AJAX function
add_action('wp_ajax_update_massive_product_descriptions', function() {
    // Verify nonce
    if (!isset($_POST['massive_desc_editor_nonce_field']) || !wp_verify_nonce($_POST['massive_desc_editor_nonce_field'], 'massive_desc_editor_nonce')) {
        die('Security check failed');
    }

    // Extract POST data
    $category_id = $_POST['category_id'];
    $new_description = $_POST['new_description'];

    // Update product descriptions
    update_product_descriptions_by_category($category_id, $new_description);

    // End AJAX request
    wp_die();
});
