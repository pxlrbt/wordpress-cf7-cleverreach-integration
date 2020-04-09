<?php

namespace pxlrbt\Cf7Cleverreach\CF7;

use pxlrbt\Cleverreach\Api as CleverreachApi;
use pxlrbt\Wordpress\Notifier\Notifier;
use pxlrbt\Wordpress\Logger\Logger;
use pxlrbt\Cf7Cleverreach\Config\Config;
use pxlrbt\Cf7Cleverreach\Plugin;
use WPCF7_ContactForm;
use WPCF7_Submission;



class SubmissionHandler
{
	public function __construct(CleverreachApi $api)
	{
        $this->logger = new Logger('cf7-cleverreach');
        $this->api = $api;
        $this->plugin = Plugin::getInstance();
        $this->notifier = $this->plugin->notifier;
	}



    public function handleForm(WPCF7_ContactForm $form)
    {
        $this->form = $form;
        $options = Config::getOptions($form->id());

        if (empty($options['listId']) || empty($options['formId']) || empty($options['emailField'])) {
            $this->notifier->error('Missing form configuration. Required: List ID, form ID, email field.');
            $this->logger->error('Did not process data: Missing configuration (list ID, form ID, email field) on CF7 form ' . $form->id());
            return;
        }

        if (!empty($options['requireField']) && empty($_POST[$options['requireField']])) {
            $this->logger->debug('Did not process data: Required field not set.', [
                'formId' => $form->id(),
                'options' => $options
            ]);
            return;
        }

        $formData = $this->getCF7FormData();
        $email = $formData[$options['emailField']];

        if (empty($email)) {
            $this->logger->warn('Did not process data: No email found.', [
                'formId' => $form->id(),
                'formData' => $formData,
                'options' => $options
            ]);
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
            $this->notifier->error('Error while transferring data from Contact Form 7 to CleverReach. Details in log.');
            $this->logger->error('Failed adding/updating user.', [
                'message' => $e->getMessage(),
                'data' => [
                    'email' => $email,
                    'listId' => $options['listId'],
                    'tags' => $tags,
                    'doubleOptIn' => $activate,
                    'source' => $options['source'],
                    'attributes' => $attributes,
                    'globalAttributes' => $globalAttributes
                ]
            ]);
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
