 @extends('admin::layouts.admin_layout') @section('panel_title','Forum') @stop
@section('panel_subtitle','List') @stop @section('head')
<script src="{{asset('js/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
@stop @section('content')

<script>


    app.controller('ForumController',function(URL,paginationServices,$scope,$http,$filter){
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
    $('#loader').hide();

//    $scope.pagination = paginationServices.getNew(5);
//    $scope.itemsPerPage = 5;
//    $scope.search='';

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
                $("#loadmore").show();
            }
        });
    }

    $scope.showTopicClick=function(){
            $scope.activeTopic=this.topic;
            console.log($scope.activeTopic.reply_count);
            $scope.showTopic=true;
            $http.get(generateUrl('adminforum/topic/'+this.topic.id)).
        	then(function(response) {
        	console.log($scope.activeTopic.reply_count);
            $scope.topic = response.data.response;
            console.log(response);
            $http.get(generateUrl('adminforum/topic/reply/'+ $scope.topic.id)).
            then(function(response) {
                    console.log($scope.topic.id);
                    $scope.topic.replies = response.data.response;
                console.log(response);
            });
        });
    }


    $scope.submit=function(){
        if ($('#forum-form').valid()){
            $('#loader').show();
            $('#forum-form').find('button[type=submit]').attr('disabled',true);
            $('#forum-form').find('button[type=submit]').text('Creating topic please wait..');
            var $this=this;
            var $data = {
                            title:this.title,
                            text:CKEDITOR.instances.editor_start.getData()
                                    };
    /* 	$('#fileupload').fileupload({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: 'adminforum/topic/upload'
            });
            alert("alert"); */

    /*     var fd = new FormData();
        //Take the first selected file
        fd.append("file", this.files);
            alert(this.files);
        $http.post(generateUrl('adminforum/topic/upload'), fd, {
            withCredentials: true,
            headers: {'Content-Type': undefined },
            transformRequest: angular.identity
        }).success( "...all right!..." ).error( "..damn!... "); */


//             if(!document.getElementById('replytitlesubmit').value.trim().length){
//                    alert("Please enter forum title");
//                    return;
//                }
//    /* 	 else if(!document.getElementById('editor_start').value.trim().length){
//                            alert("Please enter forum text");
//                             return;
//                    } */
//
//             else if(CKEDITOR.instances.editor_start.getData() == ''){
//                            alert("Please enter forum text");
//                             return;
//                    }

//             else{
/*     		var $this=this;
            $this.title="";
            CKEDITOR.instances.editor_start.setData("");
            $this.startform.$setPristine();
            $("#myModal label.error").remove();
            $('#fileupload').val(''); */
//                            $http.post(generateUrl('adminforum/topic/save'), $data).
                var records = new FormData($('#forum-form')[0]);
                console.log(records);
                            $http({
                url: generateUrl('adminforum/topic/save'),
                method: "POST",
                data: records,
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            }).
                              then(function(response) {
                                    $('#loader').hide();
                                      $("#forum-form").find('button[type=submit]').attr('disabled',false);
                                      $("#forum-form").find('button[type=submit]').text('Create Topic');
                                      console.log(response.data.response);
                                      $scope.topics.splice(0,0,response.data.response.data);
                                      $('a[href="#topic"]').tab('show');
                                      $this.title="";
                                      CKEDITOR.instances.editor_start.setData("");
                                      $this.startform.$setPristine();
                                      //CKEDITOR.instances.editor_start.setData("");
                                      $("#forum-form label.error").remove();
                                      $('#fileupload').val('');
                                      window.location.href='{{ route('admin.forums') }}';
                                      grit('',response.data.response.msg);
                                      //console.log(rwindow.locationesponse.data.response);
                                      //alert(response.data.response.msg);
                                      //window.location = "http://localhost/apartment/public/index.php/admin/adminforum";
                              });
                          }else{
                console.log('form is not valid');
            }
//                    }
        }


    $scope.backToTopics=function(){
    		//$scope.pagination();
    		//console.log($scope.activeTopic);
            $scope.showTopic=false;
            $scope.topic.replies=null;
    }



//    $scope.backToTopics=function(){
//
//            $scope.showTopic=false;
//            $scope.topic.replies=null;
//    }



// $('#reply-form').submit(function(e){
//            e.preventDefault();
//             if($('#reply-form').valid()){
//                $scope.replysubmit();
//             }console.log("error");
//        });
    $scope.replysubmit=function(){
                if($('#reply-form').valid()){
                    $('#reply-form').find('button[type=submit]').attr('disabled',true);
                    $('#reply-form').find('button[type=submit]').text('Adding reply please wait..');
//             if(!document.getElementById('replytitle').value.trim().length){
//                    alert("Please enter reply title");
//                    return;
//                }
//             else if(!('replytext')||!document.getElementById('replytext').value.trim().length){
//                            alert("Please enter reply text");
//                             return;
//                     }
//
//
            console.log(this.reply);
            console.log($scope.topic.id);

                    var $data = {
                            title:this.reply.title,
                            parent_id:$scope.topic.id,
                            text:this.reply.text
                    }

                    $http.post(generateUrl('adminforum/topic/reply/save'), $data).
                      then(function(response) {
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
            
            $scope.openTask = function(){
			$('#TaskModal').modal();
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


<div ng-controller="ForumController" class="col-md-12">
    <div id="hide" class="col-lg-12" style= "height:50px;">
                    <input ng-model='search' ng-change='searchtopics()' class="form-control" placeholder="Search" style="width: 300px;">
    <span class="pull-right" style="padding:7px;">
                <button type="button" class="btn btn-primary" ng-click="openTask()">Start Topic</button>
    </span>        
    </div>

<!--	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#topic"
			ng-click="show()" aria-controls="topic" role="tab" data-toggle="tab">Topic</a></li>
                        <li role="presentation"><a  href="#start" aria-controls="start"
			ng-click="hide()" role="tab" data-toggle="tab">Start A Topic</a></li>
		 <li role="presentation"><a href="#search" aria-controls="search"
                            role="tab" data-toggle="tab">Search</a></li> 
	</ul>-->

	<!-- Tab panes -->
<!--	<div class="tab-content">
		 START 
		<div role="tabpanel" class="tab-pane active" id="topic">-->

			<div class="col-md-12" ng-if="showTopic == false">

<!--				<div class="row">
					<div class="col-md-8 form-group">
						<form ng-model="search" ng-change="change()" class="navbar-form navbar-left"
                                                            role="search">
                                                            <div class="form-group">
                                                                    <input type="text" class="form-control" placeholder="Search">
                                                            </div>
                                                    </form> 

						<input ng-model='search' ng-change='searchtopics()' class="form-control" placeholder="Search" style="width: 300px;">

					</div>
					 					<div class="col-md-4">
                                                    <div class="btn-group" role="group" aria-label="...">
                                                            <button type="button" class="btn btn-default">Copy</button>
                                                            <button type="button" class="btn btn-default">Excel</button>
                                                            <button type="button" class="btn btn-default">PDF</button>
                                                            <button type="button" class="btn btn-default">Print</button>
                                                    </div>
                                            </div> 
				</div>-->
				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered table-hover table-striped">
							<thead>
								<tr>
                                                                    <th><a href="" ng-click="order('title')">Topic</a>
                                                                    <span class="sortorder" ng-show="predicate === 'title'"
                                                                    ng-class="{reverse:reverse}"></span></th>
                                                                    <th><a href="" ng-click="order('created_at')">First Post</a>
                                                                    <span class="sortorder" ng-show="predicate === 'created_at'"
                                                                    ng-class="{reverse:reverse}"></span></th>
                                                                    <th><a href="" ng-click="order('reply_count')">Replies Count
                                                                    <span class="sortorder" ng-show="predicate === 'reply_count'"
                                                                    ng-class="{reverse:reverse}"></span></th>
									</div>
								</tr>
							</thead>
							<tbody>
                                                                <tr ng-if="pagination.total == 0">
                                                                    <td colspan="10" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                                                                </tr>
								<tr ng-if="pagination.total > 0" ng-repeat="topic in topics">
									<td><a ng-click="showTopicClick()" href="javascript:void(0);">@{{topic.title}}</a></td>
									<td>@{{topic.created_at}}</td>
									<td>@{{topic.reply_count}}</td>
								</tr>
							</tbody>
						</table>

						<!--						<div class="col-md-3"></div>
                                                    <div class="col-md-3">Group Email id:</div>
                                                    <a href="Himalya-adminforum@apartmentadda.com/">Himalya-adminforum@apartmentadda.com/</a>-->
					</div>
					<!--					<div>
						<ul id="pagination-demo" class="pagination-lg"></ul>
					</div>-->
				</div>
<!--				<div>
					<ul id="pagination-demo" class="pagination-sm"></ul>
				</div>-->

                                <div class="row">
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

			<!-- END Topic -->
			<!-- START show topic -->
			<div class="col-md-12" ng-if="showTopic == true">
				<h4 ng-if="topic.replies == null">loading..</h4>
				<!--				<a href="javascript:void(0)" ng-click="backToTopics()"
					style="margin-bottom: 20px;"><strong><< Back to topics</strong></a>-->

				<div class="btn-toolbar pull-right">
					<a class="btn btn-primary" href="{{ route('admin.forums') }}"><< Back to topics</a>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h4>@{{topic.title}}</h4>
						<div ng-bind-html="topic.text"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<strong>Attachment <a
							href="<?php echo url('dashboard/documents/download') ?>?file=@{{topic.http_path}}&name=@{{topic.name}}">@{{topic.name}}</a></strong>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						Created by: <a href="javascript:coid(0)">@{{topic.first_name}}</a>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-12"></div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<strong>Replies</strong>
						<div>
							<!-- For replies  -->
							<div class="media" ng-repeat="replies in topic.replies">
								<div class="media-left">
									<img class="media-object" style="width: 40px;"
										src="http://mscinaccounting.teipir.gr/images/7dffaf16a20087c779dec810d45f9713.jpg">
								</div>
								<div class="media-body">
									<h4 class="media-heading">@{{replies.first_name}}</h4>
									<div>
										<strong>@{{replies.title}}</strong>
									</div>
									<div class="media-text">@{{replies.text}}</div>
								</div>
							</div>

						</div>
					</div>
				</div>
				<a id="loadmore"
					class="@{{hasreplies}} @{{activeTopic.reply_count}}"
					href="javascript:void(0)" ng-click="loadmore()"
					ng-if="activeTopic.reply_count>10 || hasreplies ">loadMore</a>

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
			<!-- END show topic  -->
		</div>


		<!-- next panel -->

<!--		<div role="tabpanel" class="tab-pane" id="start">
			<div class="row">
				<div class="col-md-7">
					<form ng-submit="submit()" id="forum-form"
						enctype="multipart/form-data" method="POST" name="startform">
						<div class="form-group">
							<label class="form-label">Title</label> <input ng-model="title"
								type="text" class="form-control" placeholder="Title"
								id="titlesubmit" name="title" maxlength="35"
								ng-disabled="user_input_disabled">
						</div>
						<div class="form-group">
							<label class="form-label">Text</label>
							<textarea ng-model="text" name="editor_start" id="editor_start"
								rows="10" cols="80" placeholder="Text"
								ng-disabled="user_input_disabled"> </textarea>
							<script>
	                                         CKEDITOR.replace( 'editor_start' );
                                         </script>
						</div>
						<div class="form-group">
							<input id="fileupload" name="file" type="file" ng-model="files"
								enctype="multipart/form-data" ng-disabled="user_input_disabled">
						</div>
						<button type="submit" class="btn btn-primary">Create Topic</button>
					</form>
                                    <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
				</div>
			</div>
		</div>-->
        
        <!-- Modal-->
    <div class="modal fade" id="TaskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Task</h4>
                </div>
                <div class="modal-body">
                    <form ng-submit="submit()" id="forum-form"
						enctype="multipart/form-data" method="POST" name="startform">
						<div class="form-group">
							<label class="form-label">Title</label> <input ng-model="title"
								type="text" class="form-control" placeholder="Title"
								id="titlesubmit" name="title" maxlength="35"
								ng-disabled="user_input_disabled">
						</div>
						<div class="form-group">
							<label class="form-label">Text</label>
							<textarea ng-model="text" name="editor_start" id="editor_start"
								rows="10" cols="80" placeholder="Text"
								ng-disabled="user_input_disabled"> </textarea>
							<script>
	                                         CKEDITOR.replace( 'editor_start' );
                                         </script>
						</div>
						<div class="form-group">
							<input id="fileupload" name="file" type="file" ng-model="files"
								enctype="multipart/form-data" ng-disabled="user_input_disabled">
						</div>
						<button type="submit" class="btn btn-primary">Create Topic</button>
					</form>
                                     <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                                    <div id="loader" class="loading">Loading&#8230;</div>
          </div>
        </div>
      </div>
    </div>
        
        <!---->

		<!-- next panel -->

		<!-- 		<div role="tabpanel" class="tab-pane" id="search">

                            <table>
                                    <tr>
                                            <form class="navbar-form navbar-left" role="search">
                                                    <div class="form-group">
                                                            <input type="text" class="form-control" placeholder="">
                                                    </div>
                                                    <button type="submit" class="btn btn-default">Search</button>
                                            </form>
                                    </tr>
                                    <tr>
                                            <td>
                                                    <div class="radio">
                                                            <label> <input type="radio" name="optionsRadios"
                                                                    id="optionsRadios1" value="option1" checked> Topic No.
                                                            </label>
                                                    </div>
                                            </td>
                                            <td>
                                                    <div class="radio">
                                                            <label> <input type="radio" name="optionsRadios"
                                                                    id="optionsRadios2" value="option2"> Topic
                                                            </label>
                                                    </div>
                                            </td>
                                            <td>
                                                    <div class="radio disabled">
                                                            <label> <input type="radio" name="optionsRadios"
                                                                    id="optionsRadios3" value="option3"> Author
                                                            </label>
                                                    </div>
                                            </td>
                                            <td>
                                                    <div class="radio disabled">
                                                            <label> <input type="radio" name="optionsRadios"
                                                                    id="optionsRadios3" value="option3"> Discussion
                                                            </label>
                                                    </div>
                                            </td>
                                    </tr>
                            </table>
                    </div> -->
	</div>
</div>
<script>
        $('document').ready(function(){
            $("#forum-form").validate({
                ignore: [],
                rules: {
                    title: {
                        required:true,
                        minlength:5,
                    },
                    editor_start:{

                         required: function()
                        {

                         CKEDITOR.instances.editor_start.updateElement();
                        },
                        minlength:5,
                    }
                }
            });
        });
    </script>
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
