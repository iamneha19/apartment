@section('title', 'Resident Files')
@section('panel_title', 'Resident Files')
@section('head')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/additional-methods.min.js"></script>
@stop
@section('content')

<script>
app.controller("DocumentCtrl", function(URL,paginationServices,$scope,$http) {
    $scope.documents;
    $scope.folders;
    $scope.folder_id = {{ $folder_id }};
    
    $scope.pagination = paginationServices.getNew(4);
    $scope.itemsPerPage = 4;
    $scope.search='';
            
        $scope.getFolder = function(id) {
                var request_url = generateUrl('folder/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.folder_name = result.response;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            
        $scope.getFolder({{$folder_id}});
        
    $scope.getDocuments= function(folder_id,offset,limit,search) {
        var options = {folder_id:folder_id,offset:offset,limit:limit};
        if(search){
            options['search']=search;
        }
        var request_url = generateUrl('document/list',options);
        $http.get(request_url)
        .success(function(result, status, headers, config) {  
            $scope.documents = result.response.data;
            $scope.pagination.total = result.response.total;
            $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
        }).error(function(data, status, headers, config) {
            console.log(data);
        });
    };
    
    $scope.getFolders = function() {
        var request_url = generateUrl('folder/allData');
        $http.get(request_url)
        .success(function(result, status, headers, config) {  
            $scope.folders = result.response.data;
        }).error(function(data, status, headers, config) {
            console.log(data);
        });
    };
    
    $scope.$on('pagination:updated', function(event,data) {
        $scope.getDocuments($scope.folder_id,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
    });

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
    
    $scope.delete = function(id){
        var confirm_msg = confirm("Are you sure to delete this document!");
        if(confirm_msg == true)
        {
            var request_url = generateUrl('document/delete');
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
                        grit('','Successfully deleted document');
                    }else{
                        grit('','Error in deleting document');
                    }

                }, 
                function(response) { // optional
//                    alert("fail");
                });
        }else{
                return false;
        }
    };
    $scope.openDocumentForm = function(){
        $scope.getFolders();
	$('#documentFormModal').modal();
    };
    
    $scope.closeDocumentForm = function(){
        $("#document-upload")[0].reset();
        $("#document-upload label.error").remove();
        $('#documentFormModal').modal('hide');
    };
    
    $('#document-upload').submit(function(e){
        e.preventDefault();
        if ($(this).valid()){
            $(this).find('button[type=submit]').attr('disabled',true);
            $(this).find('button[type=submit]').text('Uploading document please wait..');  
           var records = new FormData(this);
            var request_url = generateUrl('document/create');
            $http({
                url: request_url,
                method: "POST",
                data: records,
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            })
            .then(function(response) {
                var result = response.data.response; // to get api result
                var folder_id = result.folder_id;
                $('#document-upload').find('button[type=submit]').attr('disabled',false);
                $('#document-upload').find('button[type=submit]').text('Submit');
               
                if(result.success){
                    grit('','Document uploaded successfully!');
                    window.location="<?php echo route('document.resident','') ?>"+'/'+folder_id;
//                    $scope.pagination.total=0;
//                    $scope.pagination.offset = 0;
//                    $scope.pagination.currentPage = 1;
//
//                    $scope.pagination.setPage(1);
//                    $scope.closeDocumentForm();
                }else{
                    console.log('returned false');
                    return false;
                }
                  
            }, 
            function(response) { // optional
//                   alert("fail");
            }); 
        }
        
    });
    
    $scope.openFolderForm = function(){
        $('#documentFormModal').modal('hide');
	$('#folderFormModal').modal();
    };
    
    $scope.closeCreateFolderForm = function(){
        $('#folderFormModal').modal('hide');
        $('#documentFormModal').modal();
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
                    $('#folderFormModal').modal('hide');
                    $scope.openDocumentForm();
                }else{
                    $( '#folder-create-form input[name="name"]' ).after( '<label id="name-error" class="error" for="name">'+result.msg+'</label>' ); 
                }
                
            }, 
            function(response) { // optional
//                   alert("fail");
            });
        } 
    });
});
</script>
<script>
var folder_id = {{$folder_id}};
</script>
    <div class="col-md-12" ng-controller="DocumentCtrl">
        <div class="row form-group">
            <div class="col-lg-12">
                <h4>Files under : @{{folder_name.name}}</h4>
            </div>
            <div class="col-md-12">
                <!--<a href='{{route('folders')}}' type="button" class="btn btn-default pull-left">Back to folders</a>-->
               <a class="btn btn-primary" href="{{route('folders')}}" class="pull-left" ><< Back to folders</a>
                <button type="button" class="btn btn-primary pull-right" ng-click="openDocumentForm()">Upload Document</button>
            </div>
        </div>
        <div class="row form-group">
            <div  class="col-md-12" >
                <label>Search:</label> 
                <input  ng-model="search">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>

                           Name

                            </th>

                            <th>
                               Uploaded By

                            </th>
                            <th>
                               Uploaded On

                            </th>
                            <th>
                                Actions
                            </th>

                        </tr>
                    </thead>
                    <tbody >
                            <tr ng-repeat="document in documents">
                                <!--<td><a href="@{{document.http_path}}" download>@{{document.name}}</a></td>-->
                                <td><a href="<?php echo route('documents.download') ?>?file=@{{document.http_path}}&name=@{{document.name}}">@{{document.name}}</a></td>
                                <td>@{{document.first_name}}</td>
                                <td>@{{document.created_at}}</td>
                                <td>
                                    <a class="glyphicon glyphicon-pencil" ng-show="({{Session::get('user.user_id')}} == document.user_id )  ? 1 : 0" title="edit" href="<?php echo route('documents.edit','');  ?>/@{{document.id}}"></a>
                                    <a class="glyphicon glyphicon-remove" ng-show="({{Session::get('user.user_id')}} == document.user_id )  ? 1 : 0"  ng-click="delete(document.id)" title="Delete" href=""></a>
                                </td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <ul class="pagination pagination-sm" ng-show="(pagination.pageCount) ? 1 : 0">
                    <li ng-class="pagination.prevPageDisabled()">
                      <a href ng-click="pagination.prevPage()"><i class="fa fa-angle-double-left"></i> Prev</a>
                    </li>
                    <li ng-repeat="n in pagination.range()" ng-class="{active: n == pagination.currentPage}" ng-click="pagination.setPage(n)">
                      <a href>@{{n}}</a>
                    </li>
                    <li ng-class="pagination.nextPageDisabled()">
                      <a href ng-click="pagination.nextPage()">Next <i class="fa fa-angle-double-right"></i></a>
                    </li>
                </ul>
            </div> 
        </div>
        
        
        <!-- Modal -->
        <div class="modal fade" id="documentFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" ng-click="closeDocumentForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Upload Document</h4>
                </div>
                <div class="modal-body">
                    <form id="document-upload" method="post" action="" enctype="multipart/form-data">
                         <div class="form-group">
                          <label class="form-label">File</label>
                          <input type="file" name="file">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Description</label>
                          <textarea type="text" class="form-control" name="description"  placeholder="File Description"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Folder</label>
                            <select name="folder_id">
                                <option value="" disabled="" selected="">Select a folder</option>
                                <option ng-repeat="folder in folders" value='@{{folder.id}}'>@{{folder.name}}</option>
                            </select>
                            <button type="button" class="btn btn-primary pull-right" ng-click="openFolderForm()">Create Folder</button>
                            <div class="visiblity_folder-error"></div>
                        </div>
                     
                        <div class="form-group">
                            <label class="form-label">Visible to</label>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="visible_to" value="1" >
                                    All
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="visible_to" value="2" >
                                    Owners
                                </label>
                            </div>
                            <div class="visiblity-error"></div>
                        </div>
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button class="btn btn-primary" type="button" ng-click="closeDocumentForm()">Cancel</button>
                    </form>
                </div>
              </div>
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
                    <form id="folder-create-form" method="post" action="" >
                        <div class="form-group">
                          <label class="form-label">Folder Name</label>
                          <input type="text" class="form-control" name="name" maxlength="50"  placeholder="Folder Name">
                        </div>
                    
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button class="btn btn-primary" type="button" ng-click="closeCreateFolderForm()">Cancel</button>
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
            $.validator.addMethod('filesize', function(value, element, param) {
                // param = size (en bytes) 
                // element = element to validate (<input>)
                // value = value of the element (file name)
                return this.optional(element) || (element.files[0].size <= param) 
            },'File must less than 1MB');
           $("#document-upload").validate({ 
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
                      
                      extension: "Please upload valid file of pdf, doc, docx, jpg, jpeg, png, gif, xls, xlsx, csv, txt"
                    }
                  },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "visible_to"  ) {
                        $( ".visiblity-error" ).html( error );
                    }else if (element.attr("name") == "folder_id"  ) {
                        $( ".visiblity_folder-error" ).html( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
            });
            
            $("#folder-create-form").validate({ 
                rules: {
                    name: "required"
                }
            });
            
            
        });
    </script>
@stop
