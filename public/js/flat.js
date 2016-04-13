

app.factory('AttachFlatService', function($rootScope, $http) {
    var service = {};
    service.flatsModified = 0;

    service.openAttachFlatModal = function () {
        jQuery('#attachFlatModal').modal('show');
    }

    service.closeAttachFlatModal = function () {
        $("#attachFlatModal form")[0].reset();
        $("#attachFlatModal label.error").remove();
        jQuery('#attachFlatModal').modal('hide');
    }

    service.getBuildings = function($scope) {
        $http.get(generateUrl('v1/buildings', {society_id: society_id}))
             .success(function(response) {
                 $scope.resultHandler(response, function() {
                     if (response.status == 'success') {
                         $scope.buildings = response.results;
                     } else {
                         grit('', response.message);
                     }
                 });
            });
    }

    service.resultHandler = function(result, successHandling, $scope) {
        if (result.status == 'success') {
            if (typeof successHandling == 'function') {
                successHandling();
            } else {
                grit('', result.message);
                $scope.closeFlatForm();
            }
        } else {
            $scope.alertBox('show', result.message);
        }
    }

    service.getFlats = function(blockId, $scope) {

        var options = {society_id: society_id};

        options.attached_flats = CROSS_MARK_URL;

        typeof blockId == 'undefined' ?
            options.building_id = $scope.form.building_id:
            options.block_id = blockId;

        $http.get(generateUrl('v1/flats', options))
             .success(function(response) {
                 $scope.flats = service.flats = response.results;
             });
    }

    service.getUsers = function($scope) {
        if (typeof service.users !== 'undefined') {
            return service.users;
        }

        var options = {
            society_id  : society_id,
            pagination  : false,
            relations   : false,
            concat      : 'name,email'
        };

        $http.get(generateUrl('v1/users', options))
             .success(function(response) {
                 $scope.users = service.users = response.results;
             });
    }

    service.getBlocks = function(buildingId, callback) {
        $http.get(generateUrl('building/block/list/' + buildingId))
	         .then(callback);
    }

    return service;
});

app.controller('FlatController', function($scope, $http, AttachFlatService, paginationServices) {

    $scope.form = {};
    $scope.currentFlat = 0;
    AttachFlatService.getBuildings($scope);

    $scope.openAttachFlatModal = function() {
        return AttachFlatService.openAttachFlatModal($scope);
    }

    $scope.resultHandler = function(result, successHandling) {
        AttachFlatService.resultHandler(result, successHandling, $scope);
    }

    $scope.getBlocks = function(buildingId, blockId) {
        AttachFlatService.getBlocks(buildingId, function(response) {
            var blockSelect = jQuery('#select-block_id');
            if(response.data.response.length > 0) {
                blockSelect.addClass('form-label');
                jQuery('#add_flat-form [name=block_id]').rules("add", {required:true});
            } else {
                blockSelect.removeClass('form-label');
                jQuery('#add_flat-form [name=block_id]').rules("add", {required:false});
            }

            $scope.blocks = response.data.response;
            if (typeof blockId !== 'undefined') {
                $scope.form.block_id = blockId;
            }
        });
    }

    $scope.getFlats = function(page) {
        if (typeof page == 'undefined') {page = 1;}
        var options = {
            'page': page,
            society_id: society_id,
            select2: CROSS_MARK_URL
        };

        $http.get(generateUrl('v1/flats', options))
             .then(function(response) {
                 if (response.data.status == 'success') {
                     $scope.flats = response.data.results.data;
                     $scope.pagination.total = response.data.results.total;
                     $scope.pagination.pageCount = response.data.results.last_page;
                 } else {
                     //$scope.pagination.total = 0;
                     jQuery('#dataCheck').text('No Data Found.')
                 }
             });
    }

    $scope.getFlats(1);

    $scope.openFlatModal = function() {
        jQuery('#AddFlatModal input[type=submit]').val('Submit');
        $('#AddFlatModal').modal();
    };

    $scope.editFlatModal = function(flatId) {
        jQuery('#AddFlatModal input[type=submit]').val('Update');
        jQuery('#myModalLabel').text('Edit Flat');
        $scope.currentFlat = flatId;

        var flat = jQuery.grep($scope.flats, function(flat) {
            if (flat.id == flatId) {
                return flat;
            }
        })[0];

        $scope.form = flat;
        $scope.form.building_id = flat.user_society.building.id;
        $scope.getBlocks($scope.form.building_id, flat.user_society.block_id);
        $scope.form.relation = flat.user_society.relation;

        $('#AddFlatModal').modal();
    }

    $scope.updateFlatMadal = function() {
        $http.post(generateUrl('v1/flat/' + $scope.currentFlat), $scope.form)
             .then(function(response) {
                 $scope.resultHandler(response.data);
                 $scope.getFlats(1);
             });
    }

    $scope.deleteFlatModal = function(flat) {
        bootbox.confirm('Are you sure you want to delete flat number ' + flat.flat_no + '?', function(status) {
            if (! status) {
                return true;
            }

            $http.post(generateUrl('v1/flat/' + flat.id + '/delete'))
                 .then(function(response) {
                     $scope.resultHandler(response.data);
                     location.reload();
                     $scope.getFlats(1);
                 });
        });

    }

    $scope.closeFlatForm = function() {
        $scope.alertBox('hide');
        $scope.form = {};
        $scope.currentFlat = 0;
        $("#add_flat-form")[0].reset();
        $("#add_flat-form label.error").remove();
        $('#AddFlatModal').modal('hide');
        jQuery('#myModalLabel').text('Add Flat');
    };

    $scope.addFlat = function() {
        if ($scope.currentFlat !== 0) {
            return $scope.updateFlatMadal();
        }

        if (! jQuery('#add_flat-form').valid()) {return ;}

        $scope.form.society_id = society_id;
        $http.post(generateUrl('v1/flat'), $scope.form)
             .then(function(response) {
                 var result = response.data;

                 if(result.status == 'success') {
                     grit('', result.message);
                     $scope.getFlats(1);
                     $scope.closeFlatForm();
                 } else {
                     $scope.alertBox('show', result.message);
                 }
             });
    }

     // Pagination service
    $scope.pagination = paginationServices.getNew(10);

    $scope.$on('pagination:updated', function(event, data) {
        $scope.getFlats($scope.pagination.currentPage);
    });

    $scope.alertBox = function (status, text) {
        var box = jQuery('.alert.alert-warning').text(text);

        if (status == 'show') {
           box.removeClass('hide');
        } else {
           box.addClass('hide');
        }
    }
});


app.controller('AttachFlatController', function($scope, $http, AttachFlatService) {

    $scope.form = {};
    AttachFlatService.getUsers($scope);
    AttachFlatService.getBuildings($scope);

    $scope.attachFlat = function() {
        if (! jQuery('#attachFlatModalForm').valid()) { return false; }        
        $scope.form.society_id = society_id;
        $http.post(generateUrl('v1/flat/attach-user', $scope.form))
             .success(function(response) {
                 if (response.code == '200') {
                     grit('', response.message);
                     $scope.form = {};
                     $scope.closeAttachFlatModal();
                     window.location = window.location.href;
                 } else {
                     alertBox('show', response.message);
                 }
             });
    }

    $scope.closeAttachFlatModal = function() {
        $scope.form = {};
        alertBox('hide');
        return AttachFlatService.closeAttachFlatModal();
    }

    $scope.getBlocks = function(buildingId, blockId) {
        AttachFlatService.getBlocks(buildingId, function(response) {
           var blockSelect = $('#attachFlatModal [for=block_id]');
           $scope.blocks = response.data.response;

           if(response.data.response.length > 0) {
               blockSelect.addClass('form-label');
               jQuery('#attachFlatModal [name=block_id]').rules("add", {required:true});
           } else {
               blockSelect.removeClass('form-label');
               jQuery('#attachFlatModal [name=block_id]').rules("add", {required:false});
               $scope.getFlats();
           }
           if (typeof blockId !== 'undefined') {
               $scope.form.block_id = blockId;
           }
       });
    }

    $scope.getFlats = function(blockId) {
        AttachFlatService.getFlats(blockId, $scope);
    }

    $scope.resultHandler = function(result, successHandling) {
        AttachFlatService.resultHandler(result, successHandling, $scope);
    }

});
