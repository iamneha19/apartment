@section('title', 'Overdue Dashboard')
@section('panel_title', 'Overdue Tasks')
@section('panel_subtitle', '')
@section('content')
<script>
	app.controller("MyTasksListCtrl", function(URL,paginationServices,$scope,$http,$filter) {
            $scope.task;
            $scope.pagination = paginationServices.getNew(5);
            $scope.itemsPerPage = 5;
            $scope.search='';
            $scope.type='O';
            $scope.sort = 'title';
            $scope.sort_order = 'asc';
            $scope.getTask = function(offset,limit,sort,sort_order,search,type) {
                var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order,type:type}
                if(search){
                   options['search']=search;
                }
                var request_url = generateUrl('oldtasks/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.task = result.response.data;
                    $scope.pagination.total = result.response.total;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
					if ($scope.pagination.total == 0)
						$("#dataCheck").text("No Data Found.");
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
	 
	//    $scope.getUsers($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
		$scope.$on('pagination:updated', function(event,data) {
		  $scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.type);
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
    
    $scope.remove=function(id){
        var result = confirm("Are you sure you want to close this task!");
                if(result == true)
                {
                $('.close_link').addClass('avoid-clicks');
                var request_url = generateUrl('mytasks/close');
                $http({
                    url: request_url,
                    method: "POST",
                    data:$.param({task_id:id}),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .then(function(response) {
                    $('.close_link').removeClass('avoid-clicks');
                    grit('','Task closed successfully');
                  $scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.type);
                }, 
                function(response) { // optional
//                       alert("fail");
                });
                }else{
                   return false;
               }
            }
            
            $scope.tab = function(type) {
                $scope.type = type;
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
               $scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.type);
            };
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
		
		<!--date format function -->
		$scope.formatDateTime = function(date,time){
			var dateArray = date.split("-");
			if(time){
				var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
				return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
			}else{
				var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
				return $filter('date')(dateUTC, 'yyyy-MM-dd'); 
			}
		};
                
                
		
	   
                
	});
</script>
<div class="col-lg-12" ng-controller="MyTasksListCtrl">
    <div class="row">
        <div class="col-lg-12" style="margin-bottom: 20px;">
            <!--<label>Search: <input ng-model="search"></label>-->
			<input ng-model='search'  class="form-control" placeholder="Search By Title" style="width: 200px;">
            
        </div>
        
        <div style="margin-bottom: 20px;">
            <span style="padding:12px;">
                <a class="btn btn-primary" href="{{route('admin.mytasks')}}"><< Back to MyTasks </a>
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
                            <a href="" ng-click="order('category_name')">Category</a>
                            <span class="sortorder" ng-show="predicate === 'category_name'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('title')">Title</a>
                            <span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>
                        </th>
                            <th>
                            <a href="" ng-click="order('due_on')">due On</a>
                            <span class="sortorder" ng-show="predicate === 'due_on'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th><a href = "">Action</a></th>
                    </tr>
                    </tr>
                </thead>
                <tbody>
					<tr ng-if="pagination.total == 0">
                        <td colspan="10" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                    </tr>
                    <tr ng-if="pagination.total > 0" ng-repeat="tasks in task | filter:search" class="old_data">
                        <td>@{{tasks.id}}</td>
                        <td>@{{tasks.category}}</td>
                        <td>@{{tasks.title}}</td>
                        <td>@{{tasks.due_on|date:'dd-MM-yyyy'}}</td>
                       
                            <td ng-show = "(tasks.type =='O')" ? 1 : 0 >
                                <a title="close task" class="close_link" href="javascript:void(0);" ng-click="remove(tasks.id)">Close</a>
                            </td>
                            <td ng-show = "(tasks.type =='C')" ? 1 : 0 >
                                Close
                            </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
		
		<!-- pagination -->
        <div class="row">
            <div ng-if="pagination.total > 0" class="col-lg-12">
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
		
		<!-- Modal -->
<!--        <div class="modal fade" id="TaskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Add Task</h4>
          </div>
          <div class="modal-body">
			  <form id="task-form" method="post" action="">
				<div class="form-group">
                  <label for="exampleInputEmail1">Task</label>
                  <input type="text" class="form-control" name = "title"  placeholder="Task title or description">
                </div>
				
				<div class="form-group">
                  <label for="exampleInputEmail1">Assign To</label>
				  <select name="assign_to" class="form-control">
                        <option value="" disabled="">Select </option>
                        <option ng-repeat="user in users" value='@{{user.id}}'>@{{user.first_name}}</option>
					</select>
                </div>
				
				<div class="form-group">
					<label>Task Category</label>
                    <select name="task_category_id" class="form-control">
                        <option value="" disabled="">Select a Category</option>
                        <option ng-repeat="category in task_category" value='@{{category.id}}'>@{{category.category_name}}</option>
					</select>
                </div>
				
				<div class="form-group">
					<label for="exampleInputEmail1">Begin On</label>
					<input class="date_class" id="begin_on" name="begin_on">
                </div>
				
				<div class="form-group">
					<label for="exampleInputEmail1">Due On</label>
					<input class="date_class" id="due_on" name="due_on">
                </div>
               <button type="submit" class="btn btn-success">Submit</button>
				<button class="btn btn-default" type="button" onclick="javascript:window.location.href='<?php //echo url('admin/task/', $parameters = [], $secure = null) ?>'">Cancel</button>
              </form>
          </div>
        </div>
      </div>
    </div>-->
</div>

	@stop
