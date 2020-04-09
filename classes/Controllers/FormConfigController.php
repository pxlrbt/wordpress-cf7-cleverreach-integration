<?php

namespace pxlrbt\Cf7Cleverreach\Controllers;

use pxlrbt\Cf7Cleverreach\Config\Config;
use WPCF7_ContactForm;



class FormConfigController
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



	public function init($plugin)
	{
        $this->plugin = $plugin;
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


    /* CF7 HELPER FUNCTIONS */
    public function getCurrentForm()
    {
        return WPCF7_ContactForm::get_current();
    }



    public function getCurrentFormId()
    {
        $form = WPCF7_ContactForm::get_current();
        return isset($form) ? $form->id() : null;
    }



    public function getCF7FieldNames()
    {
        $fields = [];
        $tags = $this->getCurrentForm()->scan_form_tags();

        foreach ($tags as $tag) {
            if (!empty($tag['name'])) {
                $fields[] = $tag['name'];
            }
        }

        return $fields;
    }




    /* SAVING FUNCTIONS */
    public function saveCF7Config($form)
    {
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
            $this->plugin->notifier->warning('Missing form configuration. Required: List Id, Form ID, Email Field.');
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

        Config::saveOptions($this->getCurrentFormId(), $options);
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

        Config::saveAttributeMapping($this->getCurrentFormId(), $localMapping);
        Config::saveGlobalAttributeMapping($this->getCurrentFormId(), $globalMapping);
    }

    /* PRINTING FUNCTIONS */
    public function printEditorPanel($form)
    {
        $this->options = Config::getOptions($this->getCurrentFormId());
        include __DIR__ . '/../../views/cf7-cleverreach-panel.php';
    }
}
