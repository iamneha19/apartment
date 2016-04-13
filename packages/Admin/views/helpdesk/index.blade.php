@extends('admin::layouts.admin_layout') @section('title','Help Desk')
@section('panel_title','Help Desk') @section('head')
<style>
    .marginSetter{ margin-left: 0px !important; }
    .form-group input:not([type=checkbox]):not([type=radio]),.form-group select,.form-group textArea{
        width: 90%;
    }
</style>
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
<script>

const society_id = "{!! session()->get('user.society_id') !!}";

app.controller('HelpDeskController',function(URL,paginationServices,$scope,$http,$filter){
$=jQuery.noConflict();
	$scope.openIssues = [];
	$scope.closedIssues = [];
	$scope.ticket = {
		is_urgent:'No'
	};
	$scope.categories;
	$scope.requesting = false;
	$scope.ticketView = false;
	$scope.activeTicket = {};
  $scope.activeType = 'Helpdesk';
//
//  $scope.search='';
  $scope.status = 1;
  $scope.sort = 'id';
  $scope.form = {};

  $scope.search1 = '';
  $scope.sort1 = 'id';
  $scope.itemsPerPage = 5;
  $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
  $scope.pagination.itemsPerPage = $scope.itemsPerPage;
  $scope.pagination.total =0;
  $scope.pagination2 = paginationServices.getNew($scope.itemsPerPage);
  $scope.pagination2.total =0;
  $scope.noClosedData = 0 ; 
  $scope.offset=0;
  $scope.sort_order = 'desc';

	$scope.offset1=0;
  $scope.sort_order1 = 'desc';
  jQuery('#loader1').hide();
//   jQuery('#loader2').hide();



       	jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
       		jQuery('#search_open_admin').val('');
       		jQuery('#search_close_admin').val('');
    	});
               
//        $scope.getOpenIssues = function(offset,limit,sort,sort_order,search,block,status,backStatus,ticketStatus) {
//			if ( typeof backStatus == undefined ){
//				backStatus = 0;
//			}
//			if ( typeof ticketStatus == undefined ){
//				ticketStatus = 0;
//			}
//            var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order,search:search};
//            $http.get(generateUrl('helpdesk/ticket/list',options))
//            .then(function(r){
//              $scope.openIssues = r.data.response.data;   
//              $scope.pagination.total = r.data.response.count.count;
//			  if ( $scope.pagination.currentPage != 1 && ($scope.pagination.total % $scope.pagination.itemsPerPage == 0) && backStatus == 1 ){
//				  $scope.pagination.setPage($scope.pagination.total / $scope.pagination.itemsPerPage);
//			  }
//                $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
//                if ($scope.pagination.total == 0){
//                        jQuery("#dataCheckOpen").text("No Data Found.");
//                }
//				if ( ticketStatus == 'New' ){
//						jQuery(function() {
//							jQuery("#newLink").trigger('click');
//						});
//				}	
//                });
//        }
//
//        $scope.getOpenIssues($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,null);
//
//        $scope.getCloseIssues = function(offset,limit,sort,sort_order,search,block,status,backStatus,ticketStatus) {
//			if ( typeof backStatus == undefined ){
//				backStatus = 0;
//			}
//			if ( typeof ticketStatus == undefined ){
//				ticketStatus = 0;
//			}
//			var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order,search:search,status:status};
//            $http.get(generateUrl('helpdesk/ticket/list',options))
//            .then(function(r){
//                    $scope.closedIssues = r.data.response.data;                   
//                    $scope.pagination2.total = r.data.response.count.count;
//                    console.log($scope.pagination2.total);
//					if ( $scope.pagination2.currentPage != 1 && ($scope.pagination2.total % $scope.pagination2.itemsPerPage == 0) && backStatus == 1 ){
//						$scope.pagination2.setPage($scope.pagination2.total / $scope.pagination2.itemsPerPage);
//					}
//                    $scope.pagination2.pageCount = Math.ceil($scope.pagination2.total/$scope.pagination2.itemsPerPage);
//          					if ($scope.pagination2.total == 0){
//								$scope.noClosedData = 1 ; 
//                        $("#dataCheckClosed").text("No Data Found.");
//                    }
//					if ( ticketStatus == 'Closed' ){
//						jQuery(function() {
//							jQuery("#resolvedLink").trigger('click');
//						});
//					}
//            });
//        }
//
//        $scope.getCloseIssues($scope.offset1,$scope.itemsPerPage,$scope.sort1,$scope.sort_order1,$scope.search1,$scope.block,'Closed');
	
	$scope.getIssues = function(offset,limit,sort,sort_order,search,issueType) {
			if ( typeof  issueType == "undefined" ){
				issueType = 0;
			}
			if ( typeof  search == "undefined" ){
				search = "";
			}
            var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order,search:search,issueType:issueType};
            $http.get(generateUrl('helpdesk/ticket/list',options))
            .then(function(r){
              $scope.issues = r.data.response.data;
              $scope.pagination.total = r.data.response.count.count;
              
                        $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                        if ($scope.pagination.total == 0){
                        $("#dataCheckOpen").text("No Data Found.");
                    }
                });
    }
	
	$scope.changeIssueType  = function() {
		$scope.pagination.total = 0;
		$scope.pagination.offset = 0;
		$scope.pagination.currentPage = 1;
		$scope.pagination.issueType= $("#issue_type option:selected").val();
        $scope.getIssues($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.form.search,$scope.pagination.issueType);
	}
	
	$http.get(generateUrl('v1/admin/list/typeList/'+$scope.activeType))
	.then(function(r){

		$scope.categories = r.data.results.data;

	});

	$scope.submitTicket = function() {
		if (this.ticket_form.$invalid)
			return;
		jQuery('#loader1').show();
		var $this = this;
		this.disable = true;

		$http.post(generateUrl('helpdesk/ticket/save'),$scope.ticket).
		then(function(r){
      jQuery('#loader1').hide();
			jQuery('#create_ticket').modal('hide');
//			$scope.openIssues.push(r.data.response.data);
			$scope.ticket = {};
			grit('','Ticket created successfully!');
			$this.disable = false;
			$this.ticket_form.$setPristine();
            
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.pagination.setPage(1); 

		});

	};

    $scope.$on('pagination:updated', function(event,data) {
		 $scope.getIssues($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.form.search,$scope.pagination.issueType);
	});
	
	$scope.$watch('form.search', function(newValue, oldValue) {
		if(newValue){
			console.log(newValue);
			$scope.pagination.total=0;
			$scope.pagination.offset = 0;
			$scope.pagination.currentPage = 1;
			$scope.pagination.setPage(1);
		}else{
			$scope.pagination.setPage(1);
		}
	});

	$scope.activeTicket.loading = false;
	$scope.showTicket = function(){
		jQuery('#loader').show();
		$scope.ticketView = true;
		$scope.activeTicket.loading = true;
		$http.get(generateUrl('helpdesk/ticket/'+this.issue.id))
		.then(function(r){
                    jQuery('#loader').hide();
			$scope.activeTicket.data = r.data.response;
			$http.get(generateUrl('helpdesk/ticket/'+$scope.activeTicket.data.id+'/note/list'))
			.then(function(r){
				$scope.activeTicket.notes = r.data.response;
			});
			jQuery("#openStatusSetter").val($scope.activeTicket.data.ticket_status);
			$scope.activeTicket.loading = false;
		});

	};

	$scope.backToTickets = function(ticketStatus) {
		$scope.activeTicket.note={};
		$scope.ticketView = false;
		$scope.backStatus = 1;
		if ( jQuery("#openStatusSetter").val() == 'New' ){
			ticketStatus = 'New';
		}
                $scope.getIssues($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.form.search,$scope.pagination.issueType);
//		$scope.getOpenIssues($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,null,$scope.backStatus,ticketStatus);
//		$scope.getCloseIssues($scope.pagination2.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search,$scope.block,null,$scope.backStatus,ticketStatus);
	};

	$scope.submitNote = function() {
		if (this.note_form.$invalid)
			return;
		jQuery('#loader').show();
		var $this = this;
		this.disable = true;
		$http.post(generateUrl('helpdesk/ticket/'+$scope.activeTicket.data.id+'/admin_note/save'),$scope.activeTicket.note)
		.then(function(r){
			jQuery('#loader').hide();
			$scope.activeTicket.notes.push(r.data.response.data);
			$this.disable = false;
			$this.note_form.$setPristine();
		//	$filter('filter')($scope.openIssues,{id: $scope.activeTicket.data.id},true)[0].ticket_status = $scope.activeTicket.note.status;
    $("#messagenoteadmin").val("");
			if($scope.activeTicket.data.ticket_status != "Closed" && $scope.activeTicket.note.status == 'Closed') {
				$scope.openIssues = $filter('filter')($scope.openIssues, function(value, index) {return value.id !== $scope.activeTicket.data.id});
				$scope.closedIssues.push($scope.activeTicket.data);
			} else if($scope.activeTicket.data.ticket_status == 'Closed' && $scope.activeTicket.note.status != 'Closed') {
				$scope.closedIssues = $filter('filter')($scope.closedIssues, function(value, index) {return value.id !== $scope.activeTicket.data.id});
				$scope.openIssues.push($scope.activeTicket.data);

			}

			$scope.activeTicket.data.ticket_status = $scope.activeTicket.note.status;
			$scope.activeTicket.note = {};
		});

	};

	$scope.categories = [];
	$scope.category;


	$scope.submitCategory = function() {
		if (this.category_form.$invalid)
			return;
//		jQuery('#loader').show();
		var $this = this
		this.disable = true;

		$http.post(generateUrl('helpdesk/category/save'),$scope.category)
		.then(function(r){
//                jQuery('#loader').hide();
			if (!r.data.response.success) {
				alert(r.data.response.msg);
				$this.disable = false;
				return;
			}
			jQuery('#category_form').modal('hide');
			$scope.categories.push(r.data.response.data);
			grit('',r.data.response.msg);
			$scope.category = {};
			$this.disable = false;
			$this.category_form.$setPristine();
		});
	};

	$scope.closeTicketForm = function() {
		jQuery('#create_ticket').modal('hide');
		this.ticket_form.$setPristine();
		$scope.ticket = {};
		jQuery('#issue').val('');
	}

	$scope.closeCategoryForm = function() {
		jQuery('#category_form').modal('hide');
//                jQuery("#category_form")[0].reset();
		this.category_form.$setPristine();
		$scope.category = {};
    jQuery('#description').val('');

	}


    $scope.resetFilter = function()
    {
          $scope.block = '';
          $scope.pagination.total=0;
          $scope.pagination.offset = 0;
          $scope.pagination.currentPage = 1;
          $scope.sort = 'id';
          $scope.sort_order = 'desc';
          $scope.pagination.setPage(1);
    }

    $scope.order = function(predicate) {

      if($scope.status == 1) {
        $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
        $scope.predicate = predicate;
        $scope.pagination.total=0;
        $scope.pagination.offset = 0;
        $scope.pagination.currentPage = 1;
        $scope.sort = predicate;
        $scope.sort_order = ($scope.reverse) ? 'asc' : 'desc';
        $scope.pagination.setPage(1);
      } else {
        $scope.reverse1 = ($scope.predicate1 === predicate) ? !$scope.reverse1 : false;
        $scope.predicate1 = predicate;
        $scope.pagination2.total=0;
        $scope.pagination2.offset = 0;
        $scope.pagination2.currentPage = 1;
        $scope.sort1 = predicate;
        $scope.sort_order1 = ($scope.reverse1) ? 'asc' : 'desc';
        $scope.pagination2.setPage(1);
      }
    };


     $scope.tab = function(status) {
          $scope.status = status;
      };

    $scope.getFlats = function() {
        $http.get(generateUrl('v1/flats/', {
            'society_id': society_id,
        }))
       .then(function(response) {
           $scope.flats = response.data.results;
       });
    }

    $scope.getFlats();

//    $scope.showSearch = function(name) {
//        if (name == 'open') {
//            jQuery('#search_open_admin').removeClass('hide');
//            jQuery('#search_close_admin').addClass('hide');
//        } else if (name == 'close') {
//            jQuery('#search_open_admin').addClass('hide');
//            jQuery('#search_close_admin').removeClass('hide');
//        }
//    }
//
//    jQuery(function() {
//        $scope.showSearch('open');
//    });
    
    /*Reminders Module start here*/
        $scope.reminders;
        $scope.flat_reminder;
        $scope.society_reminder;
        $scope.offcial_reminders;
        $scope.getReminders = function() {
            var request_url = generateUrl('v1/reminders');
            $http.get(request_url)
            .success(function(result, status, headers, config) {
                $scope.reminders = result.results;
                if($scope.reminders)
                {
                    jQuery.each($scope.reminders, function (i, el) {
                        $('.page-header').before('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> This is a reminder for your meeting i.e '+el.title+' schedule on '+el.date+' </div>');
                       });
                }
            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };
        $scope.getReminders();

        $scope.getFlatReminders = function()
        {
            var category_name = '';
            var request_url = generateUrl('v1/flat_document/reminders');
            $http.get(request_url)
            .success(function(result, status, headers, config) {
                $scope.flat_reminder = result.results;
                if($scope.flat_reminder)
                {
                    jQuery.each($scope.flat_reminder, function (i, el) {
                       category_name = '';
                       jQuery.each(el.cat, function (cat, cat_name) {
                            category_name += cat_name.name+ ", ";
//                           $('.page-header').before('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> Documents for flat '+i+' category '+cat_name.name+' not uploaded! </div>');
                       });
                       var category = category_name.slice(0, -2);
//                        $('.page-header').before('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> Documents for flat '+i+' category <b>'+category+'</b> not uploaded! </div>');
                        if(category)
                        {
                            $('.page-header').before('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> Documents for flat no '+i+' are not uploaded for following categories: <b>'+category+'</b>.</div>');
                        }
                       });
                }
            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };
        $scope.getFlatReminders();

        $scope.getSocietyReminders = function()
        {
            var category_name = '';
            var society_name = '';
            var request_url = generateUrl('v1/society/reminders');
            $http.get(request_url)
            .success(function(result, status, headers, config) {
                $scope.society_reminder = result.results;
                    if($scope.society_reminder)
                    {
                        jQuery.each($scope.society_reminder, function (i, el) {
    //                        console.log(el);
                           if(el.file_name==null)
                           {
                              category_name += el.name+ ", ";
                              society_name =  el.society_name;
                            }
                        });
                         var category = category_name.slice(0, -2);
    //                    console.log(result);
                        if(category)
                        {
    //                        $('.page-header').before('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> Documents for society '+society_name+' category <b>'+category+'</b> not uploaded! </div>');
                            $('.page-header').before('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> Society documents are not uploaded for following categories: <b>'+category+'</b>.</div>');
                        }
                    }
    }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };
        $scope.getSocietyReminders();

        $scope.getOffCommReminders = function()
        {
            var subject_name = '';
            var society_name = '';
            var request_url = generateUrl('v1/off_comm/reminders');
            $http.get(request_url)
            .success(function(result, status, headers, config) {
                $scope.offcial_reminders = result.results;
                    if($scope.offcial_reminders)
                    {
                        jQuery.each($scope.offcial_reminders, function (i, el) {
                              subject_name += el.subject+ ", ";
                        });
                         var subject = subject_name.slice(0, -2);
                        if(subject)
                        {
                            $('.page-header').before('<div class="alert alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> This is a official communication reminder regarding subject <b>'+subject+'</b>.</div>');
                        }
                    }
    }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };
        $scope.getOffCommReminders();


            $scope.textFormat = function(text){
               var text =  text.replace(/(<([^>]+)>)/ig,"");   // To strip html tags
               var shortText = jQuery.trim(text).substring(0, 50)
                          .trim(this) + '...';
              //+'<a href="'+notice_url;
              //+'/'+notice_id+'">Read More</a>'; 
                return shortText;
            };
    
    /*End*/
});

</script>
@stop
@section('content')
<div ng-controller="HelpDeskController" class="col-md-12">

	<div ng-if="ticketView == false">
		<div class="col-md-12">
			<!-- Tab panes -->
			<div class="tab-content" style="margin-top: 20px;">
				<div class="row form-group">
					<div class="col-md-6">
						<input type="text" ng-model="form.search" class="form-control ng-pristine ng-untouched ng-valid" placeholder="Search By Issue" style="width: 200px;display: inline" />
						<b style="margin-left:15px;">Issue Type : </b>
            <select id='issue_type'  name="issue_type" ng-model="issue_type"  ng-change="changeIssueType()" style="height: 30px;width: 100px;">
                <option disabled value="">Select Issue</option>
								<option ng-selected="true" value="0">All</option>
								<option value="1">Open</option>
								<option value="2">Close</option>
						<select>
					</div>
					<div class="col-md-6">
						<button class="btn btn-primary pull-right" data-toggle="modal"
							data-target="#create_ticket">Lodge A Helpdesk Ticket</button>
					</div>

				</div>
        
				<div role="tabpanel" class="tab-pane active" id="open">
					<table class="table table-bordered">
						<thead>
							<tr>
                  <th>Sr No.</th>
                  <th><a href="" ng-click="order('category_name')">Category</a>
                  <span class="sortorder" ng-show="predicate === 'category_name'"
                          ng-class="{reverse:reverse}"></span></th>
                  <th  style="width: 280px;"><a href="" ng-click="order('issue')">Issue</a>
                  <span class="sortorder" ng-show="predicate === 'issue'"
                          ng-class="{reverse:reverse}"></span></th>
                  <th style="width: 155px;"><a href="" ng-click="order('created_at')">Opened On</a>
                  <span class="sortorder" ng-show="predicate === 'created_at'"
                          ng-class="{reverse:reverse}"></span></th>
                  <th><a href="" ng-click="order('first_name')">Created By</a>
                  <span class="sortorder" ng-show="predicate === 'first_name'"
                          ng-class="{reverse:reverse}"></span></th>
                  <th><a href="" ng-click="order('flat_no')">Flat</a>
                  <span class="sortorder" ng-show="predicate === 'flat_no'"
                          ng-class="{reverse:reverse}"></span></th>
                  <th><a href="" ng-click="order('ticket_status')">Issue Type</a>
                  <span class="sortorder" ng-show="predicate === 'ticket_status'"
                          ng-class="{reverse:reverse}"></span></th>
                  <th><a href="" ng-click="order('is_urgent')">Urgency</a>
                  <span class="sortorder" ng-show="predicate === 'is_urgent'"
                          ng-class="{reverse:reverse}"></span></th>
							</tr>
</thead>
<tbody>
        <tr ng-if="pagination.total == 0">
			<td colspan="10" style="font-weight: bold;" id="dataCheckOpen">Fetching Data...</td>
        </tr>
		
                <tr ng-if="pagination.total > 0" ng-repeat="issue in issues">
                        <!--<td>@{{$index + 1 + (pagination.currentPage * 5) - 5}}</td>-->
                        <td>@{{issue.id}}</td>
                        <td>@{{issue.category_name}}</td>
                        <td><a href="javascript:void(0)" ng-click="showTicket()">@{{ ((issue.issue | limitTo:50) + '...') }}</a></td>
                        <!-- <td><a href="javascript:void(0)" ng-click="showTicket()">@{{ Str::limit(issue.issue,50) }}</a></td> -->
                        <td>@{{issue.created_at}}</td>
                        <td>@{{issue.first_name}}</td>
                        <td>@{{issue.flat}}</td>

                        <td class ng-show="issue.ticket_status == 'Progress'">WIP *</td>
                        <td ng-show="issue.ticket_status != 'Progress'">@{{issue.ticket_status}}</td>

                        <td>@{{issue.is_urgent}}</td>
                </tr>
        </tbody>

    </table>
          <h5> * WIP Stands For Work In Progress</h5>
					<div>
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
            </div>
					</div>
				</div>

				

				<div role="tabpanel" class="tab-pane" id="categories">
					<div class="btn-group pull-right">
						<button class="btn btn-primary" data-toggle="modal"
							data-target="#category_form">Create Category</button>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th>Id</th>
								<th>Category name</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="category in categories">
								<td>@{{category.id}}</td>
								<td>@{{category.category_name}}</td>
								<td>@{{category.description}}</td>
							</tr>
						</tbody>
					</table>
				</div>

			</div>


		</div>
	</div>

	<div ng-if="ticketView == true">
		<div class="row">
			<div class="col-md-12" style="margin-bottom: 20px;">
				<a href="javascript:void(0)" class="btn btn-primary" ng-click="backToTickets(activeTicket.data.ticket_status)">
          << Back to Tickets</a>
			</div>
		</div>
		<input type="hidden" id="openStatusSetter" value="" />
		<div class="row" ng-if="activeTicket.loading == true">
			<div class="col-md-12">
				<h2>Loading ..</h2>
			</div>
		</div>
		<div class="row" ng-if="activeTicket.loading == false">
			<div class="col-md-12">
				<p>
					<strong>Category: </strong> @{{activeTicket.data.category_name}}
				</p>
				<p>
					<strong>Issue: </strong> @{{activeTicket.data.issue}}
				</p>
				<p>
					<strong>Flat: </strong> @{{activeTicket.data.flat_no}}
				</p>
				<p>
					<strong>Opened on: </strong> @{{activeTicket.data.created_at}}
				</p>
				<p>
					<strong>Created By: </strong> @{{activeTicket.data.first_name}}
				</p>
				<p>
					<strong>Status: </strong> @{{activeTicket.data.ticket_status}}
				</p>
				<hr>
			</div>

			<div class="col-md-6">
				<h3>Comments</h3>
				<div>
					<div class="media" ng-repeat="note in activeTicket.notes">
						<div class="media-body">
							<h4 class="media-heading">@{{note.first_name}}</h4>
							<p>@{{note.note}}</p>
							<p>
								<small>@{{note.status_update}}</small>
							</p>
						</div>
					</div>
				</div>

				<form ng-submit="submitNote()" class="form-horizontal"
					style="margin-top: 20px;" name="note_form" novalidate>
					<div class="form-group" style="margin-left: 0px;">
						<label class="control-label">Status</label>
						<!--<div class="col-md-10">-->
						<select class="form-control" name="status"
							ng-model="activeTicket.note.status">
							<option value="">-- Select status --</option>
							<option value="New"
								ng-disabled="activeTicket.data.ticket_status == 'New'">New</option>
							<option value="Progress"
								ng-disabled="activeTicket.data.ticket_status == 'WIP'">WIP</option>
							<option value="Inhold"
								ng-disabled="activeTicket.data.ticket_status == 'Inhold'">Inhold</option>
							<option value="Closed"
								ng-disabled="activeTicket.data.ticket_status == 'Closed'">Closed</option>
						</select>
						<div>
							<small>Leave status blank if you don't want to change ticket
								status.</small>
						</div>
						<!--</div>-->
					</div>

					<div class="form-group" style="margin-left: 0px;">
						<label class="control-label">Message</label>
						<textarea id="messagenoteadmin" class="form-control" name="note" placeholder="message"
							ng-model="activeTicket.note.note" required ng-minlength="6"></textarea>
						<label class="error"
							ng-show="note_form.$submitted && note_form.note.$invalid">Please
							enter atleast 6 characters</label>
					</div>

					<div class="form-group" style="margin-left: 0px;">
						<button class="btn btn-primary " type="submit"
							ng-disabled="disable">Submit</button>
					</div>

				</form>


			</div>
		</div>
             <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
	</div>


	<div class="modal fade" id="create_ticket" data-backdrop="static">
		<div class="modal-dialog">
			<form class="form-horizontal" ng-submit="submitTicket()"
				name="ticket_form" novalidate>
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" ng-click="closeTicketForm()"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Lodge a helpdesk ticket</h4>
					</div>
					<div class="modal-body">
						<div class="form-group marginSetter">
							<label for="category" class="form-label">Category</label>
							<!--<div class="col-sm-9">-->
								<select class="form-control" id="category" name="category"
									ng-model="ticket.category_id" required>
									<option selected value="">-- Select Category --</option>
									<option ng-repeat="cat in categories" value="@{{cat.id}}">@{{cat.name}}</option>
								</select> <label class="error"
									ng-show="ticket_form.$submitted && ticket_form.category.$invalid">Please
									select category</label>
							<!--</div>-->
						</div>
						<div class="form-group marginSetter">
							<label for="flat" class="form-label">Flat</label>
							<!--<div class="col-sm-9">-->
								<select class="form-control" id="flat" name="flat"
									ng-model="ticket.flat_id" required>

<!-- 									<option selected value="">-- Select flat --</option>
									<option value="{{Session::get('user.flat_id')}}" selected>{{Session::get('user.flat_no')}}</option>
 -->
 									<option value="" selected>-- Select Flat --</option>

                                    <option ng-repeat="flat in flats" value="@{{ flat.id }}">@{{ flat.text }}</option>

								</select> <label class="error"
									ng-show="ticket_form.$submitted && ticket_form.flat.$invalid">Please
									select flat</label>
							<!--</div>-->
						</div>
						<div class="form-group marginSetter">
							<label class="form-label" for="issue">Issue</label>
							<!--<div class="col-sm-9">-->
								<textarea class="form-control" id="issue" name="issue"
									placeholder="Issue" ng-model="ticket.issue" required
									ng-minlength="10"></textarea>
								<label class="error"
									ng-show="ticket_form.$submitted && ticket_form.issue.$invalid">Issue
									should contain atleast 10 characters</label>
							<!--</div>-->
						</div>
						<div class="form-group marginSetter">
							<!--<div class="col-sm-9 col-sm-offset-3">-->
<!--								<div class="">
									<label> <input type="radio" name="access"> Personal
									</label> <label> <input type="radio" name="access"> Community
									</label>
								</div>-->
							<!--</div>-->
						</div>

						<div class="form-group marginSetter">
							<!--<div class="col-sm-9 col-sm-offset-3">-->
								<label> <input type="checkbox" name="is_urgent"
									ng-model="ticket.is_urgent" ng-true-value="'Yes'" ng-false-value="'No'"> This is urgent
								</label>
							<!--</div>-->
						</div>
					</div>
					<div class="form-group" style="margin-left: 10px;">
                                            <button type="submit" class="btn btn-primary"
							ng-disabled="disable">Submit</button>
                                            <button type="button" class="btn btn-primary"
							ng-click="closeTicketForm()">Cancel</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</form>
                    <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                <div id="loader1" class="loading">Loading&#8230;</div>
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->

	<div class="modal fade" id="category_form">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" ng-click="closeCategoryForm()"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Add Category</h4>
				</div>
				<form class="form-horizontal" ng-submit="submitCategory()"
					name="category_form" novalidate>
					<div class="modal-body">
						<div class="form-group marginSetter">
							<label for="category" class="form-label">Category
								name</label>
							<!--<div class="col-sm-9">-->
								<input type="text" name="category" class="form-control"
									id="category" placeholder="Category"
									ng-model="category.category_name" required ng-minlength="3"> <label
									class="error"
									ng-show="category_form.$submitted && category_form.category.$invalid">Category
									name should contain atleast 3 characters</label>
							<!--</div>-->
						</div>
						<div class="form-group marginSetter">
							<label for="description" class="form-label">Description</label>
							<!--<div class="col-sm-9">-->
								<input type="text" name="description" class="form-control"
									id="description" placeholder="Description"
									ng-model="category.description" ng-minlength="5"> <label
									class="error"
									ng-show="category_form.$submitted && category_form.description.$invalid">Description
									should contain atleast 5 characters</label>
							<!--</div>-->
						</div>
					</div>
                                    <div class="form-group" style="margin-left: 10px;">
						<button type="button" class="btn btn-primary"
							ng-click="closeCategoryForm()">Close</button>
						<button type="submit" class="btn btn-primary"
							ng-disabled="disable">Save changes</button>
					</div>
				</form>
<!--                            <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader2" class="loading">Loading&#8230;</div>-->
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->

</div>

@stop
