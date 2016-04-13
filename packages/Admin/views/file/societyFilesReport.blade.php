@section('title', 'Society Flies')
@section('panel_title', 'Society Document')
@section('panel_subtitle', 'Report')
@section('head')
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
<script>
 app.controller('reportController',function($scope,paginationServices,$http,$filter) {
     
    $scope.activeType = 'Society Document';
    $scope.itemsPerPage = 5;
    $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
        
     $scope.getMad = function(offset,limit) {  
     var options = {offset:offset,limit:limit};
     $http.get(generateUrl('admin_file/mandatoryFile/'+$scope.activeType,options))
     .then(function(r){
            $scope.files = r.data.response.data; 
            $scope.pagination.total =  r.data.response.total;
            if($scope.pagination.total == 0)
            {
                $('#fetch').text('No Data Found.'); 
            }else {
                 $('#fetch').hide();
            }
            var total =   Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
            console.log(r.data.response.total);
            $scope.pagination.pageCount = total;
            
     });
    }
 
        $scope.getMad($scope.pagination.offset,$scope.pagination.itemsPerPage);
        
        $scope.$on('pagination:updated', function(event,data) {
              $scope.getMad($scope.pagination.offset,$scope.pagination.itemsPerPage);
            });
            
        
//    $scope.initializePagination = function() {
//        $http.get(generateUrl('admin_file/mandatoryFile/'+$scope.activeType))
//             .then(function(r){
//                 $scope.files = r.data.response.data; 
//               var totalPages = parseInt(r.data.response.total)/1<= 1 ? 1 : (parseInt(r.data.response.total)/1)+1;
//               console.log(totalPages);
//              
//               $('#pagination').twbsPagination({
//                        totalPages: totalPages,
//                        visiblePages: 10,
//                    first: false,
//                    last: false,
//                    prev : "<small> << </small> Prev",
//                    next : "Next <small> >> </small>",
//                        onPageClick: function (event, page) {
//                        $http.get(generateUrl('admin_file/mandatoryFile/'+$scope.activeType)+'&page='+page).
//                        then(function(response) {
//                             $scope.files = response.data.response.data;
//                         });
//                     }
//                });
//            });
////            }
//            
//        $scope.initializePagination();    
 });
    
</script>
@stop 

@section('content')

<div class="col-lg-12" ng-controller="reportController">
    <div style="margin-bottom: 20px">
        <a class="btn btn-primary" href="<?php echo route('admin.society_files',''); ?>"  > << Back To Society Documents</a>
    </div>
    
    <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Society Document Type</th>
                    <th>Mandatory</th>
                    <th>Uploaded</th>    
                </tr>
            </thead>
            <tbody>
                <tr >
                    <td id="fetch" colspan="10" style="font-weight: bold;">
                        Fetching data....
                    </td>
                </tr>
                <tr ng-repeat="file in files">
                    <td>@{{file.id}}</td>
                    <td>@{{file.name}}</td>
                    <td>@{{ file.is_mandatory == 1 ? 'Yes' : 'No'}}</td>
                    <td>@{{ file.file_name == null ? 'Not Uploaded' : 'Uploaded'}}</td>
                </tr>
            </tbody>
   </table>
    
    <div>
       <div id="pagination" class="row">
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
</div>
@stop