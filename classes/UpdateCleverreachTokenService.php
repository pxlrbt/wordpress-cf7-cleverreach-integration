<?php

namespace pxlrbt\Cf7Cleverreach;

use pxlrbt\Cf7Cleverreach\CF7\ApiCredentials;
use Exception;

class UpdateCleverreachTokenService
{
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        $this->logger = $plugin->logger;
        $this->notifier = $plugin->notifier;
        $this->api = $plugin->getApi();

        add_action(Plugin::$prefix . 'update_token', [$this, 'refreshToken']);

        if (! wp_next_scheduled(Plugin::$prefix . 'update_token')) {
            $this->logger->info('Scheduled RefreshToken');
            wp_schedule_event(time(), 'weekly', Plugin::$prefix . 'update_token');
        }
    }

    public function refreshToken()
    {
        $this->logger->info('Refreshing API token.');

        if (ApiCredentials::refreshToken() === null) {
            $this->notifier->warning('Cannot refresh API token as refresh token is empty. Please go to CF7 to CleverReach settings and manually refresh the API token.');
            $this->logger->warning('Refresh token is empty.');
            return;
        }

        try {
            $result = $this->api->refreshApiToken(
                Plugin::$clientId,
                Plugin::$clientSecret,
                ApiCredentials::refreshToken()
            );
        } catch (Exception $e) {
            $this->logger->error('Failed to refresh token.', [$e]);
            $this->notifier->error('Could not retrieve API token: '. $e->getMessage());
            return;
        }

        if (isset($result->error_description)) {
            $this->logger->error('Failed to refresh token.', [$result]);
            $this->notifier->error('Could not retrieve API token: ' . $result->error_description);
            return;
        }

        if (isset($result->access_token)) {
            ApiCredentials::updateFromResult($result);
        }
    }
}
