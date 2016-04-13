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
            $scope.type = "Flat Document";
            $scope.access_type = 0;
  
            $scope.getDocument = function() {
               var request_url = generateUrl('flat/document/'+$scope.file_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.file = result.response.data;
                    console.log($scope.file);
                    if($scope.file.visible_to==2)
                           {
                               $scope.access_type = 1;
                               $scope.getRoles();
                               $scope.role_ids = result.response.role_ids;
                           }
//                    $scope.role_ids = result.response.role_ids;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getDocument();
            
             $scope.getRoles = function() {
                var request_url = generateUrl('acl/role/list');
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.roles = result.response;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
            $scope.getRoles();
            
            $scope.getAttendee = function()
            {
                $scope.access_type = 1;
                $scope.getRoles();
//                $scope.role_ids = result.response.role_ids;
            }
            
            $scope.getselectedroles = function(value)
          {
              
//              console.log(value);
//              console.log($scope.role_ids);
            if($.inArray(value, $scope.role_ids)!='-1'){
                return true;
            }else{
                return false;
            }
               
              
          };
            
            $scope.getFlatDocumentType = function() {
                var request_url = generateUrl('v1/category/type/list/'+ $scope.type);
                  $http.get(request_url)
                .success(function(response, status, headers, config) {  
                        $scope.flat_document_type = response.results;
//                        console.log(result.results);
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
            
            
            
            $scope.getFlatDocumentType();

            $('#document-update-form').submit(function(e){
                e.preventDefault();
                if ($('#document-update-form').valid()){
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Updating document please wait..');  
                   console.log(new FormData(this));
//                    var records = $.param($( this ).serializeArray());
                     var records = new FormData(this);
                    var request_url = generateUrl('update/flat/document/'+$scope.file_id);
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
//                            var folder_id = result.data.folder_id;
                            window.location="<?php echo route('document.resident') ?>";
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
                    <input type="hidden" name="folder_id" value="@{{file.folder_id}}">
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea type="text" class="form-control" name="description"  placeholder="File Description">@{{file.description}}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="category_id" class="form-control">
                            <option value="" disabled="" selected="">Select Type</option>
                            <option ng-repeat="type in flat_document_type"  value='@{{type.id}}' ng-selected="file.category_id == type.id">@{{type.name}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Visible to</label>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" class="access" name="visible_to" ng-checked="(file.visible_to == 1) ? 1 : 0 " value="1" >
                               Public
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" class="access" name="visible_to" ng-checked="(file.visible_to == 2) ? 1 : 0 " ng-click="getAttendee()" value="2" >
                               Private
                            </label>
                        </div>
                        <div class="visiblity-error"></div>
                    </div>
                    <div ng-show="access_type">
                        <div id="member_roles" class="form-group">
                            <label class="form-label">Member Roles</label>
                            <select name="role_id[]" class="form-control" multiple="multiple">
                                <option value="" disabled="" selected="">Select Roles</option>
                                <option ng-repeat="role in roles" ng-hide ="role.role_name =='Member' || role.role_name == 'Associate Member'" value='@{{role.id}}' ng-selected="getselectedroles(role.id)">@{{role.role_name}}</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a class="btn btn-primary" href="<?php echo  route('document.resident');  ?>">Cancel</a>
                </form>
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
    <script>
    $(document).ready(function(){
        $('.access').on('click',function(){
            if($(this).attr('checked'))
            {
                var value = $(this).attr('value');
                if(value == '2')
                {
                    $('#member_roles').show();
                }else{
                    $('#member_roles').hide();
                }
            }
        });
    });
</script>
<script>
    $(document).ready(function(){
        $('#document-update-form').validate({
            rules :
            {
                file : "required",
                description:"required",
                category_id:"required",
                visible_to:"required",
                "multipleselect[]":"required",
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "visible_to"  ) {
                    $( ".visiblity-error" ).html( error );
                }else {
                  error.insertAfter(element);
                }
            }
        });
    });
</script>
@stop
