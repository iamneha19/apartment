@section('title', 'Task Category Dashboard')
@section('panel_title', 'Task Category')
@section('panel_subtitle', 'List')
@section('content')
    <script>
    app.controller("TaskCategoryListCtrl", function(URL,paginationServices,$scope,$http) {
        $scope.task_category;
        $scope.pagination = paginationServices.getNew(5);
        $scope.itemsPerPage = 5;
        $scope.sort = 'created_at';
        $scope.sort_order = 'desc';
        $scope.search='';
        $scope.getTask_category = function(offset,limit,sort,sort_order,search) {
            var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order}
            if(search){
                options['search']=search;
            }
            var request_url = generateUrl('task_category/list',options);
            $http.get(request_url)
            .success(function(result, status, headers, config) {
                $scope.task_category = result.response.data;
                $scope.pagination.total = result.response.total;
                $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };

        $scope.$on('pagination:updated', function(event,data) {
            $scope.getTask_category($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
        });
        $scope.$watch('search', function(newValue, oldValue) {
            if(newValue){
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'category_name';
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

        $scope.openTaskCategory = function(){
            $('#TaskCategoryModal').modal();
        };
        
        $scope.closeForm = function(){
            $("#task_category-form")[0].reset();
            $("#task_category-form label.error").remove();
            $('#TaskCategoryModal').modal('hide');
        };
        
        $scope.closeUpdateForm = function(){
            $("#TaskCategory-update-form")[0].reset();
            $("#TaskCategory-update-form label.error").remove();
            $('#TaskCategoryEditFormModal').modal('hide');
        };
        $('#task_category-form').submit(function(e){
            
            e.preventDefault();
            var society_id = {{$society_id}};
            if($('#task_category-form').valid())
            {
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Creating category please wait..');
                var data = $(this).serializeArray();
                data.push({name:'society_id',value:society_id});
                var records = $.param(data);
//                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('task_category/create');
                $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                .then(function(response) {
                    $("#task_category-form").find('button[type=submit]').attr('disabled',false);
                    $("#task_category-form").find('button[type=submit]').text('Submit');
                    var result = response.data.response;
                    if(result.success)
                    {
                        $scope.pagination.total=0;
                        $scope.pagination.offset = 0;
                        $scope.pagination.currentPage = 1;
                        $scope.pagination.setPage(1); 
                        $scope.closeForm();
                        grit('','Task category created successfully!');
                    }else{
                        if(result.duplicate_category)
                            {
                                $( "label#category_name-error" ).remove();
                                $( "#category_name" ).after( '<label id="category_name-error" class="error" for="category_name">Category name is already exists.</label>' );
                            }else{
                                $( "label#category_name-error" ).remove();
                            }
                    }
                }, 
                function(response) { // optional
//                    alert("fail");
                }); 
            }
        }); 
        
        $scope.edit_task_category;
        $scope.edit_id;
        $scope.openTaskCategoryEditForm = function(task_category_id,category_name){
            $scope.edit_task_category = category_name;
            $scope.edit_id = task_category_id;
            $('#TaskCategoryEditFormModal').modal();
        };
        
        $('#TaskCategory-update-form').submit(function(e){
        e.preventDefault();
        if ($(this).valid() && $scope.edit_id){
            $(this).find('button[type=submit]').attr('disabled',true);
            $(this).find('button[type=submit]').text('updating category please wait..');
            var records = $.param($( this ).serializeArray());
            var request_url = generateUrl('task_category/update/'+$scope.edit_id);
            $http({
                url: request_url,
                method: "POST",
                data: records,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(function(response) {
                $("#TaskCategory-update-form").find('button[type=submit]').attr('disabled',false);
                $("#TaskCategory-update-form").find('button[type=submit]').text('Submit');
                var result = response.data.response;
                    if(result.success)
                    {
                        $scope.pagination.total=0;
                        $scope.pagination.offset = 0;
                        $scope.pagination.currentPage = 1;
                        $scope.pagination.setPage(1);   
                        $('#TaskCategoryEditFormModal').modal('hide');
                        grit('','Task category updated successfully!');
                    }else{
                        if(result.duplicate_category)
                            {
                                $( "#category_name_edit-error" ).remove();
                                $( "#category_name_edit" ).after( '<label id="category_name_edit-error" class="error" for="category_name_edit">Category name is already exists.</label>' );
                            }else{
                                $( "label#category_name_edit-error" ).remove();
                            }
                    }
                
            }, 
            function(response) { // optional
//                   alert("fail");
            });
        }
    });
    });
    </script>
    

    <div class="col-lg-12" ng-controller="TaskCategoryListCtrl">
        <div class = "row">
            <div class = "col-lg-12">
                <label>Search: <input ng-model="search"></label>
                <span class="pull-right" style="padding:7px;">
                    <button type="button" class="btn btn-primary" ng-click="openTaskCategory()">Create Task Category</button>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>
                                <a href="" ng-click="order('category_name')">Category</a>
                                <span class="sortorder" ng-show="predicate === 'category_name'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('created_at')">Created At</a>
                                <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th> <a href="">Action</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="category in task_category | filter:search">
                            <td>@{{category.category_name}}</td>
                            <td>@{{category.created_at}}</td>
                            <td>
                                <a class="glyphicon glyphicon-pencil" title="update" href="" ng-click="openTaskCategoryEditForm(category.id,category.category_name)"></a>
                            </td>
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

        <!-- Modal -->
        <div class="modal fade" id="TaskCategoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create Task Category</h4>
                    </div>
                    <div class="modal-body">
                        <form id="task_category-form" method="post" action="">
                            <div class="form-group">
                                <label class="form-label">Category Name</label>
                                <input type="text" class="form-control" name = "category_name" maxlength="50" id="category_name"  placeholder="Category">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button class="btn btn-primary" type="button" ng-click="closeForm()">Cancel</button>
                         </form>
                    </div>
                </div>
            </div>
        </div>
        <!--  EditModal  -->
    <div class="modal fade" id="TaskCategoryEditFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="closeUpdateForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Task Category</h4>
                </div>
                <div class="modal-body">
                    <form id="TaskCategory-update-form" method="post" action="">
                        <div class="form-group">
                            <label class="form-label">Category Name </label>
                            <input type="text" class="form-control" name="category_name" maxlength="50" id="category_name_edit" value="@{{edit_task_category}}"  placeholder="Category">
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button class="btn btn-primary" type="button" ng-click="closeUpdateForm()">Cancel</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function(){
        $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            }); 
        var society_id = {{$society_id}};
        $('#task_category-form').validate({
            rules: {
            category_name : {
                    required:true,
                    remote:{
                        url: generateUrl('task_category/check_category'),
                        type: "post",
                        dataType:"json",
                        data: {category_name:function() {
                                return $( "#category_name" ).val();
                              },society_id:society_id},
                        success:function(r) {
                                var result = r.response;
//                                alert(result);
                               $( "label#category_name-error" ).remove();                        
                                 if(result.success){
                                     $( "label#category_name-error" ).remove();
                                     return true;
                                 }else{
                                     $( "label#category_name-error" ).remove();
                                     $( "#category_name" ).after( '<label id="category_name-error" class="error" for="category_name">Category name is already exists.</label>' );
                                     return false;
                                 }
                             }
                    }
                },
            }
        });
         $('#TaskCategory-update-form').validate({
            rules: {
                category_name :{required:true},
            }
        });
    });
    </script>
@stop
