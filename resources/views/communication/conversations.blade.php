@extends('layouts.default')
@section('title', 'Conversations')
@section('panel_title')Conversations and Groups @stop
@section('head')
<style>.replypost{display:none;}
    .active{
font-weight: normal;}
</style>
@stop

@section('content')
<script type="text/javascript">
app.controller('ConversationController',function(paginationServices,$scope,$http){
	$scope.user_id = "{!! session()->get('user.user_id') !!}";
	$scope.conversations = null;
	$scope.toggleReply = false;
	$scope.disableGrpSubmit = false;
	$scope.disableConvSubmit = false;
        
	$('#loader1').hide();
        $('#loader2').hide();
        $('#loader').hide();
        
        $scope.pagination = paginationServices.getNew(5);
        $scope.itemsPerPage = 5;  
        $scope.search='';
        
	$http.get(generateUrl('post/list'))
	.then(function(r){
		$scope.conversations = r.data.response;
	});

	$scope.submitConversation = function() {
		if(this.composer.$invalid)
			return;
		$('#loader').show();
		var $this = this;
		$this.disable = true;
		$scope.disableConvSubmit = true;
		$http.post(generateUrl('post/create'),$scope.conversation)
			.then(function(r){
                                $('#loader').hide();
				$scope.conversations.splice(0,0,r.data.response.data);
				$this.composer.$setPristine();
				$scope.conversation = {};
				$this.disable = false;
			});
	}

	
	$scope.like = function(parent_id,group) {
		
		var id = null;
		var like;
		if (parent_id == null) {

			if (this.cn.liked) {
				this.cn.liked = false;
				this.cn.like_count 
				= parseInt(this.cn.like_count) - 1;
				
			} else {
				this.cn.liked = true;
				this.cn.like_count 
				= parseInt(this.cn.like_count) + 1;
				
			}

			like = this.cn.liked;
			
			id = this.cn.id;
			
		} else {


			if (this.reply.liked) {
				this.reply.liked = false;
				this.reply.like_count 
				= parseInt(this.reply.like_count) - 1;
				
			} else {
				this.reply.liked = true;
				this.reply.like_count 
				= parseInt(this.reply.like_count) + 1;
				
			}

			like = this.reply.liked;
			
			id = this.reply.id;
		}
		
		var data = {
			entity_id: id,
			like: like
		};
		
		$http.post(generateUrl('like/create',data))
		.then(function(r){
			
		});
	};

	$scope.reply = function(parent_id,group) {
		var $this = this;	
		$this.disable = true;
		var text;
		
		$('#loader').show();	
                $('#loader2').show();
		text = this.cn.reply.edit.text;
		this.cn.reply.disable = true;
		
		var data = {
			text: text,
			post_id: this.$parent.cn.id
		};
		
		$http.post(generateUrl('reply/create'),data)
		.then(function(r){
			$('#loader').hide();
                        $('#loader2').hide();
			$this.cn.replies.push(r.data.response.data);
			$this.cn.reply.edit.text = "";
			$this.cn.reply_count = parseInt($this.cn.reply_count) + 1;
			$this.disable = false;
			
		});
	};

	$scope.toggleReplyClick = function(group) {

		
		var $i = this.$index;
		var id = this.cn.id;
		$('#loader2').show();
		if ($scope.toggleReply == this.cn.id)
			$scope.toggleReply = false;
		else
			$scope.toggleReply = id;

		if (group) {
			if ($scope.group.group.conversations[$i].repliesLoaded || $scope.group.group.conversations[$i].repliesLoading)
				return;
		} else {
			if ($scope.conversations[$i].repliesLoaded || $scope.conversations[$i].repliesLoading)
				return;
		}
		
		this.cn.repliesLoading = true;
		$http.get(generateUrl('reply/list/'+id)).
		then(function(r){
                    $('#loader2').hide();
			if (group) {
				$scope.group.group.conversations[$i].replies = r.data.response;
				$scope.group.group.conversations[$i].repliesLoaded =  true;
				$scope.group.group.conversations[$i].repliesLoading = false;
				
			} else {
				$scope.conversations[$i].replies = r.data.response;
				$scope.conversations[$i].repliesLoaded =  true;
				$scope.conversations[$i].repliesLoading = false;
			}
		});
		
	};

	$scope.groupsLoaded = false;
	$scope.groupsLoading = false;
	$scope.groups = null;
        
        $scope.loadGroups = function(offset,limit,sort,sort_order,search) {
                 var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order};
            if(search){
                   options['search']=search;
                }   
		$scope.groupsLoading = true;
		$http.get(generateUrl('group/list',options))
		.then(function(r){
                    $scope.groups = r.data.response.data;            
                    $scope.groupsLoaded = true;
                    $scope.pagination.total = r.data.response.count;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);	
                    if($scope.pagination.total=='0') {
					    $("#area").show();
					    $('#dataCheck').html('No Data Found');
					}else{
					  $("#area").hide();
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
            $scope.loadGroups($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
        });
        
        $scope.loadGroups($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
        
        $scope.loadGroup = function() {
                $('#loader2').show();
		$scope.group.active = true;
		$scope.group.group = this.group;
		var groupId = this.group.id;
		$http.get(generateUrl('group/post/list/'+groupId)).
		then(function(r){
                    $('#loader2').hide  ();
			$scope.group.group.conversations = r.data.response;
		});
	}
        
	$scope.submitGroup = function() {

		if (this.grpForm.$invalid)
			return;
		$('#loader1').show();
		$scope.disableGrpSubmit = true;
		var $this = this;
		
		$http.post(generateUrl('group/create'),$scope.group.form).
		then(function(r){
                    
                       $('#loader1').hide();
                       
			grit('','Group created successfully!');
                        $("#area").hide();
			$('#create_group').modal('hide');
			$scope.loadGroups($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);

			$scope.group.form = {};
			
			$this.grpForm.$dirty = false;
		});

		
	};

	$scope.showGrpForm = function () {
		$scope.disableGrpSubmit = false;
         $scope.grpForm.$setPristine();
		$('#create_group').modal('show');
	};
	
	
        
	$scope.group = {};
	$scope.group.active = false;
        
	

	$scope.backToGroups = function() {
		$scope.group.active = false;
	}

	$scope.submitGroupConversation = function() {

		if (this.grp_composer.$invalid)
			return;
		$('#loader2').show();
		var $this = this;
		$this.disable = true;
		$scope.group.group.conversation.group_id = $scope.group.group.id;
		//console.log($scope.group.group.conversation);return;
		$http.post(generateUrl('group/post/create'),$scope.group.group.conversation)
		.then(function(r){
                    $('#loader2').hide();
			$scope.group.group.conversations.splice(0,0,r.data.response.data);
			$this.grp_composer.$setPristine();
			$scope.group.group.conversation = {};
			$this.disable = false;
		});
     };
                
         $scope.submitJoin = function(insideGroup)
         {  
           
	    var $this = this;
            $this.disable = true;
             var groupId;
                if (insideGroup)  
                {
                    groupId = $scope.group.group.id;
                    $this = $scope.group; 
                } else
                {
                   groupId = this.group.id;
                   $this = this;  
                }
     
	            $http.post(generateUrl('group/join'),{'group_id':groupId})
	            .then(function(r) {
                       
	                $this.group.user_id = r.data.response.user_id; 
	                grit('',r.data.response.msg);
                            $this.disable = false;
	            });
		};

		$scope.closeGroupForm = function() {
			//console.log(this);return;
			$('#create_group').modal('hide');
			$scope.group.form = {};
			this.grpForm.$setPristine();
		}
		
		   $scope.textFormat = function(text){
               var text =  text.replace(/(<([^>]+)>)/ig,"");   // To strip html tags
           
                var shortText = jQuery.trim(text).substring(0, 50)
                          .trim(this)+ '...'; 
                return shortText;
            };
            
            $scope.delete = function() {                            
                var r = confirm("Deleted type cannot be retrieved");
                if (r == true) {
                var $this = this;
                $http.get(generateUrl('group/delete/'+this.group.id))
                .then(function(r){
                    grit('',r.data.response.msg);                   
                    $scope.loadGroups($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
            });
       
        
       }
        else return;
            }
            
    $scope.edit = function() { 
        var $this = this;
        $scope.disableGrpSubmit = false;
        $('#create_group').modal('show');
        $http.get(generateUrl('group/edit/'+$this.group.id))
          .then(function(response) {
            $scope.group.form = response.data.response.data;
        });
    }
    
    $scope.close = function() {
        $scope.group.form = {};
        $scope.grp_composer.$setPristine(); 
        $scope.disableGrpSubmit = false;
    }
});
</script>

<div ng-controller="ConversationController" class="col-md-12">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#conversations" aria-controls="conversations" role="tab" data-toggle="tab">All Conversations</a></li>
    <li role="presentation"><a href="#groups" aria-controls="groups" role="tab" data-toggle="tab" >My Groups</a></li>
  </ul>

    <!-- Tab panes -->
  <div class="tab-content">
  
  <!-- Conversations -->
    <div class="tab-pane active" id="conversations">
        <div class="well">
<!--<div class="btn-group form-group" data-toggle="buttons">
  <label class="btn btn-default active">
    <input type="radio" name="options" id="option1" autocomplete="off" checked> Conversation
  </label>
  <label class="btn btn-default">
    <input type="radio" name="options" id="option2" autocomplete="off"> Poll
  </label>
  <label class="btn btn-default">
    <input type="radio" name="options" id="option3" autocomplete="off"> Photo
  </label>
</div>-->
    <form ng-submit="submitConversation()" name="composer" novalidate>
	<div class="form-group">
	<input required type="text" placeholder="Title" class="form-control" name="title" ng-model="conversation.title" ng-minlength="5">
	  <label class="error" ng-show="composer.$submitted && composer.title.$invalid">Title should contain atleast 5 characters.</label>
	</div>
	<div class="form-group">
	<textarea class="form-control" name="text" ng-model="conversation.text" ng-minlength="5" required></textarea>
	  <label class="error" ng-show="composer.$submitted && composer.text.$invalid">Text should contain atleast 5 characters.</label>
	</div>
	<div class=""><button class="btn btn-primary pull-right" disabled type="submit" 
	ng-disabled="disable" >Post</button></div>
	<div class="clearfix"></div>
<!--         <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>-->
	</form>
</div>
<div ng-if="conversations == null"><strong>loading..</strong></div>

<div class="media-list">
<div class="media" ng-repeat="cn in conversations">
  <div class="media-left">
    <a href="#">
      <img class="media-object" style="width:40px;" src="http://mscinaccounting.teipir.gr/images/7dffaf16a20087c779dec810d45f9713.jpg">
    </a>
  </div>
  <div class="media-body">
  <div><a class="media-heading" href="#"><strong>@{{cn.first_name}} @{{cn.last_name}}</strong></a></div>
    <div>
	    <div><strong>@{{cn.title}}</strong></div>
		<div>@{{cn.text}}</div>
    </div>
    	<div style="margin-bottom: 20px;"><strong><a href="javascript:void(0)" ng-click="toggleReplyClick()">@{{cn.reply_count||0}} replies</a></strong> <strong style="margin-left: 10px"><a href="javascript:void(0)" ng-click="like(null)">@{{cn.like_count||0}} @{{cn.liked ? 'unlike':'like'}}</a></strong></div>
    	<div ng-if="toggleReply == cn.id">
    	    <div class="media" ng-repeat="reply in cn.replies">
    	    <div class="media-left">
			    <a href="#">
			      <img class="media-object" style="width:40px;" src="http://mscinaccounting.teipir.gr/images/7dffaf16a20087c779dec810d45f9713.jpg">
			    </a>
			  </div>
	    	<div class="media-body">
	    	<div><a class="media-heading" href="#"><strong>@{{reply.first_name}}</strong></a></div>
	    	<div>@{{reply.text}}</div>
	    	<div><strong><a href="javascript:void(0)" ng-click="like($parent.$index)">@{{reply.like_count||0}} @{{reply.liked ? 'unlike':'like'}}</a></strong></div>
	    	</div>
	    	</div>
	    	<form ng-submit="reply(cn.id)" style="margin-top: 20px" novalidate name="reply_form">
	    	<div class="form-group">
	    	<textarea rows="" cols="" class="form-control" placeholder="reply" ng-model="conversations[$parent.$index].reply.edit.text" required ng-minlength="5"></textarea>
	    	</div>
		    	<button class="btn btn-success btn-sm" type="submit" ng-disabled="disable || reply_form.$invalid">Reply</button>
	    	</form>
    	</div>
    
  </div>
  
</div>
</div>
    
    </div>
    <!-- End Conversations -->
    
    <!-- Groups -->
    <div role="tabpanel" class="tab-pane" id="groups">
        <!--<input ng-model="search" class="form-control pull-left ng-pristine ng-untouched ng-valid" ng-change="searchGroup()" placeholder="Search By Title" >-->
        <input style="width: 200px;" ng-model="search"
                ng-show="group.active == false" class="pull-left form-control"
                placeholder="Search By Title" >
        <div ng-if="group.active == false">
        <div class="row">
        <div class="col-lg-12 form-group">			
			<!--<input ng-model="search" class="form-control pull-left ng-pristine ng-untouched ng-valid" ng-change ="loadGroups()" placeholder="Search By Title" style="width: 300px">-->
            <div class="btn-group pull-right"><button class="btn btn-primary" ng-click="showGrpForm()">Create Group</button></div>
        </div>
    </div>
        
        <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th><a href="#" >Sr No.</a></th>
<!--                        <th>userId</th>-->
                        <th>
                            <a href="" ng-click="order('title')">Title</a>
                            <span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('description')">Description</a>
                            <span class="sortorder" ng-show="predicate === 'description'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th><a href="" ng-click="order('first_name')">Created By</a>
                            <span class="sortorder" ng-show="predicate === 'first_name'" ng-class="{reverse:reverse}"></span></th>
                       <th><a href="#" >Join Group</a></th>
                        <th><a href="#" >Action</a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="display:none;" id="area" >
                        <td colspan="7" style="font-weight: bold;" id="dataCheck"></td>
                    </tr>
                    <tr ng-repeat="group in groups">
                        <td>@{{group.id}}</td>
<!--                        <td>@{{user_id}}</td>-->
                        <td>@{{group.title}}</td>
                                <!-- <td>@{{group.text}}</td> -->
                                 <td ng-bind-html="textFormat(group.text)" style="clear:both;word-wrap: break-word;"></td>
                                 <td>@{{group.first_name}} @{{group.last_name}}</td>
                        <td>
                            <button ng-disabled="disable" ng-if="group.user_id == null" ng-click="submitJoin(false)" href="javscript:void(0)" class="btn btn-primary" role="button">Join</button>
                            <button    ng-if="group.user_id != null" disabled class="btn btn-primary btn-sm" role="button">Joined</button>
                        </td>
                        <td>
                            <a class="glyphicon glyphicon-eye-open" title="view" role="button" ng-click="loadGroup()"></a>
                            <a ng-show="user_id == group.user_id"class="glyphicon glyphicon-pencil" ng-click="edit()" href="" title="Edit" ></a>
                            <a ng-show="user_id == group.user_id" class="glyphicon glyphicon-remove" ng-click="delete() "href=""  title="Delete"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
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
    </div>
 
           
 
    </div>
      <div ng-if="group.active == true">
      <div><a class=" btn btn-primary" href="javascript:void(0);" ng-click="backToGroups()"><strong><< Back to groups</strong></a></div>
        <div ng-if="group.group.user_id == null" class="alert alert-info" role="alert">
            <p>
                Please join @{{group.group.title}} to start conversation
                <button  ng-disabled="disable" ng-if="group.group.user_id == null" ng-click="submitJoin(true)" href="javscript:void(0)" class="btn btn-primary" role="button">Join Group </button>
            </p>
        </div>
        <div>
        <h1>@{{group.group.title}}</h1>
        <p>@{{group.group.text}}</p>
        </div>
        <div ng-if="group.group.user_id != null">
                <div class="well">
                <form ng-submit="submitGroupConversation()" name="grp_composer" novalidate>
				<div class="form-group">
				<input required type="text" placeholder="Title" class="form-control" name="title" ng-model="group.group.conversation.title" ng-minlength="5">
				  <label class="error" ng-show="grp_composer.$submitted && grp_composer.title.$invalid">Title should contain atleast 5 characters.</label>
				</div>
				<div class="form-group">
				<textarea class="form-control" name="text" ng-model="group.group.conversation.text" ng-minlength="5" required></textarea>
				  <label class="error" ng-show="grp_composer.$submitted && grp_composer.text.$invalid">Text should contain atleast 5 characters.</label>
				</div>
				<div class=""><button class="btn btn-primary pull-right" disabled type="submit" ng-disabled="disable">Post</button></div>
				<div class="clearfix"></div>
<!--                                      <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                                    <div id="loader2" class="loading">Loading&#8230;</div>    -->
				</form> 
                    
                </div>
		</div>
      
      		      <div class="media-list">
<div class="media" ng-repeat="cn in group.group.conversations">
  <div class="media-left">
    <a href="#">
      <img class="media-object" style="width:40px;" src="http://mscinaccounting.teipir.gr/images/7dffaf16a20087c779dec810d45f9713.jpg">
    </a>
  </div>
  <div class="media-body">
  <div><a class="media-heading" href="#"><strong>@{{cn.first_name}} @{{cn.last_name}}</strong></a></div>
    <div>
	    <div><strong>@{{cn.title}}</strong></div>
		<div>@{{cn.text}}</div>
    </div>
    	<div style="margin-bottom: 20px;"><strong><a href="javascript:void(0)" ng-click="toggleReplyClick(true)">@{{cn.reply_count||0}} replies</a></strong> <strong style="margin-left: 10px"><a href="javascript:void(0)" ng-click="like(null,true)">@{{cn.like_count||0}} @{{cn.liked ? 'unlike':'like'}}</a></strong></div>
    	<div ng-if="toggleReply == cn.id">
    	    <div class="media" ng-repeat="reply in cn.replies">
    	    <div class="media-left">
			    <a href="#">
			      <img class="media-object" style="width:40px;" src="http://mscinaccounting.teipir.gr/images/7dffaf16a20087c779dec810d45f9713.jpg">
			    </a>
			  </div>
	    	<div class="media-body">
	    	<div><a class="media-heading" href="#"><strong>@{{reply.first_name}}</strong></a></div>
	    	<div>@{{reply.text}}</div>
	    	<div><strong><a href="javascript:void(0)" ng-click="like($parent.$index,true)">@{{reply.like_count||0}} @{{reply.liked ? 'unlike':'like'}}</a></strong></div>
	    		    	
	    	</div>
	    	</div>
	    	<form ng-submit="reply(cn.id,true)" style="margin-top: 20px" name="reply_form">
	    	<div class="form-group">
	    		<textarea rows="" cols="" class="form-control" placeholder="reply" ng-model="group.group.conversations[$parent.$index].reply.edit.text" ng-minlength="5" required></textarea>
	    	</div>
		    	<button class="btn btn-success btn-sm" type="submit" ng-disabled="disable || reply_form.$invalid">Reply</button>
	    	</form>
    	</div>
  </div>
  
</div>
</div>
        </div>     

      
     
      


    </div>
    <!-- End Groups -->
    
  </div>
  
  
    <div class="modal fade" id="create_group">
  <div class="modal-dialog">
  <form class="form-horizontal" ng-submit="submitGroup()" name="grpForm" novalidate>
	  <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" ng-click="closeGroupForm()"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Create Group</h4>
      </div>
      <div class="modal-body" >
        
		  <div class="form-group" style="width: 500px;margin-left: 10px;">
    <label for="title" class=" form-label">Group name</label>
      <input type="text" name="title" class="form-control form-label ng-pristine ng-invalid ng-invalid-required ng-valid-pattern ng-valid-minlength ng-touched" id="title" placeholder="Title" ng-model="group.form.title" required ng-minlength="5">
      <label id="title-error" ng-show="grpForm.$submitted && grpForm.title.$invalid" class="error" for="title">This field is required and should contain atleast 5 characters.</label>
  </div>
  <div class="form-group" style="width: 500px;margin-left: 10px;">
    <label for="text" class=" form-label">Group Description</label>
      <textarea class="form-control form-label ng-pristine ng-invalid ng-invalid-required ng-valid-pattern ng-valid-minlength ng-touched" name="text" ng-model="group.form.text" required ng-minlength="5"></textarea>
      <label id="title-error" ng-show="grpForm.$submitted && grpForm.text.$invalid" class="error" for="title">This field is required and should contain atleast 5 characters.</label>
  </div>
            <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader1" class="loading">Loading&#8230;</div>
      </div>
      <div class="form-group" style="width: 500px;margin-left: 15px;">
        
        <button type="submit" class="btn btn-primary" ng-disabled="disableGrpSubmit">@{{ group.form.id != null ? 'Update' : 'Create'}}</button>
		<button type="button" class="btn btn-primary"  ng-click="closeGroupForm()">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
            
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>
@stop
