<?php

namespace Pixelarbeit\CF7Cleverreach\CF7;

use Pixelarbeit\Cleverreach\Api as CleverreachApi;
use Pixelarbeit\Wordpress\Notifier\Notifier;
use Pixelarbeit\Wordpress\Logger\Logger;
use Pixelarbeit\CF7Cleverreach\Config\Config;
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

        $attributes = $this->getAttributes();
        $globalAttributes = $this->getGlobalAttributes();
        
        
        try {
            $contact = $this->getContactByEmail($options['listId'], $email);
            
            if ($contact == null) {
                $result = $this->createContact($options['listId'], $email, $attributes, $globalAttributes);
                $mail = $this->sendActivationMail($options['formId'], $email);
            } else {
                $result = $this->updateContact($options['listId'], $email, $attributes, $globalAttributes);
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
                $key = strtolower($mapping[$cf7Name]);
                $mapped[$key] = $cf7Value;
            }
        }

        return $mapped;
    }



    private function setArrayValueByMultidimensionalKey(&$array, $key, $value)
    {
        $reference = &$array;
        $keys = explode('.', $key);

        foreach ($keys as $key) {
            if (!array_key_exists($key, $reference)) {
                $reference[$key] = [];
            }

            $reference = &$reference[$key];
        }

        $reference = $value;
    }



    public function getContactByEmail($listId, $email)
    {

        $url = $this->api->buildUrl('receivers/filter.json');
        $result = $this->api->request($url, 'POST', [
            'rules' => [
                [
                    'field' => 'email',
                    'logic' => 'eq',
                    'condition' => $email
                ]
            ],
            'activeonly' => false,
            'groups' => [$listId],
            'page' => 0,
            'pagesize' => 1

        ]);

        if (isset($result->error)) {
            throw new \Exception("CF7 to Cleverreach:" . $result->error->message);
        }

        return count($result) > 0 ? $result[0] : null;        
    }
    
    
    
    public function updateContact($listId, $email, $attributes = [], $globalAttributes = [])
    {
        $url = $this->api->buildUrl('groups.json/' . $listId . '/receivers/' . $email);
        $result = $this->api->request($url, 'PUT', [
            "email" => $email,
            "source" => "Webseite",    
            "attributes" => $attributes,
            "global_attributes" => $globalAttributes            
        ]);

        if (isset($result->error)) {
            throw new \Exception("CF7 to Cleverreach:" . $result->error->message);
        }

        return $result;
    }
    


    public function createContact($listId, $email, $attributes = [], $globalAttributes = [])
    {
        $url = $this->api->buildUrl('groups.json/' . $listId . '/receivers');
        $result = $this->api->request($url, 'POST', [
            "email" => $email,
            "created" => time(),
            "deactivated" => 1,
            "attributes" => $attributes,
            "global_attributes" => $globalAttributes
        ]);

        if (isset($result->error)) {
            throw new \Exception("CF7 to Cleverreach:" . $result->error->message);
        }

        return $result;
    }

    

    public function sendActivationMail($formId, $email)
    {
        
        $doidata = array(
            "user_ip" => $_SERVER['REMOTE_ADDR'],
            "user_agent" => $_SERVER['HTTP_USER_AGENT'],
            "referer" => $_SERVER['HTTP_REFERER'],
        );

        $url = $this->api->buildUrl('forms.json/' . $formId . '/send/activate');

        $result = $this->api->request($url, 'POST', [
            'email' => $email,
            'doidata' => $doidata
        ]);

        if (isset($result->error)) {
            throw new \Exception("CF7 to Cleverreach:" . $result->error->message);
        }

        return $result;
    }
}
