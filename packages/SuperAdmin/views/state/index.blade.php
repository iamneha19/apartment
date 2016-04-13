@section('title', 'state Dashboard')
@section('panel_title', 'State')
@section('panel_subtitle', 'List')
@section('content')
    <script>
    app.controller("StateListCtrl", function(URL,paginationServices,$scope,$http) {
        $scope.state;
        $scope.pagination = paginationServices.getNew(5);
        $scope.itemsPerPage = 5;
        $scope.sort = 'state';
        $scope.sort_order = 'asc';
        $scope.search='';
        $scope.getState = function(page,search) {
             var options = {page:page,orderby:'ASC'}
            if(search){
                options['search']=search;
            }
            var request_url = generateUrl('v1/states',options);
            console.log(request_url);
            $http.get(request_url)
            .success(function(result, status, headers, config) {
                $scope.state = result.results.data;
                console.log($scope.state);
                $scope.pagination.total = result.results.total;
                $scope.pagination.pageCount = result.results.last_page;
				if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };

        $scope.$on('pagination:updated', function(event,data) {
            $scope.getState($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
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
            $('#StateModal').modal();
        };
        
        $scope.closeForm = function(){
            $("#State-form")[0].reset();
            $("#State-form label.error").remove();
            $('#StateModal').modal('hide');
        };
        
        $scope.closeUpdateForm = function(){
            $("#State-update-form")[0].reset();
            $("#State-update-form label.error").remove();
            $('#StateEditFormModal').modal('hide');
        };
        $('#State-form').submit(function(e){
            
            e.preventDefault();
            if($('#State-form').valid())
            {
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Creating state please wait..');
                var data = $(this).serializeArray();
//                data.push({name:'society_id',value:society_id});
                var records = $.param(data);
//                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('v1/state');
                $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                .then(function(response) {
                    $("#State-form").find('button[type=submit]').attr('disabled',false);
                    $("#State-form").find('button[type=submit]').text('Submit');
                    var result = response.data;
                    console.log(result);
                    if(result.status=="success")
                    {
//                        $scope.pagination.total=0;
//                        $scope.pagination.offset = 0;
//                        $scope.pagination.currentPage = 1;
//                        $scope.pagination.setPage(1); 
                        $scope.closeForm();
                        grit('','State created successfully!');
                        location.reload();
                    }else{
                        $("#State-form label.error").remove();
                        if(result.status=='validation_failed')
                            {
                                $( "label#state-error" ).remove();
                                $( "#state" ).after( '<label id="state-error" class="error" for="state">State name is already exists.</label>' );
                            }else{
                                $( "label#state-error" ).remove();
                            }
                    }
                }, 
                function(response) { // optional
                    alert("fail");
                }); 
            }
        }); 
        
        $scope.edit_state;
        $scope.edit_id;
        
        $scope.openStateEditForm = function(state_id,state){
            $scope.edit_state = state;
            $scope.edit_id = state_id;
//            console.log($scope.edit_id);
            $('#StateEditFormModal').modal();
        };
        
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
        
        $('#State-update-form').submit(function(e){
        e.preventDefault();
        if ($(this).valid() && $scope.edit_id){
            $(this).find('button[type=submit]').attr('disabled',true);
            $(this).find('button[type=submit]').text('updating state please wait..');
            var records = $.param($( this ).serializeArray());
            console.log(records);
            var request_url = generateUrl('v1/state/update/'+$scope.edit_id);
            $http({
                url: request_url,
                method: "POST",
                
                data: records,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(function(response) {
                $("#State-update-form").find('button[type=submit]').attr('disabled',false);
                $("#State-update-form").find('button[type=submit]').text('Submit');
                var result = response.data;
                console.log(result);
                    if(result.status=='success')
                    {
                        $scope.pagination.total=0;
                        $scope.pagination.offset = 0;
                        $scope.pagination.currentPage = 1;
                        $scope.pagination.setPage(1);   
                        $('#StateEditFormModal').modal('hide');
                        grit('','State updated successfully!');
                        location.reload();
                    }else{
                        if(result.status=='validation_failed')
                            {
                                $( "#state-error" ).remove();
                                $( "#state_edit" ).after( '<label id="state_edit-error" class="error" for="state_edit">State name is already exists.</label>' );
                            }else{
                                $( "label#state_edit-error" ).remove();
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
    

    <div class="col-lg-12" ng-controller="StateListCtrl">
        <div class = "row">
            <div class = "col-lg-12" style="height: 50px;">
                <input ng-model='search' class="form-control" placeholder="Search" style="width: 300px;float: left">
                <!--<span class="pull-right" style="padding:7px;">-->
                    <button type="button" class="btn btn-primary pull-right" ng-click="openState()">Create State</button>
                <!--</span>-->
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>
<!--                                <a href="" ng-click="order('state')">State</a>-->
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
                        <tr ng-if="pagination.total > 0" ng-repeat="states in state | filter:search">
                            <td>@{{states.name}}</td>
                            <td>@{{states.created_at}}</td>
                            <td>
                                <a class="glyphicon glyphicon-pencil" title="update" href="" ng-click="openStateEditForm(states.id,states.name)"></a>
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
                    <li ng-repeat="n in pagination.range()"  ng-class="{active: n == pagination.currentPage}" ng-click="pagination.setPage(n)">
                      <a href>@{{n}}</a>
                    </li>
                    <li ng-class="pagination.nextPageDisabled()">
                        <a href ng-click="pagination.nextPage()" title="Next">Next <i class="fa fa-angle-double-right"></i></a>
                    </li>
                </ul>
            </div> 
        </div>

        <!-- Modal -->
        <div class="modal fade" id="StateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">State</h4>
                    </div>
                    <div class="modal-body">
                        <form id="State-form" method="post" action="">
                            <div class="form-group">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name = "name" maxlength="50" id="state"  placeholder="State">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button class="btn btn-primary" type="button" ng-click="closeForm()">Cancel</button>
                         </form>
                    </div>
                </div>
            </div>
        </div>
        <!--  EditBlockModal  -->
    <div class="modal fade" id="StateEditFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="closeUpdateForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">State</h4>
                </div>
                <div class="modal-body">
                    <form id="State-update-form" method="post" action="">
                        <div class="form-group">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="name" maxlength="50" id="state_edit" value="@{{edit_state}}"  placeholder="State">
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
        $('#State-form').validate({
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
            },
            messages: {
                    name: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter valid state name!"
                    }
                }
        });
         $('#State-update-form').validate({
            rules: {
                name :{
                    required:true,domain:true
                },
            },
            messages: {
                    name: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter valid state name!"
                    }
                }
        });
    });
    </script>
@stop

