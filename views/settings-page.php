<?php
    use pxlrbt\Cf7Cleverreach\Plugin;
    use pxlrbt\Cleverreach\Api as CleverreachApi;
?>
<div class="wrap">
    <h1><?php _e('Settings', 'wordpress'); ?> › <?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
        $redirectUrl = esc_url(admin_url('admin.php?page=cf7-cleverreach'));
        $apiToken = get_option('wpcf7-cleverreach_api-token', null);
        $apiExpires = get_option('wpcf7-cleverreach_api-expires', null);

        if ($apiExpires != null) {
            $apiExpires = date('Y-m-d', $apiExpires);
        }
    ?>


    <table class="form-table">
        <tbody>
            <tr>
                <th><label for="wpcf7-cleverreach_api-token">API Token:</label></th>
                <td><input size="200" type="text" name="wpcf7-cleverreach_api-token" id="wpcf7-cleverreach_api-token" value="<?php echo $apiToken; ?>"></td>
            </tr>
            <?php if ($apiExpires != null): ?>
                <tr>
                    <th><label for="wpcf7-cleverreach_api-expires">Expires:</label></th>
                    <td><?php echo $apiExpires; ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a class="button button-primary"
        href="<?php echo CleverreachApi::generateAuthLink(Plugin::$clientId, $redirectUrl); ?>">Get CleverReach API Token</a>
</div>
