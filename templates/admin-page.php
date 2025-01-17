<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Fetch states and zones
$state_options = wc_zone_based_delivery_get_states_from_api(); // Correct function name
$zones = get_option('wc_zones', []); // Use get_option to retrieve saved zones
?>

<div class="wrap">
    <h1><?php _e('Zone Based Delivery', 'wc-zone-based-delivery'); ?></h1>

    <form method="post" action="options.php">
        <?php 
        settings_fields('wc_zone_based_delivery_options'); 
        do_settings_sections('wc_zone_based_delivery'); 
        ?>
        
        <!-- Add nonce for security -->
        <?php wp_nonce_field('wc_zone_based_delivery_nonce', 'nonce'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Zone Name', 'wc-zone-based-delivery'); ?></th>
                <td><input type="text" name="wc_zone_name" value="<?php echo esc_attr(get_option('wc_zone_name')); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Regional/Metro', 'wc-zone-based-delivery'); ?></th>
                <td>
                    <select name="wc_zone_type">
                        <option value="regional"><?php _e('Regional', 'wc-zone-based-delivery'); ?></option>
                        <option value="metro"><?php _e('Metro', 'wc-zone-based-delivery'); ?></option>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('State', 'wc-zone-based-delivery'); ?></th>
                <td>
                    <select id="state" name="wc_zone_state">
                        <?php if (!empty($state_options)) : ?>
                            <?php foreach ($state_options as $state) : ?>
                                <option value="<?php echo esc_attr($state['StateShort']); ?>"><?php echo esc_html($state['State']); ?></option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <option value=""><?php _e('No States Available', 'wc-zone-based-delivery'); ?></option>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Postcodes', 'wc-zone-based-delivery'); ?></th>
                <td id="postcodes-container">
                    <!-- Checkboxes populated by JavaScript -->
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Custom Message', 'wc-zone-based-delivery'); ?></th>
                <td><textarea name="wc_zone_custom_message"><?php echo esc_textarea(get_option('wc_zone_custom_message')); ?></textarea></td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>

    <h2>Existing Zones</h2>
    <ul>
        <?php foreach ($zones as $zone) : ?>
            <li><?php echo esc_html($zone['zone_name']); ?> - <?php echo esc_html($zone['custom_message']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Populate postcodes when state is selected
        $('#state').on('change', function() {
            var state = $(this).val();
            if (state) {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'wc_get_postcodes',
                        state: state,
                        nonce: '<?php echo wp_create_nonce('wc_zone_based_delivery_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var postcodes = response.data.postcodes;
                            var container = $('#postcodes-container');
                            container.empty();
                            postcodes.forEach(function(postcode) {
                                container.append('<label><input type="checkbox" name="wc_zone_postcodes[]" value="' + postcode + '"> ' + postcode + '</label><br>');
                            });
                        } else {
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
</script>
