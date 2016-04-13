@section('title', 'Albums')
@section('panel_title', 'Photo Gallery')
@section('head')
<script src="{{ asset('js/moment.js') }}"></script>
<style>
    .album_list .thumbnail img{
        max-height: 177px;
        min-height: 177px; 
    }
            
</style>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("albumCtrl", function(paginationServices,$scope,$http,$filter) {
            $scope.albums;
            $scope.pagination = paginationServices.getNew(4);
            $scope.itemsPerPage = 4;

            $scope.search='';
            $('#loader').hide();
             $('#loader1').hide();
            $scope.getAlbums = function(offset,limit,search) {
               var options = {offset:offset,limit:limit};
               if(search){
                   options['search']=search;
               }
               var request_url = generateUrl('album/list',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.albums = result.response.data;
                    $scope.pagination.total = result.response.total;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
					if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.$on('pagination:updated', function(event,data) {
              $scope.getAlbums($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
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

//            $scope.formatDateTime = function(date,time){
//                var dateArray = date.split("-");
//                if(time){
////                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
//                    var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time).toDate();
//               
//                    return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
//                }else{
////                   var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
//                   var dateUTC = moment(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]).toDate();
//                   return $filter('date')(dateUTC, 'yyyy-MM-dd'); 
//                }
//                
//            };
            
            $scope.textFormat = function(text,notice_id){
               var text =  text.replace(/(<([^>]+)>)/ig,"");   // To strip html tags
               var notice_url = '<?php echo url('notice/');  ?>';
                var shortText = jQuery.trim(text).substring(0, 50)
                          .trim(this) + '...'+'<a href="'+notice_url+'/'+notice_id+'">Read More</a>'; 
                return shortText;
            };
            
            $scope.formatDateTime = function(date_time){
//                var dateUTC =  new Date(date_time);
                 var dateUTC = moment(date_time).toDate(); // to handle cross-browser 
                return $filter('date')(dateUTC, 'd MMM yyyy'); 
            };

            $scope.openCreateForm = function(){
                $('#albumCreateModal').modal();
            };
            
             $scope.closeForm = function(){
                $("#album-create-form")[0].reset();
                $("#album-create-form label.error").remove();
                $('#albumCreateModal').modal('hide');
            };
             
              $scope.closeupdateForm = function(){
                $("#album-update-form")[0].reset();
                $("#album-update-form label.error").remove();
                $('#albumEditModal').modal('hide');
            };
            $('#album-create-form').submit(function(e){
                e.preventDefault();
               
                if ($(this).valid()){
                    $('#loader').show();
                     $('#loader1').show();
                     $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Creating album please wait..'); 
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('album/create');
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                        $('#loader').hide();
                        $('#loader1').hide();
                        var result = response.data.response; // to get api result
                        $('#album-create-form').find('button[type=submit]').attr('disabled',false);
                        $('#album-create-form').find('button[type=submit]').text('Submit');
                        if(result.success){
                            $scope.pagination.total=0;
                            $scope.pagination.offset = 0;
                            $scope.pagination.currentPage = 1;
                            $scope.pagination.setPage(1);
                            $scope.closeForm();
                            grit('','Album created successfully!');
                        }else{
                           $( '#album-create-form input[name="name"]' ).after( '<label id="name-error" class="error" for="name">'+result.msg+'</label>' ); 
                        }
                    }, 
                    function(response) { // optional
//                           alert("fail");
                    });
                }
                
            });
           
            $scope.edit_id;
            $scope.edit_album_name;
            $scope.edit_album_desc;
            
            $scope.openEditForm = function(album_id,album_name,album_desc){
                $scope.edit_album_name = album_name;
                $scope.edit_id = album_id;
                 $scope.edit_album_desc = album_desc;
                $('#albumEditModal').modal();
            };
            
            $scope.openDeleteModal = function(album_id){
                $('#delete-id').val(album_id);
                $('#albumDeleteModal').modal();
            };
            
            $('#album-delete-form').submit(function(e){
                e.preventDefault();
                var records = $.param($( this ).serializeArray());
                var request_url = generateUrl('album/delete');
                $http({
                    url: request_url,
                    method: "POST",
                    data: records,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .then(function(response) {
                    if(response.data.response.success){
                        $scope.pagination.total=0;
                        $scope.pagination.offset = 0;
                        $scope.pagination.currentPage = 1;
                        $scope.pagination.setPage(1);

                        $("#album-delete-form")[0].reset();
                        grit('','Album deleted successfully!');
                        $('#albumDeleteModal').modal('hide');
                    }else{
                            
                    }
                    
                }, 
                function(response) { // optional
//                       alert("fail");
                });
            });    
            
            $('#album-update-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid() && $scope.edit_id){
                     $('#loader1').show();
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('album/update/'+$scope.edit_id);
                    $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                    .then(function(response) {
                         $('#loader1').hide();
                        var result = response.data.response; // to get api result 
                        if(result.success){
                            $scope.pagination.total=0;
                            $scope.pagination.offset = 0;
                            $scope.pagination.currentPage = 1;
                            $scope.pagination.setPage(1);

                            $("#album-update-form")[0].reset();
                            grit('','Album updated successfully!');
                           $('#albumEditModal').modal('hide');
                        }else{
                            $( '#album-update-form input[name="name"]' ).after( '<label id="name-error" class="error" for="name">'+result.msg+'</label>' ); 
                        }
                    }, 
                    function(response) { // optional
//                           alert("fail");
                    });
                }
                
            });
        });
    </script>
    <div class="col-lg-12" ng-controller="albumCtrl" >
        <div class="row form-group">
            <div class="col-lg-12">
                <input ng-model="search" class="form-control ng-pristine ng-untouched ng-valid"  placeholder="Search" style="width: 200px;display: inline">
                <button type="button" class="btn btn-primary pull-right" ng-click="openCreateForm()">Create Album</button>
            </div>
        </div>
         
<!--        <div class="row album_list">
			<div ng-if="pagination.total == 0" style="margin-left: 20px;">
				<span  style="font-weight: bold;" id="dataCheck">Fetching Data...</span>
            </div>
            <div ng-if="pagination.total > 0" class="col-sm-6 col-md-3" ng-repeat="album in albums">
            <div class="thumbnail" style="
					  border-radius: 23px;
					  border-width: 6px;
					  text-align: center;
				 ">
              <a href="{{ route('album.photos','') }}/@{{album.id}}">
              <img class="img-responsive" ng-show='(album.image_url) ? 1 : 0' src="@{{album.image_url}}" alt="@{{album.image_name}}">    
              <img class="img-responsive" ng-show='(album.image_url) ? 0 : 1' src="https://placeholdit.imgix.net/~text?txtsize=19&bg=efefef&txtclr=aaaaaa%26text%3Dno%2Bimage&txt=no+image&w=338&h=200" alt="...">
              </a>
              <div class="caption">
                <h4 style="overflow: hidden;text-overflow: ellipsis;" title="@{{album.name}}">@{{album.name}}</h4>
                <h6>@{{formatDateTime(album.created_at)}}</h6>
                <div class="" role="toolbar"  style="text-align: center;" >
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-primary" href="{{ route('album.photos','') }}/@{{album.id}}"  role="button">View Photos</a>
                        </div>
                        <div class="btn-group btn-group-sm" ng-show="({{Session::get('user.user_id')}} == album.user_id )  ? 1 : 0">
                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu btn-group-xs">
                                <li><a href="" ng-click="openEditForm(album.id,album.name,album.description)" title="Edit"><i class="fa fa-pencil" style="margin-right: 5px;"></i>Edit</a></li>
                                <li><a href="" ng-click="openDeleteModal(album.id)" title="Delete" ><i class="fa fa-remove" style="margin-right: 5px;"></i>Delete</a></li>
                          </ul>
                        </div>
                    
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="#" class="btn btn-success" role="button" ng-click="openEditForm(album.id,album.name,album.description)">Edit</a> 
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="#" id='album-delete-btn' ng-click="openDeleteModal(album.id)" class="btn btn-default" role="button" >Delete</a>
                    </div>
                </div>
                
              </div>
            </div>
          </div>
        </div>
        -->
        
        <div class="row">
            <div class="col-lg-12">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th><a href="#" >
                            Sr No.
                        </a></th>
                        <th>
                            <a href="" ng-click="order('name')">Name</a>
                            <span class="sortorder" ng-show="predicate === 'name'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('description')">Description</a>
                            <span class="sortorder" ng-show="predicate === 'description'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th>
                            <a href="" ng-click="order('created_at')">Created At</a>
                            <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                        </th>
                        <th><a href="#" >Action</a></th>
                    </tr>
                </thead>
                <tbody>
					<tr ng-if="pagination.total == 0">
                        <td colspan="7" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                    </tr>
                    <tr ng-if="pagination.total > 0" ng-repeat="album in albums">
                        <td>@{{album.id}}</td>
                        <td>@{{album.name}}</td>
                         <td>@{{ ((album.description | limitTo : 50) + '...') }}</td>
                        <td>@{{album.created_at}}</td>
                        <td>
                            <a class="glyphicon glyphicon-eye-open" title="view photos" href="{{ route('album.photos','') }}/@{{album.id}}"  role="button"></a>
                            <a href="#" class="glyphicon glyphicon-pencil" title="update" role="button" ng-click="openEditForm(album.id,album.name,album.description)"></a> 
                            <a href="#" id='album-delete-btn' ng-click="openDeleteModal(album.id)" class="glyphicon glyphicon-remove" title="delete" role="button" ></a>
                        </td>
                        
<!--                        <td>
                            <div ng-show="(({{Session::get('user.user_id')}} == tasks.assign_to) ||({{Session::get('user.user_id')}} == tasks.created_by))  ? 1 : 0">
                                <a class="glyphicon glyphicon-pencil" title="update" href="{{route('admin.taskupdate','')}}/@{{tasks.id}}"></a>
                             </div>
                        </td>-->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
        
        <div ng-if="pagination.total > 0" class="row">
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
        <div class="modal fade" id="albumCreateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" ng-click="closeForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create Album</h4>
                  </div>
                  <div class="modal-body">
                    <form id="album-create-form" method="post" action="">
                        <div class="form-group">
                          <label class="form-label">Name</label>
                          <input type="text" class="form-control" name="name" maxlength="50"   placeholder="Name">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Description</label>
                          <textarea class="form-control" name="description" placeholder="Description"></textarea>
                        </div>
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button class="btn btn-primary" type="button" ng-click="closeForm()">Cancel</button>
                    </form>
                      <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                        <div id="loader" class="loading">Loading&#8230;</div>
                  </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="albumEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="closeupdateForm()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Album</h4>
                  </div>
                  <div class="modal-body">
                    <form id="album-update-form" method="post" action="">
                        <div class="form-group">
                          <label class="form-label">Name</label>
                          <input type="text" class="form-control" name="name" value="@{{edit_album_name}}"   placeholder="Name">
                        </div>
                        <div class="form-group">
                          <label class="form-label">Description</label>
                          <textarea class="form-control" name="description" placeholder="Description">@{{edit_album_desc}}</textarea>
                        </div>
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <button class="btn btn-primary" type="button" ng-click="closeupdateForm()" data-dismiss="modal">Cancel</button>
                    </form>
                      <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                        <div id="loader1" class="loading">Loading&#8230;</div>
                  </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="albumDeleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id='album-delete-form' action='' method="POST">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Delete Album</h4>
                        </div>
                        <div class="modal-body">
                            <p>Do you want to delete this album?</p>
                            <input type='hidden' id='delete-id' name='id' value="" />
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-primary">Delete</button>
                        </div>
                    </form> 
                </div>
            </div>
        </div>
    </div>
    <script>
        $('document').ready(function(){
            $(':input[type="text"]').change(function() {
                $(this).val($(this).val().trim());
            });
             $('textarea').change(function() {
                $(this).val($(this).val().trim());
            });
            $("#album-create-form").validate({ 
                rules: {
                    name: "required",
                    description: "required"
                }
            });
            $("#album-update-form").validate({ 
                rules: {
                    name: "required",
                    description: "required"
                }
            });
        });
    </script>
@stop
