<?php

namespace pxlrbt\Cf7Cleverreach\Cleverreach;

use pxlrbt\Cf7Cleverreach\Plugin;

class ApiCredentials
{
    public static $clientId = 'dDHV6YpJm3';
    public static $clientSecret = 'ysqrbL2NNKTwGWphfWMRkZu1VA0kjnoS';

    public static function token()
    {
        return get_option(Plugin::$prefix . 'api-token', null);
    }

    public static function refreshToken()
    {
        return get_option(Plugin::$prefix . 'api-refresh-token', null);
    }

    public static function expires()
    {
        return get_option(Plugin::$prefix . 'api-expires', null);
    }

    public static function updateFromResult($result)
    {
        update_option(Plugin::$prefix . 'api-token', $result->access_token);
        update_option(Plugin::$prefix . 'api-refresh-token', $result->refresh_token);
        update_option(Plugin::$prefix . 'api-expires', time() + $result->expires_in);
    }
}
