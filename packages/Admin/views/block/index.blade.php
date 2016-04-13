@section('title', 'Admin Dashboard')
@section('panel_title', 'Blocks')
@section('panel_subtitle', 'List')
@section('content')
<script>
app.controller("BlockListCtrl", function(URL,paginationServices,$scope,$http) {
    
    $scope.block;
    $scope.pagination = paginationServices.getNew(5);
    $scope.itemsPerPage = 5;
    $scope.sort = 'block';
    $scope.sort_order = 'asc';
    $scope.search='';
  
    $scope.getBlock = function(offset,limit,sort,sort_order,search) {
         var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order}
            if(search){
                options['search']=search;
            }
        var request_url = generateUrl('block/list',options);
        $http.get(request_url)
//        $http.get(URL+'block/list?offset='+offset+'&limit='+limit+'&search='+search+'&sort='+sort+'&sort_order='+sort_order)
        .success(function(result, status, headers, config) {
            $scope.block = result.response.data;
            $scope.pagination.total = result.response.total;
            $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
        }).error(function(data, status, headers, config) {
            console.log(data);
        });
    };
  
    $scope.$on('pagination:updated', function(event,data) {
            $scope.getBlock($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
        });
        $scope.$watch('search', function(newValue, oldValue) {
            if(newValue){
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'block';
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
   
     $scope.openBlock = function(){
        $('#BlockModal').modal();
    };
    
    $scope.closeForm = function(){
        $("#block-form")[0].reset();
        $("#block-form label.error").remove();
        $('#BlockModal').modal('hide');
    };
    
    $scope.closeUpdateForm = function(){
        $("#block-update-form")[0].reset();
        $("#block-update-form label.error").remove();
        $('#BlockEditFormModal').modal('hide');
    };
    
    $scope.edit_block;
    $scope.edit_id;
    $scope.openBlockEditForm = function(block_id,block){
        $scope.edit_block = block;
        $scope.edit_id = block_id;
        $('#BlockEditFormModal').modal();
    };
    
    $scope.delete_id;
    $scope.openBlockDeleteForm = function(block_id){
        $scope.delete_id = block_id;
        $('#BlockDeleteFormModal').modal();
    };
            
    $('#block-update-form').submit(function(e){
        e.preventDefault();
        if ($(this).valid() && $scope.edit_id){
            var records = $.param($( this ).serializeArray());
            var request_url = generateUrl('block/update/'+$scope.edit_id);
            $http({
                url: request_url,
                method: "POST",
                data: records,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(function(response) {
                var result = response.data.response; // to get api result
                $('#block-update-form label.error').remove(); // Remove errors if any
                if(result.success){
                    $scope.pagination.total=0;
                    $scope.pagination.offset = 0;
                    $scope.pagination.currentPage = 1;
                    $scope.pagination.setPage(1);
                    $('#BlockEditFormModal').modal('hide');
                }else{
                     if(result.input_errors){
                        var errors = result.input_errors;    
                        for (var key in errors) {
                             var error = errors[key];
                             for (var index in error) {
                                $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('#block-update-form input[name="'+key+'"]');
                             }
                          }
                     }else if(result.block_error){
                        $('#block-update-form input[name="block"]' ).after( '<label id="block-error" class="error" for="block">This block is already taken.</label>' );
                     }
                } 
                
//            }, 
//            function(response) { // optional
//                   alert("fail");
            });
        }
    });
  
    $('#block-form').submit(function(e){
        e.preventDefault();
        if ($(this).valid()){
            var records = $.param($( this ).serializeArray());
            var request_url = generateUrl('block/create');
            $http({
                url: request_url,
                method: "POST",
                data: records,
		headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(function(response) {
                var result = response.data.response; // to get api result
                $('#block-form label.error').remove(); // Remove errors if any
                if(result.success){
                    $scope.pagination.total=0;
                    $scope.pagination.offset = 0;
                    $scope.pagination.currentPage = 1;
                    $scope.sort = 'block';
                    $scope.sort_order = 'desc';

                    $scope.pagination.setPage(1);
                
                    $scope.closeForm();
                    grit('','Block created successfully!');
                }else{
                     if(result.input_errors){
                        var errors = result.input_errors;    
                        for (var key in errors) {
                             var error = errors[key];
                             for (var index in error) {
                                $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('#block-form input[name="'+key+'"]');
                             }
                          }
                     }else if(result.block_error){
                        $('#block-form input[name="block"]' ).after( '<label id="block-error" class="error" for="block">This block is already taken.</label>' );
                     }
                }  
//            }, 
//            function(response) { // optional
//                alert("fail");
             }); 
	}
    }); 
    
    $('#block-delete-form').submit(function(e){
        e.preventDefault();
        if ($(this).valid() && $scope.delete_id){
            var request_url = generateUrl('block/delete');
            $http({
                url: request_url,
                method: "POST",
                data: $.param({id:$scope.delete_id}),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(function(response) {
                var result = response.data.response; // to get api result
                if(result.success){
                    grit('','Successfully deleted the block.');
                    $scope.pagination.total=0;
                    $scope.pagination.offset = 0;
                    $scope.pagination.currentPage = 1;
                    $scope.pagination.setPage(1);
                    $('#BlockDeleteFormModal').modal('hide');
                }else{
                      if(result.block_error){
                          $('#BlockDeleteFormModal').modal('hide');;
                        grit('','Could not delete this block, because flats are assigned to this block.');
                    }
                } 
                
//            }, 
//            function(response) { // optional
//                   alert("fail");
            });
        }
    });
    jQuery.validator.addMethod("domain", function(value, element) {
//        alert(value);
//            var  block =  $('.block_ex').val();
//            alert(block);
            if(value!='')
            {
                if(value != value.match(/^[a-zA-Z0-9]+$/))
                {
                    return false;
              }else{
                     return true;
               }
           }else{
               return true;
           }
        
          });
	
});

</script>
<script>
       $(document).ready(function(){
           $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            });
           $("#block-form").validate({
                rules: {
                  block: {
                    required:true,domain:true
                }
                },
                messages: {
                    block: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter valid block name!"
                    }
                }
            });
            
            
            $("#block-update-form").validate({
                rules: {
                  block: {
                    required:true,domain:true
                }
                },
                messages: {
                    block: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter valid block name!"
                    }
                }
            });
        });
    </script>
    <div class="col-lg-12" ng-controller="BlockListCtrl">
        <div class="row form-group">
            <div class="col-lg-12">
                <label>Search: <input ng-model="search"></label>
                <div class="btn-toolbar pull-right">
                   <button type="button" class="btn btn-primary" ng-click="openBlock()">Add Blocks</button> 
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>
                                <a href="" ng-click="order('block')">Block</a>
                                <span class="sortorder" ng-show="predicate === 'block'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('created_at')">Created At</a>
                                <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="blocks in block | filter:search">
                            <td>@{{blocks.block}}</td>
                            <td>@{{blocks.created_at}}</td>
                            <td>
                                <a class="glyphicon glyphicon-pencil" title="update" href="" ng-click="openBlockEditForm(blocks.id,blocks.block)"></a>
                                <a class="glyphicon glyphicon-remove" href="" title="delete" ng-click="openBlockDeleteForm(blocks.id,blocks.block)"></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
             
    <!--**Pagination**-->
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
	
    <!--  CreateBlockModal -->
    <div class="modal fade" id="BlockModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="closeForm()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create Blocks</h4>
                </div>
                <div class="modal-body">
                    <form id="block-form" method="post" action="">
                        <div class="form-group">
                            <label class="form-label">Block</label>
                            <input type="text" class="form-control block_ex" maxlength="10" name = "block"  placeholder="Block">
                        </div>          
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button class="btn btn-default" type="button" ng-click="closeForm()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
                
    <!--  EditBlockModal  -->
    <div class="modal fade" id="BlockEditFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="closeUpdateForm()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Block</h4>
                </div>
                <div class="modal-body">
                    <form id="block-update-form" method="post" action="">
                        <div class="form-group">
                            <label class="form-label">Block </label>
                            <input type="text" class="form-control block_ex" name="block" maxlength="10" value="@{{edit_block}}"  placeholder="Block">
                        </div>
                        <button type="submit" class="btn btn-success">Update</button>
                        <button class="btn btn-default" type="button" ng-click="closeUpdateForm()">Cancel</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    
        <!--  DeleteBlockModal  -->
        <div class="modal fade" id="BlockDeleteFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Delete Block</h4>
                    </div>
                    <div class="modal-body">
                        <p>Do you want to delete this block?</p>
                        <form id="block-delete-form" method="post" action="">
                            <button type="submit" class="btn btn-success">Delete</button>
                            <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
