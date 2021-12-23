<?php

namespace pxlrbt\Cf7Cleverreach;

use pxlrbt\Cf7Cleverreach\Cleverreach\ApiCredentials;
use Exception;

class UpdateCleverreachTokenService
{
    public function __construct(Container $container)
    {
        $this->logger = $container->getLogger();
        $this->notifier = $container->getNotifier();
        $this->api = $container->getApi();

        add_action(Plugin::$prefix . 'update_token', [$this, 'refreshToken']);

        if (! wp_next_scheduled(Plugin::$prefix . 'update_token')) {
            $this->logger->info('Scheduled RefreshToken');
            wp_schedule_event(time(), 'weekly', Plugin::$prefix . 'update_token');
        }
    }

    public function refreshToken()
    {
        $this->logger->info('Refreshing API token.');

        if (ApiCredentials::token() === null) {
            return;
        }

        if (ApiCredentials::refreshToken() === null) {
            $this->container->getNotifier()->dispatch(
                Notification::create(
                    sprintf(
                        __('Cannot automatically refresh API token as refresh token is empty. Please go to <a href="%s">CF7 to CleverReach settings</a> and manually refresh the API token.', 'wpcf7-cleverreach'),
                        esc_url(admin_url('admin.php?page=cf7-cleverreach'))
                    )
                )
                    ->title('CF7 to CleverReach: ')
                    ->id('refresh-token')
                    ->type('warning')
                    ->dismissible(true)
                    ->persistent(true)
            );

            $this->logger->warning('Refresh token is empty.');
            return;
        }

        try {
            $result = $this->api->refreshApiToken(
                ApiCredentials::$clientId,
                ApiCredentials::$clientSecret,
                ApiCredentials::refreshToken()
            );
        } catch (Exception $e) {
            $this->logger->error('Failed to refresh token.', [$e]);
            $this->notifier->error(__('Could not retrieve API token: ', 'wpcf7-cleverreach') . $e->getMessage());
            return;
        }

        if (isset($result->error_description)) {
            $this->logger->error('Failed to refresh token.', [$result]);
            $this->notifier->error(__('Could not retrieve API token: ', 'wpcf7-cleverreach') . $result->error_description);
            return;
        }

        if (isset($result->access_token)) {
            ApiCredentials::updateFromResult($result);
        }
    }
}
