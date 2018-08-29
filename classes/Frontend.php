<?php

namespace Pixelarbeit\CF7Cleverreach;

use Pixelarbeit\Cleverreach\Api as CleverreachApi;
use Pixelarbeit\Wordpress\Notifier\Notifier;
use Pixelarbeit\Wordpress\Logger\Logger;
use Pixelarbeit\CF7Cleverreach\Config\Config;
use Pixelarbeit\CF7Cleverreach\CF7\SubmissionHandler;



class Frontend
{
    private $api;
    private $notifier;
    private $submissionHandler;


    
	public function __construct()
	{        
        $this->notifier = new Notifier('CF7 to Cleverreach');
        
        if ($this->loadConfig() == false) {
            return; 
        }
        
        $this->api = new CleverreachApi($this->token);
        $this->submissionHandler = new SubmissionHandler($this->api);

        $this->addHooks();
	}	



    public function loadConfig()
    {
        $this->token = get_option('cf7-cleverreach_token');
        
        if (empty($this->token)) {
            $this->notifier->warning('Incomplete configuration.');
            return false;
        }

        return true;
    }



    private function addHooks()
    {
        add_action('wpcf7_mail_sent', [$this, 'onCF7MailSent']);
    }
    
    

    public function onCF7MailSent($form) {
        return $this->submissionHandler->handleForm($form); 
    }
}
