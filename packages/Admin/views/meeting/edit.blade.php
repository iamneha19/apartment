@section('title', 'Edit Meeting') @section('panel_title', 'Edit
Meeting') @section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}"
	rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@stop @section('content')
<script type="text/javascript">
        app.controller("MeetingListCtrl", function($scope,$http,$filter) {
            $scope.meeting;
            $scope.getMeetingType;
            $scope.type = 'Meeting';
            $scope.attendee;
            $scope.attendee_type = 0;
            $('#loader').hide();
            $scope.getRoles = function(buildingId) {
                var options = {};
                if(buildingId){
                   options = {building_id:buildingId} 
                }
                var request_url = generateUrl('acl/role/list',options);
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.roles = result.response;
//                        console.log($scope.roles);
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
            
            $scope.getMeetingType = function() {
                var request_url = generateUrl('v1/category/type/list/'+ $scope.type);
                  $http.get(request_url)
                .success(function(response, status, headers, config) {  
                        $scope.meeting_type = response.results;
//                        console.log(result.results);
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
            
            $scope.getMeeting = function(id) {
               var request_url = generateUrl('meeting/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.meeting = result.response.post;
                    
//                    $scope.attendee = result.response.post.attendees;
                   if($scope.meeting.attendees=='M')
                   {
                       $scope.attendee_type = 1;
                       if($scope.meeting.building_id){
                           $scope.getRoles($scope.meeting.building_id);
                       }else{
                          $scope.getRoles();
                       }
                       
                       $scope.role_ids = result.response.role_ids;
                   }
//                    console.log($scope.role_ids);
                    
                    $scope.getMeetingType();
                    $scope.meeting.date = moment( $scope.meeting.date).toDate(); // Converting to UTC date
					CKEDITOR.replace( 'meeting_desc' );
//					console.log($scope.meeting.date);
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            $scope.getMeeting({{$id}});
            
            $scope.getAttendee = function()
            {
                $scope.attendee_type = 1;
                $scope.getRoles();
//                $scope.role_ids = result.response.role_ids;
            }
            
          $scope.formatDateTime = function(date,time){
                var dateArray = date.split("-");
                if(time){
//                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
               var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time).toDate();
                    return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
                }else{
//                   var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
                     var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]).toDate();
               
                   return $filter('date')(dateUTC, 'yyyy-MM-dd'); 
                }
                
          };
          
        $scope.getselectedroles = function(value)
        {
            if($.inArray(value, $scope.role_ids)!='-1'){
                return true;
            }else{
                return false;
            }
       };

            $scope.openForm = function(){
                $('#formModal').modal();
            };

	$('#meeting-form').submit(function(e){
                e.preventDefault();
                $('#meeting_desc').val(CKEDITOR.instances.meeting_desc.getData());
                if ($("#meeting-form").valid()){
                	$('#loader').show();
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Updating meeting please wait..');
                    var  enteredDate =  $('#meeting_date').val();
					var new_date = enteredDate.split(' ');
					var formatedDate = $scope.formatDateTime(new_date[0]);
//                                        console.log(formatedDate);
					var dateUTC =  moment(new_date[1]+' '+new_date[2], ["h:mm A"]).format("HH:mm");
//                                        console.log(formatedDate+' '+dateUTC);
                                        var new_date_time = formatedDate+' '+dateUTC;
//					var convertedDate = $filter('date')(new_date_time, 'yyyy-MM-dd HH:mm:ss'); 
					$('#meeting_date').val(new_date_time);
					var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('meeting/update/'+$scope.meeting.id);
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                    	$('#loader').hide();
                         var result = response.data.response; // to get api result 
                        if(result.success){
                            grit('',result.msg);
                            $("#meeting-form").find('button[type=submit]').attr('disabled',false);
                            $("#meeting-form").find('button[type=submit]').text('Submit');
                            window.location="{{route('admin.meeting')}}";  
                        }else{
                            window.location="{{route('admin.meeting')}}"; 
                            console.log("error");
                        }
//                    }, 
//                    function(response) { // optional
//                           alert("fail");
                    });
                }
                
            }); 
        });
    </script>
<div class="col-lg-12" ng-controller="MeetingListCtrl">
	<div class="row">
		<div class="col-lg-6">
			<form id="meeting-form" method="post" action="">

				<div class="form-group">
					<label class="form-label">Date</label> <input id="meeting_date"
						name="date" class="form-control" placeholder="Date"
						value="@{{meeting.date| date:'dd-MM-yyyy hh:mm a'}}">
				</div>
				<div class="form-group">
					<label class="form-label">Brief Topic</label> <input type="text"
						class="form-control" name="title" maxlength="100"
						value=@{{meeting.title}} placeholder="Title">
				</div>

				<div class="form-group">
					<label class="form-label">Venue</label> <input type="text"
						class="form-control" name="venue" maxlength="100"
						placeholder="Venue" value="@{{meeting.venue}}">
				</div>

				<div class="form-group">
					<label for="exampleInputPassword1">Notes</label>
					<textarea id="meeting_desc" class="form-control" name="description"
						value="@{{meeting.description}}" placeholder="Notes">@{{meeting.description}}</textarea>
				</div>

				<div class="form-group">
					<label for="exampleInputPassword1">Agenda</label> <input
						type="text" class="form-control" name="agenda"
						value="@{{meeting.agenda}}" maxlength="50" placeholder="Agenda">
				</div>

				<div class="form-group">
					<label class="form-label">Attendees</label></br>
					<div class="radio-inline">
						<label> <input type="radio" name="attendees" class="attendees"
							ng-checked="(meeting.attendees == 'O') ? 1 : 0 " value="O"
							disabled="">Owner
						</label>
					</div>
					<div class="radio-inline">
						<label> <input type="radio" name="attendees" class="attendees"
							ng-checked="(meeting.attendees == 'A') ? 1 : 0 " value="A"
							disabled="">All Members
						</label>
					</div>
					<div class="radio-inline">
						<label> <input type="radio" name="attendees" class="attendees"
							ng-checked="(meeting.attendees == 'M') ? 1 : 0 "
							ng-click="getAttendee()" value="M" disabled="">Role Based Members
						</label>
					</div>
				</div>

				<div ng-show="attendee_type">
					<div id="member_roles" class="form-group">
						<label class="form-label">Member Roles</label> 
						<select
							name="role_id[]" class="form-control" multiple="multiple">
							<option value="" disabled="" selected="">Select Roles</option>
							<option ng-repeat="role in roles" value='@{{role.id}}'
								ng-selected="getselectedroles(role.id)">@{{role.role_name}}</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="form-label">Type</label> <select name="type_id"
						class="form-control">
						<option value="" disabled="" selected="">Select Type</option>
						<option ng-repeat="type in meeting_type" value='@{{type.id}}'
							ng-selected="meeting.type_id == type.id">@{{type.name}}</option>
					</select>
				</div>

				<!--				<div class="form-group">
                    <label for="exampleInputPassword1">Send alert </label></br>
						<div class="checkbox-inline">
							<label>
								<input type="checkbox" class="send_alert" name="checkbox_hour" ng-checked="(meeting.hour == '1') ? 1 : 0 ">Hour before Meeting 
							</label>
						</div></br>
                                                <div class="checkbox-inline">
							<label>
								<input type="checkbox" class="send_alert"  name="checkbox_day" ng-checked="(meeting.day == '1') ? 1 : 0 " >Day before Meeting 
							</label>
						</div></br>
                                                <div class="checkbox-inline">
							<label>
								<input type="checkbox" class="send_alert" name="checkbox_week" ng-checked="(meeting.week == '1') ? 1 : 0 " >Week before Meeting 
							</label>
						</div></br>
					</div>
					<input type="hidden" id="hour" name="hour" value="@{{meeting.hour}}">
					<input type="hidden" id="day" name="day" value="@{{meeting.day}}">
					<input type="hidden" id="week" name="week" value="@{{meeting.week}}">-->

				<button type="submit" class="btn btn-primary">Update</button>
				<button class="btn btn-primary" type="button"
					onclick="javascript:window.location.href='<?php echo route('admin.meeting') ?>'">Cancel</button>

			</form>
			<link href="{!! asset('css/loader.css') !!}" rel="stylesheet"
				type="text/css" />
			<div id="loader" class="loading">Loading&#8230;</div>
		</div>
	</div>
</div>
<script>
        $('document').ready(function(){
            $('#meeting_date').datetimepicker({
                useCurrent : true,
                sideBySide: true,
                minDate:moment(new Date()),
                format: 'DD-MM-YYYY hh:mm a',
                widgetPositioning: {
                    horizontal: 'left',
                    vertical:'bottom'
                 }
            });
            
            $("#meeting-form").validate({
                rules: {
                    date:"required",
                    title:"required",
                    venue:"required",
                    type_id:"required",
                    'role_id[]':"required",
                }
            });
        });
    </script>
<!--	<script>
	$(document).ready(function(){
		$('.send_alert').on('click',function(){
			if($(this).attr('checked')){
				var name = $(this).attr('name');
				var value = name.split('_').slice(-1);
				$('#'+value).val(1);
			}else{
				var name = $(this).attr('name');
				var value = name.split('_').slice(-1);
				$('#'+value).val(0);
			}
		});
	});
	</script>-->
<script>
    $(document).ready(function(){
        $('.attendees').on('click',function(){
            if($(this).attr('checked'))
            {
                var value = $(this).attr('value');
                if(value == 'M')
                {
                    $('#member_roles').show();
                }else{
                    $('#member_roles').hide();
                }
            }
        });
    });
    </script>
@stop
