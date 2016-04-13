@section('title', 'Family Details')
@section('panel_title', 'Flat')
@section('panel_subtitle', 'Details')
@section('head')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{asset('js/jquery.twbsPagination.js')}}"></script>
    <script src="{{asset('bower_components/typeahead.js/dist/typeahead.bundle.js')}}"></script>

    <style>
        .member_list .add:first-child .remove_member{display: none;}
        .ui-autocomplete {
            z-index:2147483647;
}
    </style>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("FlatListCtrl", function($scope,paginationServices,$http,$filter) {
            $scope.flat;
            $scope.associate_member;
			$scope.totalAssociateMember =0;
			$scope.totalMember =0;
            $scope.slots;
            $scope.flat_parking;
            $scope.search_slots;
            $('#member').show();
            $('#parking1').hide();
//            $scope.itemsPerPage = 10;
//            $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
             $scope.pagination = paginationServices.getNew(5);
            $scope.itemsPerPage = 5;
            $scope.sort = 'slot_name';
            $scope.sort_order = 'asc';
            $scope.search='';
			$scope.pagination.deleteStatus =0;

            $scope.getSlots = function() {
               var request_url = generateUrl('v1/all_slots');
                $http.get(request_url)
                .success(function(result, status, headers, config) {
//                    console.log(result);
                    $scope.slots = result.data;
//                    console.log($scope.slots);
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getSlots();

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
//                                $scope.pagination.total=0;
//                                $scope.pagination.offset = 0;
//                                $scope.pagination.currentPage = 1;
//
//                                $scope.pagination.setPage(1);
                                grit('','Slot empty successfully');
//								console.log($scope.pagination);return false;
								if ($scope.pagination.currentPage != 1 && ($scope.pagination.total % $scope.pagination.itemsPerPage) == 1){
									$scope.pagination.currentPage -= 1;

								}
								else{
									$scope.pagination.noData = 0;
								}
									$scope.getFlatParking({{$id}},$scope.pagination.currentPage,null,1);

                                //window.location.reload();
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

            $scope.getFlatParking = function(id,page,search,deleteStatus) {
            if(!page){
                page = 1;
            }
			if (typeof deleteStatus == undefined){
				deleteStatus = 0;
			}
            var options = {page:page,per_page:$scope.itemsPerPage};
            $scope.pagination.currentPage = page;
            if(search){
                options['search']=search;
            }
//                 var options = {page:page,per_page:$scope.itemsPerPage,orderby:'ASC'}
            if(search){
                options['search']=search;
            }
//			console.log(options);
            var request_url = generateUrl('v1/flat_parking/list/'+id,options);
            $http.get(request_url)
            .success(function(response) {
                $scope.flat_parking = response.results.data;
                $scope.pagination.total = response.results.total;
                $scope.pagination.pageCount = response.results.last_page;
                $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);

				if ($scope.pagination.total > 0){
					$scope.pagination.noData = 1;
				}

				if ($scope.pagination.total == 0   ){
					$("#dataCheck").text("No Data Found.");
				}
				if (deleteStatus == 1 && $scope.pagination.total == 0){
					$scope.pagination.deleteStatus = 1;
				}
				else{
					$scope.pagination.deleteStatus = 0;
				}

            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };


        $scope.getFlatParking({{$id}});

        $scope.$on('pagination:updated', function(event,data) {
            $scope.getFlatParking({{$id}},$scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
        });
        $scope.$watch('search', function(newValue, oldValue) {
//            console.log("fgfh");
            if(newValue){
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'slot_name';
                $scope.sort_order = 'asc';

                $scope.pagination.setPage(1);
            }else{
                $scope.pagination.setPage(1);
            }

        });

        $scope.openFlatParking = function()
        {
			$scope.getSlots();
          $('#FlatParkingModal').modal();
        };

        $scope.closeParkingSlotForm = function(){
            $("#parking-form")[0].reset();
            $("#parking-form label.error").remove();
            $('#FlatParkingModal').modal('hide');
        };

        $scope.parking_id;
        $scope.slot_name;
        $scope.vehicle_type;
        $scope.FlatParkingUpdateForm = function(parking_id,slot_name,vehicle_type)
        {
            $scope.parking_id = parking_id;
            $scope.slot_name = slot_name;
            $scope.vehicle_type = vehicle_type;
            console.log($scope.slot_name);
            $('#FlatUpdateParkingModal').modal();
        };

            $scope.getFlat = function(id) {
                var request_url = generateUrl('flat/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.flat = result.response.data;
//                    console.log($scope.flat.id);
                }).error(function(data, status, headers, config) {

                });
            };

            $scope.getFlat({{$id}});

            $scope.getAssociateMember = function() {
               var request_url = generateUrl('associatemember/'+{{$id}});
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.associate_member = result.response.data;
					$scope.totalAssociateMember = result.response.total;
					if ($scope.totalAssociateMember == 0 ){ $("#dataCheckAssociate").text("No Data Found."); }
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getAssociateMember();

                /// datetime format function

            $scope.formatDateTime = function(date,time){
                var dateArray = date.split("-");
                if(time){
                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
                    return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
                }else{
                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
                    return $filter('date')(dateUTC, 'yyyy-MM-dd');
                }
            };

            $('#flat-form').submit(function(e){
                e.preventDefault();
                if ($("#flat-form").valid()){
                    var records = $.param($( this ).serializeArray());
                    console.log(records);
                    var request_url = generateUrl('flat/edit/'+$scope.flat.id);
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
//                       alert("Flat updated successfully");
////				window.location.reload();
                           grit('','Flat updated successfully!');
                    },
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });

//        });
//

        app.directive('onFinishRender', function ($timeout) {
            return {
                restrict: 'A',
                link: function (scope, element, attr) {
                    if (scope.$last === true) {
//                        $timeout(function () {
                            scope.$emit('ngRepeatFinished');
//                        });
                    }
                }
            }
        });

//        app.controller("MemberCtrl", function($scope,$http,$filter) {
            var flat_id = {{$id}};
            $scope.members;
            $scope.getMembers = function() {
//                 console.log(flat_id);
               var request_url = generateUrl('member/list/'+flat_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.members = result.response.data;
					$scope.totalMember = $scope.members.length;
					if ($scope.totalMember == 0 ){ $("#dataCheckMember").text("No Data Found."); }

                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getMembers();

            $scope.$on('ngRepeatFinished', function(ngRepeatFinishedEvent) {
               $('.dob').datetimepicker({
                    useCurrent : true,
                    format: 'DD-MM-YYYY',
                    widgetPositioning: {
                            horizontal: 'left',
                            vertical:'bottom'
                         }
                });
            });

            $scope.formatDateTime = function(date,time){
                var dateArray = date.split("-");
                if(time){
                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);

                    return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
                }else{
                   var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);

                   return $filter('date')(dateUTC, 'yyyy-MM-dd');
                }

          };

            $scope.remove=function(id){
                $('.member_list tr.edit#'+id).remove(); // To remove row
                if($('.member_list tr').length == 0){  // If removed last element disable submit button
                    $('#member_form').find('input[type=submit]').attr('disabled',true);
                }
                var request_url = generateUrl('member/delete');
                $http({
                    url: request_url,
                    method: "POST",
                    data:$.param({member_id:id}),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .then(function(response) {
                    $scope.getMembers();
                },
                function(response) { // optional
//                       alert("fail");
                });
            }

            $('#member_form').submit(function(e){
                 e.preventDefault();
                var member = [];
                $('#member_form label.error').remove();
//                if ($(this).valid()){
                 $('#member_form').find('input[type=submit]').attr('disabled',true);
                var validated = true;
                var validations = true;
                var flat_id = {{$id}};
                    $( '.member_list input[name="member[name][]"]' ).each(function( index ) {
                        if($( this ).val() == ''){
                            $(this).after(' <label  class="error" > This field is required.</label>');
//                            console.log('df');
                            validated = false;
                        }else{
                            member.push({name:$( this ).val()});
                        }

                    });

                    if(validated){

                         $( '.member_list input[name="member[voter_id][]"]' ).each(function( index ) {
                             $(this).val($(this).val().trim());
                            if($( this ).val() != ''){
                                    if($( this ).val().length < 6){
                                        $('.error').remove();
                                   $(this).after(' <label class="error" > Please enter atleast 6 characters.</label>');
                                   validations = false;
                                    }
                                   if ($( this ).val()!= $( this ).val().match(/^[a-zA-Z0-9]+$/)){
                                        $('.error').remove();
                                   $(this).after(' <label class="error" > Please enter valid id</label>');
//                               }
//                                    console.log('eeee');
                                    validations = false;
                                }else{
                                    member[index]['voter_id']=$( this ).val();
                               }
                           }
                            });

                        $( '.member_list input[name="member[unique_id][]"]' ).each(function( index ) {
                            $(this).val($(this).val().trim());
                           if($( this ).val() != ''){
                                    if($( this ).val().length < 6){
                                        $('.error').remove();
                                   $(this).after(' <label class="error" > Please enter atleast 6 characters.</label>');
                                   validations = false;
                                    }
                                   if ($( this ).val()!= $( this ).val().match(/^[a-zA-Z0-9]+$/)){
                                        $('.error').remove();
                                   $(this).after(' <label class="error" > Please enter valid id.</label>');
//                               }
//                                    console.log('eeee');
                                    validations = false;
                                }else{
                                    member[index]['unique_id']=$( this ).val();
                               }
                           }

                    });
                        if(validations){

                        $( '.member_list input[name="member[dob][]"]' ).each(function( index ) {
                            if($( this ).val() != ''){
                               member[index]['dob'] = $scope.formatDateTime($( this ).val());
                            }

                        });
                        $( '.member_list input[name="member[id][]"]' ).each(function( index ) {
                          member[index]['id']=$( this ).val();
                        });



                        var records = $.param({members:member,flat_id:{{$id}}});

                        var request_url = generateUrl('member/create');
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                        .then(function(response) {
                            $('#member_form').find('input[type=submit]').attr('disabled',false);
                            grit('','Members updated successfully!');
                            $('.member_list tr.add').remove();
                            $scope.getMembers();
                        },
                        function(response) { // optional
//                               alert("fail");
                        });
                        }else{
                             $('#member_form').find('input[type=submit]').attr('disabled',false);
                        }
                    }else{
                        $('#member_form').find('input[type=submit]').attr('disabled',false);
                    }



//                }
            });

            $('#parking-form').submit(function(e){
                e.preventDefault();
                if ($('#parking-form').valid()){
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('please wait');
                    var records = $.param($( this ).serializeArray());
                    console.log(records);
                    var request_url = generateUrl('v1/create/parking');
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("#parking-form").find('button[type=submit]').attr('disabled',false);
                        $("#parking-form").find('button[type=submit]').text('Submit');
                      var result = response.data; // to get api result
//                       console.log(response);
                        if(result.success){
                            grit('',result.msg);
							$('#FlatParkingModal').modal('hide');
							$scope.getFlatParking({{$id}});
                           // window.location.reload();
                        }else{
                            console.log("some error occured!");
                        }
                    },
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });

//            $scope.getSearchslots = function() {
//               var request_url = generateUrl('v1/search/slots');
//                $http.get(request_url)
//                .success(function(result, status, headers, config) {
////                    console.log(result);
//                    $scope.search_slots = result.data;
//                    console.log($scope.search_slots);
//                }).error(function(data, status, headers, config) {
//                    console.log(data);
//                });
//            };
//
//            $scope.getSearchslots();

//            $scope.complete=function(){

//                    var fakedata = ['test1','test2','test3','test4','ietsanders'];
//                $( "#slot_id" ).autocomplete({
//                    source: fakedata,
//                    options.autocompleteOptions.source = false,
//
//                });
//

//            }
//
//            $scope.complete();

//    $scope.getSearchslots = function() {
//               var request_url = generateUrl('v1/search/slots' , {'search': '%QUERY'});
//                $http.get(request_url)
//                .success(function(result, status, headers, config) {
////                    console.log(result);
//                    $scope.search_slots = result.data;
//                    console.log($scope.search_slots);
//                }).error(function(data, status, headers, config) {
//                    console.log(data);
//                });
//    };
//
//    $scope.getSearchslots();
    var bestPictures = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('slot_name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit : 100,
//        prefetch: generateUrl('v1/search/slots'),
        remote: {
          url: generateUrl('v1/search/slots', {'search': '%QUERY'}),
          wildcard: '%QUERY'
        }
    });
    bestPictures.initialize();

    $('.typeahead').typeahead(null, {
        name: 'bestPictures',
        displayKey: 'slot_name',
        source: bestPictures
    });

    $scope.parking = function() {
        $('#member').hide();
        $('#parking1').show();
    }

        $scope.member = function() {
        $('#member').show();
        $('#parking1').hide();
    }
        });
    </script>
    <!-- Nav tabs -->

    <div class="col-lg-12" ng-controller="FlatListCtrl" >

        <h3 id="member">Flat Member List - @{{flat.flat_no}}</h3>
        <h3 class="ng-binding"id="parking1">Flat Parking Details - @{{flat.flat_no}}</h3>


<!--            <ul class="nav nav-tabs">
                <li id="default" class="active"><a href="#" id="DefaultTab">Default</a></li>
                <li id="advanced">  <a href="#"  class="" id="AdvancedTab">Advanced</a></li>
            </ul>-->


<!--            <div role="tabpanel" class="tab-pane active" id="default">-->
        <ul class="nav nav-tabs" role="tablist">
          <!--<li role="presentation" class="active"><a href="#default" aria-controls="profile" role="tab" data-toggle="tab">Default</a></li>-->
          <li  role="presentation" class="active"><a href="#flats"   ng-click="member()" aria-controls="messages" role="tab" data-toggle="tab">Advanced</a></li>
          <li  role="presentation"><a href="#parking"  ng-click="parking()" aria-controls="parking" role="tab" data-toggle="tab">Parking</a></li>
        </ul>

        <!--<div id="FLatEdit">-->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane" id="default">
            <div class="row">
                <div class="col-lg-12">
                <form id="flat-form" method="post" action="">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Floor</label>
                        <input type="text" class="form-control" name = "floor" value ="@{{flat.floor}}" maxlength="2"  placeholder="floor">
                    </div>
                    <div class="form-group">
                      <label>Occupancy</label>
                        <div class="form-group">
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="occupancy" ng-checked="(flat.occupancy == 'O') ? 1 : 0 "   value="O" >
                                   Owner
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="occupancy" ng-checked="(flat.occupancy == 'T') ? 1 : 0 "   value="T" >
                                    Tenant
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="occupancy" ng-checked="(flat.occupancy == 'V') ? 1 : 0 "   value="V" >
                                   Vacant
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="occupancy" ng-checked="(flat.occupancy == 'B') ? 1 : 0 "   value="B" >
                                   Builder
                                </label>
                            </div>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="exampleInputEmail1">Flat SqFt </label>
                        <input type="text" class="form-control" name = "square_feet_1" maxlength="5" value ="@{{flat.square_feet_1}}"  placeholder="Flat SqFt ">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Bill-To Name</label>
                        <input type="text" class="form-control" name = "bill_to_name" maxlength="50" value ="@{{flat.bill_to_name}}"  placeholder="Bill-To Name">
                    </div>
                     <div class="form-group">
                        <label for="exampleInputEmail1">Parking Slot Type </label>
                        <input type="text" class="form-control" name = "parking_slot_1" maxlength="4" value ="@{{flat.parking_slot_1}}"  placeholder="Parking Slot Type ">
                    </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                         <button class="btn btn-primary" type="button" onclick="javascript:window.location.href='<?php echo route('admin.users') ?>'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    <!--</div>-->

    <!--</div>-->
    <!--<div class="col-lg-12" ng-controller="MemberCtrl" >-->
        <!--<div id="FlatMember" style="display:none;">-->
        <div role="tabpanel" class="tab-pane active" id="flats">
            <!--<form id="member_form">-->
                <!-- <div class="row">
                    <div class="col-lg-12">
                        <h4 class="pull-left">Associate Member</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Date of Birth</th>
                                    <th>Relation</th>
                                    <th>Voter Id</th>
                                    <th>Unique Id</th>
                                    <th>Mobile Number</th>
                                </tr>
                            </thead>
                            <tbody >
								<tr ng-if="totalAssociateMember == 0">
									<td colspan="8" style="font-weight: bold;" id="dataCheckAssociate">Fetching Data...</td>
								</tr>
                                <tr ng-if="totalAssociateMember > 0">
                                    <td>@{{associate_member.first_name + ' ' + associate_member.last_name}}</td>
                                    <td>@{{associate_member.email}}</td>
                                    <td>@{{associate_member.dob != "0000-00-00" ? associate_member.dob : ""| date:'dd-MM-yyyy'}}</td>
                                    <td>@{{associate_member.relation}}</td>
                                    <td>@{{associate_member.voter_id}} </td>
                                    <td>@{{associate_member.unique_id}}</td>
                                    <td>@{{associate_member.contact_no}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div> -->
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Date of Birth</th>
                                    <th>Relation</th>
                                    <th>Voter Id</th>
                                    <th>Unique Id</th>
                                    <th>Mobile Number</th>
                                    <th>Associated Member</th>
                                </tr>
                            </thead>
                            <tbody class="member_list">
    							<tr ng-if="totalMember == 0">
    								<td colspan="8" style="font-weight: bold;" id="dataCheckMember">Fetching Data...</td>
    							</tr>
                                <tr ng-if="totalMember > 0" ng-repeat="member in members">
                                    <td>@{{member.first_name + ' ' + (member.last_name == null? '': member.last_name) }}</td>
                                    <td>@{{member.email}}</td>
                                    <td>@{{member.dob != "0000-00-00" ? member.dob : ""| date:'dd-MM-yyyy'}}</td>
                                    <td>@{{member.relation}}</td>
                                    <td>@{{member.voter_id}} </td>
                                    <td>@{{member.unique_id}}</td>
                                    <td>@{{member.contact_no}}</td>
                                    <td>@{{member.associate_member != 0 ? 'Yes' : 'No' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <!--</form>-->
<!--            <div class="row">
                <div class="col-lg-3">
                 <button id="add_btn" class="btn btn-primary">Add</button>
                </div>
            </div>-->
            <div class="hide">
                <div id="new_member">
                    <table>
                        <tr class="add">
                            <th><input type="text" name="member[name][]" value="" /></th>
                            <th><input class="dob" type="text" name="member[dob][]" value="" /></th>
                            <th><input type="text" name="member[voter_id][]" value="" /></th>
                            <th><input type="text" name="member[unique_id][]" value="" /></th>
                            <th><input type="hidden" name="member[id][]" value="0" /><a title="delete" class="remove_member" href="javascript:void(0);"><i class="fa fa-remove"></i></a></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!--parking module-->

        <div role="tabpanel" class="tab-pane" id="parking">
            <div class="row">
                <div class="col-lg-12" style="height: 50px;">
                    <input ng-model='search' class="form-control" placeholder="Search By Slot Name" style="width: 300px;float: left">
                        <button type="button" class="btn btn-primary pull-right"
                            ng-click="openFlatParking()">Add Parking Slot</button>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Slot Name</th>
<!--                                    <span class="sortorder" ng-show="predicate === 'slot_name'"
                                    ng-class="{reverse:reverse}"></span></th>-->
                                <th>Vehicle Type</th>
<!--                                    <span
                                    class="sortorder" ng-show="predicate === 'vehicle_type'"
                                    ng-class="{reverse:reverse}"></span></th>-->
<!--                                        <th>Created On</th><span
                                    class="sortorder" ng-show="predicate === 'created_at'"
                                    ng-class="{reverse:reverse}"></span></th>-->
                                                                <th><a href="#" >Operation</a></th>
                            </tr>
                        </thead>
                        <tbody>
							<tr ng-if="pagination.total == 0 && pagination.deleteStatus != 1" >
								<td colspan="3" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
							</tr>
							<tr ng-if="pagination.deleteStatus == 1" >
								<td colspan="3" style="font-weight: bold;" id="noDataCheck">No Data Found.</td>
							</tr>
                            <tr ng-if="pagination.total > 0" ng-repeat="parking_list in flat_parking" ng-hide="parking_list.parking == null">
                                <td>@{{parking_list.parking.slot_name}}</td>
                                <td ng-show="parking_list.parking.vehicle_type == 1"> 2 wheeler</td>
                                <td ng-show="parking_list.parking.vehicle_type == 2"> 4 wheeler</td>
                                <!--<td>@{{parking_list.created_at}}</td>-->
                                <td>
                                <a class="glyphicon glyphicon-remove" title="remove parking slot" href="#" ng-click="RemoveSlot(parking_list.parking.id)"></a>
<!--                                <a class="btn btn-default" title="send invitees" href="#" ng-click="SendInvitees(meeting.id)">Send Invitees</a>-->
                            </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- pagination -->
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
                    <!---->
                </div>
            </div>

            <!--Parking Slot Modal-->
            <div class="modal fade" id="FlatParkingModal" tabindex="-1" role="dialog" aria-labelledby="userFormModalLabel">
                <div class="modal-dialog" role="user">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                ng-click="closeParkingSlotForm()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" >Add Parking Slot</h4>
                        </div>
                        <div class="modal-body">
                            <form id="parking-form" method="post" action="">
                                <div class="form-group">
                                    <label class="form-label">Slots</label>
                                    <!--<input class="typeahead" type="text" name="parking_slot_id" id="slot_id">-->
                                    <select name="parking_slot_id" class="form-control">
                                            <option value="" disabled="" selected="">Select Slots</option>
                                            <option ng-repeat="all_slots in slots" value='@{{all_slots.id}}'>@{{all_slots.slot_name}}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Vehicle Type</label>
                                      <div class="form-group">
                                          <div class="radio-inline">
                                              <label>
                                                  <input type="radio" name="vehicle_type" value="1" >
                                                 2 wheeler
                                              </label>
                                          </div>
                                          <div class="radio-inline">
                                              <label>
                                                  <input type="radio" name="vehicle_type" value="2" >
                                                  4 wheeler
                                              </label>
                                          </div>
                                          <div class="visiblity_error"></div>
                                      </div>
                                </div>
                                <input type="hidden" name="flat_id" value="{{$id}}">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button class="btn btn-primary" type="button" ng-click="closeParkingSlotForm()">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--End-->
        </div>
         <!--End of parking module-->
    </div>
    </div>
<!--            <script>
                $('#DefaultTab').click(function(){
                   $('#FLatEdit').show();
                   $('#FlatMember').hide();
                   $('#default').addClass('active');
                   $('#advanced').removeClass('active');
                });

                $('#AdvancedTab').click(function(){
                    $('#FlatMember').show();
                    $('#FLatEdit').hide();
                    $('#advanced').addClass('active');
                    $('#default').removeClass('active');

                });
                </script>-->
                 <script>
        $('document').ready(function(){
//            $("#member_form").validate({
//                rules: {
//                    'member[name][]': "required",
//                    'member[dob][]': "required"
//                }
//            });

            $('.dob').datetimepicker({
                    useCurrent : true,
                    format: 'DD-MM-YYYY',
                    maxDate:moment(new Date()),
                    ignoreReadonly : true,
                    widgetPositioning: {
                            horizontal: 'left',
                            vertical:'bottom'
                         }
            });

            $('#add_btn').on('click',function(){
                $('.member_list').append($('#new_member table tbody').html());
                $('#member_form').find('input[type=submit]').attr('disabled',false);
                $('.member_list .add:first-child .remove_member').show();
                $('.member_list .add .dob:last').datetimepicker({
                    useCurrent : true,
                    format: 'DD-MM-YYYY',
                    maxDate:moment(new Date()),
                    ignoreReadonly : true,
                    widgetPositioning: {
                        horizontal: 'left',
                        vertical:'bottom'
                     }
                });
            });

            $('.member_list').on('click','.remove_member',function(e){
                $(this).parents('.add').remove();

                if(!$('.member_list tr').length){
                    $('#member_form').find('input[type=submit]').attr('disabled',true);

                }

                if($('.member_list tr.add').length == 1){
                   $('.member_list .add:first-child .remove_member').hide();
                }

                if($('.member_list tr.add').length > 1){
                   $('.member_list .add:first-child .remove_member').show();
                }
                return false;
            });

//            var $eventSearch = $("#slot_id").select2({
//                var request_url = generateUrl('v1/search/slots');
//                $http.get(request_url)
//                .success(function(result, status, headers, config) {
//                    return result;
//                }).error(function(data, status, headers, config) {
//
//                });
//            });



//                ajax: {
//                  url: API_URL+'society/search',
//                  dataType: 'json',
//                  delay: 250,
//                  data: function (params) {
//                    return {
//                      search: params.term, // search term
//                      page: params.page
//                    };
//                  },
//                  processResults: function (result, page) {
//                    return {
//                      results: result.data
//                    };
//                  },
//                  cache: true
//                },
//                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
//                minimumInputLength: 3,
//                templateResult: formatRepo,
//                templateSelection: formatRepoSelection
//              });
        });
    </script>
    <script>
        $('document').ready(function(){
            $('#parking-form').validate({
		rules :
		{

                    parking_slot_id : "required",
                    vehicle_type:"required",
		},
               errorPlacement: function(error, element) {
                    if (element.attr("name") == "vehicle_type"  ) {
                        $( ".visiblity_error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
        });
    });
    </script>
@stop
