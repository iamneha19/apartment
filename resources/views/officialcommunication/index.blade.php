@extends('layouts.default') @section('panel_title') Official
Communication @stop @section('panel_subtitle','') @section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}"
	rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
@stop @section('content')

<script>
    app.controller("OfficialCommCtrl", function(paginationServices,$scope,$http,$filter) {

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
	$scope.totalUsers =0;
	$scope.type = '1';
    $scope.pagination = paginationServices.getNew(5);
    $scope.itemsPerPage = 5;  
    $scope.search='';
    
	$scope.getCommunications = function(type,offset,limit,search) {
               var options = {type:type,offset:offset,limit:limit};
                if(search){
                    options['search'] = search;
                }
               var request_url = generateUrl('v1/officialcomm/letterlist/resident',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {                                  
                    $scope.users = result.results;
                    $scope.pagination.total = result.count.letter_count;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                    if($scope.pagination.total == 0) {
                        $('#dataCheck').show();
                    } else {
                        $('#dataCheck').hide();
                    }
                });
                 
            };
            
            $scope.getCommunications($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
            
        $scope.$on('pagination:updated', function(event,data) {
            $scope.getCommunications($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
        });
        
        
        $scope.$watch('search', function(newValue, oldValue) {
        if(newValue){
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            
            $scope.pagination.setPage(1);
        }else{
            $scope.pagination.setPage(1);
        }

    });

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
            console.log(this);
            $scope.getRoles();
        };


        //Disable function
        jQuery.fn.extend({
            disable: function(state) {
                return this.each(function() {
                    this.disabled = state;
                });
            }
        });
	    $scope.showLetterClick=function(){

	    	$scope.showLetter=true;
    		$scope.activeLetter=this.letter;
    		//console.log($scope.activeLetter.letter_count);
            $id=this.user.id;
            $http.get(generateUrl('v1/officialcomm/letter/'+this.user.id)).
        	then(function(response) {
            $scope.letter = response.data;
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
        	});
        	});
    	}

/*     $scope.list = function() {
        $http.get(generateUrl('v1/officialcomm/letterlist/resident'))
        .then(function(response) {
        $scope.users = response.data.results;
        });
    }

    $scope.list(); */

    $scope.submit=function()
    {
    	var $this=this;

        if (this.myform.$invalid)
            return;
        $this.disable=true;
        $http.post(generateUrl('v1/officialcomm/save'),$scope.comm)
        .then(function(response) {
		  $scope.closeForm();
          grit('','Official communication is created successfully!');
		});

    }
	
	          $scope.closeForm = function(){
                $("#myform")[0].reset();
                $("#myform label.error").remove();
             
                $('#myModal').modal('hide');
				 location.reload();
            };

      $scope.close=function(){
              var $this=this;
              $('#myModal').modal('hide');
              console.log("close");
              //$this.comm.recepient_id="";
              $scope.getRoles();
              $('#emailsubject').val("");
              $('#emailtext').val("");
              $('#subjectref').val("");
              $('#documentref').val("");
              $scope.comm = {};
              $scope.myform.$setPristine();
              //$("#myModal label.error").remove();
              //$this.eventform.$setPristine();

    	}


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
<div class="col-lg-12" ng-controller="OfficialCommCtrl">
    <div  ng-show="showLetter == false" class="col-lg-12 form-group">
   <input ng-model='search' class="pull-left form-control" placeholder="Search By Subject" style="width: 200px; margin-left: -16px" />
   <button style="margin-right: -14px;"type="button" id="openCreate" class="btn btn-primary pull-right form-group"
					data-toggle="modal" data-target="#myModal" ng-click="openLetter()">Create
					Letter</button>
   </div>
	<div class="row">
	<div class="col-lg-12" ng-if="showLetter == false">
          
               
            
         
			<table class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th>Sr No.</th>
						<th>Subject</th>
						<th>Status</th>
						<th>Created At</th>
					</tr>
				</thead>
				<tbody>
                                    <tr >
						<td colspan="4" style="font-weight: bold;" id="dataCheck">No Data Found.
							</td>
					</tr>
					<tr  ng-repeat="user in users">
						<td>@{{user.id}}</td>
						<td><a ng-click="showLetterClick()" href="javascript:void(0);">@{{user.subject}}</a></td>
						<!-- <td>@{{user.subject}}</td> -->
						<td>@{{user.status}}</td>
						<td>@{{user.created_at}}</td>
					</tr>
				</tbody>
			</table>
                       
			<div  ng-if="pagination.total > 0" class="row">
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
	</div>

	<div class="row">
		<div class="col-md-12" ng-if="showLetter == true">

			<div class="btn-toolbar">
				<a class="btn btn-primary"
					href="{{ route('officialcommunication') }}"><< Back to list</a>
			</div>


			<div class="row">
				<div class="col-lg-12">

					<h3>Letter</h3>
					<p>
						<span class="highlight">To :</span>@{{role.role_name}}
					</p>
					<p>
						<span class="highlight">By :</span>@{{letter.first_name}}
					</p>
					<div class='clear-both'></div>
					<p>
						<span class="highlight">Subject :</span>@{{letter.subject}}
					</p>
					<p>
						<span class="highlight">Text :</span>@{{letter.text}}
					</p>
					<p>
						<span class="highlight">Created On :</span>@{{letter.created_at |
						date:'dd-MMM-yyyy' }}
					</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<strong>Conversations:</strong>
					<div>
						<!-- For replies  -->
						<div class="media" ng-repeat="replies in letter.replies">
							<div class="media-left">
								<img class="media-object" style="width: 40px;"
									src="http://mscinaccounting.teipir.gr/images/7dffaf16a20087c779dec810d45f9713.jpg">
							</div>
							<div class="media-body">
								<h4 class="media-heading">@{{replies.first_name}}</h4>
								<div class="media-text">@{{replies.comment}}</div>
							</div>
						</div>

					</div>
				</div>
			</div>


			<!--<a id="loadmore"
					class="@{{hasreplies}} @{{activeTopic.letter_count}}"
					href="javascript:void(0)" ng-click="loadmore()"
					ng-if="activeTopic.letter_count>10 || hasreplies ">loadMore</a> -->

			<div class="row">
				<div class="col-md-6">
					<form name="reply_form" ng-submit="replysubmit()" id="reply-form"
						enctype="multipart/form-data" method="POST">
						<div class="form-group">
							<label class="form-label">Comment</label>
							<textarea ng-model="reply.text" type="text" name="text"
								class="form-control" placeholder="Text" rows="4" id="replytext"></textarea>
						</div>
						<button type="submit" id="reply_submit" class="btn btn-primary">Submit</button>
					</form>
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
					<h4 class="modal-title" id="myModalLabel">Create Communication</h4>
				</div>
				<form ng-submit="submit()" class="modal-body" id="myform"
					name="myform" novalidate>
					<div class="form-group">
						<label class="form-label" for="emailsubject">Role</label> <select
							name="role" ng-model="comm.recepient_id" id="role_id"
							class="form-control" required>
							<option value="" disabled="" selected="">Select Roles</option>
							<option ng-repeat="role in roles" value='@{{role.id}}'
								ng-if="role.is_unique == '1' && role.role_name != 'Admin'">@{{role.role_name}}</option>
						</select> <label class="error"
							ng-show="myform.$submitted && myform.role.$invalid "> Please
							enter a valid role </label>
					</div>
					<div class="form-group">
						<label class="form-label" for="emailsubject">Subject</label> <input
							name="email_subject" ng-model="comm.subject" type="text"
							class="form-control" id="emailsubject" placeholder="Subject"
							required> <label class="error"
							ng-show="myform.$submitted && myform.email_subject.$invalid ">
							Please enter a valid subject </label>
					</div>
					<div class="form-group">
						<label class="form-label" for="emailtext">Email Text</label>
						<textarea ng-model="comm.text" type="text" name="email_text"
							class="form-control" placeholder="Text" rows="4" id="emailtext"
							required>
                    </textarea>
						<label class="error"
							ng-show="myform.$submitted && myform.email_text.$invalid ">
							Please enter a valid text </label>
					</div>
					<div class="form-group">
						<label class="form-label" for="emailsubject">Subject Reference</label>
						<input name="subject_ref" ng-model="comm.subject_reference"
							type="text" class="form-control" id="subjectref"
							placeholder="Subject Reference" required> <label class="error"
							ng-show="myform.$submitted && myform.subject_ref.$invalid ">
							Please enter a valid reference </label>
					</div>
					<div class="form-group">
						<label class="form-label" for="emailsubject">Document Reference</label>
						<input name="document_ref" ng-model="comm.document_reference"
							type="text" class="form-control" id="documentref"
							placeholder="Document Reference" required> <label class="error"
							ng-show="myform.$submitted && myform.document_ref.$invalid ">
							Please enter a valid reference </label>
					</div>
					<div class="form-group">
						<button id="buttonsuggest" type="submit" class="btn btn-primary"
							ng-disabled="disable">@{{ disable ? 'Creating Letter..' :
							'Create' }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- End Modal -->
</div>

<script>
     $(document).on("click", "#reply_submit", function() {
        $("#reply-form").validate({
                   rules: {
                       text:"required",
                   }
               });
     });
    </script>

@stop
