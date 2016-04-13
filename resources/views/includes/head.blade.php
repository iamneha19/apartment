<meta charset="utf-8">
<meta name="description" content="">
<meta name="author" content="Scotch">

	<title>{{env('PROJECT_NAME')}} - @yield('title')</title>
	<!-- Bootstrap Core CSS -->
	<link href="{{ asset('packages/admin/admin_theme/css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="{{ asset('packages/admin/admin_theme/css/sb-admin.css') }}" rel="stylesheet">
	<!-- Morris Charts CSS -->
	<link href="{{ asset('packages/admin/admin_theme/css/plugins/morris.css') }}" rel="stylesheet">
	<!-- Custom Fonts -->
	<link href="{{ asset('packages/admin/admin_theme/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
	
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
		var ACCESS_TOKEN = 'fg565hgh6';
		var CLIENT_ID = {!! "'".Config::get('app.api.client_id')."'" !!};
	</script>
	<script src="{{ asset('js/resident_app.js') }}"></script>
