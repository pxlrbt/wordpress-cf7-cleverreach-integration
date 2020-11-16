<?php

namespace pxlrbt\Cf7Cleverreach\CF7;

class ApiCredentials
{
    public static function token()
    {
        return get_option('wpcf7-cleverreach_api-token', null);
    }

    public static function refreshToken()
    {
        return get_option('wpcf7-cleverreach_api-refresh-token', null);
    }

    public static function expires()
    {
        return get_option('wpcf7-cleverreach_api-expires', null);
    }

    public static function updateFromResult($result)
    {
        update_option('wpcf7-cleverreach_api-token', $result->access_token);
        update_option('wpcf7-cleverreach_api-refresh-token', $result->refresh_token);
        update_option('wpcf7-cleverreach_api-expires', time() + $result->expires_in);
    }
}
