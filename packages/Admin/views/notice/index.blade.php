@section('title', 'Notice Board')
@section('panel_title', 'Notice')@section('panel_subtitle','List')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("NoticeCtrl", function(paginationServices,$scope,$http,$filter) {
            $scope.notices;
            $scope.type = '1';
            $scope.pagination = paginationServices.getNew(3);
            $scope.pagination.itemsPerPage = 5;
			$scope.pagination.noticeType =0;
            $scope.noData = 0;
            $scope.search='';
            $('#loader').hide();

            $scope.getNotices = function(type,offset,limit,search,noticeType) {
               var options = {offset:offset,limit:limit};
               if(search){
                   options['search']=search;
               }
			   if ( typeof  noticeType == "undefined" ){
				   noticeType = 0;
			   }
			   options['type']=noticeType;
               var request_url = generateUrl('notice/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.notices = result.response.data;
                    if(result.response.total == 0)
                    {
                        $('#id').show();
                    }
                    else {
                         $('#id').hide();
                    }
                    $scope.pagination.total = result.response.total;
					$scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
					if ($scope.pagination.total == 0 ){ 
						$scope.noData = 1;
					}
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            
			$( "#notice_type" ).change(function() {
				$scope.pagination.total =0;
				$scope.noData = 0;
				$scope.pagination.offset = 0;
				$scope.pagination.currentPage = 1;
				$scope.pagination.noticeType= $("#notice_type option:selected").val();
                $scope.getNotices($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search,$scope.pagination.noticeType);
              });
			
            $scope.getRoles = function() {
                var request_url = generateUrl('acl/role/list');
                  $http.get(request_url)
                .success(function(result, status, headers, config) {
                        $scope.roles = result.response;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };


            $scope.$on('pagination:updated', function(event,data) {
				$scope.getNotices($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search,$scope.pagination.noticeType);
            });

            $scope.$watch('search', function(newValue, oldValue) {
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

			$scope.formatDateTime = function(date,time){
                var dateArray = date.split("-");
                if(time){
//                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
                    var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time).toDate();

                    return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
                }else{
//                   var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
                   var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]).toDate();
                   return $filter('date')(dateUTC, 'yyyy-MM-dd');
                }

            };

            $scope.displayDate = function(date){
                var dateArray = date.split("-");
//                dateUTC = new Date(date);
                var dateUTC = moment(date).toDate(); // to handle cross-browser
                return $filter('date')(dateUTC, 'd MMM yyyy');
            };

            $scope.textFormat = function(text,notice_id){
               var text =  text.replace(/(<([^>]+)>)/ig,"");   // To strip html tags
               var notice_url = '<?php echo route('admin.notice.view','');  ?>';
               notice_url = notice_url;

                var shortText = jQuery.trim(text).substring(0, 50)
                          .trim(this) + '...';
						  //+'<a href="'+notice_url+'/'+notice_id+'">Read More</a>';
                return shortText;
            };


            $scope.openForm = function(){
                $('#expiry-date').val('');
                $('#formModal').modal();
                $scope.getRoles();
            };
			
			 $scope.openEditForm = function(val){
			    $('#expiry-date').val('');
                $('#formModal').modal();
                $scope.getRoles();
            };
			
            $scope.closeForm = function(){
                $("#notice-form")[0].reset();
                $("#notice-form label.error").remove();
                CKEDITOR.instances.notice_desc.setData('');
                $('#formModal').modal('hide');
                $('#member_roles').hide();
            };

            $('#notice-form').submit(function(e){
                e.preventDefault();
                
                $('#notice_desc').val(CKEDITOR.instances.notice_desc.getData()); // Pass ckeditor data to textarea to validate
                if ($("#notice-form").valid()){
                    $('#loader').show();
                    var selected_type = $('input[name="type"]:checked').val();
                    var  enteredDate =  $('#expiry-date').val();
                    if(enteredDate != '' ){
                        var formatedDate = $scope.formatDateTime(enteredDate,'23:59:59');
                        $('#expiry-date').val(formatedDate); // Change format to Y-M-D H:i:s
                    }

                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Creating notice please wait..');
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('notice/create');
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                        $('#loader').hide();
                        var result = response.data.response; // to get api result
                        $("#notice-form").find('button[type=submit]').attr('disabled',false);
                        $("#notice-form").find('button[type=submit]').text('Submit');
                        if(result.success){
                            
                           
                            $scope.closeForm();
                            grit('','Notice created successfully!');
                            $scope.getNotices($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
                        }else{
                           // To handle server side validation errors
                           if(result.input_errors){
                               var errors = result.input_errors;
                              $('#expiry-date').val(''); // To reset expiry date
                               for (var key in errors) {
                                    var error = errors[key];
                                    for (var index in error) {
                                        if (key == "text"  ) {
                                            $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter("#cke_notice_desc");
                                        } else if (key == "type"  ) {
                                            $( ".form-group.type-radio-group" ).append( '<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' );
                                        } else if (key == "status"  ) {
                                            $( ".publish-error" ).html( '<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' );
                                        }else {
                                            $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('input[name="'+key+'"]');
                                        }
                                    }
                                 }

                           }else{
                               console.log('not input errors check msg');
                           }
                        }

                    },
                    function(response) { // optional
//                           alert("fail");
                    });
                }

            });
        });
    </script>
    <div class="col-lg-12" ng-controller="NoticeCtrl" >
        <div class="row form-group">
            <div class="col-lg-12">
				<input ng-model="search" class="form-control ng-pristine ng-untouched ng-valid" placeholder="Search By Title" style="width: 200px;display: inline">
				<b style="margin-left:15px;">Notice Type : </b><select id='notice_type'  name="notice_type" style="height: 30px;">
								<option disabled value="">Select Notice</option>
                                <option value="0">All</option>
								<option value="1">AGM Notices</option>
								<option value="2">General Notices</option>
								<option value="3">Special AGM Notices</option>
							</select>

                <!--<input ng-model="search" class="form-control ng-pristine ng-untouched ng-valid"  placeholder="Search By Title" style="width: 300px;display: inline">-->
                <div class="btn-toolbar pull-right">
                    <button type="button" class="btn btn-primary" ng-click="openForm()">Add Notice</button>
                    <a class="btn btn-primary" href="{{route('admin.notice.old')}}">Old Notices</a>
                </div>


            </div>
        </div>
<!--         <div class="row">
             <div class="col-lg-12">
                <ul class="nav nav-tabs">
                        <li  ng-class="{active: type === '1'}"><a href="" ng-click="tab('1')">AGM Notices</a></li>
                        <li  ng-class="{active: type === '2'}"><a  href="" ng-click="tab('2')">General Notices</a></li>
                        <li  ng-class="{active: type === '3'}"><a href="" ng-click="tab('3')">Special AGM Notices</a></li>
                </ul>
             </div>
         </div>-->
<!--        <div class="row">
            <div ng-if="pagination.total == 0" style="margin-left: 30px;">
                <table>
				<tr ng-if="pagination.total == 0" style="margin-left: 30px;">
				<td colspan="3" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
			</tr>
                </table>
			</div>
            <div class="col-lg-4" ng-repeat="notice in notices" ng-show="( notice.status == 1 || {{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title" style="word-wrap: break-word;">@{{notice.title}}</h3>
                    </div>
                    <div class="panel-body" >
                        <span class='pull-right label label-default' >@{{displayDate(notice.created_at)}}</span>
                        <div ng-bind-html="textFormat(notice.text,notice.id)" style="clear:both;word-wrap: break-word;"></div>
                    </div>
                    <div class="panel-footer" ng-show="({{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0">
                        <span class="label label-primary" ng-show="(notice.status == 1)  ? 1 : 0">Published</span>
                        <span class="label label-warning" ng-show="(notice.status == 0)  ? 1 : 0">Draft</span>
                        <a class='pull-right' ng-hide="(notice.status == 1)  ? 1 : 0" ng-show="({{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0" href="<?php echo route('admin.notice.edit','') ?>/@{{notice.id}}"  >Edit</a>
                    </div>
                </div>
            </div>
        </div>-->
      
        <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                       <th>
                                <a href="" ng-click="order('title')">Title</a>
                                <span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('description')">Description</a>
                                <span class="sortorder" ng-show="predicate === 'description'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('created_at')">Uploaded On</a>
                                <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('status')">Status</a>
                                <span class="sortorder" ng-show="predicate === 'status'" ng-class="{reverse:reverse}"></span>
                            </th>
							<th>
                                <a href="" ng-click="order('type')"> Notice Type</a>
                                <span class="sortorder" ng-show="predicate === 'type'" ng-class="{reverse:reverse}"></span>
                            </th>
                        <th><a href="#" >Action</a></th>
                    </tr>
                </thead>
                <tbody>
					<tr ng-if="pagination.total == 0 && noData == 0">
                        <td colspan="7" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                    </tr>
					<tr ng-if="pagination.total == 0 && noData == 1">
                        <td colspan="7" style="font-weight: bold;" >No Data Found.</td>
                    </tr>
                    <tr ng-if="pagination.total > 0" ng-repeat="notice in notices" ng-show="( notice.status == 1 || {{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0">
                         <td>@{{notice.id}}</td>
                        <td>@{{notice.title}}</td>
                        <td ng-bind-html="textFormat(notice.text,notice.id)" style="clear:both;word-wrap: break-word;"></td>
                        <td>@{{notice.created_at}}</td>
                        <!--<td ng-show="({{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0">-->
                        <td ng-show="(notice.status == 1)  ? 1 : 0">Published</td>
                        <td ng-show="(notice.status == 0)  ? 1 : 0">Draft</td>
						<td ng-if="notice.type == 1">AGM Notices</td>
						<td ng-if="notice.type == 2">General Notices</td>
						<td ng-if="notice.type == 3">Special AGM Notices</td>
                        <td>
                            <a class="glyphicon glyphicon-pencil" title="update" ng-hide="(notice.status == 1)  ? 1 : 0" ng-show="({{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0" href="<?php echo route('admin.notice.edit','') ?>/@{{notice.id}}"></a>
							<a class="glyphicon glyphicon-eye-open" title="view" href="<?php echo route('admin.notice.view','') ?>/@{{notice.id}}"></a>
							
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
        
        <!---->
        
        

        <div ng-if="pagination.total > 0" class="row">
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


        <!-- Modal -->
        <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Notice</h4>
                  </div>
                  <div class="modal-body">
                    <form id="notice-form" method="post" action="">
                        <div class="form-group">
                          <label class="form-label">Title</label>
                          <input type="text" class="form-control" name="title" maxlength="50"  placeholder="Title">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Description</label>
                          <textarea id="notice_desc" class="form-control" name="text" placeholder="Description"></textarea>
                          <!--<input type="text" class="form-control" name="text"  placeholder="Description">-->
                        </div>
                        <div class="form-group">
                          <label class="form-label">Expiry date</label>
                          <input id='expiry-date' type="text" class="form-control" name="expiry_date"  placeholder="Expiry date">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Notice Type</label>
                            <div class="form-group type-radio-group">
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="type" class="attendees"  value="1" >
                                        AGM
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="type" class="attendees"  value="2" >
                                        General
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="type" class="attendees"  value="3" >
                                        Special AGM
                                    </label>
                                </div>
								
							   <div id="member_roles" class="form-group" style="display: none;">
									<label class="form-label">Member Roles</label>
									<select name="role_id[]" class="form-control" multiple="multiple">
										  <option value="" disabled="" selected="">Select Roles </option>
										  <option ng-if="role.role_name != 'Admin'" ng-repeat="role in roles" value='@{{role.id}}'>@{{role.role_name}}</option>
									  </select>
                               </div>
								
                              <!-- 
							    <div class="radio-inline">
                                    <label>
                                        <input type="radio" class="attendees" name="type" value="4">Role based members
                                    </label>
                                </div>  
								--->
                            </div>
                           
      <div class="form-group">
                          <label class="form-label">Publish</label>
                          <div class="form-group">
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="status" value="1" >
                                        Yes
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="status" value="0" >
                                        No
                                    </label>
                                </div>
                                <div class="radio-inline publish-error">
                                </div>
                          </div>

                        </div>

                            </div>
							<button type="submit" class="btn btn-primary">Submit</button>
                        <button class="btn btn-primary" type="button" ng-click="closeForm()">Cancel</button>
                     </form>
                      <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
				</div>
                  
                        
                   
                  </div>
            </div>
        </div>
    </div>
  
    <script>
        $('document').ready(function(){
            $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            });
//            console.log(moment(new Date()));
            $('#expiry-date').datetimepicker({
//                useCurrent : true,
                format: 'DD-MM-YYYY',
                minDate:moment(new Date()).format('YYYY-MM-DD'),
                widgetPositioning: {
                    horizontal: 'left',
                    vertical:'bottom'
                 }
            });

             CKEDITOR.replace( 'notice_desc' );

            $("#notice-form").validate({
                ignore: [],
                rules: {
                    title: {
                        required: true,
                        minlength: 5
                      },
                    text: "required",
                    expiry_date: "required",
                    type : "required",
                    status : "required"
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "text"  ) {
                      error.insertAfter("#cke_notice_desc");
                    } else if (element.attr("name") == "type"  ) {
                        $( ".form-group.type-radio-group" ).append( error );
                    } else if (element.attr("name") == "status"  ) {
                        $( ".publish-error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
            });
        });
    </script>

    <script>
    $(document).ready(function(){
        $('.attendees').on('click',function(){
            if($(this).attr('checked'))
            {
                var value = $(this).attr('value');
                if(value == '3')
                {
                    $('#member_roles').show();
                }else{
                    $('#member_roles').hide();
                }
            }
        });
    });

    </script>


@stop
