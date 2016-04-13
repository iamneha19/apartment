var buildingIds = {} ;
function initSelect2(elmId, data) {
    return jQuery(elmId).select2({
        placeholder: "Search Amenities",
        data: data || [],
        ajax: {
            url: API_URL + "v1/wings/amenities?access_token=" + ACCESS_TOKEN + '&paginate=no',
            dataType: 'json',
            cache: true
        }
    });
};



var removeBuildingWrapper = function () {
    jQuery('#building-form-wrapper .close-building').click(function() {
        jQuery(this).parent().remove();
    });
};

removeBuildingWrapper();

app.controller("ConfigurationCtrl", function(URL,paginationServices,$scope,$http,$filter) {
    $scope.form = {
        building_count: 1,
        building_ids: [],
        building_names: [],
        building_has_wing: [],
        building_amenities: [],
        amenities: [],
        
    };

    
    $scope.blocks = {};

   
    $scope.building_id = 477 ; 
//    sending dummy building id,contact mudasir for building_id
    
    $scope.buildingConfig = function() {        
        $('#buildingconfig').modal('show');       
        }
    


    // Temporary data storage
    $scope.temp = {};

    $scope.response = {};
    
    $scope.loadConfig = function(callback) {
        return $http.get(generateUrl('v1/society/config', {
            society_id: society_id
        })).then(function(response) {
            $scope.mapToScope(response.data);
            $scope.mapBuildingAmenities();
            if (typeof callback == 'function') {
                return callback();
            }
        });
    };

    $scope.loadConfig();

    $scope.mapToScope = function(response) {
        var result = response.results;
        var tags = [];
        
        resetForm();

        $scope.form.building_count = result.building_count;
        $scope.amenity_tags = result.amenities;

        jQuery(result.buildings).each(function(index, building) {
            $scope.form.building_ids.push(building.id);
            $scope.form.building_names.push(building.name);
            $scope.form.building_has_wing.push(building.wing_exists);
            $scope.form.building_amenities.push(building.amenities);
            
            buildingIds = $scope.form.building_ids;
        });
    }

    $scope.mapBuildingAmenities = function() {
        jQuery('[name*=building_amenities]').each(function(index, elm) {
            jQuery($scope.form.building_amenities[index]).each(function(index1, amenity) {
                jQuery(elm).find('option').each(function(index2, option) {
                    if (jQuery(option).val() == amenity.id) {
                        jQuery(option).prop('selected', true);
                    }
                });
            });
        });
    };

    $scope.getAmenities = function(callback) {
        if (typeof $scope.amenities != 'undefined') {
            if (typeof callback == 'function') {
                return callback($scope.amenities);
            }
            return $scope.amenities;
        }

        return $http.get(generateUrl("v1/wings/amenities", {
            paginate: 'no'
        }))
        .then(function(response) {
            $scope.amenities = response.data.results;

            if (typeof callback == 'function') {
                return callback($scope.amenities);
            } else {
                return $scope.amenities;
            }
        });
    }

    $scope.buildAmenities = function() {
        $scope.getAmenities(function(amenities) {
            jQuery(amenities).each(function(index, amenity) {
                jQuery('[name*=building_amenities]')
                .append('<option value="'+amenity.id+'">'+amenity.text+'</option>');
            });
        });
    }();

    $scope.buildingConfig = function() {
        $('#buildingconfig').modal('show');
    }

    $scope.$watch('form.building_count', function(building_count, old_building_count) {
        $scope.temp.building_count = building_count;
        $scope.temp.old_building_count = old_building_count;
    });

    $scope.setupSocietyConfig = function() {
        var dummyForm = jQuery('#dummy-building-form');
        var buildingWrapper = jQuery('#building-form-wrapper');

        // Remove all building form which was created previously
        jQuery('.building-form').remove();

        for (var i = 1; i <= $scope.temp.building_count; i++) {
            var form = dummyForm
                        .clone()
                        .removeAttr('id')
                        .removeClass('hide')
                        .attr({
                            'id': 'building-form-' + i,
                            'class': 'row col-3 building-form'
                        });

            form.find('.building_names').last().attr({
                'id': 'building_name_' + i,
                'name': 'building_name_' + i
            })
            .val($scope.form.building_names[i - 1]);

            form.find('.has_wing').slice(-2).attr({
                'name': 'has_wing_' + i
            })
            .each(function(index, elm) {
                status = $scope.form.building_has_wing[i - 1];
                if (status == 'YES' && jQuery(elm).val() == 1) {
                    jQuery(elm).attr('checked', 'checked');
                }
                if (status == 'NO' && jQuery(elm).val() == 0) {
                    jQuery(elm).attr('checked', 'checked');
                }
            });
            
            var id = 'addWingWithBuilding_' + i;
            form.find('.wingClass').last().attr({
                id: id,
                name: id,
                'ng-model': id
            });
            
            var id = 'building_amenities_' + i;
            form.find('.building_amenities').last().attr({
                id: id,
                name: id,
                'ng-model': id
            });

            buildingWrapper.append(form);

            removeBuildingWrapper();

            jQuery("[name=building_name_" + i + "]").rules('add', 'required');
            // jQuery("[name=has_wing_" + i + "]").rules('add', {
            //     'required': true
            // });
            // jQuery("[name=" + id + "]").rules('add', "required");
        }
    }
    
        
    $scope.saveBuilding = function() {
        if ($scope.temp.building_count > 99) {
            alert('Building can\'t be greater then 99.');
            $scope.form.building_count = $scope.temp.old_building_count;
            return ;
        }

        $http.post(generateUrl('v1/society/dummy/building/' + $scope.form.building_count, {
            society_id: society_id
        }))
        .then(function(response) {
            console.log('Building count saved, Loading Config.');
            $scope.loadConfig($scope.setupSocietyConfig);
        });
    }

    $scope.saveConfig = function() {
        if (! jQuery('#society-config-form').valid()) {
            return ;
        }
        // jQuery('#society-config-form [name*=society_amenities] option:selected').each(function(index, elm) {
        //     $scope.form.amenities.push(jQuery(elm).val());
        // });

        jQuery('#society-config-form input[name*=building_name]').each(function(index, elm) {
            $scope.form.building_names.push(jQuery(elm).val());
        });

        jQuery('#society-config-form input[name*=has_wing]:checked').each(function(index, elm) {
            $scope.form.building_has_wing.push(jQuery(elm).val() == '1' ? 'YES': 'NO');
        });

        var tempAmenities = [];
        jQuery('#society-config-form [name*=building_amenities] option:selected').each(function(index, elm) {
             tempAmenities.push(jQuery(elm).val());
        });

        $scope.form.building_amenities.push(tempAmenities);

        jQuery($scope.amenity_tags).each(function(index, elm) {
            $scope.form.amenities.push(elm.id);
        });

        $http.post(generateUrl('v1/society/config', {society_id: society_id}), $scope.form)
            .then(function(response) {
                grit('', response.data.message);
            });
    }

    var resetForm = function() {
        $scope.form.building_names = [];
        $scope.form.building_has_wing = [];
        $scope.form.building_amenities = [];
        $scope.form.amenities = [];
        $scope.form.building_ids = [];
    }

    var floors = $scope.floors;
    
    $scope.buildingConfig = function() {
        $('#buildingconfig').modal('show');
        console.log(this.building_id);        
         $http.get(generateUrl('v1/config/edit',{building_id:this.building_id}))
        .then(function(response) {
            var flats = response.data.results.is_flat_same_on_each_floor;    
            if(flats == "YES") {  
                $scope.floor = response.data.results;
                 
                
            } else {
               $('#fields').hide();
                var newHTML = [];
                console.log(response.data.results);
                var data = response.data.results; 
                angular.forEach(data, function(value, key){
                    console.log(key);
                    $scope.floor = value;
                    $scope.flats = value.no_of_flat;
                    var count = value.no_of_floor;
                    newHTML.push((key+1)+ ' Floor'+ '<input  name="flat1[]" value="'+$scope.flats+'" type="text" class="form-control input-sm"  placeholder="No. of Flats" required/><br>');             
                    });
                $('#sandbox').html(newHTML.join(''));  
                }
            });
            
        }


    $scope.yes = function() {
      $('#sandbox').hide();
      $('#fields').show();

    }

    $scope.floorsInput = function() {
       $('#sandbox').hide();
       $('#fields').show();
       $("input:radio").removeAttr("checked");
    }

    $scope.no = function() {
       console.log(this);
       var $this = this;
       $('#sandbox').show();
       $scope.floor.flat_on_each_floor = '';
       $('#fields').hide();
            var count = $this.floor.no_of_floor;
            var newHTML = [];
             for(var i=1;i<=count;i++){
               newHTML.push(i +' floor' +' <input id="flats"  name="flats[]" type="text" class="form-control input-sm"  placeholder="No. of Flats" ng-model=required/><br>');
           }
             $('#sandbox').html(newHTML.join(''));

          
       }
    
    $scope.submitForm = function() {   
        if (this.buildingForm.$invalid)
            return;        
        var count = this.floor.no_of_floor;
        var flats = [];
        $("input[name^='flats']").each(function(index, element) {
            console.log(element);
            var i = index+1;
            flats.push($(this).val());
        });
        console.log(flats);
        $scope.floor.flats = flats
        $http.post(generateUrl('v1/config/save'),$scope.floor)
        .then(function(response) {
            $('#buildingconfig').modal('hide');
            alert('building saved');
            
        });
        
    }
    
    $scope.closeModal = function() {
        $scope.floor = {} ;
        $scope.buildingForm.$setPristine();
    }

    // Wing configuration :
    // Wing Configuration :
	$scope.editableBlock;
	$scope.duplicateBlock = null;
        $scope.block = {};
	$scope.block.editing = false;
	$scope.duplicateUpdatedBlock = null;
	
	$scope.amenities = {};
	$scope.config = {};
	$scope.config.flats = 'undefined';
	$scope.BlocksAminities = {};
	$scope.configFloorField = false;
	$scope.diffFlatFlag = false;	
	$scope.currentFlatDialog = null;
	$scope.flatEditing = "";
    
    $scope.loadWing = function(id) {
        if (angular.isUndefined(id))
            $scope.buildingId = $scope.block.building_id;
        else
            $scope.buildingId = $scope.form.building_ids[--id];
        
        $('#blocks_modal').modal('show');
        $('#loader1').show();
        
        $http.get(generateUrl('/building/block/list/'+$scope.buildingId),$scope.blocks)
        .then(function(response){
            $('#loader1').hide();
            $scope.blocks = response.data.response;
        });
        $scope.getAmenitiesForBlock();
    }
    
    // fetch aminites :
    $scope.getAmenitiesForBlock = function() {
	var options = {society_id:$scope.buildingId};
	$http.get(generateUrl('v1/wings/amenities',options))
        .then(function(response) {
			$scope.amenities = response.data.results.data;	
        });
    }
    
    // check duplicate block :
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
    
    // save block :
    $scope.submitBlock = function() {
        if (this.block_form.$invalid)
                return;
            $('#loader1').show();
        var $this = this;
        $this.disable = true;
        $scope.block.building_id = $scope.buildingId;
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
    
    // fetch block info for edit :
    $scope.editBlock = function() {
	$scope.block.id = this.block.id;
	var options = {blockId:this.block.id};
	
        $http.get(generateUrl('/building/block/edit',options))
        .then(function(response){
            console.log(response.data.response.results.data[0]);
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
    
    // set amenities selected : 
    $scope.getSelectedAmenities = function(value) {
        if($.inArray(value, $scope.BlocksAminities)!='-1'){
            return true;
        } else {
            return false;
        }
    };
    
    // update block :
    $scope.updateBlock = function() {
        if (this.edit_block_form.$invalid)
            return;
		
        $('#loader1').show();
         var $this = this;
        $this.disable = true;
        $scope.block.building_id = $scope.buildingId;
	console.log($scope.block.building_id);
	var options = {blockId:$scope.block.id,block:$scope.block.edit_block,building_id:$scope.block.building_id,amenitiesId:$scope.form.edit_amenity};
        
	$http.post(generateUrl('building/block/save',options) )
        .then(function(response) {
            $('#loader1').hide();
        
            if (response.data.response.success) {
                grit('',response.data.response.msg);
		$scope.loadWing();
                $this.block.editing = false;
                $this.block.duplicate = null;
            } else {
                $this.block.duplicate = response.data.response.msg;
            }
            
            $this.disable = false;
        });
    }
    
    // delete block :
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
        } else {
            return ;
        }
    }
    
    // cancel update :
    $scope.cancelBlock = function() {
        $scope.block.block = "";
	$scope.block.edit_block = "";
        this.block.editing = false;
        
        $http.get(generateUrl('/building/block/list/'+$scope.block.building_Id),$scope.blocks)
        .then(function(response){
            console.log(response.data.response); return;
            $scope.blocks = response.data.response;
        });
    }

});
function mapBuildingId(id) {
    var idArr = id.split("_");
        angular.element(document.getElementById('configurationDiv')).scope().loadWing(idArr[1]);

        
}

