@extend('admin::layouts.admin_layout')
@section('title', 'Configuration')
@section('panel_title', 'Configuration')
@section('head')
@stop
@section('content')
<script>
    const society_id = "{!! session()->get('user.society_id') !!}";
    var billingUrl ="{!! route('billing.index'); !!}";
</script>

<script type="text/javascript" src="{!! asset('js/society-config.js') !!}"></script>

<div ng-controller="ConfigurationCtrl" class="col-lg-12" id="configurationDiv">
    <button ng-click="buildingConfig()">building</button>
    <div class="row">
        <form id="society-config-form" novalidate>
            <div class="col-lg-9" id="building-form-wrapper">
                <div class="row col-1">
                    <div class="col-lg-4">
                        <h4>Enter number of Buildings</h4>
                    </div>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" ng-model="form.building_count" id="building_count">
                        <button class="btn btn-primary" ng-click="saveBuilding()">SAVE</button>
                    </div>
                </div>

                <div class="row col-2">
                    <div class="col-lg-4">
                        <h4>Amenities</h4>
                    </div>
                    <div class="col-lg-8 ">

                    <tags-input ng-model="amenity_tags" add-on-paste="true" placeholder="Search Amenities">
                        <auto-complete source="getAmenities()"
                             min-length="0"
                             load-on-focus="true"
                             load-on-empty="true"
                             max-results-to-show="32" name="amenities[]"></auto-complete>
                    </tags-input>

                        <!-- <select ng-options="item as item.text for item in amenity_tags track by item.id" ng-model="amenity_tags" name="society_amenities[]" id="society_amenities" multiple="multiple">
                            <option disabled>Select Amenities</option>
                        </select> -->
                    </div>
                </div>
            </div>

        </form>
        <div class="row col-3 hide" id="dummy-building-form">
            <button class="close close-building">x</button>
            <div class="col-lg-3 col-md-3">
                <P>Name of the building</p>
                <input type="text" palceholder="Name of the building" class="form-control building_names" name="building_names[]" />
            </div>
            <div class="col-lg-2 col-md-2 ">
                <P>Is wing exist</p>
                <p>
                    <input type="radio" class="has_wing" value="1" /> Yes
                    <span class="radio-wing">
                        <input type="radio" class="has_wing" value="0" /> No
                    </span>
                    <button class="btn btn-primary btn-xs wingClass" onclick="mapBuildingId(this.id)" >Add Wings</button>
                </p>
            </div>
            <div class="col-lg-5 col-md-5 lastChild ">
                <P>Amenities</p>

                <!-- <tags-input class="col-sm-12 building_amenities" ng-model="building_amenities" add-on-paste="true" placeholder="Search Amenities">
                    <auto-complete source="getAmenities()"
                         min-length="0"
                         load-on-focus="true"
                         load-on-empty="true"
                         max-results-to-show="32" name="building_amenities[]"></auto-complete>
                </tags-input> -->
                <select class="col-sm-12 form-control" name="building_amenities[]" >
                </select>
                <span><button class="btn btn-primary btn-xs">Add Wings</button></span>
            </div>
            <div class="clearfix"></div>
            <div class="internal-pop">
            	<div class="row">

					<div class="col-md-9">
                    	<div class="row">
                        	<div class="col-lg-4">
	                            <input type="text" class="form-control" ng-model="" id="" value="A Wing">
		                    </div>
                            <div class="col-lg-8">
                                <select class="col-sm-12 form-control" name="">
                                    <option>Amenities</option>
                                </select>
                                <span class="button-group">
                                <button class="btn btn-primary glyphicon glyphicon-cog"></button>
                                <button class="btn btn-primary glyphicon glyphicon-remove"></button>
                                <button class="btn btn-primary glyphicon glyphicon-plus"></button>
                                </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                        	<div class="col-lg-4 col-md-4">
	                            <input type="text" class="form-control" ng-model="" id="" value="A Wing">
		                    </div>
                            <div class="col-lg-8 col-md-8">
                                <select class="col-sm-12 form-control" name="">
                                    <option>Amenities</option>
                                </select>
                                <span class="button-group">
                                <button class="btn btn-primary glyphicon glyphicon-cog"></button>
                                <button class="btn btn-primary glyphicon glyphicon-remove"></button>
                                </span>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-3"></div>
                    <p class="internal-pop-save"><button class="btn btn-primary">SAVE</button></p>
                </div>
            </div>
        </div>
    </div>

<!--Building Configuration Model-->
<div id="buildingconfig" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
         <div class="modal-header">
            <button type="button" ng-click="closeModal()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Building Configuration</h4>
        </div>
        <div class="modal-body">
            <!--demo data-->
            <form class="form-horizontal" name="buildingForm"  ng-submit="submitForm()" novalidate>
                <div class="form-group">
                  <label for="input-sm" class="col-xs-6">Number of Floors</label>
                  <div class="col-xs-3">
                      <input type="text" name="floors" ng-change="floorsInput()" ng-model="floor.no_of_floor" class="form-control input-sm" required="" />

                  </div>
                  <div style="margin-left: 14px;">
                   <label class="error"
                       ng-show="buildingForm.$submitted && buildingForm.floors.$invalid ">
                       Please enter valid Floor
                        </label>
                  </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="input-sm" class="col-xs-12">Is Flat Same on Each Floors</label>
                    <div id="checks"class="col-xs-12">
                        <label  name="forYes" class="radio-inline"><input ng-model="floor.is_flat_same_on_each_floor" ng-click="yes()" value="YES" type="radio" ng-checked="floor.is_flat_same_on_each_floor == 'YES'" name="optradio">Yes</label>
                        <label  id="forNo" class="radio-inline"><input ng-click="no()" ng-model="floor.is_flat_same_on_each_floor" value="NO" type="radio" ng-checked="floor.is_flat_same_on_each_floor == 'NO'"  name="optradio">No</label>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                     <label for="input-sm" class="col-xs-12">Please Specify Flats</label>
                        <div class="col-xs-12">
                            <input id="fields" name="field" type="text" ng-model="floor.flat_on_each_floor" class="form-control input-sm"  placeholder="No. of Flats"  />
                       <label class="error"
                       ng-show="buildingForm.$submitted && buildingForm.field.$invalid ">
                       Please enter valid total Flats
                        </label>
                        </div>
                </div>
                <div id="sandbox">
                </div>
                <input type="hidden" value="@{{building_id}}">
                  <button type="submit" class="btn btn-primary">Save</button>

            </form>
        </div>

    </div>
  </div>
</div>





<!--Add Block Modal -->
<div class="modal fade"  id="blocks_modal" tabindex="0" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
  <div class="modal-dialog" role="document" >
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" ng-click="closeBlock()"data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Wings</h4>
        </div>
        <div class="modal-body" >
            <form ng-show="block.editing == false" ng-submit="submitBlock()" id="block_form" name="block_form" class="form-inline" novalidate>
                <div class="col-sm-5">
                    <input  ng-change = "duplicateBlocks()"ng-model="block.block" type="text" class="form-control wingsLayout" name="block" id="exampleInputName2" placeholder="Wing Name" required>
                    <label class="error"
                        ng-show="duplicateBlock != null">
                        @{{duplicateBlock}}
                    </label>
                    <label class="error"
                        ng-show="block_form.$submitted && block_form.block.$invalid">
                        Please enter Block
                    </label>
					
					
					
					<select multiple="multiple" class="form-control wingsLayout" name="amenity" id="amenity" ng-model="form.amenity">
                                     <!--<option value="" selected="">Select Amenitiy</option>-->     
									 <option value="" selected disabled>Select Amenity</option>
									<option ng-repeat="amenity in amenities" value="@{{ amenity.id }}">@{{ amenity.name }}</option>
                    </select>
					<label class="error"
                        ng-show="amenities.length == 0">
                        Amenities are not available.
                    </label>
					
					<button  type="submit" class="btn btn-primary wingsLayout" ng-disabled="disable">Add Wing</button>

                </div>
            </form>
			
			<!--Edit Block-->
			
			<form ng-show="block.editing" id="edit_block_form" name="edit_block_form" class="form-inline" novalidate>
                <div class="col-sm-5">
                    <input  ng-change = "duplicateBlocks()"ng-model="block.edit_block" type="text" class="form-control wingsLayout" name="edit_block" id="exampleInputName2" placeholder="Block Name" required>
                    <label class="error"
                                        ng-show="block.duplicate != null">
                                        '@{{block.duplicate}}'
                                    </label>
                    <label class="error"
                        ng-show="edit_block_form.$submitted && edit_block_form.edit_block.$invalid">
                        Please enter Block
                    </label>
					
					<select multiple="multiple" class="form-control wingsLayout" name="edit_amenity" id="edit_amenity" ng-model="form.edit_amenity">
									 <option value="" selected disabled>Select Amenity</option>
									<option ng-repeat="amenity in amenities"  
											ng-selected="getSelectedAmenities(amenity.id)" value="@{{ amenity.id }}">@{{ amenity.name }}</option>
                    </select>
					
					<button  ng-disabled="disable" ng-click="updateBlock()" type="submit" class="btn btn-primary btn-sm">Update</button>
                    <button type="submit" ng-click="cancelBlock()" class="btn btn-primary btn-sm" >Cancel</button>

                </div>
            </form>
			
			
            <div class="clearfix">
                <table class="table" >
                    <thead>
                        <tr class="scrollLayout">
                            <th>Wings</th>
                            <th>Operations</th>
                        </tr>
                    </thead>
					<tbody style="height: 200px ;overflow-y: auto ;display: block;" >
					
                    <!--<tbody style="height: 50px;overflow-y: auto;" >-->
                        <tr ng-repeat="block in blocks" class="edit scrollLayout" on-finish-render="ngRepeatFinished" >
                            <td>
                                <form name="spanBlock" novalidate>
									<a class="listAmenitiesClass" href="javascript:void(0)" id="block_@{{block.id}}" ng-mouseleave="hideAmenitiesModal()"  ng-mouseover="listAmenities(block.id)">@{{block.block}}</a>
<!--                                    <span ng-show="block.editing == null">@{{block.block}}</span>
                                    <span ng-show="block.editing">
                                        <input ng-change="duplicateBlocks()"type="text" ng-model="block.block" class="form-control" name="blockUpdate" required>
                                    <label class="error"
                                        ng-show="block.duplicate != null">
                                        '@{{block.duplicate}}'
                                    </label>
                                    <label class="error"
                                        ng-show="spanBlock.$submitted && spanBlock.blockUpdate.$invalid">
                                        Please enter Block
                                    </label>
                                        <div style="margin-top: 7px;">
                                        <button  ng-disabled="disable" ng-click="updateBlock()" type="submit" class="btn btn-primary btn-sm">Update</button>
                                        <button type="submit" ng-click="cancelBlock()" class="btn btn-primary btn-sm" >Cancel</button>
                                    </div>
                                    </span>-->
                                </form>
                            </td>

                            </div>
                        <td >
                            <a href="" title="Edit Wing" class="glyphicon glyphicon-pencil" ng-click="editBlock()"></a>
                            <a href="" title="Delete Wing" class="glyphicon glyphicon-remove" ng-click="deleteBlock()"></a>
							<a href="javascript:void(0)" ng-click="loadConfigurations(block.id)" class="glyphicon glyphicon-cog" title="Configure Wing" ng-click="configureBlock()"></a>
                        </td>
                        </tr>
                    </tbody>
            </table>

            </div>
            <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader1" class="loading">Loading&#8230;</div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->




<div id="amenitiesModal" class="modal fade bs-example-modal-sm"  role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-sm" >
		<div class="modal-content" style="width: 200px;">
			<div class="modal-header" style="padding-top: 0px;padding-bottom: 0px;">
				<h4>Blockwise Amenities</h4>
			</div>
			<div class="modal-body" id="loadAmenities">
				
			</div>
		</div>
	</div>
</div>

<div id="configModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-sm" style="width: 25%;">
    <div class="modal-content">
         <div class="modal-header">
            <button type="button" ng-click="closeModal()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel" ng-show="flatEditing === ''">Wings Configuration (@{{block.block}})</h4>
			<h4 class="modal-title" id="myModalLabel" ng-show="flatEditing !== ''">Edit Wings Configuration (@{{block.block}})</h4>
        </div>
        <div class="modal-body">
            <!--demo data-->
            <form class="form-horizontal" name="configForm"  id="configForm"  novalidate>
                <div class="form-group">
                  <label for="input-sm" class="col-xs-6">Number of Floors</label>
                  <div class="col-xs-3">
                      <input type="text" ng-change="shuffleOption()" name="nos_floors" ng-model="config.floor" class="form-control input-sm" required  />
                  </div>
                </div>
				<div class="col-lg-10" id="nosFloorsErr">
<!--						<label class="error"
							ng-show="configForm.$submitted && configForm.nos_floors.$invalid">
							Please Enter atleast 1 Floor.
						</label>-->
				</div>
                <hr>
                <div class="form-group">
                    <label for="input-sm" class="col-xs-12">Is Flat Same on Each Floors</label>
                    <div id="checks"class="col-xs-12">
                        <label  name="forYes" class="radio-inline"><input class="uncheckRadio" ng-model="config.isFlatSame" ng-click="sameFlat()" value="YES" type="radio" required  name="isFlatSame">Yes</label>
                        <label  id="forNo" class="radio-inline"><input class="uncheckRadio" ng-click="diffFlat()" ng-model="config.isFlatSame" value="NO" type="radio" required name="isFlatSame">No</label>
                    </div>
                </div>
					<div class="col-lg-10" id="isFlatSameErr">
<!--						<label class="error"
							ng-show="configForm.$submitted && configForm.isFlatSame.$invalid">
							Please Select one option.
						</label>-->
					</div>
                <hr>
                <div class="form-group hide" id="sameFlatModal">
<!--                    <label for="input-sm" class="col-xs-12">Please Specify Flats</label>
                    <div class="col-xs-12">
                            <input name="flats" id="flats"type="text" ng-model="config.flats" class="form-control input-sm"  placeholder="No. of Flats"/>
                    </div>-->
                </div> 
                <div id="diffFlatModal" class="diffFlatModalScroll hide" style="margin-bottom: 10px;">
					
				</div>
                  <button type="submit"  ng-click="submitConfig()" ng-show="flatEditing === ''" class="btn btn-primary">Save</button>
				  <button type="submit"  ng-click="updateConfig()" ng-show="flatEditing !== ''" class="btn btn-primary">Update</button>
				  <input type="button" ng-click="closeModal()" value="Cancel" class="btn btn-primary" />
				  <!--<button type="click"  ng-click="closeModal()" class="btn btn-primary">Cancel</button>-->
				  <input type="hidden" name="blockId" ng-value="block.id" />
            </form>
        </div>
     
    </div>
  </div>
</div>

</div>
@stop

@section('footerCSSAndJS')
    <script type="text/javascript">
    var et = [];
        $("#society-config-form").validate({
        	ignore: [],
            rules: {
                building_count: {
                    required:true,
                    number: true
                }
            },
            errorPlacement: function(error, element) {
                element.parent().append(error);
            }
        });
    </script>
@stop
