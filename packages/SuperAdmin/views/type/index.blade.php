@section('title', 'Society Type List')
@section('panel_title','Types')
@section('panel_subtitle', 'List')
@section('content')
<script>
app.controller('TypeController',function($http,paginationServices,$scope,$filter){
    
    $scope.isDisabled = false;
    $scope.duplicateType= null;
    
    $scope.addType = function() {
        $('#typeModal').modal('show');
    }
     
    $scope.getTypes = function() {
        $http.get(generateUrl('v1/list/type'),$scope.type) 
        .then(function(response) {
                $scope.types = response.data.results.data;
          });
    }
    
    $scope.submitForm = function() {
         if (this.type_form.$invalid)
            return; 
        var $this = this;
        $this.disable = true;
         $http.post(generateUrl('v1/type/create'),$scope.type) 
        .then(function(response) {
            if (response.data.status == "success")
            {
                $('#typeModal').modal('hide');
                grit('',response.data.message);
                $scope.getTypes();               
                $scope.type = {};
                $scope.type_form.$setPristine();
                $scope.duplicateType = null;
            } else 
                {
                    $scope.duplicateType = response.data.message;
                }
                 $this.disable = false;
        });
     }
   
    $scope.delete = function() {
        console.log(this)
        var r = confirm("Deleted type cannot be retrieved");
        if (r == true) {
        var $this = this;
        $http.get(generateUrl('v1/delete/'+this.type.id))
        .then(function(r){
                grit('',r.data.message);
                   grit('',r.data.msg);
            if(r.data.success ==true) {
                $scope.types= $filter('filter')($scope.types, function(value, index) {return value.id != $this.type.id});                
            }
            else
                return;
        });
        }  else {
            return ;
        }
    };
    
     $scope.edit = function() {
        $('#typeModal').modal('show');
        $http.get(generateUrl('v1/type/edit/'+this.type.id))
          .then(function(response) {           
            $scope.type = response.data.results;          
        });
    }
    
    $scope.duplicate = function() {
    $http.post(generateUrl('v1/type/checkDuplicate'),$scope.type)
    .then(function(response) {
         if (response.data.status == "validation_failed") {
            $scope.duplicateType = response.data.message;
         }
        else {
            $scope.duplicateType= null;
             
        }
    });
    }
    
    $scope.close = function() {
        $scope.type = {};
        $scope.type_form.$setPristine(); 
        $scope.duplicateType= null;
    }
    
     $scope.disableButton = function() {
       $scope.isDisabled = true;
    }
    
    $scope.getTypes();
});
</script>

<div class="col-md-12" ng-controller="TypeController">
    <div class="row pull-right" style= "height:50px;">
        <button  id="buttonAdd" type="button" class="btn btn-primary " ng-click="addType()">Create Type</button> 
    </div>
    
    <div class="clearfix">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type</th>  
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
			<tr ng-if="pagination.total == 0" style="margin-left: 30px;">
				<td colspan="3" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
			</tr>	
            <tr ng-repeat="type in types" class="edit" on-finish-render="ngRepeatFinished">
                <td>@{{type.type}}</td>
                <td>
                    <a class="glyphicon glyphicon-pencil" ng-click="edit()" href="" title="Edit" ></a>
                    <a class="glyphicon glyphicon-remove" ng-click="delete() "href=""  title="Delete"></a>
                    <input  ng-model="type.id" type="hidden" <input type="hidden" name="id">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    
    <div id="typeModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button   ng-click="close()" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Type</h4>
      </div>
      <div class="modal-body">
        <form name="type_form" ng-submit="submitForm()" novalidate >
            <div class="form-group">
                <label for="exampleInputEmail1" class="form-label">Type</label>
                <input  ng-model="type.type" type="text" ng-model-options="{debounce:500}"  class="form-control" id="exampleInputEmail1" maxlength="20" name="name"  placeholder="Name" ng-change="duplicate()"required >
                <label class="error"
                    ng-show="type_form.$submitted && type_form.name.$invalid ">
                    Please Enter a Valid Name
                </label>
                <label  class="error"
                    ng-show="duplicateType != null">
                    @{{duplicateType}}
                </label>
            </div>
        <div >
          <button  ng-disabled="disable" type="submit" class="btn btn-primary">@{{ type.id != null ? 'Update' : 'Submit'}}</button>
          <button ng-click="close()" type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>        
        </div>
    </form> 
    </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>
@stop

