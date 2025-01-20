<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Ensure function is only declared once
if (!function_exists('wc_zone_based_delivery_check_availability')) {
    // AJAX Handler for checking delivery availability
    function wc_zone_based_delivery_check_availability() {
        // Check the nonce for security
        /*if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_zone_based_delivery_nonce')) {
            wp_send_json_error(['message' => __('Nonce verification failed', 'wc-zone-based-delivery')]);
        }*/

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
        /*if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_zone_based_delivery_nonce')) {
            wp_send_json_error(['message' => __('Nonce verification failed', 'wc-zone-based-delivery')]);
        }*/

        // Sanitize the state input
        $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';

        if (empty($state)) {
            wp_send_json_error(['message' => __('State is required', 'wc-zone-based-delivery')]);
        }

        /*// Fetch locations from the API
        $locations = wc_zone_based_delivery_get_states_from_api();
        
        if (empty($locations)) {
            wp_send_json_error(['message' => __('Failed to fetch locations. Please try again later.', 'wc-zone-based-delivery')]);
        }

        $postcodes = [];
        foreach ($locations as $location) {
            if (isset($location['StateShort']) && $location['StateShort'] === $state) {
                $postcodes[] = isset($location['Postcode']) ? $location['Postcode'] : '';
            }
        }*/

        $postcode_ranges = [
            'New South Wales' => [2000, 2599],
            'Victoria' => [3000, 3999],
            'Queensland' => [4000, 4999],
            'South Australia' => [5000, 5999],
            'Western Australia' => [6000, 6999],
            'Tasmania' => [7000, 7999],
            'Australian Capital Territory' => [200, 299], // Leading zero not needed in PHP
            'Northern Territory' => [800, 999],          // Leading zero not needed in PHP
        ];

        // Check if the state is valid
        if (!isset($postcode_ranges[$state])) {
            wp_send_json_error(['message' => __('Failed to fetch locations. Please try again later.', 'wc-zone-based-delivery')]);
        }

        // Get the range for the given state
        [$start, $end] = $postcode_ranges[$state];

        // Generate all postcodes in the range
        $postcodes = [];
        for ($i = $start; $i <= $end; $i++) {
            $postcodes[] = str_pad($i, 4, '0', STR_PAD_LEFT); // Ensure 4 digits
        }

        //return $postcodes;

        if (empty($postcodes)) {
            wp_send_json_error(['message' => __('No postcodes found for the selected state.', 'wc-zone-based-delivery')]);
        }

        wp_send_json_success(['postcodes' => $postcodes]);
    }
}
add_action('wp_ajax_wc_get_postcodes', 'wc_zone_based_delivery_get_postcodes');
add_action('wp_ajax_nopriv_wc_get_postcodes', 'wc_zone_based_delivery_get_postcodes');


if (!function_exists('wc_zone_based_delivery_edit_zone')) {
    function wc_zone_based_delivery_edit_zone() {

        //check_ajax_referer('wc_zone_based_delivery_nonce', 'nonce');

        // Get the index of the zone to remove
        $index = isset($_POST['index']) ? intval($_POST['index']) : null;

        if ($index === null) {
            wp_send_json_error(['message' => __('Invalid zone index.', 'wc-zone-based-delivery')]);
        }

        // Get the current zones
        $zones = get_option('wc_zones', []);

        if (!isset($zones[$index])) {
            wp_send_json_error(['message' => __('Zone not found.', 'wc-zone-based-delivery')]);
        }

        $zone = $zones[$index];
        wp_send_json_success(['zone' => $zone]);
    }
}
add_action('wp_ajax_wc_edit_zone', 'wc_zone_based_delivery_edit_zone');
add_action('wp_ajax_nopriv_wc_edit_zone', 'wc_zone_based_delivery_edit_zone');


if (!function_exists('wc_zone_based_delivery_remove_zone')) {
    // AJAX Handler for fetching postcodes based on state
    function wc_zone_based_delivery_remove_zone() {

        //check_ajax_referer('wc_zone_based_delivery_nonce', 'nonce');

        // Get the index of the zone to remove
        $index = isset($_POST['index']) ? intval($_POST['index']) : null;

        if ($index === null) {
            wp_send_json_error(['message' => __('Invalid zone index.', 'wc-zone-based-delivery')]);
        }

        // Get the current zones
        $zones = get_option('wc_zones', []);

        if (!isset($zones[$index])) {
            wp_send_json_error(['message' => __('Zone not found.', 'wc-zone-based-delivery')]);
        }

        // Remove the zone
        unset($zones[$index]);

        // Re-index the array and save it
        $zones = array_values($zones);
        update_option('wc_zones', $zones);

        wp_send_json_success();
    }
}

// Register the AJAX handler for fetching postcodes
add_action('wp_ajax_wc_remove_zone', 'wc_zone_based_delivery_remove_zone');
add_action('wp_ajax_nopriv_wc_remove_zone', 'wc_zone_based_delivery_remove_zone');
