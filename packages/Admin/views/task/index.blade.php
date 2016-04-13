@section('title', 'Task Dashboard')
@section('panel_title', 'Tasks')@section('panel_subtitle','List')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>@stop
@section('content')
<script>
  
	app.controller("TaskListCtrl", function(URL,paginationServices,$scope,$http,$filter) {
            $scope.task;
            $scope.task_category;
            $scope.users;
            $scope.pagination = paginationServices.getNew(5);
            $scope.itemsPerPage = 5;
            $scope.sort = 'created_at';
            $scope.sort_order = 'desc';
            $scope.form = {};
            $scope.type = 'A';
            $scope.activeType = 'Task';
            $('#loader').hide();
			
            $scope.getTask = function(offset,limit,sort,sort_order,search,type) {
                if( typeof  type == "undefined" ) { options['type'] = 'A'; }
				if( typeof  search == "undefined" ) { search = ''; }
				var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order,type:type,search:search}
                var request_url = generateUrl('task/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.task = result.response.data;
					$scope.pagination.total = result.response.total;
					$scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
					if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
			$scope.changeTaskType  = function() {
				$scope.pagination.total = 0;	
				$scope.pagination.offset = 0;
				$scope.pagination.currentPage = 1;
				$scope.type = $("#task_type option:selected").val();
				$scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.form.search,$scope.type);
			}

            $scope.getTaskCategory = function() {
                var request_url = generateUrl('v1/admin/list/typeList/'+$scope.activeType);
                  $http.get(request_url)
                .success(function(result, status, headers, config) {
                        $scope.task_category = result.results.data;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };

            $scope.getUsers = function() {
                var request_url = generateUrl('user/data');
                $http.get(request_url)
               .success(function(result, status, headers, config) {
                    $scope.users = result.response.data;
                    console.log($scope.users);
               }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

	//    $scope.getUsers($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
		$scope.$on('pagination:updated', function(event,data) {
		  $scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.form.search,$scope.type);
		});
//		$scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
	   $scope.$watch('form.search', function(newValue, oldValue) {
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

		$scope.openTask = function(){
			$('#TaskModal').modal();
                        $('#begin_on').val('');
                        $('#due_on').val('');
			$scope.getTaskCategory();
			$scope.getUsers();
		};
        $scope.closeForm = function(){
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.pagination.setPage(1);
            $("#task-form ")[0].reset();
            $("#task-form label.error").remove();
            $('#TaskModal').modal('hide');
        };

		//date format function
		$scope.formatDateTime = function(date,time){
			var dateArray = date.split("-");
			if(time){
//				var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
                                var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time).toDate();
				return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
			}else{
//				var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
                                var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]).toDate();
				return $filter('date')(dateUTC, 'yyyy-MM-dd');
			}
		};

//                $scope.typeFilter = function(type){
//                    $scope.type = type;
//                    $scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,type);
//        };

	   $('#task-form').submit(function(e){
			e.preventDefault();

			if($('#task-form').valid()){
                            $('#loader').show();
                            $(this).find('button[type=submit]').attr('disabled',true);
                            $(this).find('button[type=submit]').text('Creating task please wait..');
				var  beginDate =  $('#begin_on').val();
				var formatedDate = $scope.formatDateTime(beginDate);
//				$('#begin_on').val(formatedDate); // Change format to Y-M-D H:i:s

				var endDate =  $('#due_on').val();
				var formated_dueDate = $scope.formatDateTime(endDate);

                                    var data = $(this).serializeArray();
                                    data.push({name:'begin_on',value:formatedDate});
                                    data.push({name:'due_on',value:formated_dueDate});
                                    var records = $.param(data);
				 var request_url = generateUrl('task/create');
				$http({
					url: request_url,
					method: "POST",
					data: records,
					headers: {'Content-Type': 'application/x-www-form-urlencoded'}
				})
				.then(function(response) {
                                    $('#loader').hide();
                                    $("#task-form").find('button[type=submit]').attr('disabled',false);
                                    $("#task-form").find('button[type=submit]').text('Submit');
                                        $scope.closeForm();
					grit('','Task created successfully!');
					$scope.getTask($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.type);
//					window.location.reload();
				},
				function(response) { // optional
//				   alert("fail");
				});
			}
		});

        jQuery.validator.addMethod("domain", function(value, element) {
        var  beginDate =  $('#begin_on').val();
        var start_date = $scope.formatDateTime(beginDate);

        var endDate =  $('#due_on').val();
        var end_date = $scope.formatDateTime(endDate);

           if(end_date>start_date)
           {
               return true;
         }else{
                return false;
          }

          }, "Due date must be greater than start date!");


	});
</script>
<script>
$(document).ready(function(){

	$('#task-form').validate({
		rules :
		{
                        due_on : {required:true,domain:true},
			title : "required",
			assign_to:"required",
			task_category_id:"required",
			begin_on:"required",
//			due_on:"required",
		},
        errorPlacement: function(error, element) {
                    if (element.attr("name") == "begin_on"  ) {
                        $( ".visiblity_error" ).html( error );
                    }else if (element.attr("name") == "due_on"  ) {
                        $( ".visiblity_due_error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
	});
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
<div class="col-lg-12" ng-controller="TaskListCtrl">
    <div class="row">
        <div class="col-lg-12 form-group">
			<div class="col-md-9" style="margin-left: -12px;">
				<input type="text" ng-model="form.search" class="form-control ng-pristine ng-untouched ng-valid" placeholder="Search By Title" style="width: 200px;display: inline" />
				<b style="margin-left:15px;">Task Status : </b>
				<select id='task_type'  name="task_type" ng-model="task_type" ng-change="changeTaskType()" style="height: 30px;width: 100px;">
    				<option disabled value="">Select Task</option>
                    <option ng-selected="true" value="A">All</option>
    				<option value="O">Open</option>
    				<option value="C">Close</option>
				</select>
			</div>
            <button type="button" class="btn btn-primary pull-right" ng-click="openTask()">Add Task</button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>
                            <a href="" ng-click="order('category')">Category</a>
                            <span class="sortorder" ng-show="predicate === 'category'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('title')">Title</a>
                            <span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('assign_to')">Assign To</a>
                            <span class="sortorder" ng-show="predicate === 'assign_to'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('created_by')">Created By</a>
                            <span class="sortorder" ng-show="predicate === 'created_by'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('type')">Task Status</a>
                            <span class="sortorder" ng-show="predicate === 'type'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('created_at')">Created At</a>
                            <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th><a href="#" >Action</a></th>
                    </tr>
                </thead>
                <tbody>
					<tr ng-if="pagination.total == 0">
                        <td colspan="8" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                    </tr>
                    <tr ng-if="pagination.total > 0" ng-repeat="tasks in task | filter:search">
                        <td>@{{tasks.id}}</td>
                        <td>@{{tasks.category}}</td>
                        <td>@{{tasks.title}}</td>
                        <td>@{{tasks.assignTo}}</td>
                        <td>@{{tasks.createdBy}}</td>
                        <td>@{{tasks.task_type}}</td>
                        <td>@{{tasks.created_at}}</td>
                        <td>
                            <div ng-show="(({{Session::get('user.user_id')}} == tasks.assign_to) ||({{Session::get('user.user_id')}} == tasks.created_by))  ? 1 : 0">
                                <a class="glyphicon glyphicon-pencil" title="update" href="{{route('admin.taskupdate','')}}/@{{tasks.id}}"></a>
                             </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

        <!-- pagination -->
            <div ng-if="pagination.total > 0" class="row">
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
        <div class="modal fade" id="TaskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Add Task</h4>
          </div>
          <div class="modal-body">
			  <form id="task-form" method="post" action="">
				<div class="form-group">
                  <label class="form-label">Task</label>
                  <input type="text" class="form-control" name = "title" id="reset_title" maxlength="100" maxlength="100" placeholder="Task title or description">
                </div>

                    <div class="form-group">
                  <label class="form-label">Assign To</label>
				  <select name="assign_to" class="form-control">
                        <option value="" disabled="" selected="">Select User </option>
                        <option ng-repeat="user in users" value='@{{user.id}}'>@{{user.first_name+' '+user.last_name}}</option>
					</select>
                </div>

				<div class="form-group">
					<label class="form-label">Task Category</label>
                    <select name="task_category_id" class="form-control">
                        <option value="" disabled="" selected="">Select a Category</option>
                        <option ng-repeat="category in task_category" value='@{{category.id}}'>@{{category.name}}</option>
					</select>
                </div>

                <div class="form-group">
                    <label class="form-label">Begin On</label>
					<input type="text" class="form-control date_class" name = "begin_on" id="begin_on" maxlength="100"  placeholder="Begin On">
                     <div class="visiblity_error"></div>
                </div>


                <div class="form-group">
                    <label class="form-label">Due On</label>
					<input type="text" class="form-control date_class" name = "due_on" id="due_on" maxlength="100"  placeholder="Due On">
                     <div class="visiblity_due_error"></div>
                </div>


               <button type="submit" class="btn btn-primary">Submit</button>
				<button class="btn btn-primary" type="button" ng-click="closeForm()" >Cancel</button>
              </form>
               <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
          </div>
        </div>
      </div>
    </div>
</div>
	@stop
