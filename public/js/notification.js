app.controller('NotificationController', function($scope, $http) {

    $scope.form = {};
    
    $scope.getNotification = function() {
        $http.get(generateUrl('v1/notification'))
             .then(function(response) {
                 if (response.data.status == 'success') {
                     $scope.notes = response.data.results.notes;
                     
                 } else {
                     //$scope.pagination.total = 0;
                     jQuery('#dataCheck').text('No Data Found.')
                 }
             });
    }

    $scope.getNotification();

    
});
