<?php
// Fetch states from the API
$state_options = get_states_from_api();
$zones = get_zones_from_db();
?>

<div class="wrap">
    <h1>Zone-Based Delivery</h1>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="save_zone">
        <table class="form-table">
            <tr>
                <th><label for="zone_name">Zone Name</label></th>
                <td><input type="text" name="zone_name" id="zone_name" required></td>
            </tr>
            <tr>
                <th><label for="regional_metro">Regional/Metro</label></th>
                <td>
                    <select name="regional_metro" id="regional_metro" required>
                        <option value="metro">Metro</option>
                        <option value="regional">Regional</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="state">State</label></th>
                <td>
                    <select name="state" id="state" required>
                        <?php foreach ($state_options as $state) : ?>
                            <option value="<?php echo esc_attr($state['StateShort']); ?>">
                                <?php echo esc_html($state['State']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="postcodes">Postcodes</label></th>
                <td id="postcodes-container">
                    <!-- Dynamically populated postcodes will go here -->
                </td>
            </tr>
            <tr>
                <th><label for="custom_message">Custom Message</label></th>
                <td><textarea name="custom_message" id="custom_message" required></textarea></td>
            </tr>
        </table>
        <p><input type="submit" class="button-primary" value="Save Zone"></p>
    </form>

    <h2>Existing Zones</h2>
    <ul>
        <?php foreach ($zones as $zone) : ?>
            <li><?php echo esc_html($zone['zone_name']); ?> - <?php echo esc_html($zone['custom_message']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
