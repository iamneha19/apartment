@section('title', 'Edit Flie')
@section('panel_title', 'Edit File')
@section('content')
    <script type="text/javascript">
        app.controller("fileCtrl", function(paginationServices,$scope,$http,$filter) {
            $scope.file;
            $scope.file_id={{$file_id}};
  
            $scope.getFile = function() {
               var request_url = generateUrl('admin_file/'+$scope.file_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.file = result.response.data;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            
            
            $scope.getFolders = function() {
                var request_url = generateUrl('admin_folder/list');
                $http.get(request_url)
                .success(function(result, status, headers, config) {  
                    $scope.folders = result.response.data;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getFile();
            $scope.getFolders();

            $('#file-update-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid()){
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Updating file please wait..'); 
                   console.log(new FormData(this));
//                    var records = $.param($( this ).serializeArray());
                     var records = new FormData(this);
                    var request_url = generateUrl('admin_file/update/'+$scope.file_id);
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined}
                    })
                    .then(function(response) {
                        var result = response.data.response;
                        console.log(result);
                        $('#file-update-form').find('button[type=submit]').attr('disabled',false);
                        $('#file-update-form').find('button[type=submit]').text('Update'); 
                        if(result.success)
                        {
                            grit('','Files updated successfully!');
                            var folder_id = result.data.folder_id;
                            window.location="<?php echo route('admin.files', '');  ?>"+'/'+folder_id;
                        }else{
                            console.log("error occurred!");
                            return false;
                        }
//                    }, 
//                    function(response) { // optional
//                           alert("fail");
                    });
                }
                
            });
            
            $scope.openFolderForm = function(){
                $('#fileFormModal').modal('hide');
                $('#folderFormModal').modal();
            };
            $scope.closeCreateFolderForm = function(){
                $("#folder-create-form")[0].reset();
                $("#folder-create-form label.error").remove();
                $('#folderFormModal').modal('hide');
            };
            
            $('#folder-create-form').submit(function(e){
                e.preventDefault();
                if($(this).valid())
                {
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('admin_folder/create');
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                        $('#folderFormModal').modal('hide');
                        $scope.getFolders();
//                    }, 
//                    function(response) { // optional
//                       alert("fail");
                    });
                }
            });
        });
    </script>
    <div class="col-lg-12" ng-controller="fileCtrl" >
        
        <div class="row">
            <div class="col-md-4"> 
                <form id="file-update-form" method="post" action="" enctype="multipart/form-data">
                         <div class="form-group">
                            <label class="form-label">File </label>
                            <div class="form-control-static file-static">
                              <p class="pull-left">@{{file.name}}</p>
                              <p class="pull-right"><a class="glyphicon glyphicon-remove remove-file" title="remove" href="javascript:void(0);"></a></p> 
                            </div>    
                            <div class="form-control-static file-input" style="display: none;" >
                                <div class="col-sm-10" style="padding-left: 0px;">
                                    <input type="file"  name="file" >
                                </div>
                                <div class="col-sm-2">
                                    <p><a class="glyphicon glyphicon-remove remove-input" title="remove" href="javascript:void(0);"></a></p>
                                </div>    
                            </div>
                        </div>
                        <div class="form-group">
                          <label class="form-label">Description</label>
                          <textarea type="text" class="form-control" name="description"  placeholder="File Description">@{{file.description}}</textarea>
                        </div>
                        <div class="form-group">
                          <label class="form-label">Folder</label>
                          <select class="form-control" name="folder_id">
                              <option value="" disabled="">Select a folder</option>
                              <option ng-repeat="folder in folders" ng-selected="(file.folder_id == folder.id) ? 1 : 0 " value='@{{folder.id}}'>@{{folder.name}}</option>
                          </select>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" ng-click="openFolderForm()">Create New Folder</button>
                        </div>    
                        <div class="form-group">
                            <label class="form-label" >Visible to</label>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="visible_to" ng-checked="(file.visible_to == 1) ? 1 : 0 " value="1" >
                                    All
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="visible_to" ng-checked="(file.visible_to == 2) ? 1 : 0 " value="2" >
                                    Owners
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="visible_to" ng-checked="(file.visible_to == 3) ? 1 : 0 " value="3" >
                                    Admin
                                </label>
                            </div>
                        </div>
                      <button type="submit" class="btn btn-success">Update</button>
                      <a class="btn btn-default" href="<?php echo route('admin.files', '');  ?>/@{{file.folder_id}}">Cancel</a>
                    </form>
              </div>
        </div>
       
        <!-- Modal -->
        <div class="modal fade" id="folderFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" ng-click ="closeCreateFolderForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Create Folder</h4>
                </div>
                <div class="modal-body">
                    <form id="folder-create-form" method="post" action="" enctype="multipart/form-data">
                        <div class="form-group">
                          <label>Folder Name</label>
                          <input type="text" class="form-control" name="name"  placeholder="Folder Name">
                        </div>
                    
                      <button type="submit" class="btn btn-primary">Create</button>
                      <button class="btn btn-primary" type="button" ng-click ="closeCreateFolderForm()" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    </div>
    <script>
        $('document').ready(function(){
           $("#file-update-form").validate({
                rules: {
                    file: "required",
                    description: "required",
                    visible_to: "required",
                }
            });
            
            $('.remove-file').on('click',function(){
                 $('.file-static').hide();
                 $('.file-input').show();
                 $('.remove-input').hide();
            });
            $('.remove-input').on('click',function(){
                $('.file-input').hide();
                $('.file-static').show();
                $('.remove-input').hide();
            });
        });
    </script>
    <script>
        $('document').ready(function(){
           $('#folder-create-form').validate({
              rules:{
                  name:"required"
              } 
           });
        });
        </script>
@stop
