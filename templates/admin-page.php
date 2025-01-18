<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized access', 'wc-zone-based-delivery'));
}

if(isset($_POST['wc_zone_name']) && isset($_POST['wc_zone_type']) && isset($_POST['wc_zone_state']))
{

// Verify nonce for security
    check_admin_referer('wc_zone_based_delivery_nonce', 'nonce');
    $zones = get_option('wc_zones', []);

// Sanitize and prepare data
    $zone_name = sanitize_text_field($_POST['wc_zone_name']);
    $zone_type = sanitize_text_field($_POST['wc_zone_type']);
    $zone_state = sanitize_text_field($_POST['wc_zone_state']);
    $zone_postcodes = isset($_POST['wc_zone_postcodes']) && is_array($_POST['wc_zone_postcodes'])
        ? array_map('sanitize_text_field', $_POST['wc_zone_postcodes'])
        : [];
    $zone_custom_message = sanitize_textarea_field($_POST['wc_zone_custom_message']);

// Add the new zone to the array
    $zones[] = [
        'zone_name' => $zone_name,
        'type' => $zone_type,
        'state' => $zone_state,
        'postcodes' => $zone_postcodes,
        'custom_message' => $zone_custom_message,
    ];

// Save the updated zones back to the database
    update_option('wc_zones', $zones);

// Redirect back with a success message
    wp_redirect(admin_url('admin.php?page=wc-zone-based-delivery&status=success'));
    exit;
}

$state_options = [
    'New South Wales', 'Victoria', 'Queensland', 'South Australia',
    'Western Australia', 'Tasmania', 'Australian Capital Territory', 'Northern Territory'
];
$zones = get_option('wc_zones', []);// Use get_option to retrieve saved zones

$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';


?>

<div class="wrap">
    <h1><?php _e('Zone Based Delivery', 'wc-zone-based-delivery'); ?></h1>

    <!-- Display success or error messages -->
    <?php if ($status === 'success') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Your changes were saved successfully.', 'wc-zone-based-delivery'); ?></p>
        </div>
    <?php elseif ($status === 'error') : ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e('There was an error saving your changes. Please try again.', 'wc-zone-based-delivery'); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url('admin.php?page=wc-zone-based-delivery'); ?>">
        <!-- Add required fields for WordPress form handling -->
        <input type="hidden" name="action" value="wc_zone_based_delivery_save_zone">
        <?php wp_nonce_field('wc_zone_based_delivery_nonce', 'nonce'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Zone Name', 'wc-zone-based-delivery'); ?></th>
                <td><input type="text" name="wc_zone_name" required /></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Regional/Metro', 'wc-zone-based-delivery'); ?></th>
                <td>
                    <select id="wc_zone_type" name="wc_zone_type" required>
                        <option value="regional"><?php _e('Regional', 'wc-zone-based-delivery'); ?></option>
                        <option value="metro"><?php _e('Metro', 'wc-zone-based-delivery'); ?></option>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('State', 'wc-zone-based-delivery'); ?></th>
                <td>
                    <select id="state" name="wc_zone_state" required>
                        <option value="" disabled selected >Select State</option>
                        <?php foreach ($state_options as $state) : ?>
                            <option value="<?php echo esc_attr($state); ?>">
                                <?php echo esc_html($state); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr valign="top" id="postcode-row" style="display:none;">
                <th scope="row"><?php _e('Postcodes', 'wc-zone-based-delivery'); ?></th>
                <td>
                    <select id="postcodes-container" name="wc_zone_postcodes[]" multiple="multiple" required class="wc-zone-state">
                        <option value="">Select Post Codes</option>
                    </select>

                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Custom Message', 'wc-zone-based-delivery'); ?></th>
                <td><textarea name="wc_zone_custom_message"></textarea></td>
            </tr>
        </table>

        <?php submit_button(__('Save Zone', 'wc-zone-based-delivery')); ?>
    </form>

    <h2><?php _e('Existing Zones', 'wc-zone-based-delivery'); ?></h2>

    <?php if (!empty($zones)) : ?>
        <table class="widefat fixed striped">
            <thead>
            <tr>
                <th><?php _e('#', 'wc-zone-based-delivery'); ?></th>
                <th><?php _e('Zone Name', 'wc-zone-based-delivery'); ?></th>
                <th><?php _e('Type', 'wc-zone-based-delivery'); ?></th>
                <th><?php _e('State', 'wc-zone-based-delivery'); ?></th>
                <th><?php _e('Postcodes', 'wc-zone-based-delivery'); ?></th>
                <th><?php _e('Custom Message', 'wc-zone-based-delivery'); ?></th>
                <th><?php _e('Actions', 'wc-zone-based-delivery'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($zones as $index => $zone) : ?>
                <tr>
                    <td><?php echo esc_html($index + 1); ?></td> <!-- Display number -->
                    <td><?php echo esc_html($zone['zone_name']); ?></td>
                    <td><?php echo esc_html($zone['type']); ?></td>
                    <td><?php echo esc_html($zone['state']); ?></td>
                    <td><?php echo implode(', ', $zone['postcodes']); ?></td>
                    <td><?php echo esc_html($zone['custom_message']); ?></td>
                    <td>
                        <button class="button button-secondary remove-zone" data-index="<?php echo esc_attr($index); ?>">
                            <?php _e('Remove', 'wc-zone-based-delivery'); ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php _e('No zones added yet.', 'wc-zone-based-delivery'); ?></p>
    <?php endif; ?>

</div>

<script type="text/javascript">

    jQuery(document).ready(function($) {

        // Populate postcodes when state is selected
        $('#state').on('change', function() {
            var state = $(this).val();

            $('#postcode-row').hide();
            $('#postcodes-container').empty();

            if (state) {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'wc_get_postcodes',
                        state: state,
                        nonce: '<?php echo wp_create_nonce('wc_zone_based_delivery_nonce', 'nonce'); ?>'
                    },
                    success: function(response) {

                        var postcodes = response.data.postcodes;
                        if (postcodes.length > 0) {
                            // Show the postcode row
                            $('#postcode-row').show();

                            // Create checkboxes for postcodes
                            var container = $('#postcodes-container');
                            $('#postcodes-container').select2({
                                placeholder: "Select Post Codes",
                                allowClear: true,
                                width: 'auto'
                            });

                            postcodes.forEach(function(postcode) {
                                container.append('<option value="' + postcode + '">' + postcode + '</option>');
                            });
                        }else {
                            alert(response.data.message); // Show error if no postcodes found
                        }
                    },
                    error: function() {
                        alert('An error occurred while fetching postcodes.');
                    }
                });
            }
        });
    });

    jQuery(document).ready(function($) {
        // Handle zone removal
        $('#zone-list').on('click', '.remove-zone', function() {
            if (confirm('<?php _e('Are you sure you want to delete this zone?', 'wc-zone-based-delivery'); ?>')) {
                var index = $(this).data('index');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'wc_remove_zone',
                        index: index,
                        nonce: '<?php echo wp_create_nonce('wc_zone_based_delivery_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('<?php _e('Zone removed successfully.', 'wc-zone-based-delivery'); ?>');
                            location.reload(); // Reload the page to update the zones list
                        } else {
                            alert(response.data.message);
                        }
                    },
                    error: function() {
                        alert('<?php _e('An error occurred while removing the zone.', 'wc-zone-based-delivery'); ?>');
                    }
                });
            }
        });
    });

</script>

<?php

add_action('wp_ajax_wc_remove_zone', 'wc_remove_zone');
function wc_remove_zone() {
    // Check the nonce for security
    check_ajax_referer('wc_zone_based_delivery_nonce', 'nonce');

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

?>
