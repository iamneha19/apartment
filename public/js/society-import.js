
var app = angular.module("sahkari", ['ngTagsInput', 'ngFileUpload', 'ngProgress'])
                 .constant('URL',API_URL)
                 .constant('ACCESS_TOKEN',ACCESS_TOKEN);

// make pagination service due to app is overwrite.
makePaginationService();

app.controller("SocietyImportController", function(URL, paginationServices, $scope, $http, Upload, ngProgressFactory) {
$('#we').hide();
 $('#start-import').hide();
 $('#start-import1').hide();

    $scope.progressbar = ngProgressFactory.createInstance();

    $scope.loadConfig = function(callback) {
        return $http.get(generateUrl('v1/society/config', {
            society_id: society_id
        })).then(function(response) {
            if(response.data.results.notes) {
               $('#we').show();
           }
            $scope.amenity_tags = response.data.results.amenities;
            var newHTML = [];          
            newHTML.push(response.data.results.notes);
            $('#v1').html(newHTML.join(''));
            if (typeof callback == 'function') {
                return callback();
            }
        });
    };

    $scope.loadConfig();

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
    
    $scope.role = function() {
         $http.get(generateUrl('v1/role'))
        .then(function(r){           
          if(r.data.success == true) {             
              $('#start-import').show();
                $('#start-import1').hide();
          } else {
              $('#start-import').hide();
              $('#start-import1').show();
          }
        });
    }
    
    $scope.role();
    
    $scope.import = function() {
        $scope.progressbar.start();
        Upload.upload({
            url: generateUrl('v1/import/society/config', {
                'society_id': society_id
            }),
            data: {
                'config_file': $scope.importFile,
                'society_amenities': $scope.amenity_tags
            },
            method: 'POST'
        }).then(function(response) {
            $scope.progressbar.complete();            
            if(response.data.code == 401) {
                alertBox('show', response.data.message);
            }
            if (response.data.code == 200) {
                alertBox('show', response.data.message);
               $scope.importFile = null ;
               
            }
            else  {
                $scope.errorBag = response.data.results;
                alertBox('show');
                // grit('', response.data.message, 'orange');
            
            }
        });
    }
});
