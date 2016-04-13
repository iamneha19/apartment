@section('title', 'Flat Document Reports')
@section('panel_title', 'Flat Documents')
@section('panel_subtitle', 'Reports')
@section('head')
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>@stop
@section('content')
<script>
	app.controller("FlatReportsListCtrl", function(URL,paginationServices,$scope,$http,$filter) {
            $scope.reports;
            $scope.pagination = paginationServices.getNew(5);
            $scope.itemsPerPage = 5;
            $scope.sort = 'title';
            $scope.sort_order = 'asc';
            $scope.search='';
            $scope.type='O';
            
            $scope.getReports = function() {
                var request_url = generateUrl('v1/flat_document/reports');
                $http.get(request_url)
               .success(function(result, status, headers, config) {
                    $scope.reports = result.results;
            console.log(result.status);
                    if(result.status == "success") {
                        $('#dataCheck').hide();
                    } else {
                        $('#dataCheck').show();
                    }
               }).error(function(data, status, headers, config) {
//                    console.log(data);
                });
            };
            
            $scope.getReports();
            
            $scope.getTask = function(offset,limit,sort,sort_order,search,type) {
                var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order,type:type}
                if(search){
                   options['search']=search;
                }
                if(type)
                {

                    options['type'] = type;
                }
                var request_url = generateUrl('flat_document/reports',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.task = result.response.data;
                    $scope.pagination.total = result.response.total;
                    console.log($scope.pagination.total);
                    
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                }).error(function(data, status, headers, config) {
                    
                });
            };
	
	//    $scope.getUsers($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
		$scope.$on('pagination:updated', function(event,data) {
//		  $scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.type);
		});
//		$scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
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
    });
	   
</script>
<script>
	$(document).ready(function(){
		$('.date_class').datetimepicker({
			useCurrent : true,
			format: 'DD-MM-YYYY',
                        minDate:moment(new Date()).format('YYYY-MM-DD'),
                        widgetPositioning: {
                            horizontal: 'left',
                            vertical:'bottom'
                     }
		});
         
	});
	</script>
<div class="col-lg-12" ng-controller="FlatReportsListCtrl">
    <div class="row">
        <div class="col-lg-12">
           <!--<input ng-model='search' class="form-control" placeholder="Search" style="width: 300px;">-->
             <span class="pull-left" style="padding:12px;">
                    <a class="btn btn-primary" href="{{ route('admin.flat_documents') }}" ><< Back to Documents</a>
                </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>
                            Flat No
                            <!--<span class="sortorder" ng-show="predicate === 'category_name'" ng-class="{reverse:reverse}"></span>-->
                        </th>
                        <th>
                            Categories
                            <!--<span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>-->
                        </th>
                        <th>
                            Status
                            <!--<span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>-->
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr  style="margin-left: 30px;">
                        <td colspan="3" style="font-weight: bold;" id="dataCheck">No Data Found.</td>
                    </tr>
                    <tr ng-repeat="(key, value) in reports">
                        <td>@{{key}}</td>
                        <td>
                            <ul>
                                <li ng-repeat="category in value.cat">
                                    @{{category.name}}
                                </li>
                            </ul>
                       </td>
                       <td> Not Uploaded</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
		
        <!-- pagination -->
            <div class="row">
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
	@stop
