@section('title', 'Flat Documents')
@section('panel_title', 'Flat Documents')
@section('content')
    <script type="text/javascript">
        app.controller("folderCtrl", function(paginationServices,$scope,$http,$filter) {
            $scope.folders;
            $scope.type = '2';
            $scope.pagination = paginationServices.getNew(4);
            $scope.itemsPerPage = 4;

            $scope.search='';

            $scope.getFolders = function(type,offset,limit,search) {
               var options = {type:type,offset:offset,limit:limit};
               if(search){
                   options['search']=search;
               }
               var request_url = generateUrl('/flat/folder/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.folders = result.response.data;
                    $scope.pagination.total = result.response.total;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                    if (parseInt($scope.pagination.total) == 0 ){ $("#dataCheck").text("No Data Found."); }
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.$on('pagination:updated', function(event,data) {
              $scope.getFolders($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
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

            $scope.openFolderForm = function(){
                $('#folderFormModal').modal();
            };

            $scope.closeForm = function(){
                $("#folder-create-form")[0].reset();
                $("#folder-create-form label.error").remove();
                $('#folderFormModal').modal('hide');
            };

            $scope.closeUpdateForm = function(){
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
                    var request_url = generateUrl('admin_folder/create');
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
                            $( '#folder-create-form input[name="name"]' ).after( '<label  class="error" for="name">'+result.msg+'</label>' );
                        }
//                    },
//                    function(response) { // optional
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
                    $("#folder-update-form label.error").remove();
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('admin_folder/update/'+$scope.edit_id);
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
                            $scope.closeUpdateForm();
                        }else{
                            $("#folder-update-form label.error").remove();
                            $( '#folder-update-form input[name="name"]' ).after( '<label class="error" for="name">'+result.msg+'</label>' );
                        }


//                    },
//                    function(response) { // optional
//                           alert("fail");
                    });
                }

            });

            $scope.delete = function(id){
                var confirm_msg = confirm("Are you sure to delete this folder!");
                if(confirm_msg == true)
                {
                    var request_url = generateUrl('admin_folder/delete');
                     var records = $.param({id:id});
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                            var result = response.data.response; // to get api result

                            if(result.success){
                                grit('','Successfully deleted folder!');
                                $scope.pagination.total=0;
                                $scope.pagination.offset = 0;
                                $scope.pagination.currentPage = 1;

                                $scope.pagination.setPage(1);

                            }else{
                                if(result.folder_error){
                                    grit('',''+result.folder_error)
                                }
//                                grit('','Error in deleting file');
                            }

//                        },
//                        function(response) { // optional
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
            <div class="col-lg-12 form-group">
				<input ng-model="search" class="form-control pull-left" placeholder="Search By Name" style="width: 200px">
                <a class="btn btn-primary pull-right" href="{{route('admin.flat_reports')}}">Flat Document Reports</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>
                               Folder Name
                            </th>
                             <th>
                                Created By
                            </th>
                            <th>
                                Created On
                            </th>

                        </tr>
                    </thead>
                    <tbody >
                        <tr ng-if="pagination.total == 0" style="margin-left: 30px;">
			<td colspan="4" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
			</tr>
                            <tr ng-repeat="folder in folders">
                                 <td>@{{folder.id}}</td>
                                <td><a href="<?php echo route('admin.flat_documents_files', '');  ?>/@{{folder.id}}"</a>@{{folder.folder_name}}</td>
                                <td>@{{folder.first_name}} @{{folder.last_name}}</td>
                                <td>@{{folder.created_at}}</td>
                            </tr>
                    </tbody>
                </table>
              </div>
        </div>

<!--        <div class="row">
            <div class="col-sm-6 col-md-3 ng-scope" ng-repeat="folder in folders">
                <div class="thumbnail" style="
					  border-radius: 23px;
					  border-width: 6px;
					  text-align: center;
				 ">
                  <div class="center-block" style="width: 75px">
                        <i class="fa fa-folder-open" style="font-size: 5em;"></i>
                </div>
                  <div class="caption">
                    <h3 class="ng-binding">@{{folder.folder_name}}</h3>
                    <p class="ng-binding">@{{folder.first_name}} @{{folder.last_name}}</p>
                    <div class="" role="toolbar" style="text-align: center;">
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-primary" href="<?php echo route('admin.flat_documents_files', '');  ?>/@{{folder.id}}"  role="button">View Files</a>
                        </div>
                        <div class="btn-group btn-group-sm">
                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu btn-group-xs">
                                <li><a ng-click="openFolderEditForm(folder.id,folder.name)" href="" title="Edit"><i class="fa fa-pencil" style="margin-right: 5px;"></i>Update</a></li>
                                <li><a ng-click="delete(folder.id)" href="" title="Delete" ><i class="fa fa-remove" style="margin-right: 5px;"></i>Delete</a></li>
                          </ul>
                        </div>
                    </div>

                  </div>
                </div>
              </div>
        </div>-->
        <!---pagination--->
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
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="closeForm()"><span aria-hidden="true">&times;</span></button>
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
                      <button class="btn btn-primary" type="button" ng-click="closeForm()" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
              </div>
            </div>
        </div>

        <!-- EditFormModal -->
        <div class="modal fade" id="folderEditFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" ng-click="closeUpdateForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                        <input type="hidden" value="@{{edit_id}}" />
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button class="btn btn-primary" type="button" ng-click="closeUpdateForm()" data-dismiss="modal">Cancel</button>
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
