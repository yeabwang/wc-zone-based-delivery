<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Ensure function is only declared once
if (!function_exists('wc_zone_based_delivery_check_availability')) {
    // AJAX Handler for checking delivery availability
    function wc_zone_based_delivery_check_availability() {
        // Get the user input from the request
        $input = isset($_POST['input']) ? sanitize_text_field($_POST['input']) : '';

        if (empty($input)) {
            wp_send_json_error(['message' => __('Please provide a valid input', 'wc-zone-based-delivery')]);
        }

        // Retrieve saved zones from the database
        $zones = get_option('wc_zones', []);

        // Default response
        $response = ['message' => __('No matching zones found for your input', 'wc-zone-based-delivery')];

        // Match the input against each zone
        foreach ($zones as $zone) {
            if (
                (stripos($zone['state'], $input) !== false) || // Use stripos() instead of str_contains()
                in_array($input, $zone['postcodes']) ||
                (stripos($zone['zone_name'], $input) !== false) // Use stripos() instead of str_contains()
            ) {
                $response = ['message' => esc_html($zone['custom_message'])];
                break;
            }
        }

        wp_send_json_success($response);
    }
}

// Register the AJAX handlers for logged-in and guest users
add_action('wp_ajax_wc_check_availability', 'wc_zone_based_delivery_check_availability');
add_action('wp_ajax_nopriv_wc_check_availability', 'wc_zone_based_delivery_check_availability');
