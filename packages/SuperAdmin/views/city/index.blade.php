@section('title', 'City Dashboard')
@section('panel_title', 'City')
@section('panel_subtitle', 'List')
@section('content')
    <script>
    app.controller("CityListCtrl", function(URL,paginationServices,$scope,$http) {
        $scope.city;
        $scope.states;
        $scope.city_edit;
        $scope.pagination = paginationServices.getNew(5);
        $scope.itemsPerPage = 5;
        $scope.sort = 'state';
        $scope.sort_order = 'asc';
        $scope.search='';
        $scope.getCityList = function(page,search) {
            var options = {page:page,orderby:'ASC'}
//            var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order}
            if(search){
                options['search']=search;
            }
            var request_url = generateUrl('v1/cities',options);
            console.log(request_url);
            $http.get(request_url)
            .success(function(result, status, headers, config) {
                $scope.city = result.results.data;
                console.log($scope.city);
               $scope.pagination.total = result.results.total;
                $scope.pagination.pageCount = result.results.last_page;
				if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };
        
        $scope.getStates = function() {
             var options = {orderby:'ASC'}
                var request_url = generateUrl('v1/states',options) + "&per_page=unlimited";
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.states = result.results;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
        
        $scope.$on('pagination:updated', function(event,data) {
            $scope.getCityList($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
        });
        $scope.$watch('search', function(newValue, oldValue) {
            if(newValue){
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'state';
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

        $scope.openState = function(){
            $('#CityModal').modal();
             $scope.getStates();
        };
        
        $scope.closeForm = function(){
            $("#City-form")[0].reset();
            $("#City-form label.error").remove();
            $('#CityModal').modal('hide');
        };
        
        $scope.closeUpdateForm = function(){
            $("#City-update-form")[0].reset();
            $("#City-update-form label.error").remove();
            $('#CityEditFormModal').modal('hide');
        };
        $('#City-form').submit(function(e){
            
            e.preventDefault();
            if($('#City-form').valid())
            {
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Creating city please wait..');
                var data = $(this).serializeArray();
//                data.push({name:'society_id',value:society_id});
                var records = $.param(data);
//                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('v1/city');
                $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                .then(function(response) {
                    $("#City-form").find('button[type=submit]').attr('disabled',false);
                    $("#City-form").find('button[type=submit]').text('Submit');
                    var result = response.data;
                    console.log(result);
                    if(result.status=="success")
                    {
//                        $scope.pagination.total=0;
//                        $scope.pagination.offset = 0;
//                        $scope.pagination.currentPage = 1;
//                        $scope.pagination.setPage(1); 
                        $scope.closeForm();
                        grit('','City created successfully!');
                        location.reload();
                    }else{
                        if(result.status=='validation_failed')
                            {
                                $( "label#city-error" ).remove();
                                $( "#city" ).after( '<label id="city-error" class="error" for="city">City name is already exists!</label>' );
                            }else{
                                $( "label#city-error" ).remove();
                            }
                    }
                }, 
                function(response) { // optional
                    alert("fail");
                }); 
            }
        }); 
        
//        $scope.edit_state;
//        $scope.edit_id;
//        
//        $scope.openStateEditForm = function(state_id,state){
//            $scope.edit_state = state;
//            $scope.edit_id = state_id;
////            console.log($scope.edit_id);
//            $('#StateEditFormModal').modal();
//        };
        
//        $scope.delete = function(id){
//        var confirm_msg = confirm("Are you sure to delete this state!");
//        if(confirm_msg == true)
//        {
//            var request_url = generateUrl('document/delete');
//             var records = $.param({id:id});
//                $http({
//                    url: request_url,
//                    method: "POST",
//                    data:records,
//                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
//                }).then(function(response) {
//                    var result = response.data.response; // to get api result 
//
//                    if(result.success){
//                        $scope.pagination.total=0;
//                        $scope.pagination.offset = 0;
//                        $scope.pagination.currentPage = 1;
//
//                        $scope.pagination.setPage(1);
//                        grit('','Successfully deleted document');
//                    }else{
//                        grit('','Error in deleting document');
//                    }
//
//                }, 
//                function(response) { // optional
//                    alert("fail");
//                });
//        }else{
//                return false;
//        }
//    };        
                $scope.edit_id;
                $scope.getCity = function(id) {
                var request_url = generateUrl('v1/city/'+id);
                $scope.edit_id = id;
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.city_edit = result.results;
//                    console.log($scope.city_edit);
                   $scope.getStates();
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
                $('#CityEditFormModal').modal();
            };

        
        $('#City-update-form').submit(function(e){
        e.preventDefault();
        if ($(this).valid() && $scope.edit_id){
            $(this).find('button[type=submit]').attr('disabled',true);
            $(this).find('button[type=submit]').text('updating city please wait..');
            var records = $.param($( this ).serializeArray());
            console.log(records);
            var request_url = generateUrl('v1/city/update/'+$scope.edit_id);
            $http({
                url: request_url,
                method: "POST",
                
                data: records,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(function(response) {
                $("#City-update-form").find('button[type=submit]').attr('disabled',false);
                $("#City-update-form").find('button[type=submit]').text('Submit');
                var result = response.data;
                
//                console.log(result);
                    if(result.status=='success')
                    {
                        $scope.pagination.total=0;
                        $scope.pagination.offset = 0;
                        $scope.pagination.currentPage = 1;
                        $scope.pagination.setPage(1);   
                        $('#CityEditFormModal').modal('hide');
                        grit('','City updated successfully!');
                        location.reload();
                    }else{
                        if(result.status=='validation_failed')
                            {
                                $( "#city-error" ).remove();
                                $( "#city_edit" ).after( '<label id="city_edit-error" class="error" for="city_edit">City name is already exists.</label>' );
                            }else{
                                $( "label#city_edit-error" ).remove();
                            }
                    }
                
            }, 
            function(response) { // optional
                   alert("fail");
            });
        }
    });
    });
    </script>
    

    <div class="col-lg-12" ng-controller="CityListCtrl">
        <div class = "row">
            <div class = "col-lg-12" style="height: 50px;">
                <input ng-model='search' class="form-control" placeholder="Search" style="width: 300px;float: left;margin-left: 0px">
                <span class="pull-right" style="padding:7px;">
                    <button type="button" class="btn btn-primary" ng-click="openState()">Create City</button>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>
                                <!--<a href="" ng-click="order('state')">City</a>-->
                                City
                                <span class="sortorder" ng-show="predicate === 'name'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <!--<a href="" ng-click="order('state')">State</a>-->
                                State
                                <span class="sortorder" ng-show="predicate === 'name'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <!--<a href="" ng-click="order('created_at')">Created At</a>-->
                                Created At
                                <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
						<tr ng-if="pagination.total == 0">
							<td colspan="3" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
						</tr>
                        <tr ng-if="pagination.total > 0" ng-repeat="city in city | filter:search">
                            <td>@{{city.name}}</td>
                             <td>@{{city.state.name}}</td>
                            <td>@{{city.created_at}}</td>
                            <td>
                                <a class="glyphicon glyphicon-pencil" title="update" href="" ng-click="getCity(city.id)"></a>
<!--                                <a class="glyphicon glyphicon-remove" ng-click="delete(document.id)" title="Delete" href=""></a>-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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

        <!-- Modal -->
        <div class="modal fade" id="CityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">City</h4>
                    </div>
                    <div class="modal-body">
                        <form id="City-form" method="post" action="">
                            <div class="form-group">
                                <label class="form-label">States</label>
                                <select name="state_id" class="form-control">
                                    <option value="" disabled="" selected="">Select State </option>
                                    <option ng-repeat="state in states" value='@{{state.id}}'>@{{state.name}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name = "name" maxlength="50" id="city"   placeholder="City">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button class="btn btn-primary" type="button" ng-click="closeForm()">Cancel</button>
                         </form>
                    </div>
                </div>
            </div>
        </div>
        <!--  EditBlockModal  -->
    <div class="modal fade" id="CityEditFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="closeUpdateForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">City</h4>
                </div>
                <div class="modal-body">
                    <form id="City-update-form" method="post" action="">
                        <div class="form-group">
                            <label class="form-label">State</label>
                            <select name="state_id" class="form-control">
                                <option value="" disabled="">Select State</option>
                                <option ng-repeat="state in states" value='@{{state.id}}' ng-selected="city_edit.state_id == state.id">@{{state.name}}</option>
                            </select>
                         </div>
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="name" maxlength="50" id="city_edit" value="@{{city_edit.name}}"  placeholder="City">
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
    $(document).ready(function(){
        jQuery.validator.addMethod("domain", function(value, element) {
//        alert(value);
//            var  block =  $('.block_ex').val();
//            alert(block);
            if(value!='')
            {
                if(value != value.match(/^[A-Za-z\s]+$/))
                {
                    return false;
              }else{
                     return true;
               }
           }else{
               return true;
           }
        
          });
        $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            }); 
        $('#City-form').validate({
            rules: {
            name : {
                    required:true,domain:true
//                    remote:{
//                        url: generateUrl('task_category/check_category'),
//                        type: "post",
//                        dataType:"json",
//                        data: {category_name:function() {
//                                return $( "#category_name" ).val();
//                              },},
//                        success:function(r) {
//                                var result = r.response;
////                                alert(result);
//                               $( "label#category_name-error" ).remove();                        
//                                 if(result.success){
//                                     $( "label#category_name-error" ).remove();
//                                     return true;
//                                 }else{
//                                     $( "label#category_name-error" ).remove();
//                                     $( "#category_name" ).after( '<label id="category_name-error" class="error" for="category_name">Category name is already exists.</label>' );
//                                     return false;
//                                 }
//                             }
//                    }
                },
                 state_id:{
                         required:true,
                    },
            },
            messages: {
                    name: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter valid city name!"
                    }
                }
        });
         $('#City-update-form').validate({
            rules: {
                name :{required:true,domain:true},
                state_id:{
                         required:true,
                    },
            },
            messages: {
                    name: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter valid city name!"
                    }
                }
        });
    });
    </script>
@stop

