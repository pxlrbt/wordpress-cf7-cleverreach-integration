<?php

namespace pxlrbt\Cf7Cleverreach;

use pxlrbt\Cf7Cleverreach\Controllers\FormConfigController;
use pxlrbt\Cf7Cleverreach\Controllers\SettingsPageController;
use pxlrbt\Cf7Cleverreach\ContactForm7\FormConfig;
use pxlrbt\Cf7Cleverreach\ContactForm7\SubmissionHandler;
use pxlrbt\Cf7Cleverreach\Cleverreach\ApiCredentials;
use pxlrbt\Cf7Cleverreach\Vendor\pxlrbt\WordpressNotifier\Notification;

class Plugin
{
    public static $name = 'cf7-cleverreach-integration';
    public static $prefix = 'wpcf7-cleverreach_';
    public static $version = '2.4.8';
    public static $title = 'CleverReach Integration for Contact Form 7';

    /**
     * Initializes the plugin
     *
     * @author Dennis Koch <info@pixelarbeit.de>
     * @since 1.0
     */
    public function boot()
    {
        $this->container = new Container($this);

        /* Backend hooks */
        register_uninstall_hook(__FILE__, [__CLASS__, 'uninstall']);
        add_action('init', [$this, 'checkUpdate'], 10, 2);
        add_action('delete_post', [$this, 'deleteConfig'], 10, 1);

        /* Frontend hooks */
        add_action('wpcf7_mail_sent', [$this, 'onCF7MailSent']);

        new FormConfigController($this->container);
        new SettingsPageController($this->container);
        new UpdateCleverreachTokenService($this->container);
    }

    /**
     * Handles CF7 form submits
     *
     * @author Dennis Koch <info@pixelarbeit.de>
     * @since 1.1
     */
    public function onCf7MailSent($form)
    {
        $options = FormConfig::getOptions($form->id());

        if (isset($options['active']) == false || $options['active'] == false) {
            return;
        }

        $api = $this->container->getApi();

        $submissionHandler = new SubmissionHandler($api);
        $submissionHandler->handleForm($form);
    }

    /**
     * Update routine
     *
     * @author Dennis Koch <info@pixelarbeit.de>
     * @since 1.1
     */
    public function checkUpdate()
    {
        if (version_compare($this->getVersion(), '1.1.1', '<')) {
            $token = get_option('cf7-cleverreach_token');
            update_option(self::$prefix . 'api-token', $token);
            delete_option('cf7-cleverreach_token');

            $this->container->getLogger()->info('Updated to version 1.1');
        }

        if (version_compare($this->getVersion(), '2.4.0', '<')) {
            if (ApiCredentials::token() !== null && ApiCredentials::refreshToken() === null) {
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
            }
        }

        $this->setVersion(self::$version);
    }

    /**
     * Get saved plugin version
     *
     * @author Dennis Koch <info@pixelarbeit.de>
     * @since 1.1
     */
    public function getVersion()
    {
        return get_option(self::$prefix . 'version', '1.0');
    }

    /**
     * Set saved plugin version
     *
     * @author Dennis Koch <info@pixelarbeit.de>
     * @since 1.1
     */
    public function setVersion($version)
    {
        return update_option(self::$prefix . 'version', $version);
    }

    /**
     * Delete config for given post id if it is cf7 form
     *
     * @author Dennis Koch <info@pixelarbeit.de>
     * @since 1.0
     */
    public function deleteConfig($postId)
    {
        if (get_post_type($postId) == \WPCF7_ContactForm::post_type) {
            FormConfig::deleteConfig($postId);
        }
    }

    /**
     * Uninstall function. Deletes all config.
     *
     * @author Dennis Koch <info@pixelarbeit.de>
     * @since 1.0
     */
    public static function uninstall()
    {
        $forms = \WPCF7_ContactForm::find();
        foreach ($forms as $form) {
            FormConfig::deleteConfig($form->id());
        }
    }
}
