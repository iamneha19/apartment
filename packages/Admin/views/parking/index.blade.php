@section('title', 'Parking')
@section('panel_title', 'Parking')@section('panel_subtitle','List')
@section('head')
    <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
@stop
@section('content')
    <script>
	app.controller("ParkingListCtrl", function(URL,paginationServices,$scope,$http,$filter) {
            $scope.parking_slots;
//            $scope.getVehicles;
//            $scope.getCategory;
            $scope.RemoveSlot;
            $scope.pagination = paginationServices.getNew(5);
			$scope.pagination.noData = 1;
            $scope.itemsPerPage = 5;
            $scope.sort = 'slot_name';
            $scope.sort_order = 'asc';
            $scope.search='';

            $scope.getParkingSlots = function(offset,limit,sort,sort_order,search) {
                var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order}
                if(search){
                   options['search']=search;
                }
                var request_url = generateUrl('v1/parking_slots/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    console.log(result.data);
                    $scope.parking_slots = result.data;
                    $scope.pagination.total = result.total;
                   $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
				   if ($scope.pagination.total == 0 ){
                        $("#dataCheck").text("No Data Found.");
                    }
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };

//            $scope.getVehicles = function() {
//                var request_url = generateUrl('v1/vehicle_type');
//                  $http.get(request_url)
//                .success(function(result, status, headers, config) {
////                    console.log(result.data);
//                        $scope.vehicle = result.data;
//                }).error(function(data, status, headers, config) {
//                        console.log(data);
//                });
//            };
//
//             $scope.getCategory = function() {
//                var request_url = generateUrl('v1/parking_category');
//                  $http.get(request_url)
//                .success(function(result, status, headers, config) {
////                    console.log(result.data);
//                        $scope.category = result.data;
//                }).error(function(data, status, headers, config) {
//                        console.log(data);
//                });
//            };

            $scope.RemoveSlot = function(id)
            {
                var confirm_msg = confirm("Are you sure to empty this slot!");
                if(confirm_msg == true)
                {
                    var request_url = generateUrl('v1/delete/alloted_slots');
                     var records = $.param({id:id});
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                            var result = response.data; // to get api result

                            if(result.success){
                                /*
								$scope.pagination.total=0;
                                $scope.pagination.offset = 0;
                                $scope.pagination.currentPage = 1;
								$scope.pagination.setPage(1);
								*/
                                
                                grit('','Slot empty successfully');
                                location.reload();
								if ($scope.pagination.currentPage != 1 && ($scope.pagination.total % $scope.pagination.itemsPerPage) == 1){
									$scope.pagination.currentPage -= 1;
//                                    location.reload();
									
								}
								else{
									$scope.pagination.noData = 0;
								}
								
                            }else{
                                grit('','Error in deleting file');
                            }

                        },
                        function(response) { // optional
//                            alert("fail");
                        });
                }else{
                        return false;
                }
            }
//
	//    $scope.getUsers($scope.offset,$scope.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);

            $scope.$on('pagination:updated', function(event,data) {
                $scope.getParkingSlots($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
            });
//	   $scope.getMeetings($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
            $scope.$watch('search', function(newValue, oldValue) {
                if(newValue){
                    $scope.pagination.total=0;
                    $scope.pagination.offset = 0;
                    $scope.pagination.currentPage = 1;
                    $scope.sort = 'id';
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
        /*date formatfunction */
        $scope.formatDateTime = function(date,time){
            var dateArray = date.split("-");
            if(time){
//                var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
                  var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time).toDate();
                return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
            }else{
//                var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
                var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]).toDate();
                return $filter('date')(dateUTC, 'yyyy-MM-dd');
            }
        };


        $scope.closeSlotForm = function(){
            $("#slot-form")[0].reset();
            $("#slot-form label.error").remove();
            $('#SlotModal').modal('hide');
            $('#sub_category').hide();
        };

        $('#slot-form').submit(function(e){
            e.preventDefault();
//            $('#meeting_desc').val(CKEDITOR.instances.meeting_desc.getData());
            if ($("#slot-form").valid()){
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Creating meeting please wait..');
                var  enteredDate =  $('#date').val();
                var new_date = enteredDate.split(' ');
                var formatedDate = $scope.formatDateTime(new_date[0]);
//                var dateUTC =  moment(formatedDate+' '+new_date[1]+' '+new_date[2]).toDate();
                var dateUTC =  moment(new_date[1]+' '+new_date[2], ["h:mm A"]).format("HH:mm");
                var new_date_time = formatedDate+' '+dateUTC;
//                var convertedDate = $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
                $('#date').val(new_date_time);
                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('meeting/create');
                $http({
                    url: request_url,
                    method: "POST",
                    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                    .then(function(response) {
                        $("#meeting-form").find('button[type=submit]').attr('disabled',false);
                        $("#meeting-form").find('button[type=submit]').text('Submit');
                        var result = response.data.response; // to get api result
                        if(result.success){
                            $scope.pagination.total=0;
                            $scope.pagination.offset = 0;
                            $scope.pagination.currentPage = 1;

                            $scope.pagination.setPage(1);

                            $scope.closeMeetingForm();
                            grit('',result.msg);
                        }else{
                            // To handle server side validation errors
                           if(result.input_errors){
                               var errors = result.input_errors;
                              $('#meeting-form input[name="date"]').val(''); // To reset meeting date
                               for (var key in errors) {
                                    var error = errors[key];
                                    for (var index in error) {

                                            $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('input[name="'+key+'"]');
                                    }
                                 }
//

                           }else{
                               console.log('not input errors check msg');
                           }
                        }

                    },
                    function(response) { // optional
//                        alert("fail");
                    });
            }
        });
    });
</script>

    <div ng-controller="ParkingListCtrl" class="col-lg-12"  id="meeting">
        <div class="row" style="height: 50px;">
            <div class="col-lg-12">
		<input ng-model="search" class="form-control ng-pristine ng-untouched ng-valid"  placeholder="Search By Slot Name" style="width: 200px;display: inline">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
		<table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>
                                <a href="" ng-click="order('slot_name')">Slot Name</a>
                                <span class="sortorder" ng-show="predicate === 'slot_name'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                Category
                                <!--<span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>-->
                            </th>
                            <th>
                                Flat no
                                <!--<span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>-->
                            </th>
                            <th>
                                Status
                                <!--<span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>-->
                            </th>
<!--                             <th>
                                <a href="" ng-click="order('title')">Created At</a>
                                <span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>
                            </th>-->
                            <th>
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
						<tr ng-if="pagination.total == 0">
							<td colspan="6" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
						</tr>
                        <tr ng-if="pagination.total > 0" ng-repeat="slot in parking_slots | filter:search">
                            <td>@{{slot.id}}</td>
                            <td>@{{slot.slot_name}}</td>
                            <td>@{{slot.category_name}}</td>
                            <td>@{{slot.flat}}</td>
                            <td>@{{slot.status=="1" ? "Vacant" : "Occupied"}}</td>
                            <!--<td>@{{meeting.created }}</td>-->
                            <td ng-show="(@{{slot.status}} == '0')  ? 1 : 0">
                                <a class="glyphicon glyphicon-remove" title="remove parking slot" href="#" ng-click="RemoveSlot(slot.id)"></a>
<!--                                <a class="btn btn-default" title="send invitees" href="#" ng-click="SendInvitees(meeting.id)">Send Invitees</a>-->
                            </td>
                            <td ng-show="(@{{slot.status}} == '1')  ? 1 : 0">
                                No Action
                            </td>
                        </tr>
                    </tbody>
		</table>
            </div>
        </div>
        <!-- pagination -->
        <div class="row">
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
        <div class="modal fade" id="SlotModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                            ng-click="closeSlotForm()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Create Slot</h4>
                    </div>
                    <div class="modal-body">
                        <form id="slot-form" method="post" name="slot" action="">
                            <div class="form-group">
                                <label class="form-label">Slot Name</label>
                                <input type="text" class="form-control" name = "slot_name" maxlength="100"  placeholder="Slot Name">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Vehicle Type</label>
                                <select name="vehicle_type" class="form-control">
                                    <option value="" disabled="" selected="">Select Vehicle Type</option>
                                    <option ng-repeat="vehicles in vehicle" value='@{{vehicles.id}}'>@{{vehicles.type_name}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Category</label>
                                <select id="category" name="category_id" class="form-control" >
                                      <option value="" disabled="" selected="">Select Category </option>
                                      <option ng-repeat="categories in category" value='@{{categories.id}}'>@{{categories.category_name}}</option>
                                  </select>
                            </div>
                            <div id="sub_category" class="form-group type-radio-group" style="display: none;">
							<label class="form-label">sub category</label>
							<div class="form-group ">
								<div class="radio-inline">
									<label> <input type="radio" name="category_id" value="single"> Single
									</label>
								</div>
								<div class="radio-inline">
									<label> <input type="radio" name="category_id" value="mutiple"> Multiple
									</label>
								</div>
							</div>
						</div>
                            <button type="submit" id="btn" class="btn btn-primary">Submit</button>
                            <button class="btn btn-primary" type="button" ng-click="closeSlotForm()" >Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<script>
	$("#slot-form").validate({
		ignore: [],
		rules: {
		  // simple rule, converted to {required:true}
		  slot_name:"required",
		  vehicle_type:"required",
		  category_id:"required",
		  // attendees:"required",
		  // description:"required",
		}
	});
	</script>
    <script>
    $(document).ready(function(){
        $('#category').on('change',function(){
            var state_id = jQuery("#category").val();
//            alert(state_id);
            if(state_id == 4)
            {
                    $('#sub_category').show();
                }else{
                    $('#sub_category').hide();
                }
        });
    });

    </script>
	@stop
