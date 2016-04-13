<?php 
/*
Version: 2.3
Author: Ajay Lulia
Author URI: http://www.joomlaserviceprovider.com/
*/
$file_permission =  (is_writable(dirname(__FILE__).'/params.php')) ? 1 : 0 ;
$opt ="";
if(trim($_REQUEST['opt']) =='' )
{
/* 	echo "<pre>";
	print_r( $_REQUEST );
 */
	$_REQUEST['opt'] = 'adv' ; 
	/* Redirect to Basic COnfiguration after Save */
	if((isset($_REQUEST['update']) && trim($_REQUEST['update']) == "true" ) || (isset($_REQUEST['Save']) && trim($_REQUEST['Save']) == "Save" ))
	{
		$_REQUEST['opt'] = 'config' ; 
	}
	/* // Redirect to Basic COnfiguration after Save */
}  
$opt = trim($_REQUEST['opt']);


$flag_saved = 0;
/* Code to SAve wSecure Config */
if($_POST['wsecure_action']=="update")
  	{
	include(dirname(__FILE__).'/params.php');
  $WSecureConfig = new WSecureConfig();
		$newkey = $_POST["key"]=="" ? $WSecureConfig->key : md5(base64_encode($_POST["key"])) ;
		$string = '<?php 
		class WSecureConfig {
		var $publish = "'. $_POST["publish"]. '";
		var $passkeytype = "'. $_POST["passkeytype"] . '";
		var $key = "'. $newkey . '";
		var $options = "'. $_POST["options"]. '";
		var $custom_path = "'. $_POST["custom_path"]. '";
		}
		?>';
		if (is_writable(dirname(__FILE__).'/params.php'))
		{
			$fp = fopen(dirname(__FILE__).'/params.php', "w+");
			fwrite($fp, $string);
			fclose($fp);
			wp_redirect(get_site_url()."/wp-admin/options-general.php?page=wsecure-configuration&w_action=save&opt=config");
			
		}

 	}
	

/* // Code to SAve wSecure Config */
?>

<div class="wrap">
<table width="100%" style="margin: 0px 0px 20px 0px;" >
<tr>
<td width="80%">
<?php 
screen_icon("wSecure");
?>
<h2 class="wsecure_heading" >wSecure Lite</h2>
</td>
<td class="sm-toolbar-item" align="right">
<a title="Get Premium Version" style="text-decoration: none;border: 1px solid rgb(199, 195, 195);padding: 7px 7px;background-color: #11B896;font-weight: bold;border-radius: 8px;color: rgb(235, 235, 235);border-color: transparent;" href="http://www.joomlaserviceprovider.com/extensions/wordpress/commercial/wsecure-authentication.html" target="_blank">Get Premium Version</a>
</td>
</table>
  
  
  <?php 
 
/* 
  if($_REQUEST['action']=="update" && $file_permission=="1")
  {
  //	 echo "<meta http-equiv='refresh' content='0;url=options-general.php?page=wsecure-configuration&update=true' />";
  } */
  
  if( $_REQUEST['w_action'] == "save" && $file_permission=="0")
  {
  	 echo "<div id='message' class='updated fade'>Settings is not updated! Check file permission. </div>"; 
	 $flag_saved = 0;
  }
  else if($_REQUEST['w_action'] == "save" )
  {
  	echo "<div id='message' class='wsecure_updated fade'>Settings Updated</div>";
	 $flag_saved = 0;
  }

   
   ?>
  <ul class="nav-tab-wrapper wsecuremenu">
    <li><a class="nav-tab-wsecure<?php $class = ($opt == 'adv') 	? $class = " nav-tab-wsecure-active" : $class = "";  echo $class; ?>" href="?page=<?php echo $_GET['page']; ?>&opt=adv">Advanced Configuration</a></li>
    <li><a class="nav-tab-wsecure<?php $class = ($opt == 'config')  ? $class = " nav-tab-wsecure-active" : $class = "";  echo $class; ?>" href="?page=<?php echo $_GET['page']; ?>&opt=config">Basic Configuration</a></li>
    <li><a class="nav-tab-wsecure<?php $class = ($opt == 'help') 	? $class = " nav-tab-wsecure-active" : $class = "";  echo $class; ?>" href="?page=<?php echo $_GET['page']; ?>&opt=help">Help</a></li>
  </ul>
  
  <?php 	


  
  if($_REQUEST['opt']=='config')
  { 
  	
  include(dirname(__FILE__).'/params.php');
  $WSecureConfig = new WSecureConfig();
   ?>
  
  <div class="wsecure_container" >
    <form name="save" id="save" method="post" action="options-general.php?page=wsecure-configuration" autocomplete="off" onsubmit="return validate();">
        <?php wp_nonce_field( $action ); ?> 
        
	<table class="form-table">
          
          	<tr valign="top">
            	<th class="wsecure_th" scope="row" ><label for="enable"><?php _e('Enable') ?></label></th>
                <td>
                    <select name="publish" id="enable" style="width:100px" class="wsecure_input" >
                        <option value="0" <?php echo ($WSecureConfig->publish == 0)?"selected":''; ?>><?php _e('No'); ?></option>
                        <option value="1" <?php echo ($WSecureConfig->publish == 1)?"selected":''; ?>><?php _e('Yes'); ?></option>
                    </select>
					<img class="wsecure_info" src="../wp-content/plugins/wsecure/images/wsecure_info.png" onmouseout="hideTooltip('wsecure_desc_publish' );" onmouseover="showTooltip('wsecure_desc_publish', 'Enable', 'For wSecure to be activated set this to yes and go to the plugin manager and Activate wSecure Lite plugin')" />
					<div class="setting-description" id="wsecure_desc_publish" ><?php _e('For wSecure to be activated set this to yes and go to the plugin manager and Activate wSecure Lite plugin'); ?></div>
                </td>		

			</tr>	
            
			 <tr valign="top">
        <th  class="wsecure_th"  scope="row"><label for="passkeytype">
          <?php _e('Pass Key') ?>
          </label></th>
        <td><select name="passkeytype" id="passkeytype" style="width:100px"  class="wsecure_input"  >
            <option value="url" <?php echo ($WSecureConfig->passkeytype == "url")?"selected":''; ?>>
            <?php _e('URL'); ?>
            </option>
            <option value="form" <?php echo ($WSecureConfig->passkeytype == "form")?"selected":''; ?>>
            <?php _e('FORM'); ?>
            </option>
          </select>
		  <img class="wsecure_info" src="../wp-content/plugins/wsecure/images/wsecure_info.png" onmouseout="hideTooltip('wsecure_desc_pass_key' );" onmouseover="showTooltip('wsecure_desc_pass_key', 'Pass Key', 'Select the mode in which you want to enter the key for authentication in wSecure.<br/><b>FORM</b> mode gives a customized form to enter the authentication key.<br/><b>URL</b> mode allows to enter the authentication directly in the url in the format /wp-admin?secretkey')" />
		 <div class="setting-description" id="wsecure_desc_pass_key" >
          <?php _e('Select the mode in which you want to enter the key for authentication in wSecure.<br/><b>FORM</b> mode gives a customized form to enter the authentication key.<br/><b>URL</b> mode allows to enter the authentication directly in the url in the format /wp-admin?secretkey.'); ?>
          </div> </td>
      </tr>
			
            <tr valign="top">
              <th scope="row" class="wsecure_th" ><label for="key"><?php _e('Key') ?></label></th>
              <td>
              		<input type="password" name="key" value="" size="50" id="key" class="wsecure_input regular-text"/>
				    <img class="wsecure_info" src="../wp-content/plugins/wsecure/images/wsecure_info.png" onmouseout="hideTooltip('wsecure_desc_secret_key' );" onmouseover="showTooltip('wsecure_desc_secret_key', 'Secret Key', 'Enter the new key here. For example, if your desired URL is /wp-admin/?secretkey then enter <b>secretkey</b> in this field. Please do not use any spaces or special characters.The key is case sensitive and can **ONLY** contain alphanumeric values. PLEASE dont use numeric values')" />
					<div class="setting-description" id="wsecure_desc_secret_key" ><?php _e('Enter the new key here. For example, if your desired URL is /wp-admin/?secretkey then enter "secretkey" in this field. Please do not use any spaces or special characters.The key is case sensitive and can **ONLY** contain alphanumeric values. PLEASE dont use numeric values'); ?></div>
              </td>
            </tr>
            
            <tr valign="top">
              <th scope="row" class="wsecure_th" ><label for="redirect_options"><?php _e('Redirect Options') ?></label></th>
              <td>
              	<select name="options" id="redirect_options" style="width:160px" onchange="javascript: hideCustomPath(this);"  class="wsecure_input"  >
					<option value="0" <?php echo ($WSecureConfig->options == 0)?"selected":''; ?>><?php _e('Redirect to index page'); ?></option>
					<option value="1" <?php echo ($WSecureConfig->options == 1)?"selected":''; ?>><?php _e('Custom Path'); ?></option>
				</select>
					<img class="wsecure_info" src="../wp-content/plugins/wsecure/images/wsecure_info.png" onmouseout="hideTooltip('wsecure_desc_redirect' );" onmouseover="showTooltip('wsecure_desc_redirect', 'Redirect Options', 'This sets where the user will be sent if they try to access the default Wordpress administrator URL (/wp-admin)')" />
					<div class="setting-description" id="wsecure_desc_redirect" ><?php _e('This sets where the user will be sent if they try to access the default Wordpress administrator URL (/wp-admin)'); ?></div>
              </td>
            </tr>
            
            <tr valign="top" id="custom_path">
              <th scope="row" class="wsecure_th" ><label for="custompath"><?php _e('Custom Path') ?></label></th>
              <td>
              	<input name="custom_path" type="text" value="<?php echo $WSecureConfig->custom_path; ?>" size="50" class="regular-text" id="custompath"  class="wsecure_input" />
                <span class="setting-description"><?php _e('Set the path to the page that will be displayed if the user tries to access the normal admin URL (/wp-admin)'); ?></span>
              </td>
            </tr>
            
          </table>

		  <input type="submit" name="Save" class="button-primary" value="Save" style="padding: 0px 18px;margin: 13px 0px;" />

				<input type="hidden" name="wsecure_action" value="update" />
       
    </form>
    
  
  </div>
  <?php
  }
  ?>
  <?php 
  if($_REQUEST['opt']=='help')
  {
  ?>
  <div class="wsecure_container" >
  	<h3 style="color:#2EA2CC;margin: 12px 0px 0px 0px;" ><?php _e('Drawback:'); ?></h3>
  	<p><?php _e('Wordpress has one drawback, any web user can easily know the site is created in Wordpress! by typing the URL to access the administration area (i.e. www.site name.com/wp-admin). This makes hackers hack the site easily once they crack username and password for Wordpress!.'); ?></p>
	
    <h3 style="color:#2EA2CC;" ><?php _e('Instructions:'); ?></h3>
  	<p><?php _e('wSecure Lite plugin prevents access to administration (back end) login page without appropriate access key.'); ?></p>
    
    <h3 style="color:#2EA2CC;" ><?php _e('Important! :'); ?></h3>
  	<p><?php _e('In order for wSecure to work the wSecure Lite plugin must be activated. Go to Plugins ->Plugin Manager and look for the "wSecure Lite plugin". Make sure this plugin is activated.'); ?></p>
    
    <h3 style="color:#2EA2CC;" ><?php _e('Basic Configuration:'); ?></h3>
  	<p>
		<?php _e('The basic configuration will hide your administrator URL from public access. This serves for the basic security threat for all wordpress websites.'); ?>
        <ul style="font-weight:bold;" >
        	<li><?php _e('1. Set "Enable" to "yes".'); ?></li>
			<li><?php _e('2. In the "Pass Key" field enter the option of URL or FORM.In the case of url the secret key will be added to url For example, if you enter "wSecure" into the key field, then the admin URL will be http://www.yourwebsite/wp-admin/?wSecure.<p>
If you choose the option form it will lead to the display of wSecure form where one can enter the secret key to gain admin access.</p>'); ?></li>
			<li><?php _e('3. In the "Key" field enter the key that will be part of your new administrator URL. For example, if you enter "wSecure" into the key field, then the administrator URL will be http://www.yourwebsite/wp-admin/?wSecure. Please note that you cannot have a key that is only numbers.
			<p>If you do not enter a key, but enable the wSecure component, then the URL to access the administrator area is /?wSecure (http://www.yourwebsite/wp-admin/?wSecure).</p>'); ?></li>
			<li><?php _e('4. Set the "Redirect Options" field. By default, if someone tries to access you /wp-admin URL without the correct key, they will be redirected to the home page of your Wordpress site. You can also set up a "Custom Path" is you would like the user to be redirected somewhere else, such as a 404 error page.'); ?></li>
        </ul>
    </p>
     <p>
    	<?php _e('For More information <a href="http://joomlaserviceprovider.com" title="http://joomlaserviceprovider.com" target="_blank">http://joomlaserviceprovider.com</a><br/>'); ?>
    </p>
	</div>
  <?php 
  }
  ?>
  <?php 
  if($_REQUEST['opt']=='adv')
  {
  ?>
  <div class="wsecure_container" >
  <p style="font-weight: bold;font-size: 15px;" >
  Please upgrade to <a title="Get Premium Version" href="http://www.joomlaserviceprovider.com/extensions/wordpress/commercial/wsecure-authentication.html" target="_blank" style="text-decoration:none;" >Premium Version</a> to enjoy the following list of advanced features.
  </p>
  
  	<hr/>
	<div class="wsecure_header_disp" >Current Features </div>	
	<hr/>
	<div class="wsecure_acc_parent" >
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >Mail
			<div class="wsecure_acc_child_desc" >Provides you an option whether you want an email to be sent every time there is a failed login attempt into the Wordpress administration area.<br/>You can set it to send the wSecure correct key or the incorrect key that was entered</div>
		</div>
		</div>
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >IP
			<div class="wsecure_acc_child_desc" > Provides an option to allow you to control which IPs have access to your admin URL.<br/><span style="min-width: 130px;width: 130px;display: inline-block;" >White Listed IPs:</span> If set to "White Listed IPs" you can make a white list for certain IPs. Only those specific IPS will be allowed to access your admin URL.<br/><span style="min-width: 130px;width: 130px;display: inline-block;" >Blocked IPs:</span> If set to "Blocked IPs" you can block certain IPs form accessing your admin URL.
			</div>
			</div>
		</div>
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >Master Password
			<div class="wsecure_acc_child_desc" >You can block access to the wSecure component from other administrators. Setting to "Yes", allows you to create a password that will be required when any administrator tries to access the wSecure configuration settings in the Wordpress administration area.</div>
		</div>
		</div>
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >Master Mail
			<div class="wsecure_acc_child_desc" >Provides an option to allow you to have an email sent every time any of the wSecure configuration is changed, so that you have record  of the new configuration made.</div>
		</div>
		</div>
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >Log
			<div class="wsecure_acc_child_desc" > This setting allows you to decide how long the wSecure logs should remain in the database. The longer this is set for, the more database space will be used.
			</div>
			</div>
		</div>
	</div>
  
  
		<hr/>
	<div class="wsecure_header_disp" >Upcoming Features</div>	
	<hr/>
	<div class="wsecure_acc_parent" >
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >AutoBan Ip
			<div class="wsecure_acc_child_desc" >With this feature you automate the process to add vulnerable IP addresses to Blacklisted/ Blocked IP'S, by just selecting the time duration and number of invalid admin access attempts.</div>
			</div>
		</div>
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >Master Password (upgrade)
			<div class="wsecure_acc_child_desc" >We  are upgrading the current feature of Master Password, to allow option to include/ exclude different sections of wSecure configurations in password protection of Master Password Protection.</div>
			</div>
		</div>
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >Directory Listing
			<div class="wsecure_acc_child_desc" >Directory listing to show list of all files and folders with their permissions on the site.
			</div>
			</div>
		</div>
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >Plugin Password Protection
				<div class="wsecure_acc_child_desc" >With this feature you can restrict access to different admin's of site for configuration and data of plugins that are installed.
You can set password for the admin side access of plugins that are installed and set option to "Enabled". This will restrict other administrators from accessing the protected plugins.</div>
			</div>
		</div>
		<div class="wsecure_acc_child" >
			<div class="wsecure_acc_child_title" >Log (upgrade)
			<div class="wsecure_acc_child_desc" > We  are upgrading the current feature of Log, we are going to add an option to directly add the IP's from Log to Blacklist or remove from blackList. So can analyze the Log and classify IP's directly.
			</div>
			</div>
		</div>
	</div>
  
  
</div>  
  <?php 
  }
  ?>
  
</div>

<script type="text/javascript">
	hideCustomPath(document.getElementById('redirect_options'));
</script>