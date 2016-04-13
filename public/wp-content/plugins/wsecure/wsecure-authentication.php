<?php
/*
Plugin Name: wSecure Lite
Plugin URI: http://www.joomlaserviceprovider.com/
Description: Word press! has one security problem, any web user can easily know if the site is created in Word press! by typing the URL to access the administration area (i.e. www.sitename.com/wp-admin). This allows hackers to hack the site easily once they crack the id and password for Word press!. The wSecure plugin prevents access to the administration (back end) login page if the user does not use the appropriate access key.
Version: 2.3
Author: Ajay Lulia
Author URI: http://www.joomlaserviceprovider.com/
*/

define('JA_VERSION','3.1.7');
define('JA_DIR',dirname(__FILE__));

require_once(JA_DIR.'/includes.php');
session_start();

add_action('wp_logout', 'ja_logout');
add_action('init', 'ja_checkUrlKey');

function wsecure_menu()
{
    global $wpdb;
    include 'wsecure-config.php';
}
 
function wsecure_admin_actions()
{
    add_options_page("wSecure", "wSecure", 1, "wsecure-configuration", "wsecure_menu");
}
 
add_action('admin_menu', 'wsecure_admin_actions');

function wsecure_addScript(){
	$css = '<link rel="stylesheet" href="' .get_bloginfo("wpurl") . '/wp-content/plugins/wsecure/css/wsecure.css" type="text/css" media="screen" />';
    $css1    = '<link rel="stylesheet" href="' .get_bloginfo("wpurl") . '/wp-content/plugins/wsecure/css/tabs.css" type="text/css" media="screen" />';
	$script = '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/wsecure/js/basic.js"></script>';
	$script2 = '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/wsecure/js/tabbed.js"></script>';
	
	echo  $css.$css1.$script.$script2;
}
add_action ('admin_head', 'wsecure_addScript');

?>