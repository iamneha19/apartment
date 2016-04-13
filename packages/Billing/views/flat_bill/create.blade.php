@extends('admin::layouts.admin_layout')
@section('title','Create Bill Item')
@section('panel_title','Create Bill Item')
@section('head')
<link href="{!! asset('css/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet" type="text/css" />

<link href="{!! asset('bower_components/select2/dist/css/select2.min.css') !!}" rel="stylesheet" type="text/css" />

<link href="{!! asset('bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('js/moment.js') !!}"></script>
<script src="{!! asset('bower_components/select2/dist/js/select2.full.min.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect-collapsible-groups.js') !!}"></script>
<script src="{!! asset('js/bootstrap-datetimepicker.min.js') !!}"></script>
<script src="{!! asset('bower_components/vue/dist/vue.min.js') !!}"></script>
<script src="{!! asset('js/date.js') !!}"></script>

<script type="text/javascript">
    const society_id = "{!! session()->get('user.society_id') !!}";
</script>
<script>

app.controller("billingController", function(URL,paginationServices,$scope,$http,$filter) {

    $('#billing_form').submit(function(e) {
        jQuery('.alert.alert-warning').addClass('hide');
        e.preventDefault();
        jQuery('input[name=society_id]').val(society_id);
        if ($("#billing_form").valid()) {
            var records = $.param($( this ).serializeArray());
            var request_url = generateUrl('v1/billing/item');
            $http({
                url: request_url,
                method: "POST",
                data: records,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(response) {
                if (response.data.status == "success") {
        		  grit('',response.data.message);
        		  $scope.form = {};
                  window.location = window.location.href;
                } else {
                   jQuery('.alert.alert-warning').text(response.data.message).removeClass('hide');
                }
            });
        }
    });

    $scope.warningBox = function (status) {
        if (status == 'hide') {
            jQuery('.alert.alert-warning').addClass('hide');
        } else {
            jQuery('.alert.alert-warning').removeClass('hide');
        }
    }
});

</script>
@stop
@section('content')
<div class="col-md-12" ng-controller="billingController">

    <div class="alert alert-warning hide">
    </div>

    <form name="billing_form"  id="billing_form" class="form-horizontal"  novalidate>
        <input  name="parent_entity"  ng-model="form.parent_entity" type="hidden" value="billing" />
        <input  name="society_id"  ng-model="form.society_id" type="hidden" value="" />
        <div class="form-group">
            <label class="control-label col-sm-2 form-label" for="item_name" >Item Name</label>
            <div class="col-sm-5">
                <input type="text" name="item_name" class="form-control" ng-click="warningBox('hide')" ng-model="form.item_name" id="item-name" v-model="itemName" required ng-minlength="2" placeholder="Enter Name">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="Buildings">Building's</label>
            <div class="col-sm-5">
                <select class="col-sm-12" name="buildings[]" multiple="multiple" ng-model="form.buildings" name="billing-item-buildings" id="billing-item-buildings" placeholder="">
                    <option disabled>Select Buildings</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="flats">Flat's</label>
            <div class="col-sm-5">
                <select class="col-sm-12" name="flats[]" multiple="multiple" ng-model="form.flats" id="billing-item-flats">
                    <option disabled>Select Flats</option>
                </select>
            </div>
        </div>

		 <div class="form-group">
          <label class="control-label col-sm-2" for="flat_category">Flat Type</label>
          <div class="col-sm-5">
              <select class="form-control" name="flat_category"  ng-model="form.flat_category">
                  <option value="">--select Flat Type--</option>
                  <option value="tenant">Tenant</option>
                  <option value="owner">Owner</option>
              </select>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2 form-label" for="fixed_billing_item">Fixed billing item</label>
          <div class="col-sm-5">
              <select class="form-control" ng-model="form.fixed_billing_item" name="fixed_billing_item">
                    <option value="">--Select Fixed billing item--</option>
                  <option value="yes">Yes</option>
                  <option value="no">No</option>
              </select>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2 form-label" for="month">For the month of</label>
          <div class="col-sm-5">
              <input type="text" class="form-control" name="month" ng-model="form.month" id="billing-item-start-date" placeholder="For the month of">
          </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2 form-label" for="charge">Charge</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="charge" ng-model="form.charge" id="charge" placeholder="Charge">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-5">
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </div>

    </form>
</div>
<script>
$("#billing_form").validate({
	ignore: [],
	rules: {
	  charge : {
			required:true,
			number: true,
		 },
		month: {
		   required:true,
		} ,
		fixed_billing_item: {
		   required:true,
		} ,

	}
});
</script>
@stop

@section('footerCSSAndJS')
    <script src="{!! asset('js/main.js') !!}"></script>
@stop
