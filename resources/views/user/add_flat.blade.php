@section('title', 'Members')
@section('panel_title', 'My Flat')
@section('head')
@section('content')
    <script type="text/javascript">
        app.controller("FlatCtrl", function($scope,$http,$filter) {
            $scope.buildings;
            $scope.blocks;
            
            $scope.getFlats = function() {
                var request_url = generateUrl('flat/list');
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.flats = result.response.data;
                   
                   if($scope.flats.length){
                      
                        var society_id= $("#my-societies option:selected").val();

                        $.ajax({
                            url: API_URL+'society/switch',
                            method: "POST",
                            data: {access_token:ACCESS_TOKEN,society_id:society_id}
                        })
                        .success(function(result) {
                             var data = result.response; // to get api result
                                if(data.success){
                                    $.ajax({
                                        url: '<?php echo route('switch') ?>',
                                        method: "POST",
                                        dataType:"json",
                                        data: {user:data.user,acl:data.acl}
                                    })
                                    .success(function(data) {
                                        if(data.success){
                                            location.reload();
                                        }else{
                                            console.log('Sessin could not saved');
                                        }

                                    }).error(function(response){
                                        console.log('Store session error');
                                    });
                                }else{
                                    console.log('switch error');
                                }
                            }).error(function(response){
                                console.log('Society switch error');
                            });
                        
//                       setTimeout(function(){ window.location = '{{route("logout")}}'; }, 2000);
                       
                   }
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            $scope.getFlats();
            
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
                var request_url = generateUrl('building/block/list/'+buildingId);
                $http.get(request_url)
                .success(function(result, status, headers, config) {

                    $scope.blocks = result.response;

                    var blockSelect = jQuery("#block_id");

                    if($scope.blocks.length > 0) {
						jQuery(blockSelect.prev('label')[0]).addClass('form-label');
						blockSelect.rules("add", {required:true});

                    } else {
                    	jQuery(blockSelect.prev('label')[0]).removeClass('form-label');
                    	blockSelect.rules("add", {required:false});
                    }

                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };


            $scope.getBuildings();

            $scope.change = function(){
                $scope.getBlocks($scope.buildingSelected);
            };

            $("#flat-form").validate({
                rules: {
                  // simple rule, converted to {required:true}
                  building_id:"required",
                  block_id : {
                      required:false,
                      remote: {
							url:API_URL+"society/flat/check_occupancy",
							type:"post",
							data:{
								building_id: function(){
									return jQuery('#building_id').val();
								},
								block_id: function() {
									return jQuery('#block_id').val();
								},
								flat_no: function(){
									return jQuery("#flat_no").val();
								},
								relation: function(){
									return jQuery('input[name=relation]').val();
								}
							}
                      }
                  },
                  flat_no: "required",
                  relation: "required",
                  type:'required'
                },
                 errorPlacement: function(error, element) {
                    if (element.attr("name") == "type"  ) {
                        $( ".visiblity_error" ).html( error );
                    }else if (element.attr("name") == "relation"  ) {
                        $( ".visiblity_role_error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
 	         });

            $('#flat-form').submit(function(e){
                e.preventDefault();

                if ($(this).valid()){
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('flat/admin/add');
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(r) {
                      var result = r.data.response; // to get api result
                        if(result.success){
                           grit('',result.msg);
                           $.ajax({
                                url: '<?php echo route('updateflat') ?>',
                                method: "POST",
                                dataType:"json",
                                data: {flat_id:result.data.flat_id}
                            })
                            .success(function(data) {
                                if(data.success){
                                    location.reload();
                                }else{
                                    console.log('Sessin could not saved');
                                }

                            }).error(function(response){
                                console.log('Store session error');
                            });

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
                <label class="form-label" >My Building</label>
                <select ng-model="buildingSelected" ng-change="change()" name="building_id" class="form-control" id="building_id">
                    <option value="" disabled="" selected="">Select Building </option>
                    <option ng-repeat="building in buildings" value='@{{building.id}}' >@{{building.name}}</option>
                </select>
            </div>
            <div class="form-group">
                <label>My Block</label>
                <select name="block_id" class="form-control" id="block_id">
                    <option value="" disabled="" selected="">Select Block</option>
                    <option ng-repeat="block in blocks" value='@{{block.id}}' >@{{block.block}}</option>
                </select>
            </div>

            <div class="form-group type-radio-group">
                <label class="form-label">Occupancy</label>
                <div class="form-group ">
                        <div class="radio-inline">
                                <label> <input type="radio" name="relation" value="owner"> Owner
                                </label>
                        </div>
                        <div class="radio-inline">
                                <label> <input type="radio" name="relation" value="tenant"> Tenant
                                </label>
                        </div>
                </div>
                <div class="visiblity_role_error"></div>
            </div>

            <div class="form-group">
                <label class="form-label" >Flat No./ Shop No./ Office No</label>
                <input type="text" class="form-control" name="flat_no" id="flat_no" maxlength="4"  placeholder="My  Flat No./ Shop No./ Office No">
            </div>
            <div class="form-group type-radio-group">
                <label class="form-label">Flat type</label>
                <div class="form-group ">
                        <div class="radio-inline">
                                <label> <input type="radio" name="type" value="office"> Office
                                </label>
                        </div>
                        <div class="radio-inline">
                                <label> <input type="radio" name="type" value="shop"> Shop
                                </label>
                        </div>
                    <div class="radio-inline">
                                <label> <input type="radio" name="type" value="flat"> Flat
                                </label>
                    </div>
                </div>
                <div class="visiblity_error"></div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('conversations')}}" class="btn btn-primary">Cancel</a>
        </form>


    </div>
@stop
