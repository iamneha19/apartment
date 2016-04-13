@section('title', 'Edit Task')
@section('panel_title', 'Edit Task')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("TaskCtrl", function($scope,$http,$filter) {
            $scope.task;
             $scope.activeType = 'Task';
             $('#loader').hide();
             
            
            $scope.getTaskCategory = function() {
                var request_url = generateUrl('v1/admin/list/typeList/'+$scope.activeType);
                  $http.get(request_url)
                .success(function(result, status, headers, config) { 
                        $scope.task_category = result.results.data;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
            
//            $scope.getTaskCategory = function() {
//               var request_url = generateUrl('task_category/allData');
//                $http.get(request_url)
//                .success(function(result, status, headers, config) {  
//                    $scope.task_category = result.response.data;
//                }).error(function(data, status, headers, config) {
//                    console.log(data);
//                });
//            };
		
            $scope.getUsers = function() {
               var request_url = generateUrl('user/data');
                $http.get(request_url)
                .success(function(result, status, headers, config) {  
                    $scope.users = result.response.data;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
 
            $scope.getTask = function(id) {
                var request_url = generateUrl('task/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.task = result.response;
                    $scope.task.begin_on = new Date( $scope.task.begin_on); // Converting to UTC date
                    $scope.task.due_on = new Date( $scope.task.due_on); // Converting to UTC date
                    $scope.getTaskCategory();
                    $scope.getUsers();
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            $scope.getTask({{$id}});
            
            
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

            $('#task-form').submit(function(e){
                e.preventDefault();
                if ($("#task-form").valid()){
                    $('#loader').show();
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Updating task please wait..');
                    $("#task-form .error").html('');
                    var  beginDate =  $('#begin_on').val();
                    var formatedDate = $scope.formatDateTime(beginDate);
//                    $('#begin_on').val(formatedDate); // Change format to Y-M-D H:i:s
				
                    var endDate =  $('#due_on').val();
                    var formated_dueDate = $scope.formatDateTime(endDate);
//                    $('#due_on').val(formated_dueDate); // Change format to Y-M-D H:i:s
                   
                   var data =  $( this ).serializeArray();
                   data.push({name:'begin_on',value:formatedDate});
                   data.push({name:'due_on',value:formated_dueDate});
                     
                    var records = $.param(data);
                    
                    console.log(records);
                    var request_url = generateUrl('task/edit/'+$scope.task.id);
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
                        grit('','Task updated successfully!');
                        window.location="{{route('admin.task')}}";  
                    }, 
                    function(response) { // optional
//                        alert("fail");
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
        
          });
        });
    </script>
    <div class="col-lg-12" ng-controller="TaskCtrl" >
        <div class="row">
            <div class="col-lg-6">
                <form id="task-form" method="post" action="">
                    <div ng-show="( {{$user_id}} == task.created_by )  ? 1 : 0">
                        <div class="form-group">
                             <label class="form-label">Task</label>
                             <input type="text" class="form-control" name = "title" maxlength="100" value="@{{task.title}}"  placeholder="Task title or description">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Assign To</label>
                            <select name="assign_to" class="form-control">
                                <option value="" disabled="">Select User </option>
                                <option ng-repeat="user in users" value='@{{user.id}}' ng-selected="task.assign_to == user.id">@{{user.first_name+' '+user.last_name}}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Task Category</label>
                            <select name="task_category_id" class="form-control">
                                <option value="" disabled="">Select a Category</option>
                                <option ng-repeat="category in task_category" value='@{{category.id}}' ng-selected="task.task_category_id == category.id">@{{category.name}}</option>
                            </select>
                         </div>

<!--                        <div class="form-group">
                            <label class="form-label">Begin On</label>
                            <input class="date_class" id="begin_on" value="@{{task.begin_on| date:'dd-MM-yyyy'}}" name="begin_on">
                            <div class="visiblity_error"></div>
                        </div>-->

<!--                        <div class="form-group">
                            <label class="form-label">Due On</label>
                            <input class="date_class" id="due_on" value="@{{task.due_on| date:'dd-MM-yyyy'}}" name="due_on">
                            <div class="visiblity_due_error"></div>
                        </div>
-->                        <div class="form-group">
                            <label class="form-label">Begin On</label>
                                <input type="text" class="form-control date_class" value="@{{task.begin_on| date:'dd-MM-yyyy'}}" name = "begin_on" id="begin_on" maxlength="100"  placeholder="Begin Date">
                             <div class="visiblity_error"></div>
                        </div>


                        <div class="form-group">
                            <label class="form-label">Due On</label>
                                <input type="text" class="form-control date_class" value="@{{task.due_on| date:'dd-MM-yyyy'}}" name = "due_on" id="due_on" maxlength="100"  placeholder="Begin Date">
                             <div class="visiblity_due_error"></div>
                        </div>
                        
                       <div class="form-group" ng-show="task.type=='O'">
                            <label for="exampleInputPassword1">Close Task </label>
<!--                            div class="radio-inline">
                                    <label>
                                        <input type="radio" name="type" value="1">
                                        Administration Notice
                                    </label>
                                </div>-->
                            <div class="radio-inline">
                                <label>
                                    <input type="checkbox" class="close_task" name="type">
                                </label>
                            </div>
                        </div>

                        <div class="form-group" ng-show="task.type=='C'">
                            <label for="exampleInputPassword1">Re-open Task </label>
                            <div class="radio-inline">
                                <label>
                                    <input type="checkbox" class="reopen_task" name="type">
                                </label>
                            </div>
                        </div>
                         <button type="submit" class="btn btn-primary">Update</button>
                        <button class="btn btn-primary" type="button" onclick="javascript:window.location.href='{{route('admin.task')}}'">Cancel</button>
                    </div>
                    <div ng-show="( {{$user_id}} == task.assign_to && {{$user_id}} != task.created_by  )  ? 1 : 0">
                        
                          <h3> Task: @{{task.title}}</h3>
                        
                        <div class="form-group" ng-show="task.type=='O'">
                            <label for="exampleInputPassword1">Close Task </label>
                           <div class="radio-inline">
                                <label>
                                    <input type="checkbox" class="close_task" name="type">
                                </label>
                            </div><br/>
                             <button type="submit" class="btn btn-primary">Update</button>
                            <button class="btn btn-primary" type="button" onclick="javascript:window.location.href='{{route('admin.task')}}'">Cancel</button>
                        </div>
                        <div class="form-group" ng-show="task.type=='C'">
                            <h5> This task is closed.You have no access to update this task!</h5>
                            <button class="btn btn-primary" type="button" onclick="javascript:window.location.href='{{route('admin.task')}}'"> << Back To Task</button>
                        </div>
                    </div>
                     
                     <input type="hidden" name="created_by" value="@{{task.created_by}}">
                  
                </form>
                 <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
            </div>
        </div>
    </div>
    <script>
        $('document').ready(function(){
            $('.date_class').datetimepicker({
                useCurrent : true,
		format: 'DD-MM-YYYY',
                minDate:moment(new Date()).format('YYYY-MM-DD'),
                widgetPositioning: {
                    horizontal: 'left',
                    vertical:'bottom'
                }
            });
            
           $('#task-form').validate({
		rules :
		{
                    due_on : {required:true,domain:true},
                    title : "required",
                    assign_to:"required",
                    task_category_id:"required",
                    begin_on:"required",
		},
                messages: {
                    due_on: {
                        domain: "Due date must be greater than start date!"
                    }
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
            $('.close_task').on('click',function(){
                
                if($(this).attr('checked')){
                    $(this).val("C");
                }else{
                    $(this).val("O");
                }
            });
                
             $('.reopen_task').on('click',function(){
                if($(this).attr('checked')){
                    $(this).val("O");
                }else{
                    $(this).val("C");
                }
            });
	});
    </script>
@stop
