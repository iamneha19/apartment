@extends('admin::layouts.admin_layout')
@section('title', 'Add Bill')
@section('panel_title', 'Add Bill')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<link href="{!! asset('bower_components/select2/dist/css/select2.min.css') !!}" rel="stylesheet" type="text/css" />

<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{!! asset('bower_components/select2/dist/js/select2.full.min.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect-collapsible-groups.js') !!}"></script>

<script type="text/javascript">
    const society_id = "{!! session()->get('user.society_id') !!}";
    var billingUrl ="{!! route('billing.index'); !!}"
</script>
<script src="{!! asset('js/billing.js') !!}" type="text/javascript"></script>
@stop
@section('content')

<div class="alert alert-warning hide">
</div>

<div  ng-controller="BillingCtrl" class="col-lg-12">

      <form class="form-horizontal" role="create-bill" name="create-bill"  id="create-bill">
      <input  name="society_id"  ng-model="form.society_id" type="hidden" value="" />
        <div class="form-group">
          <label class="control-label col-sm-2 form-label" for="office_charge">Office Charge</label>
          <div class="col-sm-5">
            <input type="text" class="form-control" id="office_charge" name="office_charge" ng-model="form.office_charge" placeholder="">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2 form-label" for="shop_charge">Shop Charge</label>
          <div class="col-sm-5">
            <input type="text" class="form-control" id="shop_charge" name="shop_charge" ng-model="form.shop_charge" placeholder="">
          </div>
        </div>

		<div class="form-group">
          <label class="control-label col-sm-2 form-label" for="residential_charge">Residential Charge</label>
          <div class="col-sm-5">
            <input type="text" class="form-control" id="residential_charge" name="residential_charge" ng-model="form.residential_charge" placeholder="">
          </div>
        </div>

       <div class="form-group">
        <label class="control-label col-sm-2 form-label" for="month">For the month of</label>
        <div class="col-sm-5">
            <input type="text" ng-click="warningBox('hide')" class="form-control" name="month" ng-model="form.month" id="billing-item-start-date" placeholder="">
        </div>
      </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="flats">Flat's</label>
            <div class="col-sm-5">
                <select class="col-sm-12" name="flats[]" multiple="multiple" ng-model="form.blocks" id="billing-item-flats" placeholder="">
                    <option disabled>Select Flats</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="billing-item-blocks">Building's</label>
            <div class="col-sm-5">
                <select class="col-sm-12" name="buildings[]" multiple="multiple" ng-model="form.Buildings" id="billing-item-buildings" placeholder="">
                    <option disabled>Select Building's</option>
                </select>
            </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-5">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="button"  id="cancel" class="btn btn-primary">Cancel</button>
          </div>
        </div>
      </form>
</div>
@stop

@section('footerCSSAndJS')
    <script>
        $("#create-bill").validate({
        	ignore: [],
            rules: {
                office_charge : {
                    required:true,
                    number: true,
                },
                residential_charge : {
                    required:true,
                    number: true,
                },
                shop_charge : {
                    required:true,
                    number: true,
                },
                month: {
                    required:true,
                }
            }
        });

        $('#cancel').click(function() {
             window.location="<?php echo route('billing.index');  ?>"
        });
    </script>
    <script src="{!! asset('js/main.js') !!}"></script>
@stop
