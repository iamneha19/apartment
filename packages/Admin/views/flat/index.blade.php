@extends('admin::layouts.admin_layout')
@section('title', 'Flats')
@section('panel_title','Flats')
@section('panel_subtitle','List')

@section('head')
<link href="{!! asset('css/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! asset('bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('js/moment.js') !!}"></script>
<script src="{!! asset('bower_components/select2/dist/js/select2.full.min.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect-collapsible-groups.js') !!}"></script>
<script src="{!! asset('bower_components/bootbox.js/bootbox.js') !!}"></script>
<script src="{!! asset('js/bootstrap-datetimepicker.min.js') !!}"></script>

<script type="text/javascript">
    const society_id = "{!! session()->get('user.society_id') !!}";

    jQuery(function() {
        jQuery("#add_flat-form").validate({
            rules: {
                role: "required",
                type: "required",
                flat_no : "required",
                building_id : "required",
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "type"  ) {
                    $( ".visiblity_error" ).html( error );
                }else if (element.attr("name") == "role"  ) {
                    $( ".visiblity_role_error" ).html( error );
                }else {
                  error.insertAfter(element);
                }
            }
         });

         jQuery('#attachFlatModalForm').validate({
             rules: {
                 user_id: 'required',
                 building_id: 'required',
                 flat_id: 'required',
                 square_feet_1: 'required'

             }
         });

//         $('#id').keyup(function() {
//            $('span.error-keyup-1').hide();
//            var inputVal = $(this).val();
//                var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
//            if(!numericReg.test(inputVal)) {
//            $(this).after('<span class="error error-keyup-1">Numeric characters only.</span>');
//             $("input[type=submit]").attr("disabled", "disabled");
//            }
//            else if(inputVal.length === 0) {
//                $("input[type=submit]").removeAttr("disabled");
//            }
//            else {
//                 $("input[type=submit]").removeAttr("disabled");
//            }
//
//            });
});

</script>
<script src="{!! asset('js/flat.js') !!}" type="text/javascript"></script>
@stop

@section('content')

<div class="col-md-12" ng-controller="FlatController">
    <div class="pull-right" style="margin-bottom: 14px">
        <button ng-click="openFlatModal($http)" class="btn btn-primary">
            Add Flat
        </button>
        <button ng-click="openAttachFlatModal()" class="btn btn-primary">Attach Flat - User</button>
        <!-- <button type="button" ng-click="showAddBillModel()" class="btn btn-primary">Add Bill</button> -->
    </div>
    <div class="clearfix"></div>
    <div role="tabpanel" class="tab-pane active" id="items">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Building</th>
                    <th>Block</th>
                    <th>Flat No.</th>
                    <th>Square feet </th>
                    <th>Flat/Shop/Office</th>
                    <th>floor No.</th>
                    <th>Occupancy</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
				<tr ng-if="pagination.total == 0" style="margin-left: 30px;">
					<td colspan="7" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
				</tr>
                <tr ng-if="pagination.total > 0" ng-repeat="flat in flats">
                    <td>@{{ flat.user_society.building.name.capitalizeFirstLetter() }}</td>
                    <td>@{{ flat.user_society.block.name.capitalizeFirstLetter() }}</td>
                    <td>@{{ flat.flat_no }}</td>
                    <td>@{{ flat.square_feet_1 }}</td>
                    <td>@{{ flat.type.capitalizeFirstLetter() }}</td>
                    <td>@{{flat.floor}}</td>
                    <td>@{{ flat.user_society.relation.capitalizeFirstLetter() }}</td>
                    <td>
                        <a ng-click="editFlatModal(flat.id)" href="javascript:void(0)" class="glyphicon glyphicon-pencil" title="Update"></a>
                        <a ng-if="! flat.user_society.user_id" ng-click="deleteFlatModal(flat)" href="javascript:void(0)" class="glyphicon glyphicon-remove" title="Delete"></a>
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
    </div>
    <div class="modal fade" id="AddFlatModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="user">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                        ng-click="closeFlatForm()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Add Flat</h4>
                </div>
                <div class="modal-body">
                    <form id="add_flat-form" ng-submit="addFlat()" novalidate>
                        <div class="alert alert-warning hide"></div>
                        <div class="form-group">
                            <label class="form-label">Building</label>
                            <select class="form-control" name="building_id" ng-change="getBlocks(form.building_id)" ng-model="form.building_id"
                                     ng-options="selectedBuilding.id as selectedBuilding.text for selectedBuilding in buildings">
                                     <option value="" selected="">Select Building</option>
                            </select>
    <!--                            <select ng-model="form.building_id" ng-change="getBlocks(form.building_id)" name="building_id" class="form-control">
                                <option value="" disabled selected>Select Building</option>
                                <option ng-repeat="selectedBuilding in buildings" value="@{{ selectedBuilding.id }}" ng-selected="form.building_id == selectedBuilding.id">@{{ selectedBuilding.text }}</option>
                            </select>-->
                        </div>
                        <div class="form-group">
                            <label for="block_id" id="select-block_id">Block</label>
                            <select class="form-control" name="block_id"  ng-model="form.block_id"
                                     ng-options="block.id as block.block for block in blocks">
                                     <option value="" selected="">Select Block</option>
                            </select>
<!--                            <select class="form-control" name="block_id" ng-model="form.block_id">
                                <option value="" selected disabled="disabled">Select Block</option>
                                <option ng-repeat="block in blocks" value="@{{ block.id }}" ng-selected="block.id == form.block_id">@{{ block.block }}</option>
                            </select>-->
                        </div>
                        <div class="form-group">
                            <label class="form-label">Flat No./ Shop No./ Office No</label> <input type="text"
                                id='input-flat_no' class="form-control" maxlength="4" ng-model="form.flat_no"
                                name="flat_no" placeholder="My Flat No./ Shop No./ Office No">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Floor No.</label> <input type="text"
                                id='input-flat_no' class="form-control" maxlength="4" ng-model="form.floor"
                                name="floor" placeholder="floor_no">
                        </div>
                         <div class="form-group">
                            <label class="form-label">Square feet</label> <input type="text" id="id"
                                 class="form-control" maxlength="4" ng-model="form.square_feet_1"
                                name="square_feet_1" placeholder="Square feet">
                            <div class="visiblity_error"></div>
                        </div>


                        <div class="form-group type-radio-group">
                            <label class="form-label">Flat type</label>
                            <div class="form-group">
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="type" ng-model="form.type" value="office">
                                        Office
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="type" ng-model="form.type" value="shop">
                                        Shop
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="type" ng-model="form.type" value="flat">
                                        Flat
                                    </label>
                                </div>
                            </div>
                            <div class="visiblity_error"></div>
                        </div>

                        <div class="form-group type-radio-group">
                            <label class="form-label">Occupancy</label>
                            <div class="form-group ">
                                <div class="radio-inline">
                                    <label> <input type="radio" name="role" ng-model="form.relation" value="owner"> Owner
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label> <input type="radio" name="role" ng-model="form.relation" value="tenant"> Tenant
                                    </label>
                                </div>
                            </div>
                            <div class="visiblity_role_error"></div>
                        </div>
                        <input type="submit" class="btn btn-primary" name="submit" value="Update" />
                        <button class="btn btn-primary" type="button" ng-click="closeFlatForm()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachFlatModal" tabindex="-1" role="dialog" ng-controller="AttachFlatController">
    <div class="modal-dialog" role="user">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" ng-click="closeAttachFlatModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Attach Flat</h4>
            </div>
            <div class="modal-body">
                <form id="attachFlatModalForm" ng-submit="attachFlat()" novalidate>
                    <div class="alert alert-warning hide"></div>
                    <div class="form-group">
                        <label for="user_id" class="form-label">User</label>
                        <select class="form-control" name="user_id" ng-model="form.user_id">
                            <option value="" selected disabled>Select User</option>
                            <option ng-repeat="user in users" value="@{{ user.id }}">@{{ user.name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="building_id" class="form-label">Building</label>
                        <select class="form-control" name="building_id" ng-change="getBlocks(form.building_id)" ng-model="form.building_id">
                            <option value="" selected disabled="disabled">Select Building</option>
                            <option ng-repeat="building in buildings" value="@{{ building.id }}">@{{ building.text }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="block_id">Block</label>
                        <select class="form-control" name="block_id" ng-change="getFlats(form.block_id)" ng-model="form.block_id">
                            <option value="" selected disabled="disabled">Select Block</option>
                            <option ng-repeat="block in blocks" value="@{{ block.id }}">@{{ block.block }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="flat_id" class="form-label">Flat</label>
                        <select class="form-control" name="flat_id" ng-model="form.flat_id">
                            <option value="" selected disabled="disabled">Select Flat</option>
                            <option ng-repeat="flat in flats" value="@{{ flat.id }}">@{{ flat.text }}</option>
                        </select>
                    </div>
                    <input type="submit"  class="btn btn-primary" name="submit" value="Submit" />
                    <button class="btn btn-primary" type="button" ng-click="closeAttachFlatModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
