<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized access', 'wc-zone-based-delivery'));
}

if(isset($_POST['zone_index']) && $_POST['action'] == "wc_zone_based_delivery_update_zone" && isset($_POST['wc_zone_name']) && isset($_POST['wc_zone_type']) && isset($_POST['wc_zone_state']))
{
    check_admin_referer('wc_zone_based_delivery_nonce', 'nonce');
    $zones = get_option('wc_zones', []);
    $index = $_POST['zone_index'];

    // Sanitize and prepare data
    $zone_name = sanitize_text_field($_POST['wc_zone_name']);
    $zone_type = sanitize_text_field($_POST['wc_zone_type']);
    $zone_state = sanitize_text_field($_POST['wc_zone_state']);
    $zone_postcodes = isset($_POST['wc_zone_postcodes']) && is_array($_POST['wc_zone_postcodes'])
        ? array_map('sanitize_text_field', $_POST['wc_zone_postcodes'])
        : [];
    $zone_custom_message = sanitize_textarea_field($_POST['wc_zone_custom_message']);

    $zones[$index] = [
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
elseif($_POST['action'] == "wc_zone_based_delivery_save_zone" && isset($_POST['wc_zone_name']) && isset($_POST['wc_zone_type']) && isset($_POST['wc_zone_state']))
{

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
        <input type="hidden" name="zone_index" value="">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="wc_zone_name">
                        <?php _e('Zone Name', 'wc-zone-based-delivery'); ?>
                    </label>
                    <input type="text" name="wc_zone_name" id="wc_zone_name" class="form-control form-control-sm" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="wc_zone_type">
                        <?php _e('Regional/Metro', 'wc-zone-based-delivery'); ?>
                    </label>
                    <br>
                    <select id="wc_zone_type" name="wc_zone_type" class="form-select" style="width: 100%;" required>
                        <option value="regional"><?php _e('Regional', 'wc-zone-based-delivery'); ?></option>
                        <option value="metro"><?php _e('Metro', 'wc-zone-based-delivery'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="state">
                        <?php _e('State', 'wc-zone-based-delivery'); ?>
                    </label><br>
                    <select id="state" name="wc_zone_state" class="form-select" style="width: 100%;" required>
                        <option value="" disabled selected>Select State</option>
                        <?php foreach ($state_options as $state) : ?>
                            <option value="<?php echo esc_attr($state); ?>">
                                <?php echo esc_html($state); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row" id="postcode-row" style="display:none;">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="postcodes-container">
                        <?php _e('Postcodes', 'wc-zone-based-delivery'); ?>
                    </label><br>
                    <select id="postcodes-container" name="wc_zone_postcodes[]" multiple="multiple" class="form-select wc-zone-state" style="width: 100%;" required>
                        <option value="">Select Post Codes</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="wc_zone_custom_message">
                        <?php _e('Custom Message', 'wc-zone-based-delivery'); ?>
                    </label>
                    <textarea name="wc_zone_custom_message" id="wc_zone_custom_message" class="form-control"></textarea>
                </div>
            </div>
        </div>

        <button type="submit" name="submit" class="mb-3 btn btn-primary">
            <?php _e('Save Zone', 'wc-zone-based-delivery'); ?>
        </button>
    </form>


    <h2 class="mt-3"><?php _e('Existing Zones', 'wc-zone-based-delivery'); ?></h2>

    <?php if (!empty($zones)) : ?>
        <table class="table table-striped table-bordered">
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
            <tbody id="zone-list">
            <?php foreach ($zones as $index => $zone) : ?>
                <tr>
                    <td><?php echo esc_html($index + 1); ?></td> <!-- Display number -->
                    <td><?php echo esc_html($zone['zone_name']); ?></td>
                    <td><?php echo esc_html($zone['type']); ?></td>
                    <td><?php echo esc_html($zone['state']); ?></td>
                    <td><?php echo implode(', ', $zone['postcodes']); ?></td>
                    <td><?php echo esc_html($zone['custom_message']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-secondary edit-zone" data-index="<?php echo esc_attr($index); ?>">
                            <?php _e('Edit', 'wc-zone-based-delivery'); ?>
                        </button>
                        <button class="btn btn-sm btn-danger remove-zone" data-index="<?php echo esc_attr($index); ?>">
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

        $('#zone-list').on('click', '.edit-zone', function() {
            var index = $(this).data('index');

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                method: 'POST',
                data: {
                    action: 'wc_edit_zone',
                    index: index,
                    nonce: '<?php echo wp_create_nonce('wc_zone_based_delivery_nonce', 'nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        let zone = response.data['zone'];
                        $('[name="zone_index"]').val(index);
                        $('[name="wc_zone_name"]').val(zone['zone_name']);
                        $('[name="wc_zone_type"]').val(zone['type']).trigger('change');
                        $('[name="wc_zone_state"]').val(zone['state']).trigger('change');
                        $('[name="wc_zone_custom_message"]').val(zone['custom_message']);
                        //$('[name="wc_zone_postcodes"]').val().trigger('change');
                        var selectedValues = zone['postcodes'];
                        var interval = setInterval(function () {
                            var selectField = $('#postcodes-container');
                            if (selectField.hasClass('select2-hidden-accessible')) {
                                selectField.val(selectedValues).trigger('change');

                                clearInterval(interval);
                            }
                        }, 100);

                        $('[name="action"]').val("wc_zone_based_delivery_update_zone");
                        $('[name="submit"]').val("Update Zone");
                        $('[name="submit"]').text("Update Zone");
                    }
                },
                error: function() {
                    const message = "<?php _e('An error occurred while fetching the zone.', 'wc-zone-based-delivery'); ?>";
                    const status = 'error';
                    const encodedMessage = encodeURIComponent(message);

                    const redirectUrl = `${adminUrl}&status=${status}&message=${encodedMessage}`;
                    window.location.href = redirectUrl;
                }
            });
        })

        // Handle zone removal
        $('#zone-list').on('click', '.remove-zone', function() {
            if (confirm('<?php _e('Are you sure you want to delete this zone?', 'wc-zone-based-delivery'); ?>')) {
                var index = $(this).data('index');
                const adminUrl = '/wp-admin/admin.php?page=wc-zone-based-delivery';

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'wc_remove_zone',
                        index: index,
                        nonce: '<?php echo wp_create_nonce('wc_zone_based_delivery_nonce', 'nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {

                            const message = "<?php _e('Zone removed successfully.', 'wc-zone-based-delivery'); ?>";
                            const status = 'success';
                            const encodedMessage = encodeURIComponent(message);

                            const redirectUrl = `${adminUrl}&status=${status}&message=${encodedMessage}`;

                            window.location.href = redirectUrl;
                        } else {

                            const message = "<?php _e('Zone not found.', 'wc-zone-based-delivery'); ?>";
                            const status = 'error';
                            const encodedMessage = encodeURIComponent(message);

                            const redirectUrl = `${adminUrl}&status=${status}&message=${encodedMessage}`;

                            window.location.href = redirectUrl;
                        }
                    },
                    error: function() {
                        const message = "<?php _e('An error occurred while removing the zone.', 'wc-zone-based-delivery'); ?>";
                        const status = 'error';
                        const encodedMessage = encodeURIComponent(message);

                        const redirectUrl = `${adminUrl}&status=${status}&message=${encodedMessage}`;
                        window.location.href = redirectUrl;
                    }
                });
            }
        });
    });

</script>

<?php

/*function wc_remove_zone() {
    // Check the nonce for security
    check_ajax_referer('wc_zone_based_delivery_nonce', 'nonce');

    // Get the index of the zone to remove
    $index = isset($_POST['index']) ? intval($_POST['index']) : null;

    var_dump($index);

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
add_action('wp_ajax_wc_remove_zone', 'wc_remove_zone');
add_action('wp_ajax_nopriv_wc_remove_zone', 'wc_remove_zone');*/

?>
