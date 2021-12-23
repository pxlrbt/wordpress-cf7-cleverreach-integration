<?php

namespace pxlrbt\Cf7Cleverreach\Controllers;

use pxlrbt\Cleverreach\Api as CleverreachApi;
use pxlrbt\Cf7Cleverreach\Container;
use pxlrbt\Cf7Cleverreach\Plugin;
use pxlrbt\Cf7Cleverreach\Cleverreach\ApiCredentials;
use WPCF7_ContactForm;
use Exception;

class SettingsPageController
{
	public function __construct(Container $container)
	{
        $this->container = $container;
        $this->notifier = $container->getNotifier();
        $this->logger = $container->getLogger();

        add_action('admin_menu', [$this, 'registerMenu']);
    }

    public function registerMenu()
    {
        add_submenu_page(
            'wpcf7',
            __('CF7 to CleverReach', 'wpcf7-cleverreach'),
            __('CF7 to CleverReach', 'wpcf7-cleverreach'),
            'manage_options',
            'cf7-cleverreach',
            [$this, 'printPage']
        );
    }

    public function printPage()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['code'])) {
            $this->getApiToken($_GET['code']);
        }

        include __DIR__ . '/../../views/settings-page.php';
    }

    public function getApiToken($code)
    {
        $api = $this->container->getApi();
        $redirectUrl = esc_url(admin_url('admin.php?page=cf7-cleverreach'));

        try {
            $result = $api->getApiToken(
                ApiCredentials::$clientId,
                ApiCredentials::$clientSecret,
                $code,
                $redirectUrl
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [$e]);
            $this->notifier->error($e->getMessage());
            return;
        }

        if (isset($result->error_description)) {
            $this->logger->error($e->error_description, [$result]);
            $this->notifier->error(__('Could not retrieve api token: ', 'wpcf7-cleverreach') . $result->error_description);
            return;
        }

        if (isset($result->access_token)) {
            ApiCredentials::updateFromResult($result);
            $this->notifier->success(__('Api token updated', 'wpcf7-cleverreach'));
        }
    }
}
