<?php
/**
 * Plugin Name: Massive Product Description Editor
 * Description: A plugin to massively edit WooCommerce product descriptions.
 * Version: 1.1
 * Author: Sviluppando sul Web di Ivan Saliani
 * Website: https://sviluppandosulweb.com
 */

// Include other PHP files for admin page and functions
require_once(plugin_dir_path(__FILE__) . 'includes/admin-page.php');
require_once(plugin_dir_path(__FILE__) . 'includes/functions.php');

// Function to add settings link on the plugin page
function bulk_update_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=massive-description-editor">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Add filter to include settings link
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'bulk_update_add_settings_link');

// Function to enqueue admin styles
function enqueue_my_admin_styles() {
    // Load the CSS file
    wp_enqueue_style('my-admin-styles', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}

// Hook the function to admin_enqueue_scripts
add_action('admin_enqueue_scripts', 'enqueue_my_admin_styles');
