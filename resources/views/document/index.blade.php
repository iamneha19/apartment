@section('title', 'Documents')
@section('panel_title', 'Document')
@section('content')
    <script type="text/javascript">
        app.controller("folderCtrl", function(paginationServices,$scope,$http,$filter,$location) {
            $scope.folders;
            $scope.type = '1';
            @if(Input::get("type") == 'official')
            $scope.folder_type = '2';
            @else
            $scope.folder_type = '1';
            @endif
            
            $scope.pagination = paginationServices.getNew(4);
            $scope.itemsPerPage = 4;

            $scope.search='';
               
            $scope.getResidentFolders = function(type,offset,limit,search) {
               var options = {type:type,offset:offset,limit:limit};
               if(search){
                   options['search']=search;
               }
               var request_url = generateUrl('folder/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.folders = result.response.data;
                    $scope.pagination.total = result.response.total;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            $scope.getAdminFolders = function(type,offset,limit,search) {
               var options = {type:type,offset:offset,limit:limit};
               if(search){
                   options['search']=search;
               }
               var request_url = generateUrl('document/official/folderlist',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.folders = result.response.data;
                    $scope.pagination.total = result.response.total;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.$on('pagination:updated', function(event,data) {
              if($scope.folder_type === '1'){
                $scope.getResidentFolders($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);  
              }else{
                $scope.getAdminFolders($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);    
              }  
//              $scope.getFolders($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
            });
            
//            $scope.getFolders($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);

            $scope.$watch('search', function(newValue, oldValue) {
                if(newValue){
                    $scope.pagination.total=0;
                    $scope.pagination.offset = 0;
                    $scope.pagination.currentPage = 1;

                    $scope.pagination.setPage(1);
                }else{
                    $scope.pagination.setPage(1);
                }

            });
            
            $scope.tab = function(folder_type) {
                $scope.folder_type = folder_type;

                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                if($scope.folder_type === '1'){
                  $scope.getResidentFolders($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);  
                }else{
                  $scope.getAdminFolders($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);    
                }
                
            };

            $scope.openFolderForm = function(){
                $('#folderFormModal').modal();
            };
            
            $scope.closeForm = function(){
                $("#folder-create-form")[0].reset();
                $("#folder-create-form label.error").remove();
                $('#folderFormModal').modal('hide');
            };
            
            $scope.closeupdateForm = function(){
                $("#folder-update-form")[0].reset();
                $("#folder-update-form label.error").remove();
                $('#folderEditFormModal').modal('hide');
            };

            $('#folder-create-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid()){
                     $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Creating folder please wait..'); 
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('folder/create');
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                        var result = response.data.response; // to get api result
                        $('#folder-create-form').find('button[type=submit]').attr('disabled',false);
                        $('#folder-create-form').find('button[type=submit]').text('Submit');

                        if(result.success){
                            grit('','Folder created successfully!');
                            $scope.pagination.total=0;
                            $scope.pagination.offset = 0;
                            $scope.pagination.currentPage = 1;

                            $scope.pagination.setPage(1);
                            $scope.closeForm();
                        }else{
                            $("#folder-create-form label.error").remove();
                            $( '#folder-create-form input[name="name"]' ).after( '<label class="error" for="name">'+result.msg+'</label>' );
                        } 
                    }, 
                    function(response) { // optional
//                           alert("fail");
                    });
                }
                
            });
            
            $scope.edit_folder_name;
            $scope.edit_id;
            $scope.openFolderEditForm = function(folder_id,folder_name){
                $scope.edit_folder_name = folder_name;
                $scope.edit_id = folder_id;
                $('#folderEditFormModal').modal();
            };
            
            $('#folder-update-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid() && $scope.edit_id){
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('folder/update/'+$scope.edit_id);
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                        
                        var result = response.data.response; // to get api result
                        
                         if(result.success){
                            grit('','Folder updated successfully!');
                            $scope.pagination.total=0;
                            $scope.pagination.offset = 0;
                            $scope.pagination.currentPage = 1;

                            $scope.pagination.setPage(1);
                            $scope.closeupdateForm();
                        }else{
                            $("#folder-update-form label.error").remove();
                            $( '#folder-update-form input[name="name"]' ).after( '<label class="error" for="name">'+result.msg+'</label>' );
                        }
                        
                        
                    }, 
                    function(response) { // optional
//                           alert("fail");
                    });
                }
                
            });
            
         $scope.delete = function(id){
                var confirm_msg = confirm("Are you sure to delete this folder!");
                if(confirm_msg == true)
                {
                    var request_url = generateUrl('resident_folder/delete');
                     var records = $.param({id:id});
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                            var result = response.data.response; // to get api result 

                            if(result.success){
                                $scope.pagination.total=0;
                                $scope.pagination.offset = 0;
                                $scope.pagination.currentPage = 1;

                                $scope.pagination.setPage(1);
                                grit('','Successfully deleted folder!');
                            }else{
                                if(result.folder_error){
                                    grit('',''+result.folder_error)
                                }
//                                grit('','Error in deleting file');
                            }

                        }, 
                        function(response) { // optional
//                            alert("fail");
                        });
                }else{
                        return false;
                }
            };
        });
    </script>
    <div class="col-lg-12" ng-controller="folderCtrl" >
        <div class="row">
             <div class="col-lg-12">
                <ul class="nav nav-tabs">
                        <li  ng-class="{active: folder_type === '1'}"><a href="" ng-click="tab('1')">Resident</a></li>
                        <li  ng-class="{active: folder_type === '2'}"><a  href="" ng-click="tab('2')">Official</a></li>
                </ul>
             </div>
         </div>
        <div class="row form-group">
            <div class="col-lg-12"  style="margin-top:20px;">
                <label>Search:</label> 
                <input  ng-model="search">
                <button type="button" class="btn btn-primary pull-right" ng-click="openFolderForm()" ng-show="folder_type === '1'">Add Folder</button>
                
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 col-md-3 ng-scope" ng-repeat="folder in folders"> 
                <div class="thumbnail" style="
					  border-radius: 23px;
					  border-width: 6px;
					  text-align: center;
				 ">
                    <div class="center-block" style="width: 75px;">
                        <i class="fa fa-folder-open" style="font-size: 5em;"></i>
                    </div>
                  <div class="caption">
                      <h4 class="ng-binding" style="overflow: hidden;text-overflow: ellipsis;" title="@{{folder.folder_name}}">@{{folder.folder_name}}</h4>
                    <h6 class="ng-binding">@{{folder.first_name}} @{{folder.last_name}}</h6>
                   
                    <div style="text-align: center;" role="toolbar" ng-show="folder_type === '1'">
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-success" href="<?php echo route('document.resident','');  ?>/@{{folder.id}}"  role="button">View Files</a>
                        </div>
                        <div class="btn-group btn-group-sm" ng-show="({{Session::get('user.user_id')}} == folder.user_id )  ? 1 : 0">
                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu btn-group-xs">
                                <li><a href="" ng-click="openFolderEditForm(folder.id,folder.name)" title="Edit"><i class="fa fa-pencil" style="margin-right: 5px;"></i>Edit</a></li>
                                <li><a href="" ng-click="delete(folder.id)" title="Delete" ><i class="fa fa-remove" style="margin-right: 5px;"></i>Delete</a></li>
                          </ul>
                        </div> 
                    </div>
                    <div style="text-align: center;" role="toolbar" ng-show="folder_type === '2'">
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-success" href="<?php echo route('document.official','');  ?>/@{{folder.id}}"  role="button">View Files</a>
                        </div>
                    </div>     
                   
                  </div>
                </div>
              </div>
        </div>
        
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
        <div class="modal fade" id="folderFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Create Folder</h4>
                </div>
                <div class="modal-body">
                    <form id="folder-create-form" method="post" action="">
                        <div class="form-group">
                          <label class="form-label">Folder Name</label>
                          <input type="text" class="form-control" name="name" maxlength="50"  placeholder="Folder Name">
                        </div>
                        
<!--                        <div class="form-group">
                          <label>Folder Description</label>
                          <input type="text" class="form-control" name="text"  placeholder="Folder Description">
                        </div>-->
                      
                        <input type="hidden" name="type" value="1" >
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button class="btn btn-primary" type="button" ng-click="closeForm()">Cancel</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="folderEditFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" ng-click="closeupdateForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Update Folder</h4>
                </div>
                <div class="modal-body">
                    <form id="folder-update-form" method="post" action="">
                        <div class="form-group">
                          <label class="form-label">Folder Name</label>
                          <input type="text" class="form-control" name="name" maxlength="50" value="@{{edit_folder_name}}"  placeholder="Folder Name">
                        </div>
                        
<!--                        <div class="form-group">
                          <label>Folder Description</label>
                          <input type="text" class="form-control" name="text"  placeholder="Folder Description">
                        </div>-->
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button class="btn btn-primary" type="button" ng-click="closeupdateForm()" data-dismiss="modal">Cancel</button>
                    </form>
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
           $("#folder-create-form").validate({ 
                rules: {
                    name: "required"
                }
            });
            
            $("#folder-update-form").validate({ 
                rules: {
                    name: "required"
                }
            });
        });
    </script>
@stop
