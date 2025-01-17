<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Ensure function is only declared once
if (!function_exists('wc_zone_based_delivery_check_availability')) {
    // AJAX Handler for checking delivery availability
    function wc_zone_based_delivery_check_availability() {
        // Check the nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_zone_based_delivery_nonce')) {
            wp_send_json_error(['message' => __('Nonce verification failed', 'wc-zone-based-delivery')]);
        }

        // Get the user input from the request and sanitize
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

// Ensure function is only declared once
if (!function_exists('wc_zone_based_delivery_get_postcodes')) {
    // AJAX Handler for fetching postcodes based on state
    function wc_zone_based_delivery_get_postcodes() {
        // Check the nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_zone_based_delivery_nonce')) {
            wp_send_json_error(['message' => __('Nonce verification failed', 'wc-zone-based-delivery')]);
        }

        // Sanitize the state input
        $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';

        if (empty($state)) {
            wp_send_json_error(['message' => __('State is required', 'wc-zone-based-delivery')]);
        }

        // Fetch locations from the API
        $locations = wc_zone_based_delivery_get_states_from_api();
        
        if (empty($locations)) {
            wp_send_json_error(['message' => __('Failed to fetch locations. Please try again later.', 'wc-zone-based-delivery')]);
        }

        $postcodes = [];
        foreach ($locations as $location) {
            if (isset($location['StateShort']) && $location['StateShort'] === $state) {
                $postcodes[] = isset($location['Postcode']) ? $location['Postcode'] : '';
            }
        }

        if (empty($postcodes)) {
            wp_send_json_error(['message' => __('No postcodes found for the selected state.', 'wc-zone-based-delivery')]);
        }

        wp_send_json_success(['postcodes' => $postcodes]);
    }
}

// Register the AJAX handler for fetching postcodes
add_action('wp_ajax_wc_get_postcodes', 'wc_zone_based_delivery_get_postcodes');
add_action('wp_ajax_nopriv_wc_get_postcodes', 'wc_zone_based_delivery_get_postcodes');
