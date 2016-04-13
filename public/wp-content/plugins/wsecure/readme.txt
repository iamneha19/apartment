=== wSecure Lite ===
Contributors: ajaylulia
Tags: wordpress security, security plugin, admin security, authentication, access & security, site security, login protection, prevent admin hack
Requires at least: 2.7
Tested up to: 4.0

wSecure hides admin URL so that default URL will no longer bring up the admin page. Only people who enter the secret key will be able to access admin area.

== Description ==

The wSecure plugin hides admin URL so that "www.yoursite.com/wp-admin" will no longer
bring up the admin page. Instead, wSecure allows you to set your own admin URL using a secret key
(for example: www.yoursite.com/wp-admin/?secret). Only people who enter the secret key will be able to
access your admin area.

<strong>Features:</strong>
The <strong>Basic Version</strong> will hide your administrator URL from public access. This is all most people need.

* Set "Enable" to "yes" in order for wSecure to work.
* The "Pass Key" field allows admin to select the mode in which admin can enter the "Secret Key" to access the WordPress admin login page. Possible options are directly through "url" or a separate "form" asking to enter the secure key. 
* In the "Key" field enter the key that will be part of your new administrator URL. For example,
      if you enter "wSecure" into the key field, then the administrator URL will be http://www.yourwebsite/wp-admin/?wSecure.
      Please note that you cannot have a key that is only numbers.
* If you do not enter a key, but enable the wSecure plugin, then the default URL to access the administrator area is /?wSecure
      (http://www.yourwebsite/wp-admin/?wSecure).
* Set the "Redirect Options" field. By default, if someone tries to access you /wp-admin URL without the correct key, they
   will be redirected to the home page of your Wordpress site. You can also set up a "Custom Path" is you would like the user
   to be redirected somewhere else, such as a 404 error page.
* Click on the save button to make changes.

The <strong><a href="http://www.joomlaserviceprovider.com/extensions/wordpress/commercial/wsecure-authentication.html" title="Click here to download advanced version" target="_blank">Advanced version</a></strong> has additional features that you can have.

* Mail tab: This sets whether you want an email to be sent every time there  is a failed login attempt into the Wordpress administration area. You can set it to send the wSecure key or the incorrect key that was entered.
* IP tab: This tab allows you to control which IPs have access to your admin URL.
* White Listed IPs: If set to "White Listed IPs" you can make a white list for certain IPs. Only those specific IPS will be allowed to access your admin URL.
* Blocked IPs: If set to "Blocked IPs" you can block certain IPs  form accessing your admin URL.
* Master Password: You can block access to the wSecure component from other administrators.
   Setting to "Yes", allows you to create a password that will be required when any administrator tries to access
   the wSecure configuration settings in the Wordpress administration area..
* Master Mail: These setting allow you to have an email sent every time the wSecure configuration is changed.
* Log: This setting allows you to decide how long the wSecure logs should remain in the database.

== Installation ==

Installing wSecure from a package

1. In WordPress 2.7 and above you can install plugins directly from the admin area.
   Downlod the plugin to your system, then log in to your WP admin area and go to Plugins > Add New.
   Browse to the plugin archive and select it. Then click Install Now and the plugin will be installed shortly.
2. Make sure that the $WP_HOME/wp-content/plugins/wsecure/params.php file is writable by the web server.
3. Activate the plugin.
4. The wSecure settings are located under "Settings"-> "wSecure".

Manual Installation of wSecure

1. Download the plugin file and unzip it.
2. Put the wsecure directory into your (Wordpress home directory (varies depending on hosting company))/wp-content/plugins/ directory.
3. Make sure that the (Wordpress home directory (varies depending on hosting company))/wp-content/plugins/wsecure/params.php
   file is writable by the web server.
4. Then log into your Wordpress administration area Activate the plugin.
5. The wSecure settings are located under "Settings"-> "wSecure".

== Important ==
In order for wSecure to work the wSecure plugin must be activated. Go to Plugins Manager and look for
the "wSecure Authentication". Make sure this plug in is activated.

== Help ==
Click on the "Help" link present on wSecure Authentication plugin page. Here you will find help on how to use this plugin.

== Changelog ==
<strong>Version 1.0 - Basic Version.</strong> Works fine, with basic functionality.

<strong>Version 2.0 - Session problem corrected.</strong>

<strong>Version 2.1 - Redirection problem corrected when user chooses custom path option.</strong> 

<strong>Version 2.1 - (New) Added option to select the "Pass Key" mode i.e. the mode in which you can enter the secret key for accessing the WordPress admin login page. Possible options are "form" and "url". </strong> 


<strong>Version 2.2 - <a href="http://www.joomlaserviceprovider.com/extensions/wordpress/commercial/wsecure-authentication.html" title="Click here to download advanced version" target="_blank">Advanced version</a>- Redirection problem corrected when user chooses custom path option.</strong> 

<strong>Features:</strong>

* Added the option of form to type the secret key in the form instead of the URL.
* Added E-mail option to send the change log in wSecure Authentication.
* User can choose from White Listed IPs / Blocked IPs.
* User Friendly option to add ip address.
* Enter specific IPs(White Listed IPs) that will allow access to administration area.
* Added Master Password to access the wSecure Authentication.
* Added view log  functionality to show the log made by  wSecure.
* Added delete log  functionality to keep the log of the plugin for a specified amount of time.
* Improved back-end layout and presentation.

<strong>Version 2.3</strong>

<strong>Added Features:</strong>

* Added functionality to pass wSecure key by FORM / URL.
* Improved UI of the plugin.
* Improved security by adding required validations for wSecure key.