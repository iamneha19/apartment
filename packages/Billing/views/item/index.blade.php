@extends('admin::layouts.admin_layout')
@section('title', 'Billing Item list')
@section('panel_title', 'Billing Item')
@section('panel_subtitle', 'List')

@section('head')
<link href="{!! asset('css/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet" type="text/css" />

<link href="{!! asset('bower_components/select2/dist/css/select2.min.css') !!}" rel="stylesheet" type="text/css" />

<link href="{!! asset('bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('js/moment.js') !!}"></script>
<script src="{!! asset('bower_components/select2/dist/js/select2.full.min.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect-collapsible-groups.js') !!}"></script>
<script src="{!! asset('js/bootstrap-datetimepicker.min.js') !!}"></script>

<script type="text/javascript">
    const society_id = "{!! session()->get('user.society_id') !!}";


        $("[name=billing_form]").validate({
                ignore: [],
                rules: {
                    item_name: {
                            required:true
                    },
                    charge: {
                                required:true,
                                number: true,
                         },
                    month: {
                        required: function() {
                            return jQuery('[name=fixed_billing_item]').val() === 'NO';
                        },
                    },
                    fixed_billing_item: {
                        required:true,
                    } ,
                }
        });

    app.controller('ItemController', function($scope, paginationServices, $http, $filter) {
        $scope.activeIndex=null;

        $scope.pagination = paginationServices.getNew(10);
        $scope.getItems = function(currentPage) {
            var options =  {'society_id': society_id};
            options.page = currentPage;
            $http.get(generateUrl('v1/billing/items', options))
            .then(function(response) {
                $scope.items = response.data.results.data;
                $scope.pagination.total = response.data.results.total;
                $scope.pagination.pageCount = response.data.results.last_page;
				if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
            });
        };

        $scope.getItems();
        $scope.$on('pagination:updated', function(event,data) {
            $scope.getItems($scope.pagination.currentPage);
        });

        $scope.editBillingItem = function() {
            var $this = this;
            $scope.activeIndex = this.$index;
            $('#editModal').modal('show');
            $http.get(generateUrl('v1/billing/item/'+$this.item.id, {'society_id': society_id}),$scope.form)
              .then(function(response) {
                   $scope.form = response.data.results;
                   $scope.flats = response.data.results.flats;
                   $scope.buildings = response.data.results.buildings;
                   if ($scope.form.fixed_billing_item == 'YES') {
                       $scope.form.month = moment().format('MMMM YYYY');
                   }
            });
        }

        $scope.deleteBillingItem = function() {
            var r = confirm("Deleted Item cannot be retrieved");
            if (r == true) {
            var $this = this;
            $http.delete(generateUrl('v1/billing/item/'+this.item.id,{'society_id': society_id}))
            .then(function(r){
                    grit('',r.data.message);
                    $scope.items= $filter('filter')($scope.items, function(value, index) {return value.id != $this.item.id});
                });
            }  else {
                return ;
            }
        };

        $scope.submitForm = function() {

        if (!$("[name=billing_form]").valid())
            return ;
            var $this = this;
            $this.form.item_name = $this.form.name;
            $this.form.society_id = society_id;
            $this.form.month = jQuery('#billing-item-start-date').val();
            $http.post(generateUrl('v1/billing/item/' + $this.form.id),$this.form)
            .then(function(response) {
                if (response.data.status == 'success') {
                    $('#editModal').modal('hide');
                    grit('',response.data.message);
                    $scope.form = response.data.results.results;
                    $scope.getItems();
                } else if (response.data.status == 'validation_failed') {
                    jQuery('#editModal .alert.alert-warning').text(response.data.message).removeClass('hide');
                } else {
                    console.log(response);
                }
            });
        }

        $scope.alertBox = function(status) {
            jQuery('#editModal .alert.alert-warning').addClass(status);
        };

        $scope.getFlats = function() {
            return $http.get(generateUrl('v1/flats', {'society_id': society_id}))
            .then(function(response) {
                return response.data.results;
            });
        }

        $scope.getBuildings = function() {
            return $http.get(generateUrl('v1/buildings', {'society_id': society_id}))
            .then(function(response) {
                return response.data.results;
            });
        }

        $scope.view = function() {
            $('#billing-item-view').modal('show');
             $http.get(generateUrl('v1/billing/item/'+this.item.id, {'society_id': society_id}))
             .then(function(r) {
                 $scope.buildings = r.data.results.buildings;
                 $scope.flats = r.data.results.flats;
             });
        }
    });
</script>
@stop

@section('content')
<div class="col-md-12">
    <div class="pull-right" style="margin-bottom: 14px">
        <a href="{{ route('billing.item.create') }}" class="btn btn-primary">
            Add Bill Item
        </a>
    </div>
    <div class="clearfix"></div>

    <div role="tabpanel" class="tab-pane active" id="items" ng-controller="ItemController">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Billing Item Name</th>
                    <th>Charge</th>
                    <th>Month</th>
                    <th>Flat Type</th>
                    <th>Buildings - Flats</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
				<tr ng-if="pagination.total == 0" style="margin-left: 30px;">
					<td colspan="6" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
				</tr>
                <tr ng-if="pagination.total > 0" ng-repeat="item in items">
                    <td>@{{item.name}}</td>
                    <td>@{{item.charge}}</td>
                    <td><span>@{{ item.fixed_billing_item === 'YES' ? 'Fixed Billing Item *': item.month }}</span></td>
                    <td>@{{ item.flat_type.capitalizeFirstLetter() || 'None' }}</td>
                    <td>
                        <table class="table table-bordered equal-td">
                            <tr>
                                <td>
                                    <div ng-if="item.buildings.length == 0 && item.flats.length == 0">
                                        All Buildings
                                    </div>
                                    <div ng-if="item.buildings.length == 0 && item.flats.length != 0">
                                        No Buildings
                                    </div>
                                    <ol style="padding-left:15px;">
                                        <li ng-repeat="building in item.buildings">@{{building.name}}</li>
                                    </ol>
                                </td>
                                <td>
                                    <div ng-if="item.flats.length == 0">All flats</div>
                                    <ol style="padding-left:15px;">
                                        <li ng-repeat="flat in item.flats">@{{ flat.flat_no }} -
                                            @{{ flat.flat_details.block.block }} -
                                            @{{ flat.flat_details.building.name }}</li>
                                    </ol>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <a ng-click="editBillingItem()" href="javascript:void(0)" class="glyphicon glyphicon-pencil" title="Update Bill Item"></a>
                        <a ng-click="deleteBillingItem()" href="javascript:void(0)" class="glyphicon glyphicon-remove" title="Delete Bill Item"></a>
                        <!-- <a ng-click="view()" href="javascript:void(0)">View</a> -->
                    </td>
                </tr>
            </tbody>
        </table>
        <h5>*Fixed Billing Items are those items which are fixed and will be attached in every month for every bill.</h5>
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
                  <button type="button" class="close" ng-click="alertBox('hide')" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true" >&times;</span>
                  </button>
                  <h4 class="modal-title">Edit Billing Item</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning hide">
                    </div>
                    <form name="billing_form" ng-submit="submitForm()" novalidate>
                        <input  name="id" ng-model="form.id" type="hidden" />
                        <div class="form-group">
                            <label class="control-label form-label" for="item_name">Item Name</label>
                            <input type="text" name="item_name" class="form-control" ng-model="form.name" id="item-name" required ng-minlength="2" ng-click="alertBox('hide')" placeholder="">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="flats">Building's</label>
                            <tags-input ng-model="buildings" add-on-paste="true" ng-click="alertBox('hide')" placeholder="">
                                <auto-complete source="getBuildings()"
                                     min-length="0"
                                     load-on-focus="true"
                                     load-on-empty="true"
                                     max-results-to-show="32" name="flats[]"></auto-complete>
                            </tags-input>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="flats">Flat's</label>
                            <tags-input ng-model="flats" add-on-paste="true" ng-click="alertBox('hide')">
                                <auto-complete source="getFlats()"
                                     min-length="0"
                                     load-on-focus="true"
                                     load-on-empty="true"
                                     max-results-to-show="32" name="flats[]"></auto-complete>
                            </tags-input>
                        </div>

                         <!-- <div class="form-group">
                          <label class="control-label" for="flat_category">Occupancy</label>
                          <select class="form-control" name="flat_category"  ng-click="alertBox('hide')" ng-model="form.flat_category">
                              <option value="">None</option>
                              <option value="tenant">Tenant</option>
                              <option value="owner">Owner</option>
                          </select>
                        </div> -->

                         <div class="form-group">
                          <label class="control-label" for="flat_type">Flat Type</label>
                          <select class="form-control" name="flat_type"  ng-click="alertBox('hide')" ng-model="form.flat_type">
                              <option value="">None</option>
                              <option value="office">Office</option>
                              <option value="shop">Shop</option>
                              <option value="flat">Flat</option>
                          </select>
                        </div>

                        <div class="form-group">
                          <label class="control-label form-label" for="fixed_billing_item">Fixed billing item</label>
                          <select class="form-control" ng-model="form.fixed_billing_item" ng-click="alertBox('hide')" name="fixed_billing_item">
                              <option value="YES">Yes</option>
                              <option value="NO">No</option>
                          </select>
                        </div>

                        <div class="form-group" ng-hide="form.fixed_billing_item !== 'NO'">
                          <label class="control-label form-label" for="month">For the month of</label>
                          <input type="text" class="form-control" ng-model="form.month" id="billing-item-start-date">
                        </div>

                        <div class="form-group">
                            <label class="control-label form-label" for="charge">Charge</label>
                            <input type="text" class="form-control" name="charge" ng-model="form.charge" id="charge" placeholder="">
                        </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-left"  ng-click="alertBox('hide')">Update</button>
                      <button type="button" class="btn btn-primary pull-left"  ng-click="alertBox('hide')" data-dismiss="modal"ng-click="alertBox('hide')">Cancel</button>
                    </div>
                    </form>
                </div>
              </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


<div id="billing-item-view" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" ng-click="alertBox('hide')" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Edit Bill Item</h4>
        </div>
        <div class="modal-body">
         <table class="table table-bordered">
            <tr>
                <th>Buildings</th>
                <th>Flats</th>
            </tr>
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
        </table>
     </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</div>
</div>
@stop

@section('footerCSSAndJS')
    <script src="{!! asset('js/main.js') !!}"></script>
@stop
