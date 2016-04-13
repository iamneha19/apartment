@section('title', 'Society Type List')
@section('panel_title','Types')
@section('panel_subtitle', 'List')
@section('content')
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>

<script>
app.controller('CategoryController',function($http,paginationServices,$scope,$filter){
    $scope.activeIndex = null;
    $scope.activeType = 'society';
    $scope.societyTypes;
    $scope.societyType;
    $scope.type;
    $scope.isDisabled = false;
    $scope.duplicateType= null;
    
    $scope.itemsPerPage = 10;
    $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
  
    $scope.defaultList = function(page) {
        var options = {page:page,per_page:$scope.itemsPerPage};
        $scope.pagination.currentPage = page;
        $http.get(generateUrl('v1/categories',options),$scope.societyTypes)
        .then(function(response){
            $scope.societyTypes = response.data.results.data;
                $scope.pagination.total = response.data.results.total;
                $scope.pagination.pageCount = response.data.results.last_page;
        });
    }
      $scope.defaultList();

    $scope.typeSelect = function(type,page) {
        $scope.activeType = type;
        if(!page){
            page = 1;
        }
         var options = {page:page,per_page:$scope.itemsPerPage};
         $scope.pagination.currentPage = page;
        $http.get(generateUrl('v1/superadmin/list/typeList/'+type,options))
        .then(function(response){
            $scope.societyTypes = response.data.results.data;
            $scope.pagination.total = response.data.results.total;
            $scope.pagination.pageCount = response.data.results.last_page;
			if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
        });
    }

    $scope.typeSelect($scope.activeType);
    $scope.$on('pagination:updated', function(event,data) {
        $scope.typeSelect($scope.activeType,$scope.pagination.currentPage);
    });
    
    $scope.submitForm = function() {
        if (this.type_form.$invalid)
            return;
        console.log(this);
        var $this = this;
        $this.disable = true;
        $scope.type.type = $scope.activeType;
        $http.post(generateUrl('v1/superAdmin/type/update'),$scope.type) 
        .then(function(response) {
        if($this.type.id == undefined) {
            if (response.data.status == "success")
            {
                
                $('#editModal').modal('hide');
                grit('',response.data.message);
                 $scope.typeSelect($scope.activeType);
                 $scope.type = {};
                $scope.type_form.$setPristine();
                $scope.duplicateType = null;
            }
        } else {
            if (response.data.status == "success") {
                $('#editModal').modal('hide');
                grit('',response.data.message);
                $scope.societyTypes[$scope.activeIndex] = response.data.results;
                $scope.type = {};
                $scope.type_form.$setPristine(); 
                 $scope.duplicateType = null;
            }
            else{
                $scope.duplicateType = response.data.message;
                }
        }    
        $this.disable = false;
        });
    }
    
    $scope.edit = function() {
        $scope.activeIndex = this.$index;
        console.log(this);
        var $this = this;
        $('#editModal').modal('show');
        $http.get(generateUrl('v1/type/'+$this.type.id))
          .then(function(response) {
            $scope.type = response.data.results;
        });
    }
    
    $scope.delete = function() {
        var r = confirm("Deleted type cannot be retrieved");
        if (r == true) {
        var $this = this;
        $http.get(generateUrl('v1/Type/delete/'+this.type.id,{type:$this.type.type}))
        .then(function(r){
            grit('',r.data.msg);
            if(r.data.success ==true) {
                $scope.societyTypes= $filter('filter')($scope.societyTypes, function(value, index) {return value.id != $this.type.id});
                $scope.typeSelect($scope.activeType,1);
            }
            else
                return;
        });
        }  else {
            return ;
        }
    };
    
    $scope.addType = function() { 
        console.log(this);
        console.log($scope.activeType);
        $('#editModal').modal('show');
        if($scope.activeType == 'society') {
            $('#check').hide();
        } else {
            $('#check').show();
        }
    }
    
    $scope.closeEdit = function() {
        $scope.type = {};
        $scope.type_form.$setPristine(); 
        $scope.duplicateType= null;
    }
    
    $scope.duplicate = function() {
    $scope.type.type = $scope.activeType;
    $http.post(generateUrl('v1/checkDuplicate'),$scope.type)
    .then(function(response) {
         if (response.data.status == "validation_failed") {
            $scope.duplicateType = response.data.message;
           
         }
        else {
            $scope.duplicateType= null;
        }
    });
    }
    
    $scope.disableButton = function() {
       $scope.isDisabled = true;
    }
   
    $( document.body ).on( 'click', '.dropdown-menu li', function( event ) {

      var $target = $( event.currentTarget );

      $target.closest( '.btn-group' )
         .find( '[data-bind="label"]' ).text( $target.text() )
            .end()
         .children( '.dropdown-toggle' ).dropdown( 'toggle' ); 
     

   });
});
</script>

<div class="col-md-12" ng-controller="CategoryController">
    <div class="row" style= "margin-right: 5px;  margin-left: 5px; height:30px;">
   
        Select Type:
        <div class="btn-group">
            
             <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span data-bind="label">Society</span>&nbsp;<span class="caret"></span>
            </button>
              <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                <li>
                    <a ng-hide = "societyId != null" role="menuitem" tabindex="-1" ng-click="typeSelect('society')"href="javascript:void(0)">Society</a>
                </li>
                 <li class="divider"></li>
                <li>
                    <a role="menuitem" tabindex="-1" href="javascript:void(0)" ng-click="typeSelect('relationship')">Relationship</a>
                </li>
            </ul>
        </div>
        <div class="btn-toolbar pull-right">
        <button  id="buttonAdd" type="button" class="btn btn-primary " ng-click="addType()">Create Type</button>
        </div>
    </div>
    <!--EMPTY DIV-->
    <div class="col-lg-12" style= "height:30px;">    
    </div>
    <div class="clearfix">
    <!--EMPTY DIV END-->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
			<tr ng-if="pagination.total == 0">
				<td colspan="3" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
			</tr>	
            <tr ng-if="pagination.total > 0" ng-repeat="type in societyTypes" class="edit" on-finish-render="ngRepeatFinished">
                <td>@{{type.name}}</td>
                <td>@{{type.description}}</td>
                <td>
                    <a class="glyphicon glyphicon-pencil" ng-click="edit()" href="" title="Edit" ></a>
                    <a class="glyphicon glyphicon-remove" ng-click="delete() "href=""  title="Delete"></a>
                    <input  ng-model="type.id" type="hidden" <input type="hidden" name="id">
                </td>
            </tr>
            </tbody>
        </table>
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
   
   <!--Category MODAL-->
   
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" ng-click="closeEdit()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Create Type</h4>
        </div>
        <div class="modal-body">
            <form name="type_form" ng-submit="submitForm()" novalidate >
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Name</label>
                    <input  ng-model="type.name" type="text" class="form-control" id="exampleInputEmail1" name="name"  ng-model-options="{debounce:1000}"  placeholder="Name" ng-change="duplicate()"required ng-minlength="2" maxlength="20">
                    <label class="error"
                        ng-show="type_form.$submitted && type_form.name.$invalid ">
                        Please enter valid Name
                    </label>
                    <label  class="error"
                        ng-show="duplicateType != null">
                        @{{duplicateType}}
                    </label>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Description</label>
                    <textarea  ng-model="type.description" type="text" class="form-control" id="exampleInputPassword1" maxlength="200" name="description" placeholder="Description"  ></textarea>
                    <label class="error"
                       ng-show="type_form.$submitted && type_form.description.$invalid ">
                       Please Enter a Valid description
                    </label>
                </div>
<!--                <div class="form-group" id="check">
                    <label for="exampleInputPassword1">Mandatory</label>
                        <input type="checkbox" ng-model="type.is_mandatory"
                            ng-true-value="1" ng-false-value="0"
                            ng-checked="type.is_mandatory == 1">
                </div>-->
                
                    <button type="submit" id="submits" ng-disabled="disable" class="btn btn-primary">@{{ type.id != null ? 'Update' : 'Submit'}}</button>
                    <button type="button" ng-click="closeEdit()" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    
               
           </form>  
        </div>
    </div>
    </div>
</div> 
</div>
@stop