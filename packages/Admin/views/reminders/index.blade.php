@section('title', ' Meetings Reminder Configurations')
@section('panel_title', 'Meetings Reminder Configurations')@section('panel_subtitle','List')
@section('head')
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>

<script>
        app.controller("RemindersCtrl", function($http,paginationServices,$scope,$filter) {
           $scope.activeIndex = null;
    $scope.activeType = 'meeting';
    $scope.types;
    $scope.societyType;
    $scope.type;
    $scope.isDisabled = false;
    $scope.duplicateType= null;
   $('#loader').hide(); 
    $scope.itemsPerPage = 10;
    $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
    
//    $scope.defaultList = function(page) {
//        var options = {page:page,per_page:$scope.itemsPerPage};
//        $scope.pagination.currentPage = page;
//    $http.get(generateUrl('v1/categories/admin',options),$scope.societyTypes)
//    .then(function(response){
//        $scope.societyTypes = response.data.results.data;
//            $scope.pagination.total = response.data.results.total;
//            $scope.pagination.pageCount = response.data.results.last_page;
//    });
//    }
//      $scope.defaultList();
//    
    $scope.typeSelect = function(type,page) {
        $scope.activeType = type;
//        console.log(type);
        if(!page){
            page = 1;
        }
        var options = {page:page,per_page:$scope.itemsPerPage};
       $scope.pagination.currentPage = page;
        $http.get(generateUrl('v1/list/reminders/'+type,options))
        .then(function(response){
            $scope.types = response.data.results;
            $scope.pagination.total = $scope.types.length;
            $scope.pagination.pageCount = response.data.results.last_page;
			if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
        });  
    }
    
    $scope.typeSelect($scope.activeType);
    $scope.$on('pagination:updated', function(event,data) {
        $scope.typeSelect($scope.activeType,$scope.pagination.currentPage);
    });

    $scope.openReminder = function(typeId){
      
        $('#type_id').val(typeId);
        $('#ReminderModal').modal();
    }; 
    
        $scope.edit_reminder;
        $scope.edit_id;
        $scope.type_id;
        
        $scope.openReminderEditForm = function(reminder_id,alert,type_id){
            
            $scope.edit_reminder = alert;
            $scope.edit_id = reminder_id;
            $scope.type_id = type_id;
            $('#ReminderUpdateModal').modal();
            
        };
    
    $scope.closeForm = function(){
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.pagination.setPage(1);
            $("#reminder-form ")[0].reset();
            $("#reminder-form label.error").remove();
            $('#ReminderModal').modal('hide');
        };
        
    $scope.closeupdateForm = function(){
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.pagination.setPage(1);
            $("#reminder-update-form ")[0].reset();
            $("#reminder-update-form label.error").remove();
            $('#ReminderUpdateModal').modal('hide');
        };
        
        $( document.body ).on( 'click', '.dropdown-menu li', function( event ) {
      var $target = $( event.currentTarget );
      $target.closest( '.btn-group' )
         .find( '[data-bind="label"]' ).text( $target.text() )
            .end()
         .children( '.dropdown-toggle' ).dropdown( 'toggle' );
      return false;

   }); 
        $('#reminder-form').submit(function(e){
            e.preventDefault();
            
//            $('#meeting_desc').val(CKEDITOR.instances.meeting_desc.getData());
            if ($("#reminder-form").valid()){
                $('#loader').show();
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Creating reminder please wait..');
                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('v1/create/reminders');
                $http({
                    url: request_url,
                    method: "POST",
                    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                    .then(function(response) {
                        $("#reminder-form").find('button[type=submit]').attr('disabled',false);
                        $("#reminder-form").find('button[type=submit]').text('Submit');
                        $('#loader').hide();
//                        console.log(response.data.status);
                    var result = response.data; // to get api result 
                        if(result.status){
                            $scope.pagination.total=0;
                            $scope.pagination.offset = 0;
                            $scope.pagination.currentPage = 1;

                            $scope.pagination.setPage(1);
            
                            $scope.closeForm();
                            grit('',"Reminder added successfully!");
                        }else{
                            // To handle server side validation errors 
                               console.log('not input errors check msg');
                           }
//                        }
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });  
            }
        });
        
        $('#reminder-update-form').submit(function(e){
            e.preventDefault();
//            $('#meeting_desc').val(CKEDITOR.instances.meeting_desc.getData());
            if ($('#reminder-update-form').valid()){
                           $('#loader').show();

                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Updating reminder please wait..');
                var records = $.param($('#reminder-update-form').serializeArray());
//                console.log(records);

                var request_url = generateUrl('v1/update/reminders/'+$scope.edit_id);
                $http({
                    url: request_url,
                    method: "POST",
                    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                    .then(function(response) {
                        $("#reminder-update-form").find('button[type=submit]').attr('disabled',false);
                        $("#reminder-update-form").find('button[type=submit]').text('Submit');
                        $('#loader').hide();
//                        console.log(response.data.status);
                        
                    var result = response.data; // to get api result 
                        if(result.status){
                            $scope.pagination.total=0;
                            $scope.pagination.offset = 0;
                            $scope.pagination.currentPage = 1;

                            $scope.pagination.setPage(1);
            
                            $scope.closeupdateForm();
                            grit('',"Reminder updated successfully!");
                        }else{
                            // To handle server side validation errors 
                               console.log('not input errors check msg');
                           }
//                        }
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });  
            }
        });
        
        });
    </script>
    @stop
    @section('content')
    <style>.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #fff;
    opacity: 1;</style>
    <div class="col-md-12" ng-controller="RemindersCtrl">
<!--     <div class="clearfix">
        <div class="row pull-left">
            Select Type:
            <div class="btn-group">
           
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span data-bind="label">Meeting</span>&nbsp;<span class="caret"></span>
            </button>
              <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                <li><a role="menuitem" tabindex="-1" href="javascript:void(0)" ng-click="typeSelect('meeting')">Meeting</a></li>
                <li class="divider"></li>
                <li><a role="menuitem" tabindex="-1" href="javascript:void(0)" ng-click="typeSelect('flat_document')">Flat Document</a></li>
                <li class="divider"></li>
                <li><a role="menuitem" tabindex="-1" href="javascript:void(0)" ng-click="typeSelect('official_communication')">Official Communication</a></li>
                 <li class="divider"></li>
                <li><a role="menuitem" tabindex="-1" href="javascript:void(0)" ng-click="typeSelect('society_document')">Society Document</a></li>
              </ul>
            </div>
        </div>
     </div>-->
<!--     <div class="col-lg-12" style= "height:50px;">
        <div class="btn-toolbar pull-right">
            <button  id="buttonAdd" type="button" class="btn btn-primary " ng-click="addType()">Add New Type</button> 
        </div>
    </div>-->
    <div class="clearfix">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Alert Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
			<tr ng-if="pagination.total == 0" style="margin-left: 30px;">
				<td colspan="4" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
			</tr>	
            <tr ng-if="pagination.total > 0" ng-repeat="type in types" class="edit" on-finish-render="ngRepeatFinished">
                <td>@{{type.name}}</td>
                <td>@{{type.description}}</td>
                <td>@{{type.reminder.alert}}</td>
                <input type="hidden" name="society_id" value="@{{type.society_id}}" />
            
                <td ng-show="(type.reminder===null) ? 1 : 0">
                     <button type="button" class="btn btn-primary" ng-click="openReminder(type.id)">Add Alert</button>
                </td>
                <td ng-show="(type.reminder!==null) ? 1 : 0">
                     <button type="button" class="btn btn-primary" ng-click="openReminderEditForm(type.reminder.id,type.reminder.alert,type.reminder.type_id)">Update Alert</button>
                </td>
            </tr>
            </tbody>
        </table>
        
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
    </div>

    <!-- Modal -->
        <div class="modal fade" id="ReminderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Add Task</h4>
          </div>
          <div class="modal-body">
			  <form id="reminder-form" method="post" action="">
				<div class="form-group">
                  <label class="form-label">Alert</label>
                  <input type="text" class="form-control" name = "alert" maxlength="100"  placeholder="Alert in hours">
                </div>
                 <input type="hidden" id="type_id" name="type_id" value="">
               <button type="submit" class="btn btn-primary">Submit</button>
				<button class="btn btn-primary" type="button" ng-click="closeForm()" >Cancel</button>
              </form>
            
          </div>
        </div>
      </div>
    </div>
    
    <!-- Update Modal -->
    <div class="modal fade" id="ReminderUpdateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="closeupdateForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Task</h4>
                </div>
                <div class="modal-body">
                    <form id="reminder-update-form" method="post" action="">
                        <div class="form-group">
                            <label class="form-label">Alert</label>
                            <input type="text" class="form-control" name = "alert" maxlength="100" value="@{{edit_reminder}}"  placeholder="Alert in hours">
                        </div>
                        <input type="hidden" name="type_id" value="@{{type_id}}">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button class="btn btn-primary" type="button" ng-click="closeupdateForm()" >Cancel</button>
                    </form>
                    <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
                </div>
            </div>
        </div>
    </div>
</div>
   
    <script>
    $('document').ready(function(){
    $("#reminder-form").validate({
                rules: {
                   alert : {
                        required:true,
                        number: true,
                        minlength: 1,
                        maxlength: 2,
                     },
                 },
                 });
     $("#reminder-update-form").validate({
                rules: {
                   alert : {
                        required:true,
                        number: true,
                        minlength: 1,
                        maxlength: 2,
                     },
                 },
                 });
             });
             </script>
@stop
