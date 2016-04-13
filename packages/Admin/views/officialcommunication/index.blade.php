@extends('admin::layouts.admin_layout') @stop @section('panel_title',
'Official Communication')@section('panel_subtitle','List')
@section('title', 'Official Communication')
 @stop
@section('head')
<script src="{{asset('js/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
@stop @section('content')

<script>

    app.controller('OfficialCommControllerr',function(URL,paginationServices,$scope,$http,$filter){
        $scope.data=null;
//        
        
        $scope.showLetter=false;
        $scope.hasreplies=false;
        $scope.letter={};
        $scope.role={};
        $scope.activeLetter=null;
        $scope.replies=null;
        $scope.reply= {};
        $scope.pagination = paginationServices.getNew(5);
        $scope.itemsPerPage = 5;  
        $scope.search='';
        $scope.sort = 'subject';
        $scope.sort_order = 'desc';        

         $scope.getLetterList = function(offset,limit,sort,sort_order,search) {
            var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order};
            if(search){
                   options['search']=search;
                }
            var request_url = generateUrl('v1/officialcomm/letterlist',options);
            $http.get(request_url)
            .success(function(response, status, headers, config) { 
                $scope.letters = response.results;                           
                $scope.pagination.total = response.total.letter_count;
                $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                console.log($scope.pagination.pageCount);
                if($scope.pagination.total == 0) {
                    $('#dataCheck').show();
                }else {
                    $('#dataCheck').hide();
                }
            });
        };
        
        $scope.$watch('search', function(newValue, oldValue) {
        if(newValue){
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.sort = 'subject';
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
        
        $scope.$on('pagination:updated', function(event,data) {
            console.log($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
            $scope.getLetterList($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
        });
       
        $scope.approve = function(letter_id){
            console.log(letter_id);
            var confirm_msg = confirm("Are you sure you want to approve?");
            if(confirm_msg == true)
            {
                var $data = {
                		id:letter_id,
                        status:'approved'
                }
    	        $http.post(generateUrl('v1/officialcomm/update'),$data)
    	        .then(function(response) {
    	            grit('','Letter approved');
    	            $http.get(generateUrl('v1/officialcomm/letterlist')).
    	            then(function(response) {
    	                console.log(response.data.results);
    	            	$scope.getLetterList($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
    	            });
    	        });
            }else{
                return false;
            }
        };

        $scope.disapprove = function(letter_id){
            console.log(letter_id);
            var confirm_msg = confirm("Are you sure you want to disapprove?");
            if(confirm_msg == true)
            {
                var $data = {
                		id:letter_id,
                        status:'disapproved'
                }
    	        $http.post(generateUrl('v1/officialcomm/update'),$data)
    	        .then(function(response) {
    	            grit('','Letter disapproved');
    	            $http.get(generateUrl('v1/officialcomm/letterlist')).
    	            then(function(response) {
    	                console.log(response.data.results);
    	            	$scope.getLetterList($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
    	            });
    	        });
            }else{
                return false;
            }
        };

/*          $http.get(generateUrl('v1/officialcomm/letterlist')).
         then(function(response) {
             console.log(response.data.results);
         	$scope.letters = response.data.results;
         });  */


	    $scope.showLetterClick=function(){

	    		$scope.showLetter=true;
	    		$scope.activeLetter=this.letter;
	    		//console.log($scope.activeLetter.letter_count);
				$id=this.letter.id;
	            $http.get(generateUrl('v1/officialcomm/letter/'+this.letter.id)).
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
                      	  //console.log($scope.activeLetter.letter_count);
                          //console.log(response.data.response);
                          console.log(response.data.results);
                          $scope.letter.replies.push(response.data.results);
                          $scope.reply={};
		  		});
            }
            else{
                console.log("form is not valid!");
            }
         }
    })
</script>

<div ng-controller="OfficialCommControllerr" class="col-lg-12">
	<div class="row">
            <div ng-show="showLetter == false" class="col-lg-12 form-group">
			<input ng-model="search"
				class="pull-left form-control ng-pristine ng-untouched ng-valid"
				placeholder="Search By Subject" style="width: 200px">
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12" ng-if="showLetter == false">
			<table class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th>Sr No.</th>
						<th><a href="javascript:void();" ng-click="order('subject')">Subject</a>
							<span class="sortorder" ng-show="predicate === 'subject'"
							ng-class="{reverse:reverse}"></span></th>
						<th><a href="javascript:void();" ng-click="order('first_name')">Sent
								By</a> <span class="sortorder"
							ng-show="predicate === 'first_name'" ng-class="{reverse:reverse}"></span></th>
						<th><a href="javascript:void();" ng-click="order('created_at')">Created
								on</a> <span class="sortorder"
							ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span></th>
						<th><a href="javascript:void();" ng-click="order('status')">Status</a>
							<span class="sortorder" ng-show="predicate === 'status'"
							ng-class="{reverse:reverse}"></span></th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr  style="margin-left: 30px;">
						<td colspan="6" style="font-weight: bold;" id="dataCheck">
							No Data Found.</td>
					</tr>
					<tr ng-if="pagination.total > 0" ng-repeat="letter in letters">
						<td>@{{letter.id}}</td>
						<td><a ng-click="showLetterClick()" href="javascript:void(0);">@{{letter.subject}}</a></td>
						<td>@{{letter.first_name}}</td>
						<td>@{{letter.created_at}}</td>
						<td>@{{letter.status}}</td>
						<td><a class="glyphicon glyphicon-check"
							ng-if="letter.status == 'pending'" href="javascript:void(0); id="
							approve_id"
							title="Approve" ng-click="approve(letter.id)"></a> <a
							class="glyphicon glyphicon-remove"
							ng-if="letter.status == 'pending'" href="javascript:void(0); id="
							reject_id" title="Reject" ng-click="disapprove(letter.id)"></a></td>
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
			<!-- 			<div ng-if="pagination.total > 0">
				<ul id="pagination-demo" class="pagination-sm"></ul>
			</div> -->
		</div>
	</div>

	<div class="row">
		<div class="col-md-12" ng-if="showLetter == true">

			<div class="btn-toolbar pull-right">
				<a class="btn btn-primary"
					href="{{ route('admin.officialcommunication') }}"><< Back to list</a>
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
					<strong>Conversations</strong>
					<div>
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

			<!-- 			<a id="loadmore"
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

</div>
<script>
     $(document).on("click", "#reply_submit", function() {
//         $("#reply-form").click(function(){
//           console.log("hi");
//         });
        $("#reply-form").validate({
                   rules: {
                       text:"required",
                   }
               });
     });
    </script>
@stop
