@section('title', 'Edit Flie')
@section('panel_title', 'Edit Society Document')
@section('content')

<script>
     app.controller("fileCtrl", function(paginationServices,$scope,$http,$filter) {
         
         $scope.activeType = 'Society Document';
         $scope.file_id={{$id}};
        
        $scope.type = function() {
            var request_url = generateUrl('v1/admin/list/typeList/'+$scope.activeType);
              $http.get(request_url)
            .success(function(result, status, headers, config) {  
                    $scope.categories = result.results.data;
                    
            }).error(function(data, status, headers, config) {
                    console.log(data);
            });
        };
        
        
        
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
                        window.location="<?php echo route('admin.society_files', '');  ?>"+'/'+folder_id;
                    }else{
                        console.log("error occurred!");
                        return false;
                    }
//                }, 
//                function(response) { // optional
//                       alert("fail");
                });
            }

        });
        
        $('document').ready(function(){
           $("#file-update-form").validate({
                rules: {
                    file: "required",
                 
                    
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
        
         $scope.getFile = function() {
               var request_url = generateUrl('admin_file/'+this.file_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.document = result.response.data;
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
           
            $scope.getFile();
            $scope.type(); 
            
            
     });
</script>
    
<div class="col-lg-12" ng-controller="fileCtrl" >
    <div class="row">
        <div class="col-md-4"> 
            <form id="file-update-form" method="post" action="" enctype="multipart/form-data">
                     <div class="form-group">
                        <label class="form-label">File </label>
                        <div class="form-control-static file-static">
                          <p class="pull-left">@{{document.name}}</p>
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
                      <label>Description</label>
                      <textarea type="text" class="form-control" name="description"  placeholder="File Description">@{{document.description}}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label @{{document.category_id}}" for="emailsubject">Category</label>
<!--                        <select name="category_id" class="form-control" ng-if="ngSelected"
                                ng-model="ngSelected"
                                ng-options="category.name for category in categories track by category.id" 
                                
                                required>

                        </select>-->
                        <select name="category_id"  class="form-control" required>
                            <option ng-repeat="category in categories" value='@{{category.id}}' ng-selected="category.id == document.category_id">@{{category.name}}</option>
                         </select>
                         <div class="visiblity-error"></div>
                    </div>          
<!--                    <div class="form-group">
                        <label class="form-label" >Visible to</label>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="visible_to" ng-checked="(document.visible_to == 1) ? 1 : 0 " value="1" >
                                All
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="visible_to" ng-checked="(document.visible_to == 2) ? 1 : 0 " value="2" >
                                Owners
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="visible_to" ng-checked="(document.visible_to == 3) ? 1 : 0 " value="3" >
                                Admin
                            </label>
                        </div>
                    </div>-->
                    <input type="hidden" name="folder_id" value="@{{document.folder_id}}">
                    <input type="hidden" name="id" value="@{{document.id}}">
                    <button type="submit"  class="btn btn-primary">Update</button>
                  <a class="btn btn-primary" href="<?php echo route('admin.society_files', '');  ?>/@{{document.folder_id}}">Cancel</a>
                </form>
          </div>
    </div>
</div>
  @stop    
