@section('title', 'Users')
@section('panel_title','Users')
@section('panel_subtitle','List')

@section('head')
<style>
.type-radio-group .form-group {
	margin-bottom: 0px;
}
</style>
<script src="{!! asset('js/moment.js') !!}"></script>
@stop

@section('content')
<script>
const society_id = "{!! session()->get('user.society_id') !!}";

$('document').ready(function(){

    jQuery('input[type=text]').click(function(e) {
        jQuery('.alert-warning').addClass('hide');
    });

    $('#loader').hide();
    var elm;
    $(':input[type="text"]').change(function() {
        $(this).val($(this).val().trim());
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
            role: "required",
             type: "required",
//            block_id : "required",
            building_id : "required",
             contact_no : {
                required:true,
                number: true,
                minlength: 10,
                maxlength: 10,

             },
            email: {
                required: true,
                domain: true,
            },
            flat_no : {
                required:true,
                number: true,
//               remote:{
//                   url: generateUrl('society/checkflat'),
//                   type: "post",
//                   dataType:"json",
//                   data: {
//                     flat_no: function() {
//                       return $( "#input-flat_no" ).val();
//                     },
//                     block_id: function() {
//                       return $( "#select-block_id" ).val();
//                     }
//                   },
//                   success:function(r) {
//                       var result = r.response;
//                       $( "label#input-flat_no-error" ).remove();
//                        if(result.success){
//                            $( "label#input-flat_no-error" ).remove();
//                            return true;
//                        }else{
//                            $( "label#input-flat_no-error" ).remove();
//                            $( "#input-flat_no" ).after( '<label id="input-flat_no-error" class="error" for="input-flat_no">'+result.msg+'</label>' );
//                            return false;
//                        }
//                    }
//                 }
            }
        },
        messages: {
                    contact_no: {
                        required: "This field is required",
                        number: "Please enter valid mobile number",
                        maxlength:"Please enter valid mobile number",
                        minlength:"Please enter valid mobile number"
                    },
                    email: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter a valid email address."
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
                    }else if (element.attr("name") == "role"  ) {
                        $( ".visiblity_role_error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
    });
});

    app.filter('capitalize', function() {
        return function(input) {
          return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
        }
    });

    app.controller("UserListCtrl", function(URL,paginationServices,$scope,$http,$filter) {

        $scope.users;
        $scope.blocks;
        $scope.user_input_readonly = false;
        $scope.pagination = paginationServices.getNew(10);
        $scope.pagination.itemsPerPage = 10;
        $scope.sort = 'flat';
        $scope.sort_order = 'asc';
        $scope.search='';
        $scope.block = '';
        $scope.buildings;
		$scope.noData = 1;

		$http.get(generateUrl('society/buildings/'+{{$society_id}}))
		.then(function(r){
			$scope.buildings = r.data.response;
		});

		$scope.buildingSelect = function() {
			console.log(this);
			$http.get(generateUrl('building/block/list/'+this.buildingId))
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

		}

        $scope.getUsers = function(page,sort,sort_order,search,block,status) {
            $scope.users = {};
            var options = {page: page,sort:sort,sort_order:sort_order,block_id:block,status:status};
            if (typeof search !== 'undefined') { options.search = search;}
			if (typeof status === 'undefined') { options.status = '' ;}

            $http.get(generateUrl('v1/users',options))
            .success(function(response) {
                if (response.code == 200) {
                    $scope.users = response.results.data;
                    $scope.pagination.total = response.results.total;
                    $scope.pagination.pageCount = Math.ceil(response.results.total / response.results.per_page);
                }

                if (! $scope.pagination.total > 0){
                    $scope.noData = 1;
                }
            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };
		
		$scope.changeUserType  = function() {
				$scope.pagination.total = 0;
				$scope.noData = 0;
				$scope.pagination.offset = 0;
				$scope.pagination.currentPage = 1;
				$scope.status = $("#user_type option:selected").val();
				
				
				$scope.getUsers(
                $scope.pagination.currentPage,
                $scope.sort,
                $scope.sort_order,
                $scope.search,
                $scope.block,
                $scope.status
            );
		}

        $scope.$on('pagination:updated', function(event,data) {
            $scope.getUsers(
                $scope.pagination.currentPage,
                $scope.sort,
                $scope.sort_order,
                $scope.search,
                $scope.block,
                $scope.status
            );
        });

        $scope.$watch('search', function(newValue, oldValue) {
            if(newValue){
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'first_name';
                $scope.sort_order = 'asc';
                $scope.pagination.setPage(1);
            } else {
                $scope.pagination.setPage(1);
            }
        });

        $scope.resetFilter = function() {
            $scope.block = '';
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.sort = 'first_name';
            $scope.sort_order = 'asc';
            $scope.pagination.setPage(1);
        }

        $scope.order = function(predicate) {
            $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
            $scope.predicate = predicate;
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.sort = predicate;
            $scope.sort_order = ($scope.reverse) ? 'asc' : 'desc';
            $scope.pagination.setPage(1);
        }

        $scope.tab = function(status) {
			jQuery("#dataCheck").text("Fetching Data...");
            $scope.status = status;
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.getUsers(
                $scope.pagination.currentPage,
                $scope.sort,
                $scope.sort_order,
                $scope.search,
                $scope.block,
                status
            );
        };

        $scope.deactivate = function(user_id) {
            var confirm_msg = confirm("It will deactive this user from all its flats in the society. Are you sure to deactivate this user!");
            if (confirm_msg == true) {
                $http.post(generateUrl('user/deactivate'), {
                    'id': user_id
                }).then(function(response) {
                        var result = response.data.response;
                        $scope.tab('1');
                        grit('',result.msg)
                    });
            }
        };

        $scope.activate = function(user_id){
            var confirm_msg = confirm("It will activate this user from all its flats in the society. Are you sure to activate this user!");
            if(confirm_msg == true){
                var request_url = generateUrl;
                $http.post(generateUrl('user/activate'), {id:user_id})
                .then(function(response) {
                    var result = response.data.response; // to get api result
                    $scope.tab('0');
                    grit('',result.msg);
                });
            }
        }

        $scope.approve = function(user_id){
            var confirm_msg = confirm("Are you sure to activate this user!");
            if (confirm_msg == true) {
                $http.post(generateUrl('user/approve'), {id:user_id})
                .then(function(response) {
                    var result = response.data.response; // to get api result
                    $scope.tab('2');
                    grit('',result.msg);
                });
            }
        };

        $scope.openDialog = function() {
            jQuery('#user-form .alert-warning').addClass('hide');
            jQuery('#userFormModal').modal();

            var request_url = generateUrl('getpermissiontype');
                var records = $.param({permission:'user.create'});
                $http({
                    url: request_url,
                    method: "POST",
                    data:records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function(response) {
                    var result = response.data; // to get api result
                    console.log(result.data.permission_type);
                    if(result.success){
                        if(result.data.building_permission){
                            $scope.buildings= $filter('filter')($scope.buildings, function(value, index) {return result.data.building_id == value.id});
                        }
                    }
                });
      };

       $scope.closeUserForm = function(){
            $scope.user_input_readonly = false;
            $scope.user_info = '';
            $("#user-form")[0].reset();
            $("#user-form label.error").remove();
            jQuery('.alert-warning').text('').addClass('hide');
            // $scope.buildingId = '';
            // var blockSelect = $('#select-block_id');
            // jQuery(blockSelect.prev('label')[0]).removeClass('form-label');
        	// blockSelect.rules("add", {required:false});
            $('#userFormModal').modal('hide');
        };
      //filter
        $scope.blockFilter = function(block)
        {
            $scope.block = block;
            $scope.getUsers(
                $scope.pagination.currentPage,
                $scope.sort,
                $scope.sort_order,
                $scope.search,
                block,
                $scope.status
            );
        };
        $('#registration-email').focusout(function() {
            var user_email = $('#registration-email').val();
            if(user_email){
                var options = {email:user_email};
               var request_url = generateUrl('user/find',options);
                $http.get(request_url)
                .success(function(r, status, headers, config) {
                    var result = r.response;
                    if (result.success) {
                        $scope.user_info = '';
                        $scope.user_info = result.data;
                        $scope.user_input_readonly = true;
                        $('#first_name-error').remove();
                        $('#last_name-error').remove();
                        $('#contact_no-error').remove();
                    } else {
//                        $scope.user_info = '';
                        $scope.user_input_readonly = false;
                    }

                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            }

        });

        $('#user-form').submit(function(e){
            e.preventDefault();
            if ($('#user-form').valid()){
                $('#loader').show();
                $('[name=society_id]').val(society_id);
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Creating user please wait..');
                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('user/create');
                $http({
                    url: request_url,
                    method: "POST",
                    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function(response) {
                    console.log(response);
                    $('#loader').hide();
                    var result = response.data; // to get api result
                    console.log(result);
                    $("#user-form").find('button[type=submit]').attr('disabled',false);
                    $("#user-form").find('button[type=submit]').text('Submit');
                    $( "label#input-flat_no-error" ).remove();
                    $scope.user_input_readonly = false;
                    if (result.status == 'success') {
                        $scope.tab('1');
                        $scope.closeUserForm();
                        grit('', result.message);
                    }else{
                        jQuery('#user-form .alert-warning').text(result.message).removeClass('hide');
                    }
//                    alert("User created successfully");
//                    window.location.reload();
                });
            }
        });

        jQuery.validator.addMethod("domain", function(value, element) {
            if(value!='')
            {
                if(value != value.match(/\S+@\S+\.\S+/)) {
                    return false;
                } else {
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
<!--    <style>

    </style>-->
<div class="col-lg-12" ng-controller="UserListCtrl">
	<div class="row">
		<div class="col-lg-12">
            @if($listPermission)
			<input ng-model="search" class="form-control ng-pristine ng-untouched ng-valid"  placeholder="Search By Flat/Email/Name" style="width: 200px;display: inline">
			<b style="margin-left:15px;">User Type : </b>
				<select id='user_type'  name="user_type" ng-model="user_type" ng-change="changeUserType()" style="height: 30px;width: 120px;">
    				<option disabled value="">Select User</option>
                    <option ng-selected="true" value="">All</option>
    				<option value="1">Approved</option>
    				<option value="2">Unapproved</option>
    				<option value="0">De-Activated</option>
				</select>
            @endif
<!--                        <div class="col-lg-12">
                            <div class="row">
                            <label>Blocks:<a class="block_link"
                                    href="" ng-click="resetFilter()">All</a><a href="#" class="block_link"
                                    ng-repeat="block in blocks" ng-click="blockFilter(block.id)">
                                            @{{block.block}} </a></label>
                                <label>Blocks:<button type="button" ng-click="resetFilter()" class="btn btn-sm btn-link"> All</button>
                                <button type="button" ng-repeat="block in blocks" ng-click="blockFilter(block.id)" class="btn btn-sm btn-default" > @{{block.block}}</button>&nbsp;
                                </div>
                            </div>-->

<!-- 			<div class="dropdown">
				<a id="dLabel" data-target="#" href="http://example.com"
					data-toggle="dropdown" role="button" aria-haspopup="true"
					aria-expanded="false"> Block <span class="caret"></span>
				</a>

				<ul class="dropdown-menu" aria-labelledby="dLabel"><a href="#"
				ng-repeat="block in blocks" ng-click="blockFilter(block.id)">
					@{{block.block}} </a>
				</ul>
			</div> -->

         @if($createPermission)
			<span class="pull-right" style="padding: 7px;">
				<button type="button" class="btn btn-primary"
					ng-click="openDialog()">Add User</button>
			</span>
         @endif
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
						<th><a href="" ng-click="order('flat')">Flat No</a> <span
							class="sortorder" ng-show="predicate === 'flat'"
							ng-class="{reverse:reverse}"></span></th>

                                                 <th><a href="" ng-click="order('type')">Type</a> <span
							class="sortorder" ng-show="predicate === 'type'"
							ng-class="{reverse:reverse}"></span></th>

						<th><a href="" ng-click="order('first_name')">Name</a> <span
							class="sortorder" ng-show="predicate === 'first_name'"
							ng-class="{reverse:reverse}"></span></th>
						<th><a href="" ng-click="order('email')">Email</a> <span
							class="sortorder" ng-show="predicate === 'email'"
							ng-class="{reverse:reverse}"></span></th>
						<th><a href="" ng-click="order('building')">Building</a> <span
							class="sortorder" ng-show="predicate === 'building'"
							ng-class="{reverse:reverse}"></span></th>
						<th><a href="" ng-click="order('block')">Block</a> <span
							class="sortorder" ng-show="predicate === 'block'"
							ng-class="{reverse:reverse}"></span></th>
                        <th><a href="" ng-click="order('relation')">Occupancy</a> <span
							class="sortorder" ng-show="predicate === 'relation'"
							ng-class="{reverse:reverse}"></span></th>
						<th><a href="" ng-click="order('created_at')">Created On</a> <span
							class="sortorder" ng-show="predicate === 'created_at'"
							ng-class="{reverse:reverse}"></span></th>
                        <th><a href="" ng-click="order('status')">User Type</a> <span
                            class="sortorder" ng-show="predicate === 'status'"
                            ng-class="{reverse:reverse}"></span></th>    
                        <th><a href="#" >Action</a></th>
					</tr>
				</thead>
				<tbody>
					<tr ng-if="pagination.total == 0 && noData == 0">
                        <td colspan="10" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                    </tr>
					<tr ng-if="pagination.total == 0 && noData == 1">
                        <td colspan="10" style="font-weight: bold;" >No Data Found.</td>
                    </tr>

					<tr ng-if="pagination.total > 0" ng-repeat="user in users">
                        <td>
                            <span ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                <a title="flat update" href="{!! route('admin.user.flat_edit','') !!}/@{{user_society.flat.id}}" > @{{user_society.flat.flat_no}}</a>
                                <br />
                            </span>
                        </td>
                        <td>
                            <span ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                @{{user_society.flat.type | capitalize}}<br />
                            </span>
                        </td>

						<td>@{{user.first_name}} @{{user.last_name}}</td>
						<td>@{{user.email}}</td>
					 	<td>
                            <span ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                @{{ user_society.building.name }}<br />
                            </span>
                        </td>
						<td>
                            <span ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                @{{ user_society.block.block }}<br />
                            </span>
                        </td>
                        <td class="capitalize">
                            <span ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                @{{user_society.relation}}<br />
                            </span>
                        </td>
                        <style>
                        td.capitalize {
                            text-transform: capitalize;
                        }
                        </style>
						<td>@{{ user.created_at.format('DD-MM-YYYY') }}</td>
                        </td> 
                        <td>
                            <span ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                <span ng-if="user_society.status == 1" >Approved</span>
                                <span ng-if="user_society.status == 2" >Unapproved</span>
                                <span ng-if="user_society.status == 0" >De-activated</span>

                                <br />
                            </span>
                        </td>
                        <span  >
                            
                        </span>
                        
						<td>
                            @if($updatePermission)
                            <ul class="list-unstyled col-sm-1">
                                <li>
                                    <a class="glyphicon glyphicon-pencil" title="update" href="{!! url('dashboard/admin/user/edit'); !!}/@{{user.id}}"></a>
                                </li>
                            </ul>
                            @endif
                            @if($deactivatePermission)
                                <ul class="list-unstyled col-sm-1">
                                    <li ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                        <a class="glyphicon glyphicon-remove" ng-show="status === '1'"
        							ng-click="deactivate(user_society.id)" title="De-Activate" href=""></a>
                                    </li>
                                </ul>
                            @endif
                            @if($approvePermission)
                                <span ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                    <a class="glyphicon glyphicon-check" ng-show="status === '2'"
        							ng-click="approve(user_society.id)" title="Approve" href=""></a>
                                </span>
                            @endif
                            @if($activatePermission)
                                <span ng-repeat="user_society in user.user_societies" ng-if="user_society.flat_id">
                                    <a class="glyphicon glyphicon-check" ng-show="status === '0'"
        							ng-click="activate(user_society.id)" title="Activate" href=""></a>
                                </span>
                             @endif
                        </td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<!--pagination--->
    <div class="row">
        <div class="col-lg-12">
            <ul class="pagination pagination-sm">
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
	<div class="modal fade" id="userFormModal" tabindex="-1" role="dialog"
		aria-labelledby="userFormModalLabel">
		<div class="modal-dialog" role="user">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						ng-click="closeUserForm()" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" >Add User</h4>
				</div>
				<div class="modal-body">
					<form id="user-form" method="post" action="">
                        <input type="hidden" name="society_id" />
                        <div class="alert alert-warning hide"></div>
						<div class="form-group">
							<label class="form-label">First Name</label> <input type="text"
								class="form-control" name="first_name" ng-model="user_info.first_name"
                                ng-readonly="user_input_readonly" maxlength="20"
								placeholder="First Name">
						</div>
						<div class="form-group">
							<label class="form-label">Last Name</label> <input type="text"
								class="form-control" name="last_name" ng-model="user_info.last_name"
                                ng-readonly="user_input_readonly" maxlength="20" placeholder="Last Name">
						</div>
						<div class="form-group">
							<label class="form-label">Email address</label> <input
								type="email" id="registration-email" class="form-control"
								name="email" maxlength="40" placeholder="Email">
						</div>
						<div class="form-group">
							<label class="form-label">Mobile No</label> <input type="text"
								data-rule-minlength="10" class="form-control" name="contact_no" ng-model="user_info.contact_no" ng-readonly="user_input_readonly" placeholder="Mobile No">
						</div>
						<!-- <div class="form-group">
							<label class="form-label">Building</label>
							<select id="building_select" class="form-control" ng-model="buildingId" ng-change="buildingSelect(building.id)" name="building_id">
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
							<label class="form-label">Type</label>
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
									<label> <input type="radio" name="type" value="flat"> Flat</label>
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
						</div> -->
						<button type="submit" class="btn btn-primary">Submit</button>
						<button class="btn btn-primary" type="button"
							ng-click="closeUserForm()">Cancel</button>
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
            $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            });
            $("#user-form").validate({
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
                    role: "required",
                    type: "required",
//                    block_id : "required",
                    building_id : "required",
                     contact_no : {
                        required:true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,

                     },
                    email: {
                        required: true,
                        email: true,
                    },
                    flat_no : {
                        required:true,
                        number: true,
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
                    }
                },
//                errorPlacement: function(error, element) {
//                    if (element.attr("name") == "type"  ) {
//                        $( ".form-group.type-radio-group" ).append( error );
//                    }else {
//                      error.insertAfter(element);
//                    },
//                    if (element.attr("name") == "role"  ) {
//                        $( ".form-group.type-radio-group" ).append( error );
//                    }else {
//                      error.insertAfter(element);
//                    }
//                }
            });
        });
    </script>
@stop
