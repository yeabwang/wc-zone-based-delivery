<?php
/*
Plugin Name: WC Zone Based Delivery
Description: A plugin to manage zone-based delivery.
Version: 1.0
Author: Yeabsira Tesfaye
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';
include_once plugin_dir_path(__FILE__) . 'includes/api-handler.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-handlers.php';

// Register admin menu
function wc_zone_based_delivery_admin_menu() {
    add_menu_page(
        __('Zone Based Delivery', 'wc-zone-based-delivery'),
        __('Zone Based Delivery', 'wc-zone-based-delivery'),
        'manage_options',
        'wc-zone-based-delivery',
        'wc_zone_based_delivery_admin_page'
    );
}
add_action('admin_menu', 'wc_zone_based_delivery_admin_menu');
add_action('admin_init', 'wc_zone_based_delivery_register_settings');


function wc_zone_based_delivery_register_settings() {
    // Register settings
    register_setting('wc_zone_based_delivery_options', 'wc_zone_name');
    register_setting('wc_zone_based_delivery_options', 'wc_zone_type');
    register_setting('wc_zone_based_delivery_options', 'wc_zone_state');
    register_setting('wc_zone_based_delivery_options', 'wc_zone_postcodes');
    register_setting('wc_zone_based_delivery_options', 'wc_zone_custom_message');
}

// Admin page callback
function wc_zone_based_delivery_admin_page() {
    include plugin_dir_path(__FILE__) . 'templates/admin-page.php';
}

// Enqueue scripts and styles
function wc_zone_based_delivery_enqueue_scripts() {
    wp_enqueue_script('wc-zone-based-delivery-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', ['jquery'], '1.0', true);
    wp_localize_script('wc-zone-based-delivery-script', 'ZoneDelivery', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'states_action' => 'wc_get_states' // Action to fetch states via AJAX
    ]);
    wp_enqueue_style('wc-zone-based-delivery-style', plugin_dir_url(__FILE__) . 'assets/css/styles.css');
    // Load Bootstrap CSS
    wp_enqueue_style(
        'bootstrap-css',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css',
        [],
        '4.5.2'
    );

    // Load Bootstrap JS and Popper.js
    wp_enqueue_script(
        'bootstrap-js',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js',
        ['jquery', 'popper-js'],
        '4.5.2',
        true
    );

    wp_enqueue_script(
        'popper-js',
        'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js',
        [],
        '1.16.1',
        true
    );
}
add_action('admin_enqueue_scripts', 'wc_zone_based_delivery_enqueue_scripts');
add_action('wp_enqueue_scripts', 'wc_zone_based_delivery_enqueue_scripts');

// Register shortcode for check availability form
function wc_zone_based_delivery_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/check-availability-form.php';
    return ob_get_clean();
}
add_shortcode('wc_check_availability', 'wc_zone_based_delivery_shortcode');
