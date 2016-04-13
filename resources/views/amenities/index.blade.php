@extends('admin::layouts.admin_layout')
@section('panel_title') Amenities @stop
@section('panel_subtitle','') @section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}"
	rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
@stop @section('content')
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
<script>
    app.controller("AmenitiesCtrl", function($scope,$http,$filter) {

    $scope.getRoles;
    $scope.comm;
    $scope.users;


    $scope.data=null;
    $scope.letters=null;
    $scope.showLetter=false;
    $scope.hasreplies=false;
    $scope.letter={};
    $scope.role={};
    $scope.activeLetter=null;
    $scope.replies=null;
    $scope.reply={};


    
    //newly implemented - resident
//    $scope.list = function() {
//    $http.get(generateUrl('v1/amenities/amenitiescount'))
//	.then(function(r){
//
//		//console.log("amenities_count");
//
//		//console.log(r.data.amenities_count);
// 		var count = String(parseInt(r.data.amenities_count)/10 < 1 ? 1 : parseInt(r.data.amenities_count)/10);
// 		console.log(count);
// 		if ( count != 1 )
// 		{
// 	 		var res = count.split(".",1);
// 	 		countfinal = parseInt(res)+parseInt(1);
// 	 		console.log(countfinal);
// 		}
// 		else
// 		{
// 			countfinal = 1;
// 		}
//
// 		$('#pagination-demo').twbsPagination({
//	         totalPages:  countfinal,
//	         visiblePages: 20,
//	         onPageClick: function (event, page) {
//		         $http.get(generateUrl('v1/amenities/amenitieslist')+'&page='+page).
//		         then(function(response) {
//		         	console.log("in pagination letterlist");
//		         	console.log(response.data.results);
//		             $scope.users = response.data.results;
//		         });
//		         $('#page-content').text('Page ' + page);
//	     	}
//         });
//	});
//    }
//    $scope.list();


    $scope.list = function() {
        
   	    $http.get(generateUrl('v1/amenities/amenitiescount'))
   		 .then(function(r){
   	            //console.log(r);return;
   	           var totalPages = parseInt(r.data.amenities_count)/5 <= 1 ? 1 : (parseInt(r.data.amenities_count)/5)+1;                  
   	           console.log(totalPages);
   	           $('#pagination-demo').twbsPagination({
   	                    totalPages: totalPages,
   	                    visiblePages: 5,
   	                    onPageClick: function (event, page) {
   	                    $http.get(generateUrl('v1/amenities/amenitieslist')+'&page='+page).
   	                    then(function(response) {
   	                         $scope.users = response.data.results;
   	                     });
   	                 }
   	            });
   	        });
    }

    $scope.list();
    
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

        $scope.openForm = function(){
            console.log(this);
        };


        //Disable function
        jQuery.fn.extend({
            disable: function(state) {
                return this.each(function() {
                    this.disabled = state;
                });
            }
        });
	    $scope.showAmenitiesClick=function(){

	    	$scope.showLetter=true;
    		$scope.activeLetter=this.letter;
    		//console.log($scope.activeLetter.letter_count);
            $id=this.user.id;
            $http.get(generateUrl('v1/amenities/details/'+this.user.id)).
        	then(function(response) {
            $scope.letter = response.data;
            console.log("in details");
            console.log($scope.letter);
            /* 
            $http.get(generateUrl('v1/officialcomm/letter/to/'+$id)).
            then(function(response) {
            $scope.role = response.data;
            console.log(response);
            $http.get(generateUrl('v1/officialcomm/letter/reply/'+ $scope.letter.id)).
            then(function(response) {
                    console.log($scope.letter.id);
                    console.log(response.data);
                    $scope.letter.replies = response.data;
            });
        	}); */
        	});
    	}

    $scope.submit=function()
    {
    	var $this=this;
        if (this.myform.$invalid)
            return;
          $this.disable=true;
		  if ($("#myform").valid()){
				//$http.post(generateUrl('v1/amenities/save'),$scope.comm)
				
				$(this).find('button[type=submit]').attr('disabled',true);
                                $(this).find('button[type=submit]').text('Creating Amenities please wait..');
				
				  var records = new FormData($('#myform')[0]);
					
									$http({
						url: generateUrl('v1/amenities/save'),
						method: "POST",
						data: records,
						transformRequest: angular.identity,
						headers: {'Content-Type': undefined}
					}).
					then(function(response) {
					//$scope.list();
					$(this).find('button[type=submit]').attr('disabled',false);
                    $(this).find('button[type=submit]').text('Creating Amenities please wait..');
					
					$('#myModal').modal('hide');
					grit('','Amenities Saved Successfully');
					$this.disable=false;
					
					$scope.comm = {};
                                         $("#myform")[0].reset();
					$scope.myform.$setPristine();
					location.reload();
				});
		  }
    }

      $scope.close=function(){
              var $this=this;
              $('#myModal').modal('hide');
              console.log("close");
              $scope.getRoles();
              $('#amenitiesname').val("");
              $('#amenitiesdescription').val("");
              $('#amenitiesimage').val("");
              $('#amenitiescharges').val("");
              $scope.comm = {};
              $scope.myform.$setPristine();
    	}

        $scope.closeForm = function(){
            $("#myform")[0].reset();
            $scope.comm = {};           
            $scope.myform.$setPristine();
            $("#myform label.error").remove();
            $('#myModal').modal('hide');
        };
	    $scope.replysubmit=function(){
            if($('#reply-form').valid()){
                $('#reply-form').find('button[type=submit]').attr('disabled',true);
                $('#reply-form').find('button[type=submit]').text('Adding comment please wait..');
                var $data = {
                		letter_id:$scope.letter.id,
                        comment:this.reply.text
                }

                $http.post(generateUrl('v1/officialcomm/letter/reply/save'), $data).
                  then(function(response) {
                      console.log("success");
                                $("#reply-form").find('button[type=submit]').attr('disabled',false);
                                $("#reply-form").find('button[type=submit]').text('Submit');
                      	  //$scope.activeLetter.letter_count=parseInt($scope.activeLetter.letter_count)+1
                          console.log(response.data.results);
                          $scope.letter.replies.push(response.data.results);
                          $scope.reply={};
		  		});
            }
            else{
                console.log("form is not valid!");
            }
         }


//      $http.get(generateUrl('v1/OfficialComm/list')).
//      then(function(response) {
//          $scope.topics = response.data.response;
//          console.log(response.data.response);
//      });

});
</script>
<div class="col-lg-12" ng-controller="AmenitiesCtrl">
	<!-- 	<div class="row form-group col-lg-12 btn-toolbar pull-right">
		<button type="button" id="openCreate" class="btn btn-primary"
			data-toggle="modal" data-target="#myModal" ng-click="openLetter()">Create
			Letter</button>
	</div> -->
	<div class="row">
		<div class="col-lg-12" ng-if="showLetter == false">
            <div class="form-group">
                <button type="button" id="openCreate" class="btn btn-primary pull-right"
					data-toggle="modal" data-target="#myModal" ng-click="openForm()">Add
					Amenities</button>
                <div class="clearfix"></div>
            </div>
				
			
			<table class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th>Name</th>
						<th>Charges</th>
						<th>Created on</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="user in users">
						<td><a ng-click="showAmenitiesClick()" href="javascript:void(0);">@{{user.name}}</a></td>
						<!-- <td>@{{user.subject}}</td> -->
						<td>@{{user.charges}}</td>
						<td>@{{user.created_at}}</td>
					</tr>
				</tbody>
			</table>
			<div>
				<ul id="pagination-demo" class="pagination-sm"></ul>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12" ng-if="showLetter == true">

			<div class="btn-toolbar pull-right">
				<a class="btn btn-primary" href="{{ route('admin.amenities') }}"><< Back to list</a>
			</div>


			<div class="row">
				<div class="col-lg-12">

					<h3>Amenities</h3>
					<p>
						<!-- <span class="highlight">To :</span>@{{role.`}} -->
					</p>
					<p>
						<span class="highlight">Name :</span>@{{letter.name}}
					</p>
					<div class='clear-both'></div>
					<p>
						<span class="highlight">Description :</span>@{{letter.description}}
					</p>
				
					<p>
						<span class="highlight">Charges :</span>@{{letter.charges}}
					</p>
					<p>
						<span class="highlight">Created On :</span>@{{letter.created_at |
						date:'dd-MMM-yyyy' }}
					</p>

					<strong>Attachment <a
						href="<?php echo url('dashboard/documents/downloadotherfile') ?>?file=@{{letter.http_path}}@{{letter.image}}&name=@{{letter.image}}">@{{letter.image}}</a></strong>

				</div>
			</div>

		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" aria-label="Close"
						ng-click="close()">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Add Amenities</h4>
				</div>
				<form ng-submit="submit()" class="modal-body" id="myform" enctype="multipart/form-data"
					name="myform" >
					<div class="form-group">
						<label class="form-label" for="amenitiesname">Name</label> <input
							name="name" ng-model="comm.name" type="text"
							class="form-control" id="amenitiesname" placeholder="Name"
							required> <label class="error"
							ng-show="myform.$submitted && myform.amenities_name.$invalid ">
							Please enter a valid name </label>
					</div>
					<div class="form-group">
						<label class="form-label" for="amenitiesdescription">Description</label>
						<textarea ng-model="comm.description" type="text"
							name="description" class="form-control"
							placeholder="Description" rows="4" id="emailtext" required>
                    </textarea>
						<label class="error"
							ng-show="myform.$submitted && myform.amenities_description.$invalid ">
							Please enter a valid description </label>
					</div>

					<!-- upload image new -->
					<div class="form-group">
						<input id="fileupload" name="file" type="file" ng-model="comm.files"
							enctype="multipart/form-data" ng-disabled="user_input_disabled">
					</div>


					<div class="form-group">
						<label class="form-label" for="amenitiescharges">Charges</label> <input
							name="charges"  ng-model="comm.charges" type="text"
							class="form-control" id="amenitiescharges" placeholder="Charges"
							required> <label class="error"
							ng-show="myform.$submitted && myform.document_ref.$invalid ">
							Please enter charges </label>
					</div>
					<div class="form-group">
						<button id="buttonsuggest" type="submit" class="btn btn-success"
							>Submit</button>
                        <button class="btn btn-default" type="button" ng-click="closeForm()" >Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- End Modal -->
</div>

<script>
     $(document).on("click", "#buttonsuggest", function() {
         $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            });
        $("#myform").validate({
                   rules: {
                       text:"required",
					   charges : {
                       number: true,
				      }
                   },
				   
               });
     });
    </script>

@stop