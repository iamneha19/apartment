
<!DOCTYPE html>
<html lang="en" ng-app="sahkari">
    <head>
		<title>{{env('PROJECT_NAME')}} - @yield('title')</title>
		<!-- Bootstrap Core CSS -->
		<link href="{{ asset('packages/admin/admin_theme/css/bootstrap.min.css') }}" rel="stylesheet">
		<!-- Custom CSS -->
		<link href="{{ asset('packages/admin/admin_theme/css/sb-admin.css') }}" rel="stylesheet">
		<!-- Morris Charts CSS -->
		<link href="{{ asset('packages/admin/admin_theme/css/plugins/morris.css') }}" rel="stylesheet">
		<!-- Custom Fonts -->
		<link href="{{ asset('packages/admin/admin_theme/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
                <link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css">

		<!-- Latest compiled and minified JavaScript -->
		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<!-- Bootstrap Core JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.2/angular.min.js"></script>
		 <!-- Morris Charts JavaScript -->
		<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js"></script>
		<script>
			var API_URL = {!! "'".Config::get('app.api.request_url')."'" !!};
			var ACCESS_TOKEN = {!! "'".Session::get('access_token')."'" !!};
			var CLIENT_ID = {!! "'".Config::get('app.api.client_id')."'" !!};
		</script>
        <!-- Anguler tagit -->
        <link rel="stylesheet" href="http://mbenford.github.io/ngTagsInput/css/ng-tags-input.min.css" />
        <script src="http://mbenford.github.io/ngTagsInput/js/ng-tags-input.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.2/angular-sanitize.js"></script>
		<script src="{{ asset('js/app.js') }}"></script>
		<style>
		#gritter{
		display:none;
			position: fixed;
		    background-color: #AAE2BD;
		    z-index: 100000;
		    width: 100%;
		    text-align: center;
		    padding: 7px;
		    color: black;
		    font-weight: bold;
		}

                .tab-pane{
                    margin-top:21px;
                }
                .active{
                    font-weight: bold;
                }
				.disabledClass {
					opacity: 0.60;
					cursor: not-allowed;
				   }
		</style>
		@yield('head')


    </head>
	<body>
	<div id="gritter">
		<div>
		<span class="msg">Group created successfully!</span>
		<a href="javascript:void(0)" style="float: right;" id="close_grit"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
	</div>

		<div id="wrapper">
			 <!-- Navigation -->
        <nav class="navbar navbar-inverse" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('helpdesk') }}" style="padding:3px; position:relative; z-index:1; background:#FFFFFF;"><img src="{{ asset('img/logo.png') }}"></a>
            </div>
            <ul class="nav navbar-nav">
	            <?php 
				if(Session::get('acl.admin') && preg_match('/\b' . 'chairperson' . '\b/', strtolower(Session::get('role_name')))) { ?>
                    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
               <?php } ?>
                    
               <?php if(Session::get('user.flat_no') != "") { ?>
               <?php if(Session::get('acl.resident') && preg_match('/\b' . 'member' . '\b/', strtolower(Session::get('role_name')))) { ?>
                    <li class="active"><a href="{{ route('helpdesk') }}">Resident</a></li>
               <?php } ?>
               <?php } ?>
            </ul>

            <!-- Top Menu Items -->
			<div class="container-fluid">
            <ul class="nav navbar-right top-nav">
			<?php if(strtolower(Session::get('role_name'))!='admin'){ ?>
                <li class="dropdown">
                   @include('admin::includes.society_dropdown')
                </li>
			 <?php } ?>	

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php  $user = Session::get('user',null); ?> {{$user['first_name']}} <b class="caret"></b></a>
                    <ul class="dropdown-menu" style=" width: 180px;">
                        <li>
                            <a href="{{ route('change_password') }}"><i class="fa fa-fw fa-key"></i> Change Password </a>
                        </li>
                        <li>
                            <a href="{{ route('personal_info') }}"><i class="fa fa-fw fa-user"></i> Edit Personal Info </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
			<div class="menu-decorator">
				<nav class="menu">
					<ul class="nav navbar-nav home-menu-ul">
						<li class=""><a href="{{ route('helpdesk') }}">Home</a></li>
						<li><a href="{!! url('features?visitor=â�Œ') !!}">Features</a></li>
					</ul>
				</nav>
			</div>
			</div>
			 <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->




					<!-- Brand and toggle get grouped for better mobile display -->

						<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
						<div class="collapse navbar-collapse navbar-ex1-collapse">
                            {{--*/  $routeName = Route::getCurrentRoute()->getName() /*--}}
                            <?php
								$disabledMenuItem = Session::get('socities.0.is_approved'); 
                                $modules = array();
                                if(Session::get('acl.resident')){
                                  $modules = Session::get('acl.resident');
                                }
                                
                            ?>
							<ul class="nav navbar-nav side-nav">
							   <?php if(array_key_exists('res_helpdesk', $modules)){ ?>
                                    <li class='{{ (in_array($routeName, array('helpdesk'))) ? 'active' : '' }}'>
                                        <a href="{{ route('helpdesk') }}"><i class="fa fa-fw fa-dashboard"></i>Dashboard</a>
                                    </li>
                                <?php } ?>
                                <?php if(array_key_exists('res_conversation', $modules) ){ ?>
                                    <li class='{{ (in_array($routeName, array('conversations'))) ? 'active' : '' }}'>
                                        <a href="{{ route('conversations') }}"><i class="fa fa-group"></i> Conversations & Groups</a>
                                    </li>
                                <?php } ?>
								<?php if(array_key_exists('res_myflat', $modules)){ ?>
                                    <li class='{{ (in_array($routeName, array('members'))) ? 'active' : '' }}'>
                                        <a href="{{ route('members') }}" ><i class="fa fa-home"></i> MyFlat</a>
                                    </li>
                                <?php } ?>
								<?php if(array_key_exists('res_documents', $modules) ){ ?>
                                    <li class='{{ (in_array($routeName, array('folders','document.resident','document.official'))) ? 'active' : '' }}'>
                                        <a href="{{ route('document.resident') }}"><i class="glyphicon glyphicon-folder-open"></i> Documents</a>
                                    </li>
                                <?php } ?>
								<?php if(array_key_exists('res_photo_gallery', $modules) ){ ?>
                                    <li class='{{ (in_array($routeName, array('albums','album.photos'))) ? 'active' : '' }}'>
                                        <a href="{{ route('albums') }}"><i class="fa fa-file-image-o"></i> Photo Gallery</a>
                                    </li>
                                <?php } ?>
                                <?php if(array_key_exists('res_notices', $modules)){ ?>
                                    <li class='{{ (in_array($routeName, array('notice','notice.old','notice.view','notice.edit'))) ? 'active' : '' }}'>
                                        <a href="{{ route('notice') }}"><i class="fa fa-fw fa-table"></i> Notice Board</a>
                                    </li>
                                <?php } ?>
								<?php if(array_key_exists('res_events', $modules)){ ?>
                                    <li class='{{ (in_array($routeName, array('events'))) ? 'active' : '' }}'>
                                        <a href="{{ route('events') }}"><i class="fa fa-calendar"></i> Events</a>
                                    </li>
                                <?php } ?>
                                
                                <?php if(array_key_exists('res_official_communication', $modules)){ ?>    
                                    <li class='{{ (in_array($routeName, array('officialcommunication'))) ? 'active' : '' }}'>
                                        <a href="{{ route('officialcommunication') }}" ><i class="fa fa-fw fa-comment"></i> Official Communication </a>
                                    </li>
                                <?php } ?>
                                
<!--								<li>
									<a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-arrows-v"></i> Dropdown <i class="fa fa-fw fa-caret-down"></i></a>
									<ul id="demo" class="collapse">
										<li>
											<a href="#">Dropdown Item</a>
										</li>
										<li>
											<a href="#">Dropdown Item</a>
										</li>
									</ul>
								</li>-->
							</ul>
						</div>
						<!-- /.navbar-collapse -->
					</nav>
				<div id="page-wrapper">
				<div class="container-fluid">
					<!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            @yield('panel_title','No Title') <small>@yield('panel_subtitle')</small>
                        </h1>
<!--                         <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </li>
                        </ol>
 -->                    </div>
                </div>
                <!-- /.row -->

				<div id="main" class="row" style="min-height: 450px;">

						@yield('content')

				</div>
				</div>
			</div>
		</div>
        <div class="fatfooter">
        	<footer class="container-fluid" >
			    <div class="row">
					@include('admin::includes.footer')
                    <div class="" ng-controller="ReminderListCtrl">
                        </div>
				</div>
				</footer>
        </div>

    </body>

</html>
<script>
	$(document).ready(function() {
		var isApproved = <?php echo json_encode($disabledMenuItem) ?>;
		
		if (isApproved == 'NO'){
			$('ul.side-nav > li > a').addClass('disabledClass');
			$('ul.side-nav > li > a').attr('href','#');
			$('ul.side-nav > li ').removeClass('active');
		} else {
			$('ul.side-nav > li > a').removeClass('disabledClass');
		}
		
	});
    
	app.controller("ReminderListCtrl", function(URL,$scope,$http) {});
</script>
