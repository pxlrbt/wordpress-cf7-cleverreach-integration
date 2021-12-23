<?php
    use pxlrbt\Cf7Cleverreach\Cleverreach\ApiCredentials;
    use pxlrbt\Cf7Cleverreach\Cleverreach\Api as CleverreachApi;
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
                    <label for="wpcf7-cleverreach_api-token">
                        <?php _e('API token', 'wpcf7-cleverreach'); ?>:
                    </label>
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
                    <label for="wpcf7-cleverreach_api-refresh-token">
                        <?php _e('Refresh token', 'wpcf7-cleverreach'); ?>:
                    </label>
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
                    <th>
                        <label for="wpcf7-cleverreach_api-expires">
                            <?php _e('Expires', 'wpcf7-cleverreach'); ?>:
                        </label>
                    </th>
                    <td>
                        <?php echo date('Y-m-d', ApiCredentials::expires()); ?>
                        <small>(<?php _e('Every API token is only valid for a month, but should be renewed weekly', 'wpcf7-cleverreach'); ?>)</small>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a
        class="button button-primary"
        href="<?php echo CleverreachApi::generateAuthLink(ApiCredentials::$clientId, $redirectUrl); ?>"
    >
        <?php _e('Get CleverReach API token', 'wpcf7-cleverreach'); ?>
    </a>
</div>
