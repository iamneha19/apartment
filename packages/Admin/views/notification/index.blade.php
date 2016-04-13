@extends('admin::layouts.admin_layout')
@section('title', 'Flats')
@section('panel_title','Configuration')
@section('panel_subtitle','')

@section('head')
<link href="{!! asset('css/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! asset('bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('js/moment.js') !!}"></script>
<script src="{!! asset('bower_components/select2/dist/js/select2.full.min.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect-collapsible-groups.js') !!}"></script>
<script src="{!! asset('bower_components/bootbox.js/bootbox.js') !!}"></script>
<script src="{!! asset('js/bootstrap-datetimepicker.min.js') !!}"></script>
<script src="{!! asset('js/notification.js') !!}"></script>

@stop

@section('content')

<div class="col-md-12" ng-controller="NotificationController">
    
	
	<div class="col-lg-9" >
		<form role="form">
			<div class="form-group" >
				<h4>Feedback From Chairman</h4>
			</div>
			<div class="form-group">
			  <!--<textarea ng-model="notes" name="notes" cols="70" rows="10" ></textarea>-->
				<div ng-bind-html="notes"></div>
			</div>

		</form>
		
	</div>
	
    
</div>


@stop
