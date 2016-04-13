@extends('admin::layouts.admin_layout')

@section('title', 'Billing Configuration')
@section('panel_title', 'Billing Configuration')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@stop
@section('content')
<script type="text/javascript">
    const society_id = "{!! session()->get('user.society_id') !!}";

    app.controller("BillingConfigCtrl", function(URL,paginationServices,$scope,$http,$filter) {

        $scope.current_form = {};

        responseHandler = function(response, callback) {
            jQuery.each(response.data.results, function(key, value) {
                callback(key, value);
            });
        }

        $scope.getBillingConfig = function() {
            $http.get(generateUrl('v1/billing/config/' + society_id))
                 .then(function (response) {
                     if (response.data.status == 'success') {
                        responseHandler(response, function(key, value) {
                            $scope.current_form[key] = parseInt(value);
                        });
                     }
                 });
        }

        // Set billing configuration for first time
        $scope.getBillingConfig();

        $scope.showBillingConfig = function() {
            $http.get(generateUrl('v1/billing/config/' + society_id))
                .then(function (response) {
                    if (response.data.status == 'success') {
                        $scope.form = response.data.results;
                        // removing validation after populating data from form
                        jQuery('#editBillingConfigForm label[class="error"]').remove();

                        return;
                    }
                });

            jQuery('#edit-billing-config').modal('show');
        }

        $scope.hideBillingConfig = function () {
            jQuery('#editBillingConfigForm label[class="error"]').remove();
            jQuery('#edit-billing-config').modal('hide');
        }

        $scope.submitBillingConfig = function() {
            if (! jQuery('#editBillingConfigForm').valid()) {
                return;
            }

            $http.post(generateUrl('v1/billing/config/' + society_id), this.form)
                 .then(function (response) {
                    if (response.data.status == 'success') {
                        $scope.getBillingConfig();
                        $scope.hideBillingConfig();
                        grit('', response.data.message);

                        return;
                    }
                    if (response.data.status == 'validation_failed') {
                        responseHandler(response, function(key, value) {
                            jQuery('[name=' + key + ']').parent().append(
                                '<label id="' + key + '-error" class="error" for="' + key + '">' + value + '</label>'
                            );
                        });
                    }
                });
        }

        // generateUrl('v1/billing/config/'+society_id+'/store')
    });
</script>

<div name="billing-config" class="col-md-12" ng-controller="BillingConfigCtrl" >
    <div class="col-lg-7">
		<table class="table table-bordered table-hover table-striped">
            <tr>
                <th class="col-lg-6">Office Charge (Per Square feet)</th>
                <td ng-bind="current_form.office_charge"></td>
            </tr>
            <tr>
                <th class="col-lg-6">Shop Charge (per square feet)</th>
                <td ng-bind="current_form.shop_charge"></td>
            </tr>
            <tr>
                <th class="col-lg-6">Residential Charge (per square feet)</th>
                <td ng-bind="current_form.residential_charge"></td>
            </tr>
            <tr>
                <th class="col-lg-6">Interest Rate (in percent)</th>
                <td ng-bind="current_form.interest_rate"></td>
            </tr>
            <tr>
                <th class="col-lg-6">Service Tax (in percent)</th>
                <td ng-bind="current_form.service_tax"></td>
            </tr>
            <tr>
                <th class="col-lg-6">Action</th>
                <td>
                    <button type="button" class="btn btn-primary btn-sm" ng-click="showBillingConfig()">Edit</button>
                </td>
            </tr>
		</table>
    </div>

    <div id="edit-billing-config" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close"  ng-click="hideBillingConfig()" aria-label="Close">
                  <span aria-hidden="true" ng-click="hideBillingConfig()">&times;</span>
              </button>
              <h4 class="modal-title">Edit Billing Config</h4>
            </div>
            <div class="modal-body">
                <form id="editBillingConfigForm" ng-submit="submitBillingConfig()" novalidate>
                    <input name="society_id"  ng-model="society_id" type="hidden"/>

                    <div class="form-group">
                        <label class="control-label form-label" for="office_charge">Office Charge (per square feet)</label>
                        <input type="text" class="form-control" id="office_charge" name="office_charge" ng-model="form.office_charge" placeholder="" />
                    </div>

                    <div class="form-group">
                        <label class="control-label form-label" for="shop_charge">Shop Charge (per square feet)</label>
                            <input type="text" class="form-control" id="shop_charge" name="shop_charge" ng-model="form.shop_charge" placeholder="" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label form-label" for="residential_charge">Residential Charge (per square feet)</label>
                            <input type="text" class="form-control" id="residential_charge" name="residential_charge" ng-model="form.residential_charge" placeholder="" required />
                    </div>

                    <div class="form-group">
                        <label class="control-label form-label" for="interest_rate">Interest Rate (in percent)</label>
                        <input type="text" class="form-control" id="interest_rate" name="interest_rate" ng-model="form.interest_rate" placeholder="" ng-maxlength="99" ng-minlenght="1" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label form-label" for="service_tax">Service Tax (in percent)</label>
                        <input type="text" class="form-control" id="service_tax" name="service_tax" ng-model="form.service_tax" placeholder="" ng-maxlength="99" ng-minlenght="1" required>
                    </div>

                    <div class="form-group">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary pull-left">Update</button>
                            <button type="button" class="btn btn-primary pull-left" ng-click="hideBillingConfig()">
                                Close
                            </button>
                        </div>
                    </div>

                </form>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
@stop


@section('footerCSSAndJS')
    <script type="text/javascript">
        $("#editBillingConfigForm").validate({
            ignore: [],
            rules: {
                office_charge: {
                    required: true,
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
                interest_rate : {
                    required:true,
                    number: true,
                },
                transfer_charge : {
                    required:true,
                    number: true,
                },
            }
        });
    </script>
@stop
