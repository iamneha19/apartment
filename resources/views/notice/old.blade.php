@section('title', 'Old Notices')
@section('panel_title', 'Old Notices')
@section('head')
<script src="{{ asset('js/moment.js') }}"></script>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("NoticeCtrl", function(paginationServices,$scope,$http,$filter) {
            $scope.notices;
            $scope.type = '1';
            $scope.pagination = paginationServices.getNew(3);
            $scope.itemsPerPage = 3;

            $scope.search='';
   
            $scope.getNotices = function(type,offset,limit,search) {
               var options = {type:type,offset:offset,limit:limit};
               if(search){
                   options['search']=search;
               }
               var request_url = generateUrl('notice/expired',options);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.notices = result.response.data;
                    
                    $scope.pagination.total = result.response.total;
                    $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };

            $scope.$on('pagination:updated', function(event,data) {
              $scope.getNotices($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.search);
            });

            $scope.$watch('search', function(newValue, oldValue) {
                if(newValue){
                    console.log(newValue);
                    $scope.pagination.total=0;
                    $scope.pagination.offset = 0;
                    $scope.pagination.currentPage = 1;

                    $scope.pagination.setPage(1);
                }else{
                    $scope.pagination.setPage(1);
                }

            });


            $scope.tab = function(type) {
                $scope.type = type;

                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.getNotices($scope.type,$scope.pagination.offset,$scope.pagination.itemsPerPage);
            };
            
            
            
            $scope.textFormat = function(text,notice_id){
               var text =  text.replace(/(<([^>]+)>)/ig,"");   // To strip html tags
              
               var notice_url = '<?php echo route('notice','');  ?>';
               notice_url = notice_url.slice(0,-1);
                var shortText = jQuery.trim(text).substring(0, 200)
                          .trim(this) + '...'+'<a href="'+notice_url+'/'+notice_id+'">Read More</a>'; 
                return shortText;
            };
            
            $scope.formatDateTime = function(date_time){
//                var dateUTC =  new Date(date_time);
                var dateUTC = moment(date_time).toDate(); // to handle cross-browser 
                return $filter('date')(dateUTC, 'd MMM yyyy'); 
            };
            

            
        });
    </script>
    <div class="col-lg-12" ng-controller="NoticeCtrl" >
        <div class="row form-group">
            <div class="col-lg-12">
                <label>Search: <input ng-model="search"></label>
                <div class="btn-toolbar pull-left" style="padding:7px;">
                   <a class="btn btn-primary" href="{{ route('notice') }}" ><< Back to notices</a>
                </div>
            </div>
        </div>
         <div class="row">
             <div class="col-lg-12">
                <ul class="nav nav-tabs">
                        <li  ng-class="{active: type === '1'}"><a href="" ng-click="tab('1')">Admin Notices</a></li>
                        <li  ng-class="{active: type === '2'}"><a  href="" ng-click="tab('2')">General Notices</a></li>
                        <li  ng-class="{active: type === '3'}"><a href="" ng-click="tab('3')">Buy/Sell Notices</a></li>
                </ul>
             </div>
         </div>
<!--        <div class="row">
            <div class="col-lg-4" ng-repeat="notice in notices" ng-show="( notice.status == 1 || {{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">@{{notice.title}}</h3>
                    </div>
                    <div class="panel-body" >
                        <span class='pull-right label label-default' ng-bind-html="formatDateTime(notice.created_at)"></span>
                        <div ng-bind-html="textFormat(notice.text,notice.id)" style="clear:both;word-wrap: break-word;"></div>
                    </div>
                    <div class="panel-footer">
                        <span class="label label-primary" ng-show="(notice.status == 1)  ? 1 : 0">Published</span>
                        <span class="label label-warning" ng-show="(notice.status == 0)  ? 1 : 0">Draft</span>
                        <span class="label label-info pull-right">Expired : @{{formatDateTime(notice.expiry_date)}}</span>
                    </div>
                </div>
            </div>
        </div>-->
        <!----->
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>
                                <a href="" ng-click="order('title')">Title</a>
                                <span class="sortorder" ng-show="predicate === 'title'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('description')">Description</a>
                                <span class="sortorder" ng-show="predicate === 'description'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('created_at')">Uploaded On</a>
                                <span class="sortorder" ng-show="predicate === 'created_at'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('status')">Status</a>
                                <span class="sortorder" ng-show="predicate === 'status'" ng-class="{reverse:reverse}"></span>
                            </th>
                            <th>
                                <a href="" ng-click="order('expired_on')">Expired On</a>
                                <span class="sortorder" ng-show="predicate === 'expired_on'" ng-class="{reverse:reverse}"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-if="pagination.total == 0">
                            <td colspan="7" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
                        </tr>
                        <tr ng-if="pagination.total > 0" ng-repeat="notice in notices" ng-show="( notice.status == 1 || {{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0">
                            <td>@{{notice.title}}</td>
                            <td ng-bind-html="textFormat(notice.text,notice.id)" style="clear:both;word-wrap: break-word;"></td>
                            <td>@{{notice.created_at}}</td>
                            <!--<td ng-show="({{Session::get('user.user_id')}} == notice.user_id )  ? 1 : 0">-->
                            <td ng-show="(notice.status == 1)  ? 1 : 0">Published</td>
                            <td ng-show="(notice.status == 0)  ? 1 : 0">Draft</td>
                            <td>@{{formatDateTime(notice.expiry_date)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--->
        
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
    <script>
        $('document').ready(function(){
           
        });
    </script>
@stop
