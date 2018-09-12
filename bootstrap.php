<?php
/*
Plugin Name: Contact Form 7 - Cleverreach Integration
Description: Send Contact Form 7 form data to Cleverreach
Version:     2.1
Author:      pixelarbeit
Author URI:  https://pixelarbeit.de
*/

require __DIR__ . '/vendor/autoload.php';

use Pixelarbeit\CF7Cleverreach\Plugin;


$plugin = Plugin::getInstance();
$plugin->init();