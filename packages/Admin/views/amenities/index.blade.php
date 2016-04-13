@extends('admin::layouts.admin_layout')
@section('title', 'Amenities')
@section('panel_title') Amenities @stop
@section('panel_subtitle','List') @section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}"
	rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
@stop @section('content')
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
<script>
    app.controller("AmenitiesCtrl", function($scope, paginationServices, $http, $filter) {

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

    $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
	$scope.pagination.total =0;
	$scope.itemsPerPage = 5;
    $scope.sort = 'subject';
    $scope.sort_order = 'asc';
	$scope.offset =0;
    $('#loader').hide();



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
    	.then(function(r) {
     		var count = String(parseInt(r.data.amenities_count)/5 < 1 ? 1 : parseInt(r.data.amenities_count)/5);
     		if ( count != 1 ) {
     	 		var res = count.split(".",1);
     	 		countfinal = parseInt(res)+parseInt(1);
     	 		console.log(countfinal);
     		} else {
     			countfinal = 1;
     		}

     		$('#pagination-demo').twbsPagination({
    	         totalPages:  countfinal,
    	         visiblePages: 10,
                 first: false,
                 last: false,
                 prev : '<small> << </small> Prev',
                 next : 'Next <small> >> </small>',
    	         onPageClick: function (event, page) {
    		         $http.get(generateUrl('v1/amenities/amenitieslist')+'&page='+page).
    		         then(function(response) {
    		            $scope.users = response.data.results == 'undefined' ? response.data.results: [];
    					$scope.pagination.total = $scope.users.length;
                        console.log($scope.pagination.total);
                        $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
    					 if ($scope.pagination.total == 0 ) {
                             $("#dataCheck").text("No Data Found.");
                         }
    		         });
    	     	}
             });
    	});
    }

	$scope.getAmenitiesList = function(offset,limit,sort,sort_order) {
            var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order};

            var request_url = generateUrl('v1/amenities/amenitieslist',options);
            $http.get(request_url)
            .success(function(response, status, headers, config) {
                if(response.code == 200){
                    $scope.users = response.results;
                    $scope.pagination.total = response.total;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
					if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
                }else{
                   grit('','Enable to get Result');
                }

            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };

	$scope.getAmenitiesList($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order);

     $scope.$on('pagination:updated', function(event, data) {
         $scope.getAmenitiesList($scope.pagination.currentPage);
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
				$('#loader').show();
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

                                        $('#loader').hide();
                                        $('#myModal').modal('hide');
					//$scope.list();
					$(this).find('button[type=submit]').attr('disabled',false);
                    $(this).find('button[type=submit]').text('Creating Amenities please wait..');


					grit('','Amenities Saved Successfully');
					$this.disable=false;

					$scope.comm = {};
                                         $("#myform")[0].reset();
					$scope.myform.$setPristine();
					location.reload();
				});
		  }
    }

//      $scope.close=function(){
//              var $this=this;
//              $('#myModal').modal('hide');
//              console.log("close");
//              $scope.getRoles();
//              $('#amenitiesname').val("");
//              $('#amenitiesdescription').val("");
//              $('#amenitiesimage').val("");
////              $('#amenitiescharges').val("");
//              $scope.comm = {};
//              $scope.myform.$setPristine();
//    	}

        $scope.closeForm = function(){
            $("#myform")[0].reset();
            $scope.comm = {};            
            $scope.myform.$setPristine();            
            $('#myModal').modal('hide');
        };
	    $scope.replysubmit=function(){

            if($('#reply-form').valid()){
                $('#loader').show();
                $('#reply-form').find('button[type=submit]').attr('disabled',true);
                $('#reply-form').find('button[type=submit]').text('Adding comment please wait..');
                var $data = {
                		letter_id:$scope.letter.id,
                        comment:this.reply.text
                }

                $http.post(generateUrl('v1/officialcomm/letter/reply/save'), $data).
                  then(function(response) {
                      $('#loader').hide();
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
        $scope.id;
        $scope.name;
        $scope.description;
        $scope.image;
        $scope.openAmenitiesEditForm = function(id,name,description,image){
            $scope.id = id;
            $scope.name = name;
            $scope.description = description;
            $scope.image = image;
            $('#AmenitiesEditFormModal').modal();
            $('.file-static').show();
            $('.remove-file').show();
            $('.file-input').hide();
        };
        $scope.closeUpdateForm = function(){
            $("#Amenities-update-form")[0].reset();
            $("#Amenities-update-form label.error").remove();
            $('#AmenitiesEditFormModal').modal('hide');
        };

        $('#Amenities-update-form').submit(function(e){
        e.preventDefault();
        if ($(this).valid() && $scope.id){
            $(this).find('button[type=submit]').attr('disabled',true);
            $(this).find('button[type=submit]').text('updating amenities please wait..');
            var records = new FormData(this);
            var request_url = generateUrl('v1/amenities/update/'+$scope.id);
            $http({
                url: request_url,
                method: "POST",
                data: records,
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
//                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(function(response) {
                $("#Amenities-update-form").find('button[type=submit]').attr('disabled',false);
                $("#Amenities-update-form").find('button[type=submit]').text('Update');
                var result = response.data;
                if(result.status)
                {
                    grit('','Amenities updated successfully!');
                	$scope.list();
                    $('#AmenitiesEditFormModal').modal('hide');
                    location.reload();
                }
//            },
//            function(response) { // optional
//                   alert("fail");
            });
        }
    });
 $scope.delete = function(id){
        var confirm_msg = confirm("Are you sure to delete this document!");
        if(confirm_msg == true)
        {
            var request_url = generateUrl('v1/amenities/delete');
             var records = $.param({id:id});
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

    $scope.orderBy = function(name) {
    	$scope.getAmenitiesList($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order);
    }
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
                                            <th>Sr No.</th>
						<th>Name</th>
						<!--<th>Charges</th>-->
                        <th>Image</th>
						<th>Created on</th>
                        <th> Actions </th>
					</tr>
				</thead>
				<tbody>
                    <tr ng-if="pagination.total == 0" style="margin-left: 30px;">
						<td colspan="5" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
					</tr>
					<tr ng-if="pagination.total > 0" ng-repeat="user in users">
                                            <td>@{{user.id}}</td>
						<!--<td><a ng-click="showAmenitiesClick()" href="javascript:void(0);">@{{user.name}}</a></td>-->
                        <td>@{{user.name}}</td>
                        <td><a href="<?php echo url('dashboard/documents/downloadotherfile') ?>?file=@{{user.image}}&name=@{{user.image}}">@{{user.image}}</a></strong></td>
<!--					<tr ng-if="pagination.total == 0" style="margin-left: 30px;">
						<td colspan="3" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
					</tr>-->
<!--					<tr ng-if="pagination.total > 0" ng-repeat="user in users">
						<td><a ng-click="showAmenitiesClick()" href="javascript:void(0);">@{{user.name}}</a></td>
						 <td>@{{user.subject}}</td>
						<td>@{{user.charges}}</td>-->
						<td>@{{user.created_at}}</td>
                           <td>
                                <a class="glyphicon glyphicon-pencil" title="update" href="" ng-click="openAmenitiesEditForm(user.id,user.name,user.description,user.image)"></a>
                                <a class="glyphicon glyphicon-remove" ng-click="delete(user.id)" title="Delete" href=""></a>
                            </td>
					</tr>
				</tbody>
			</table>
<div id="pagination-demo" ng-if="pagination.total > 0" class="row">
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
<!--        </div>-->
		</div>
	</div>

	<div class="row">
		<div class="col-md-12" ng-if="showLetter == true">

			<div class="btn-toolbar">
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

<!--					<p>
						<span class="highlight">Charges :</span>@{{letter.charges}}
					</p>-->
					<p>
						<span class="highlight">Created On :</span>@{{letter.created_at |
						date:'dd-MMM-yyyy' }}
					</p>

					<strong>Attachment <a
						href="<?php echo url('dashboard/documents/downloadotherfile') ?>?file=@{{letter.image}}&name=@{{letter.image}}">@{{letter.image}}</a></strong>

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
						ng-click="closeForm()">
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
                                                <textarea id="w"ng-model="comm.description" type="text"
							name="description" class="form-control"
							placeholder="Description" rows="4" id="emailtext" required>
                    </textarea>
						<label class="error"
							ng-show="myform.$submitted && myform.amenities_description.$invalid ">
							Please enter a valid description </label>
					</div>

					<!-- upload image new -->
					<div class="form-group">
                                        <label class="form-label" for="amenitiesfile">File</label>
						<input id="fileupload" name="file" type="file" ng-model="comm.files"
							enctype="multipart/form-data" ng-disabled="user_input_disabled">
					</div>


<!--					<div class="form-group">
						<label class="form-label" for="amenitiescharges">Charges</label> <input
							name="charges"  ng-model="comm.charges" type="text"
							class="form-control" id="amenitiescharges" placeholder="Charges"
							required> <label class="error"
							ng-show="myform.$submitted && myform.document_ref.$invalid ">
							Please enter charges </label>
					</div>-->
					<div class="form-group">
						<button id="buttonsuggest" type="submit" class="btn btn-primary"
							>Submit</button>
                        <button class="btn btn-primary" type="button" ng-click="closeForm()" >Cancel</button>
					</div>
				</form>
                             <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                            <div id="loader" class="loading">Loading&#8230;</div>
			</div>
		</div>
	</div>
	<!-- End Modal -->

    <!--  EditModal  -->
    <div class="modal fade" id="AmenitiesEditFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="closeUpdateForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Amenities</h4>
                </div>
                <div class="modal-body">
                    <form id="Amenities-update-form" method="post" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">File</label>
                                <div class="form-control-static file-static">
                                    <p class="pull-left">@{{image}}</p>
                                    <p class="pull-right"><a class="glyphicon glyphicon-remove remove-file" title="remove" href="javascript:void(0);"></a></p>
                                </div>
                        <div class="form-control-static file-input" style="display: none;" >
                            <div class="form-control" style="padding-left: 0px;">
                                <input type="file" name="image" id="input_file" >
                            </div>
                            <div class="col-sm-2">
                                <p><a class="glyphicon glyphicon-remove remove-input" title="remove" href="javascript:void(0);"></a></p>
                            </div>
                        </div>
                    </div>
                        <div class="form-group">
                            <label class="form-label">Name </label>
                            <input type="text" class="form-control" name="name" maxlength="50" value="@{{name}}"  placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description </label>
                            <textarea  rows="4" type="text" class="form-control" name="description" maxlength="50" value="@{{description}}"  placeholder="Description">@{{description}}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <button class="btn btn-primary" type="button" ng-click="closeUpdateForm()">Cancel</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
</div>

<script>
     $(document).on("click", "#buttonsuggest", function() {
         $(':input[type="text"], textarea').change(function() {
                $(this).val($(this).val().trim());
            });
        $("#myform").validate({
                   rules: {
                       text:"required",
                       file:"required",
                       description:"required",
//					   charges : {
//                       number: true,
//				      }
                   },

               });


     });
    </script>
    <script>
        $(document).ready(function(){
            $("#Amenities-update-form").validate({
                   rules: {
                       name:"required",
                       image:"required",
                       description:"required",
//					   charges : {
//                       number: true,
//				      }
                   },

               });
            $('.remove-file').on('click',function(){
                $('.file-static').hide();
                 $('.file-input').show();
                 $('.remove-input').hide();
                  $('.remove-file').hide();

            });
            $('.remove-input').on('click',function(){
                $('.file-input').hide();
                $('.file-static').show();
//                $('.remove-input').hide();
            });
        });
    </script>

@stop
