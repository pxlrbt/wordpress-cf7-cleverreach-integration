<?php

namespace pxlrbt\Cf7Cleverreach\ContactForm7;

use WPCF7_ContactForm;
use WPCF7_Submission;

use pxlrbt\Cf7Cleverreach\Cleverreach\Api as CleverreachApi;
use pxlrbt\Cf7Cleverreach\ContactForm7\FormConfig;
use pxlrbt\Cf7Cleverreach\Container;
use pxlrbt\Cf7Cleverreach\Vendor\pxlrbt\WordpressNotifier\Notification;

class SubmissionHandler
{
	public function __construct(CleverreachApi $api)
	{
        $this->api = $api;
        $this->container = Container::getInstance();
        $this->notifier = $this->container->getNotifier();
        $this->logger = $this->container->getLogger();
	}

    public function handleForm(WPCF7_ContactForm $form)
    {
        $this->form = $form;
        $formData = $this->getCF7FormData();
        $options = FormConfig::getOptions($form->id());

        if (!empty($options['requireField']) && empty($_POST[$options['requireField']])) {
            $this->logger->debug('Did not process data: Required field not set.', [
                'formId' => $form->id(),
                'options' => $options
            ]);
            return;
        }

        if (empty($options['listId']) || empty($options['formId']) || empty($options['emailField'])) {
            $this->notifier->dispatch(
                Notification::create(
                    sprintf(
                        __('Form config for form "<a href="%s">%s</a>" is incomplete.', 'wpcf7-cleverreach'),
                        esc_url(admin_url('admin.php?page=wpcf7&post=' . $form->id())),
                        $form->title()
                    )
                )
                    ->title('CF7 to CleverReach: ')
                    ->id('error.incomplete-config.' . $form->id())
                    ->type('error')
                    ->dismissible(true)
                    ->persistent(true)
            );

            $this->logger->error(
                'Did not process data: Missing configuration (list ID, form ID, email field).',
                [
                    'formId' => $form->id(),
                    'formData' => $formData,
                    'options' => $options
                ]
            );
            return;
        }

        $email = $formData[$options['emailField']];

        if (empty($email)) {
            $this->logger->error('Did not process data: No email found.', [
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

            } else {
                $result = $this->api->updateContact(
                    $options['listId'],
                    $email,
                    $tags,
                    $attributes,
                    $globalAttributes
                );
            }

            $sendActivationMail = $contact == null || $contact->activated == false;
            $doubleOptInActive = isset($options['doubleOptIn']) == false || $options['doubleOptIn'] == true;

            if ($doubleOptInActive && $sendActivationMail) {
                $this->api->sendActivationMail($options['formId'], $email);
            }
        } catch (\Exception $e) {
            $this->notifier->dispatch(
                Notification::create(
                    sprintf(
                        __('Error while transferring data from Contact Form 7 to CleverReach. %sCheck log file%s for details.', 'wpcf7-cleverreach'),
                        '<a href="' . $this->container->getLogUrl() . '">',
                        '</a>',
                    )
                )
                    ->title('CF7 to CleverReach: ')
                    ->id('error.transfer')
                    ->type('warning')
                    ->dismissible(true)
                    ->persistent(true)
            );

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
        $submission = WPCF7_Submission::get_instance();

        if (isset($submission) == false) {
            return null;
        }

        return $submission->get_posted_data();
    }

    private function getAttributes()
    {
        $mapped = [];

        $formData = $this->getCF7FormData();
        $mapping = FormConfig::getAttributeMapping($this->form->id());

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
        $mapping = FormConfig::getGlobalAttributeMapping($this->form->id());

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
