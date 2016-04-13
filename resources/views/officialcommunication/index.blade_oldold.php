@extends('layouts.default') @section('panel_title')Official
Communication @stop @section('panel_subtitle','') @section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}"
	rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@stop @section('content')

<script type="text/javascript">
app.controller("OfficialCommCtrl", function(paginationServices,$scope,$http,$filter) {
     $scope.getRoles;
   	 $scope.getRoles = function() {
	            var request_url = generateUrl('acl/role/list');
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.roles = result.response;
						console.log($scope.roles);
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
       };
			
		 $scope.openLetter = function(){
            $scope.getRoles();
        };



    	$http.get(generateUrl('adminforum/topic/count'))
    	.then(function(r){
    		//$scope.openIssues = r.data.response;
    		console.log(parseInt(r.data.response.topic_count));
            $('#pagination-demo').twbsPagination({
                totalPages:  parseInt(r.data.response.topic_count)/10,
                //totalPages:  3,
                visiblePages: 20,
                onPageClick: function (event, page) {
                $http.get(generateUrl('adminforum/topic/list')+'&page='+page).
                then(function(response) {
                    $scope.topics = response.data.response;
                });

                $('#page-content').text('Page ' + page);
            }
                });

    	});




		
			
    $scope.submit=function()
	{
        if($('#myform').valid())
        {
			this.disable=true;
			var $this=this;
			var $data = {
						 role_id:$('#role_id').val(),
						 emailsubject:$('#emailsubject').val(),
						 emailtext:$('#emailtext').val(),
						 subjectref:$('#subjectref').val(),
						 documentref:$('#documentref').val(),
						};
		    var request_url = generateUrl('v1/OfficialComm/save');
			var records = $('#myform').serializeArray() ;
			 
			$http({
                    url: request_url,
                    method: "POST",
				    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function(response){
                   grit("","Letter Saved!")
                   console.log(response.data.response.msg);
			       $this.disable=false;
					/*$('#myModal').modal('hide');
					$this.date="";
					$('#eventdate').val("");
					$this.name="";
					grit("","Letter Saved!")
					$this.disable=false;
					$this.eventform.$setPristine(); */
					//grit("",response.data.response.msg) 
				  });
            }
            else{
                console.log("validation error!");
            }
    }
    
      $scope.close=function(){
              //console.log(this);
              var $this=this;
              $('#myModal').modal('hide');
              $this.name="";
              $this.date="";
              $('#eventdate').val("");
              // 	$("#myModal")[0].reset();
              $("#myModal label.error").remove();
              $this.eventform.$setPristine(); 
    }

      $http.get(generateUrl('OfficialComm/list')).
      then(function(response) {
          $scope.topics = response.data.response;
          console.log(response.data.response);
      });
      

});
</script>
<div class="col-lg-12" ng-controller="OfficialCommCtrl">
	<div class="row form-group col-lg-12 btn-toolbar pull-right">
		<button type="button" id="openCreate" class="btn btn-primary"
			data-toggle="modal" data-target="#myModal"  ng-click="openLetter()">Create Letter</button>
	</div>
	
	<div class="row">
		<div class="col-lg-12">
			<table class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th><a href="" ng-click="order('first_name')">Name</a> <span
							class="sortorder" ng-show="predicate === 'first_name'"
							ng-class="{reverse:reverse}"></span></th>
						<th><a href="" ng-click="order('email')">Email</a> <span
							class="sortorder" ng-show="predicate === 'email'"
							ng-class="{reverse:reverse}"></span></th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="user in users">
						<td>@{{user.first_name}} @{{user.last_name}}</td>
						<td>@{{user.email}}</td>
						<td><a class="glyphicon glyphicon-pencil" title="update"
							href="<?php echo url('dashboard/admin/user/edit');  ?>/@{{user.id}}"></a>
							<a class="glyphicon glyphicon-remove" ng-show="status === '1'"
							ng-click="deactivate(user.user_society_id)" title="De-Activate"
							href=""></a> <a class="glyphicon glyphicon-check"
							ng-show="status === '2'" ng-click="approve(user.user_society_id)"
							title="Approve" href=""></a> <a class="glyphicon glyphicon-check"
							ng-show="status === '0'"
							ng-click="activate(user.user_society_id)" title="Activate"
							href=""></a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<!-- Modal -->
	<div ng-controller="OfficialCommCtrl" class="modal fade" id="myModal"
		tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" aria-label="Close"
						ng-click="close()">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Create Communication</h4>
				</div>
			<form ng-submit="submit()" class="modal-body" id="myform"
					name="myform" novalidate>

				 <div class="form-group">
					
					<label class="form-label" for="emailsubject">Role</label>
						 <select id="role_id" name="role_id" class="form-control" >
							  <option value="" disabled="" selected="">Select Roles </option>
							  <option ng-repeat="role in roles" value='@{{role.id}}'>@{{role.role_name}}</option>
						  </select>
					</div>
					
					<div class="form-group">
						<label class="form-label" for="emailsubject">Subject</label> 
						<input name="email_subject" ng-model="subject" type="text" class="form-control" id="emailsubject" placeholder="Subject">
					</div>
					
					<div class="form-group">
						<label class="form-label" for="emailtext">Email Text</label>
						<textarea ng-model="text" type="text" name="email_text"
							class="form-control" placeholder="Text" rows="4" id="emailtext"></textarea>
					</div>
					
					<div class="form-group">
						<label class="form-label" for="emailsubject">Subject Reference</label>
						<input name="subject_ref" ng-model="subject_ref" type="text"
							class="form-control" id="subjectref"
							placeholder="Subject Reference">
					</div>
					
					<div class="form-group">
						<label class="form-label" for="emailsubject">Document Reference</label>
						<input name="document_ref" ng-model="document_ref" type="text"
							class="form-control" id="documentref"
							placeholder="Document Reference">
					</div>
					
					<div class="form-group">
						<button id="buttonsuggest" type="submit" class="btn btn-success"
							ng-disabled="disable">@{{ disable ? 'creating letter ..' :
							'Create' }}</button>
						<button type="button" class="btn btn-default" ng-click="close()">Cancel</button>	
					</div>
					
				</form>

			</div>
		</div>
	</div>
	<!-- End Modal -->
</div>
@stop
