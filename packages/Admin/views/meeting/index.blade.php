@section('title', 'Meeting')
@section('panel_title', 'Meeting')
@section('panel_subtitle', 'List')
@section('head')
    <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@stop
@section('content')
    <script>
	app.controller("MeetingListCtrl", function(URL,paginationServices,$scope,$http,$filter) {
            $scope.meetings;
            $scope.getMeetingType;
            $scope.type = 'Meeting';
            $scope.roles;
            $scope.societyRoles;
            $scope.buildingRoles;
            $scope.SendInvitees;
            $scope.pagination = paginationServices.getNew(5);
            $scope.itemsPerPage = 5;
            $scope.sort = 'created_at';
            $scope.sort_order = 'desc';
            $scope.search='';
            $scope.createPermissionType = {};
            $scope.inputAttendeeType;
            $scope.inputLevelType = 'society';
             $('#loader').hide();

            $scope.getMeetings = function(offset,limit,sort,sort_order,search) {
                var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order}
                if(search){
                   options['search']=search;
                }
                var request_url = generateUrl('meeting/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    if(result.status_code == 200){
                        $scope.meetings = result.response.data;
                        $scope.pagination.total = result.response.total;
                        $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
						if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
                    }else{
                       grit('',result.msg);
                    }

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

            $scope.getRoles = function() {
                var request_url = generateUrl('acl/role/list');
                  $http.get(request_url)
                .success(function(result, status, headers, config) {
                        $scope.roles = result.response;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };

            $scope.SendInvitees = function(id)
            {
                $('#send_invitees_'+id).attr('disabled',true);
                $('#send_invitees_'+id).text('Sending..');
                var request_url = generateUrl('/meeting/invitees/'+id);
                  $http.get(request_url)
                .success(function(result, status, headers, config) {
                       var result = result.response;
                       $('#send_invitees_'+id).attr('disabled',false);
                        $('#send_invitees_'+id).text('Send Invitees');
                        if(result.success)
                        {
                            grit('',result.msg);
                        }else{
                            console.log("error");
                        }
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            }

	//    $scope.getUsers($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
         $scope.getMeetings($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
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
        /*date formatfunction */
        $scope.formatDateTime = function(date,time){
            var dateArray = date.split("-");
            if(time){
//                var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
                  var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time).toDate();
                return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
            }else{
//                var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
                var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]).toDate();
                return $filter('date')(dateUTC, 'yyyy-MM-dd');
            }
        };

        $scope.openMeeting = function(){
            $('#meetingModal').modal();
            $('#date').val('');

            var request_url = generateUrl('getpermissiontype');
            var records = $.param({permission:'meeting.create'});
            $http({
                url: request_url,
                method: "POST",
                data:records,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(response) {
                var result = response.data; // to get api result
                if(result.success){
                    var permission = [];
                    if(result.data.society_permission){
                        permission.push( 'society' );
                        var request_url = generateUrl('acl/role/list');
                            $http.get(request_url)
                          .success(function(result, status, headers, config) {
                                  $scope.societyRoles = result.response;
                          }).error(function(data, status, headers, config) {
                                  console.log(data);
                          });
                    }
                    if(result.data.building_permission){
                       permission.push( 'building' );
                       var building_id = result.data.building_id;
                       var options = {'building_id':building_id};
                       var request_url = generateUrl('acl/role/list',options);
                            $http.get(request_url)
                          .success(function(result, status, headers, config) {
                                  $scope.buildingRoles = result.response;
                          console.log($scope.buildingRoles);
                          }).error(function(data, status, headers, config) {
                                  console.log(data);
                          });
                    }
                    $scope.createPermissionType = permission;

                }else{
                    grit('',result.msg);
                }

//            },
//            function(response) { // optional
//                alert("fail");
            });

//            $scope.getRoles();
            $scope.getMeetingType();

        };

        // Update roles when level (society,building) is changed
        $scope.$watch('inputLevelType', function(newValue, oldValue) {
            if($scope.inputLevelType == 'society' && $scope.createPermissionType.length  == 2){
                    $scope.roles = $scope.societyRoles;

            }
            if($scope.inputLevelType == 'building' && $scope.createPermissionType.length  == 2){
                $scope.roles = $scope.buildingRoles;
            }
        });


        // Update roles when attendee type  is changed
        $scope.$watch('inputAttendeeType', function(newValue, oldValue) {

            if(newValue == 'M'){

                if($scope.createPermissionType.length  == 1){
                    if($scope.societyRoles){
                        $scope.roles = $scope.societyRoles;
                    }

                    if($scope.buildingRoles){
                        $scope.roles = $scope.buildingRoles;
                    }
                }
                console.log($scope.inputLevelType);
                if($scope.inputLevelType == 'society' && $scope.createPermissionType.length  == 2){
                    $scope.roles = $scope.societyRoles;

                }

                if($scope.inputLevelType == 'building' && $scope.createPermissionType.length  == 2){
                    $scope.roles = $scope.buildingRoles;
                }

            }
        });

        $scope.closeMeetingForm = function(){
            $("#meeting-form")[0].reset();
            $("#meeting-form label.error").remove();
            $('#meetingModal').modal('hide');
            $('#member_roles').hide();
           for (instance in CKEDITOR.instances){
                CKEDITOR.instances[instance].setData(" ");
            }
        };

        $('#meeting-form').submit(function(e){
            e.preventDefault();
            $('#meeting_desc').val(CKEDITOR.instances.meeting_desc.getData());
            if ($("#meeting-form").valid()){
                 $('#loader').show();
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Creating meeting please wait..');
                var  enteredDate =  $('#date').val();
                var new_date = enteredDate.split(' ');
                var formatedDate = $scope.formatDateTime(new_date[0]);
//                var dateUTC =  moment(formatedDate+' '+new_date[1]+' '+new_date[2]).toDate();
                var dateUTC =  moment(new_date[1]+' '+new_date[2], ["h:mm A"]).format("HH:mm");
                var new_date_time = formatedDate+' '+dateUTC;
//                var convertedDate = $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
                $('#date').val(new_date_time);
                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('meeting/create');
                $http({
                    url: request_url,
                    method: "POST",
                    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                    .then(function(response) {
                         $('#loader').hide();
                        $("#meeting-form").find('button[type=submit]').attr('disabled',false);
                        $("#meeting-form").find('button[type=submit]').text('Submit');
                        var result = response.data.response; // to get api result
                        if(result.success){
                            $scope.pagination.total=0;
                            $scope.pagination.offset = 0;
                            $scope.pagination.currentPage = 1;

                            $scope.pagination.setPage(1);

                            $scope.closeMeetingForm();
                            grit('',result.msg);
                        }else{
                            // To handle server side validation errors
                           if(result.input_errors){
                               var errors = result.input_errors;
                              $('#meeting-form input[name="date"]').val(''); // To reset meeting date
                               for (var key in errors) {
                                    var error = errors[key];
                                    for (var index in error) {

                                            $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('input[name="'+key+'"]');
                                    }
                                 }
//

                           }else{
                               console.log('not input errors check msg');
                           }
                        }

//                    },
//                    function(response) { // optional
//                        alert("fail");
                    });
            }
        });
    });
</script>

    <div ng-controller="MeetingListCtrl" class="col-lg-12"  id="meeting">
        <div class="row">
            <div class="col-lg-12 form-group">
                 @if($listPermission)
                 <!--<input ng-model='search'  placeholder="Search" style="width: 300px;margin-bottom: -31px">-->
                 <input ng-model='search' class="form-control pull-left" ng-change='searchtopics()' class="pull-left form-control" placeholder="Search By Brief Topic" style="width: 200px;" />
                @endif
                <span class="btn-toolbar pull-right">
                    @if($createPermission)
                    <button type="button" class="btn btn-primary" ng-click="openMeeting()">Create Meeting</button>
                    @endif
                    @if($oldListPermission)
                    <a class="btn btn-primary pull-right" href="{{route('admin.oldmeeting')}}">Old Meetings</a>
                    @endif
                </span>
            </div>
        </div>
        @if(!$listPermission)
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Warning!</strong> You don't have permission to access this page.
            </div>
        @else


        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th style="width:60px;">Sr No.</th>
                                    <th style="width:158px;">
                                        <a href="" ng-click="order('date')">Meeting Date</a>
                                        <span class="sortorder" ng-show="predicate === 'date'" ng-class="{reverse:reverse}"></span>
                                    </th>
                                    <th>
                                        <a href="" ng-click="order('title')">Brief Topic</a>
                                        <span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>
                                    </th>
                                    <th>
                                        <a href="" ng-click="order('venue')">Venue</a>
                                        <span class="sortorder" ng-show="predicate === 'venue'" ng-class="{reverse:reverse}"></span>
                                    </th>
                                    <th>
                                        <a href="" ng-click="order('agenda')">Agenda</a>
                                        <span class="sortorder" ng-show="predicate === 'agenda'" ng-class="{reverse:reverse}"></span>
                                    </th>
                                    <th style="width:96px;">
                                        <a href="" ng-click="order('user_id')">Initiated By</a>
                                        <span class="sortorder" ng-show="predicate === 'user_id'" ng-class="{reverse:reverse}"></span>
                                    </th>
                                    <th style="width:154px;">
                                        <a href="" ng-click="order('created_at')">Created At</a>
                                        <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                                    </th>
                                    <th >
                                        Invitees
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
								<tr ng-if="pagination.total == 0">
									<td colspan="10" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                                </tr>
                                <tr ng-if="pagination.total > 0" ng-repeat="meeting in meetings | filter:search">
                                    <td>@{{meeting.id}}</td>
                                    <td>@{{meeting.date| date:'dd-MM-yyyy hh:mm a'}}</td>
                                    <td>@{{meeting.title}}</td>
                                    <td>@{{meeting.venue}}</td>
                                    <td>@{{meeting.agenda}}</td>
                                    <td>@{{meeting.user_name}}</td>
                                    <td>@{{meeting.created_at}}</td>
                                    <td><button class="btn btn-primary" id="send_invitees_@{{meeting.id}}" title="send invitees" type="button" ng-click="SendInvitees(meeting.id)">Send Invitees</button></td>
                                    <td>
                                        @if($updatePermission)
                                        <a class="glyphicon glyphicon-pencil" title="update" href="<?php echo route('admin.meeting.edit','');  ?>/@{{meeting.id}}"></a>
                                        @endif
        <!--                                <a class="btn btn-default" title="send invitees" href="#" ng-click="SendInvitees(meeting.id)">Send Invitees</a>-->
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

		@endif

        <!-- Modal -->
        <div class="modal fade" id="meetingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="closeMeetingForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create Meeting</h4>
                    </div>
                    <div class="modal-body">
                        <form id="meeting-form" method="post" name="meeting" action="">
                            <div class="form-group">
                                <label class="form-label">Date</label>
                                <input id="date" name="date" class="form-control" placeholder="Date">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Brief Topic</label>
                                <input type="text" class="form-control" name = "title" maxlength="100"  placeholder="Title">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Venue</label>
                                <input type="text" class="form-control" name = "venue" maxlength="100"  placeholder="Venue">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Notes</label>
                                <textarea id="meeting_desc" class="form-control" name="description" placeholder="Notes"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Agenda</label>
                                <input type="text" class="form-control" name = "agenda" maxlength="50"  placeholder="Agenda">
                            </div>
                            <div class="form-group" ng-if="createPermissionType.length == 2">
                                <label class="form-label">Level</label></br>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" ng-model="$parent.inputLevelType"  name="level" value="society" checked>Society
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" ng-model="$parent.inputLevelType"  name="level" value="building">Building
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Attendees</label></br>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" ng-model="inputAttendeeType" class="attendees" name="attendees" value="O" checked>Owner
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" ng-model="inputAttendeeType" class="attendees"  name="attendees" value="A">All Members
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" ng-model="inputAttendeeType" class="attendees" name="attendees" value="M">Role based members
                                    </label>
                                </div>
                            </div>
                            <div id="member_roles" class="form-group" style="display: none;">
                                <label class="form-label">Member Roles</label>
                                <select name="role_id[]" class="form-control" multiple="multiple">
                                      <option value="" disabled="" selected="">Select Roles </option>
                                      <option ng-repeat="role in roles" value='@{{role.id}}'>@{{role.role_name}}</option>
                                  </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Category</label>
                                <select name="type_id" class="form-control">
                                    <option value="" disabled="" selected="">Select Category</option>
                                    <option ng-repeat="type in meeting_type" value='@{{type.id}}'>@{{type.name}}</option>
                                </select>
                            </div>
<!--                            <div class="form-group">
                                <label for="exampleInputPassword1">Send alert </label></br>
                                <div class="checkbox-inline">
                                    <label>
                                        <input type="checkbox" class="send_alert" name="checkbox_hour">Hour before Meeting
                                    </label>
                                </div></br>
                                <div class="checkbox-inline">
                                    <label>
                                        <input type="checkbox" class="send_alert"  name="checkbox_day" >Day before Meeting
                                    </label>
                                </div></br>
                                <div class="checkbox-inline">
                                    <label>
                                        <input type="checkbox" class="send_alert" name="checkbox_week" >Week before Meeting
                                    </label>
                                </div></br>
                            </div>-->
<!--                            <input type="hidden" id="hour" name="hour">
                            <input type="hidden" id="day" name="day">
                            <input type="hidden" id="week" name="week">-->
                            <button type="submit" id="btn" class="btn btn-primary">Submit</button>
                            <button class="btn btn-primary" type="button" ng-click="closeMeetingForm()" >Cancel</button>
                        </form>
                         <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                        <div id="loader" class="loading">Loading&#8230;</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<script>
	$("#meeting-form").validate({
		rules: {
		  // simple rule, converted to {required:true}
		  date:"required",
		  title:"required",
		  venue:"required",
          type_id:"required",
          'role_id[]':"required",
//		   attendees:"required",
		  // description:"required",
		}
	});
	</script>
	<script>
	$('document').ready(function(){
		$('#date').datetimepicker({
			useCurrent : true,
			sideBySide: true,
                        minDate:moment(new Date()),
			format: 'DD-MM-YYYY hh:mm a',
//                        startDate: '16-08-2015',
		});
//                $("#date").on("dp.change", function (ev) {
//                    e.hide();
//                });
	});
	CKEDITOR.replace( 'meeting_desc' );
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
