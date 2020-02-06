<?php

namespace pxlrbt\CF7Cleverreach\Controllers;

use pxlrbt\CF7Cleverreach\Config\Config;
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
            'title' => 'Cleverreach',
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
        $this->saveGlobalAttributeMapping();
    }



    private function checkForOptions()
    {
        $options = $_POST['wpcf7-cleverreach_options'];

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
        if (isset($_POST['wpcf7-cleverreach_attribute']) == false) {
            return;
        }

        $mapping = [];

        foreach ($_POST['wpcf7-cleverreach_attribute'] as $cf7Name => $cleverreachName) {
            if (empty($cleverreachName) == false) {
                $mapping[$cf7Name] = strtolower($cleverreachName);
            }
        }

        Config::saveAttributeMapping($this->getCurrentFormId(), $mapping);
    }



    private function saveGlobalAttributeMapping()
    {
        if (isset($_POST['wpcf7-cleverreach_global_attribute']) == false) {
            return;
        }

        $mapping = [];
        foreach ($_POST['wpcf7-cleverreach_global_attribute'] as $cf7Name => $cleverreachName) {
            if (empty($cleverreachName) == false) {
                $mapping[$cf7Name] = strtolower($cleverreachName);
            }
        }

        Config::saveGlobalAttributeMapping($this->getCurrentFormId(), $mapping);
    }



    /* PRINTING FUNCTIONS */
    public function printEditorPanel($form)
    {
        $this->options = Config::getOptions($this->getCurrentFormId());
        include __DIR__ . '/../../views/cf7-cleverreach-panel.php';
    }
}
