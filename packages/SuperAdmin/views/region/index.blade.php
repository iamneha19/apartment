@section('title', 'Region')
@section('panel_title','Region')
@section('panel_subtitle', 'List')
@section('content')
<script>
    app.controller('regionController',function($http,paginationServices,$scope,$filter)
    { 
        $scope.getStates = function() {
            $http.get(generateUrl('v1/states?per_page=unlimited&orderby=ASC'))
            .then(function(response) {
              $scope.states = response.data.results;
            });
        }
        
        $scope.getStateDivision = function() {
            var $this = this;
            console.log(this);
            $http.get(generateUrl('v1/states/div/'+$this.region.state_id))
            .then(function(response) {
              $scope.divisions = response.data.results;
            });
        }
        
        $scope.Region;
        $scope.duplicateDivision= null;
        $scope.isDisabled = false;
        $scope.itemsPerPage = 2;
        $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
        
        $scope.getDivision = function() {
            $http.get(generateUrl('v1/division?per_page=unlimited&orderby=ASC'))
            .then(function(response) {
                  $scope.divisions = response.data;
           });
        }
        
        $scope.getRegion = function(page,search) {
            var options = {page:page,per_page:$scope.itemsPerPage};
            $scope.pagination.currentPage = page;
            if(search) {
                options['search']=search;
            }
            
            $http.get(generateUrl('v1/region',options))
            .then(function(response) {  
                $scope.regions = response.data.data;
                $scope.pagination.total = response.data.total;
                $scope.pagination.pageCount = response.data.last_page;
                $scope.pagination.currentPage = response.data.current_page;
                if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
           });
        }
        
         $scope.getRegion($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
        
         $scope.$on('pagination:updated', function(event,data) {
            $scope.getRegion($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
        });
        
        $scope.$watch('search', function(newValue, oldValue) {
            if(newValue){
                $scope.pagination.total=0;
                $scope.pagination.offset = 0;
                $scope.pagination.currentPage = 1;
                $scope.sort = 'state';
                $scope.sort_order = 'asc';

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
        
        $scope.addRegion = function() {
           $('#addRegionModal').modal('show');
           $scope.getStates();
           
       }
       
       $scope.close = function() {
           $("#region_form")[0].reset();
            $scope.region = {};
            $scope.region_form.$setPristine();
            $scope.duplicateDivision = null;
       }
       
       $scope.edit = function() {
            $('#addRegionModal').modal('show');
             $scope.getStates();
            $scope.getDivision();
            
            $http.get(generateUrl('v1/region/edit/'+this.region.id,{division_id:this.region.division_id}))
            .then(function(response) {
                $scope.region = response.data.results;
           });
       }
       
       $scope.submitForm = function() {
            if (this.region_form.$invalid)
           return;
        var $this = this;
        $this.disable = true;
        $http.post(generateUrl('v1/region/save'),$scope.region)
        .then(function(response) {
             if (response.data.status == "success") {
                $('#addRegionModal').modal('hide');                
                $scope.getRegion($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
                $scope.duplicateDivision = null;
                $scope.close();
            } else {
                 $scope.duplicateDivision = response.data.message;
            }
             $this.disable = false;
       });
       }
       
       $scope.delete = function() {
           console.log(this);
        var r = confirm("Deleted Region cannot be retrieved");
        if (r == true) {
            var options = {division_id:this.region.division_id,state_id:this.region.state_id};
            var $this = this;
            $http.get(generateUrl('v1/region/delete/'+this.region.id,options))
            .then(function(r){
               grit('',r.data.msg);
                if(r.data.success) {
                    $scope.pagination.currentPage = 1;
                   $scope.getRegion($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
               }
            });
        } else {
            return ;
        }
       }
       
       $scope.duplicate = function() {
        console.log(this);
            $http.post(generateUrl('v1/region/check'),$scope.region)
            .then(function(response) {
                 if (response.data.status == "validation_failed") {
                    $scope.duplicateDivision = response.data.message;

                 }
                else {
                    $scope.duplicateDivision= null;
                }
            });
        }
        
        $scope.searchFile = function() {
            console.log(this);
            $http.get(generateUrl('v1/region')+'&search='+this.search)
            .then(function(r) {
                $scope.regions = r.data.results.data;
            });
        }
       
    });
</script>

<div class="col-md-12" ng-controller="regionController"> 
    <div class="col-lg-12" style= "height:50px;">
		<input ng-model='search' class="form-control" placeholder="Search" ng-change="searchFile()" style="width: 300px;float: left;margin-left: -15px">
        <div class="btn-toolbar pull-right">
            <button  id="buttonAdd" type="button" class="btn btn-primary " ng-click="addRegion()">Create Region</button> 
        </div>
    </div>
    <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Region</th>
                    <th>Division</th>
                    <th>State</th>
                    <th>Created at </th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
			<tr ng-if="pagination.total == 0">
				<td colspan="3" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
			</tr>	
            <tr ng-if="pagination.total > 0" ng-repeat="region in regions" class="edit" on-finish-render="ngRepeatFinished">
                <td>@{{region.name}}</td>
                <td>@{{region.division}}</td>
                <td>@{{region.state}}</td>
                <td>@{{region.created_at}}</td>
                
                <td>
                    <a class="glyphicon glyphicon-pencil" ng-click="edit()" href="" title="Update" ></a>
                    <a class="glyphicon glyphicon-remove" ng-click="delete() "href=""  title="Delete"></a>
                </td>
            </tr>
            </tbody>
        </table>
    <!--pagination-->   
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
    <!--add Region model-->
<div id="addRegionModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" ng-click="close()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Region</h4>
      </div>
      <div class="modal-body">
          <form id="region_form" name="region_form" ng-submit="submitForm()" novalidate >
              <div class="form-group" ng-hide="region.id != null">
                <label class="form-label" for="emailsubject">Select State</label> 
                <select	 ng-change="getStateDivision()" name="state" class="form-control"
                        ng-model="region.state_id"
                        ng-options="state.id as state.name for state in states" 
                        required>
                    <option value="" selected="">Select State</option>
                </select>
                
                <label class="error"
                    ng-show="region_form.$submitted && region_form.state.$invalid ">
                    This field is required
                </label>
            </div>
                <div class="form-group" ng-hide="region.id == null">
                   <label for="emailsubject">State</label> 
                     <input value="@{{region.state}}" class="form-control" selected="" disabled>
                </div>
            <div class="form-group" ng-hide="region.id != null">
                <label class="form-label" for="emailsubject">Select Division</label> 
                <select	 ng-change="duplicate()" name="division" class="form-control"
                        ng-model="region.division_id"
                        ng-options="division.id as division.name for division in divisions" 
                        required>
                    <option value="" selected="">Select Division </option>
                </select>
<!--                <select ng-model="region.division_id" name="division" class="form-control" required>
                    <option value="" selected="">Select division</option>
                    <option ng-repeat="division in divisions"  ng-selected = "division.id == region.division_id" value='@{{division.id}}'>@{{division.name}}</option>
                    
                 </select>-->
                <label class="error"
                    ng-show="region_form.$submitted && region_form.division.$invalid">
                    This field is required
                </label>
            </div>
              <div class="form-group" ng-hide="region.id == null">
                   <label for="emailsubject">Division</label> 
                     <input value="@{{region.division}}" class="form-control" selected="" disabled>
                </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">Region</label>
                <input  ng-model="region.name" type="text" ng-model-options="{debounce:1000}" ng-pattern="/^[A-Za-z\s]+$/" class="form-control" id="exampleInputEmail1" maxlength="20" name="name"  placeholder="Region" ng-change="duplicate()"required ng-minlength="2">
                 <label class="error"
                    ng-show="region_form.$submitted && region_form.name.$invalid ">
                    Please enter a valid Region
                </label>
                <label  class="error"
                    ng-show="duplicateDivision != null">
                    @{{duplicateDivision}}
                </label>
            </div>
              <button type="submit" class="btn btn-primary">@{{ region.id != null ? 'Update' : 'Submit'}}</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal" ng-click="close()">Cancel</button>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>
@stop