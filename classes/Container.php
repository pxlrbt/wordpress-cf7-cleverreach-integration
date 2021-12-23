<?php

namespace pxlrbt\Cf7Cleverreach;

use pxlrbt\Cf7Cleverreach\Vendor\Monolog\Handler\StreamHandler;
use pxlrbt\Cf7Cleverreach\Vendor\Monolog\Logger;
use pxlrbt\Cf7Cleverreach\Vendor\pxlrbt\WordpressNotifier\Notifier;
use pxlrbt\Cf7Cleverreach\Cleverreach\ApiCredentials;
use pxlrbt\Cf7Cleverreach\Cleverreach\Api as CleverreachApi;
use pxlrbt\Cf7Cleverreach\Vendor\pxlrbt\WordpressNotifier\Notification;

class Container
{
    private static $instance;
    private $logger;
    private $notifier;
    private $api;

    public function __construct($plugin)
    {
        self::$instance = $this;
        $this->plugin = $plugin;
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function getNotifier()
    {
        if ($this->notifier === null) {
            $this->notifier = new Notifier(Plugin::$prefix);
        }

        return $this->notifier;
    }

    public function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = new Logger('cf7-cleverreach');
            $this->logger->pushHandler(
                new StreamHandler($this->getLogPath())
            );
        }

        return $this->logger;
    }

    public function getLogPath()
    {
        return WP_CONTENT_DIR . '/' . md5(NONCE_KEY) . '-cf7-cleverreach.log';
    }

    public function getLogUrl()
    {
        return content_url() . '/' . md5(NONCE_KEY) . '-cf7-cleverreach.log';
    }

    public function getApi()
    {
        if (($token = ApiCredentials::token()) === null) {
            $this->notifier->dispatch(
                Notification::create(__('Incomplete configuration', 'wpcf7-cleverreach'), 'configuration')
                    ->title('CF7 to CleverReach: ')
                    ->type('warning')
            );

            return new CleverreachApi();
        }

        return new CleverreachApi($token);
    }
}
