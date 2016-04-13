@section('title', 'Society Flies')
@section('panel_title', 'Society File')
@section('head')
<script>
app.controller('SocietyFileController',function($http,paginationServices,$scope,$filter){
    $scope.folder;
    $scope.activeType = 'Society Document';
    $scope.documents;
    $scope.document;
    $scope.pagination = paginationServices.getNew(4);
    $scope.itemsPerPage = 4;  
    $scope.search='';
    
    $scope.type = function() {
        var request_url = generateUrl('v1/admin/list/typeList/'+$scope.activeType);
          $http.get(request_url)
        .success(function(result, status, headers, config) {  
                $scope.categories = result.results.data;
        }).error(function(data, status, headers, config) {
                console.log(data);
        });
   };
 
    $scope.openFolderForm = function() {
        $('#fileFormModal').modal('show');
        $scope.type();
    }
    
    $scope.delete = function() {
        var r = confirm("Deleted Document cannot be retrieved");
        var $this = this;
        if (r == true) {
            $http.post(generateUrl('admin_file/delete'),{'id':$this.document.id})
            .then(function(r){
                
                grit('',r.data.response.msg);
                $scope.documents= $filter('filter')($scope.documents, function(value, index) {return value.id != $this.document.id});
                $scope.getFiles($scope.pagination.offset,$scope.pagination.itemsPerPage);

            });
        } else 
        return;
    }
    
    $scope.openReport = function() {
                console.log(this);
              
        $('#reportModal').modal('show');
        $http.get(generateUrl('admin_file/mandatoryFile/'+$scope.activeType))
        .then(function(r){
            $scope.files = r.data.response; 
        });
        
    }
    
    $scope.edit = function() {
        window.location="<?php echo route('admin.editSociety_file', '');  ?>"+'/'+this.document.id;
    }
    
    $scope.closeFileForm = function() {
        $("#file-upload-form")[0].reset();
        $("#category_id-error").remove();
        $("#file-error").remove();
    }
    
    $scope.getFiles = function(offset,limit,search) {
    var options = {offset:offset,limit:limit};
    if(search){
            options['search'] = search;
        }
    $http.get(generateUrl('admin_file/listsocietydocuments',options))
    .then(function(r){
       $scope.documents = r.data.response.data;   
        $scope.pagination.total = r.data.response.total;
        $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);   
        if (parseInt($scope.pagination.total) == 0 ){ $("#dataCheck").text("No Data Found."); }
    });
    }
    
    $scope.getFiles($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
    
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
            
//    $scope.searchFile = function() {
//        console.log(this.search);
//    $http.get(generateUrl('admin_file/search')+'&search='+this.search)
//    .then(function(r) {
//        grit('',r.data.response.msg);
//        $scope.documents = r.data.response.data;  
//    });
//    }
    

    $scope.textFormat = function(text,document_id){
               var text =  text.replace(/(<([^>]+)>)/ig,"");   // To strip html tags

                var shortText = jQuery.trim(text).substring(0, 50)
                          .trim(this) + '...';
                          //+'<a href="'+notice_url+'/'+notice_id+'">Read More</a>';
                return shortText;
    };
    
    $('#file-upload-form').submit(function(e){
    e.preventDefault();
    if ($(this).valid()){
        $(this).find('button[type=submit]').text('Uploading file please wait..'); 
        $(this).find('button[type=submit]').attr('disabled',true);
        var records = new FormData(this);
        var request_url = generateUrl('admin_file/society/upload');
        $http({
            url: request_url,
            method: "POST",
            data: records,
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .then(function(response) {
            var result = response.data.response; // to get api result
//                        console.log(result.folder_id);
            var folder_id = result.folder_id;
            $('#file-upload-form').find('button[type=submit]').attr('disabled',false);
            $('#file-upload-form').find('button[type=submit]').text('Submit');

            if(result.success){
                grit('','Files uploaded successfully');
                    $scope.pagination.total=0;
                    $scope.pagination.offset = 0;
                    $scope.pagination.currentPage = 1;

                    $scope.pagination.setPage(1);
                    $scope.closeFileForm();
                    window.location= window.location.href;
            }else{
                console.log('error occured!');
                return false;
            }
//        }, 
//        function(response) { // optional
//               alert("fail");
        });
        }

    });
    
        $scope.$on('pagination:updated', function(event,data) {
          $scope.getFiles($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
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
@stop 

@section('content')

<div class="col-md-12" ng-controller="SocietyFileController">
    <div class="row form-group">
        <div class="col-lg-12">
            @if($listPermission)
            
                 <!--<label>Search: <input ng-model="search" ng-model-options="{ debounce: 1000 }" ></label>-->
				 <input ng-model='search' class="form-control" placeholder="Search By Name" style="width: 300px;margin-bottom: -31px">
          
            @endif
                @if($createPermission)
                <button type="button" style="margin-left:10px;" class="btn btn-primary pull-right" ng-click="openFolderForm()">Upload File</button>
                @endif
                <a type="button" href="<?php echo route('admin.report',''); ?>"  class="btn btn-primary pull-right" > Report</a>      
        </div>
    </div>
    @if(!$listPermission)
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Warning!</strong> You don't have permission to access this page.
        </div>
    @else
    <div class="clearfix">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Category</th>                    
                    <th>Description</th>
                    <th>Uploaded By</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="pagination.total == 0" style="margin-left: 30px;">
                    <td colspan="7" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                </tr>
                <tr ng-repeat="document in documents" class="edit" on-finish-render="ngRepeatFinished">
                    <td>@{{document.id}}</td>
                <td><a href="<?php echo route('documents.download') ?>?file=@{{document.http_path}}&name=@{{document.name}}">@{{document.name}}</a></td>
                
                <td>@{{document.category_type}}</td>   
                <td ng-bind-html="textFormat(document.description,document.id)" style="clear:both;word-wrap: break-word;"></td>                          
                <!-- <td>@{{document.description}}</td> -->
                <td>@{{document.first_name}} @{{document.last_name}}</td> 
                <td>@{{document.created_at}}</td>
                <td>
                    <a class="glyphicon glyphicon-pencil"  href="<?php echo route('admin.editSociety_file','');  ?>/@{{document.id}}" title="Edit" ></a>
                    <a class="glyphicon glyphicon-remove" ng-click="delete() "href=""  title="Delete"></a>
                </td>
            </tr>
            </tbody>
        </table>
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
    </div>
    @endif
    
<!--Adding File Modal-->

<div class="modal fade" id="fileFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" ng-click="closeFileForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Upload File</h4>
        </div>
        <div class="modal-body">
            <form id="file-upload-form" name="uploadForm" method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                  <label class="form-label">File </label>
                  <input  type="file"  name="file" >
                </div>                
                <div class="form-group">
                    <label>Description</label>
                    <textarea type="text" class="form-control" name="description"  value="" placeholder="File Description"></textarea>
                    <div class="visiblity-error"></div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="emailsubject">Category</label>
                    <select name="category_id"  class="form-control" required>
                        <option  disabled="" selected="">Select Category</option>
                        <option  ng-repeat="category in categories" value='@{{category.id}}'>@{{category.name}}</option>
                     </select>
                     <div class="visiblity-error"></div>
                </div>
                <input type="hidden" name="folder_id"value="">
              <button type="submit" class="btn btn-primary">Submit</button>
              <button class="btn btn-primary" type="button" ng-click="closeFileForm()" data-dismiss="modal">Cancel</button>
            </form>
        </div>
      </div>
    </div>
</div>

<!--Report Modal-->
<div id="reportModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Report</h4>
      </div>
        <div class="modal-body">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Society Document Type</th>
                    <th>Mandatory</th>
                    <th>Uploaded</th>    
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="file in files">
                    <td>@{{file.name}}</td>
                    <td>@{{ file.is_mandatory == 1 ? 'Yes' : 'No'}}</td>
                    <td>@{{ file.file_name == null ? 'Not Uploaded' : 'Uploaded'}}</td>
                </tr>
            </tbody>
        </table>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>
 <script>
        $('document').ready(function(){
           $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            }); 
            $("#file-upload-form").validate({ 
                rules: {
                    file: "required"
                }
            });
        });
    </script>
@stop
