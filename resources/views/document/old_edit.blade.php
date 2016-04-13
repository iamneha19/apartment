@section('title', 'Edit Document')
@section('panel_title', 'Edit Document')
@section('head')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/additional-methods.min.js"></script>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("fileCtrl", function(paginationServices,$scope,$http,$filter) {
            $scope.file;
            $scope.file_id={{$file_id}};
  
            $scope.getDocument = function() {
               var request_url = generateUrl('document/'+$scope.file_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.file = result.response.data;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            
            
            $scope.getFolders = function() {
                var request_url = generateUrl('folder/list');
                $http.get(request_url)
                .success(function(result, status, headers, config) {  
                    $scope.folders = result.response.data;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getDocument();
            $scope.getFolders();

            $('#document-update-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid()){
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Updating document please wait..');  
                   console.log(new FormData(this));
//                    var records = $.param($( this ).serializeArray());
                     var records = new FormData(this);
                    var request_url = generateUrl('document/update/'+$scope.file_id);
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined}
                    })
                    .then(function(response) {
                         var result = response.data.response;
                        $('#document-update-form').find('button[type=submit]').attr('disabled',false);
                        $('#document-update-form').find('button[type=submit]').text('Update');
                        if(result.success)
                        {
                            grit('','Documents updated successfully!');
                            var folder_id = result.data.folder_id;
                            window.location="<?php echo route('document.resident','') ?>"+'/'+folder_id;
                        }else{
                             console.log('returned false');
//                            return false;
                        }
                    }, 
                    function(response) { // optional
//                           alert("fail");
                    });
                }
                
            });
            
            $scope.openFolderForm = function(){
                $('#fileFormModal').modal('hide');
                $('#folderFormModal').modal();
            };
            
            $scope.closeForm = function(target_id){
                $('#'+target_id).modal('hide');
            };
            
            $('#folder-create-form').submit(function(e){
                e.preventDefault();
                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('folder/create');
                $http({
                    url: request_url,
                    method: "POST",
                    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .then(function(response) {
                    $('#folderFormModal').modal('hide');
                    $scope.getFolders();
                }, 
                function(response) { // optional
//                       alert("fail");
                });
            });
        });
    </script>
    <div class="col-lg-12" ng-controller="fileCtrl" >
        
        <div class="row">
            <div class="col-md-4"> 
                <form id="document-update-form" method="post" action="" enctype="multipart/form-data">
                         <div class="form-group">
                            <label class="form-label">File</label>
                            <div class="form-control-static file-static">
                              <p class="pull-left">@{{file.name}}</p>
                              <p class="pull-right"><a class="glyphicon glyphicon-remove remove-file" title="remove" href="javascript:void(0);"></a></p> 
                            </div>    
                            <div class="form-control-static file-input" style="display: none;" >
                                <div class="col-sm-10" style="padding-left: 0px;">
                                    <input type="file"  name="file" id="input_file" >
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
                            <label class="form-label">Visible to</label>
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
                        </div>
                      <button type="submit" class="btn btn-primary">Update</button>
                      <a class="btn btn-primary" href="<?php echo  route('document.resident','');  ?>/@{{file.folder_id}}">Cancel</a>
                    </form>
              </div>
        </div>
       
        <!-- Modal -->
        <div class="modal fade" id="folderFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Create Folder</h4>
                </div>
                <div class="modal-body">
                    <form id="folder-create-form" method="post" action="" enctype="multipart/form-data">
                        <div class="form-group">
                          <label>Folder Name</label>
                          <input type="text" class="form-control" name="name"  placeholder="Folder Name">
                        </div>
                    
                      <button type="submit" class="btn btn-success">Create</button>
                      <button class="btn btn-default" type="button" ng-click="closeForm('folderFormModal')">Cancel</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    </div>
    <script>
        $('document').ready(function(){
           $.validator.addMethod('filesize', function(value, element, param) {
                // param = size (en bytes) 
                // element = element to validate (<input>)
                // value = value of the element (file name)
                return this.optional(element) || (element.files[0].size <= param) 
            },'File must less than 1MB'); 
           $("#document-update-form").validate({
                rules: {
                    file: {
                        required: true,
                        extension: "pdf|doc|docx|jpg|jpeg|png|gif|xls|xlsx|csv|txt",
                        filesize: 1048576
                    },
                    description:"required",
                    folder_id:"required",
                    visible_to:"required"
                },
                messages: {
                    file: {
                      extension: "Please upload valid file."
                    }
                  },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "visible_to"  ) {
                        $( ".visiblity-error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
            });
            
            $('.remove-file').on('click',function(){
                $('.file-static').hide();
                 $('.file-input').show();
                 $('.remove-input').hide();
                  $('.remove-file').hide();
                 
            });
            $('.remove-input').on('click',function(){
                $('.file-input').hide();
                $('.file-static').show();
//                $('.remove-input').hide();
            });
//            $('#input_file').on('change',function(){
//                 $('.remove-input').show();
//            });
        });
    </script>
@stop
