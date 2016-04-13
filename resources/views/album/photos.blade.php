@section('title', 'Album Photos')
@section('panel_title', 'Photos')
@section('head')
    <style>
            .my-drop-zone { border: dotted 3px lightgray; }
            .nv-file-over { border: dotted 3px red; } /* Default class applied to drop zones on over */
            .another-file-over-class { border: dotted 3px green; }

            html, body { height: 100%; }

            canvas {
                background-color: #f3f3f3;
                -webkit-box-shadow: 3px 3px 3px 0 #e3e3e3;
                -moz-box-shadow: 3px 3px 3px 0 #e3e3e3;
                box-shadow: 3px 3px 3px 0 #e3e3e3;
                border: 1px solid #c3c3c3;
                height: 100px;
                margin: 6px 0 0 6px;
            }
    </style>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/angular-file-upload.min.js') }}"></script>
@stop
@section('content')
    <script type="text/javascript">
        app = angular.module("sahkari", ['angularFileUpload']);
        app.controller("photosCtrl", function(FileUploader,$scope,$http,$filter) {
            $scope.album;
            $scope.photos = [];
            $scope.album_id = {{$album_id}};
            $scope.offset = 0;
            $scope.itemsPerPage = 4;
            $scope.disable_more = 0;

            $scope.search='';
            
            $scope.getAlbum = function() {
                
               var request_url = generateUrl('album/'+$scope.album_id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.album = result.response.data;
//                    $scope.album.created_at = new Date( $scope.album.created_at); // Converting to UTC date
                    $scope.album.created_at = moment($scope.album.created_at).toDate();
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            
            $scope.getAlbum();
   
            $scope.getPhotos = function(offset,limit,search) {
               var options = {offset:offset,limit:limit};
               if(search){
                   options['search']=search;
               }
               var request_url = generateUrl('album/photos/'+$scope.album_id,options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
//                    $scope.photos = result.response.data;
                    var photos = [];
                    photos = result.response.data;
                    if(photos.length > 0){
                        for(var i = 0; i < photos.length; i++) {
                            $scope.photos.push(photos[i]);
                        }
                        $scope.disable_more = 1;
                    }else{
                        $scope.disable_more = 0;
                    }
                    
                    
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.getPhotos($scope.offset,$scope.itemsPerPage);
            
            $scope.getMorePhotos = function(){
               $scope.offset = $scope.offset+ $scope.itemsPerPage;
               $scope.getPhotos($scope.offset,$scope.itemsPerPage); 
            };
            
            $scope.openDeleteModal = function(album_id){
                $('#delete-id').val(album_id);
                $('#imageDeleteModal').modal();
            };
            
            $scope.currentSliderImg;
            $scope.currentIndex = 0; // Initially the index is at the first image
            $scope.showSlider = function(img_src,index){
               $scope.currentIndex = index;
               $scope.currentSliderImg = img_src;
                $('#imageSliderModal').modal();
            };
            
            

            $scope.next = function() {
              $scope.currentIndex < $scope.photos.length - 1 ? $scope.currentIndex++ : $scope.currentIndex = 0;
            };

            $scope.prev = function() {
              $scope.currentIndex > 0 ? $scope.currentIndex-- : $scope.currentIndex = $scope.photos.length - 1;
              
            };
            
            $scope.$watch('currentIndex', function() {
                if($scope.photos.length > 0){
                    $scope.currentSliderImg = $scope.photos[$scope.currentIndex].http_path;
                }
            });
            
            $('#image-delete-form').submit(function(e){
                e.preventDefault();
                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('album/photo/delete');
                $http({
                    url: request_url,
                    method: "POST",
                    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .then(function(response) {
                    if(response.data.response.success){
                        $scope.offset = 0;
                        $scope.photos = [];
                         $scope.getPhotos($scope.offset,$scope.itemsPerPage);
                        $("#image-delete-form")[0].reset();
                        grit('','Photo deleted successfully!');
                        $('#imageDeleteModal').modal('hide');
                    }else{
                            
                    }
                    
                }, 
                function(response) { // optional
//                       alert("fail");
                });
            });
          
            $scope.formatDateTime = function(date,time){
                var dateArray = date.split("-");
                if(time){
                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
               
                    return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
                }else{
                   var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
               
                   return $filter('date')(dateUTC, 'yyyy-MM-dd'); 
                }
                
            };
            
            $scope.formatDateTime = function(date_time){
                var dateUTC =  new Date(date_time);
                return $filter('date')(dateUTC, 'MMM d yyyy'); 
            };
            
            var options = {folder_id:$scope.album_id};     
            var request_url = generateUrl('album/upload',options);   
            var uploader = $scope.uploader = new FileUploader({
                
                url: request_url
            });

            // FILTERS

            uploader.filters.push({
                name: 'imageFilter',
                fn: function(item /*{File|FileLikeObject}*/, options) {
                    var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                    return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
                }
            });

            // CALLBACKS

            uploader.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
//                console.info('onWhenAddingFileFailed', item, filter, options);
            };
            uploader.onAfterAddingFile = function(fileItem) {
//                console.info('onAfterAddingFile', fileItem);
            };
            uploader.onAfterAddingAll = function(addedFileItems) {
//                console.info('onAfterAddingAll', addedFileItems);
            };
            uploader.onBeforeUploadItem = function(item) {
//                console.info('onBeforeUploadItem', item);
            };
            uploader.onProgressItem = function(fileItem, progress) {
//                console.info('onProgressItem', fileItem, progress);
            };
            uploader.onProgressAll = function(progress) {
//                console.info('onProgressAll', progress);
            };
            uploader.onSuccessItem = function(fileItem, response, status, headers) {
//                console.info('onSuccessItem', fileItem, response, status, headers);
            };
            uploader.onErrorItem = function(fileItem, response, status, headers) {
//                console.info('onErrorItem', fileItem, response, status, headers);
            };
            uploader.onCancelItem = function(fileItem, response, status, headers) {
//                console.info('onCancelItem', fileItem, response, status, headers);
            };
            uploader.onCompleteItem = function(fileItem, response, status, headers) {
//                console.info('onCompleteItem', fileItem, response, status, headers);
            };
            uploader.onCompleteAll = function() {
//                console.info('onCompleteAll');
                uploader.clearQueue();
                // Reset photos and offset
                $scope.photos = [];
                $scope.offset = 0;
                $scope.getPhotos($scope.offset,$scope.itemsPerPage);
                grit('','Photos uploaded successfully!');
                $('a[href="#view_photos"]').tab('show');
            };

            console.info('uploader', uploader);
        });
        
        app.directive('ngThumb', ['$window', function($window) {
            var helper = {
                support: !!($window.FileReader && $window.CanvasRenderingContext2D),
                isFile: function(item) {
                    return angular.isObject(item) && item instanceof $window.File;
                },
                isImage: function(file) {
                    var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                    return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
                }
            };

            return {
                restrict: 'A',
                template: '<canvas/>',
                link: function(scope, element, attributes) {
                    if (!helper.support) return;

                    var params = scope.$eval(attributes.ngThumb);

                    if (!helper.isFile(params.file)) return;
                    if (!helper.isImage(params.file)) return;

                    var canvas = element.find('canvas');
                    var reader = new FileReader();

                    reader.onload = onLoadFile;
                    reader.readAsDataURL(params.file);

                    function onLoadFile(event) {
                        var img = new Image();
                        img.onload = onLoadImage;
                        img.src = event.target.result;
                    }

                    function onLoadImage() {
                        var width = params.width || this.width / this.height * params.height;
                        var height = params.height || this.height / this.width * params.width;
                        canvas.attr({ width: width, height: height });
                        canvas[0].getContext('2d').drawImage(this, 0, 0, width, height);
                    }
                }
            };
        }]);
    </script>
    <div class="col-md-12" ng-controller="photosCtrl" nv-file-drop="" uploader="uploader">
        <div class="row form-group">
                <div class="col-md-12">
                    <a class="btn btn-primary" href="{{ route('albums') }}" ><< Back to albums</a>
                </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p><strong>Name:</strong> @{{album.name}}</p>
                <p><strong>Description:</strong> @{{album.description}}</p>
                <p><strong>Created by:</strong> @{{album.first_name}} @{{album.last_name}}, @{{album.created_at | date:'dd-MMM-yyyy'}}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                 <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                  <li role="presentation" class="active"><a href="#view_photos" aria-controls="home" role="tab" data-toggle="tab">Photos</a></li>
                  <li role="presentation"><a href="#upload_photos" aria-controls="profile" role="tab" data-toggle="tab">Upload Photos</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="view_photos">
                      <div class="row">
                        <div class="col-xs-6 col-md-3 album_photo" ng-repeat="(index,photo) in photos">
                            <a href='javascript:void(0);' ng-show="({{Session::get('user.user_id')}} == photo.user_id )  ? 1 : 0" class="glyphicon glyphicon-remove remove_icon" ng-click="openDeleteModal(photo.id)" aria-hidden="true"></a>
                            <a href="" class="thumbnail" ng-click="showSlider(photo.http_path,index)">
                              <img src="@{{photo.http_path}}" alt="@{{photo.name}}">
                            </a>
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-md-12 text-center">
                              <button type="button" class="btn btn-primary" ng-show='disable_more' ng-click="getMorePhotos()">Load More</button>
                              <span class="label label-default" ng-show='!disable_more'>No photos to load....</span>
                          </div>
                      </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="upload_photos">
                    <div ng-show="uploader.isHTML5">
                        <!-- 3. nv-file-over uploader="link" over-class="className" -->
                        <div class="well my-drop-zone" nv-file-over="" uploader="uploader">
                            Drag and Drop Files
                        </div>

                        
                    </div>

                    <!-- Example: nv-file-select="" uploader="{Object}" options="{Object}" filters="{String}" -->
                    <input type="file" nv-file-select="" uploader="uploader" multiple  /><br/>
                    
                        <h3>The queue</h3>
                        <p>Queue length: @{{ uploader.queue.length }}</p>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="50%">Name</th>
                                    <th ng-show="uploader.isHTML5">Size</th>
                                    <th ng-show="uploader.isHTML5">Progress</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in uploader.queue">
                                    <td>
                                        <strong>@{{ item.file.name }}</strong>
                                        <!-- Image preview -->
                                        <!--auto height-->
                                        <!--<div ng-thumb="{ file: item.file, width: 100 }"></div>-->
                                        <!--auto width-->
                                        <div ng-show="uploader.isHTML5" ng-thumb="{ file: item._file, height: 100 }"></div>
                                        <!--fixed width and height -->
                                        <!--<div ng-thumb="{ file: item.file, width: 100, height: 100 }"></div>-->
                                    </td>
                                    <td ng-show="uploader.isHTML5" nowrap>@{{ item.file.size/1024/1024|number:2 }} MB</td>
                                    <td ng-show="uploader.isHTML5">
                                        <div class="progress" style="margin-bottom: 0;">
                                            <div class="progress-bar" role="progressbar" ng-style="{ 'width': item.progress + '%' }"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span ng-show="item.isSuccess"><i class="glyphicon glyphicon-ok"></i></span>
                                        <span ng-show="item.isCancel"><i class="glyphicon glyphicon-ban-circle"></i></span>
                                        <span ng-show="item.isError"><i class="glyphicon glyphicon-remove"></i></span>
                                    </td>
                                    <td nowrap>
    <!--                                    <button type="button" class="btn btn-success btn-xs" ng-click="item.upload()" ng-disabled="item.isReady || item.isUploading || item.isSuccess">
                                            <span class="glyphicon glyphicon-upload"></span> Upload
                                        </button>-->
    <!--                                    <button type="button" class="btn btn-warning btn-xs" ng-click="item.cancel()" ng-disabled="!item.isUploading">
                                            <span class="glyphicon glyphicon-ban-circle"></span> Cancel
                                        </button>-->
                                        <button type="button" class="btn btn-primary btn-xs" ng-click="item.remove()">
                                            <span class="glyphicon glyphicon-trash"></span> Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div>
                            <div>
                                Queue progress:
                                <div class="progress" style="">
                                    <div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.progress + '%' }"></div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-s" ng-click="uploader.uploadAll()" ng-disabled="!uploader.getNotUploadedItems().length">
                                <span class="glyphicon glyphicon-upload"></span> Upload all
                            </button>
<!--                            <button type="button" class="btn btn-warning btn-s" ng-click="uploader.cancelAll()" ng-disabled="!uploader.isUploading">
                                <span class="glyphicon glyphicon-ban-circle"></span> Cancel all
                            </button>-->
                            <button type="button" class="btn btn-primary btn-s" ng-click="uploader.clearQueue()" ng-disabled="!uploader.queue.length">
                                <span class="glyphicon glyphicon-trash"></span> Remove all
                            </button>
                        </div>

                  </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="imageDeleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id='image-delete-form' action='' method="POST">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Delete Photo</h4>
                        </div>
                        <div class="modal-body">
                            <p>Do you want to delete this photo?</p>
                            <input type='hidden' id='delete-id' name='id' value="" />
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-primary">Delete</button>
                        </div>
                    </form> 
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="imageSliderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content slider">
                    <a href='javascript:void(0);' data-dismiss="modal" class='close-btn'><span class='glyphicon glyphicon-remove'  aria-hidden="true" ></span></a>
                    <div class="slide">
                        <img ng-src="@{{currentSliderImg}}" class="img-responsive">
                    </div>
                    
                    <div class="arrows">
                        <a href="" ng-click="prev()" class='pull-left'> 
                          <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        </a>
                        <a href="" ng-click="next()" class='pull-right'>
                          <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('document').ready(function(){
            
        });
    </script>
@stop
