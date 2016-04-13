<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<?php /*?><title><?php wp_title( '|', true, 'right' ); ?></title><?php */?>
<title><?php wp_title( '', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<!-- Latest compiled and minified CSS -->
<?php wp_head(); ?>
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link
	href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css"
	rel="stylesheet" />
<link rel="stylesheet" href="<?php echo WP_HOME; ?>/css/custom.css">

<!- fgfg-->
<!--<script src="http://code.jquery.com/jquery.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<link rel="stylesheet"
      href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
<link rel="stylesheet"
     href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap-theme.min.css">-->

<!-fgf-->
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script
	src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script
	src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js"></script>
<script
	src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

<script>
    var API_URL = '<?php echo getenv('API_URL'); ?>';
    var CLIENT_ID = '<?php echo getenv('CLIENT_ID'); ?>';

    jQuery(function() {
        if (window.location.href.search('visitor=%E2%9D%8C') !== -1) {
            jQuery('#menu-signin-signup').addClass('hide');
            jQuery('a').each(function(index, node) {
                var elm = jQuery(node).attr('href');

                elm == -1 ? []: jQuery(node).attr('href', elm + '?&visitor=%E2%9D%8C');
            });

            var backBtn = jQuery('#menu-main-menu li:first');

            jQuery('#menu-main-menu li:last').after('<li><a href="http://' + window.location.host + '/dashboard/admin">Back</a></li>');
        }
    });

/*     function cleardropdown() {
        alert("inside");
	    // To clear dropdown values we need to write code like as shown below
	    $('#city').empty();
	    // Bind new values to dropdown
	    $('#city').each(function() {
	    // Create option
	    var option = $("<option />");
	    option.attr("value", '0').text('Select City');
	    $('#city').append(option);
	    });
    } */
//$http.get(generateUrl('v1/categories'),$scope.societyTypes)
//    .then(function(response){
//        $scope.societyTypes = response.data.results.data;
//    });

</script>
<style>
    #society-form a:hover, #society-form a{
        text-decoration: none;
    }
    #society-join-form a:hover, #society-join-form a{
       text-decoration: none;
    }
    .radio-group .form-group{margin-bottom: 0px;}
</style>
</head>

<body <?php body_class(); ?>>

	<div class="site-loader-icon"></div>
	<div id="page" class="hfeed site">
		<header id="masthead" class="site-header" role="banner">
			<div class="grid-920">
				<div class="inner">
					<hgroup>
						<h1 class="site-title">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
								title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"
								rel="home"><?php bloginfo( 'name' ); ?></a>
						</h1>
						<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
					</hgroup>
					<div class="logo-grid">
					<?php if ( get_header_image() ) : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img
							src="<?php header_image(); ?>" class="header-image"
							width="<?php echo esc_attr( get_custom_header()->width ); ?>"
							height="<?php echo esc_attr( get_custom_header()->height ); ?>"
							alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" /></a>
					<?php endif; ?>
				</div>
					<div class="responsive-menu-trigger-grid">
						<button class="menu-toggle"><?php /*?><?php _e( 'Menu', 'twentytwelve' ); ?><?php */?><span
								class="layer top"></span> <span class="layer mid"></span> <span
								class="layer btm"></span>
						</button>
					</div>
					<div class="signin-signup-menu-grid">
					<?php wp_nav_menu( array( 'theme_location' => 'signin-signup-menu' ) ); ?>
				</div>
					<nav id="site-navigation" class="main-navigation" role="navigation">
						<a class="assistive-text" href="#content"
							title="<?php esc_attr_e( 'Skip to content', 'twentytwelve' ); ?>"><?php _e( 'Skip to content', 'twentytwelve' ); ?></a>
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
				</nav><!-- #site-navigation -->
			</div>
		</div>
	</header><!-- #masthead -->
	<div class="banner-grid">
		<div class="grid-920">
			<?php dynamic_sidebar( 'banner-1' )  ?>
		</div>
	</div>

	<!-- Login Form Modal -->
	<div class="modal fade" id="formLoginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" id="login_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="societyModalLabel">Sign In</h4>
			  </div>
			  <div class="modal-body">
				<form id="login-form" method="post" action="">
					<div class="form-group">
					  <label class="form-label" >Email</label>
					  <input type="text" class="form-control" name="email"  placeholder="My Email">
					</div>
					<div class="form-group">
					  <label class="form-label" >Password</label>
					  <input type="password" id="login-password" class="form-control" name="password"  placeholder="Password">
					</div>
					<div class="row">
						<div class="col-md-6">
                           <button type="submit" class="btn btn-primary">Submit</button>
                           <button type="button" id="login_cancel" class="btn btn-primary">Cancel</button>
						</div>
						<div class="col-md-6">
							<span class="pull-right"><a href="#" id="fgt_pwd">Forgot Password?</a></span>
						</div>
					</div>
				</form>
                <link href="<?php echo WP_HOME; ?>/css/loader.css" rel="stylesheet" type="text/css" />
                <div id="loader" class="loading">Loading&#8230;</div>
			  </div>
			</div>
		</div>
	</div>

        <!-- Forgot Password modal--->
            <div class="modal fade" id="ForgotPwdModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" id="frgot_pwd" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="societyModalLabel">Forgot Password!</h4>
                      </div>
                      <div class="modal-body">
                        <form id="fgt_pwd-form" method="post" action="">
                            <div class="form-group">
                              <label class="form-label" >Email</label>
                              <input type="text" class="form-control" name="email" id="user_email"  placeholder="Please enter your email address!">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success">Reset</button>
                                </div>
                            </div>
                        </form>
                      </div>
                    </div>
                </div>
            </div>

            <!---->

	<!-- Society Form Modal -->
	<div class="modal fade" id="formSocietyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" id="register-close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create Society</h4>
                </div>
                <div class="modal-body">
                    <span id="validation_msg" class="warning_msg"></span>
                    <form id="society-form" method="post" action="">
                    <!--accrodian-->

                        <div class="DemoBS2">
                            <div class="panel-group" id="accordion"> <!-- accordion 1 -->
                                <div class="panel panel-default">
                                    <div class="panel-heading"> <!-- panel-heading -->
                                        <h4 class="panel-title"> <!-- title 1 -->
                                           <span class="form-label">Society Information</span>
											<a data-toggle="collapse" class="pull-right glyphicon glyphicon-plus" data-parent="#accordion" href="#accordionOne" id="accordionOneanchor" >
                                        </a>
                                       </h4>
                                    </div>
                                    <!-- panel body -->
                                    <div id="accordionOne" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="form-label">Society Name</label>
                                                <input id="sname" type="text" class="form-control" name="name"  placeholder="Society Name">
                                            </div>
                                            <div class="form-group">
                                                    <label class="form-label">Society Type</label> <select id="SocietyType"
                                                             name="society_category_id" class="form-control">
                                                            <option value="" disabled="" selected="">Select Society Type</option>
                                                    </select>
                                                    <div class="visiblity_state_error"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Address Line 1</label>
                                                <textarea class="form-control" name="address"
                                                           placeholder="Address"></textarea>
                                            </div>
                                            <div class="form-group">
                                                   <label>Address Line 2</label>
                                                   <textarea class="form-control" name="address_line_2"
                                                           placeholder="Address"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">  <!-- accordion 2 -->
                                    <div class="panel-heading">
                                        <h4 class="panel-title"> <!-- title 2 -->
										 <span class="form-label">Location</span>
                                          <a data-toggle="collapse" class="pull-right glyphicon glyphicon-plus" data-parent="#accordion" href="#accordionTwo"   id="accordionTwoanchor">
                                          </a>
                                        </h4>
                                    </div>
                                    <!-- panel body -->
                                    <div id="accordionTwo" class="panel-collapse collapse">
                                        <div class="panel-body">

                                            <div class="form-group">
                                                   <label class="form-label">State</label> <select id="state"
                                                           name="state_id" class="form-control">
                                                           <option value="" disabled="" selected="">Select State</option>
                                                   </select>
                                                   <div class="visiblity_state_error"></div>
                                            </div>
                                            <div class="form-group">
                                                   <label class="form-label">City</label> <select name="city_id"
                                                           id="city" class="form-control">
                                                           <option value="" disabled="" selected="">Select City</option>
                                                   </select>
                                                   <div class="visiblity_city_error"></div>
                                            </div>
                                            <div class="form-group">
                                                   <label class="form-label">Pincode</label> <input type="text"
                                                           class="form-control" name="pincode" placeholder="Pincode">
                                            </div>
                                            <div class="form-group">
                                                   <label>Landmark</label> <input type="text"
                                                           class="form-control" name="landmark" placeholder="Landmark">
                                            </div>
                                            <div class="form-group">
                                                   <label>Nearest Station/Bus Depot</label> <input
                                                           type="text" class="form-control" name="nearest_station"
                                                           placeholder="Nearest Station">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">  <!-- accordion 3 -->
                                    <div class="panel-heading">
                                        <h4 class="panel-title"> <!-- title 3 -->
										   <span class="form-label">User</span>
                                          <a data-toggle="collapse"  id="accordionThreeanchor" class="pull-right glyphicon glyphicon-plus" data-parent="#accordion" href="#accordionThree">
                                          </a>
                                        </h4>
                                    </div>
                                    <div id="accordionThree" class="panel-collapse collapse">
                                    <!-- panel body -->
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label class="form-label">First Name</label> <input type="text"
                                                        class="form-control" name="first_name" placeholder="First Name">
                                            </div>
                                            <div class="form-group">
                                                    <label class="form-label">Last Name</label> <input type="text"
                                                            class="form-control" name="last_name" placeholder="Last Name">
                                            </div>
                                            <div class="form-group">
                                                    <label class="form-label">Email</label> <input type="text"
                                                            class="form-control" id="registration-email" name="email"
                                                            placeholder="Email Id">
                                            </div>
                                            <div class="form-group">
                                                    <label class="form-label">Mobile No</label> <input type="text"
                                                            class="form-control" name="contact_no" placeholder="Mobile No">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--end-->
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" id="register-cancel"class="btn btn-primary">Cancel</button>
                    </form>
                    <link href="<?php echo WP_HOME; ?>/css/loader.css" rel="stylesheet" type="text/css" />
                    <div id="society_loader" class="loading">Loading&#8230;</div>
                </div>
            </div>
        </div>
    </div>

		<!-- Society Join Form Modal -->
		<div class="modal fade" id="joinSocietyModal" tabindex="-1"
			role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close join-cancel" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="joinSocietyTitle"></h4>
					</div>
					<div class="modal-body">
                        <span id="join_validation_msg" class="warning_msg"></span>
						<form id="society-join-form" method="post" action="">
                             <div class="DemoBS2">
                                <div class="panel-group" id="joinAccordion"> <!-- accordion 1 -->
                                    <div class="panel panel-default">
                                        <div class="panel-heading"> <!-- panel-heading -->
                                            <h4 class="panel-title"> <!-- title 1 -->
                                               <span class="form-label">Flat Information</span>
                                                <a data-toggle="collapse" class="pull-right glyphicon glyphicon-plus" data-parent="#joinAccordion" href="#joinAccordionOne" id="joinAccordionOneanchor" >
                                            </a>
                                           </h4>
                                        </div>
                                        <div id="joinAccordionOne" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label class="form-label">My Building</label> <select
                                                        class="form-control" name="building_id" id="building-select">
                                                        <option value='' disabled selected>Select Building</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>My Block</label> <select class="form-control"
                                                        name="block_id" id="block-select">
                                                                                        <option value='' disabled selected>Select Block</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">My Flat</label> <input type="text"
                                                        class="form-control" name="flat_no" maxlength="4"
                                                        placeholder="My Flat" id="join_flat_no">
                                                </div>
                                                <div class="form-group occupancy-radio-group radio-group">
                                                    <label class="form-label">Occupancy</label>
                                                    <div class="form-group">
                                                        <div class="radio-inline">
                                                            <label> <input type="radio" name="relation" value="owner">
                                                                Owner
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label> <input type="radio" name="relation" value="tenant">
                                                                Tenant
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group type-radio-group radio-group">
                                                    <label class="form-label">Type</label>
                                                    <div class="form-group ">
                                                        <div class="radio-inline">
                                                            <label> <input type="radio" name="type" value="office"> Office
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label> <input type="radio" name="type" value="shop"> Shop
                                                            </label>
                                                        </div>
                                                        <div class="radio-inline">
                                                            <label> <input type="radio" name="type" value="flat"> Flat</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel panel-default">
                                        <div class="panel-heading"> <!-- panel-heading -->
                                            <h4 class="panel-title"> <!-- title 1 -->
                                               <span class="form-label">User</span>
                                                <a data-toggle="collapse" class="pull-right glyphicon glyphicon-plus" data-parent="#joinAccordion" href="#joinAccordionTwo" id="joinAccordionTwoanchor" >
                                            </a>
                                           </h4>
                                        </div>
                                        <div id="joinAccordionTwo" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label class="form-label">Email</label> <input type="text"
                                                        class="form-control" id="registration-email" name="email"
                                                        placeholder="My Email">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">First Name</label> <input type="text"
                                                        class="form-control" name="first_name" placeholder="First Name">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Last Name</label> <input type="text"
                                                        class="form-control" name="last_name" placeholder="Last Name">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Mobile No</label> <input type="text"
                                                        class="form-control" name="contact_no" placeholder="Mobile No">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                             </div>


							<input type="hidden" id="join-society-id" name="society_id"
								value="0" />
							<button type="submit" class="btn btn-primary">Submit</button>
							<button type="button" class="join-cancel btn btn-primary">Cancel</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Successful Society Registration Modal -->
		<div class="modal fade" id="societySuccessModal" tabindex="-1"
			role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="societyModalLabel">Thank you for
							registering society.</h4>
					</div>
					<div class="modal-body">
						<p>Your society has been created successfully! Please check your
							email for login details.</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Successful Society Join Request  Modal -->
		<div class="modal fade" id="societyJoinSuccessModal" tabindex="-1"
			role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close join-cancel" data-dismiss="modal"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="societyModalLabel">Thank you for
							creating account.</h4>
					</div>
					<div class="modal-body">
						<p>Your account has been created successfully. Will be approved by
							society admin. Please check your email for login details.</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Society  Search Registration Modal -->
		<div class="modal fade" id="searchSocietyModal" role="dialog"
			aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close join-cancel" data-dismiss="modal"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="societyModalLabel">Join Society</h4>
					</div>
					<div class="modal-body">
						<form id="search-society-form" method="post" action="">
							<div class="form-group">
								<div class="input-group">
									<select class="js-data-example-ajax js-example-responsive"
										style="width: 100%">
										<option value="0" selected="selected"></option>
									</select> <span class="input-group-addon" id="society-join-btn">Join</span>
								</div>

								<!--<a href="javascript:void(0);" id="society-join-btn" class="btn btn-success" >Join</a>-->
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function()
		{
                 jQuery('#loader').hide();
                 jQuery('#society_loader').hide();



            jQuery(':input[type="text"], textarea').change(function() {
                jQuery(this).val(jQuery(this).val().trim());
            });

	 	  jQuery("#accordionThree").on("hide.bs.collapse", function(){
		    jQuery('#accordionThreeanchor').addClass('glyphicon-plus');
		    jQuery('#accordionThreeanchor').removeClass('glyphicon-minus');
		  });

		  jQuery("#accordionThree").on("show.bs.collapse", function(){
		     jQuery('#accordionThreeanchor').addClass('glyphicon-minus');
		     jQuery('#accordionThreeanchor').removeClass('glyphicon-plus');
		  });

		  jQuery("#accordionTwo").on("hide.bs.collapse", function(){
		    jQuery('#accordionTwoanchor').addClass('glyphicon-plus');
		    jQuery('#accordionTwoanchor').removeClass('glyphicon-minus');
		  });

		  jQuery("#accordionTwo").on("show.bs.collapse", function(){
		     jQuery('#accordionTwoanchor').addClass('glyphicon-minus');
		     jQuery('#accordionTwoanchor').removeClass('glyphicon-plus');
		  });


		  jQuery("#accordionOne").on("hide.bs.collapse", function(){
		    jQuery('#accordionOneanchor').addClass('glyphicon-plus');
		    jQuery('#accordionOneanchor').removeClass('glyphicon-minus');
		  });

		  jQuery("#accordionOne").on("show.bs.collapse", function(){
		     jQuery('#accordionOneanchor').addClass('glyphicon-minus');
		     jQuery('#accordionOneanchor').removeClass('glyphicon-plus');
		  });

          jQuery("#joinAccordionTwo").on("hide.bs.collapse", function(){
		    jQuery('#joinAccordionTwoanchor').addClass('glyphicon-plus');
		    jQuery('#joinAccordionTwoanchor').removeClass('glyphicon-minus');
		  });

		  jQuery("#joinAccordionTwo").on("show.bs.collapse", function(){
		     jQuery('#joinAccordionTwoanchor').addClass('glyphicon-minus');
		     jQuery('#joinAccordionTwoanchor').removeClass('glyphicon-plus');
		  });


		  jQuery("#joinAccordionOne").on("hide.bs.collapse", function(){
		    jQuery('#joinAccordionOneanchor').addClass('glyphicon-plus');
		    jQuery('#joinAccordionOneanchor').removeClass('glyphicon-minus');
		  });

		  jQuery("#joinAccordionOne").on("show.bs.collapse", function(){
		     jQuery('#joinAccordionOneanchor').addClass('glyphicon-minus');
		     jQuery('#joinAccordionOneanchor').removeClass('glyphicon-plus');
		  });

//<!-- Society Join Code -->

            function getBuildings(buildingId){
                jQuery.ajax({
                     url: API_URL+'society/join/buildings/'+buildingId,
                     method: "GET",
                     dataType: "json",
                    })
                    .success(function(r) {
                        console.log(r);
                        var buildings = r.response.data;
                        var buildingSelect = jQuery("#building-select");
                        buildingSelect.html("<option value ='' disabled selected>Select Building</option>");
                        jQuery.each(buildings, function (i, el) {
                            buildingSelect.append("<option value ='"+el.id+"'>" + el.name + "</option>");
                        });
                    }).error(function(response){
                        console.log('Building fetch error');
                    });
            }

            jQuery("#building-select").change(function(){
                var buildingId = jQuery(this).val();
                if(buildingId != null)
                {
                    jQuery.ajax({
                        url: API_URL+'society/join/building/blocks/'+buildingId,
                        method: "GET",
                        dataType: "json",
                    })
                    .success(function(r) {
                        var blocks = r.response.data;
                        var blockSelect = jQuery("#block-select");

                        if(r.response.data.length > 0) {
							jQuery(blockSelect.prev('label')[0]).addClass('form-label');
							blockSelect.rules("add", {required:true});

                        } else {
                        	jQuery(blockSelect.prev('label')[0]).removeClass('form-label');
                        	blockSelect.rules("add", {required:false});
                        }
                        blockSelect.html("<option value ='' disabled selected>Select Block</option>");
                        jQuery.each(blocks, function (i, el) {
                            blockSelect.append("<option value ='"+el.id+"'>" + el.block + "</option>");
                        });

                    }).error(function(response){
                             console.log('Society blocks error');
                    });
                }
            });

           var $eventSearch = jQuery(".js-data-example-ajax").select2({
                ajax: {
                  url: API_URL+'society/search',
                  dataType: 'json',
                  delay: 250,
                  data: function (params) {
                    return {
                      search: params.term, // search term
                      page: params.page
                    };
                  },
                  processResults: function (result, page) {
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data
//                    console.log(result.data);
                    return {
                      results: result.data
                    };
                  },
                  cache: true
                },
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 3,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
              });

              function formatRepo (repo) {
                if (repo.loading) return repo.name;

                var markup = '<div class="clearfix">' +

                '<div clas="col-sm-10">' +
                '<div class="clearfix">' +
                '<div class="col-sm-6">' + repo.name + ' - ' + repo.address+ '</div>'

                '</div>';
                markup += '</div></div>';

                return markup;
              }

              function formatRepoSelection (repo) {
//                  console.log(repo.id);
                  if(repo.id != 0){
//                        jQuery('#searchSocietyModal').modal('hide');
                        jQuery('#joinSocietyTitle').text('Join '+repo.name);
                        jQuery('#join-society-id').val(repo.id);
                        getBuildings(repo.id);
//                        jQuery('#joinSocietyModal').modal();
                  }

                return repo.name;
              }

             $eventSearch.on("select2:open", function (e) {
                        var text_val =  jQuery('.select2-selection__rendered').text();
                        jQuery('.select2-search__field').val(text_val);
             });

            jQuery('#society-join-btn').on('click',function(){
                if(jQuery('#join-society-id').val() != 0){
                    jQuery('#searchSocietyModal').modal('hide');
                    jQuery('#joinSocietyModal').modal();
                }

            });

            jQuery('.join_society-link').on('click',function(){
                        jQuery('.select2-selection__rendered').text('');
                       jQuery('#searchSocietyModal').modal();   // Search society modal
            });


//<!--- Society join code ends --->

			// Login Society form buttom event
			jQuery('#menu-item-50').on('click',function(){
                jQuery('#formLoginModal').modal();
            });
            jQuery('#fgt_pwd').on('click',function(){
                jQuery('#ForgotPwdModal').modal();
                 jQuery('#formLoginModal').modal('hide');
            });

            jQuery("#fgt_pwd-form").validate({
                rules: {
                  // simple rule, converted to {required:true}
                  password: "required",
                  // compound rule
                  email: {
                    required: true,
                    email: true
                  }
                }
            });

            jQuery('#login_close').on('click',function(){
                jQuery("#login-form")[0].reset();
                jQuery("#login-form label.error").remove();
                jQuery('#formLoginModal').modal('hide');
            });

            jQuery('#login_cancel').on('click',function(){
                jQuery("#login-form")[0].reset();
                jQuery("#login-form label.error").remove();
                jQuery('#formLoginModal').modal('hide');
            });

           jQuery('#fgt_pwd-cancel').on('click',function(){
                jQuery("#fgt_pwd-form")[0].reset();
                jQuery("#fgt_pwd-form label.error").remove();
                jQuery('#ForgotPwdModal').modal('hide');
                jQuery('#formLoginModal').modal();
            });

            jQuery('#frgot_pwd').on('click',function(){
                jQuery("#fgt_pwd-form")[0].reset();
                jQuery("#fgt_pwd-form label.error").remove();
                jQuery('#ForgotPwdModal').modal('hide');
                jQuery('#formLoginModal').modal();
            });

            jQuery('#fgt_pwd-form').submit(function(e){
                e.preventDefault();
                var user_email =jQuery('#user_email').val();
                if (jQuery("#fgt_pwd-form").valid()){
                   jQuery(this).find('button[type=submit]').attr('disabled',true);
                    jQuery(this).find('button[type=submit]').text('Reset password please wait...');
                    jQuery.ajax({
                       url: API_URL+'society/check_useremail',
                       method: "POST",
                       dataType: "json",
                       data: {email:user_email}
                   })
                    .success(function(response) {
                       jQuery("#fgt_pwd-form").find('button[type=submit]').attr('disabled',false);
                        jQuery("#fgt_pwd-form").find('button[type=submit]').text('Submit');
                      var result = response;
                       if(result.success){
                            isSuccess = true;
                          jQuery( "#user_email" ).after( '<label id="user_email-success" class="text-success" for="user_email">You will be receiving an email for resetting your password shortly. Please check your email!</label>' );
                           setTimeout(function(){ location.reload(); }, 2000);
                       }else{
                           jQuery("label#login-password-error").remove();
                           jQuery("label#password-error").remove();
                           jQuery( "label#user_email-error" ).remove();
                           isSuccess = false;
                        jQuery( "#user_email" ).after( '<label id="user_email-error" class="error" for="user_email">Invalid Username!</label>' );
                       }
                   }).error(function(response){
                       return false;
                   }).then(function(response){
                       console.log(isSuccess);
                       return isSuccess;
                   });
                }

            });
           jQuery("#login-form").validate({
                rules: {
                  // simple rule, converted to {required:true}
                  password: "required",
                  // compound rule
                  email: {
                    required: true,
                    email: true
                  }
                }
            });

			// Create Society form  button event
            jQuery('#menu-item-51').on('click',function(){
                jQuery('#formSocietyModal').modal();
               setTimeout(function(){
				jQuery('#sname').focus();
				}, 500);

            });

            closeSocietyForm = function(){
                jQuery("#society-form")[0].reset();

                jQuery("#society-form label.error").remove();
                jQuery('#city').empty();
                jQuery('#city').find('option').remove();
                jQuery('#formSocietyModal').modal('hide');
                jQuery('#city').val('');
            };

            closeJoinSocietyForm = function(){
                jQuery('#join-society-id').val(0);
                jQuery("#society-join-form")[0].reset();
                jQuery('#society-join-form').find('button[type=submit]').attr('disabled',false);
                jQuery('#society-join-form').find('button[type=submit]').text('Submit');
                jQuery("#join_validation_msg").hide();
                jQuery("#society-join-form label.error").remove();
                jQuery('#joinSocietyModal').modal('hide');
            };


            jQuery('#register-close, #register-cancel').on('click',function(){
                jQuery("#society-form")[0].reset();
				jQuery('#accordionOne').collapse("hide");
			    jQuery('#accordionTwo').collapse("hide");
				jQuery('#accordionThree').collapse("hide");
                jQuery("#validation_msg").hide();
                jQuery("#society-form label.error").remove();
                jQuery('#city').find('option').remove();
                jQuery('#city').find('option')
		               jQuery('#city').each(function() {
		        		    // Create option
		        		    var option = jQuery("<option />");
		        		    option.attr("value", '0').prop('selected', true).text('Select City');
		        		    jQuery('select>option:eq(0)').prop('selected', true);
		        		    jQuery('#city').append(option);
		        		    });
                var combo = jQuery("#city");
                combo.html("<option value ='' disabled=''>Select City</option>");

                getStates();
                getCities(1); // Fetched Maharashtra cities.
                jQuery('#formSocietyModal').modal('hide');
                jQuery('#city').val('');


            });

             jQuery('.join-cancel').on('click',function(){
                closeJoinSocietyForm();
                window.location.reload();
            });

//            jQuery('.join_success-cancel').on('click',function(){
//                location.reload();
//            });


            jQuery('#login-form').submit(function(e){
                e.preventDefault();
                if (jQuery("#login-form").valid()){
                     jQuery('#loader').show();
                    var data = jQuery( this ).serializeArray();
                    data.push({name: 'client_id',value:CLIENT_ID});
                    jQuery(this).find('button[type=submit]').attr('disabled',true);
                    jQuery(this).find('button[type=submit]').text('logging in please wait..');
                    jQuery.ajax({
                        url: API_URL+'getAccessToken',
                        method: "POST",
                        data: data
                    })
                    .success(function(data) {
                        jQuery('#loader').hide();
                         jQuery("#login-form").find('button[type=submit]').attr('disabled',false);
                        jQuery("#login-form").find('button[type=submit]').text('Submit');
                        if(data.success){
                            jQuery.ajax({
                                url: '<?php echo WP_SITEURL; ?>dashboard/login',
                                method: "POST",
                                dataType:"json",
                                data: {access_token:data.access_token,user:data.user,socities:data.socities,acl:data.acl,role_name:data.role_name}
                            })
                            .success(function(data)
                            {
                                if (data.success){
                                    window.location='<?php echo WP_SITEURL; ?>'+data.redirect_url;
                                }else{
                                    console.log('Session could not saved');
                                }

                            }).error(function(response)
                            {
                                console.log('Store session error');
                                console.log(response);
                            });
                        }else{
                                  jQuery("label#password-error").remove();
                                jQuery( "label#login-password-error" ).remove();
                                 jQuery("label#password-error").remove();
                        	jQuery( "#login-password" ).after( '<label id="password-error" class="error" for="password">'+data.msg+'</label>' );
/*                             if(data.deactive)
                            {
                                jQuery( "#login-password" ).after( '<label id="password-error" class="error" for="password">User is deactivate.</label>' );
                            }else{
                                jQuery( "#login-password" ).after( '<label id="password-error" class="error" for="password">Please check email and password.</label>' );
                            }
 */                        }

                    }).error(function(response){
                        jQuery(this).find('button[type=submit]').attr('disabled',false);
                        jQuery(this).find('button[type=submit]').text('submit');

                    });
                }
            });

			// varible to check ajax email valiadation is applied or not
            var ajaxEmailValidation = true;
            jQuery.validator.addMethod("validateUserEmail", function(value, element) {
                var isSuccess;
                jQuery.ajax({
                     url: API_URL+'society/checkemail',
                     method: "POST",
                     dataType: "json",
                     data: {email:value}
                 })
                 .success(function(r) {
                    var result = r.response;
                     if(result.success){
 //                        console.log(result.success);
                         isSuccess = true;
 //                        return true;
                     }else{
                         jQuery('#warning-msg').html(result.msg);
                         jQuery('#formSocietyModal').modal('hide');
                         jQuery('#emailConfirmModal').modal();
                         isSuccess = false;
 //                        return false;
                     }
                 }).error(function(response){
                     return false;
                 }).then(function(response){
                     console.log(isSuccess);
                     return isSuccess;
                 });

 //                if(true)
 //                {
 //                    return true;
 //                }else{
 //                    jQuery('#formSocietyModal').modal('hide');
 //                    jQuery('#emailConfirmModal').modal();
 //                    return false;
 //                }
             }, 'Email address is already taken');

             jQuery('.confirmed-yes').on('click',function(){
                 // Disable ajax validation if user want to continue
                 ajaxEmailValidation = false;
 //                jQuery( "#registration-email" ).rules( "remove", "validateUserEmail" ); // Remove ajax email validation
                 jQuery( "#registration-email" ).rules( "remove", "remote" ); // Remove ajax email validation
                 jQuery('#emailConfirmModal').modal('hide');
                 jQuery('#formSocietyModal').modal();
                 jQuery('#registration-email-error').remove();
                 console.log(jQuery('#registration-email').rules());
             });

             jQuery('.confirmed-no').on('click',function(){
                 jQuery('#emailConfirmModal').modal('hide');
                 jQuery('#formSocietyModal').modal();
             });

             jQuery('#emailConfirmModal').on('hide.bs.modal', function () {
                 jQuery("#formSocietyModal").css("overflow-y", "auto"); // 'auto' or 'scroll'
             });

             jQuery('#joinSocietyModal').on('show.bs.modal', function () {
                 jQuery(this).css("overflow-y", "auto"); // 'auto' or 'scroll'
				 jQuery('body').css("overflow-y", "hidden");
             });

           jQuery("#society-form").validate({
//                onkeyup: false,
                  ignore: [],
                rules: {
                  // simple rule, converted to {required:true}
                    name: "required",
                    first_name:
                            {
                              required:true,
                              user_name:true
                            },
                    last_name:
                            {
                                required:true,
                                user_name:true
                            },
                    relation: "required",
                    society_category_id:"required",
                    address: "required",
                    block : "required",
                    state_id:"required",
                    city_id:"required",
//                    nearest_station:"required",
//                    landmark:"required",
                    flat_no:{
                        required: true,
                        number: true
                    },
                    contact_no:{
                        required:true,
                        maxlength:10,
                        minlength:10,
                        number:true,
                    },
                    pincode:{
                      required: true,
                      number: true,
                      minlength: 6
                    },
                  // total_flats:{
                    // required: true,
                    // number: true
                  // },

                  // compound rule
                  email: {
                    required: true,
                    domain: true,
                    remote: {
                        url: API_URL+'checkemail',
                        method: "POST",
                        type: "json",
                        data : {
                            email_id: function() {
                                return jQuery("#registration-email").val();
                            }
                        },
                        success: function(result) {
                            console.log(result);
                            if (result.success) {
                                jQuery("label#registration-error").remove();
                                console.log(result.success);
                            }
                            else {
                                jQuery("label#registration-email-error").remove();
                                jQuery("label#registration-error").remove();
                                jQuery( "#registration-email" )
                                        .after( '<label id="registration-error" class="error" for="email">'+result.msg+'</label>' );
                            }
                        }
                    },
//                    validateUserEmail:true,
//                    remote:{
//                            url: API_URL+'society/checkemail',
//                            type: "post",
//                            data: {
//                              email: function() {
//                                return jQuery( "#registration-email" ).val();
//                              }
//                            },
//                            success:function(r) {
//                                var result = r.response;
//                                 if(result.success){
//                                     return true;
//                                 }else{
//                                     jQuery('#warning-msg').html(result.msg);
//                                     jQuery('#formSocietyModal').modal('hide');
//                                     jQuery('#emailConfirmModal').modal();
//                                     return false;
//                                 }
//                             }
//                          }
                  }
                },
                messages: {
                    contact_no: {
                        required: "This field is required",
                        number: "Please enter valid mobile number",
                        maxlength:"Please enter valid mobile number",
                        minlength:"Please enter valid mobile number"
                    },
                    email: {
                        domain: "Please enter a valid email address."
                    },
                    first_name: {
//                        required: "We need your email address to contact you",
                        user_name: "Special characters and numbers are not allowed."
                    },
                    last_name: {
//                        required: "We need your email address to contact you",
                        user_name: "Special characters and numbers are not allowed."
                    }
                },
//                errorPlacement: function(error, element) {
//                    if (element.attr("name") == "relation"  ) {
//                        jQuery( "#society-form .form-group.type-radio-group" ).append( error );
//                    }else {
//                      error.insertAfter(element);
//                    }
//                }

                 errorPlacement: function(error, element){
                    if (element.attr("name") == "state_id"  ) {
                        jQuery( ".visiblity_state_error" ).html( error );
                    }else if (element.attr("name") == "city_id"  ) {
                       jQuery( ".visiblity_city_error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
            });
//            jQuery('#loader').hide();
			jQuery('#society-form').submit(function(e){
				e.preventDefault();
				if (jQuery("#society-form").valid()){
                    jQuery('#society_loader').show();
				    jQuery(this).find('button[type=submit]').attr('disabled',true);
				    jQuery(this).find('button[type=submit]').text('Registering society please wait..');
				    var data = jQuery( this ).serializeArray();
				    jQuery.ajax({
				         url: API_URL+'society/create',
				         method: "POST",
				         data: data
				     })
				     .success(function(r) {
                         jQuery('#society_loader').hide();
				         jQuery('#society-form').find('button[type=submit]').attr('disabled',false);
				         jQuery('#society-form').find('button[type=submit]').text('Submit');
				          console.log(r);
                        var result = r.response;

                         if(r.email)
                         {
                            jQuery( "#registration-email" )
                                        .after( '<label id="registration-error" class="error" for="email">'+r.msg+'</label>' );
                         }
				         if(result.success){
				             closeSocietyForm();
				             jQuery('#societySuccessModal').modal();
                         }else{
				             console.log('returned false');
				             return false;
				         }


				     }).error(function(response){
				         console.log('Society form error');
				     });

				}else{
                    jQuery('#validation_msg').text("Please fill all the required sections.").show();
                }
			});

            jQuery("#society-join-form").validate({
//                onkeyup: false,
                ignore: [],
                rules: {
                    building_id:'required',
                    first_name:
                            {
                                required:true,
                                user_name:true
                            },
                    last_name:
                            {
                                required:true,
                                user_name:true
                            },
                    relation: "required",
                    flat_no : {
                        required:true,
                        number: true,
                    },
                    type: "required",
//                    flat_no:{
//                        required: true,
//                        number: true,
//                        remote: {
//							url:API_URL+"society/flat/check_occupancy",
//							type:"post",
//							data:{
//								building_id: function(){
//									return jQuery('#building-select').val();
//								},
//								block_id: function() {
//									return jQuery('#block-select').val();
//								},
//								flat_no: function(){
//									return jQuery("#join_flat_no").val();
//								},
//								relation: function(){
//									return jQuery('input[name=relation]').val();
//								}
//							}
//                        }
//                    },
                    contact_no:{
                        required:true,
                        maxlength:10,
                        minlength:10,
                        number:true,
                    },
                    pincode:{
                      required: true,
                      number: true,
                      minlength: 6
                    },
                  email: {
                    required: true,
                    domain: true
                  }
                },
                messages: {
                    contact_no: {
                        required: "This field is required",
                        number: "Please enter valid mobile number",
                        maxlength:"Please enter valid mobile number",
                        minlength:"Please enter valid mobile number"
                    },
                    email: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter a valid email address."
                    },
                    first_name: {
//                        required: "We need your email address to contact you",
                        user_name: "Special characters and numbers are not allowed."
                    },
                    last_name: {
//                        required: "We need your email address to contact you",
                        user_name: "Special characters and numbers are not allowed."
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "relation"  ) {
                        jQuery( "#society-join-form .form-group.occupancy-radio-group" ).append( error );
                    }else if (element.attr("name") == "type"  ) {
                        jQuery( "#society-join-form .form-group.type-radio-group" ).append( error );
                    }else {
                      error.insertAfter(element);
                    }
                },
//                messages: {
//					flat_no:{
//						remote: function() {
//                            console.log(jQuery('input[name=relation]').val());
//							return 'Flat has already occupied with occupancy '+jQuery('input[name=relation]').val();
//						}
//					}
//                }

            });

            jQuery('#society-join-form').submit(function(e){
				e.preventDefault();
				if (jQuery(this).valid()){
				    jQuery(this).find('button[type=submit]').attr('disabled',true);
				    jQuery(this).find('button[type=submit]').text('Sending join request please wait..');
				    var data = jQuery( this ).serializeArray();
				    jQuery.ajax({
				         url: API_URL+'society/join',
				         method: "POST",
				         data: data
				     })
				     .success(function(r) {
				         jQuery('#society-join-form').find('button[type=submit]').attr('disabled',false);
				         jQuery('#society-join-form').find('button[type=submit]').text('Submit');
				         var result = r.response;
				         if(result.success){
				             closeJoinSocietyForm();
				             jQuery('#societyJoinSuccessModal').modal();
                             setTimeout(function(){ location.reload(); }, 2000);
				         }else{
                             if(result.flat_error){
                                jQuery( "#join_flat_no" ).after( '<label id="join_flat_no-error" class="error" >'+result.flat_error+'</label>' );
                                jQuery('#join_validation_msg').text("Please fill all the required sections.").show();
                              }else if(result.input_errors){
                                  var errors = result.input_errors;

                                  for (var key in errors) {
                                       var error = errors[key];
                                       for (var index in error) {
                                          jQuery('<label id="'+key+'-error" class="error" for="'+key+'">'+error[index]+'</label>' ).insertAfter('#society-join-form input[name="'+key+'"]');
                                       }
                                    }

                              }else{
                                  console.log('not input errors check msg');
                              }


				             return false;
				         }


				     }).error(function(response){
				         console.log('Society form error');
				     });

				}else{
                    jQuery('#join_validation_msg').text("Please fill all the required sections.").show();
                }
			});

			jQuery.ajax({
			     url:API_URL+'society/list',
			     method:'GET',
			     success:function(r){
			             jQuery.each(r,function(i,r){
			                     jQuery('#society_list').append('<div class="media"><div class="media-body"><a href="#"><h4 class="media-heading">'+r.name+'</h4></a><div>'+r.address+' - '+r.pincode+'</div></div></div>');
			             });

			     }
			 });

          function getSocietyTypes(){
            jQuery.ajax({
                 url: API_URL+'v1/types',
                 method: "GET",
                 dataType: "json",
                })
                .success(function(r) {
                    var result = r.results.data;
                    var combo = jQuery("#SocietyType");
                    jQuery.each(result, function (i, el) {
                        combo.append("<option value ='"+el.id +"'>" + el.name + "</option>");
                    });
                }).error(function(response){
                    console.log('Society form error');
                });
        }
       getSocietyTypes();

        function getStates(){
            jQuery.ajax({
                 url: API_URL+'v1/states?per_page=unlimited&orderby=ASC',
                 method: "GET",
                 dataType: "json",
                })
                .success(function(r) {
                    var result = r.results;
                    var combo = jQuery("#state");
                    jQuery.each(result, function (i, el) {
                        if(el.id == 1){
                            combo.append("<option value ='"+el.id+"' selected>" + el.name + "</option>");
                        }else{
                            combo.append("<option value ='"+el.id+"'>" + el.name + "</option>");
                        }

                    });
                }).error(function(response){
                    console.log('Society form error');
                });
        }
       getStates();

       function getCities(state_id){

           if(state_id == '1'){
               var default_html = "<option value ='' disabled='' selected>Select City</option>\n\
                                        <option value ='1'>Mumbai</option>\n\
                                        <option value ='9'>Pune</option>\n\
                                        <option value ='17'>Thane</option>\n\\n\
                                        <option value ='27'>Nashik</option>\n\\n\
                                        <option value ='39'>Navi Mumbai</option>\n\
                                        <option value='' disabled>――――</option>";
           }else{
             var default_html = "<option value ='' disabled='' selected>Select City</option>";
           }

           jQuery.ajax({
                    url: API_URL+'v1/cities?state_id='+state_id+'&per_page=unlimited&orderby=ASC',
                    method: "GET",
                    dataType: "json",
                })
                .success(function(r) {
                    var result = r.results;
                    var combo = jQuery("#city");

                    combo.html(default_html);
                    jQuery.each(result, function (i, el) {
                        if( ! (jQuery.inArray( el.id, [ 1, 9, 17, 27, 39 ] ) > -1)){
                            combo.append("<option value ='"+el.id+"'>" + el.name + "</option>");
                        }
                    });

                }).error(function(response){
				         console.log('Society form error');
                });
       }


       getCities(1); // Fetched Maharashtra cities.

        jQuery("#state").on('change',function(){
            var state_id = jQuery("#state").val();
            if(state_id != null)
            {
                getCities(state_id);
            }
        });

        jQuery.validator.addMethod("domain", function(value, element) {
            if(value!='')
            {
                if(value != value.match(/\S+@\S+\.\S+/))
                {
                    return false;
              }else{
                     return true;
               }
           }else{
               return true;
           }

          });

          jQuery.validator.addMethod("user_name", function(value, element) {
            if(value!='')
            {
                if(value != value.match(/^[a-zA-Z ]+$/))
                {
                    return false;
              }else{
                     return true;
               }
           }else{
               return true;
           }

          });

//        jQuery("#registration-email").change(function(){
//            var emailId = jQuery("#registration-email").val();
////            console.log(email_id);
//            jQuery.ajax({
//                 url: API_URL+'checkemail',
//                 method: "POST",
//                 dataType: "json",
//                 data : {email_id:emailId},
//                })
//                .success(function(result) {
//                    if(result.success)
//                    {
//                        console.log("true");
//                    }else{
//                        jQuery( "#registration-email" ).after( '<label id="registration-error" class="error" for="email">'+result.msg+'</label>' );
//                        return false;
//                        }
//                }).error(function(response){
//                    console.log('Society form error');
//                });
//        });

    });
	</script>

	<div id="main" class="wrapper">
