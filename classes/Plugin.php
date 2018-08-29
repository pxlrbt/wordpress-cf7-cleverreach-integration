<?php

namespace Pixelarbeit\CF7Cleverreach;

use Pixelarbeit\CF7Cleverreach\Config\Config;



class Plugin
{
    public function __construct()
    {
        $this->init();
    }



    /**
     * Load dependcies
     * Initialize plugin
     */
    public function init()
    {
        $admin = Admin::getInstance();
        $admin->init();
        
        $frontend = new Frontend();
        
        register_uninstall_hook(__FILE__, [__CLASS__, 'uninstall']);
        add_action('delete_post', [$this, 'deleteConfig'], 10, 1);
    }



    public static function uninstall()
    {
        $forms = \WPCF7_ContactForm::find();
        foreach ($forms as $form) {
            Config::deleteConfig($form->id());
        }
    }

    
    
    public function deleteConfig($postId)
    {   
        if (get_post_type($postId) == \WPCF7_ContactForm::post_type) {
            Config::deleteConfig($postId);
        }
    }
}