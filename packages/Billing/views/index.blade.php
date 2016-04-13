@extends('admin::layouts.admin_layout')
@section('title', 'Bills')
@section('panel_title', 'Bills')
@section('panel_subtitle','List')

@section('head')
<link href="{!! asset('css/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! asset('bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('js/moment.js') !!}"></script>
<script src="{!! asset('bower_components/select2/dist/js/select2.full.min.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect-collapsible-groups.js') !!}"></script>
<script src="{!! asset('js/bootstrap-datetimepicker.min.js') !!}"></script>

<script type="text/javascript">
    const society_id = "{!! session()->get('user.society_id') !!}";
</script>
<script src="{!! asset('js/billing.js') !!}" type="text/javascript"></script>
@stop

@section('content')
<div class="col-md-12" ng-controller="ItemController">
    <div class="pull-right" style="margin-bottom: 14px">
        <a href="{{ route('billing.create') }}" class="btn btn-primary">
            Add Bill
        </a>
        <!-- <button type="button" ng-click="showAddBillModel()" class="btn btn-primary">Add Bill</button> -->
    </div>
    <div class="clearfix"></div>

    <div role="tabpanel" class="tab-pane active" id="items">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Office Charge</th>
                    <th>Shop Charge</th>
                    <th>Residential Charge</th>
                    <th>Month</th>
                    <th>Buildings - Flats</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
				<tr ng-if="pagination.total == 0" style="margin-left: 30px;">
					<td colspan="6" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
				</tr>
                <tr ng-if="pagination.total > 0" ng-repeat="bill in bills">
                    <td>@{{bill.office_charge}}</td>
                    <td>@{{bill.shop_charge}}</td>
                    <td>@{{bill.residential_charge}}</td>
                    <td>@{{bill.month}}</td>
                    <td>
                        <table class="table table-bordered">
                            <tr>
                                <td style="width:50%;">
                                    <div ng-if="bill.buildings.length == 0 && bill.flats.length == 0">
                                        All buildings
                                    </div>
                                    <div ng-if="bill.buildings.length == 0 && bill.flats.length != 0">
                                        No buildings
                                    </div>
                                    <ol style="padding-left:15px;">
                                        <li ng-repeat="building in bill.buildings">@{{building.name}}</li>
                                    </ol>
                                </td>
                                <td>
                                    <div ng-if="bill.flats.length == 0">All flats</div>
                                    <ol style="padding-left:15px;">
                                        <li ng-repeat="flat in bill.flats">@{{ flat.flat_no }} -
                                            @{{ flat.flat_details.block.block }} -
                                            @{{ flat.flat_details.building.name }}</li>
                                    </ol>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <a ng-click="editBilling()" href="javascript:void(0)" class="glyphicon glyphicon-pencil" title="Update Bill"></a>
                        <a ng-click="deleteBilling()" href="javascript:void(0)" class="glyphicon glyphicon-remove" title="Delete Bill"></a>
                        <a ng-click="showBillGeneratorModal(bill.month)" href="javascript:void(0)" class="glyphicon glyphicon-stats" title="Generate Flat Bills"></a>
                        <!-- <a ng-click="view()" href="javascript:void(0)">View</a> -->
                    </td>
                </tr>
            </tbody>
        </table>
        <div ng-if="pagination.total > 0" class="row">
            <div class="col-lg-12">
                <ul class="pagination pagination-sm" ng-show="(pagination.pageCount) ? 1 : 0">
                    <li ng-class="pagination.prevPageDisabled()">
                      <a href ng-click="pagination.prevPage()" title="Previous"><i class="fa fa-angle-double-left"></i> Prev</a>
                    </li>
                    <li ng-repeat="n in pagination.range()" ng-class="{active: n == pagination.currentPage}" ng-click="pagination.setPage(n)">
                      <a href>@{{n}}</a>
                    </li>
                    <li ng-class="pagination.nextPageDisabled()">
                        <a href ng-click="pagination.nextPage()" title="Next">Next <i class="fa fa-angle-double-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>


    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <button ng-click="close()" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Edit Bill</h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning hide">
            </div>
            <form name="billing_form" ng-submit="submitForm()" novalidate>
                <input  name="society_id"  ng-model="bill.society_id" type="hidden" value="" />
                <div class="form-group">
                    <label class="control-label form-label" for="month">For the month of</label>
                        <input ng-model="bill.month" id="billing-item-start-date" type="text" class="form-control" name="month" ng-click="alertBox('hide')" placeholder="">
                </div>
                <div class="form-group">
                    <label for="office_charge" class="form-label">Office Charge</label>
                    <input  ng-model="bill.office_charge" type="text" class="form-control" id="office_charge" name="office_charge" ng-click="alertBox('hide')" placeholder="Office Charge" ng-minlength="2">
                </div>
                <div class="form-group">
                    <label for="shop_charge" class="form-label">Shop Charge</label>
                    <input  ng-model="bill.shop_charge" type="text" class="form-control" id="shop_charge" name="shop_charge" ng-click="alertBox('hide')" placeholder="Shop Charge" ng-minlength="2">
                </div>
                <div class="form-group">
                    <label for="residential_charge" class="form-label">Residental Charge</label>
                    <input  ng-model="bill.residential_charge" type="text" class="form-control" id="residential_charge" name="residential_charge" ng-click="alertBox('hide')" placeholder="Residental Charge" ng-minlength="2">
                </div>
                <div class="form-group">
                    <label class="control-label " for="flats">Flat's</label>
                    <tags-input ng-model="flats" add-on-paste="true" ng-click="alertBox('hide')" placeholder="Add Flat's">
                        <auto-complete source="getFlats()"
                             min-length="0"
                             load-on-focus="true"
                             load-on-empty="true"
                             max-results-to-show="32" name="flats[]"></auto-complete>
                    </tags-input>
                </div>
                <div class="form-group">
                    <label class="control-label" for="flats">Building's</label>
                    <tags-input ng-model="buildings" add-on-paste="true" ng-click="alertBox('hide')" placeholder="Add Building's">
                        <auto-complete source="getBuildings()"
                             min-length="0"
                             load-on-focus="true"
                             load-on-empty="true"
                             max-results-to-show="32" name="flats[]"></auto-complete>
                    </tags-input>
                </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary pull-left" ng-click="alertBox('hide')">Update</button>
              <button ng-click="close()" type="button" class="btn btn-primary pull-left" data-dismiss="modal" ng-click="alertBox('hide')">Cancel</button>
            </div>
        </form>
     </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="editModal1" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Edit Bill</h4>
        </div>
        <div class="modal-body">
         <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Buildings</th>
                    <th>Flats</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div ng-if="buildings.length == 0 && flats.length == 0">
                            All Buildings
                        </div>
                        <div ng-if="buildings.length == 0 && flats.length != 0">
                            No Buildings
                        </div>
                        <ol ng-repeat="building in buildings">
                            <li>@{{building.text}}</li>
                        </ol>
                    </td>
                    <td>
                        <div ng-if="flats.length == 0">
                            All flats
                        </div>
                        <ol ng-repeat="flat in flats">
                            <li>@{{flat.text}}</li>
                        </ol>
                    </td>
                </tr>
            </tbody>
        </table>
     </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="AddBillModel" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Edit Bill</h4>
        </div>
        <div class="modal-body">
            <div ng-controller="BillingCtrl">
              <form role="create-bill" name="create-bill"  id="create-bill">
              <input  name="society_id"  ng-model="form.society_id" type="hidden" value="" />
                <div class="form-group">
                  <label class="form-label" for="office_charge">Office Charge</label>
                    <input type="text" class="form-control" id="office_charge" name="office_charge" ng-model="form.office_charge" placeholder="">
                </div>

                <div class="form-group">
                  <label class="form-label" for="shop_charge">Shop Charge</label>
                    <input type="text" class="form-control" id="shop_charge" name="shop_charge" ng-model="form.shop_charge" placeholder="">
                </div>

        		<div class="form-group">
                  <label class="form-label" for="residential_charge">Residential Charge</label>
                    <input type="text" class="form-control" id="residential_charge" name="residential_charge" ng-model="form.residential_charge" placeholder="">
                </div>

               <div class="form-group">
                <label class="form-label" for="month">For the month of</label>
                    <input type="text" ng-click="warningBox('hide')" class="form-control" name="month" ng-model="form.month" id="billing-item-start-date" placeholder="">
              </div>

                <div class="form-group">
                    <label class="form-label" for="flats">Flat's</label>
                    <select class="form-control" name="flats[]" multiple="multiple" ng-model="form.blocks" id="billing-item-flats" placeholder="">
                        <option disabled>Select Flats</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="billing-item-blocks">Building's</label>
                    <div class="control-label">
                        <select name="buildings[]" multiple="multiple" ng-model="form.Buildings" id="billing-item-buildings" placeholder="">
                            <option disabled>Select Building's</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-5">
                    <button type="submit" class="btn btn-primary">Generate</button>
                  </div>
                </div>
              </form>
        </div>
     </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    </div>

    @include('Billing::layouts.partial.generate_flat_bill')
</div>
@stop

@section('footerCSSAndJS')
    <script>
        $("#editModal form").validate({
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
    </script>
    <script src="{!! asset('js/main.js') !!}"></script>
@stop
