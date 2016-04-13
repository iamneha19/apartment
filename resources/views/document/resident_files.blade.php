@section('title', 'Resident Files')
@section('panel_title', 'Documents')
@section('head')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/additional-methods.min.js"></script>
@stop
@section('content')
<script>
app.controller("DocumentCtrl", function(URL,paginationServices,$scope,$http) {
    $scope.documents;
    $scope.folders;
    $scope.type = "Flat Document";
    $scope.flat_id = {{$flat_id}};
    $scope.pagination = paginationServices.getNew(4);
    $scope.itemsPerPage = 4;
    $scope.search='';
    $scope.flat_document_type;
            
        
    $scope.getDocuments= function(offset,limit,search) {
        var options = {offset:offset,limit:limit};
        if(search){
            options['search']=search;
        }
        var request_url = generateUrl('document/list',options);
        $http.get(request_url)
        .success(function(result, status, headers, config) {  
            $scope.documents = result.response.data;
            console.log($scope.documents);
            $scope.pagination.total = result.response.total;
            $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
        }).error(function(data, status, headers, config) {
            console.log(data);
        });
    };
    
    $scope.getFlatdocuments = function(offset,limit,search)
    {
        var options = {offset:offset,limit:limit};
        if(search){
            options['search']=search;
        }
		var request_url = generateUrl('flat/document/list/'+ $scope.flat_id ,options);
        $http.get(request_url)
        .success(function(result, status, headers, config) {  
            $scope.flat_documents = result.response.data;
            console.log($scope.flat_documents);
            $scope.pagination.total = result.response.total;
            $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
			if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
        }).error(function(data, status, headers, config) {
            console.log(data);
        });
    };
//     $scope.getFlatdocuments();
     
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
            
    $scope.getRoles = function() {
                var request_url = generateUrl('acl/role/list');
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.roles = result.response;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
            
    $scope.$on('pagination:updated', function(event,data) {
//        $scope.getDocuments($scope.folder_id,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
        $scope.getFlatdocuments($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
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
            var request_url = generateUrl('flat_documents/delete');
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
    $scope.getFlatDocumentType();
    $scope.getRoles();
	$('#documentFormModal').modal();
    };
    
    $scope.closeDocumentForm = function(){
        $("#document-upload")[0].reset();
        $("#document-upload label.error").remove();
        $('#documentFormModal').modal('hide');
        $('#member_roles').hide();
    };
    
    $('#document-upload').submit(function(e){
        e.preventDefault();
        if ($('#document-upload').valid()){
            $(this).find('button[type=submit]').attr('disabled',true);
            $(this).find('button[type=submit]').text('Uploading document please wait..');  
           var records = new FormData(this);
//           $scope.documents.flat_id = $scope.flat_id;
            var request_url = generateUrl('/flat/document/create');
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
                    window.location="<?php echo route('document.resident') ?>";
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
    $scope.getFlats = function() {
                var request_url = generateUrl('flat/list');
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.flats = result.response.data;
                    console.log(result);
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
    $scope.getFlats();
    $( "#flat-select" ).change(function() {
                var optVal= $("#flat-select option:selected").val();
                $scope.flat_id = optVal;
				$scope.pagination.total =0;
                $scope.getFlatdocuments($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
//                 alert($scope.flat_id); 
              });
    
    $scope.openFolderForm = function(){
        $('#documentFormModal').modal('hide');
	$('#folderFormModal').modal();
    };
    
    $scope.closeCreateFolderForm = function(){
        $('#folderFormModal').modal('hide');
        $('#documentFormModal').modal();
    };
});
</script>
<div class="col-md-12" ng-controller="DocumentCtrl">
    
    
   
            <div  class="col-md-12 form-group">
                <input ng-model="search" class="form-control ng-pristine ng-untouched ng-valid"  placeholder="Search By Name" style="width: 200px;display: inline">
                <button type="button" class="btn btn-primary pull-right" ng-click="openDocumentForm()">Upload Document</button>
         
            </div>
    
            <div class="col-lg-12 form-group" >
                Flat/Shop/Office:<select id='flat-select'  name="flat_id" >
                    <option disabled value="">Select Flat/Shop/Office</option>
                    <option ng-repeat="flat in flats" value='@{{flat.flat_id}}'>@{{flat.building_name+' -'}} @{{(flat.block) ? flat.block+' -' : ''}} @{{flat.flat_no}}</option>
                </select>
                 
            </div>
   
        
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                Sr No.
                            </th>
                            <th>

                           Name

                            </th>
                            <th>

                           Category

                            </th>

                            <th>
                               Uploaded By

                            </th>
                            <th>
                               Uploaded On

                            </th>
                            <th>
                                Action
                            </th>

                        </tr>
                    </thead>
                    <tbody >
							<tr ng-if="pagination.total == 0">
								<td colspan="6" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
							</tr>
                            <tr ng-if="pagination.total > 0" ng-repeat="document in flat_documents">
                                <td>@{{document.id}}</td>
                                <!--<td><a href="@{{document.http_path}}" download>@{{document.name}}</a></td>-->
                                <td><a href="<?php echo route('documents.download') ?>?file=@{{document.http_path}}&name=@{{document.name}}">@{{document.name}}</a></td>
                                <td>@{{document.category_name}}</td>
                                <td>@{{document.first_name}}  @{{document.last_name}}</td>
                                <td>@{{document.created_at}}</td>
                                <td>
                                    <a class="glyphicon glyphicon-pencil" ng-show="({{Session::get('user.user_id')}} == document.user_id )  ? 1 : 0" title="edit" href="<?php echo route('documents.flat_edit','');  ?>/@{{document.id}}"></a>
                                    <a class="glyphicon glyphicon-remove" ng-show="({{Session::get('user.user_id')}} == document.user_id )  ? 1 : 0"  ng-click="delete(document.id)" title="Delete" href=""></a>
                                </td>
                            </tr>
                    </tbody>
                </table>
            </div>
       
        <div ng-if="pagination.total > 0" class="row">
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
                        <input type="hidden" name="flat_id" value="@{{flat_id}}">
                        <div class="form-group">
                          <label class="form-label">Description</label>
                          <textarea type="text" class="form-control" name="description"  placeholder="File Description"></textarea>
                        </div>
<!--                        <div class="form-group">
                            <label class="form-label">Folder</label>
                            <select name="folder_id">
                                <option value="" disabled="" selected="">Select a folder</option>
                                <option ng-repeat="folder in folders" value='@{{folder.id}}'>@{{folder.name}}</option>
                            </select>
                            <button type="button" class="btn btn-primary pull-right" ng-click="openFolderForm()">Create Folder</button>
                            <div class="visiblity_folder-error"></div>
                        </div>-->
                         <div class="form-group">
                                <label class="form-label">Type</label>
                                <select name="category_id" class="form-control">
                                    <option value="" disabled="" selected="">Select Type</option>
                                    <option ng-repeat="type in flat_document_type" value='@{{type.id}}'>@{{type.name}}</option>
                                </select>
                            </div>
                     
                        <div class="form-group">
                            <label class="form-label">Visible to</label>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" class="access" name="visible_to" value="1" >
                                   Public
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" class="access" name="visible_to" value="2" >
                                   Private
                                </label>
                            </div>
                            <div class="visiblity-error"></div>
                        </div>
                        
                        <div id="member_roles" class="form-group" style="display: none;">
                                <label class="form-label">Member Roles</label>
                                <select name="role_id[]" class="form-control" multiple="multiple">
                                      <option value="" disabled="" selected="">Select Roles </option>
                                      <option ng-repeat="role in roles" ng-hide ="role.role_name =='Member' || role.role_name == 'Associate Member'" value='@{{role.id}}'>@{{role.role_name}}</option>
                                  </select>
                        </div>
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button class="btn btn-primary" type="button" ng-click="closeDocumentForm()">Cancel</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
        
</div>
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
        $('#document-upload').validate({
            rules :
            {
                file : "required",
                description:"required",
                category_id:"required",
                visible_to:"required",
                'role_id[]':"required",
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
