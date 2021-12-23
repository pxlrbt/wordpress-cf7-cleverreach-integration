# CleverReach Integration for Contact Form 7

Contributors: pixelarbeit
Tags: contact form 7, cf7, cleverreach, contact form 7 addon, contact form 7 integration
Tested up to: 5.6
Requires at least: 4.6
Requires PHP: 5.5
Stable tag: 2.4.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Connect your Contact Form 7 forms with your CleverReach account.
## Description

Add or update new recipients to CleverReach when your Contact Form 7 form is submitted.

### Features

* Quick setup with CleverReach
* Map Contact Form 7 fields to CleverReach fields with ease
* Invidual settings per form (e.g. group, form, mapping, ...)
* Choose between Single Opt-In and Double Opt-In
* Mark a field as "required" for CleverReach submission

### Requires

* PHP >= 5.5
* PHP cURL Extension
* Contact Form 7 Plugin >= 4.5
* CleverReach Account

### Support

If you find a bug please open an issue on [GitHub](https://github.com/pxlrbt/wordpress-contact-form-7-cleverreach/issues). For additional feature request or _paid support_ contact me via [email](mailto:info@pixelarbeit.de)

### Links
- [GitHub Repository](https://github.com/pxlrbt/wordpress-contact-form-7-cleverreach)
- [WordPress Plugin Directory](https://wordpress.org/plugins/cf7-cleverreach-integration/)

## Configuration

1. Go to Settings > CF7 to CleverReach
1. Click "Get CleverReach API token". You will be redirected to CleverReach.
1. Authenticate against CleverReach and give access to the plugin. You will be redirected back to Wordpress and should see the generated API token.
1. Configure your forms via Contact -> "Your form" > CleverReach tab

## Installation

1. Upload the plugin files to the `/wp-content/plugins/cf7-cleverreach-integration` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

## Screenshots

1. Get CleverReach API Token via settings page
2. Configure form

## Changelog

### 2.4.5
* Fix: Add link to log file in error message.
* Fix: Don't show CF7 panel when token is not set.
* Fix: Make strings translatable.
### 2.4.4
* Fix: Solved issue inside CF7 configuration panel introduced with 2.4.3.
### 2.4.3
* Fix: Do not show refresh token warning on first install or when API token is empty

### 2.4.2
* Fix: Send Double-Opt-In Mail for existing users if they are not activated yet.
### 2.4.1
* Fix: Added note on token expiry

### 2.4.0
* Feat: Automatic token renewal
* Feat: Improved Notifications/Logging

### 2.3.4
* Feat: Add not regarding access tokens on settings page

### 2.3.3
* Fix: Downgrade monolog for compatibility with PHP 5.5

### 2.3.2
* Fix: Patch autoloader files to prevent collision

### 2.3.1
* Fix: A function still relied on the old http client

### 2.3
* Refactor: Use guzzle as http client
* Refactor: Use monolog as logger
* Refactor: Use php-Scoper

### 2.2
* Merge global and local attributes in one select
* Fix: Disable checks when integration is not active
* Fix: Enable SSL verification in JsonClient
* Fix: Improve logs and notifications

### 2.1.2
* Update name to comply with Contact Form 7 trademark policy
* Move settings to Contact Form 7 submenu

### 2.1.1
* Fix bug with static var access

### 2.1
* Fix bug for new api token generation

### 2.0
* No need for custom app anymore
* Select fields, form, group from list instead of giving an id.
* Added source, double opt-in and tags option

### 1.1.2
* Fix for checkboxes and radio buttons as input

### 1.1.1
* Transfer api token from old option field to new one

### 1.1
* Added cleverreach api token generation on settings page
* Fixed case-sensitivity of attributes

### 1.0
* First version
