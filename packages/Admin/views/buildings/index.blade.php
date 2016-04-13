@extends('admin::layouts.admin_layout')
@section('title','Buildings & Block')@section('panel_subtitle','List')
@section('panel_title',Session::get('user.society_name'))
@section('head')

<link href="{!! asset('bower_components/select2/dist/css/select2.min.css') !!}" rel="stylesheet" type="text/css" />
<link href="{!! asset('bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('js/moment.js') !!}"></script>
<script src="{!! asset('bower_components/select2/dist/js/select2.full.min.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') !!}"></script>
<script src="{!! asset('bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect-collapsible-groups.js') !!}"></script>


<style>
    .marginSetter{
        margin-left: 0px !important;
    }
    .marginSetter>input{
        width: 90%;
    }
	.wingsLayout{
		margin-bottom: 10px;
	}
	.scrollLayout{
		display:table;
		width:100%;
		table-layout:fixed;/* even columns width , fix width of table too*/
	}
	.diffFlatModalScroll{
		overflow-y: auto;
		max-height: 250px;
	}
	body{
		padding-right: 0px !important;
	}
	
</style>
<script>
	$(document).ready(function() {
		
   $("#block-item-amenities").select2({
		 
        allowClear: true,
		dropdownParent: "#blocks_modal",
        placeholder: "Search Amenities",
        ajax: {
            url: API_URL + "v1/wings/amenities?access_token=" + ACCESS_TOKEN ,
            dataType: 'json',
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    });
	
	$('#configForm').validate({
		rules: {
			'nos_floors': {
				 required: true,
				 number : true,
               },
			'isFlatSame': {
				 required: true
            },
			'flats': {
				required: {
					depends: function(element) {
						return ($("input[type='radio'][name='isFlatSame']:checked").val() === 'YES');
					}
				},
				number:true
				
			},
			'flats[]': {
				required: {
					depends: function(element) {
						return ($("input[type='radio'][name='isFlatSame']:checked").val() === 'NO');
					}
				},
				number:true
			},
		},
		messages: {
            'nos_floors': {
                required: "Please Enter Number of Floors.",
                number: "Please Enter Valid Number."
            },
			'isFlatSame': {
                required: "Please Select type.",
            },
			'flats': {
                required: "Please Enter Number of Flats.",
                number: "Please Enter Valid Number."
            },
			 'flats[]': {
                required: "Please Enter All Flats.",
                number: "Please Enter Valid Number."
            },
            
        },
		errorPlacement: function(error, element) {
                    if (element.attr("name") == "flats[]"  ) {
                        $( "#flatsArrErr" ).html( error );
                    }
					else if (element.attr("name") == "flats"  ) {
                        $( "#flatArrErr" ).html( error );
                    }
					else if (element.attr("name") == "nos_floors"  ) {
                        $( "#nosFloorsErr" ).html( error );
                    }
					else if (element.attr("name") == "isFlatSame"  ) {
                        $( "#isFlatSameErr" ).html( error );
                    }
                }
        
	});
	

  
});
	
app.controller('ComplexController',function(URL,paginationServices,$scope,$http,$filter) {
	$scope.pagination = paginationServices.getNew(5);
    $scope.buildings;
    $scope.buildingsTotal = 0;
    $scope.building;
    $scope.block = {};
    $scope.blocks;
	
    $scope.activeIndex = null;
    $scope.isDisabled = false;
    $scope.duplicateBuilding = null;
   
	$scope.pagination.total =0;
	$scope.pagination.offset =0;
	$scope.pagination.itemsPerPage = 10;
	$scope.sort = 'society.name';
	$scope.sort_order = 'asc';
	
	// Wing Configuration :
	$scope.editableBlock;
	$scope.duplicateBlock = null;
	$scope.block.editing = false;
	$scope.duplicateUpdatedBlock = null;
	$scope.form = {};
	$scope.amenities = {};
	$scope.config = {};
	$scope.config.flats = 'undefined';
	$scope.BlocksAminities = {};
	$scope.configFloorField = false;
	$scope.diffFlatFlag = false;	
	$scope.currentFlatDialog = null;
	$scope.flatEditing = "";
	
	
	
     $('#loader').hide();
     $scope.item ;
	 $scope.getAmenities = function() {
		 var options = {society_id:$scope.building};
		 $http.get(generateUrl('v1/wings/amenities',options))
        .then(function(response) {
			$scope.amenities = response.data.results.data;	
        });
	 }
	 $scope.getAmenities();
	 
	
	 
    $scope.submitForm = function() {
        if (this.building_form.$invalid)
                return;
             $('#loader').show();
        var $this = this;
        $this.disable = true;
        $http.post(generateUrl('building/save'),$scope.building)
        .then(function(response) {
             $('#loader').hide();
            if($this.building.id == undefined)

            {
                if (response.data.response.success) {
                    $('#building_modal').modal('hide');
                    grit('',response.data.response.msg);
                    $scope.getBuildings($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,status);
                    $scope.buildings.push(response.data.response.data);
                    $scope.building = {};
                    $scope.building_form.$setPristine();
                    $scope.duplicateBuilding = null;

                 }
            } else {
                if (response.data.response.success) {
                    $('#building_modal').modal('hide');
                    grit('',response.data.response.msg);
                    $scope.getBuildings($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,status);
                    $scope.buildings[$scope.activeIndex] = response.data.response.data;
                    $scope.building = {};
                    $scope.building_form.$setPristine();
                }else {
                    $scope.duplicateBuilding = response.data.response.msg;
                }
            }
                $this.disable = false;
        });

    }

    $scope.duplicate = function() {
    $http.post(generateUrl('building/checkDuplicate'),$scope.building)
        .then(function(response) {
             if (response.data.response.success) {
                 $scope.duplicateBuilding= null
             }
            else {
            $scope.duplicateBuilding = response.data.response.msg;
            }
        });
    }

    $scope.disableButton = function() {
       $scope.isDisabled = true;
    }
   

	$scope.getBuildings = function(offset,limit,sort,sort_order,search,block,status) {
        var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order,block_id:block,status:status};
        if($scope.buildingId){
            options['building_id']=$scope.buildingId;
        }
        var request_url = generateUrl('building/list',options);
         $http.get(request_url)
        .then(function(r){
			
            $scope.buildings = r.data.response.data;
            $scope.pagination.total = r.data.response.total;
            $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
            if ($scope.pagination.total == 0 )
                $("#dataCheck").text("No Data Found.");
        }); 
    }
    
    $scope.getBuildings($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,status);
    
	$scope.$on('pagination:updated', function(event,data) {
            $scope.getBuildings($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,status);
            
        });
		
    $scope.updateBuilding = function() {
        var $this = this;
        $scope.activeIndex = this.$index;
        $('#building_modal').modal('show');
        $http.get(generateUrl('building/'+$this.building.id))
          .then(function(response) {
           $scope.building = response.data.response;
        });
    }

     $scope.close = function() {
        $scope.building = {};
        $scope.building_form.$setPristine();
        $scope.duplicateBuilding = null;
    };

    $scope.closeBlock = function() {
        $("#block_form")[0].reset();
        $scope.block_form.$setPristine();
        $scope.duplicateBlock = null;
    };

    $scope.loadBlocks = function(buildingId) {
		
		if (typeof buildingId === 'undefined')
	        $scope.building_Id = this.building.id;
		
        $('#blocks_modal').modal('show');
                $('#loader1').show();
        $http.get(generateUrl('/building/block/list/'+this.building_Id),$scope.blocks)
        .then(function(response){
            $('#loader1').hide();
            $scope.blocks = response.data.response;
        });
    }
	
	$scope.loadConfigurations = function(blockId){
		if (typeof blockId === 'undefined') {
			blockId = 0;
		}
		$scope.block.id = blockId;
		
		$('#blocks_modal').modal('hide');
		$('#configModal').modal('show');
		$('#sameFlatModal').modal('hide');
		$('#diffFlatModal').modal('hide');
		
		$(".uncheckRadio").attr('checked',false);
		$(".diffFlatsInput").val('');
		
		$scope.config.floor			= null;
		$scope.config.flats			= null;
		$scope.config.floor			= null;
		$scope.config.isFlatSame    = null;
		$scope.config.flats		    = null;
		$scope.block.block			= null;
		
		//reset error messages :
		$("#flatArrErr").html("");
		$("#flatsArrErr").html("");
		$("#nosFloorsErr").html("");
		$("#isFlatSameErr").html("");
		
		$('#loader1').show();
		var options = {blockId:blockId};
		
		$http.get(generateUrl('/wings/flats/',options))
		.then(function (response) {
			
			if (typeof response.data.response.results.block === 'object'){
				$scope.config.floor      = response.data.response.results['nos_of_floors'];
				$scope.config.isFlatSame = response.data.response.results['is_flat_same_on_each_floor'];

				var sameFlatModal = $("#sameFlatModal");
				var diffFlatModal = $("#diffFlatModal");
				
				$scope.block.block = response.data.response.results.block['block'];

				if ($scope.config.isFlatSame == 'YES'){
					$scope.currentFlatDialog = 'YES';
					$scope.flatEditing = 'YES';
					$scope.config.flats  = response.data.response.results['flat_on_each_floor'];

					if ($scope.flatEditing = 'YES'){
						var newHTML = [];
						newHTML  = '<label for="input-sm" class="col-xs-12">Please Specify Flats</label>';
						newHTML += '<div class="col-xs-12">';
						newHTML += '<input required="required" value="'+ $scope.config.flats +'" name="flats" id="flats"type="text" ng-model="config.flats" class="form-control input-sm"  placeholder="No. of Flats"/>';
						newHTML += '<div id="flatArrErr"></div></div>';
						sameFlatModal.html(newHTML);
					}

					diffFlatModal.addClass("hide")
								 .removeClass("show");
					sameFlatModal.addClass("show")
								 .removeClass("hide");	 
				}
				else if ($scope.config.isFlatSame == 'NO'){
					$scope.currentFlatDialog = 'NO';
					$scope.flatEditing = 'NO';
					var newHTML = [];
					$scope.config.flats  = response.data.response.results['block_config'];
					for(var i=1; i<=$scope.config.flats.length; i++){
						var suffix = $scope.addSuffix(i);
						newHTML.push('<div id="flatsArrErr"></div><h4>'+suffix +' Floor</h4>' +' <input required="required" id="flats" ng-model="config.flats"  name="flats[]" type="text" class="form-control input-sm diffFlatsInput" value="'+ $scope.config.flats[i-1]['no_of_flat'] +'"  placeholder="No. of Flats"/>');
					}

					$('#diffFlatModal').html(newHTML.join(''));

					sameFlatModal.addClass("hide")
								 .removeClass("show");
					diffFlatModal.addClass("show")
								 .removeClass("hide");	 
				}
			}
			else{
				$scope.block.block = response.data.response.results.block;
				$scope.currentFlatDialog = null;
			}
			$('#loader1').hide();
		});
	}

    $scope.submitBlock = function() {
        if (this.block_form.$invalid)
                return;
            $('#loader1').show();
        var $this = this;
        $this.disable = true;
        $scope.block.building_id = this.building_Id;
		var options = {block:$scope.block.block,amenitiesId:$scope.form.amenity,building_id:$scope.block.building_id};
        $http.post(generateUrl('building/block/save',options) )
        .then(function(response) {
            $('#loader1').hide();
			$("#exampleInputName2").val('');
			$("#amenity option:selected").removeAttr("selected");

             if (response.data.response.success) {
                grit('',response.data.response.msg);
                $scope.blocks.push(response.data.response.data);
                $scope.block = {};
                $scope.block_form.$setPristine();
                $scope.duplicateBlock = null;
				$this.block.editing = false;
				
             } else {
                 $scope.duplicateBlock = response.data.response.msg;
             }
              $this.disable = false;
        });
    }

    $scope.duplicateBlocks = function() {
        var $this = this;
        $http.post(generateUrl('building/block/checkDuplicate'),$scope.block)
            .then(function(response) {
                 if (response.data.response.success) {
                     $scope.duplicateBlock = null
                 }
                else {
                $scope.duplicateBlock = response.data.response.msg;
                }
            });
        }

    $scope.updateBlock = function() {
        if (this.edit_block_form.$invalid)
            return;
		
        $('#loader1').show();
         var $this = this;
        $this.disable = true;
        $scope.block.building_id = this.building_Id;
		console.log($scope.block.building_id);
		var options = {blockId:$scope.block.id,block:$scope.block.edit_block,building_id:$scope.block.building_id,amenitiesId:$scope.form.edit_amenity};
        
		$http.post(generateUrl('building/block/save',options) )
        .then(function(response) {
            $('#loader1').hide();
            if (response.data.response.success) {
                grit('',response.data.response.msg);
				$scope.loadBlocks($scope.block.building_id);
                $this.block.editing = false;
                $this.block.duplicate = null;
            } else {
                 $this.block.duplicate = response.data.response.msg;
             }
			 				

                $this.disable = false;
        });
    }

    $scope.deleteBuilding = function() {
        var r = confirm("Deleted building cannot be retrieved");
        if (r == true) {
        var $this = this;
        $http.get(generateUrl('building/delete/'+$this.building.id))
        .then(function(r){
                grit('',r.data.response.msg);
                if(r.data.response.success == true) {
                $scope.buildings= $filter('filter')($scope.buildings, function(value, index) {return value.id != $this.building.id});
            }
        });
        }  else {
            return ;
        }
    }

    $scope.editBlock = function() {
		$scope.block.id = this.block.id;
		var options = {blockId:this.block.id};
		$http.get(generateUrl('/building/block/edit',options))
        .then(function(response){
			console.log(response);
            $scope.editableBlock = response.data.response.results.data[0];
			$scope.block.edit_block = $scope.editableBlock.block; 
			
			var amenitiesArr = [];
			for (var i =0 ; i <$scope.editableBlock.amenities.length; i++){
				amenitiesArr[i] = $scope.editableBlock.amenities[i].amenity_id;
			}
			$scope.BlocksAminities = amenitiesArr;
        });
		$scope.block.editing = true;

    }
	
	$scope.getSelectedAmenities = function(value)
        {
            if($.inArray(value, $scope.BlocksAminities)!='-1'){
                return true;
            }else{
                return false;
            }
       };
	
    $scope.cancelBlock = function() {
		$scope.block.block = "";
		$scope.block.edit_block = "";
        this.block.editing = false;
        $http.get(generateUrl('/building/block/list/'+this.building_Id),$scope.blocks)
        .then(function(response){
            $scope.blocks = response.data.response;
        });
    }

    $scope.deleteBlock = function() {
        var r = confirm("Deleted building cannot be retrieved");
        if (r == true) {
        var $this = this;
        $http.get(generateUrl('/building/block/delete/'+this.block.id))
        .then(function(r){
                grit('',r.data.response.msg);
                if(r.data.response.success == true) {
                $scope.blocks= $filter('filter')($scope.blocks, function(value, index) {return value.id != $this.block.id});
            } 
        });
        }  else {
            return ;
        }
    }
	
	
	$scope.sameFlat = function() {
		var sameFlatModal = $("#sameFlatModal");
		var diffFlatModal = $("#diffFlatModal");
		
		if (typeof $scope.config.floor !== 'undefined' && $scope.config.floor !== null ){
			diffFlatModal.addClass("hide")
				         .removeClass("show")
					     .html("");
		
			sameFlatModal.addClass("show")
				     .removeClass("hide");
			         
		}
		
		
		
//		if ($scope.flatEditing !== 'YES') {
			var newHTML = [];
					newHTML  = '<label for="input-sm" class="col-xs-12">Please Specify Flats</label>';
					newHTML += '<div class="col-xs-12">';
					newHTML += '<input value="" name="flats" id="flats" type="text" ng-model="config.flats" class="form-control input-sm" required  placeholder="No. of Flats"/>';
					newHTML += '<div id="flatArrErr"></div></div>';
//					newHTML += '<div class="col-lg-10"><label id="sameFlatErr" class="error" ng-show="configForm.$submitted && configForm.flats.$invalid">Please Enter Flats.</label></div>';
					sameFlatModal.html(newHTML);
//		}
		
		if ($scope.flatEditing === 'NO')
			$scope.config.flats = "";
		
		$scope.currentFlatDialog = 'YES';	 
	}
	
	$scope.diffFlat = function() {

		var sameFlatModal = $("#sameFlatModal");
		var diffFlatModal = $("#diffFlatModal");
		
		sameFlatModal.addClass("hide")
				         .removeClass("show")
				         .html("");
		if (typeof $scope.config.floor !== 'undefined' ){
			if ($scope.config.floor	> 200)
				$scope.config.floor = $scope.config.floor.slice(0,-1);
			
			diffFlatModal.addClass("show")
						 .removeClass("hide");
		} else{
			diffFlatModal.addClass("hide").removeClass("show").html("");
		}
		if ($scope.flatEditing === 'YES')
			$scope.config.flats = null;
		
		$scope.currentFlatDialog = 'NO';
		$scope.diffFlatFlag = true ;
		
		
//		if ($scope.flatEditing !== 'NO') {
			var newHTML = [];
			var count = $scope.config.floor;

			for(var i=1;i<=count;i++){
				var suffix = $scope.addSuffix(i);
				newHTML.push('<div id="flatsArrErr"></div><h4>'+suffix +' Floor</h4>' +' <input required="required" id="flats" ng-model="config.flats"  name="flats[]" type="text" class="form-control input-sm diffFlatsInput"  placeholder="No. of Flats"/>');
			}
			$('#diffFlatModal').html(newHTML.join(''));
//		}
		
	}
		
			$scope.addSuffix = function(i) {
				var j = i % 10,
					k = i % 100;
				if (j == 1 && k != 11) {
					return i + "st";
				}
				if (j == 2 && k != 12) {
					return i + "nd";
				}
				if (j == 3 && k != 13) {
					return i + "rd";
				}
				return i + "th";
		}
	
	$scope.$watch('config.floor', function(floorCount, oldFloorCount) {
        if (floorCount > 200) {
            alert('Floors can\'t be greater than 200.');
            $scope.config.floor = oldFloorCount;
            return ;
        }

       
    });
	
	$scope.shuffleOption = function(){
		if ($scope.currentFlatDialog === 'YES')
			$scope.sameFlat();
		else if ($scope.currentFlatDialog === 'NO')
			$scope.diffFlat();
	}
	
	$scope.updateConfig = function() {
		console.log(this);
//		if (this.configForm.$invalid)
//            return;
		if (! $("#configForm").valid()){
			return false;
		}
		var formArray = $.param($("#configForm").serializeArray());
        $('#loader').show();
        var $this = this;
        $this.disable = true;
		 $http({
                url: generateUrl('/wings/flats/update'),
                method: "POST",
                data: formArray,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
        .then(function(response) {
				$('#loader').hide();
				if (response.data.response.success) {
					 grit('',response.data.response.msg);
					$scope.config = {};
					$scope.configForm.$setPristine();
					$('#blocks_modal').modal('show');
					$('#configModal').modal('hide');
					$this.disable = false;
				}
				else{
				}
				
        });

    }
	
	
	$scope.submitConfig = function() {
		console.log(this);
		if (this.configForm.$invalid)
            return;
		if(! $('#configForm').valid()){
			return false;
		}
		
		var formArray = $.param($("#configForm").serializeArray());
        $('#loader').show();
        var $this = this;
        $this.disable = true;
		 $http({
                url: generateUrl('/wings/flats/add'),
                method: "POST",
                data: formArray,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
        .then(function(response) {
				 grit('',response.data.response.msg);
				$('#loader').hide();
				
				if (response.data.response.success) {
					$scope.config = {};
					$scope.configForm.$setPristine();
					$('#blocks_modal').modal('show');
					$('#configModal').modal('hide');
					$this.disable = false;
				}
				else{
				}
				
        });

    }
	
	$scope.hideAmenitiesModal = function() {
		var amenitiesModal = $("#amenitiesModal");
		amenitiesModal.modal("hide");
//		amenitiesModal.hide();
	}
	
	 $scope.listAmenities = function(blockId) {
		var options = {blockId:blockId};
		var loadAmenities = $("#loadAmenities");
		loadAmenities.html("");
		
		$http.get(generateUrl('wings/listAmenities',options))
        .then(function(response) {
			$scope.block.amenities = response.data.response.results;	
			var p = $("#block_"+blockId); 
			var offset = p.offset();
			$("#configModal").modal("hide");
			var amenitiesModal = $("#amenitiesModal");
			amenitiesModal.modal("show")
						  .css({top : offset.top - 20, left : offset.left - 450 });
				  
				  
			
			var newHTML = [];
			console.log($scope.block.amenities);
			if ($scope.block.amenities){
				for(var i=1;i<=$scope.block.amenities.length;i++){
					newHTML.push('<b>'+$scope.block.amenities[i-1]['name']+' </b>' +' ');
				}
			}
			else{
				newHTML.push('<b>No Amenities Found.</b>' +' ');
			}
			
			loadAmenities.html(newHTML.join(','));
			
        });
	}
	
    $scope.closeModal = function() {
		$scope.flatEditing ="";
		$('#blocks_modal').modal('show');
		$('#configModal').modal('hide');
		$('#sameFlatModal').modal('hide');
		$('#diffFlatModal').modal('hide');
		var sameFlatModal = $("#sameFlatModal");
		var diffFlatModal = $("#diffFlatModal");
		
		diffFlatModal.addClass("hide")
				     .removeClass("show");
		
		sameFlatModal.addClass("hide")
				     .removeClass("show");
		
	}
	
	$(document).ready(function(e){
  $("body").find(".listAmenitiesClass").hover(function() {
      $("#amenitiesModal").modal('show');
  }, function(){
      $("#amenitiesModal").modal('hide');                            
  });
});
	
	


});
</script>
@stop

@section('content')
<div class="col-md-12" ng-controller="ComplexController">
<div class="form-group">
<button class="btn btn-primary pull-right" data-toggle="modal" data-target="#building_modal">Add Building</button>
	<div class="clearfix"></div>
</div>
<div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Building</th>
                <th>Blocks</th>
<!--                <th>No. Of Floors</th>
                <th>No. Of Flats In Each Floor</th>-->
                <th>Action</th>
            </tr>
        </thead>
    <tbody>
        <tr ng-if="pagination.total == 0">
            <td colspan="5" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
        </tr>
        <tr  ng-if="pagination.total > 0" ng-repeat="building in buildings" class="edit" on-finish-render="ngRepeatFinished">
            <td>@{{building.name}}</td>
            <td>
                <a href="javascript:void(0)" ng-click="loadBlocks()"> Edit Blocks</a>
            </td>
<!--            <td>@{{building.floors}}</td>
            <td>@{{building.flats}}</td>-->
            <td><input type="hidden" name="member_id" value="@{{building.id}}">
                <a href="" title="Edit" ng-click="updateBuilding()" class="glyphicon glyphicon-pencil"></a>
                <a href="" title="Delete" ng-click="deleteBuilding()" class="glyphicon glyphicon-remove"></a>
                <!--<a href="{{route('admin.building.acl','')}}/@{{building.id}}">Roles</a>-->
            </td>
        </tr>
    </tbody>
    </table>
	
	<div ng-if="pagination.total > 0" class="row">
		<div class="col-lg-12">
			<ul class="pagination pagination-sm"
				ng-show="(pagination.pageCount) ? 1 : 0">
				<li ng-class="pagination.prevPageDisabled()"><a href
					ng-click="pagination.prevPage()" title="Previous"><i
						class="fa fa-angle-double-left"></i> Prev</a></li>
				<li ng-repeat="n in pagination.range()"
					ng-class="{active: n == pagination.currentPage}"
					ng-click="pagination.setPage(n)"><a href>@{{n}}</a></li>
				<li ng-class="pagination.nextPageDisabled()"><a href
					ng-click="pagination.nextPage()" title="Next">Next <i
						class="fa fa-angle-double-right"></i></a></li>
			</ul>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="building_modal" tabindex="0" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" ng-click="close()"  class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@{{ building.id != null ? 'Update' : 'Add'}} Building</h4>
    </div>
    <div class="modal-body">
    
        <form ng-submit="submitForm()" id="building_form" name="building_form" class="form-horizontal"  novalidate>
            <div class="form-group marginSetter">
                <label for="name" class="form-label">Name</label>
                <!--<div class="col-sm-6">-->
                    <input ng-change="duplicate()" ng-model="building.name" type="name" class="form-control" name="name" required ng-minlength="2" placeholder="Name" >
                    <label class="error"
                        ng-show="building_form.$submitted && building_form.name.$invalid ">
                        Please enter atleast 3 characters
                    </label>
                    <label  class="error"
                        ng-show="duplicateBuilding != null">
                        @{{duplicateBuilding}}
                    </label>
                <!--</div>-->
            </div>
<!--            <div class="form-group marginSetter">
                <label for="floors" class="form-label">No. of floors</label>
                <div class="col-sm-6">
                    <input  ng-model="building.floors"type="text" class="form-control" name="floors" ng-pattern = "/^[0-9]{1,7}$/" maxlength="2" required placeholder="Floors">
                    <label class="error"
                        ng-show="building_form.$submitted && building_form.floors.$invalid" >
                        Please enter valid floors
                    </label>
                </div>
           
            <div class="form-group marginSetter">
                <label for="flats" class="form-label ">No. of flats in each floor</label>
                <div class="col-sm-6">
                    <input  ng-model="building.flats" type="text" class="form-control" name="flats" ng-pattern = "/^[0-9]{1,7}$/" maxlength="4" required placeholder="Flats">
                    <label class="error"
                        ng-show="building_form.$submitted && building_form.flats.$invalid" >
                        Please enter valid flats
                    </label>
                </div>
            </div>
            <div class="form-group marginSetter">
                <label for="blocks" class="form-label">No. of Blocks / Wings</label>
                <div class="col-sm-6">
                    <input  ng-model="building.blocks"type="text" class="form-control" name="blocks" ng-pattern = "/^[0-9]{1,7}$/" maxlength="2" required  placeholder="Blocks">
                    <label class="error"
                        ng-show="building_form.$submitted && building_form.blocks.$invalid" >
                        Please enter valid blocks
                    </label>
                </div>
            </div>-->
                <input  ng-model="building.id" type="hidden" <input type="hidden" name="building_id">
            <div class="form-group marginSetter">
                <button type="submit"   ng-disabled="disable" class="btn btn-primary">@{{ building.id != null ? 'Update' : 'Submit'}}</button>
                <button type="button" ng-click="close()"  class="btn btn-primary" data-dismiss="modal">Cancel</button>
            </div>
        </form>
            <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
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
							<!--<a href="javascript:void(0)" ng-click="loadConfigurations(block.id)" class="glyphicon glyphicon-cog" title="Configure Wing" ng-click="configureBlock()"></a>-->
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
