<?php

namespace pxlrbt\Wordpress\Logger;



/**
 * Logger helper class for wordpress.
 */
class Logger
{

    protected $prefix;


    public function __construct($prefix = "")
    {
        $this->prefix = $prefix;
    }



    public function debug($msg, $context = [])
    {
        $this->log(LogLevel::DEBUG, $msg, $context);
    }



    public function info($msg, $context = [])
    {
        $this->log(LogLevel::INFO, $msg, $context);
    }



    public function notice($msg, $context = [])
    {
        $this->log(LogLevel::NOTICE, $msg, $context);
    }



    public function warning($msg, $context = [])
    {
        $this->log(LogLevel::WARNING, $msg, $context);
    }



    public function error($msg, $context = [])
    {
        $this->log(LogLevel::ERROR, $msg, $context);
    }



    public function critical($msg, $context = [])
    {
        $this->log(LogLevel::CRITICAL, $msg, $context);
    }



    public function alert($msg, $context = [])
    {
        $this->log(LogLevel::ALERT, $msg, $context);
    }



    public function emergency($msg, $context = [])
    {
        $this->log(LogLevel::EMERGENCY, $msg, $context);
    }



    public function log($level, $msg, $context = [])
    {
        error_log(
            "[" . date('Y-m-d H:i:s') . "]" . "\t"
            . strtoupper($level)  . "\t"
            . ($this->prefix !== '' ? $this->prefix . "\t" : '')
            . $msg
            . (empty($context) === false ? "\n\t\t" . print_r($context, true) : '')
        );
    }
}
