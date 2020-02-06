<?php

namespace pxlrbt\CF7Cleverreach\CF7;

use pxlrbt\Cleverreach\Api as CleverreachApi;
use pxlrbt\Wordpress\Notifier\Notifier;
use pxlrbt\Wordpress\Logger\Logger;
use pxlrbt\CF7Cleverreach\Config\Config;
use WPCF7_ContactForm;
use WPCF7_Submission;



class SubmissionHandler
{
	public function __construct(CleverreachApi $api)
	{
        $this->notifier = new Notifier('CF7 to Cleverreach');
        $this->logger = new Logger('CF7 to Cleverreach');
        $this->api = $api;
	}



    public function handleForm(WPCF7_ContactForm $form)
    {
        $this->form = $form;
        $options = Config::getOptions($form->id());

        if (empty($options['listId']) || empty($options['formId']) || empty($options['emailField'])) {
            $this->notifier->error('Missing form configuration. Required: List Id, Form ID, Email Field.');
            return;
        }

        if (!empty($options['requireField']) && empty($_POST[$options['requireField']])) {
            return;
        }

        $formData = $this->getCF7FormData();
        $email = $formData[$options['emailField']];

        if (empty($email)) {
            return;
        }

        $tags = isset($options['tags']) ? explode(',', $options['tags']) : [];
        $activate = isset($options['doubleOptIn']) &&  $options['doubleOptIn'] ? false : true;
        $attributes = $this->getAttributes();
        $globalAttributes = $this->getGlobalAttributes();

        try {
            $contact = $this->api->getContactByEmail($options['listId'], $email);

            if ($contact == null) {
                $result = $this->api->createContact(
                    $options['listId'],
                    $email,
                    $activate,
                    $options['source'],
                    $tags,
                    $attributes,
                    $globalAttributes
                );

                if (isset($options['doubleOptIn']) == false || $options['doubleOptIn'] == true) {
                    $mail = $this->api->sendActivationMail($options['formId'], $email);
                }
            } else {
                $result = $this->api->updateContact(
                    $options['listId'],
                    $email,
                    $tags,
                    $attributes,
                    $globalAttributes
                );
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->notifier->error('Error while transferring data from cf7 to cleverreach. Details in log.');
            return;
        }
    }



    private function getCF7FormData()
    {
        $submission = \WPCF7_Submission::get_instance();

        if (isset($submission) == false) {
            return null;
        }

        return $submission->get_posted_data();
    }



    private function getAttributes()
    {
        $mapped = [];

        $formData = $this->getCF7FormData();
        $mapping = Config::getAttributeMapping($this->form->id());

        foreach ($formData as $cf7Name => $cf7Value) {
            if (array_key_exists($cf7Name, $mapping)) {
                $cf7Value = is_array($cf7Value) ? implode(',', $cf7Value) : $cf7Value;
                $key = strtolower($mapping[$cf7Name]);
                $mapped[$key] = $cf7Value;
            }
        }

        return $mapped;
    }



    private function getGlobalAttributes()
    {
        $mapped = [];

        $formData = $this->getCF7FormData();
        $mapping = Config::getGlobalAttributeMapping($this->form->id());

        foreach ($formData as $cf7Name => $cf7Value) {
            if (array_key_exists($cf7Name, $mapping)) {
                $cf7Value = is_array($cf7Value) ? implode(',', $cf7Value) : $cf7Value;
                $key = strtolower($mapping[$cf7Name]);
                $mapped[$key] = $cf7Value;
            }
        }

        return $mapped;
    }
}
