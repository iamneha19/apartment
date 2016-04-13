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
        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.2/angular-sanitize.js"></script>

        <!-- Anguler tagit -->
        <link rel="stylesheet" href="http://mbenford.github.io/ngTagsInput/css/ng-tags-input.min.css" />
        <script src="http://mbenford.github.io/ngTagsInput/js/ng-tags-input.min.js"></script>

        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.min.custom.js') }}"></script>
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
		.active{
                    font-weight: bold;
                }

			.home-menu-ul .active a{
                    padding-right:4px !important;
                }
                .deActive{
                    font-weight: normal;
                }
                .activeSubItem  {
                    color: #fff;
                }
		.menu-decorator{ margin-left: 300px; }
		.home-menu-ul>li{
			padding-left: 10px;
			padding-right: 10px;
		}
		.disabledClass {
					opacity: 0.60;
					cursor: not-allowed;
				   }
		
		</style>
            @yield('head')
    </head>
	<body>
		{{--*/  $routeName = Route::getCurrentRoute()->getName() /*--}}
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
                        <a class="navbar-brand" href="{{ route('admin.dashboard') }}" style="padding:3px;"><img src="{{ asset('img/logo.png') }}"></a>
                    </div>
                    <ul class="nav navbar-nav">
                        <?php 
						
						if(Session::get('acl.admin') && strtolower(Session::get('role_name'))!='admin'){ ?>
                            <li class="active"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <?php } ?>
                        
                         <?php if(Session::get('user.flat_no') != "") { ?>    
                        <?php if(session()->get('acl.resident') && strtolower(Session::get('role_name'))!='admin'){ 
                            ?>
                            <li><a href="{{ route('helpdesk') }}">Resident</a></li>
                        <?php } ?>
                             <?php } ?>
                    </ul>
					<?php
                                $modules = array();
                                if(Session::get('acl.admin')){
                                  $modules = Session::get('acl.admin');
                                }

                            ?>
					<div class="container-fluid" >
                    <!-- Top Menu Items -->
                    <ul class="nav navbar-right top-nav">
					 <?php if(strtolower(Session::get('role_name'))!='admin'){ ?>
                        <li class="dropdown">
                            @include('admin::includes.society_dropdown')
                        </li>
					   <?php } ?>
<!--                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i> <b class="caret"></b></a>
                        </li>-->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php  $user = Session::get('user',null); ?> {{$user['first_name']}} <b class="caret"></b></a>
                            <ul class="dropdown-menu" style=" width: 180px;">
                        <li>
                            <a href="{{ route('admin_changePassword') }}"><i class="fa fa-fw fa-key"></i> Change Password </a>
                        </li>
                        <li>
                            <a href="{{ route('admin_personal_info') }}"><i class="fa fa-fw fa-user"></i> Edit Personal Info </a>
                        </li>
<!--                        <li>
                            <a href="#"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                        </li>-->
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('logout') }}"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>

                <div class="menu-decorator">
                    @include('admin::includes.header')
                </div>
					</div>
			 <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->


					<?php  
					$disabledMenuItem = Session::get('socities.0.is_approved');  ?>
			 
					<!-- Brand and toggle get grouped for better mobile display -->

						<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
						<div class="collapse navbar-collapse navbar-ex1-collapse" >
							<ul class="nav navbar-nav side-nav">
                                <?php if(strtolower(Session::get('role_name')=='admin')) { ?>
                                    <?php if ($disabledMenuItem === 'NO') { ?>
                                        <li class='{{ (in_array($routeName, array('import.society.config'))) ? 'active' : '' }} ' >
                                            <a id="configure" href="{{ route('import.society.config') }}" ><i class="fa fa-fw fa-dashboard"></i> Configure</a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if(strtolower(Session::get('role_name')=='chairman') || strtolower(Session::get('role_name')=='chairperson') || strtolower(Session::get('role_name')=='chairperson,member') || strtolower(Session::get('role_name')=='member,chairperson') || strtolower(Session::get('role_name')=='chairman,member') || strtolower(Session::get('role_name')=='member,chairman')) { ?>
                                    <li class='{{ (in_array($routeName, array('admin.chairmanConfig'))) ? 'active' : '' }} ' >
                                        <a id="configure1" href="{{ route('admin.chairmanConfig') }}" ><i class="fa fa-fw fa-dashboard"></i> Configure</a>
                                    </li>
                                <?php } ?>
								<li class='{{ (in_array($routeName, array('admin.dashboard'))) ? 'active' : '' }} ' disabled="disabled">
									<a href="{{ route('admin.dashboard') }}" class="disabledClass" ><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
								</li>




                            <?php if(array_key_exists('admin_forum', $modules)){ ?>
								<li class='{{ (in_array($routeName, array('admin.forums'))) ? 'active' : '' }}'>
									<a href="{{ route('admin.forums') }}" class="disabledClass"><i class="fa fa-comments-o"></i> Forums</a>
								</li>
                            <?php } ?>
                            <?php if(array_key_exists('admin_meeting', $modules)){ ?>
								<li class='{{ (in_array($routeName, array('admin.meeting','admin.oldmeeting'))) ? 'active' : '' }}'>
									<a href="{{ route('admin.meeting') }}" class="disabledClass"><i class="fa fa-clock-o"></i> Meeting</a>
								</li>
                            <?php } ?>
                            <?php if(array_key_exists('admin_helpdesk', $modules)){ ?>
<!--								<li class='{{ (in_array($routeName, array('admin.helpdesk'))) ? 'active' : '' }}'>
									<a href="{{ route('admin.helpdesk') }}"><i class="fa fa-support"></i> Helpdesk Tracker</a>
								</li>-->
                            <?php } ?>

                            @if(array_key_exists('admin_tasks', $modules))
                            <li class='{{ (in_array($routeName, array('admin.task','admin.mytasks','admin.task_category'))) ? 'active' : '' }}'>
                                    <a href="javascript:;" data-toggle="collapse" data-target="#demo" class="disabledClass"><i class="fa fa-file-text-o"></i> Tasks <i class="fa fa-fw fa-caret-down"></i></a>
                                    <ul id="demo" class="collapse {{ (in_array($routeName, array('admin.task','admin.mytasks','admin.task_category'))) ? 'in' : '' }}">
                                        <li  style="font-size:13px;margin-left:12px;">
                                            <a href="{{ route('admin.task') }}" class="disabledClass"><i class="fa fa-file-text-o"></i> <div style="display: inline;" class='{{ (in_array($routeName, array('admin.task'))) ? 'activeSubItem' : 'deActive' }}'>Tasks</div></a>
                                        </li>
                                        <li  style="font-size:13px;margin-left:12px;">
                                            <a  href="{{ route('admin.mytasks') }}" class="disabledClass"><i class="fa fa-file-text-o"></i> <div style="display: inline" class='{{ (in_array($routeName, array('admin.mytasks'))) ? 'activeSubItem' : 'deActive' }}'>My Tasks</div></a>
                                        </li>
<!--                                        <li>
                                           s <a href="{{ route('admin.task_category') }}"><i class="fa fa-file-text-o"></i> <div style="display: inline" class='{{ (in_array($routeName, array('admin.task_category'))) ? 'activeSubItem' : 'deActive' }}'>Task Category</div></a>
                                        </li>-->
                                    </ul>
								</li>
                            @endif
                            @if(array_key_exists('admin_files', $modules))
                                 <li class='{{ (in_array($routeName, array('admin.society_files','admin.flat_documents'))) ? 'active' : '' }}'>
                                   <a href="javascript:;" data-toggle="collapse" data-target="#file" class="disabledClass">
                                       <i class="glyphicon glyphicon-folder-close"></i> Files  <i class="fa fa-fw fa-caret-down"></i>
                                   </a>

                                   <ul id="file" class="collapse {{ (in_array($routeName, array('admin.society_files','admin.flat_documents','admin.flat_reports'))) ? 'in' : '' }}">
                                       <li style="font-size:13px;margin-left:12px;"><a href="{{ route('admin.society_files') }}" class="disabledClass"> <i class="glyphicon glyphicon-folder-open"></i> <div style="display: inline;" class='{{ (in_array($routeName, array('admin.society_files'))) ? 'activeSubItem' : 'deActive' }}'>Society Files</div></a></li>
                                       <li style="font-size:13px;margin-left:12px;"><a href="{{ route('admin.flat_documents') }}" class="disabledClass"> <i class="glyphicon glyphicon-folder-open"></i> <div style="display: inline;" class='{{ (in_array($routeName, array('admin.flat_documents'))) ? 'activeSubItem' : 'deActive' }}'>Flat Documents</div></a></li>
                                   </ul>
                                </li>
                            @endif
                            @if(array_key_exists('admin_parking', $modules))
				<li class='{{ (in_array($routeName, array('admin.parking','admin.parking_setup'))) ? 'active' : '' }}'>
                                    <a href="javascript:;" data-toggle="collapse" data-target="#parking-nav" class="disabledClass"><i class="glyphicon-road glyphicon"></i> Parking <i class="fa fa-fw fa-caret-down"></i></a>
                                    <ul id="parking-nav" class="collapse {{ (in_array($routeName, array('admin.parking','admin.parking_setup'))) ? 'in' : '' }}">
                                        <li  style="font-size:13px;margin-left:12px;">
                                            <a href="{{ route('admin.parking') }}" class="disabledClass"><i class="glyphicon-road glyphicon"></i><div style="display: inline" class='{{ (in_array($routeName, array('admin.parking'))) ? 'activeSubItem' : 'deActive' }}'> Slots</div></a>
                                        </li>
                                        <li  style="font-size:13px;margin-left:12px;">
                                            <a href="{{ route('admin.parking_setup') }}" class="disabledClass"><i class="glyphicon-road glyphicon"></i><div style="display: inline" class='{{ (in_array($routeName, array('admin.parking_setup'))) ? 'activeSubItem' : 'deActive' }}'> Config</div></a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if(array_key_exists('admin_notices', $modules))
                                <li class='{{ (in_array($routeName, array('admin.noticeboard'))) ? 'active' : '' }}'>
                                    <a href="{{ route('admin.noticeboard') }}" class="disabledClass"><i class="fa fa-fw fa-table"></i> Notice</a>
                                </li>
                                @endif
								{{-- @if(array_key_exists('admin_reminders', $modules))
		                        <li class='{{ (in_array($routeName, array('admin.reminders'))) ? 'active' : '' }}'>
									<a href="{{ route('admin.reminders') }}" class="disabledClass"><i class="glyphicon glyphicon-warning-sign"></i> Reminders</a>
                                </li>
								 @endif --}}
                                <?php if(array_key_exists('admin_official_communication', $modules)){ ?>
                                 <li class='{{ (in_array($routeName, array('admin.officialcommunication'))) ? 'active' : '' }}'>
                                     <a href="{{ route('admin.officialcommunication') }}" class="disabledClass"><i class="fa fa-fw fa-comment"></i> Official Communication </a>
                                </li>
                                <?php } ?>
                                 @if(array_key_exists('admin_setup', $modules))
							    <li class='{{ (in_array($routeName, array('admin.type'))) ? 'active' : '' }}'>
                                 <a href="{{ route('admin.type') }}" class="disabledClass"><i class="fa fa-edit"></i> Category Setup</a>
                                </li>
                                @endif
                            <!-- Billing module -->
                            @if(array_key_exists('admin_billing', $modules))
                               <li class="{!! str_contains($routeName, 'billing') ? 'active' : '' !!}">
                                   <a href="javascript:void(0);"
                                    data-toggle="collapse"
                                    data-target="#billing"
									class="disabledClass"
                                    >
                                       <i class="fa fa-money"></i> Billing  <i class="fa fa-fw fa-caret-down"></i>
                                   </a>
                                   <ul id="billing"
                                    class="collapse {!! str_contains($routeName, 'billing') ? 'in' : '' !!}">
                                       <li style="font-size:13px;margin-left:12px;" class="{!! $routeName == 'billing.dashboard' ? 'active': '' !!}">
                                           <a href="{{ route('billing.dashboard') }}" class="disabledClass"><div style="display: inline" class='{{ (in_array($routeName, array('billing.dashboard'))) ? 'activeSubItem' : 'deActive' }}'>Dashboard</div></a>
                                       </li>
                                       <li style="font-size:13px;margin-left:12px;" class="{!! $routeName == 'billing.index' ? 'active': '' !!}">
                                           <a href="{{ route('billing.index') }}" class="disabledClass"><div style="display: inline" class='{{ (in_array($routeName, array('billing.index'))) ? 'activeSubItem' : 'deActive' }}'>Bills</div></a>
                                       </li>
                                       <li style="font-size:13px;margin-left:12px;" class="{!! $routeName == 'billing.item.index' ? 'active': '' !!}">
                                           <a href="{{ route('billing.item.index') }}" class="disabledClass"><div style="display: inline" class='{{ (in_array($routeName, array('billing.item.index'))) ? 'activeSubItem' : 'deActive' }}'>Bill Items</div></a>
                                       </li>
                                       <li style="font-size:13px;margin-left:12px;" class="{!! str_contains($routeName, 'billing.config') ? 'active' : '' !!}">
                                           <a href="{!! route('billing.config') !!}" class="disabledClass"><div style="display: inline" class='{{ (in_array($routeName, array('billing.config'))) ? 'activeSubItem' : 'deActive' }}'>Billing Config</div></a>
                                       </li>
                                   </ul>
                                </li>
                                @endif
								@if(array_key_exists('admin_amenities', $modules))
                                    <li class='{{ (in_array($routeName, array('admin.amenities'))) ? 'active' : '' }}'>
                                        <a href="{{ route('admin.amenities') }}" class="disabledClass"><i class="glyphicon glyphicon-glass"></i> Amenities </a>
                                    </li>
                                @endif
								
								<li>
									<!--<a href="{{ route('admin.notification') }}" ><i class="glyphicon glyphicon-glass"></i> Notification From Chairman </a>-->
                                </li>

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
							 @yield('panel_title','Dashboard') <small>@yield('panel_subtitle','')</small>
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

        @yield('footerCSSAndJS')
    </body>

</html>
<script>
     app.controller("ReminderListCtrl", function(URL,$scope,$http) {
        
    });
	$(document).ready(function() {
		var isApproved = <?php echo json_encode($disabledMenuItem) ?>;
		if (isApproved == 'NO'){
			$('ul.side-nav > li > a').attr('href','#');
//			$('#configure').attr('href', '{{ route('import.society.config') }}');
			
			$('.collapse > li > a').attr('href','#');
//			$('ul.side-nav > li ').removeClass('active');
//            $('#configure').addClass('active');
            
		}
		else {
			$('ul.side-nav > li > a').removeClass('disabledClass');
			$('.collapse > li > a').removeClass('disabledClass');
		}
		$('#configure1').attr('href','{{ route('admin.chairmanConfig') }}');
                $('#configure').attr('href','{{ route('import.society.config') }}');
	});
</script>
