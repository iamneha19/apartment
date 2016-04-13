@section('title', 'Division')
@section('panel_title','Division')
@section('panel_subtitle', 'List')
@section('content')
<script>
    app.controller('divisionController',function($http,paginationServices,$scope,$filter)
    {
        $scope.division;
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
        
        $scope.getDivisions = function(page,search) {
            var options = {page:page,per_page:$scope.itemsPerPage};
            $scope.pagination.currentPage = page;
            if(search) {
                options['search']=search;
            }
            $http.get(generateUrl('v1/division',options))
            .then(function(response) {
                $scope.pagination.total = response.data.total;
                $scope.pagination.pageCount = response.data.last_page;
                $scope.pagination.currentPage = response.data.current_page;
                $scope.divisions = response.data.data;
				if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
                   
           });
        }
        
        $scope.getDivisions($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order); 
        
        $scope.$on('pagination:updated', function(event,data) {
            $scope.getDivisions($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
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


        $scope.addDivision = function() {
           $('#divisionModal').modal('show');
           $scope.getStates();
       }
       
        $scope.edit = function() {
             console.log(this);
            $('#divisionModal').modal('show');
            $scope.getStates();
            $('#w').val();
            $http.get(generateUrl('v1/division/edit/'+this.division.id,{state_id:this.division.state_id}))
            .then(function(response) {
                $scope.division = response.data.results;
           });
       }
       
        $scope.submitForm = function() {
            if (this.division_form.$invalid)
           return;
        var $this = this;
        $this.disable = true;
        console.log(this);
        $http.post(generateUrl('v1/division/save'),$scope.division)
        .then(function(response) {
             if (response.data.status == "success") {
                $('#divisionModal').modal('hide');
                $scope.getDivisions($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order); 
            
                $scope.duplicateDivision = null;
                 $scope.close();
            } else {
                 $scope.duplicateDivision = response.data.message;
            }
             $this.disable = false;
       });
       }
       
        $scope.disableButton = function() {
            $scope.isDisabled = true;
        }
    
       $scope.close = function() {
           $("#division_form")[0].reset();
            $scope.division = {};
            $scope.division_form.$setPristine();
            $scope.duplicateDivision = null;
       }
       
       $scope.delete = function() {
           console.log(this);
        var r = confirm("Deleted Division cannot be retrieved");
        if (r == true) {
            var option ={state_id:this.division.state_id};
            var $this = this;
            $http.get(generateUrl('v1/division/delete/'+this.division.id,option))
            .then(function(r){
               grit('',r.data.msg);
               if(r.data.success) { 
                $scope.pagination.currentPage = 1; 
                $scope.getDivisions($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order);
            }
           });
       
        } else {
            return ;
        }
       }
       
       $scope.duplicate = function() {
        console.log(this);
            $http.post(generateUrl('v1/division/check'),$scope.division)
            .then(function(response) {
                 if (response.data.status == "validation_failed") {
                    $scope.duplicateDivision = response.data.message;
                    $scope.getDivisions($scope.pagination.currentPage,$scope.search,$scope.pagination.itemsPerPage,$scope.sort,$scope.sort_order); 

                 }
                else {
                    $scope.duplicateDivision= null;
                }
            });
        }
    });
</script>

<div class="col-md-12" ng-controller="divisionController"> 
    <div class="col-lg-12" style= "height:50px;margin-bottom: 0px;">
      <input ng-model='search' class="form-control" placeholder="Search" style="width: 300px;float: left;margin-left: -15px">
            <button  id="buttonAdd" type="button" class="btn btn-primary pull-right" ng-click="addDivision()">Create Division</button> 
    </div>
    <table class="table table-bordered">
            <thead>
                <tr>
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
            <tr ng-if="pagination.total > 0" ng-repeat="division in divisions" class="edit" on-finish-render="ngRepeatFinished">
                <td>@{{division.name}}</td>
                <td>@{{division.state}}</td>
                <td>@{{division.created_at}}</td>
                
                <td>
                    <a class="glyphicon glyphicon-pencil" ng-click="edit()" href="" title="Update" ></a>
                    <a class="glyphicon glyphicon-remove" ng-click="delete() "href=""  title="Delete"></a>
                </td>
            </tr>
            </tbody>
        </table>
        <div ng-if="pagination.total > 0" id="pagination" class="row">
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
    
    <!--adding new division modal-->
    
<div id="divisionModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" ng-click="close()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Division</h4>
      </div>
      <div class="modal-body">
          <form id="division_form" name="division_form" ng-submit="submitForm()" novalidate >
              <div class="form-group" ng-hide="division.id != null">
                <label class="form-label" for="emailsubject">Select State</label> 
                <select	 ng-change="duplicate()" name="state" class="form-control"
                        ng-model="division.state_id"
                        ng-options="state.id as state.name for state in states" 
                        required>
                    <option value="" selected="">Select State</option>
                </select>
<!--                <select ng-change="duplicate()" ng-model="division.state_id" name="state_id" class="form-control" required>
                    <option value="" selected>Select State</option>
                    <option ng-repeat="state in states"  ng-selected = "state.id == division.state_id" value='@{{state.id}}'>@{{state.name}}</option>
                    
                 </select>-->
                <label class="error"
                    ng-show="division_form.$submitted && division_form.state.$invalid ">
                    This field is required
                </label>
            </div>
                <div class="form-group" ng-hide="division.id == null">
                   <label for="emailsubject">State</label> 
                    <input value="@{{division.state}}" class="form-control" selected="" disabled>                   
                </div>
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">Division</label>
                <input  ng-model="division.name" type="text" ng-pattern="/^[A-Za-z\s]+$/"  ng-model-options="{debounce:1000}"  class="form-control" id="exampleInputEmail1" maxlength="20" name="name"  placeholder="Division" ng-change="duplicate()"required ng-minlength="2">
                 <label class="error"
                    ng-show="division_form.$submitted && division_form.name.$invalid ">
                    Please enter a valid Division
                </label>
                <label  class="error"
                    ng-show="duplicateDivision != null">
                    @{{duplicateDivision}}
                </label>
            </div>
              <button type="submit" class="btn btn-primary">@{{division.id != null ? 'Update' : 'Submit'}}</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal" ng-click="close()">Cancel</button>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>
@stop