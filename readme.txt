=== Contact Form 7 - Cleverreach Integration ===
Contributors: pixelarbeit
Tags: contact form 7, cf7, cleverreach, contact form 7 addon, contact form 7 integration
Tested up to: 4.9.8
Requires at least: 4.6
Requires PHP: 5.5
Stable tag: 1.1.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Send user form input to cleverreach to add new newletter recipients.

== Description ==

This plugin adds the possibility for sending user form input to cleverreach to add new newletter recipients. It adds a cleverreach configuration section to every form, where you can map your contact form 7 fields to cleverreach attributes.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress


==Configuration==
1. Login to Cleverreach
1. My Account > Extras > REST Api > Create Oauth > Choose a name and select api version 1 & 2
1. Copy client ID and client secret from your created app
1. Login to Wordpress
1. Settings > CF7 to Cleverreach > Set your client ID and secret
1. Configure forms via Contact -> "Your form" > Cleverreach tab

== Screenshots ==

1. Generate an REST Api app on Cleverreach
2. Plugin configuration
3. Form configuration

== Changelog ==

= 1.1.1 =
* Transfer api token from old option field to new one

= 1.1 =
* Added cleverreach api token generation on settings page
* Fixed case-sensitivity of attributes

= 1.0 =
* First version

== Upgrade Notice ==
-
