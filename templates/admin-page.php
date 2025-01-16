<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="save_zone">
    <?php wp_nonce_field('save_zone_action', 'save_zone_nonce'); ?>
    <!-- Other form fields -->
</form>
