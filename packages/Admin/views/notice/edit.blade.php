@section('title', 'Edit Notice')
@section('panel_title', 'Edit Notice')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("NoticeCtrl", function($scope,$http,$filter) {
            $scope.notice;
			 $scope.getRoles;
			   $scope.noticeAttendee;
                            $('#loader').hide();

 
            $scope.getNotice = function(id) {
               var request_url = generateUrl('notice/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.notice = result.response;
                    if (result.response.type == 3){
                        $("#member_roles").show();
                    }
//                    $scope.notice.expiry_date = new Date( $scope.notice.expiry_date); // Converting to UTC date
                    $scope.notice.expiry_date = moment($scope.notice.expiry_date).toDate(); // Converting to UTC date
                    CKEDITOR.replace( 'notice_desc' );
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
			 $scope.getNoticeAttendee = function(id) {
               var request_url = generateUrl('/notice/attendee/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                 $scope.noticeAttendee = result.response;
                  for (var i =0; i <$scope.noticeAttendee.length; i++){
                    $("#role_id option[value='" + $scope.noticeAttendee[i].role_id + "']").attr("selected", true);
                 }
                     
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
		 
          
         
            
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

            $scope.openForm = function(){
                $('#formModal').modal();
            };

            $('#notice-form').submit(function(e){
                e.preventDefault();
                 
                $('#notice_desc').val(CKEDITOR.instances.notice_desc.getData()); // Pass ckeditor data to textarea to validate
                if ($("#notice-form").valid()){
                    $('#loader').show();
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Updating notice please wait..');
                    var  enteredDate =  $('#expiry-date').val();
                    var formatedDate = $scope.formatDateTime(enteredDate,'23:59:59');
                    $('#expiry-date').val(formatedDate); // Change format to Y-M-D H:i:s
//                    $('#notice_desc').val(CKEDITOR.instances.notice_desc.getData()); // Pass ckeditor data to textarea
                    var records = $.param($( this ).serializeArray());
//                    console.log(records);
                    var request_url = generateUrl('notice/edit/'+$scope.notice.id);
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                         $('#loader').hide();
                        $("#notice-form").find('button[type=submit]').attr('disabled',false);
                        $("#notice-form").find('button[type=submit]').text('Submit');
                        var result = response.data.response; // to get api result
                        if(result.success){
                            
                            var notice_url = '<?php echo route('admin.noticeboard','');  ?>';
                            notice_url = notice_url.slice(0,-1);
                            window.location=notice_url+'/'+$scope.notice.id;
                        }else{
                           // To handle server side validation errors
                           if(result.input_errors){
                               var errors = result.input_errors;
                               for (var key in errors) {
                                    var error = errors[key];
                                    for (var index in error) {
                                        if (key == "text"  ) {
                                            $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter("#cke_notice_desc");
                                        } else if (key == "type"  ) {
                                            $( ".form-group.type-radio-group" ).append( '<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' );
                                        } else if (key == "status"  ) {
                                            $( ".publish-error" ).html( '<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' );
                                        }else {
                                            $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('input[name="'+key+'"]');
                                        }
                                    }
                                 }
//                               $('<label id="title-error" class="error" for="title" style="display: inline-block;">Title is required.</label>' ).insertAfter('input[name="title"]');

                           }else{
                               console.log('not input errors check msg');
                           }
                        }

                    },
                    function(response) { // optional
//                           alert("fail");
                    });
                }
                
            });
			$scope.getRoles = function() {
                var request_url = generateUrl('acl/role/list');
                  $http.get(request_url)
                .success(function(result, status, headers, config) {
                        $scope.roles = result.response;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
		    $scope.getRoles();
                     $scope.getNoticeAttendee({{$id}});
          $scope.getNotice({{$id}});

        });
    </script>
    <style>
         .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
            background-color: #fff;
            opacity: 1;
        }
    </style>
    <div class="col-lg-12" ng-controller="NoticeCtrl" >
        <div class="row form-group">
                <div class="col-md-12">
                    <a class="btn btn-primary" href="{{ route('admin.noticeboard') }}" ><< Back to notices</a>
                </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                    <form id="notice-form" method="post" action="">
                        <div class="form-group">
                          <label class="form-label">Title</label>
                          <input type="text" class="form-control" name="title" maxlength="50" value="@{{notice.title}}"  placeholder="Title">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Description</label>
                          <textarea id="notice_desc" class="form-control" name="text" placeholder="Description">@{{notice.text}}</textarea>
                        </div>
                        <div class="form-group">
                          <label class="form-label">Expiry date</label>
                          <input class="form-control" id='expiry-date' type="text"  name="expiry_date" value="@{{notice.expiry_date | date:'dd-MM-yyyy'}}"   placeholder="Expiry date" readonly="readonly">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Notice Type</label>
                            <div class="form-group type-radio-group">
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" class="attendees" name="type" ng-checked="(notice.type == 1) ? 1 : 0 "   value="1" >
                                        AGM
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" class="attendees" name="type" ng-checked="(notice.type == 2) ? 1 : 0 "   value="2" >
                                        General
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" class="attendees" name="type" ng-checked="(notice.type == 3) ? 1 : 0 "   value="3" >
                                         Special AGM
                                    </label>
                                </div>
                                <div id="member_roles" class="form-group" style="display: none;">
									<label class="form-label">Member Roles</label>
                                                                        <select name="role_id[]" id="role_id" class="form-control" multiple="multiple" >
										  <option value="" disabled="" >Select Roles </option>
                                                                                  <option ng-if="role.role_name != 'Admin'"  ng-repeat="role in roles"   value='@{{role.id}}'>@{{role.role_name}}</option>
									 </select>
                                </div>
								
                            </div>
                        </div>
                        <div class="form-group">
                          <label class="form-label">Publish</label>
                          <div class="form-group">
                              <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="status" ng-checked="(notice.status == 1) ? 1 : 0 " value="1" >
                                        Yes
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="status" ng-checked="(notice.status == 0) ? 1 : 0 " value="0" >
                                        No
                                    </label>
                                </div>
                                <div class="radio-inline publish-error">
                                </div>
                          </div>

                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a class="btn btn-primary" href="{{ route('admin.noticeboard') }}" >Cancel</a>
                    </form>
                <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
            </div>
        </div>
    </div>
    <script>
        $('document').ready(function(){
            $('#expiry-date').datetimepicker({
                useCurrent : true,
                format: 'DD-MM-YYYY',
                minDate:moment(new Date()).format('YYYY-MM-DD'),
                ignoreReadonly : true,
                widgetPositioning: {
                    horizontal: 'left',
                    vertical:'bottom'
                 }
            });

            $("#notice-form").validate({
                ignore: [],
                rules: {
                    title: {
                        required: true,
                        minlength: 5
                      },
                    text: "required",
                    expiry_date: "required",
                    type : "required",
                    status : "required"
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "text"  ) {
                      error.insertAfter("#cke_notice_desc");
                    } else if (element.attr("name") == "type"  ) {
                        $( ".form-group.type-radio-group" ).append( error );
                    } else if (element.attr("name") == "status"  ) {
                        $( ".publish-error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
            });
        });
    </script>
	    <script>
    $(document).ready(function(){
        $('.attendees').on('click',function(){
            if($(this).attr('checked'))
            {
                var value = $(this).attr('value');
                if(value == '3')
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