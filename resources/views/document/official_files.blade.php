@section('title', 'Official Files')
@section('panel_title', 'Official Files')

@section('content')

<script>
app.controller("DocumentCtrl", function(URL,paginationServices,$scope,$http) {
    $scope.documents;
    $scope.folder_id = {{ $folder_id }};
    
    $scope.pagination = paginationServices.getNew(4);
    $scope.itemsPerPage = 4;
    $scope.search='';
    
    $scope.getDocuments= function() {
        var options = {folder_id:folder_id};
        var request_url = generateUrl('document/official/list',options);
        $http.get(request_url)
        .success(function(result, status, headers, config) {  
            $scope.documents = result.response.data;
            $scope.pagination.total = result.response.total;
            $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
        }).error(function(data, status, headers, config) {
            console.log(data);
        });
    };
 
    $scope.$on('pagination:updated', function(event,data) {
        $scope.getDocuments($scope.folder_id,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
    });

    $scope.$watch('search', function(newValue, oldValue) {
        if(newValue){
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;

            $scope.pagination.setPage(1);
        }else{
            $scope.pagination.setPage(1);
        }

    });
  
  
});
</script>
<script>
var folder_id = {{$folder_id}};
</script>
    <div class="col-md-12" ng-controller="DocumentCtrl">
        <div class="row form-group">
            <div class="col-md-12">
                <a href='{{route('folders')}}?type=official' type="button" class="btn btn-default pull-left">Back to folders</a>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-12">
                <label>Search:</label> 
                <input  ng-model="search">
            </div>  
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>

                                Name

                            </th>

                            <th>
                                Uploaded By

                            </th>
                            <th>
                                Uploaded On

                            </th>

                        </tr>
                    </thead>
                    <tbody >
                            <tr ng-repeat="document in documents">
                                <!--<td><a href="@{{document.http_path}}" download>@{{document.name}}</a></td>-->
                                <td><a href="<?php echo route('documents.download') ?>?file=@{{document.http_path}}&name=@{{document.name}}">@{{document.name}}</a></td>
                                <td>@{{document.first_name}}</td>
                                <td>@{{document.created_at}}</td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <ul class="pagination pagination-sm" ng-show="(pagination.pageCount) ? 1 : 0">
                    <li ng-class="pagination.prevPageDisabled()">
                      <a href ng-click="pagination.prevPage()"><i class="fa fa-angle-double-left"></i> Prev</a>
                    </li>
                    <li ng-repeat="n in pagination.range()" ng-class="{active: n == pagination.currentPage}" ng-click="pagination.setPage(n)">
                      <a href>@{{n}}</a>
                    </li>
                    <li ng-class="pagination.nextPageDisabled()">
                      <a href ng-click="pagination.nextPage()">Next <i class="fa fa-angle-double-right"></i></a>
                    </li>
                </ul>
            </div> 
        </div>
      
    </div>
    <script>
        $('document').ready(function(){
           
        });
    </script>
@stop
