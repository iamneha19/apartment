@extends('admin::layouts.admin_layout')
@section('panel_title','Forum')
@section('panel_subtitle','List')
@section('head')
<script src="{{asset('js/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
@stop 
@section('content')

<script>


    app.controller('ForumController',function(URL,paginationServices,$scope,$http,$filter){
        $('#loader').hide();
    $scope.data=null;
    $scope.topics=null;
    $scope.showTopic=false;
    $scope.topic={};
    $scope.hasreplies=false;
    //$scope.search=null;
    $scope.reply={};
    $scope.replies=null;
    $scope.repliesoffset=0;
    $scope.activeTopic=null;
    
    $scope.pagination = paginationServices.getNew(5);
    $scope.pagination.total = 0;
    $scope.itemsPerPage = 10;
    $scope.sort = 'created_at';
    $scope.sort_order = 'desc';
    $scope.search='';
    $scope.status="1";

//    $scope.pagination = paginationServices.getNew(5);
//    $scope.itemsPerPage = 5;
//    $scope.search='';
    $scope.topics = function() {
                    

	$http.get(generateUrl('adminforum/topic/count'))
	.then(function(r){
                       

		//$scope.openIssues = r.data.response;
                $scope.pagination.total = r.data.response.topic_count;
                $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                if ($scope.pagination.total == 0)
                    $("#dataCheck").text("No Data Found.");
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
    }

        $scope.topics();
/*     $scope.pagination=function(){

            $('#pagination-demo').twbsPagination({
                    totalPages: 5,
                    visiblePages: 2,
                    onPageClick: function (event, page) {
                    $http.get(generateUrl('adminforum/topic/list')+'&page='+page).
                    then(function(response) {
                      $scope.topics = response.data.response;
                    });

                   //$('#page-content').text('Page ' + page);
                }
                    });

    } */

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		$('#titlesubmit').val('');
        CKEDITOR.instances.editor_start.setData("");
        $('#fileupload').val('');
        $scope.showTopic=false;
        $("#forum-form label.error").remove();
	})

    $scope.loadmore=function(){
    		//alert($scope.noreplies);
            $scope.repliesoffset=parseInt($scope.repliesoffset)+parseInt(10);
            $http.get(generateUrl('adminforum/topic/reply/'+ $scope.topic.id)+'&repliesoffset='+$scope.repliesoffset).
        	then(function(response) {
            console.log(response.data.response);
			if (response.data.response.length<10){
				$scope.topic.replies.push.apply($scope.topic.replies,response.data.response);
				$scope.hasreplies=false;
				$("#loadmore").hide();
			}else{
				$scope.topic.replies.push.apply($scope.topic.replies,response.data.response);
                $scope.hasreplies=true;
//                $("#loadmore").show();
            }
        });
    }

    $scope.showTopicClick=function(id){
            $scope.activeTopic=this.topic;
//            console.log($scope.activeTopic.reply_count);
            $scope.showTopic=true;
            $http.get(generateUrl('adminforum/topic/'+id)).
        	then(function(response) {
//        	console.log($scope.activeTopic.reply_count);
            $scope.topic = response.data.response;
            $http.get(generateUrl('adminforum/topic/reply/'+ $scope.topic.id)).
            then(function(response) {
                    $scope.topic.replies = response.data.response;
                console.log(response);
            }); 
        });
    }
    $scope.showTopicClick({{$id}});
	$scope.Page = {{$page}};
	$scope.Search = '{{$search}}';
    
	$scope.backToTopics=function(){
    		//$scope.pagination();
    		//console.log($scope.activeTopic);
            $scope.showTopic=false;
            $scope.topic.replies=null;
    }

    $scope.replysubmit=function(){
                if($('#reply-form').valid()){
                    $('#loader').show();
                    $('#reply-form').find('button[type=submit]').attr('disabled',true);
                    $('#reply-form').find('button[type=submit]').text('Adding reply please wait..');
//            console.log(this.reply);
//            console.log($scope.topic.id);

                    var $data = {
                            title:this.reply.title,
                            parent_id:$scope.topic.id,
                            text:this.reply.text
                    }

                    $http.post(generateUrl('adminforum/topic/reply/save'), $data).
                      then(function(response) {
                                    $('#loader').hide();
                                    $("#reply-form").find('button[type=submit]').attr('disabled',false);
                                    $("#reply-form").find('button[type=submit]').text('Reply');
                          	  $scope.activeTopic.reply_count=parseInt($scope.activeTopic.reply_count)+1
                          	  console.log($scope.activeTopic.reply_count);
                              console.log(response.data.response);
                              $scope.topic.replies.push(response.data.response.data);
                              $scope.reply={};

                              //console.log(response.data.response);
    /* 			  console.log(response.data.response);
                              alert(response.data.response.msg);
                              window.location = "http://localhost/apartment/public/index.php/admin/adminforum";
     */		  });
                }

                        else{
                            console.log("form is not valid!");
                        }
                }


    $scope.searchtopics=function(){
            $scope.search = this.search;
            $http.get(generateUrl('adminforum/topic/list')+'&search='+this.search).
        then(function(response) {
            console.log(this.search);
            $scope.topics = response.data.response;
			if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found.");
                    }
        });
    }

    $scope.getTopicsList=function(offset,limit,sort,sort_order,search,status) {
        var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order};
        if(search)
        {
            options['search'] = search;
        }
        $http.get(generateUrl('adminforum/topic/list',options)).
        then(function(response) {
            $scope.topics = response.data.response;
            console.log(response);
			$scope.pagination.total = response.data.total;
            $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
			if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }


        });
    }

    $scope.$on('pagination:updated', function(event,data) {
            $scope.getTopicsList($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.status);

        });

        $scope.$watch('search', function(newValue, oldValue) {
            if(newValue){
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'title';
                $scope.sort_order = 'asc';
                $scope.pagination.setPage(1);
            }else{
                $scope.pagination.setPage(1);
            }
        });

    $scope.resetFilter = function()
            {
                $scope.block = '';
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'title';
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
                $scope.getTopicsList($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,status);
            };
            
            $scope.openForum = function(){
			$('#ForumModal').modal();
		};
    // $('#pagination-demo').twbsPagination({
    //     totalPages: 10,
    //     visiblePages: 4,
    //     onPageClick: function (event, page) {
    //         $('#page-content').text($scope.topics);
    //     }
    // });

	 $scope.hide = function() {
             $('#hide').hide();
         }
         
          $scope.show = function() {
             $('#hide').show();
         }

    })
    </script>
<script>
      function copyToClipboard(text) {
        window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
      }
    </script>


<div ng-controller="ForumController" class="col-lg-12">
<div class="col-md-12" ng-if="showTopic == true">
    
  
<!--				<h4 ng-if="topic.replies == null"><b>Fetching Data...</b></h4>-->

				<div class="btn-toolbar pull-left">
					<a class="btn btn-primary" href="{{ route('admin.backforums') }}/@{{Page}}/@{{Search}}"><strong><< Back to topics</strong></a>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h5><b>Topic :</b> @{{topic.title}}</h5>
                        <h5><b>Text :</b> @{{topic.text}} </h5>
						<!--<div ng-bind-html="topic.text"></div>-->
					</div>
				</div>
<!--				<div class="row">
					<div class="col-md-12">
						<strong>Attachment <a
							href="<?php //echo url('dashboard/documents/download') ?>?file=@{{topic.http_path}}&name=@{{topic.name}}">@{{topic.name}}</a></strong>
					</div>
				</div>-->
				<div class="row">
					<div class="col-md-12">
						<b>Created by:</b> <a href="javascript:coid(0)">@{{topic.first_name}}</a>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-12"></div>
				</div>

                <div class="row">
                    <div class="col-md-12">
                        <b>Attachment:</b> 
                        <a href="<?php echo route('documents.download') ?>?file=@{{topic.http_path}}&name=@{{topic.name}}">@{{topic.name}}</a>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12"></div>
                </div>

				<div class="row">
					<div class="col-md-12">
						<strong>Replies :</strong>
						<div>
							<!-- For replies  -->
                              <div class="parcommit">   
							<div class="media" ng-repeat="replies in topic.replies">
                                
                               <div class="col-sm-1">
<div class="thumbnail">

<img class="img-responsive user-photo" src="http://mscinaccounting.teipir.gr/images/7dffaf16a20087c779dec810d45f9713.jpg">
</div><!-- /thumbnail -->
</div> 
                           
                                
                                   
<div class="col-sm-5">
<div class="panel panel-default">
<div class="panel-heading">
<strong>@{{replies.first_name}}</strong> <span class="text-muted">@{{replies.title}}</span>
</div>
<div class="panel-body">
@{{replies.text}}
</div><!-- /panel-body -->
</div><!-- /panel panel-default -->
</div>   
                                
							
							</div>
                              </div>
                         </div>
					</div>
				</div>
				<a id="loadmore"
					class="@{{hasreplies}} @{{topic.reply_count}}"
					href="javascript:void(0)" ng-click="loadmore()"
					ng-if="topic.reply_count>10 || hasreplies ">loadMore...</a>

				<div class="row">
					<div class="col-md-6">
						<form name="reply_form" ng-submit="replysubmit()" id="reply-form"
							enctype="multipart/form-data" method="POST">
							<div class="form-group">
								<label class="form-label">Title</label> <input
									ng-model="reply.title" type="text" class="form-control"
									name="title" placeholder="Title" id="replytitle">
							</div>
							<div class="form-group">
								<label class="form-label">Text</label>
								<textarea ng-model="reply.text" type="text" name="text"
									class="form-control" placeholder="Text" rows="4" id="replytext"></textarea>
							</div>
                                                        
							<button type="submit" id="reply_submit" class="btn btn-primary">Reply</button>
						</form>
                                               
					</div>
				</div>
	</div>
    <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                                                        <div id="loader" class="loading">Loading&#8230;</div>
</div>
<script>
     $(document).on("click", "#reply_submit", function() {
//         $("#reply-form").click(function(){
//           console.log("hi");
//         });
        $("#reply-form").validate({
                   rules: {
                       title: "required",
                       text:"required",
                   }
               });
     });
    </script>
@stop