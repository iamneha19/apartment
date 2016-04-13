@extends('admin::layouts.admin_layout') @section('title','Access Control
List') @section('panel_title', 'Access Control')@section('panel_subtitle','List')
@section('head')
<script type="text/javascript"> 
app.controller('AclController',function(URL,paginationServices,$scope,$http,$filter){
    $scope.buildingId = {{ isset($id) ? $id : 'undefined' }};
    $scope.resource;
    $scope.resources;
    $scope.permissions;
    $scope.roles;
    $scope.child_roles;
    $scope.role;
    $scope.sort = 'user';
    $scope.pagination = paginationServices.getNew(5);
    $scope.pagination.itemsPerPage = 10;
    $scope.sort_order = 'asc';
    $scope.block = '';
    $scope.status="1";
    $scope.roleTotal =0;
    $scope.users;
    $scope.activeUser;
    $scope.activeResourceName;
    $scope.activeRole;
    $scope.activeRoleId;
    $scope.activeRoleIndex;
    $scope.openParentRoleId;
    $('#loader').hide();
    
    $scope.getUsers = function(offset,limit,sort,sort_order,search,block,status) {
        var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order,block_id:block,status:status};
        if($scope.buildingId){
            options['building_id']=$scope.buildingId;
        }
        var request_url = generateUrl('acl/user/list',options);
         $http.get(request_url)
        .then(function(r){
            $scope.users = r.data.response;
            console.log($scope.users);
            $scope.pagination.total = $scope.users.length;
            $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
            if ($scope.pagination.total == 0 )
                $("#dataCheck").text("No Data Found.");
        }); 
    }
    
    $scope.getUsers($scope.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,status);
    
    $scope.$on('pagination:updated', function(event,data) {
            $scope.getUsers($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,$scope.status);
            
        });
        
    $scope.resetFilter = function()
            {
                $scope.block = '';
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'user';
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
    };
    
     $scope.tab = function(status) {
                $scope.status = status;
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.getUsers($scope.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,status);
            };
    
    $scope.getBuildings = function() {
        $http.get(generateUrl('building/list'))
        .then(function(response){
           $scope.buildings = response.data.response;
        });
    }
    
    $scope.getBuildings();
    
    $scope.openBuildingsModal = function() {
        $('#buildingsModal').modal('show');
    }
    
    $scope.closeBuildingsModal = function() {
        $('#buildingsModal').modal('hide');
    }
    

    $scope.showUserPermissions = function() {
        $scope.activeUser = this;
    	$scope.activeUser.acl = null;
        $scope.user_child_roles = null;
		$('#user_modal').modal('show');
        var options = {user_id:this.user.id};
        if($scope.buildingId){
            options['building_id']=$scope.buildingId;
        }
        var request_url = generateUrl('acl/user/role/list',options);
		$http.get(request_url)
		.then(function(r){
			$scope.activeUser.acl = r.data.response;
                        
		});
    }
   
//	$http.get(generateUrl('acl/resource/list'))
//	.then(function(r){
//		$scope.resources = r.data.response;
//	})

	$scope.userPermissionChange = function() {
		$http.post(generateUrl('acl/user/permission/add'),{user_id:$scope.activeUser.user.id,resource:this.module.acl_name,permitted:this.module.permitted})
                .then(function(response) {
               grit('',response.data.response.msg);
            });
	}
	   
        $scope.submitResource = function() {
            var $this =this;
            $this.disable = true;
            $http.post(generateUrl('acl/resource/add'),{acl_name:$scope.resource})
            .then(function(response) {
                $scope.resources.push(response.data.response.resource);
                grit('',response.data.response.msg);
                $scope.resource = "" ;
                $this.disable = false;
            });
        }
        
        $scope.submitPermission = function() {
            var $this =this;
            $this.disable = true;
            $http.post(generateUrl('acl/permission/add'),{acl_name:this.resource.acl_name,permission:this.resource.permission })
            .then(function(response) {
                
                $this.resource.permissions.push(response.data.response.permission.permission);
                grit('',response.data.response.msg);
                $this.resource.permission = "" ;
                $this.disable = false;
            });
        }
        
        $scope.getRoles = function() {
            var options = {};
            if($scope.buildingId){
                options['building_id']=$scope.buildingId;
            }
            $http.get(generateUrl('acl/role/list',options))
            .then(function(r){
                $scope.roles = r.data.response;
                $scope.roleTotal = $scope.roles.length;
                if ($scope.roleTotal == 0)
                    $("#dataCheckRole").text("No Data Found.");
            });
        };
        
        $scope.getRoles();
        
        
        
   
        $scope.roleModuleAccessChange = function() {
          $http.post(generateUrl('acl/role/moduleaccess/add'),{role_id:$scope.activeRoleId,resource:this.resource.resource,permitted:this.resource.permitted} )
          .then(function(response) {
               grit('',response.data.response.msg);
            });
          }
          
       $scope.roleModulePermissionChange = function(permission_id) {
            console.log(this.permission.permitted);
            $http.post(generateUrl('acl/role/modulepermission/add'),{role_id:$scope.activeRoleId,permission_id:permission_id,permitted:this.permission.permitted} )
            .then(function(response) {
                 grit('',response.data.response.msg);
              });
          } 
    $scope.showRolePermissions = function(role_id,role_name) {
//        $scope.activeRole =this;
        $scope.activeRoleId = role_id;
        $scope.activeRoleName = role_name;
        $('#roleModuleAccessModal').modal();
        
        var options = {role_id:role_id};
        if($scope.buildingId){
            options['building_id']=$scope.buildingId;
        }
        
        $http.get(generateUrl('acl/rolemoduleaccess/list',options))
                .then(function(r){
//                    $scope.activeRole.resources = r.data.response;
                    $scope.resources = r.data.response;
        });
    }
    
    $scope.showRolesModulePermissions = function(role_id,resource){
        $scope.activeResourceName = resource.display;
        $('#roleModulePermissionsModal').modal();
        $('#roleModuleAccessModal').modal('hide');
        var options = {role_id:role_id,resource:resource.resource};
        if($scope.buildingId){
            options['building_id']=$scope.buildingId;
        }
        $http.get(generateUrl('acl/rolemodulepermission/list',options))
                .then(function(r){
                    if(r.data.response){
                        $scope.permissions = r.data.response;
                    }else{
                        $scope.permissions = '';
                    }
                  
        });
    }
    
    $scope.closeRolesModulePermissions = function(){
        $('#roleModuleAccessModal').modal();
        $('#roleModulePermissionsModal').modal('hide');
    }

    $scope.moduleChange = function() {
        this.disable = true;
        var $this = this;
         $http.post(generateUrl('acl/user/set_module_access'),{ permitted:this.module.permitted,acl_name:this.module.acl_name,user_id:$scope.activeUser.user.id})
          .then(function(response) {
              $this.disable = false;
            //grit('',response.data.response.msg);
         });
    }
    
    $scope.roleChange = function() {
        this.disable = true;
        var $this = this;
        var role_id = this.module.id;
        var permitted = this.module.permitted;
        console.log('parent role');
        var data = { permitted:this.module.permitted,role_id:this.module.id,user_id:$scope.activeUser.user.id};
        if($scope.buildingId){
            data['building_id'] = $scope.buildingId;
        }
         $http.post(generateUrl('acl/user/role/add'),data)
          .then(function(response) {
            var result = response.data.response;
                if(result.success){
                    grit('',response.data.response.msg);
                    if($scope.user_child_roles[role_id]){
                        angular.forEach($scope.user_child_roles[role_id], function (value, key) {
                            $scope.user_child_roles[role_id][key].permitted = permitted;

                        });
                    }
                    
                }else{
                    $this.module.permitted = 0;
                   grit('',response.data.response.msg); 
                }
              
//              if(!result.success){ // To check unique role
//                  $this.module.permitted = 0;
//              }
              $this.disable = false;
            
         });
    }
    
    $scope.childRoleChange = function() {
        this.disable = true;
        var $this = this;
        console.log('child role');
         $http.post(generateUrl('acl/user/role/add'),{ permitted:this.child_module.permitted,role_id:this.child_module.id,user_id:$scope.activeUser.user.id})
          .then(function(response) {
            var result = response.data.response;
              console.log(result.success);
              if(!result.success){ // To check unique role
                  $this.child_module.permitted = 0;
              }
              $this.disable = false;
            grit('',response.data.response.msg);
         });
    }
 
    $scope.deleteRole = function(role_id,type) {

		            var r = confirm("Are You Sure You Want To Delete The Role");
		            if (r == true) 
			        {
		                var $this = this;
						$http.post(generateUrl('acl/role/delete'),{role_id:role_id})
						.then(function(r){
			                var result = r.data.response; // to get api result
			                if(result.success){
								grit('',r.data.response.msg);
                                if(type == 'parent'){
                                    $scope.getRoles();
                                }else{
                                    $scope.getChildRoles($scope.openParentRoleId);
                                }
                                
//					            $scope.roles= $filter('filter')($scope.roles, function(value, index) {return value.id != $this.role.id});
			                }
			                else{
			                	grit('',r.data.response.msg);
			                }
					     });
			            	}  
			             else 
				         {
			                   return ;
			             }

			}
	
    $scope.$watch('child_roles', function(newValue, oldValue) {
        if($scope.openParentRoleId){
            
            if(!$scope.child_roles[$scope.openParentRoleId].length){
              var selected_element = angular.element('#role_mgm .'+$scope.openParentRoleId); 
              selected_element.removeClass('caret-down').removeClass('caret-up').removeClass('has_children');
              $scope.child_roles = null;
            }
            
        }
        
      });

    $scope.submit = function() {

        if (this.eventform.$invalid)
                return;

    		var $this = this;
			console.log($this.rolename);
			console.log($scope.activeRole.role.id);
			this.duplicateRole = false;
			$http.post(generateUrl('acl/role/nameupdate'),{role_id:$scope.activeRole.role.id,role_name:$this.rolename})
			.then(function(r){
				if (r.data.response.success) {
					console.log(r.data.response.data);
					$scope.roles[$scope.activeRoleIndex] = r.data.response.data;
		            $('#myEditModal').modal('hide');
		            $('#rolename').val("");
				}
				else
					{
					    $this.duplicateRole = $this.rolename;
					}
				 $this.eventform.$setPristine();
			});

    }
    
    
    $scope.editRole = function(role_id,role_name,type) {
        $("#edit-role-form label.error").remove();
    	$scope.editRoleId = role_id;
        $scope.editRoleName = role_name;
        $scope.editRoleType = type;
    	
        $('#editRoleModal').modal();
       
    }
    
    $('#add-role-form').submit(function(e){
                e.preventDefault();
                
                if ($(this).valid()){
                    $('#loader').show();
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Adding please wait..');
                    
                    var arr = $( this ).serializeArray();
                    if($scope.buildingId){
                        arr.push({name:'building_id',value:$scope.buildingId});
                    }
                    var records = $.param(arr);
                    var request_url = generateUrl('acl/role/add');
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                        $('#loader').hide();
                        var result = response.data.response; // to get api result
                        $("#add-role-form").find('button[type=submit]').attr('disabled',false);
                        $("#add-role-form").find('button[type=submit]').text('Submit');
                        if(result.success){
                            $scope.closeAddRoleForm();
                            $scope.getRoles();
                            grit('','Role added successfully!');
                        }else{
                            $("#add-role-form label.error").remove();
                           // To handle server side validation errors 
                           if(result.input_errors){
                               var errors = result.input_errors;
                               for (var key in errors) {
                                    var error = errors[key];
                                    for (var index in error) {
                                            $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('input[name="'+key+'"]');
                                    }
                                 }
                               
                           }else{
                              $('<label id="role_name-error" class="error" for="role_name">'+result.msg+'</label>' ).insertAfter('#add-role-form input[type=text]');
                           }
                        }    
                        
                    
                  
                    });
                }
                
    });
    
    $('#edit-role-form').submit(function(e){
                e.preventDefault();
                
                if ($(this).valid()){
                    $('#loader').show();
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Editing please wait..');
                    var records = $.param($( this ).serializeArray());
                    var type = $( this ).find('input[name="type"]').val();
                    var request_url = generateUrl('acl/role/nameupdate');
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                        $('#loader').hide();
                        var result = response.data.response; // to get api result
                        $("#edit-role-form").find('button[type=submit]').attr('disabled',false);
                        $("#edit-role-form").find('button[type=submit]').text('Submit');
                        if(result.success){
                            $scope.closeEditRoleForm();
                            if(type == 'parent'){
                               $scope.getRoles(); 
                            }else{
                               $scope.getChildRoles($scope.openParentRoleId); 
                            }
                            grit('','Role edited successfully!');
                        }else{
                            $("#edit-role-form label.error").remove();
                           // To handle server side validation errors 
                           if(result.input_errors){
                               var errors = result.input_errors;
                               for (var key in errors) {
                                    var error = errors[key];
                                    for (var index in error) {
                                            $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('input[name="'+key+'"]');
                                    }
                                 }
                               
                           }else{
                               $('<label id="role_name-error" class="error" for="role_name">'+result.msg+'</label>' ).insertAfter('#edit-role-form input[type=text]');
                           }
                        }    
                        
                   
                    });
                }
                
    });
    
    $scope.openAddRoleForm = function(){
        $("#add-role-form label.error").remove();
        $('#addRoleModal').modal();
    };
    
    $scope.closeAddRoleForm = function(){
        $("#add-role-form")[0].reset();
        $("#add-role-form label.error").remove();
        $('#addRoleModal').modal('hide');
    };
    
    $scope.closeEditRoleForm = function(){
        $("#edit-role-form")[0].reset();
        $("#edit-role-form label.error").remove();
        $('#editRoleModal').modal('hide');
    };
    
    $scope.showChildRoles = function(parent_id){
        $scope.openParentRoleId = parent_id;
        var selected_element = angular.element('#role_mgm .'+parent_id);
        if(selected_element.hasClass('caret-down')){
            $('#role_mgm .has_children').addClass('caret-down').removeClass('caret-up');
            selected_element.removeClass('caret-down').addClass('caret-up');
            selected_element.siblings( "ul" ).show();
        }else{
            $('#role_mgm .has_children').addClass('caret-down').removeClass('caret-up');
            selected_element.removeClass('caret-up').addClass('caret-down');
            selected_element.siblings( "ul" ).hide();
        }
        $scope.getChildRoles(parent_id);
    };
    
    $scope.getChildRoles = function(parent_id){
        var options = {parent_id:parent_id};
        if($scope.buildingId){
            options['building_id']=$scope.buildingId;
        }
        $http.get(generateUrl('acl/role/list',options))
        .then(function(r){
            var child_role = {};
            child_role[parent_id] = r.data.response;
            
            $scope.child_roles=child_role;
           
        });
    };
    
    $scope.showUserChildRoles = function(user_id,parent_id){
		var selected_element = angular.element('#user_role_mgm .'+parent_id);
        if(selected_element.hasClass('caret-down')){
            $('#user_role_mgm .has_children').addClass('caret-down').removeClass('caret-up');
            selected_element.removeClass('caret-down').addClass('caret-up');
            selected_element.siblings( "ul" ).show();
        }else{
            $('#user_role_mgm .has_children').addClass('caret-down').removeClass('caret-up');
            selected_element.removeClass('caret-up').addClass('caret-down');
            selected_element.siblings( "ul" ).hide();
        }
        
		$http.get(generateUrl('acl/user/role/list',{user_id:user_id,parent_id:parent_id}))
		.then(function(r){
            var user_child_roles = {};
            user_child_roles[parent_id] = r.data.response;
            $scope.user_child_roles=user_child_roles;
           
		});
    };
    
    
    
});
</script>
<style>
    .caret-up{
        border-bottom: 4px solid #000000;
        border-left: 4px solid rgba(0, 0, 0, 0);
        border-right: 4px solid rgba(0, 0, 0, 0);
        content: "";
        display: inline-block;
        height: 0;
        vertical-align: middle;
        width: 0;
      }
      
      .caret-down {
        border-top: 4px solid #000000;
        border-left: 4px solid rgba(0, 0, 0, 0);
        border-right: 4px solid rgba(0, 0, 0, 0);
        content: "";
        display: inline-block;
        height: 0;
        vertical-align: middle;
        width: 0;
    }
</style>
@stop @section('content')

<div class="col-md-12" ng-controller="AclController">
    @if (!$society_acl && !$building_acl && !$mybuilding_acl)
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Warning!</strong> You don't have permission to access this page.
            </div> 
    @elseif (!$society_acl && !$mybuilding_acl && $building_acl)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Building</th>
                                <th>No. of blocks</th>
                                <th>No. of floors</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="building in buildings" class="edit" on-finish-render="ngRepeatFinished">
                                <td>@{{building.name}}</td>
                                <td>@{{building.blocks}}</td>
                                <td>@{{building.floors}}</td>
                                <td><a href="{{route('admin.building.acl','')}}/@{{building.id}}">Access Control</a></td>
                            </tr>
                        </tbody>
                    </table> 
    @else
        @if ($building_acl)
        <!--<button type="button" class="btn btn-primary pull-right" ng-click="openBuildingsModal()">Building Access Control</button>-->
       @endif
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#users"
				aria-controls="users" role="tab" data-toggle="tab">Users</a></li>
			<li role="presentation"><a href="#roles" aria-controls="roles"
				role="tab" data-toggle="tab">Roles</a></li>
            <!--<li role="presentation"><a href="#roles_permissions" aria-controls="roles_permissions" role="tab" data-toggle="tab">Resources And Permissions</a></li>-->
 
		</ul>
        
		<!-- Tab panes -->
		<div class="tab-content">

			<div role="tabpanel" class="tab-pane active" id="users">
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<tr>
                                                    <th><a href="" ng-click="order('user')">User Id</a><span
							class="sortorder" ng-show="predicate === 'user'"
							ng-class="{reverse:reverse}"></span></th>
                                                    <th><a href="" ng-click="order('name')">Name</a><span
							class="sortorder" ng-show="predicate === 'name'"
							ng-class="{reverse:reverse}"></span></th>
                                                    <th><a href="" ng-click="order('email')">Email</a><span
							class="sortorder" ng-show="predicate === 'email'"
							ng-class="{reverse:reverse}"></span></th>
                            <th><a href="" ng-click="order('roles')">Roles</a><span
							class="sortorder" ng-show="predicate === 'roles'"
							ng-class="{reverse:reverse}"></span></th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                                                <tr ng-if="pagination.total == 0">
                                                    <td colspan="10" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                                                </tr>
						<tr ng-if="pagination.total > 0" ng-repeat="user in users">
							<td>@{{user.id}}</td>
							<td>@{{user.name}}</td>
							<td>@{{user.email}}</td>
                            <td>@{{user.role_name}}</td>
							<td><a href="javascript:void(0)" ng-click="showUserPermissions()">Manage
									Roles</a></td>
						</tr>
					</tbody>
				</table>
				
				<div ng-if="pagination.total > 0" class="row">
		<div class="col-lg-12">
			<ul class="pagination pagination-sm"
				ng-show="(pagination.pageCount) ? 1 : 0">
				<li ng-class="pagination.prevPageDisabled()"><a href
					ng-click="pagination.prevPage()" title="Previous"><i
						class="fa fa-angle-double-left"></i> Prev</a></li>
				<li ng-repeat="n in pagination.range()"
					ng-class="{active: n == pagination.currentPage}"
					ng-click="pagination.setPage(n)"><a href>@{{n}}</a></li>
				<li ng-class="pagination.nextPageDisabled()"><a href
					ng-click="pagination.nextPage()" title="Next">Next <i
						class="fa fa-angle-double-right"></i></a></li>
			</ul>
		</div>
	</div>
			</div>

			<!-- start roles -->
			<div role="tabpanel" class="tab-pane" id="roles">
                
                <div class="btn-toolbar pull-right">
                    <button type="button" class="btn btn-primary" ng-click="openAddRoleForm()">Add Role</button>
                </div>
            
				<div class="row">
					<div class="col-md-5" id="role_mgm">
<!--						<table class="table table-hover">
							<tbody>
								<tr ng-repeat="role in roles">
									<td><a href="javascript:void(0)"
										ng-click="showRolePermissions()"> @{{role.role_name}} </a></td>
									<td><a class="text-danger pull-right" ng-click="deleteRole()"
										href="javascript:void(0)" ng-if="role.is_default != '1'">Delete</a>
										<a class="pull-right"
										style="padding: 0px 10px" ng-click="editRole(role.id,role.role_name)"
										href="javascript:void(0)" ng-if="role.is_default != '1'">Edit</a></td>
									</td>
								</tr>
							</tbody>
						</table>-->
                        
                        <ul class="list-group">
                            <li ng-if="roleTotal == 0">
                                        <div style="font-weight: bold;" id="dataCheckRole">Fetching Data...</div>
                                    </li>
                            <li ng-if="roleTotal > 0" ng-repeat="role in roles" id='role_@{{role.id}}' class="list-group-item" > @{{role.role_name}} 
                                <a href="" class="caret-down @{{role.id}}" ng-if="role.children" ng-class="{has_children: role.children}" ng-click='showChildRoles(role.id)'></a>
                               <div class="pull-right">
                                   <a href='' ng-click='showRolePermissions(role.id,role.role_name)' title="Show permissions"><i class="fa fa-user"></i></a>
                                   <a href='' ng-if="role.is_default != '1'" ng-click='editRole(role.id,role.role_name,"parent")' title="Edit role"><i class="fa fa-pencil"></i></a>
                                   <a href='' ng-if="role.is_default != '1'" ng-click='deleteRole(role.id,"parent")' title="Delete role"><i class="fa fa-remove"></i></a>
                               </div>
                                <ul class="list-group" ng-if='child_roles[role.id]'>
                                    <li class="list-group-item" ng-repeat="child_role in child_roles[role.id]" on-finish-render="ngRepeatFinished">@{{child_role.role_name}}
                                        <div class="pull-right">
                                            <a href='' ng-click='showRolePermissions(child_role.id,child_role.role_name)' title="Show permissions"><i class="fa fa-user"></i></a>
                                            <a href='' ng-click='editRole(child_role.id,child_role.role_name,"child")' title="Edit role"><i class="fa fa-pencil"></i></a>
                                            <a href='' ng-click='deleteRole(child_role.id,"child")' title="Delete role"><i class="fa fa-remove"></i></a>
                                        </div>
                                    </li>
                                </ul> 
                            </li>
                        </ul> 
					</div>
				</div>
			</div>
			<!-- start resource and permissions -->
			<div role="tabpanel" class="tab-pane" id="roles_permissions">
				<form id="newResource" class="form-inline"
					ng-submit="submitResource()">
					<div class="form-group">
						<label class="sr-only" for="exampleInputAmount">Add Resources</label>
						<div class="input-group">
							<input type="text" class="form-control" id="newResource"
								placeholder="Add Resources" ng-model="resource">
						</div>
					</div>
					<button ng-disabled="disable" type="submit" class="btn btn-primary">Add
						Resources</button>
				</form>
				<table class="table">
					<tbody>
						<tr ng-repeat="resource in resources">
							<td>
								<ul class="list-group">
									<li class="list-group-item"><strong>@{{resource.acl_name}}</strong>
									</li>
									<li class="list-group-item"
										ng-repeat="permission in resource.permissions">
										@{{permission}}</li>
									<form class="form-inline" ng-submit="submitPermission()">
										<div class="form-group">
											<label class="sr-only" for="exampleInputAmount">Add
												Permissions</label>
											<div class="input-group ">
												<input type="text" class="form-control input-sm"
													id="newResource" placeholder="Add Permissions"
													ng-model="resource.permission">
											</div>
										</div>
										<button ng-disabled="disable" type="submit"
											class="btn btn-link">Add Permissions</button>
									</form>
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>


    @endif

	<!--- Role permission modal--->
	<div id="roleModuleAccessModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">@{{activeRoleName}} Role Module Access</h4>
				</div>
				<div class="modal-body">
					<div class="checkbox">
						<ul ng-repeat="resource in resources">
							<li><input type="checkbox" ng-model="resource.permitted"
								ng-change="roleModuleAccessChange()"
								ng-checked="resource.permitted == 1" ng-true-value="1"
								ng-false-value="0"
								ng-disabled=" (activeRoleName == 'Admin') && (!buildingId)"> <strong>@{{resource.display}}</strong>
                                <a href="" ng-show="resource.permitted == 1" ng-click="showRolesModulePermissions(activeRoleId,resource)"><i class="fa fa-wrench"></i></a>
							</li>
						</ul>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
				</div>
			</div>
		</div>
	</div>
    
    <!--- Role permission modal--->
	<div id="roleModulePermissionsModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" ng-click="closeRolesModulePermissions()"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">@{{activeResourceName}} Module Permissions for @{{activeRoleName}}</h4>
				</div>
				<div class="modal-body">
					<div class="checkbox">
                        <ul ng-if="permissions">
                            <li ng-repeat="permission in permissions">
                                <input type="checkbox" ng-model="permission.permitted" ng-checked="permission.permitted == 1"  ng-change="roleModulePermissionChange(permission.id)" ng-true-value="1"
								ng-false-value="0"> <strong>@{{permission.title}}</strong>
							</li>	
						</ul>
                        <div ng-if="permissions == ''" class="alert alert-warning">Permissions need to define</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="closeRolesModulePermissions()">Done</button>
				</div>
			</div>
		</div>
	</div>
    
	<!--- Edit Role name modal--->
	<div id="editRoleModal" class="modal fade" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" ng-click="closeEditRoleForm()"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Edit Role</h4>
				</div>
				<form  id="edit-role-form" class="modal-body" name="edit_role" novalidate>
					<div class="form-group">
						<label class="form-label">Role Name</label>
						<input type="text" value="@{{editRoleName}}" class="form-control" name="role_name" placeholder="Role Name" required> 
					</div>
					<div class="form-group">
                        <input type="hidden" value="@{{editRoleId}}" class="form-control" name="role_id" required>
                        <input type="hidden" value="@{{editRoleType}}" class="form-control" name="type" required> 
						<button  type="submit" class="btn btn-primary">Update</button>
						<button type="button" ng-click="closeEditRoleForm()" class="btn btn-primary">Cancel</button>
					</div>
				</form>
                            
				<!-- 				<div class="modal-footer">
					<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
				</div> -->
			</div>
		</div>
	</div>

	<!--- User permission modal--->
	<div id="user_modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Manage Roles For @{{activeUser.user.name}}</h4>
				</div>
				<div class="modal-body">
					<strong ng-if="activeUser.acl == null">Loading..</strong>

					<table class="table" id="user_role_mgm" ng-if="activeUser.acl !== null">
						<tbody>
							<tr ng-repeat="module in activeUser.acl">
								<td><input type="checkbox" ng-model="module.permitted"
									ng-change="roleChange()" ng-true-value="1" ng-false-value="0"
                                    ng-checked="module.permitted == 1" ng-disabled=" (module.role_name == 'Admin') && (module.permitted == 1) " >
									@{{module.role_name}} 
                                    <a href="" class="caret-down @{{module.id}}" ng-if="module.children" ng-class="{has_children: module.children}" ng-click='showUserChildRoles(activeUser.user.id,module.id)'></a>
                                    <ul ng-if="user_child_roles[module.id]">
                                        <li ng-repeat="child_module in user_child_roles[module.id]"><input type="checkbox" ng-model="child_module.permitted"
									ng-change="childRoleChange()" ng-true-value="1" ng-false-value="0"
									ng-checked="child_module.permitted == 1" >
									@{{child_module.role_name}}
                                        </li>
                                    </ul> 
                                </td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
				</div>
			</div>
		</div>
	</div>
    
    <!-- Modal -->
        <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" ng-click="closeAddRoleForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Role</h4>
                  </div>
                  <div class="modal-body">
                    <form id="add-role-form" method="post" action="">
                        <div class="form-group">
                          <label class="form-label">Role</label>
                          <input type="text" class="form-control" name="role_name" maxlength="50"  placeholder="Title">
                        </div>
                        <div class="form-group">
                          <label>Parent Role</label>
                          <select name="parent_id">
                              <option value='' selected="" disabled="">Select role</option>
                              <option ng-repeat="role in roles" value="@{{role.id}}">@{{role.role_name}}</option>
                              
                          </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button class="btn btn-primary" ng-click="closeAddRoleForm()" type="button">Cancel</button>
                    </form>
                       <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                       <div id="loader" class="loading">Loading&#8230;</div>
                  </div>
                </div>
            </div>
        </div>
    
        <!--- Buildings modal--->
	<div id="buildingsModal" class="modal fade" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" ng-click="closeBuildingsModal()"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Buildings</h4>
				</div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Building</th>
                                <th>No. of blocks</th>
                                <th>No. of floors</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="building in buildings" class="edit" on-finish-render="ngRepeatFinished">
                                <td>@{{building.name}}</td>
                                <td>@{{building.blocks}}</td>
                                <td>@{{building.floors}}</td>
                                <td><a href="{{route('admin.building.acl','')}}/@{{building.id}}">Access Control</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				
				
			</div>
		</div>
	</div>
        
    <div id='' style="display: none;">
        
    </div>
</div>


<script>
        $('document').ready(function(){
            $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            });
            $("#add-role-form").validate({ 
                rules: {
                	role_name: {
                        required:true,
                        minlength:0,
                    },
                }
            });
            
            $("#edit-role-form").validate({ 
                rules: {
                	role_name: {
                        required:true,
                        minlength:0,
                    },
                }
            });
        });
    </script>
@stop
