@section('title', 'Societies List')
@section('panel_title','Societies')
@section('panel_subtitle','List')
@section('content')
    <script>
    app.controller("SocietyCtrl", function(URL,paginationServices,$scope,$http) {

        $scope.societies;
        $scope.modules;
        
        $scope.pagination = paginationServices.getNew(5);
        $scope.itemsPerPage = 10;
        $scope.sort = 'name';
        $scope.sort_order = 'desc';
        $scope.search='';
        $scope.admins;
        $scope.activeSociety ;
        
   
        $scope.getSocities = function(offset,limit,sort,sort_order,search) {
            var options = {offset:offset,limit:limit,sort:sort,sort_order:sort_order};
             if(search)
                {
                    options['search'] = search;
                }
            var request_url = generateUrl('superadmin/list/society',options);
            $http.get(request_url)
            .success(function(result, status, headers, config) {
                $scope.societies = result.response.data;
                $scope.pagination.total = result.response.total;
                $scope.pagination.pageCount = Math.ceil($scope.pagination.total/$scope.pagination.itemsPerPage);
				if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
            }).error(function(data, status, headers, config) {
                console.log(data);
            });
        };
        
            

        $scope.$on('pagination:updated', function(event,data) {
            console.log($scope.status);
            $scope.getSocities($scope.pagination.offset,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order,$scope.search);
        });

        $scope.$watch('search', function(newValue, oldValue) {
            if(newValue){
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'name';
                $scope.sort_order = 'desc';
                $scope.pagination.setPage(1);
            }else{
                $scope.pagination.setPage(1);
            }
        });

        $scope.order = function(predicate) {
            $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
            $scope.predicate = predicate;
            $scope.pagination.total=0;
            $scope.pagination.offset = 0;
            $scope.pagination.currentPage = 1;
            $scope.sort = predicate;
            $scope.sort_order = ($scope.reverse) ? 'asc' : 'desc';
            $scope.pagination.setPage(1);
        };
       $scope.showResources = function(){
           console.log(this);
           $scope.activeSociety = this.socity;
           $http.get(generateUrl('acl/resource/list') +'&userId='+this.socity.user_id +'&societyId='+this.socity.society_id)
           .then(function(r){
		$scope.activeSociety.modules = r.data.response;
	})
           $('#myModal').modal('show');
           
          }
       $scope.moduleChange = function() {
           this.disable = true;
           var $this = this;
            $http.post(generateUrl('superadmin/user/update_module_access'),{ permitted:this.module.permitted,acl_name:this.module.acl_name,user_id:$scope.activeSociety.user_id,society_id:$scope.activeSociety.society_id })
             .then(function(response) {
                 $this.disable = false;
               grit('',response.data.response.msg);
            });
       }
    
     });
    </script>
<!--    <style>
    #error
    {
    color:red;
    }
    </style>-->
    <div class="col-lg-12" ng-controller="SocietyCtrl">
        <div class="row" style="height: 50px;">
            <div class="col-lg-12">
                <input ng-model='search' class="form-control" placeholder="Search" style="width: 300px;">
                
            </div>
        </div>
        
        <div class="row">
            <div class = "col-lg-12">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            
                            <th>
                                Name
                                <span class="sortorder" ng-show="predicate === 'first_name'" ng-class="{reverse:reverse}"></span>
                            </th>
                             <th>
                               Type
                            </th>
                            
                            <th>
                                Pincode  
                            </th>
                            
                            <th>
                                Created On
                            </th>
                            <th>
                                Admin
                            </th>
<!--                            <th>
                               
                            </th>-->
                            
                        </tr>
                    </thead>
                    <tbody>
						<tr ng-if="pagination.total == 0">
							<td colspan="3" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
						</tr>
                        <tr ng-if="pagination.total > 0" ng-repeat="socity in societies">
                            
                            <td>@{{socity.name}}</td>
                            <td>@{{socity.type}}</td>
                            <td>@{{socity.pincode}}</td>
                            <td>@{{socity.created_at}}</td>
                            <td>
                                <a href="#" ng-click= "showResources()" > @{{socity.first_name }} @{{ socity.last_name}} </a>
                                </td>
                            </td>
<!--                            <td> 
                                <a class="glyphicon glyphicon-pencil" title="update" href=""></a>
                            </td>-->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Model -->
        <div id="myModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Module Access</h4>
                    </div>
                    <div class="modal-body">
                    <div class="col-md-12" ng-if="activeSociety.modules == null"><strong>Loading..</strong></div>
                    <div class="checkbox">
                        <ul ng-repeat="module in activeSociety.modules">
                             
                            <li>
                                <input type="checkbox" ng-model="module.permitted" ng-change="moduleChange()" ng-true-value="1" ng-false-value="0" ng-checked="module.permitted == 1" ng-disabled="disable">
                                    @{{module.title}}
                            </li>
                        </ul>
                      
                        
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
                    </div>
                   
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!--pagination--->
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
        
    </div>
    <script>
        $('document').ready(function(){
            
        });
    </script>
@stop
