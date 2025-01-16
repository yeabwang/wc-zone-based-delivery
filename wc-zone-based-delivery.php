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

// Admin page callback
function wc_zone_based_delivery_admin_page() {
    include plugin_dir_path(__FILE__) . 'templates/admin-page.php';
}

// Enqueue scripts and styles
function wc_zone_based_delivery_enqueue_scripts() {
    wp_enqueue_script('wc-zone-based-delivery-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', ['jquery'], '1.0', true);
    wp_localize_script('wc-zone-based-delivery-script', 'ZoneDelivery', ['ajax_url' => admin_url('admin-ajax.php')]);
    wp_enqueue_style('wc-zone-based-delivery-style', plugin_dir_url(__FILE__) . 'assets/css/styles.css');
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
