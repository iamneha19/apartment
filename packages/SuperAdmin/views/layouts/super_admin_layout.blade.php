
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
            var ACCESS_TOKEN = {!! "'".Session::get('superadmin.access_token')."'" !!};
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
                .active{
                    font-weight: bold;
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
                <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#" style="padding:3px;"><img src="{{ asset('img/logo.png') }}"></a>
                        <a class="navbar-brand" href="#">Super Admin</a>

                    </div>
                    <!-- Top Menu Items -->
                    <ul class="nav navbar-right top-nav">
                        <li class="dropdown">

                        </li>
<!--                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i> <b class="caret"></b></a>
                        </li>-->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php  $user = Session::get('superadmin.user',null); ?> {{$user['first_name']}} <b class="caret"></b></a>
                            <ul class="dropdown-menu">

                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('super_admin.logout') }}"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
			 <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->




					<!-- Brand and toggle get grouped for better mobile display -->

						<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
						<div class="collapse navbar-collapse navbar-ex1-collapse">
                                                    {{--*/  $routeName = Route::getCurrentRoute()->getName() /*--}}
							<ul class="nav navbar-nav side-nav">
								<li class='{{ (in_array($routeName, array('super_admin.dashboard'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.dashboard') }}"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
								</li>
								<li class='{{ (in_array($routeName, array('super_admin.societies'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.societies') }}"><i class="fa fa-user"></i> Societies</a>
								</li>
                                <li class='{{ (in_array($routeName, array('super_admin.state'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.state') }}"><i class="fa fa-map-marker"></i> State</a>
								</li>
                                                                 <li class='{{ (in_array($routeName, array('super_admin.division'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.division') }}"><i class="fa fa-sitemap"></i> Division</a>
								</li>
                                                                <li class='{{ (in_array($routeName, array('super_admin.region'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.region') }}"><i class="fa fa-university"></i> Region</a>
								</li>
                                                                <li class='{{ (in_array($routeName, array('super_admin.district'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.district') }}"><i class="fa fa-road"></i> District</a>
								</li>
                                <li class='{{ (in_array($routeName, array('super_admin.city'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.city') }}"><i class="fa fa-building-o"></i> City</a>
								</li>
                                                                <li class='{{ (in_array($routeName, array('super_admin.type'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.type') }}"><i class="fa fa-edit"></i>Type Setup</a>
								</li>
                                <li class='{{ (in_array($routeName, array('super_admin.societytype'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.societytype') }}"><i class="fa fa-edit"></i>Category Setup</a>
								</li>
<!--                                                                <li class='{{ (in_array($routeName, array('super_admin.files'))) ? 'active' : '' }}'>
									<a href="{{ route('super_admin.files') }}"><i class="fa fa-edit"></i> Files</a>
								</li>-->
<!--								<li class='{{ (in_array($routeName, array('super_admin.society_type'))) ? 'active' : '' }}'>
                                                                    <a href="javascript:;" data-toggle="collapse" data-target="#demo">
                                                                        <i class="fa fa-file-text-o"></i> Setup <i class="fa fa-fw fa-caret-down"></i></a>
                                                                    <ul id="demo" class="collapse ">
                                                                        <li>
                                                                            <a href="{{ route('super_admin.societytype') }}"><i class="fa fa-file-text-o"></i> society Types</a>
                                                                        </li>
                                                                    </ul>
                                                               </li>-->
                                                              
							</ul>
						</div>
						<!-- /.navbar-collapse -->
					</nav>
				<div id="page-wrapper" style="min-height: 550px;">
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

				<div id="main" class="row">

						@yield('content')

				</div>



				</div>
			</div>
			<footer class="row">
					@include('admin::includes.footer')
				</footer>
		</div>

    </body>

</html>
