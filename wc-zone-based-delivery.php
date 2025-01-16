<?php
/**
 * Plugin Name: WooCommerce Zone-Based Delivery Messages
 * Description: Create zone-based delivery messages in WooCommerce.
 * Version: 1.0
 * Author: Yeabsira Tesfaye
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/admin-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-handler.php';

class WC_Zone_Based_Delivery {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('check_delivery_availability', [$this, 'render_shortcode']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Zone-Based Delivery',
            'Delivery Zones',
            'manage_options',
            'zone-based-delivery',
            [$this, 'admin_page'],
            'dashicons-location-alt',
            20
        );
    }

    public function admin_page() {
        include plugin_dir_path(__FILE__) . 'templates/admin-page.php';
    }

    public function render_shortcode() {
        include plugin_dir_path(__FILE__) . 'templates/check-availability-form.php';
    }

    public function enqueue_assets() {
        wp_enqueue_script(
            'zone-delivery-script',
            plugin_dir_url(__FILE__) . 'assets/js/script.js',
            ['jquery'],
            '1.0',
            true
        );
        wp_localize_script('zone-delivery-script', 'ZoneDelivery', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);

        wp_enqueue_style(
            'zone-delivery-style',
            plugin_dir_url(__FILE__) . 'assets/css/styles.css'
        );
    }
}

// Initialize the plugin
new WC_Zone_Based_Delivery();
