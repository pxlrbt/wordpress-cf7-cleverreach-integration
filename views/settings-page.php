<div class="wrap">
    <h1><?php _e('Settings', 'wordpress'); ?> › <?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
        use Pixelarbeit\Cleverreach\Api as CleverreachApi;

        $clientId = get_option('wpcf7-cleverreach_client-id', null);
        $clientSecret = get_option('wpcf7-cleverreach_client-secret', null);
        $apiToken = get_option('wpcf7-cleverreach_api-token', null);

        $apiExpires = get_option('wpcf7-cleverreach_api-expires', null);
        
        if ($apiExpires != null) {
            $apiExpires = date('Y-m-d', $apiExpires);
        }

        $redirectUrl = esc_url(admin_url('options-general.php?page=cf7-cleverreach'));
    ?>

    <form action="" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="wpcf7-cleverreach_client-id">Client ID:</label></th>
                    <td><input size="50" type="text" name="wpcf7-cleverreach_client-id" id="wpcf7-cleverreach_client-id" value="<?php echo $clientId; ?>"></td>
                </tr>
                <tr>
                    <th><label for="wpcf7-cleverreach_client-secret">Client Secret:</label></th>
                    <td><input size="50" type="text" name="wpcf7-cleverreach_client-secret" id="wpcf7-cleverreach_client-secret" value="<?php echo $clientSecret; ?>"></td>
                </tr>
            </tbody>
        </table>
        <?php
            wp_nonce_field( 'wpcf7-cleverreach' );
            submit_button( __('Save credentials'));
        ?>

                
    </form>
    <hr>
    

    <?php if (empty($clientId) == false && empty($clientSecret) == false): ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="wpcf7-cleverreach_api-token">API Token:</label></th>
                    <td><input size="200" type="text" name="wpcf7-cleverreach_api-token" id="wpcf7-cleverreach_api-token" value="<?php echo $apiToken; ?>"></td>
                </tr>            
                <tr>
                    <th><label for="wpcf7-cleverreach_api-expires">Expires:</label></th>
                    <td><?php echo $apiExpires; ?></td>
                </tr>            
            </tbody>
        </table>
        
        <a class="button button-primary"
            href="<?php echo CleverreachApi::generateAuthLink($clientId, $redirectUrl); ?>">Get Cleverreach API Token</a>
    <?php endif; ?>
</div>
