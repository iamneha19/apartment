@section('title', 'Task Category')
@section('panel_title', 'Task Category')
@section('content')
    <script type="text/javascript">
        app.controller("TaskCategoryCtrl", function($scope,$http,$filter) {
            $scope.task_category;
            $scope.getTaskCategory = function(id) {
                var request_url = generateUrl('task_category/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.task_category = result.response;
                    $scope.task_category.created_at = new Date( $scope.task_category.created_at); 
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getTaskCategory({{$id}});
        });
    </script>
    <div class="col-lg-12" ng-controller="TaskCategoryCtrl" >
        <div class="row">
            <div class="col-lg-12">
                    <a class='btn btn-primary pull-right' href='<?php echo url('admin/task_category/edit', $parameters = [], $secure = null) ?>/@{{task_category.id}}'>Edit</a>
                    <h3>@{{task_category.category_name}}</h3>
                    <div class='clear-both'></div>
                    <p><span class="highlight">Posted On :</span>@{{task_category.created_at | date:'dd-MMM-yyyy H:mm a' }}</p>
                    <div class='clear-both'></div>    
            </div>
        </div>
    </div>     
@stop
