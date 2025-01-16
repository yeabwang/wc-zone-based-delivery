<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Save Zone Data Handler
function wc_zone_based_delivery_save_zone() {
    // Check if the user has the required permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('Unauthorized access', 'wc-zone-based-delivery'));
    }

    // Validate nonce for security
    check_admin_referer('save_zone_action', 'save_zone_nonce');

    // Get zones stored in the database
    $zones = get_option('wc_zones', []);

    // Sanitize and add the new zone data
    if (isset($_POST['wc_zone_name'])) {
        $zone_name = sanitize_text_field($_POST['wc_zone_name']);
        $zone_type = sanitize_text_field($_POST['wc_zone_type']);
        $zone_state = sanitize_text_field($_POST['wc_zone_state']);
        $zone_postcodes = array_map('sanitize_text_field', $_POST['wc_zone_postcodes']);
        $zone_custom_message = sanitize_textarea_field($_POST['wc_zone_custom_message']);

        $zones[] = [
            'zone_name' => $zone_name,
            'type' => $zone_type,
            'state' => $zone_state,
            'postcodes' => $zone_postcodes,
            'custom_message' => $zone_custom_message
        ];
    }


    // Update the option with new zone data
    update_option('wc_zones', $zones);

    // Redirect back to the admin page with a success message
    wp_redirect(admin_url('admin.php?page=zone-based-delivery&status=success'));
    exit;
}

// Add the save_zone action to handle form submission
add_action('admin_post_wc_save_zone', 'wc_zone_based_delivery_save_zone');
