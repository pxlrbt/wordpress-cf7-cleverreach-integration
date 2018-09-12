=== Contact Form 7 - Cleverreach Integration ===
Contributors: pixelarbeit
Tags: contact form 7, cf7, cleverreach, contact form 7 addon, contact form 7 integration
Tested up to: 4.9.8
Requires at least: 4.6
Requires PHP: 5.4
Stable tag: 2.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add new recipients to cleverreach from your Contact Form 7 forms.

== Description ==

Connects your Contact Form 7 forms with your Cleverreach account.

= Features =
* Add/update Cleverreach recipients when a form is submitted including custom Cleverreach fields
* Easy mapping between CF7 fields and Cleverreach fields
* Easy connection to Cleverreach (Only credentials are needed)
* Invidual settings per form (e.g. group, form, mapping, ...)
* Choose between Single Opt-In and Double Opt-In
* Choose a "required field" for Cleverreach submission

= Requires =
* PHP >= 5.5
* PHP cURL Extension
* Contact Form 7 >= 4.5
* Cleverreach Account

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

==Configuration==
1. Go to Settings > CF7 to Cleverreach
1. Click "Get Cleverreach API token". You will be redirected to Cleverreach.
1. Authenticate against Cleverreach and give access to the plugin. You will be redirected back to Wordpress and should see the generated API token.
1. Configure your forms via Contact -> "Your form" > Cleverreach tab

== Screenshots ==
1. Get Cleverreach API Token via settings page
2. Configure form

== Changelog ==

= 2.1 =
* Fix bug for new api token generation

= 2.0 =
* No need for custom app anymore
* Select fields, form, group from list instead of giving an id.
* Added source, double opt-in and tags option

= 1.1.2 =
* Fix for checkboxes and radio buttons as input

= 1.1.1 =
* Transfer api token from old option field to new one

= 1.1 =
* Added cleverreach api token generation on settings page
* Fixed case-sensitivity of attributes

= 1.0 =
* First version

== Upgrade Notice ==
-
