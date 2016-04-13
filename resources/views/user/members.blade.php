@section('title', 'Members')
@section('panel_title', 'My Flat')
@section('head')
<script src="{!! asset('js/jquery-birthday-picker.min.js') !!}"></script>
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">

<link href="{{ asset('css/dob.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>

<style>
    .member_list .add:first-child .remove_member{display: none;}
</style>
@stop
@section('content')
    <script type="text/javascript">

        app.directive('onFinishRender', function ($timeout) {
            return {
                restrict: 'A',
                link: function (scope, element, attr) {
                    if (scope.$last === true) {
                            scope.$emit('ngRepeatFinished');
                    }
                }
            }
        });
        app.controller("MemberCtrl", function($scope,$http,$filter) {


//            $scope.datepicker();
            $scope.member = {};
            $scope.associate_member = {};
            $scope.input_associate_member = {};
            $scope.members;
            $scope.flats;
            $scope.flat_info;
            $scope.activeIndex = null;
            $scope.flat_id = {{$flat_id}};
			$scope.totalMembers =0;
			$scope.totalAssociateMembers =0;
			$scope.deleteStatus = 0;
//            console.log(flat_id);
                $('#loader').hide();
                $('#loader1').hide();
                $scope.activeType = 'relationship';

//            $('#member_dob, #associate_member_dob').datetimepicker({
//
//            useCurrent : true,
//            format: 'DD-MM-YYYY',
//            maxDate:moment(new Date()).format('YYYY-MM-DD'),
//            ignoreReadonly : true,
//            widgetPositioning: {
//                horizontal: 'left',
//                vertical:'bottom'
//            }
//            });
//            setValue: function () {
//        // EDIT - Don't setValue if date = unix date start
//        if (DPGlobal.formatDate(this.date, this.format) == DPGlobal.formatDate(new Date(1970, 1, 1, 0, 0, 0), this.format)) return false;
//        // END EDIT


            $scope.validateAssociate = function() {
			jQuery(document).ready(function() {
              if(jQuery('#member:checked') && jQuery('#member').prop('checked') && jQuery('#member').attr('checked')){
                  console.log("checked");
            $('#update_email_validate').rules("add",
                    {
                        required: true,
                    });
                 $("label[for='exampleInputEmail']").addClass('form-label');
                 
                 $('#validate_contact').rules("add",
                    {
                        required: true,
                    });
                 $("label[for='exampleInputNumber']").addClass('form-label');
        }else{
             $('#update_email_validate').rules("add",
                    {
                        required: false,
                    });
                 $("label[for='exampleInputEmail']").removeClass('form-label');
                 $('#update_email_validate-error').remove();
                 $('#validate_contact').rules("add",
                    {
                        required: false,
                    });
                 $("label[for='exampleInputNumber']").removeClass('form-label');
                 $('#validate_contact-error').remove();
                 $('#contactNumber-error').hide();
                 $('#email_validate-error').hide();
        }
    });
        return ($scope.member.associate_member == '1') ? 1 : 0
          }

            $scope.addMember = function() {
                 $('#MemberModal').modal('show');
                 $("#example").dateDropdowns({
//                 defaultDate: "2015-11-11",
                    submitFormat: "dd-mm-yyyy",

            });
////                      $scope.member_action = "add";
//                 $scope.getRelations();
               

            }

//            $scope.addAssociateMember = function() {
//                $("#example1").dateDropdowns({
//                    submitFormat: "dd-mm-yyyy",
//                });
//                $('#associateMemberModal').modal('show');
//
//            }
//            $scope.formatDateTime = function(date,time){
//                var dateArray = date.split("-");
//                if(time){
//                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
//                    return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
//                }else{
//                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
//                    return $filter('date')(dateUTC, 'yyyy-MM-dd');
//                }
//            };

            $scope.submit = function() {
//                if (this.member_form.$invalid)
//			return;
//                     e.preventDefault();
            if ($('#member_form').valid()){
                    $('#loader').show();
                    $("#member_form").find('button[type=submit]').attr('disabled',true);
                   $("#member_form").find('button[type=submit]').text('Please wait..');
                    $this = this;
                    $scope.member.dob = $('#example').val();
                    $scope.member.relation_id;
//                    if($scope.member.dob != '' ){
//                        var formatedDate = $scope.formatDateTime($scope.member.dob);
//                        $scope.member.dob =  formatedDate;
//                    }
//
                    $scope.member.flat_id = $scope.flat_id;
                    $http.post(generateUrl('member/create'), $scope.member)

                    .then(function(response)
                    {
                        $("label#member-email-error").remove();
                         $('#loader').hide();
                        $("#member_form").find('button[type=submit]').attr('disabled',false);
                        $("#member_form").find('button[type=submit]').text('Submit');
                        var result = response.data.response;
                        console.log(response.data.response.already_exist);
                        if(result.success)
                        {

                            if($this.member.id == undefined)
                            {
                                $('#MemberModal').modal('hide');
                                grit('',"Members created successfully!");
                                $scope.getMembers();
                                $scope.members.push(response.data.response.data);
                                $scope.member = {};
                                $scope.member_form.$setPristine();
                                location.reload();
                            } else {


                                    $('#MemberModal').modal('hide');
                                    grit('',response.data.response.msg);
                                    $scope.getMembers();
                                    $scope.members[$scope.activeIndex] = response.data.response.data;
                                    //$filter('filter')($scope.members, {id: $scope.member.id}[0] = response.data.response.data;
                                    $scope.member = {};
                                    $scope.member_form.$setPristine();

                                }
                            }if(response.data.response.already_exist)
                            {
                                $("label#email_validate-error").remove();
                                $("label#member-email-error").remove();
                               $( "#email_validate" )
                                        .after( '<label id="member-email-error" class="error" for="email">'+result.msg+'</label>' );
                            }else{
                                console.log("validation error");
                            }

                    });
                    }else{
                            console.log("not validate");
                        }
            }

//             $scope.EditMember = function() {
////                if (this.member_form.$invalid)
////			return;
////                     e.preventDefault();
//            if ($('#member_update_form').valid()){
//                    $('#loader').show();
//                    $("#member_form").find('button[type=submit]').attr('disabled',true);
//                   $("#member_form").find('button[type=submit]').text('Please wait..');
//                    $this = this;
//                    $scope.member.dob = $('#example').val();
//                    $scope.member.associate_member = $('#asso_member').val();
//                    $scope.member.relation_id;
//
////                    if($scope.member.dob != '' ){
////                        var formatedDate = $scope.formatDateTime($scope.member.dob);
////                        $scope.member.dob =  formatedDate;
////                    }
////
//                    $scope.member.flat_id = $scope.flat_id;
//                    $http.post(generateUrl('member/update/'+$this.member.id), $scope.member)
//
//                    .then(function(response)
//                    {
//                        $("label#member-email-error").remove();
//                         $('#loader').hide();
//                        $("#member_form").find('button[type=submit]').attr('disabled',false);
//                        $("#member_form").find('button[type=submit]').text('Submit');
//                        var result = response.data.response;
//                        console.log(response.data.response.already_exist);
//                        if(result.success)
//                        {
//
//                            if($this.member.id == undefined)
//                            {
//                                $('#MemberModal').modal('hide');
//                                grit('',"Members created successfully!");
//                                $scope.getMembers();
//                                $scope.members.push(response.data.response.data);
//                                $scope.member = {};
//                                $scope.member_form.$setPristine();
//                            } else {
//
//
//                                    $('#MemberModal').modal('hide');
//                                    grit('',response.data.response.msg);
//                                    $scope.getMembers();
//                                    $scope.members[$scope.activeIndex] = response.data.response.data;
//                                    //$filter('filter')($scope.members, {id: $scope.member.id}[0] = response.data.response.data;
//                                    $scope.member = {};
//                                    $scope.member_form.$setPristine();
//                                }
//                            }if(response.data.response.already_exist)
//                            {
//                                $("label#email_validate-error").remove();
//                                $("label#member-email-error").remove();
//                               $( "#email_validate" )
//                                        .after( '<label id="member-email-error" class="error" for="email">'+result.msg+'</label>' );
//                            }else{
//                                console.log("validation error");
//                            }
//
//                    });
//                    }else{
//                            console.log("not validate");
//                        }
//            }
            $scope.associateSubmit = function() {
                if (this.associate_member_form.$invalid)
			return;
                    $('#loader1').show();
                    $("#associate_member_form").find('button[type=submit]').attr('disabled',true);
                    $("#associate_member_form").find('button[type=submit]').text('Please wait..');
                    $this = this;
//                    $scope.input_associate_member.dob = $('#associate_member_dob').val();
//                    if($scope.input_associate_member.dob != '' ){
//                        var formatedDate = $scope.formatDateTime($scope.input_associate_member.dob);
//                        $scope.input_associate_member.dob =  formatedDate;
//                    }

                    $scope.input_associate_member.flat_id = $scope.flat_id;
                    $scope.input_associate_member.dob = $('#example1').val();
                    $http.post(generateUrl('associatemember/create'),$scope.input_associate_member)
                    .then(function(response)
                    {
                        $('#loader1').hide();
                        $("#associate_member_form").find('button[type=submit]').attr('disabled',false);
                        $("#associate_member_form").find('button[type=submit]').text('Submit');
                        var result = response.data.response;
                        if(result.success)
                        {
                            grit('',response.data.response.msg);
                            $('#associateMemberModal').modal('hide');
                            $scope.associate_member = result.data;
                            $scope.associate_member_form.$setPristine();
                        }else{
                            grit('',response.data.response.msg);

                        }

                    });
            }

             $scope.close = function() {
//              location.reload();
                $scope.member = {};
                $("#member_form")[0].reset();
                $scope.member_form.$setPristine();
                $("#member_dob").val('');
                 $("#member_form")[0].reset();
            $("#member_form label.error").remove();
            $('#MemberModal').modal('hide');
            };

             $scope.closeUpdateform = function() {
              location.reload();
//                 $scope.member = {};
//                $("#member_update_form")[0].reset();
////                $scope.member_form.$setPristine();
////                $("#dob").val('');
////                 $("#member_update_form")[0].reset();
//            $("#member_update_form label.error").remove();
//            $('#MemberUpdateModal').modal('hide');
            };

            $scope.associateModalClose = function() {
                location.reload();

            };

            $scope.getFlats = function() {
                var request_url = generateUrl('flat/list');
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.flats = result.response.data;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getFlat = function() {
                var request_url = generateUrl('flat/'+$scope.flat_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.flat_info = result.response.data;

                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getMembers = function(deleteStatus) {
				if (typeof deleteStatus == undefined){
					deleteStatus = 0;
				}
               var request_url = generateUrl('member/list/'+$scope.flat_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {

                    $scope.members = result.response.data;
                    $scope.totalMembers = $scope.members.length;
                    if ($scope.totalMembers == 0 ){ $("#dataCheckMembers").text("No Data Found."); }
					if (deleteStatus == 1 && $scope.totalMembers == 0){
						$scope.deleteStatus = 1;
					}else{
						$scope.deleteStatus = 0;
					}
//					if ($scope.totalMembers == 0){
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getAssociateMember = function() {
               var request_url = generateUrl('associatemember/'+$scope.flat_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.associate_member = result.response.data;
					$scope.totalAssociateMembers = result.response.total;
					if ($scope.totalAssociateMembers == 0 ){ $("#dataCheckAssociateMembers").text("No Data Found."); }
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

             $scope.updateMember = function() {
                var $this = this;
                $scope.activeIndex = this.$index;
                $('#MemberUpdateModal').modal('show');
                $scope.member_action = "update";
                $('#loader').show();
                $scope.getRelations();
                $http.get(generateUrl('member/'+$this.member.id))
                .then(function(response) {
                    console.log(response.data.response.member.dob);
                     if(response.data.response.member.dob == "0000-00-00" ) {
                    $("#dob").dateDropdowns({
                       defaultDate:null,
                });
                    }
                    else {
                        $("#dob").dateDropdowns({
                       defaultDate:response.data.response.member.dob,

                   });
                    }
                $scope.member = response.data.response.member;
                $scope.associate = response.data.response.associate;
                 $('#loader').hide();

                });
            }


            $scope.updateAssociateMember = function(associate_member) {
                $scope.input_associate_member= angular.copy(associate_member);
//               if($scope.input_associate_member.dob != '0000-00-00'){
//                   $scope.input_associate_member.dob = moment($scope.input_associate_member.dob).format('YYYY-MM-DD');
//                }else{
//                  $scope.input_associate_member.dob = '';
//               }
                 $("#example1").dateDropdowns({
                        defaultDate:angular.copy($scope.input_associate_member.dob)
                    });

               $('#associateMemberModal').modal('show');
            }

            $scope.removeAssociateMember = function() {
                var r = confirm("Are you sure to delete this member!");
                if (r == true) {
                    $http.post(generateUrl('associatemember/delete/'+$scope.flat_id))
                  .then(function(response) {
                        var result = response.data.response;
                        if(result.success){
                            $scope.associate_member = {};
                            $scope.input_associate_member = {};
                            grit('',response.data.response.msg);
                        }else{

                        }
                  });
                }else{
                    return;
                }

            }

            $scope.getFlats();
            $scope.getFlat();
            $scope.getMembers();
            $scope.getAssociateMember();


            $( "#flat-select" ).change(function() {
				
				var optVal= $("#flat-select option:selected").val();
				$scope.totalMembers = 0;
					$scope.deleteStatus = 1;
					$scope.flat_id = optVal;
					$scope.getFlat();
					$scope.getMembers($scope.deleteStatus);
					$scope.getAssociateMember();
              });

            $scope.remove=function(id){
                 var r = confirm("Are you sure to delete this member!");
                if (r == true) {
                $('.member_list tr.edit#'+id).remove(); // To remove row
                if($('.member_list tr').length == 0){  // If removed last element disable submit button
                    $('#member_form').find('input[type=submit]').attr('disabled',true);
                }
                var request_url = generateUrl('member/delete');
                $http({
                    url: request_url,
                    method: "POST",
                    data:$.param({member_id:id}),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .then(function(response) {
                    grit('',"Member deleted successfully!");
				$scope.totalMembers = 0;
                    $scope.getMembers(1);
                },
                function(response) { // optional
//                       alert("fail");
                });
            } else {
                return;
            }
        }

        $('#member_update_form').submit(function(e){
                e.preventDefault();
                if ($("#member_update_form").valid()){
                    $("#btn_update").attr('disabled',true);
                    $("#btn_update").text('Updating member please wait..');
//                    var records = $.param($( this ).serializeArray());
                     var dob = $('#dob').val();
                     var data = $(this).serializeArray();
                     data.push({name:'dob',value:dob});
                     var records = $.param(data);
                    var request_url = generateUrl('member/update/'+$scope.member.id);
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("label#member-email-error").remove();
                       var result = response.data.response; // to get api result
                       $("#btn_update").attr('disabled',false);
                       $("#btn_update").text('Update');
                        if(result.success){
                              grit('','member updated successfully!');
                              location.reload();

                        }if(response.data.response.already_exist)
                            {
                                $("label#update_email_validate-error").remove();
                                $("label#member-email-error").remove();
                               $( "#update_email_validate" )
                                        .after( '<label id="member-email-error" class="error" for="email">'+result.msg+'</label>' );
                            }



                    },
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });

         $scope.getRelations = function() {
                var request_url = generateUrl('v1/superadmin/list/typeList/'+$scope.activeType);
                  $http.get(request_url)
                .success(function(result, status, headers, config) {
                        $scope.relations = result.results.data;
                console.log($scope.relations);
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
             $scope.getRelations();
             /*Make Associate Member*/

             $scope.AssociateMember = function(member_id)
             {
                 var confirm_msg = confirm("Are you sure to make this member as Associate Member!");
                if(confirm_msg == true)
        {
            var request_url = generateUrl('associatemember/create');
             var records = $.param({member_id:member_id});
                $http({
                    url: request_url,
                    method: "POST",
                    data:records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function(response) {
                    console.log(response);
                    var result = response.data; // to get api result

                    if(result.success){
                        grit('','Amenities deleted successfully!');
                        location.reload();
                    }else{
                        grit('','Error in deleting document');
                    }

//                },
//                function(response) { // optional
//                    alert("fail");
                });
        }else{
                return false;
        }
             };


        });
    </script>
    <style>
        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
            background-color: #fff;
            opacity: 1;
        }
    </style>
    <div class="col-lg-12" ng-controller="MemberCtrl" >
        <div class="row">
                <div class="col-lg-12">
                    Flat/Shop/Office:<select id='flat-select'  name="flat_id" >
                        <option ng-repeat="flat in flats" value='@{{flat.flat_id}}'>@{{flat.building_name+' -'}} @{{(flat.block) ? flat.block+' -' : ''}} @{{flat.flat_no}}</option>
                    </select>

                </div>
        </div>

<!--       <div class="row">
            <div class="col-lg-12">
                <h4 class="pull-left">Associate Member</h4>
                <span class="btn-toolbar pull-right">
                    <a href="javascript:void(0);" id="add_btn" class="btn btn-primary pull-right" ng-if="!associate_member.id" ng-click="addAssociateMember()">Add Associate Member</a>
                </span>
            </div>
        </div>-->
<!--        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped table-bordered ">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Date of Birth</th>
                            <th>Voter Id</th>
                            <th>Unique Id</th>
                            <th>Mobile Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody >
						<tr ng-if="totalAssociateMembers == 0">
							<td colspan="8" style="font-weight: bold;" id="dataCheckAssociateMembers">Fetching Data...</td>
						</tr>
                        <tr >
                            <td>@{{associate_member.first_name}}</td>
                            <td>@{{associate_member.last_name}}</td>
                            <td>@{{associate_member.email}}</td>
                            <td>@{{associate_member.dob != "0000-00-00" ? associate_member.dob : ""| date:'dd-MM-yyyy'}}</td>
                            <td>@{{associate_member.voter_id}} </td>
                            <td>@{{associate_member.unique_id}}</td>
                            <td>@{{associate_member.contact_no}}</td>
                            <td><input type="hidden" name="member_id" value="@{{associate_member.id}}">
                                <a href="" title="edit" class="glyphicon glyphicon-pencil" aria-hidden="true" ng-if="associate_member.id" ng-click="updateAssociateMember(associate_member)"></a>
                                <a href="" title="delete" class="glyphicon glyphicon-remove" aria-hidden="true" ng-if="associate_member.id" ng-click="removeAssociateMember()" ></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>-->

        <div class="row">
            <div class="col-lg-12">
                <h4 class="pull-left">Family Members</h4>
                <span class="btn-toolbar pull-right">
                    <a href="javascript:void(0);" id="add_btn" class="btn btn-primary pull-right" ng-click="addMember()">Add Family Member</a>
                </span>
            </div>
        </div>
<!--        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <input type="hidden" name="flat_id" value="{{$flat_id}}">

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>-->
        <div class="form-group">
                 <table class="table table-striped table-bordered ">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Birth Date</th>
                            <th>Relation</th>
                            <th>Voter Id</th>
                            <th>Unique Id</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Associate Member</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="member_list">
                        <tr id="fetch" ng-if="totalMembers == 0 && deleteStatus != 1">
							<td colspan="9" style="font-weight: bold;" id="dataCheckMembers">Fetching Data...</td>
						</tr>
						<tr ng-if="deleteStatus == 1" >
								<td colspan="9" style="font-weight: bold;" id="noDataCheck">No Data Found.</td>
							</tr>
                        <tr ng-if="totalMembers > 0" ng-repeat="member in members" class="edit" id="@{{member.id}}"  on-finish-render="ngRepeatFinished">
                            <td>@{{member.first_name + ' ' + (member.last_name == null? '': member.last_name) }}</td>
                            <td>@{{member.dob != "0000-00-00" ? member.dob : ""| date:'dd-MM-yyyy'}}</td>
                            <td>@{{member.relation}}</td>
                            <td>@{{member.voter_id}} </td>
                            <td>@{{member.unique_id}}</td>
                            <td>@{{member.contact_no}}</td>
                            <td>@{{member.email}}</td>
                            <td class ng-show="member.associate_member == '1'">Associate Member</td>
                            <td ng-show="member.associate_member == '0'">Member</td>
                            <td><input type="hidden" name="member_id" value="@{{member.id}}">
                                <a href="" title="edit" class="glyphicon glyphicon-pencil" aria-hidden="true" ng-click="updateMember()"></a>
                                <a href="" title="delete" class="glyphicon glyphicon-remove" aria-hidden="true" ng-click="remove(member.id)" ></a>
                                <!--<a class="glyphicon glyphicon-check" ng-click="AssociateMember(member.id)" title="Marked as Associate Member" href=""></a>-->
                            </td>
                        </tr>
                    </tbody>
                </table>
        </div>



    <div id="MemberModal"  class="modal fade" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button  type="button" class="close" data-dismiss="modal" ng-click="close()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title form-label">@{{ member.id != null ? 'Update ' : 'Add New '}}Member</h4>
                </div>
                <div class="modal-body">

                    <div>
                        <form ng-submit="submit()" id="member_form" name="member_form" novalidate>
                            <input type="hidden" ng-model="member.action" value="@{{member_action}}" />
                        <div class="form-group">
                            <label class="form-label"   for="exampleInputEmail1">First Name</label>
                            <input ng-model="member.first_name" name="first_name"class="form-control form-label" id="exampleInputEmail1" placeholder="First Name" required ng-minlength="2" ng-pattern="/^(\D)+$/">
                            <label class="error"
                                ng-show="member_form.$submitted && member_form.name.$invalid">
                                Please enter valid name
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-label"   for="exampleInputEmail1">Last Name</label>
                            <input ng-model="member.last_name" name="last_name"class="form-control form-label" id="exampleInputEmail1" placeholder="Last Name" required ng-minlength="2" ng-pattern="/^(\D)+$/">
                            <label class="error"
                                ng-show="member_form.$submitted && member_form.name.$invalid">
                                Please enter valid name
                            </label>
                        </div>
                            <div class="form-group" >
                            <label for="relation_id" >Date of Birth</label>
                            </div>
                            <div class="date-dropdowns example form-group" style="margin-bottom: -5px;margin-top: -45px;margin-left: -55px;" >
                                <input  ng-model="member.dob"
                                    id="example" placeholder="Date of Birth"
				>
                            </div>

                            <div class="form-group">
                           <label for="relation_id" >Relationship</label>
                           <select name="relation_id" class="form-control"
                                    ng-model="member.relation_id"
                                    ng-options="relation.id as relation.name for relation in relations"
                                    >
                                <option value="" selected="">Select Relation</option>
                            </select>
<!--                           <label for="relation_id" >Relationship</label>
                           <select id="relation_id" name="relation_id" ng-model="member.relation_id" class="form-control">
                                <option value="" disabled="" selected="">Select a Relation</option>
                                <option ng-repeat="relation in relations" value='@{{relation.id}}' ng-selected="relation.id == member.category_id">@{{relation.name}}</option>
                            </select>-->
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Voter Id</label>
                            <input ng-model="member.voter_id"class="form-control form-label" type="text" name="voterId" placeholder="Voter Id"ng-minlength="6">
                                <label class="error"
                                    ng-show="member_form.$submitted && member_form.voterId.$invalid">
                                    Please enter valid Voter Id
                                </label>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Unique Id</label>
                            <input ng-model="member.unique_id" type="text"  class="form-control" name="uniqueId"  placeholder="Unique Id" ng-minlength="6">
                                <label class="error"
                                    ng-show="member_form.$submitted && member_form.uniqueId.$invalid">
                                    Please enter valid Unique id
                                </label>
                        </div>
<!--                        <div class="form-group">
                            <label for="exampleInputEmail1">Contact Number</label>
                            <input  ng-model="member.contact_number" type="text" class="form-control" name="contactNumber" ng-pattern = "onlyNumber" placeholder="Contact Number " ng-maxlength="10" ng-minlength='10' ng-pattern="/^[0-9]+$/">
                                <label class="error"
                                    ng-show="member_form.$submitted && member_form.contactNumber.$invalid" >
                                    Please enter valid number
                                </label>
                        </div>-->

                            <div class="form-group">
                            <label for="exampleInputNumber">Contact Number</label>
                            <input type="text" class="form-control" name="contact_no" ng-model="member.contact_no" placeholder="Contact Number">
                        </div>

<!--                       <div class="form-group">
                            <label for="exampleInputEmail1">Email</label>
                            <input ng-model="member.email" class="form-control" id="email_validate" type="text" name="email" value="fgfg" placeholder="Email"
                                   ng-minlength="10" ng-pattern="/^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/" >
                            <label class="error"
                                    ng-show="member_form.$submitted && member_form.email.$invalid">
                                    Please enter valid email
                            </label>
                       </div>-->

                       <div class="form-group">
                            <label for="exampleInputEmail">Email</label>
                            <input class="form-control" id="email_validate" type="text" name="email" placeholder="Email" value=""
                                ng-model="member.email" ng-minlength="10">
                       </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Associate Member</label>
                                <input type="checkbox" id="asso_member" name="is_associative" ng-model="member.associate_member" ng-checked="(member.associate_member=='1') ? 1 : 0 ">
                            </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">@{{ member.id != null ? 'Update ' : 'Submit'}}</button>
                            <button  type="button" class="btn btn-primary" data-dismiss="modal" ng-click="close()">Cancel</button>
                        </div>
                    </form>
                        <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                        <div id="loader" class="loading">Loading&#8230;</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="MemberUpdateModal"  class="modal fade" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button  type="button" class="close" data-dismiss="modal" ng-click="closeUpdateform()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title form-label">Update Member</h4>
                </div>
                <div class="modal-body">

                    <div>
                        <form id="member_update_form" name="member_form" novalidate>
                        <div class="form-group">
                            <label class="form-label" for="exampleInputFirstName">First Name</label>
                            <input name="first_name"class="form-control form-label" id="exampleInputEmail1" placeholder="First Name" value="@{{member.first_name}}">
                        </div>
                        <div class="form-group">
                            <label class="form-label"   for="exampleInputLastName">Last Name</label>
                            <input name="last_name"class="form-control form-label" id="exampleInputEmail1" placeholder="Name" value="@{{member.last_name}}">
                        </div>
                        <div class="form-group" >
                            <label for="relation_id" >Date of Birth</label>
                        </div>
                        <div class="date-dropdowns example form-group" style="margin-bottom: -5px;margin-top: -45px;margin-left: -55px;" >
                            <input id="dob" name="dob" placeholder="Date of Birth" value="@{{member.dob}}" >
                        </div>

<!--                        <div class="form-group">
                           <label for="relation_id" >Relationship</label>
                           <select name="relation_id" class="form-control"

                                    ng-options="relation.id as relation.name for relation in relations"
                                    >
                                <option value="" selected="">Select Relation</option>
                            </select>
                           <label for="relation_id" >Relationship</label>
                           <select id="relation_id" name="relation_id" ng-model="member.relation_id" class="form-control">
                                <option value="" disabled="" selected="">Select a Relation</option>
                                <option ng-repeat="relation in relations" value='@{{relation.id}}' ng-selected="relation.id == member.category_id">@{{relation.name}}</option>
                            </select>
                        </div>-->
                            <div class="form-group">
                            <label>Select Relation</label>
                            <select name="relation_id" class="form-control">
                                <option value="" disabled="">Select Relation</option>
                                <option ng-repeat="relation in relations" value='@{{relation.id}}' ng-selected="member.relation_id == relation.id">@{{relation.name}}</option>
                            </select>
                         </div>

                        <div class="form-group">
                            <label for="exampleInputVoterId">Voter Id</label>
                            <input class="form-control form-label" type="text" name="voterId" placeholder="Voter Id" value="@{{member.voter_id}}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputUniqueId">Unique Id</label>
                            <input type="text"  class="form-control" name="uniqueId"  placeholder="Unique Id" value="@{{member.unique_id}}">
                        </div>
<!--                        <div class="form-group">
                            <label for="exampleInputEmail1">Contact Number</label>
                            <input  ng-model="member.contact_number" type="text" class="form-control" name="contactNumber" ng-pattern = "onlyNumber" placeholder="Contact Number " ng-maxlength="10" ng-minlength='10' ng-pattern="/^[0-9]+$/">
                                <label class="error"
                                    ng-show="member_form.$submitted && member_form.contactNumber.$invalid" >
                                    Please enter valid number
                                </label>
                        </div>-->

                            <div class="form-group">
                            <label for="exampleInputNumber">Contact Number</label>
                            <input type="text" class="form-control" id="validate_contact" name="contact_no" value="@{{member.contact_no}}" placeholder="Contact Number">
                        </div>

                       <div class="form-group">
                            <label for="exampleInputEmail">Email</label>
                            <input class="form-control" id="update_email_validate" type="text" name="email" placeholder="Email"
                               value="@{{member.email}}">
                       </div>
						<div class="form-group">
							<label for="exampleInputAssociateMember">Associate Member</label>
								<input type="checkbox" name="checkbox-associate_member" id="member" ng-checked="validateAssociate()">
							</label>
						</div>
					</div>
                        <input type="hidden" name="flat_id" value="@{{member.flat_id}}">
                        <input type="hidden" id="associate_member" name="associate_member" value="@{{member.associate_member}}">
                        <div class="form-group">
                            <button type="submit" id="btn_update" class="btn btn-primary">Update</button>
                            <button  type="button" class="btn btn-primary" data-dismiss="modal" ng-click="closeUpdateform()">Cancel</button>
                        </div>
                    </form>
<!--                        <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                        <div id="loader" class="loading">Loading&#8230;</div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>

<!--    <div id="associateMemberModal"  class="modal fade" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button  type="button" class="close" data-dismiss="modal" ng-click="associateModalClose()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title form-label">@{{ associate_member.id != null ? 'Update ' : 'Add  '}} Associate Member</h4>
                </div>
                <div class="modal-body">

                    <div>
                        <form ng-submit="associateSubmit()" id="associate_member_form" name="associate_member_form" novalidate>
                        <div class="form-group">
                            <label class="form-label"   for="exampleInputEmail1">First Name</label>
                            <input ng-model="input_associate_member.first_name" name="first_name" class="form-control form-label"  placeholder="First Name" required ng-minlength="2" ng-pattern="/^(\D)+$/">
                            <label class="error"
				ng-show="associate_member_form.$submitted && associate_member_form.first_name.$invalid">
                                Please enter valid name
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-label"   for="exampleInputEmail1">Last Name</label>
                            <input ng-model="input_associate_member.last_name" name="last_name" class="form-control form-label"  placeholder="Last Name" required ng-minlength="2" ng-pattern="/^(\D)+$/">
                            <label class="error"
                                    ng-show="associate_member_form.$submitted && associate_member_form.last_name.$invalid">
                                Please enter valid name
                            </label>
                        </div>
                            <div class="form-group" >
                                <label for="relation_id" >Date of Birth</label>
                            </div>
                            <div class="date-dropdowns example form-group" style="margin-bottom: -5px;margin-top: -45px;margin-left: -55px;" >
                                <input  ng-model="input_associate_member.dob"
                                    id="example1" placeholder="Date of Birth" >
                            </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input ng-model="input_associate_member.email" class="form-control" type="text" name="email" value="" required placeholder="Email"
                                   ng-minlength="10" ng-pattern="/^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/" >
                            <label class="error"
                                    ng-show="associate_member_form.$submitted && associate_member_form.email.$invalid">
                                    Please enter valid email
                            </label>
                       </div>
                        <div class="form-group">
                            <label class="form-label">Mobile No</label>
                            <input  ng-model="input_associate_member.contact_no" type="text" class="form-control" name="contact_no" required  placeholder="Mobile Number" ng-maxlength="10" ng-minlength='10' ng-pattern="/^[0-9]+$/">
                                <label class="error"
                                    ng-show="associate_member_form.$submitted && associate_member_form.contact_no.$invalid" >
                                    Please enter valid mobile number
                                </label>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Voter Id</label>
                            <input ng-model="input_associate_member.voter_id"class="form-control form-label" type="text" name="voter_id" placeholder="Voter Id" ng-minlength="6">
                                <label class="error"
                                    ng-show="associate_member_form.$submitted &&associate_member_form.voterId.$invalid">
                                    Please enter valid Voter Id
                                </label>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Unique Id</label>
                            <input ng-model="input_associate_member.unique_id" type="text"  class="form-control" name="unique_id"  placeholder="Unique Id" ng-minlength="6">
                                <label class="error"
                                    ng-show="associate_member_form.$submitted && associate_member_form.uniqueId.$invalid">
                                    Please enter valid Unique id
                                </label>
                        </div>


                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">@{{ associate_member.id != null ? 'Update ' : 'Submit'}}</button>
                            <button  type="button" class="btn btn-primary" data-dismiss="modal" ng-click="associateModalClose()">Cancel</button>
                        </div>
                    </form>

                    </div>
                        <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                        <div id="loader1" class="loading">Loading&#8230;</div>
                </div>
            </div>
-->
    <!--</div>-->
<!--</div>-->
    <script>
        $('document').ready(function(){
            $('#add_btn').on('click',function(){
                $('.member_list').append($('#new_member table tbody').html());
                $('#member_form').find('input[type=submit]').attr('disabled',false);
                $('.member_list .add:first-child .remove_member').show();
                $('.member_list .add .dob:last').datetimepicker({
                    useCurrent : true,
                    format: 'DD-MM-YYYY',
                    maxDate:moment(new Date()),
                    ignoreReadonly : true,
                    widgetPositioning: {
                        horizontal: 'left',
                        vertical:'bottom'
                     }
                });
            });

            $('.member_list').on('click','.remove_member',function(e){
                $(this).parents('.add').remove();

                if(!$('.member_list tr').length){
                    $('#member_form').find('input[type=submit]').attr('disabled',true);

                }

                if($('.member_list tr.add').length == 1){
                   $('.member_list .add:first-child .remove_member').hide();
                }

                if($('.member_list tr.add').length > 1){
                   $('.member_list .add:first-child .remove_member').show();
                }
                return false;
            });
        });




    </script>
   <script>
	$(document).ready(function(){
		$('#asso_member').on('click',function(){
			if($(this).attr('checked')){
				$('#asso_member').val("1");
//                var email = $('#email_validate').val();
//                alert(email);
                $('input[name="email"]').rules("add",
                    {
                        required: true,
                    });
                 $("label[for='exampleInputEmail']").addClass('form-label');
                 $('input[name="contact_no"]').rules("add",
                    {
                        required: true,
                    });
                 $("label[for='exampleInputNumber']").addClass('form-label');
			}else{
				$('#asso_member').val("0");
                $('input[name="email"]').rules("add",
                    {
                        required: false,
                    });
                 $("label[for='exampleInputEmail']").removeClass('form-label');
                 $('input[name="contact_no"]').rules("add",
                    {
                        required: false,
                    });
                 $("label[for='exampleInputNumber']").removeClass('form-label');
                 $('#contactNumber-error').hide();
                 $('#email_validate-error').hide();
			}
		});

        $("#member_form").validate({
                rules: {
                      // simple rule, converted to {required:true}
                    first_name: {
                        required:true,
                        minlength: 2,
                    },
                    last_name: {
                        required:true,
                        minlength: 2,
                    },
                    email: {
                        domain: true,
                    },
                    contact_no: {
                        maxlength:10,
                        minlength:10,
                        number:true,
                    },
                },
                messages: {
                    email: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter a valid email address."
                    },
                    contactNumber : {
                        number: "Please enter valid mobile number",
                        maxlength:"Please enter valid mobile number",
                        minlength:"Please enter valid mobile number"
                    },
                },
            });

        jQuery.validator.addMethod("domain", function(value, element) {
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
	});
	</script>
  	<script>
	$(document).ready(function(){
		$('#member').on('click',function(){
			if($(this).attr('checked')){
				var name = $(this).attr('name');
				var value = name.split('-').slice(-1);
				$('#'+value).val(1);
                $('#update_email_validate').rules("add",
                    {
                        required: true,
                    });
                 $("label[for='exampleInputEmail']").addClass('form-label');
                 $('#validate_contact').rules("add",
                    {
                        required: true,
                    });
                 $("label[for='exampleInputNumber']").addClass('form-label');

			}else{
				var name = $(this).attr('name');
				var value = name.split('-').slice(-1);
				$('#'+value).val(0);
                $('#asso_member').val("0");
                $('#update_email_validate').rules("add",
                    {
                        required: false,
                    });
                 $("label[for='exampleInputEmail']").removeClass('form-label');
                  $('#update_email_validate-error').remove();
                 $('#validate_contact').rules("add",
                    {
                        required: false,
                    });
                 $("label[for='exampleInputNumber']").removeClass('form-label');
                $('#validate_contact-error').remove();
                 $('#contactNumber-error').hide();
                 $('#email_validate-error').hide();
			}
		});
        $("#member_update_form").validate({
                rules: {
                      // simple rule, converted to {required:true}
                   first_name: {
                        required:true,
                        minlength: 2,
                    },
                    last_name: {
                        required:true,
                        minlength: 2,
                    },
                    email: {
                        domain: true,
                    },
                    contact_no: {
                        maxlength:10,
                        minlength:10,
                        number:true,
                    },
                },
                messages: {
                    email: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter a valid email address."
                    },
                    contactNumber : {
                        number: "Please enter valid mobile number",
                        maxlength:"Please enter valid mobile number",
                        minlength:"Please enter valid mobile number"
                    },
                },
            });

        jQuery.validator.addMethod("domain", function(value, element) {
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
    });
	</script>
@stop
