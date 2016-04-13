<?php 
/*
Version: 2.3
Author: Ajay Lulia
Author URI: http://www.joomlaserviceprovider.com/
*/

//Checking for authenticate key value.

function ja_checkUrlKey()
{	
	if(!isset( $_SESSION['jSecureAuthentication'] ))
	{
		$_SESSION['jSecureAuthentication'] = "";
	}


	if(strpos($_SERVER['PHP_SELF'], 'wp-login.php') !== false && $_SESSION['jSecureAuthentication']=='')
		{ 
			include(dirname(__FILE__).'/params.php');
			$WSecureConfigg = new WSecureConfig();
			
			
			$publish = $WSecureConfigg->publish;
			$value = $WSecureConfigg->key;
			$options = $WSecureConfigg->options;
			$custom_path = $WSecureConfigg->custom_path;
			$home = get_bloginfo('home');
			$reditect_option = ($options=="0") ? $home : $custom_path ;
					
			if(intval($publish) != 1)
			{ 
				return;
			}			
					
			if($WSecureConfigg->passkeytype == "url")
			{
			
				$check_url = urldecode($_SERVER['QUERY_STRING']);		
				$get_key=explode("?",$check_url);
			
				if(strpos($get_key['1'],'&reauth')!== false)
				{
					$reauth=explode("&",$get_key['1']);
					$check_key = $reauth['0'];
				}
				else
				{
					$check_key = $get_key['1'];
				}
			
				
				
			}
			else
			{ 
			
			
			//echo "<br/>$value<>br/".md5(base64_encode($check_key));
				if(strtolower($_POST['submit']) != 'submit' )
				{
				 displayForm();
				 exit;
				}
	
				$check_key = $_POST['passkey'];				
			}
			
			if( $value != md5(base64_encode($check_key)) && $publish == '1') 		
			{
			unset($_SESSION['jSecureAuthentication']);
			wp_redirect( $reditect_option ); 
			}
			else 
			{
				$_SESSION['jSecureAuthentication'] = '1';
			}	
	}
	else
	{
			if($_SESSION['jSecureAuthentication'] !=1 || empty($_SESSION['jSecureAuthentication']) || $_SESSION['jSecureAuthentication'] == ''):
			$siteurl = get_bloginfo('siteurl');
			$home = get_bloginfo('home');
			unset($_SESSION['jSecureAuthentication']);
			wp_redirect( $reditect_option ); 
			endif;
	}
}

//After logout redirect to index page

function ja_logout()
{
	$home = get_bloginfo('home');
	$_SESSION['jSecureAuthentication'] = null;
	if(!is_admin())
	{
		$_SESSION['jSecureAuthentication'] = null;
		unset($_SESSION['jSecureAuthentication']);
		wp_redirect( $home );
		exit;
	}
}



function displayForm(){
		
?>
<div style="background: rgb(25, 119, 163);margin: 0px !important;padding: 0px !important;position: absolute;width: 100%;top: 0px;bottom: 0px;right: 0px;left: 0px;overflow:hidden;" >

<form name="key" action="" method="post" autocomplete="off">
	<div style="border: 2px solid #E3E7E9;margin: 9% 38%;padding: 0% 1%;background: #F1F1F1;" >
		<div class="wsecure_key" style="background-image: url('./wp-content/plugins/wsecure/images/wsecure_key.jpg');width: 149px;height: 140px;margin: 10px auto 0;border-radius: 40px;-moz-border-radius: 40px;-webkit-border-radius: 40px;margin-top: 35px;margin-bottom: 11px" ></div>
		<div style="margin-bottom: 30px !important;" >
			<p style="font-weight: normal;font-size: 22px;text-align: center;color: #2EA2CC;
padding-top: 8px !important;margin: 0px;font-family: arial;text-transform: uppercase;" >Admin Key</p> 
			<p style="margin: 15px 0px;padding: 0px;text-align: center;" >
			<!-- <p style="text-align: center;" ><label for="passkey_id" style="font-family: Arial;font-size: 15px;text-align: center;" >Enter security key </label></p> -->
			<p style="padding: 0px 5px;text-align: center;margin:0px !important;"  >
			<input type="password" name="passkey" id="passkey_id" value="" style="width: 78%;line-height: 32px;font-size: 17px;padding: 0px 6px;" placeholder="Enter security key" /></p>
		
			<p style="text-align:center;margin:5px 0px !important;" ><input type="submit" name="submit" value="Submit" style="background: #2EA2CC;padding: 7px 18px;color: #FFF;border: 0px;cursor: pointer;cursor: hand;width: 76%;line-height: 22px;font-size: 16px;" /></p>
	</p>			
		</div>
		
	</div>
</form>
</div>
<?php 
	} 
 ?>