<?php

namespace pxlrbt\Cf7Cleverreach;

use pxlrbt\Cf7Cleverreach\Vendor\Monolog\Handler\StreamHandler;
use pxlrbt\Cf7Cleverreach\Vendor\Monolog\Logger;
use pxlrbt\Cf7Cleverreach\Cleverreach\ApiCredentials;
use pxlrbt\Cleverreach\Api as CleverreachApi;
use pxlrbt\Wordpress\Notifier\Notifier;

class Container
{
    private static $instance;

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
            $this->notifier = new Notifier(Plugin::$prefix, 'CF7 to CleverReach');
        }

        return $this->notifier;return $this->notifier;
    }

    public function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = new Logger('cf7-cleverreach');
            $this->logger->pushHandler(new StreamHandler(WP_CONTENT_DIR . '/cf7-cleverreach.log'));
        }

        return $this->logger;
    }

    public function getApi()
    {
        if (($token = ApiCredentials::token()) === null) {
            $this->notifier->warning('Incomplete configuration.');
            return new CleverreachApi();
        }

        return new CleverreachApi($token);
    }
}
