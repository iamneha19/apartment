@section('title', 'Users Edit')Flats
@section('panel_title','Users')
@section('panel_subtitle','Edit')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("UserCtrl", function($scope,$http,$filter) {
            $scope.user;
            $scope.block;
            $scope.flats;
            $scope.buildings;
            $scope.blocks;
            $scope.flat;
            $scope.getBuildings = function() {

                var request_url = generateUrl('building/list');
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.buildings = result.response;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getBlocks = function(buildingId) {
//                var request_url = generateUrl('building/block/list/'+buildingId);
//                $http.get(request_url)
//                .success(function(result, status, headers, config) {
//                    $scope.blocks = result.response;
//                }).error(function(data, status, headers, config) {
//                    console.log(data);
//                });

                console.log(this);
			$http.get(generateUrl('building/block/list/'+buildingId))
			.then(function(r){
				var blockSelect = $('#select-block_id');
                if(r.data.response.length > 0) {
					jQuery(blockSelect.prev('label')[0]).addClass('form-label');
					blockSelect.rules("add", {required:true});

                } else {
                	jQuery(blockSelect.prev('label')[0]).removeClass('form-label');
                	blockSelect.rules("add", {required:false});
                }

				$scope.blocks = r.data.response;

			});
            };


            $scope.getBuildings();

            $scope.buildingSelect = function(){
                $scope.getBlocks($scope.buildingSelected);
            };

            $('#add_flat-form').submit(function(e){
                e.preventDefault();

                if ($(this).valid()){
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Creating flat please wait..');
                    var data = $(this).serializeArray();
                     data.push({name:'user_id',value:$scope.user.id});
                    var records = $.param(data);
                    console.log(records);
                    var request_url = generateUrl('/user/addflat');
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(r) {
                      var result = r.data.response; // to get api result
                        $( "label#input-flat_no-error" ).remove();
                        $("#add_flat-form").find('button[type=submit]').attr('disabled',false);
                        $("#add_flat-form").find('button[type=submit]').text('Submit');
                        if(result.success){

                           grit('',result.msg);
                           $scope.getFlats();
                           $scope.closeFlatForm();
//                           window.location='<?php echo route('admin.user.edit','') ?>/'+$scope.flat.user_id;

                        } if(result.flat_error){
                          $( "#input-flat_no" ).after( '<label id="input-flat_no-error" class="error" for="input-flat_no">'+result.flat_error+'</label>' );
                        }else{
                            console.log('returned false');
                        }


                    },
                    function(response) { // optional
//                           alert("fail");
                    });
                }
            });
            $scope.closeFlatForm = function(){
//            $scope.user_info = '';
//            $scope.user_input_readonly = false;
            $("#add_flat-form")[0].reset();
            $("#add_flat-form label.error").remove();
//            $('#select-block_id').prev('label').removeClass('form-label');
//            $('#select-block_id').rules("add", {required:false});
            $('#AddFlatModal').modal('hide');

        };

//            $scope.getBlocks = function(id) {
//                var options = {society_id:id};
//                var request_url = generateUrl('block/allData',options);
//                $http.get(request_url)
//                .success(function(result, status, headers, config) {
//                    $scope.block = result.response.data;
//                }).error(function(data, status, headers, config) {
//                    console.log(data);
//                });
//            };
//            $scope.getBlocks({{$society_id}});

            $scope.getUser = function(id) {
                var request_url = generateUrl('user/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.user = result.response.data;
//                    $scope.user.created_at = new Date( $scope.user.created_at); // Converting to UTC date

                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getUser({{$id}});

            $scope.getFlats = function() {
               var request_url = generateUrl('user/flat/list/'+{{$id}});
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.flats = result.response.data;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getFlats();


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

            $scope.openFlatModal = function()
            {
                $('#AddFlatModal').modal();
            };

            $('#user-form').submit(function(e){
                e.preventDefault();
                if ($("#user-form").valid()){
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Updating user please wait..');
                    var  enteredDate =  $('#dob').val();
                    if(enteredDate != '' ){
                        var formatedDate = $scope.formatDateTime(enteredDate);
                        $('#dob').val(formatedDate); // Change format to Y-M-D H:i:s
                    }

                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('user/update/'+$scope.user.id);
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                       var result = response.data.response; // to get api result
                       $("#user-form").find('button[type=submit]').attr('disabled',false);
                        $("#user-form").find('button[type=submit]').text('Update');
                        if(result.success){
                              grit('','User updated successfully!');
                            window.location='<?php echo url('dashboard/admin/user/', $parameters = [], $secure = null) ?>';
                        }else{
                            // To handle server side validation errors
                            if(result.flat_error){
                                $( "#input-flat_no" ).after( '<label id="input-flat_no-error" class="error" for="input-flat_no">This flat is already taken.</label>' );
                            }else if(result.input_errors){
                                var errors = result.input_errors;

                                for (var key in errors) {
                                     var error = errors[key];
                                     for (var index in error) {
                                        $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('input[name="'+key+'"]');
                                     }
                                  }

                            }else{
                                grit('',result.msg);
                                setTimeout(function(){ location.reload(); }, 2000);
                            }
                        }


                    },
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });

             $('#flats-form').submit(function(e){
                e.preventDefault();
               var validated = true;
                $("#flats-form label.error").remove();
               $('.flat-input').each(function(i,v) {
                   if($(this).val() == "" || $(this).val() == 0){
                       $( this ).after( '<br /><label  class="error">Flat no is required.</label>' );
                       validated = false;
                   }
                });

               if(validated){
                   var data = $( this ).serializeArray();
                    var records = $.param($( this ).serializeArray());

                    var request_url = generateUrl('flats/update');
                        $http({
                                url: request_url,
                                method: "POST",
                                data: records,
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                            })
                        .then(function(response) {
                           var result = response.data.response; // to get api result
                            if(result.success){
                                grit('','Flats updated successfully!');
                                window.location='<?php echo url('dashboard/admin/user/', $parameters = [], $secure = null) ?>';

                            }else{
                                if(result.flat_ids){
                                  var flats = result.flat_ids;
                                  $.each(flats,function(index){
                                      console.log(flats[index]);
                                    $( ".flat#"+flats[index] ).append( '<br /><label  class="error">This flat is already taken.</label>' );
                                  });
                                }
                            }
                        },
                        function(response) { // optional
//                            alert("fail");
                        });
               }


            });
            jQuery.validator.addMethod("domain", function(value, element) {
            var  voter_id =  $('#voter_id').val();
            if(voter_id!='')
            {
                if(voter_id != voter_id.match(/^[a-zA-Z0-9]+$/))
                {
                    return false;
              }else{
                     return true;
               }
           }else{
               return true;
           }

          });

          jQuery.validator.addMethod("unique", function(value, element) {
            var  unique_id =  $('#unique_id').val();
            if(unique_id!='')
            {
                if(unique_id != unique_id.match(/^[a-zA-Z0-9]+$/))
                {
                    return false;
              }else{
                     return true;
               }
           }else{
               return true;
           }

          });

          jQuery.validator.addMethod("emailId", function(value, element) {
            if(value!='')
            {
                if(value != value.match(/\S+@\S+\.\S+/))
                {
                    return false;
              }else{
                     return true;
               }
           }else{
               return true;
           }

          });
        jQuery.validator.addMethod("user_name", function(value, element) {
            if(value!='')
            {
                if(value != value.match(/^[a-zA-Z ]+$/))
                {
                    return false;
              }else{
                     return true;
               }
           }else{
               return true;
           }

          });
        });
    </script>
    <script>
    $(document).ready(function(){
        $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
        });
        $('#dob').datetimepicker({
                    useCurrent : true,
                    format: 'DD-MM-YYYY',
                    maxDate:moment(new Date()),
                    ignoreReadonly : true,
                    widgetPositioning: {
                            horizontal: 'left',
                            vertical:'bottom'
                         }

            });

        $("#user-form").validate({
                rules: {
                      // simple rule, converted to {required:true}
                    first_name: {
                required:true,
                minlength: 2,
                user_name:true,
            },
            last_name: {
                required:true,
                minlength: 2,
                user_name:true,
            },
                    type : "required",
                    // email: "required",
//                    contact_no : "required",
                    block_id : "required",
                    voter_id : {
                      minlength:6,domain:true
                    },
                     unique_id : {
                      minlength:6,unique:true
//                      alpha_numeric:true,
                    },
                    contact_no : {
                        required:true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                     },
                    flat_no : {
                        required:true,
                        number: true,
                        remote:{
                            url: generateUrl('society/checkflat'),
                            type: "post",
                            dataType:"json",
                            data: {
                              flat_no: function() {
                                return $( "#input-flat_no" ).val();
                              },
                              block_id: function() {
                                return $( "#select-block_id" ).val();
                              }
                            },
                            success:function(r) {
                                var result = r.response;
                                 if(result.success){
                                     $( "label #input-flat_no-error" ).remove();
                                     return true;
                                 }else{
                                     $( "label #input-flat_no-error" ).remove();
                                     $( "#input-flat_no" ).after( '<label id="input-flat_no-error" class="error" for="input-flat_no">This flat is already taken.</label>' );
                                     return false;
                                 }
                             }
                          }
                    },
                    email: {
                        required: true,
                        emailId: true
                    }
                },
                messages: {
                    voter_id: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter valid voter id!"
                    },
                    unique_id: {
//                        required: "We need your email address to contact you",
                        unique: "Please enter valid unique id!"
                    },
                    email:{
                        emailId: "Please enter a valid email address.",
                    },
                    first_name: {
//                        required: "We need your email address to contact you",
                        user_name: "Special characters and numbers are not allowed."
                    },
                    last_name: {
//                        required: "We need your email address to contact you",
                        user_name: "Special characters and numbers are not allowed."
                    }
                },
                 errorPlacement: function(error, element) {
                    if (element.attr("name") == "type"  ) {
                        $( ".visiblity_error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
            });




            $('.deactivate_user').on('click',function(){
                if($(this).attr('checked')){
                    var name = $(this).attr('name');
                    var value = name.split('_').slice(-1);
                    $('#'+value).val(0);
//                    $(this).val("0");
                }else{
                    var name = $(this).attr('name');
                    var value = name.split('_').slice(-1);
                    $('#'+value).val(1);
                }
            });

             $('.activate_user').on('click',function(){
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

           $('.approved_user').on('click',function(){
                if($(this).attr('checked')){
                    var name = $(this).attr('name');
                    var value = name.split('_').slice(-1);
                    $('#'+value).val(1);
                }else{
                     var name = $(this).attr('name');
                    var value = name.split('_').slice(-1);
                    $('#'+value).val(2);
                }
            });

            $('.commitee_member').on('click',function(){
                if($(this).attr('checked')){
                    var name = $(this).attr('name');
                    var value = name.split('-').slice(-1);
                    $('#'+value).val(1);
                }else{
                      var name = $(this).attr('name');
                    var value = name.split('-').slice(-1);
                    $('#'+value).val(0);
                }
            });
    });
    </script>
    <style>
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: #fff;
        opacity: 1;
    }
    </style>
   <div class="col-md-12" ng-controller="UserCtrl" >
        <!-- Nav tabs -->
      <!--  <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
          <li role="presentation"><a href="#flats" aria-controls="messages" role="tab" data-toggle="tab">Flats</a></li>
        </ul> -->

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="profile">
                <div class="row">
                    <div class="col-lg-6">
                        <form id="user-form" method="post" action="">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name = "first_name" maxlength="20" value="@{{user.first_name}}"  placeholder="First Name">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name = "last_name" maxlength="20" value="@{{user.last_name}}"  placeholder="Last Name">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email address</label>
                                <input type="email" class="form-control" name = "email" maxlength="40" value="@{{user.email}}"  placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Mobile No</label>
                                <input type="text" class="form-control" name = "contact_no"value="@{{user.contact_no}}"  placeholder="Mobile No">
                            </div>
                            <div class="form-group">
                                <label>Dob</label>
                                <input id='dob' type="text" class="form-control" name="dob" value="@{{user.dob|date:'dd-MM-yyyy'}}"  placeholder="Date of birth" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <label>Voter ID</label>
                                <input type="text" class="form-control" id="voter_id" maxlength="10" name = "voter_id" value="@{{user.voter_id}}"  placeholder="Voter ID">
                            </div>
                            <div class="form-group">
                                <label>Unique ID</label>
                                <input type="text" class="form-control" id="unique_id" maxlength="10" name = "unique_id" value="@{{user.unique_id}}"  placeholder="Unique ID">
                            </div>

<!--                            <div class="form-group">
                                <label for="exampleInputPassword1">Is Member of Association Committee?</label>
                                <div class="radio-inline">
                                    <label>
                                        <input type="checkbox" class="commitee_member" name="user-commitee_member" ng-checked="((user.roles) && (user.roles.search('commitee_member') != -1)) ? 1 : 0 ">
                                    </label>
                                </div>
                            </div>-->
<!--                            <div class="form-group" ng-show="user.status==1">
                                <label for="exampleInputPassword1">Check here to De-Activate this User.  </label></br>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="deactivate_user" name="user_status">
                                    </label>
                                </div>
                            </div>

                            <div class="form-group" ng-show="user.status==0">
                                <label for="exampleInputPassword1">Check here to Activate this User. </label></br>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="activate_user" name="user_status">
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" ng-show="user.status==2">
                                <label for="exampleInputPassword1">Check here to Approved this User.  </label></br>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="approved_user" name="user_status">
                                    </label>
                                </div>
                            </div>-->
                            <input type="hidden" id="status" name="status" value="@{{user.status}}">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{route('admin.users')}}" class="btn btn-primary" type="button"  >Cancel</button></a>
                        </form>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="flats">
                <div class="row">
                    <div class="col-lg-12">
                        <span class="pull-right" style="padding: 7px;">
				<button type="button" class="btn btn-primary"
					ng-click="openFlatModal()">Add Flat</button>
			</span>
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Building</th>
                                    <th>Block</th>
                                    <th>Flat/Shop/Office</th>
                                    <th>Type</th>
                                    <th>Occupancy</th>
                                    <th>Status</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="flat in flats">
                                    <td>@{{flat.building_name}}</td>
                                    <td>@{{flat.block}}</td>
                                    <td>@{{flat.flat_no}}</td>
                                    <td>@{{flat.type}}</td>
                                    <td>@{{flat.relation}}</td>
                                    <td>@{{flat.status == 1 ? 'Active' : 'De-Active'}}</td>
                                    <td><a class="glyphicon glyphicon-pencil" title="update"
                                        href="<?php echo route('admin.user.user_flat_edit','');  ?>/@{{flat.user_society_id}}"></a> </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

<!--                    <div class="col-lg-12">
                        <form id="flats-form" method="post" action="" class="form-inline">
                            <div ng-repeat="flat in flats"  class="row" style="margin-bottom: 20px;" >
                                <div class="col-lg-12">
                                     <div class="form-group">
                                        <label class="form-label">Block</label>
                                        <select id='select-block_id' name="flat[@{{flat.user_society_id}}][block_id]" class="form-control">
                                            <option value="2" disabled="">Select Block</option>
                                            <option ng-repeat="block in blocks" value='@{{block.id}}' ng-selected="flat.block_id == block.id">@{{block.block}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-offset-1 flat" id=@{{flat.flat_id}}>
                                        <label class="form-label">Flat No</label>
                                        <input type="text" class="form-control flat-input" maxlength="4" name="flat[@{{flat.user_society_id}}][flat_no]" value="@{{flat.flat_no}}"  placeholder="Flat No">
                                        <input type="hidden" class="form-control"  name="flat[@{{flat.user_society_id}}][flat_id]" value="@{{flat.flat_id}}"  placeholder="Flat No">
                                    </div>
                                    <div class="form-group col-md-offset-1">
                                        <label>Occupancy</label>
                                        <div class="form-group">
                                            <div class="radio-inline">
                                                <label>
                                                    <input type="radio" name="flat[@{{flat.user_society_id}}][relation]" ng-checked="(flat.relation == 'owner') ? 1 : 0 "  value="owner" >
                                                    Owner
                                                </label>
                                            </div>
                                            <div class="radio-inline">
                                                <label>
                                                    <input type="radio" name="flat[@{{flat.user_society_id}}][relation]" ng-checked="(flat.relation == 'tenant') ? 1 : 0 "  value="tenant" >
                                                    Tenant
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>-->
                </div>
            </div>
        </div>
        <div class="modal fade" id="AddFlatModal" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="user">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						ng-click="closeFlatForm()" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Add Flat</h4>
				</div>
				<div class="modal-body">
					<form id="add_flat-form" method="post" action="">
						<div class="form-group">
							<label class="form-label">Building</label>
							<!--<select name="building_id" class="form-control" ng-model="buildingId" ng-change="buildingSelect(building.id)" name="building_id">-->
								<select ng-model="buildingSelected" ng-change="buildingSelect()" name="building_id" class="form-control">
                                                    <option value="" disabled="" selected="">Select Building</option>
								<option ng-repeat="building in buildings" value="@{{building.id}}">@{{building.name}}</option>
							</select>
						</div>
						<div class="form-group">
							<label>Block</label>
                                <select name="block_id"
                                    id='select-block_id' class="form-control">
                                    <option value="" disabled="" selected="">Select Block</option>
                                    <option ng-repeat="block in blocks" value='@{{block.id}}'>@{{block.block}}</option>
                                </select>
						</div>
						<div class="form-group">
							<label class="form-label">Flat No./ Shop No./ Office No</label> <input type="text"
								id='input-flat_no' class="form-control" maxlength="4"
								name="flat_no" placeholder="My Flat No./ Shop No./ Office No">
						</div>
                                              <div class="form-group type-radio-group">
							<label class="form-label">Flat type</label>
							<div class="form-group ">
								<div class="radio-inline">
									<label> <input type="radio" name="type" value="office"> Office
									</label>
								</div>
								<div class="radio-inline">
									<label> <input type="radio" name="type" value="shop"> Shop
									</label>
								</div>
                                                            <div class="radio-inline">
									<label> <input type="radio" name="type" value="flat"> flat
									</label>
                                                            </div>
							</div>
                                                        <div class="visiblity_error"></div>
						</div>
						<div class="form-group type-radio-group">
							<label class="form-label">Occupancy</label>
							<div class="form-group ">
								<div class="radio-inline">
									<label> <input type="radio" name="role" value="owner"> Owner
									</label>
								</div>
								<div class="radio-inline">
									<label> <input type="radio" name="role" value="tenant"> Tenant
									</label>
								</div>
							</div>
                                                        <div class="visiblity_role_error"></div>
						</div>
						<button type="submit" class="btn btn-primary">Submit</button>
						<button class="btn btn-primary" type="button"
							ng-click="closeFlatForm()">Cancel</button>
					</form>
				</div>
			</div>
		</div>
	</div>

<!--        <div class="row">
            <div class="col-lg-6">
                <form id="user-form" method="post" action="">
                    <div class="form-group">
                        <label for="exampleInputEmail1">First Name</label>
                        <input type="text" class="form-control" name = "first_name" value="@{{user.first_name}}"  placeholder="First Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Last Name</label>
                        <input type="text" class="form-control" name = "last_name" value="@{{user.last_name}}"  placeholder="Last Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" name = "email" value="@{{user.email}}"  placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Mobile No</label>
                        <input type="text" class="form-control" name = "contact_no"value="@{{user.contact_no}}"  placeholder="Mobile No">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Block</label>
                        <select id='select-block_id' name="block_id" class="form-control">
                            <option value="2" disabled="">Select Block</option>
                            <option ng-repeat="block in blocks" value='@{{block.id}}' ng-selected="user.block_id == block.id">@{{block.block}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Flat No</label>
                        <input type="text" class="form-control" id="input-flat_no" name = "flat_no" value="@{{user.flat_no}}"  placeholder="Flat No">
                    </div>
                    <div class="form-group">
                        <label>Occupancy</label>
                        <div class="form-group">
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="role" ng-checked="(user.relation == 'owner') ? 1 : 0 "  value="owner" >
                                    Owner
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="role" ng-checked="(user.relation == 'tenant') ? 1 : 0 "  value="tenant" >
                                    Tenant
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Is Member of Association Committee?</label></br>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="commitee_member" name="user-commitee_member" ng-checked="(user.commitee_member == '1') ? 1 : 0 ">
                            </label>
                        </div>
                    </div>
                    <div class="form-group" ng-show="user.status==1">
                        <label for="exampleInputPassword1">Check here to De-Activate this User.  </label></br>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="deactivate_user" name="user_status">
                            </label>
                        </div>
                    </div>

                    <div class="form-group" ng-show="user.status==0">
                        <label for="exampleInputPassword1">Check here to Activate this User. </label></br>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="activate_user" name="user_status">
                            </label>
                        </div>
                    </div>
                    <div class="form-group" ng-show="user.status==2">
                        <label for="exampleInputPassword1">Check here to Approved this User.  </label></br>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="approved_user" name="user_status">
                            </label>
                        </div>
                    </div>
                    <input type="hidden" id="status" name="status" value="@{{user.status}}">
                    <input type="hidden" id="commitee_member" name="commitee_member" value="@{{user.commitee_member}}">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button class="btn btn-default" type="button" onclick="javascript:window.location.href='<? //php echo url('admin/user/', $parameters = [], $secure = null) ?>'">Cancel</button>
                </form>
            </div>
        </div>-->
    </div>
    <script>
        $("#add_flat-form").validate({
                rules: {
                      // simple rule, converted to {required:true}

                    role: "required",
                    type: "required",
                    flat_no : "required",
                    building_id : "required",
//                    flat_no : {
//                        required:true,
//                        number: true,
//                       remote:{
//                           url: generateUrl('society/checkflat'),
//                           type: "post",
//                           dataType:"json",
//                           data: {
//                             flat_no: function() {
//                               return $( "#input-flat_no" ).val();
//                             },
//                             block_id: function() {
//                               return $( "#select-block_id" ).val();
//                             }
//                           },
//                           success:function(r) {
//                               var result = r.response;
//                               $( "label#input-flat_no-error" ).remove();
//                                if(result.success){
//                                    $( "label#input-flat_no-error" ).remove();
//                                    return true;
//                                }else{
//                                    $( "label#input-flat_no-error" ).remove();
//                                    $( "#input-flat_no" ).after( '<label id="input-flat_no-error" class="error" for="input-flat_no">'+result.msg+'</label>' );
//                                    return false;
//                                }
//                            }
//                         }
//                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "type"  ) {
                        $( ".visiblity_error" ).html( error );
                    }else if (element.attr("name") == "role"  ) {
                        $( ".visiblity_role_error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
                 });
        </script>
@stop
