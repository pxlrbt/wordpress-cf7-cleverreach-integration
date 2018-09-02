<?php

namespace Pixelarbeit\CF7Cleverreach\Controllers;

use Pixelarbeit\Cleverreach\Api as CleverreachApi;
use Pixelarbeit\Wordpress\Notifier\Notifier;
use WPCF7_ContactForm;
use Exception;



class SettingsPageController
{
    public static $instance;



    private function __construct() {}



    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }



	public function init()
	{
        $this->notifier = new Notifier('CF7 to Cleverreach:');
        add_action('admin_init', [$this, 'registerSetting']);
        add_action('admin_menu', [$this, 'registerMenu']);
    }



    public function registerSetting()
    {
        register_setting('wpcf7-cleverreach', 'wpcf7-cleverreach_api-token', []);        
    }



    
    public function registerMenu()
    {
        add_options_page(
            'CF7 to Cleverreach',
            'CF7 to Cleverreach',
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

        $this->saveData();
            
        include __DIR__ . '/../../views/settings-page.php';
    }



    public function getApiToken($code)
    {
        $api = new CleverreachApi();
        $clientId = get_option('wpcf7-cleverreach_client-id', null);
        $clientSecret = get_option('wpcf7-cleverreach_client-secret', null);
        $redirectUrl = esc_url(admin_url('options-general.php?page=cf7-cleverreach'));
        
        try {
            $result = $api->getApiToken($clientId, $clientSecret, $code, $redirectUrl);
        } catch (Exception $e) {
            $this->notifier->printNotification('error', 'Unexpected error.');
            return;
        }        
        
        if (isset($result->error_description)) {
            $this->notifier->printNotification('error', 'Could not retrieve api token: ' . $result->error_description);
            return;
        }
        
        if (isset($result->access_token)) {
            update_option('wpcf7-cleverreach_api-token', $result->access_token);
            update_option('wpcf7-cleverreach_api-expires', time() + $result->expires_in);
            $this->notifier->printNotification('success', 'Api token updated');
        }           
    }
    


    public function saveData()
    {
        if (empty($_POST)) {
            return;
        }

        check_admin_referer('wpcf7-cleverreach');

        $clientId = $_POST['wpcf7-cleverreach_client-id'];
        $clientSecret = $_POST['wpcf7-cleverreach_client-secret'];

        update_option('wpcf7-cleverreach_client-id', $clientId);
        update_option('wpcf7-cleverreach_client-secret', $clientSecret);
        
        $this->notifier->printNotification('success', 'Saved credentials');

        return;
    }
}
