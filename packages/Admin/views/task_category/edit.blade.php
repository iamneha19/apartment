@section('title', 'Task Category Dashboard')
@section('panel_title', 'Task Category')
@section('panel_subtitle', 'List')
@section('content')
    <script type="text/javascript">
        app.controller("TaskCategoryListCtrl", function($scope,$http,$filter) {
            $scope.task_category;
            $scope.getTaskCategory = function(id) {
                var request_url = generateUrl('task_category/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.task_category = result.response;
                    $scope.task_category.created_at = new Date( $scope.task_category.created_at); // Converting to UTC date
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getTaskCategory({{$id}});

                /// datetime format function

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

            $scope.openForm = function(){
                $('#formModal').modal();
            };

            $('#task_category-form').submit(function(e){
                e.preventDefault();
                if ($("#task_category-form").valid()){
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('task_category/update/'+$scope.task_category.id);
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        window.location="<?php echo url('admin/task_category/', $parameters = [], $secure = null) ?>/"+$scope.task_category.id; 
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });
        });
    </script>
    <script>
    $(document).ready(function(){
        $('#task_category-form').validate({
            rules:{
                category_name :'required',
            }
        });
    });
    </script>
    <div class="col-lg-12" ng-controller="TaskCategoryListCtrl" >
        <div class="row">
            <div class="col-lg-12">
                <form id="task_category-form" method="post" action="">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Category Name</label>
                        <input type="text" class="form-control" name = "category_name" value ="@{{task_category.category_name}}"  placeholder="category">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@stop