<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Fetch states from the API
$state_options = get_states_from_api();
$zones = get_zones_from_db();
?>

<div class="wrap">
    <h1><?php _e('Zone Based Delivery', 'wc-zone-based-delivery'); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields('wc_zone_based_delivery_options'); ?>
        <?php do_settings_sections('wc_zone_based_delivery'); ?>
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
                        <!-- Options populated by JavaScript -->
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
