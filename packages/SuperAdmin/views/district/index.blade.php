@section('title', 'District')
@section('panel_title','District')
@section('panel_subtitle', 'List')
@section('content')
<script>
app.controller('districtController',function($http,paginationServices,$scope,$filter)
{ 
    $scope.duplicateDivision= null;
    $scope.isDisabled = false;
    $scope.itemsPerPage = 2;
    $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
     
    $scope.getStates = function() {
            $http.get(generateUrl('v1/states?per_page=unlimited&orderby=ASC'))
            .then(function(response) {
              $scope.states = response.data.results;
            });
        }
        
    $scope.getRegion = function() {
        $http.get(generateUrl('v1/region?per_page=unlimited&orderby=ASC'))
        .then(function(response) {
              $scope.regions = response.data;
       });
    }
     
     $scope.getDivision = function() {
            $http.get(generateUrl('v1/division?per_page=unlimited&orderby=ASC'))
            .then(function(response) {
                  $scope.divisions = response.data;
           });
        }
        
     $scope.getDistricts = function(page,search) {
        var options = {page:page,per_page:$scope.itemsPerPage};
        $scope.pagination.currentPage = page;
        if(search) {
                options['search']=search;
            }
        $http.get(generateUrl('v1/district',options))
        .then(function(response) {
              $scope.pagination.total = response.data.total;
              $scope.pagination.pageCount = response.data.last_page;
              $scope.pagination.currentPage = response.data.current_page;
              $scope.districts = response.data.data;
			  if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
       });
    }
    
    $scope.getDistricts($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
        
    $scope.$on('pagination:updated', function(event,data) {
       $scope.getDistricts($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
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
        $('#addDistrictModal').modal('show');
        $scope.getStates();
    }
    
    $scope.submitForm = function() {
        if (this.district_form.$invalid)
        return;
        var $this = this;
        $this.disable = true;
        $http.post(generateUrl('v1/district/save'),$scope.district,$scope.region)
        .then(function(response) {
             if (response.data.status == "success") {
                $('#addDistrictModal').modal('hide');               
                $scope.getDistricts($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
                $scope.duplicateDivision = null;
                $scope.close();
            } else {
                 $scope.duplicateDivision = response.data.message;
            }
             $this.disable = false;
       });
    }
   
    $scope.close = function() {
           $("#district_form")[0].reset();
            $scope.district = {};
            $scope.district_form.$setPristine();
            $scope.duplicateDivision = null;
    }
     
    $scope.edit = function() {
        $('#addDistrictModal').modal('show');
         $scope.getStates();
         $scope.getDivision();
        $scope.getRegion();
        $http.get(generateUrl('v1/district/edit/'+this.district.id,{region_id:this.district.region_id}))
        .then(function(response) {
            $scope.district = response.data.results;
       });
   }
    
    $scope.delete = function() {
        var r = confirm("Deleted District cannot be retrieved");
        if (r == true) {
            var options = {division_id:this.district.division_id,state_id:this.district.state_id,region_id:this.district.region_id};
            var $this = this;
            $http.get(generateUrl('v1/district/delete/'+this.district.id,options))
            .then(function(r){
               grit('',r.data.msg);
            if(r.data.success) {
                $scope.pagination.currentPage = 1;
                $scope.getDistricts($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
            }
            });
        } else {
            return ;
        }
    }
    
     $scope.duplicate = function() {
        console.log(this);
        $http.post(generateUrl('v1/district/check'),$scope.district)
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
        $http.get(generateUrl('v1/district')+'&search='+this.search)
        .then(function(r) {
            $scope.districts = r.data.results.data;  
        });
    }
    
    $scope.getStateDivision = function() {
            var $this = this;
            console.log(this);
            $http.get(generateUrl('v1/states/div/'+$this.district.state_id))
            .then(function(response) {
              $scope.divisions = response.data.results;
            });
        }
       
       $scope.getDivisionRegion = function() {
            var $this = this;
            console.log(this);
            $http.get(generateUrl('v1/division/reg/'+$this.district.division_id))
            .then(function(response) {
              $scope.regions = response.data.results;
            });
        }
});
</script>
    
<div class="col-md-12" ng-controller="districtController"> 
    <div class="col-lg-12" style= "height:50px;">
         
		<input ng-model='search' class="form-control" placeholder="Search" ng-model-options="{debounce:800}" ng-change="searchFile()" style="width: 300px;float: left;margin-left: -15px">
        <div class="btn-toolbar pull-right">
            <button  id="buttonAdd" type="button" class="btn btn-primary " ng-click="addRegion()">Create District</button> 
        </div>
    </div>
    <table class="table table-bordered">
            <thead>
                <tr>
                    <th>District</th>
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
            <tr ng-if="pagination.total > 0" ng-repeat="district in districts" class="edit" on-finish-render="ngRepeatFinished">
                <td>@{{district.name}}</td>
                <td>@{{district.region}}</td>
                <td>@{{district.division}}</td>
                <td>@{{district.state}}</td>
                <td>@{{district.created_at}}</td>
                
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
    
    <!--add District model-->
    
<div id="addDistrictModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" ng-click="close()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">District</h4>
      </div>
      <div class="modal-body">
          <form id="district_form" name="district_form" ng-submit="submitForm()" novalidate >
            <div class="form-group" ng-hide="district.id != null">
                <label class="form-label" for="emailsubject">Select State</label> 
                <select	 ng-change="getStateDivision()" name="state" class="form-control"
                        ng-model="district.state_id"
                        ng-options="state.id as state.name for state in states" 
                        required>
                    <option value="" selected="">Select State</option>
                </select>
                <label class="error"
                    ng-show="district_form.$submitted && district_form.state.$invalid ">
                    This field is required
                </label>
            </div>
                <div class="form-group" ng-hide="district.id == null">
                   <label for="emailsubject">State</label> 
                    <input value="@{{district.state}}" class="form-control" selected="" disabled>
                </div>
              
               <div class="form-group" ng-hide="district.id != null">
                <label class="form-label" for="emailsubject">Select Division</label> 
                <select	ng-change="getDivisionRegion()" name="division" class="form-control"
                        ng-model="district.division_id"
                        ng-options="division.id as division.name for division in divisions" 
                        required>
                    <option value="" selected="">Select Division </option>
                </select>
                <label class="error"
                    ng-show="district_form.$submitted && district_form.division.$invalid ">
                    This field is required
                </label>
            </div>
              <div class="form-group" ng-hide="district.id == null">
                   <label for="emailsubject">Division</label> 
                     <input value="@{{district.division}}" class="form-control" selected="" disabled>
                </div>
            <div class="form-group" ng-hide="district.id != null">
                <label class="form-label" for="emailsubject">Select Region</label> 
                <select	 ng-change="duplicate()" name="region" class="form-control"
                        ng-model="district.region_id"
                        ng-options="region.id as region.name for region in regions" 
                        required>
                    <option value="" selected="">Select Region </option>
                </select>
                <label class="error"
                    ng-show="district_form.$submitted && district_form.region.$invalid ">
                   This field is required
                </label>
            </div>
              <div class="form-group" ng-hide="district.id == null">
                   <label for="emailsubject">Region</label> 
                     <input value="@{{district.region}}" class="form-control" selected="" disabled>
                </div>
            <div class="form-group">
        <label for="exampleInputEmail1" class="form-label">District</label>
                <input  ng-model="district.name" type="text" ng-pattern="/^[A-Za-z\s]+$/" ng-model-options="{debounce:1000}"  class="form-control" id="exampleInputEmail1" maxlength="20" name="name" ng-pattern="/^[A-Za-z\s]+$/"  placeholder="District" ng-change="duplicate()"required ng-minlength="2">
                 <label class="error"
                    ng-show="district_form.$submitted && district_form.name.$invalid ">
                    Please enter a valid District
                </label>
                <label  class="error"
                    ng-show="duplicateDivision != null">
                    @{{duplicateDivision}}
                </label>
            </div>
              <button type="submit" class="btn btn-primary">@{{ district.id != null ? 'Update' : 'Submit'}}</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal" ng-click="close()">Cancel</button>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>
@stop