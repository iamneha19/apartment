@section('title', 'Task')
@section('panel_title', 'Task')
@section('content')
    <script type="text/javascript">
        app.controller("TaskCtrl", function($scope,$http,$filter) {
            $scope.task;
            $scope.getTask = function(id) {
               var request_url = generateUrl('task/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.task = result.response;
					console.log($scope.task);
                    $scope.task.created_at = new Date( $scope.task.created_at); // Converting to UTC date
					$scope.task.begin_on = new Date( $scope.task.begin_on);
					$scope.task.due_on = new Date( $scope.task.due_on);
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getTask({{$id}});
        });
    </script>
    <div class="col-lg-12" ng-controller="TaskCtrl" >
        <div class="row">
            <div class="col-lg-12">
                    <a class='btn btn-primary pull-right' href='<?php echo url('admin/task/edit', $parameters = [], $secure = null) ?>/@{{task.id}}'>Edit</a>
                    <h3>@{{task.title}}</h3>
                    <p><span class="highlight">Posted On :</span>@{{task.created_at | date:'dd-MMM-yyyy H:mm a' }}</p>
                    <p><span class="highlight">Begin On :</span>@{{task.begin_on | date:'dd-MMM-yyyy' }}</p>
                    <p><span class="highlight">Due On :</span>@{{task.due_on | date:'dd-MMM-yyyy' }}</p>
                    <p><span class="highlight">Assign To:</span>@{{task.assign_user}}</p>
                    <div class='clear-both'></div> 
            </div>
        </div>
    </div>     
@stop
