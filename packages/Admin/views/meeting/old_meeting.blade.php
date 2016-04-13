@section('title', 'Old Meeting')
@section('panel_title', 'Old Meetings')
@section('panel_subtitle', 'List')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@stop
@section('content')
    <script>
	app.controller("OldMeetingListCtrl", function(URL,paginationServices,$scope,$http,$filter) {
            $scope.meetings;
            $scope.pagination = paginationServices.getNew(5);
            $scope.itemsPerPage = 5;
            $scope.sort = 'title';
            $scope.sort_order = 'asc';
            $scope.search='';
            
            
            $scope.getMeetings = function(offset,limit,sort,sort_order,search) {
                var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order}
                if(search){
                   options['search']=search;
                }
                var request_url = generateUrl('oldmeeting/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.meetings = result.response.data;
                    $scope.pagination.total = result.response.total.total;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                    if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
	 
	//    $scope.getUsers($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
	  
            $scope.$on('pagination:updated', function(event,data) {
                $scope.getMeetings($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
            });
//	   $scope.getMeetings($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
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

    <div ng-controller="OldMeetingListCtrl" class="col-lg-12"  id="meeting">
        <div class="row">
            <div class="col-lg-12" style="margin-bottom: 20px">
                
                <input ng-model="search"  class="form-control pull-left" placeholder="Search By Brief Topic" style="width: 200px;" />
            </div>
            <div style="margin-bottom: 20px">
            <span  style="padding:12px; ">
                    <a class="btn btn-primary" href="{{route('admin.meeting')}}" ><< Back to Meetings</a>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
		<table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>
                                <a href="" ng-click="order('date')">Date</a>
                                <span class="sortorder" ng-show="predicate === 'date'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('title')">Brief Topic</a>
                                <span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('created_at')">Venue</a>
                                <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('address')">Agenda</a>
                                <span class="sortorder" ng-show="predicate === 'address'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('address')">Initiated By</a>
                                <span class="sortorder" ng-show="predicate === 'address'" ng-class="{reverse:reverse}"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-if="pagination.total == 0">
                            <td colspan="10" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                        </tr>
                        <tr ng-repeat="meeting in meetings | filter:search" class="old_data">
                            <td>@{{meeting.id}}</td>
                            <td>@{{meeting.date | date:'DD-MM-YYYY hh:mm a'}}</td>
                            <td>@{{meeting.title}}</td>
                            <td>@{{meeting.venue}}</td>
                            <td>@{{meeting.agenda}}</td>
                            <td>@{{meeting.user_name}}</td>
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