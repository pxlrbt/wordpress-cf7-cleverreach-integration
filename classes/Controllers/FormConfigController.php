<?php

namespace pxlrbt\Cf7Cleverreach\Controllers;

use pxlrbt\Cf7Cleverreach\Cleverreach\ApiCredentials;
use pxlrbt\Cf7Cleverreach\ContactForm7\FormConfig;
use pxlrbt\Cf7Cleverreach\ContactForm7\Helpers;
use pxlrbt\Cf7Cleverreach\Container;
use pxlrbt\Cf7Cleverreach\Vendor\pxlrbt\WordpressNotifier\Notification;

class FormConfigController
{
	public function __construct(Container $container)
	{
        $this->container = $container;
        add_action('wpcf7_save_contact_form', [$this, 'saveCF7Config'], 10, 1 );
        add_filter('wpcf7_editor_panels', [$this, 'registerEditorPanel'], 10, 1);
    }

    public function registerEditorPanel($panels)
    {
        $panels['cleverreach-panel'] = [
            'title' => 'CleverReach',
            'callback' => [$this, 'printEditorPanel']
        ];

        return $panels;
    }

    public function saveCF7Config($form)
    {
        if (ApiCredentials::token() === null) {
            return;
        }

        if (count($_POST['wpcf7-cleverreach_options']) == 0) {
            return;
        }

        $this->checkForOptions();

        $this->saveOptions();
        $this->saveAttributeMapping();
    }

    private function checkForOptions()
    {
        $options = $_POST['wpcf7-cleverreach_options'];

        if (!isset($options['active'])) return;

        if (empty($options['listId']) || empty($options['formId']) || empty($options['emailField'])) {
            $this->container->getNotifier()->warning(
                __('Missing form configuration. Required: List Id, Form ID, Email Field.', 'wpcf7-cleverreach')
            );
        }
    }

    private function saveOptions()
    {
        $options = [];

        $options['active'] = isset($_POST['wpcf7-cleverreach_options']['active']);
        $options['doubleOptIn'] = isset($_POST['wpcf7-cleverreach_options']['doubleOptIn']);

        foreach ($_POST['wpcf7-cleverreach_options'] as $optionName => $optionValue) {
            $options[$optionName] = $optionValue;
        }

        FormConfig::saveOptions(Helpers::currentFormId(), $options);
    }

    private function saveAttributeMapping()
    {
        if (isset($_POST['wpcf7-cleverreach_mapping']) == false) {
            return;
        }

        $localMapping = [];
        $globalMapping = [];

        foreach ($_POST['wpcf7-cleverreach_mapping'] as $cf7Name => $attributeName) {
            if (empty($attributeName)) continue;

            list($group, $cleverreachName) = explode('--', $attributeName, 2);

            if ($group == 'global') {
                $globalMapping[$cf7Name] = strtolower($cleverreachName);
            } else {
                $localMapping[$cf7Name] = strtolower($cleverreachName);
            }
        }

        FormConfig::saveAttributeMapping(Helpers::currentFormId(), $localMapping);
        FormConfig::saveGlobalAttributeMapping(Helpers::currentFormId(), $globalMapping);
    }

    public function printEditorPanel($form)
    {
        $this->options = FormConfig::getOptions(Helpers::currentFormId());
        include __DIR__ . '/../../views/cf7-cleverreach-panel.php';
    }
}
