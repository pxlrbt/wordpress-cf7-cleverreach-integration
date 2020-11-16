<?php
    use pxlrbt\Cf7Cleverreach\Plugin;
    use pxlrbt\Cf7Cleverreach\CF7\ApiCredentials;
    use pxlrbt\Cleverreach\Api as CleverreachApi;
?>
<div class="wrap">
    <h1><?php _e('Settings', 'wordpress'); ?> › <?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
        $redirectUrl = esc_url(admin_url('admin.php?page=cf7-cleverreach'));
    ?>


    <table class="form-table">
        <tbody>
            <tr>
                <th>
                    <label for="wpcf7-cleverreach_api-token">API Token:</label>
                </th>
                <td>
                    <input
                        size="200"
                        type="text"
                        id="wpcf7-cleverreach_api-token"
                        value="<?php echo ApiCredentials::token(); ?>"
                        readonly
                    >
                </td>
            </tr>
            <tr>
                <th>
                    <label for="wpcf7-cleverreach_api-refresh-token">Refresh Token:</label>
                </th>
                <td>
                    <input
                        size="200"
                        type="text"
                        id="wpcf7-cleverreach_api-refresh-token"
                        value="<?php echo ApiCredentials::refreshToken(); ?>"
                        readonly
                    >
                </td>
            </tr>
            <?php if (ApiCredentials::expires() != null): ?>
                <tr>
                    <th><label for="wpcf7-cleverreach_api-expires">Expires:</label></th>
                    <td><?php echo date('Y-m-d', ApiCredentials::expires()); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a
        class="button button-primary"
        href="<?php echo CleverreachApi::generateAuthLink(Plugin::$clientId, $redirectUrl); ?>"
    >
        Get CleverReach API Token
    </a>
</div>
