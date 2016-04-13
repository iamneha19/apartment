@section('title', 'User Flat')
@section('panel_title', 'User Flat')
@section('head')
@section('content')

    <script type="text/javascript">
        app.controller("FlatCtrl", function($scope,$http,$filter) {
            $scope.buildings;
            $scope.blocks;
            $scope.flat;
            $scope.getBuildings = function() {

                var request_url = generateUrl('building/list');
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.buildings = result.response;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getBlocks = function(buildingId) {
//                var request_url = generateUrl('building/block/list/'+buildingId);
//                $http.get(request_url)
//                .success(function(result, status, headers, config) {
//                    $scope.blocks = result.response;
//                }).error(function(data, status, headers, config) {
//                    console.log(data);
//                });
                    console.log(this);
			$http.get(generateUrl('building/block/list/'+buildingId))
			.then(function(r){
				var blockSelect = $('#select-block_id');
        
                if(r.data.response.length > 0) {
					jQuery(blockSelect.prev('label')[0]).addClass('form-label');
					blockSelect.rules("add", {required:true});
					
                } else {
                	jQuery(blockSelect.prev('label')[0]).removeClass('form-label');
                	blockSelect.rules("add", {required:false});
                }

				$scope.blocks = r.data.response;
				
			});
            };


            $scope.getBuildings();

            $scope.change = function(){
                $scope.getBlocks($scope.buildingSelected);
            };

            $scope.getFlat = function(flatID) {
               var request_url = generateUrl('user/flat/'+flatID);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.flat = result.response.data;
                    $scope.getBlocks($scope.flat.building_id);
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getFlat({{$id}});

            $("#flat-form").validate({
                rules: {
                  // simple rule, converted to {required:true}
                  building_id:"required",
                  flat_no: "required",
                  relation: "required",
//                  flat_no : {
//                        required:true,
//                        number: true,
//                       remote:{
//                           url: generateUrl('society/checkflat'),
//                           type: "post",
//                           dataType:"json",
//                           data: {
//                             flat_no: function() {
//                               return $( "#input-flat_no" ).val();
//                             },
//                             block_id: function() {
//                               return $( "#select-block_id" ).val();
//                             }
//                           },
//                           success:function(r) {
//                               var result = r.response;
//                               $( "label#input-flat_no-error" ).remove();
//                                if(result.success){
//                                    $( "label#input-flat_no-error" ).remove();
//                                    return true;
//                                }else{
//                                    $( "label#input-flat_no-error" ).remove();
//                                    $( "#input-flat_no" ).after( '<label id="input-flat_no-error" class="error" for="input-flat_no">'+result.msg+'</label>' );
//                                    return false;
//                                }
//                            }
//                         }
//                    } 
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "relation"  ) {
                        $( ".form-group.type-radio-group" ).append( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
            });

            $('#flat-form').submit(function(e){
                e.preventDefault();
                
                if ($(this).valid()){
                    var records = $.param($( this ).serializeArray());
                    console.log(records);
                    var request_url = generateUrl('user/flat/update/'+{{$id}});
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(r) {
                      var result = r.data.response; // to get api result
                        $( "label#input-flat_no-error" ).remove();
                        if(result.success){
                           grit('',result.msg);
                           window.location='<?php echo route('admin.user.edit','') ?>/'+$scope.flat.user_id;

                        } if(result.flat_error){
                          $( "#input-flat_no" ).after( '<label id="input-flat_no-error" class="error" for="input-flat_no">'+result.flat_error+'</label>' );
                        }else{
                            console.log('returned false');
                        }


                    },
                    function(response) { // optional
//                           alert("fail");
                    });
                }
            });
         });
    </script>
    <div class="col-lg-12" ng-controller="FlatCtrl" >
        <form id="flat-form" method="post" action="">

            <div class="form-group">
                <label class="form-label" >Building</label>
                <select ng-model="buildingSelected" ng-change="change()" name="building_id" class="form-control">
                    <option value="" disabled="" selected="">Select Building</option>
                    <option ng-repeat="building in buildings" ng-selected="(building.id == flat.building_id) ? 1 : 0" value='@{{building.id}}' >@{{building.name}}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Block</label>
                <select name="block_id" class="form-control" id='select-block_id'>
                    <option value="" disabled="" selected="">Select Block</option>
                    <option ng-repeat="block in blocks" ng-selected="(block.id == flat.block_id) ? 1 : 0" value='@{{block.id}}' >@{{block.block}}</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" >Flat No./ Shop No./ Office No.</label>
                <input type="text" class="form-control" id='input-flat_no' name="flat_no" value="@{{ flat.flat_no }}" maxlength="4"  placeholder="My Flat No./ Shop No./ Office No">
                <input type="hidden" name="flat_id" value="@{{ flat.flat_id }}" />
            </div>
            <div class="form-group type-radio-group">
                <label class="form-label">Type</label>
                <div class="form-group">
                    <div class="radio-inline">
                        <label>
                            <input type="radio" name="type" ng-checked="(flat.type == 'office') ? 1 : 0 "   value="office" >
                            Office
                        </label>
                    </div>
                    <div class="radio-inline">
                        <label>
                            <input type="radio" name="type" ng-checked="(flat.type == 'shop') ? 1 : 0 "   value="shop" >
                            Shop
                        </label>
                    </div>
                     <div class="radio-inline">
                        <label>
                            <input type="radio" name="type" ng-checked="(flat.type == 'flat') ? 1 : 0 "   value="flat" >
                            Flat
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group type-radio-group">
                <label class="form-label">Occupancy</label>
                <div class="form-group">
                    <div class="radio-inline">
                        <label>
                            <input type="radio" name="relation" ng-checked="(flat.relation == 'owner') ? 1 : 0 "   value="owner" >
                            Owner
                        </label>
                    </div>
                    <div class="radio-inline">
                        <label>
                            <input type="radio" name="relation" ng-checked="(flat.relation == 'tenant') ? 1 : 0 "   value="tenant" >
                            Tenant
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?php echo route('admin.user.edit','');  ?>/@{{flat.user_id}}" class="btn btn-primary">Cancel</a>

        </form>


    </div>
    <script>
        $('document').ready(function(){

        });
    </script>
@stop