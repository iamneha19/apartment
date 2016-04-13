@section('title', 'Building')
@section('panel_title', 'Building')
@section('panel_subtitle', 'List')
@section('head')
    <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{!! asset('bower_components/select2/dist/css/select2.min.css') !!}" rel="stylesheet" type="text/css" />
    <script src="{!! asset('bower_components/select2/dist/js/select2.full.min.js') !!}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@stop
@section('content')
<style>
    .modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}
</style>
    <script>
	app.controller("BuildingListCtrl", function(URL,paginationServices,$scope,$http,$filter) {
            $('#loader').hide();
            $scope.config_details;
            $scope.parking;
            $scope.building_amenities;
            $scope.society_config_details;
            $scope.building_name;
            $scope.building_flats;
            $scope.wing_name;
            $scope.wing_flats;
            $scope.single_floor_wing;
            $scope.multiple_floor_wing;
            $scope.details;
            $scope.buildings;
            $scope.building_count;
            $scope.amenities;
            $scope.pagination = paginationServices.getNew(5);
            $scope.itemsPerPage = 5;
            $scope.sort = 'created_at';
            $scope.sort_order = 'desc';
            $scope.search='';
            $scope.createPermissionType = {};
            $scope.inputAttendeeType;
            $scope.inputLevelType = 'society';
            $scope.wings = [];
             $('#loader').hide();

            $scope.getBuildings = function() {
//                var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order}
//                if(search){
//                   options['search']=search;
//                }
                var request_url = generateUrl('v1/building/details');
                $http.get(request_url)
                .success(function(response, status, headers, config) {
                        $scope.building_count = response.results.count;
                        $scope.amenities = response.results.amenities;
                        $scope.buildings = response.results.building_details;
                        $scope.society_config_details = response.results.society_config[0];
                        CKEDITOR.replace( 'description' );
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
             $scope.getBuildings();
             
            $scope.$watch('search', function(newValue, oldValue) {
                if(newValue){
                    $scope.pagination.total=0;
                    $scope.pagination.offset = 0;
                    $scope.pagination.currentPage = 1;
                    $scope.sort = 'title';
                    $scope.sort_order = 'asc';
                    $scope.pagination.setPage(1);
                }else{
                    $scope.pagination.setPage(1);
                }
            });

        $scope.order = function(predicate) {
            $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
            $scope.predicate = predicate;
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.sort = predicate;
            $scope.sort_order = ($scope.reverse) ? 'asc' : 'desc';
            $scope.pagination.setPage(1);
        };
        /*date formatfunction */

        // Update roles when level (society,building) is changed
        $scope.$watch('inputLevelType', function(newValue, oldValue) {
            if($scope.inputLevelType == 'society' && $scope.createPermissionType.length  == 2){
                    $scope.roles = $scope.societyRoles;

            }
            if($scope.inputLevelType == 'building' && $scope.createPermissionType.length  == 2){
                $scope.roles = $scope.buildingRoles;
            }
        });
        /* Building Configuration Details*/
//        $scope.openBuilding = function(buildingId){
//			$('#buildingconfig').modal();
//            var request_url = generateUrl('v1/building/config/details');
//            var records = $.param({building_id:buildingId});
//            $http({
//                url: request_url,
//                method: "POST",
//                data:records,
//                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
//            }).then(function(response) {
//                var result = response.data;
////                $scope.config_details = response.data.details[0];
////                $scope.details = response.data.details;
//                if(result.success){
//                    $scope.config_details = result.details[0];
//                    $scope.details = result.details;
//                    
//                }
//            }, 
//            function(response) { // optional
//                alert("fail");
//            });
//		};

        $scope.openBuilding = function(buildingId){
             $('#buildingconfig').modal();
                var request_url = generateUrl('v1/building/flats/'+buildingId);
                  $http.get(request_url)
                .success(function(result, status, headers, config) { 
                        console.log(result);
                         $scope.building_name = result.building_name;
                          $scope.building_flats = result.flats;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
          }
         /* End*/
         
          /* Building Wings Details*/
          $scope.openWing = function(buildingId){
              $('#chnge_icon_'+buildingId).toggleClass("btn btn-primary btn-xs glyphicon glyphicon-plus btn btn-primary btn-xs glyphicon glyphicon-minus")
                    $('#wingconfig_'+buildingId).slideToggle();
                    var request_url = generateUrl('v1/building/wings/'+buildingId);
                  $http.get(request_url)
                .success(function(result, status, headers, config) { 
                        $scope.wings[buildingId] = result.wings;   
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
          }
        /* End*/
        
            /* Wings Configuration Details*/
             $scope.configureWing = function(wingId){
              $('#WingConfig_detail').modal();
//                $('#wingconfig').hide();
//                var request_url = generateUrl('v1/wing/config/detail/'+wingId);
//                  $http.get(request_url)
//                .success(function(result, status, headers, config) {
//                       $scope.single_floor_wing = result.details[0];
//                       console.log();
//                       $scope.multiple_floor_wing = result.details;
//                }).error(function(data, status, headers, config) {
//                        console.log(data);
//                });
                var request_url = generateUrl('v1/wing/flats/'+wingId);
                  $http.get(request_url)
                .success(function(result, status, headers, config) { 
                        $scope.wing_name = result.wing_name;
                        $scope.wing_flats = result.flats;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
          }
         
          
          /*society Amenities Modal*/
          $scope.amenityModal = function()
          {
              $('#SocietiesAmenities').modal();
          }
          
          /*End*/
          
          /*Parking Modal*/
          $scope.ParkingModal = function(id)
          {
              $('#ParkingModal').modal();
              var request_url = generateUrl('v1/parking/'+id);
                  $http.get(request_url)
                .success(function(result, status, headers, config) { 
//                        console.log(result);
                        $scope.parking = result.parking;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
          }
          
          /*Building Amenities Modal*/
          $scope.buildingAmenityModal = function(buildingId)
          {
            $('#BuildingAmenities').modal();
            var request_url = generateUrl('v1/building/amenities/'+buildingId);
                  $http.get(request_url)
                .success(function(result, status, headers, config) { 
                        $scope.building_amenities = result.building_amenities;
                        console.log($scope.building_amenities);
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
          }
          /*End*/
          
           /*Close Wing Modal*/
            $scope.closeWing = function()
            {
//                alert();
                $('#WingConfig_detail').hide();
                $('#wingconfig').show();
                $('.modal-backdrop.in')
            }
        /*End*/
        
        /*Submit building approved form*/
            
            $('#approved-form').submit(function(e){
			e.preventDefault();

			if($('#approved-form').valid()){
                $('#loader').show();
                $('#description').val(CKEDITOR.instances.description.getData());
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Updating please wait..');
                var records = $.param($( this ).serializeArray());        
				 var request_url = generateUrl('v1/building/approved');
				$http({
					url: request_url,
					method: "POST",
					data: records,
					headers: {'Content-Type': 'application/x-www-form-urlencoded'}
				})
				.then(function(response) {
                     $('#loader').hide();
                    $("#approved-form").find('button[type=submit]').attr('disabled',false);
                    $("#approved-form").find('button[type=submit]').text('Submit');
                    var result = response.data;
                    if(result.success)
                    {
                        grit('','Updated successfully!');
                        window.location.reload();
                    }else{
                        alert("failed");
                    }  
//					window.location.reload();
				},
				function(response) { // optional
//				   alert("fail");
				});
			}
		});
            
        /*End*/
          
        // Update roles when attendee type  is changed
        $scope.$watch('inputAttendeeType', function(newValue, oldValue) {

            if(newValue == 'M'){

                if($scope.createPermissionType.length  == 1){
                    if($scope.societyRoles){
                        $scope.roles = $scope.societyRoles;
                    }

                    if($scope.buildingRoles){
                        $scope.roles = $scope.buildingRoles;
                    }
                }
                console.log($scope.inputLevelType);
                if($scope.inputLevelType == 'society' && $scope.createPermissionType.length  == 2){
                    $scope.roles = $scope.societyRoles;

                }

                if($scope.inputLevelType == 'building' && $scope.createPermissionType.length  == 2){
                    $scope.roles = $scope.buildingRoles;
                }

            }
        });

    });
</script>
<style>.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #fff;
    opacity: 1;
</style>
<div ng-controller="BuildingListCtrl" class="col-lg-12">
    <div class="row">
        <div class="col-lg-9">
            <div class="row col-1">
                <div class="col-lg-4">
                    <h4>Number of Buildings</h4>
                </div>
                <div class="col-lg-8">
                    <b class="alignment">@{{building_count}}</b>
                </div>
            </div>
                
            <div class="row col-2">
                <div class="col-lg-4">
                    <h4>Society Amenities</h4>
                </div>
                <div class="col-lg-8">
                     <button class="btn btn-primary btn-xs" title=" society amenities" ng-click="amenityModal()"><span class="glyphicon glyphicon-glass"></span></button>
                </div>
            </div>
            <div class="row col-2">
                <div class="col-lg-4">
                    <h4>Society Parking</h4>
                </div>
                <div class="col-lg-8">
                     <button class="btn btn-primary btn-xs" title="society parking" ng-click="ParkingModal({{Session::get('user.society_id')}})"><span class="glyphicon glyphicon-road"></span></button>
                </div>
            </div>
            <div class="row col-4" >
                    <div class="col-lg-3 col-md-3">
                        <p><b>Building Name</b></p>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <p><b>Amenities</b></p>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <p><b>Parking</b></p>
                    </div>
                    <div class="col-lg-3 col-md-3">
                       <p><b>Is wing exist</b></p>
                    </div>
            </div>
            <div ng-repeat="building in buildings">
                <div class="row col-4" >
                    <div class="col-lg-3 col-md-3">
                       <p> @{{building.name}}</p>
                    </div>
                    <div class="col-lg-3 col-md-3 lastChild">
                         <p><button class="btn btn-primary btn-xs" title=" building amenities" ng-click="buildingAmenityModal(building.building_id)"><span class="glyphicon glyphicon-glass"></span></button></p>
                    </div>
                    <div class="col-lg-3 col-md-3 lastChild">
                         <p><button class="btn btn-primary btn-xs" title="building parking" ng-click="ParkingModal(building.building_id)"><span class="glyphicon-road glyphicon"></span></button></p>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <input type="radio" ng-checked="building.wing_exists == 'YES'" value='YES' disabled> Yes
                        <span class="radio-wing"><input type="radio" ng-checked="building.wing_exists == 'NO'" value='NO' disabled> No</span>
                        <button class="btn btn-primary btn-xs" ng-click="openBuilding(building.building_id)" title="flat details" ng-if="(building.wing_exists == 'NO') ? 1 : 0"><span class="glyphicon glyphicon-cog"></span></button>
                        <button class="btn btn-primary btn-xs glyphicon glyphicon-plus change_icon" id="chnge_icon_@{{building.building_id}}" ng-click="openWing(building.building_id)" ng-if="(building.wing_exists == 'YES') ? 1 : 0"></button>
                    </div>
                </div>
<!--                <div class="clearfix"></div>-->
                <table id="wingconfig_@{{building.building_id}}" border="1" BORDERCOLOR="#eee" class="table table-bordered table-hover table-striped wingconfig" style="display:none;width:400px">
                    <thead>
                        <tr>
                            <th>Blocks</th>
                            <th>Amenities</th>
                            <th>Flat Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="wing in wings[building.building_id]"> <!-- ngRepeat: wing  -->
                            <td>
                                @{{wing.block}}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs" title="wing amenities" ng-click="buildingAmenityModal(wing.id)"><span class="glyphicon glyphicon-glass"></span></button>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs glyphicon glyphicon-cog" title="flat details" ng-click="configureWing(wing.id)"></button>
                            </td>
                        </tr>
                    </tbody>
                </table>  
            </div>
            <form id="approved-form" method="post" action="">
                <div class="radio-inline">
                    <label> <input type="radio" name="is_approved" ng-checked="society_config_details.is_approved=='YES'" value="YES">Approve</label>
                </div>
                <div class="radio-inline">
                    <label> <input type="radio" name="is_approved" ng-checked="society_config_details.is_approved=='NO'" value="NO">Disapprove</label>
                </div>
                <div class="form-group">
                    <label class="form-label" for="exampleInputPassword1">Notes</label>
                    <textarea id="description" class="form-control" name="notes" placeholder="Notes" value="@{{society_config_details.notes}}">@{{society_config_details.notes}}</textarea>
                </div>
                <input type="hidden" name="approved_by" value="{{Session::get('user.user_id')}}">
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
        </div>
    </div>
        
    <!--Building Configuration Modal-->
    <div id="buildingconfig" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                   <h4 class="modal-title" id="myModalLabel">@{{building_name.name}}</h4>
               </div>
                <div class="modal-body">
                    <div ng-if="(building_flats=='') ? 1 : 0"><b>No data found..</b></div>
                    <!--demo data-->
<!--                    <form class="form-horizontal" >
                    <div class="form-group">
                      <label for="input-sm" class="col-xs-6">Number of Floors</label>
                      <div class="col-xs-3">
                          <input type="text" value ="@{{config_details.no_of_floor}}" class="form-control input-sm" readonly="readonly"  />
                      </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="input-sm" class="col-xs-12">Is Flat Same on Each Floors</label>
                        <div id="checks" class="col-xs-12">
                            <label class="radio-inline"><input type="radio" ng-checked="config_details.is_flat_same_on_each_floor == 'YES'" value="YES" disabled>Yes</label>
                            <label  id="forNo" class="radio-inline"><input type="radio" ng-checked="config_details.is_flat_same_on_each_floor == 'NO'" value="NO" disabled>No</label>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group" ng-show="config_details.is_flat_same_on_each_floor == 'YES'">
                         <label for="input-sm" class="col-xs-12">No. of Flats on each floor</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control input-sm" placeholder="No. of Flats" value="@{{config_details.flat_on_each_floor}}" readonly="readonly"/>
                            </div>
                    </div>
                    <div ng-show="config_details.is_flat_same_on_each_floor == 'NO'" >
                        <div class="form-group" ng-repeat="data in details">
                             <label for="input-sm" class="col-xs-12">No. of flats on @{{data.floor_no}} floor</label>
                                <div class="col-xs-12">
                                    <input type="text" class="form-control input-sm" placeholder="No. of Flats" value="@{{data.no_of_flat}}" readonly="readonly"/>
                                </div>
                        </div>
                    </div>
                   </form>-->
                   <table ng-if="(building_flats!='') ? 1 : 0" class="table">
                    <thead>
                        <tr>
                            <th>Flat No.</th>
                            <th>Square Foot</th>
                            <th>Floor No</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ngRepeat: block in blocks --><tr ng-repeat="flats in building_flats" class="edit ng-scope" on-finish-render="ngRepeatFinished">
                            <td>
                                @{{flats.flat_no}}
                            </td>
                        <td>
                             @{{flats.square_feet_1}}
                        </td>
                         <td>
                             @{{flats.floor}}
                        </td>
                         <td>
                             @{{flats.type}}
                        </td>
                        </tr>
                    </tbody>
                    
            </table>
                </div>
            </div>
        </div>
    </div>
         <!--End-->
        <!--Wing Configuration Modal-->
    <div id="WingConfig_detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                   <button type="button" ng-click="closeWing()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                   <h4 class="modal-title" id="myModalLabel">@{{wing_name.name}} building / @{{wing_name.block}} wing</h4>
               </div>
                <div class="modal-body">
                    <div ng-if="(wing_flats=='') ? 1 : 0"><b>No data found..</b></div>
                    <!--demo data-->
<!--                    <form class="form-horizontal">
                    <div class="form-group">
                      <label for="input-sm" class="col-xs-6">Number of Floors</label>
                      <div class="col-xs-3">
                          <input type="text" value ="@{{single_floor_wing.nos_of_floors}}" class="form-control input-sm" readonly="readonly"  />
                      </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="input-sm" class="col-xs-12">Is Flat Same on Each Floors</label>
                        <div id="checks" class="col-xs-12">
                            <label class="radio-inline"><input type="radio" ng-checked="single_floor_wing.is_flat_same_on_each_floor == 'YES'" value="YES" disabled>Yes</label>
                            <label  id="forNo" class="radio-inline"><input type="radio" ng-checked="single_floor_wing.is_flat_same_on_each_floor == 'NO'" value="NO" disabled>No</label>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group" ng-show="single_floor_wing.is_flat_same_on_each_floor == 'YES'">
                         <label for="input-sm" class="col-xs-12">No. of Flats on each floor</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control input-sm" placeholder="No. of Flats" value="@{{single_floor_wing.flat_on_each_floor}}" readonly="readonly"/>
                            </div>
                    </div>
                    <div ng-show="single_floor_wing.is_flat_same_on_each_floor == 'NO'" >
                        <div class="form-group" ng-repeat="multiple_data in multiple_floor_wing">
                             <label for="input-sm" class="col-xs-12">No. of flats on @{{multiple_data.floor_no}} floor</label>
                                <div class="col-xs-12">
                                    <input type="text" class="form-control input-sm" placeholder="No. of Flats" value="@{{multiple_data.no_of_flat}}" readonly="readonly"/>
                                </div>
                        </div>
                    </div>
                   </form>-->
                   <table ng-if="(wing_flats!='') ? 1 : 0" class="table">
                    <thead>
                        <tr>
                            <th>Flat No.</th>
                            <th>Square Foot</th>
                            <th>Floor No</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ngRepeat: block in blocks --><tr ng-repeat="flats in wing_flats" class="edit ng-scope" on-finish-render="ngRepeatFinished">
                            <td>
                                @{{flats.flat_no}}
                            </td>
                        <td>
                             @{{flats.square_feet_1}}
                        </td>
                         <td>
                             @{{flats.floor}}
                        </td>
                         <td>
                             @{{flats.type}}
                        </td>
<!--                        <td>
                            <button class="btn btn-primary btn-xs" ng-click="buildingAmenityModal(wing_name.id)"><span class="glyphicon glyphicon-cog"></span></button>
                        </td>-->
                        </tr>
                    </tbody>
                    
            </table>
                </div>
            </div>
        </div>
    </div>
        
    <!---Societies amenities modal----->
    <div id="SocietiesAmenities" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Societies Amenities</h4>
                </div>
                <div class="modal-body">
                     <div ng-if="(amenities=='') ? 1 : 0"><b>No data found..</b></div>
                    <table ng-if="(amenities!='') ? 1 : 0" class="table">
                        <thead>
                            <tr>
                                <th>Amenities</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ngRepeat: -->
                            <tr ng-repeat="amenity in amenities" class="edit ng-scope" on-finish-render="ngRepeatFinished">
                                <td>
                                    @{{amenity.amenity}}
                                </td>
                                <td>
                                     @{{amenity.sub_amenities}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-----End------>
    
    <!---Building amenities modal----->
    <div id="BuildingAmenities" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Building/Wings Amenities</h4>
                </div>
                
                <div class="modal-body">
                    <div ng-if="(building_amenities=='') ? 1 : 0"><b>No data found..</b></div>
                    <table ng-if="(building_amenities!='') ? 1 : 0" class="table">
                        <thead>
                            <tr>
                                <th>Amenities</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ngRepeat: -->
                            <tr ng-repeat="building_amenity in building_amenities" class="edit ng-scope" on-finish-render="ngRepeatFinished">
                                <td>
                                    @{{building_amenity.amenity}}
                                </td>
                                <td>
                                     @{{building_amenity.sub_amenities}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-----End------>
    
    <!---Society/Building Parking modal----->
    <div id="ParkingModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Parking</h4>
                </div>
                <div class="modal-body">
                    <div ng-if="(parking=='') ? 1 : 0"><b>No data found..</b></div>
                    <table ng-if="(parking!='') ? 1 : 0" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slot Name</th>
                                <th>Category</th>
                                <th> Total slot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ngRepeat: -->
                            <tr ng-repeat="data in parking" class="edit ng-scope" on-finish-render="ngRepeatFinished">
                                <td>
                                    @{{data.name}}
                                </td>
                                <td>
                                     @{{data.slot_name_prefix}}
                                </td>
                                <td>
                                     @{{data.category_name}}
                                </td>
                                <td>
                                     @{{data.total_slot}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-----End------>
        
        <!-- pagination -->
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

<script>
    $('document').ready(function(){
        $('#approved-form').validate({
            rules :
            {
                notes : "required",
            },
        });
    });
//    CKEDITOR.replace( 'description' );
    
</script>
	@stop
