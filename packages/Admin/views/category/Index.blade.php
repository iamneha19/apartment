@extends('admin::layouts.admin_layout')
@section('title', 'types')
@section('panel_title','Category')
@section('panel_subtitle', 'List')
@section('head')
<script src="{{asset('js/jquery.twbsPagination.js')}}"></script>

<script>
app.controller('CategoryController',function($http,paginationServices,$scope,$filter){
    $scope.activeIndex = null;
    $scope.activeType = "";
    $scope.types;
    $scope.societyType;
    $scope.type;
    $scope.isDisabled = false;
    $scope.duplicateType= null;

    $scope.itemsPerPage = 5;
    $scope.pagination = paginationServices.getNew($scope.itemsPerPage);
		$scope.pagination.total =0;


    $('#loader').hide();

     $scope.getTypeList = function() {
        $http.get(generateUrl('v1/list/type'))
        .then(function(response){
           $scope.lists = response.data.results.data;
       });
    }

    $scope.getTypeList();

//    $scope.defaultList = function(page) {
//        var options = {page:page,per_page:$scope.itemsPerPage};
//        $scope.pagination.currentPage = page;
//    $http.get(generateUrl('v1/categories/admin',options),$scope.societyTypes)
//    .then(function(response){
//        $scope.societyTypes = response.data.results.data;
//            $scope.pagination.total = response.data.results.total;
//            $scope.pagination.pageCount = response.data.results.last_page;
//    });
//    }
//      $scope.defaultList();
//    $scope.getTypeList = function() {
//        $http.get(generateUrl('v1/list/type'))
//        .then(function(response){
//           grit('',response.data.results.message);
//       });
//    }

//     $scope.getTypeList();

    $scope.typeSelect = function(type,page) {
        $scope.activeType = type;
     //   console.log(type);
        if(!page){
            page = 1;
        }
//		/$("#dataCheck").text("Fetching Data...");
		$scope.pagination.total =0;
		///alert("Before Ajax "+$scope.pagination.total );
        var options = {page:page,per_page:$scope.itemsPerPage,type:type};
        console.log(type);
        $scope.pagination.currentPage = page;
        $http.get(generateUrl('v1/admin/list/typeList/',options))
        .then(function(response){
            if(response.data.message =='list') {
                $('#buttonAdd1').hide()
            }else {
                $('#buttonAdd1').show()
            }                
            $scope.types = response.data.results.data;
            $scope.pagination.total = response.data.results.total;
			///alert("After Ajax "+$scope.pagination.total );
            $scope.pagination.pageCount = response.data.results.last_page;
			if (parseInt($scope.pagination.total) == 0 ){ $("#dataCheck").text("No Data Found."); }
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
          $('#loader').show();                
        if(!$scope.type.type) {
            $scope.type.type = $scope.activeType;
        }
        else {        
            $scope.type.type;
        }
        $http.post(generateUrl('v1/type/update'),$scope.type)
        .then(function(response) {
            $('#loader').hide();
        if($this.type.id == undefined) {
            if (response.data.status == "success")
            {
                $('#editModal').modal('hide');
                grit('',response.data.message);
                $scope.typeSelect($scope.activeType);
//                $scope.types.push(response.data.results);
                $scope.type = {};
                $scope.type_form.$setPristine();
                $scope.duplicateType = null;
            }
        } else {
            if (response.data.status == "success") {

                $('#editModal').modal('hide');
                grit('',response.data.message);
                $scope.types[$scope.activeIndex] = response.data.results;
                $scope.type = {};
                $scope.type_form.$setPristine();
                $scope.duplicateType = null;
            }
         else{
                $scope.duplicateType = response.data.message;
                }
        }    $this.disable = false;
        });
    }

    $scope.edit = function() {
         var re = /Document/gi;
        console.log(this);
        console.log(this.type.type);       
        if(this.type.type.search(re)== -1) {
            $('#check').hide();
        } else {
            $('#check').show();
        }
        $('#loader').show();
        $scope.activeIndex = this.$index;
        console.log(this);
        var $this = this;
        $('#editModal').modal('show');
        $http.get(generateUrl('v1/type/'+$this.type.id))
          .then(function(response) {
            $('#loader').hide();
            $scope.type = response.data.results;
        });
    }

    $scope.delete = function() {
        console.log(this)
        var r = confirm("Deleted type cannot be retrieved");
        if (r == true) {
        var $this = this;
        $http.get(generateUrl('v1/Type/delete/'+this.type.id,{type:$this.type.type}))
        .then(function(r){
                grit('',r.data.message);
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
        var re = /document/gi;
        console.log(this);
        console.log($scope.activeType);
        $('#editModal').modal('show');
        if($scope.activeType.search(re)== -1) {
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
//    console.log(this);
    if(!$scope.type.type) {
            $scope.type.type = $scope.activeType;
        }
        else {        
            $scope.type.type;
        }
    console.log($scope.type.type);
    
    $http.post(generateUrl('v1/admin/checkDuplicate'),$scope.type)
    .then(function(response) {
        $scope.type.type = response.data.results;
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

    $scope.textFormat = function(text){
            if (text !== ""){
               var text =  text.replace(/(<([^>]+)>)/ig,"");   // To strip html tags
               var shortText = jQuery.trim(text).substring(0, 50)
                          .trim(this) + '...';
                return shortText; 
            }
    };

    $( document.body ).on( 'click', '.dropdown-menu li', function( event ) {
      var $target = $( event.currentTarget );
      $target.closest( '.btn-group' )
         .find( '[data-bind="label"]' ).text( $target.text() )
            .end()
         .children( '.dropdown-toggle' ).dropdown( 'toggle' );


   });
});
</script>
@stop

@section('content')

<div ng-controller="CategoryController">

 <div class="col-lg-12 form-group">
    <div class="pull-left">
        Select Type:
        <div class="btn-group">
          <select ng-change="typeSelect(category.type)" ng-model="category.type" name="type"  class="form-control" >
              <option disabled value="x">Select Category</option>
              <option ng-selected="selected" value="">All</option>
              <option ng-repeat="list in lists"  value='@{{list.type}}'>@{{list.type}}</option>
            <!--<option value="" slected hidden />-->
         </select>
        </div>
    </div>
    @if(!$listPermission)
    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Warning!</strong> You don't have permission to access this page.
    </div>
    @else
	 @if($createPermission)
        <div class="btn-toolbar pull-right">
            <button  id="buttonAdd1" type="button" class="btn btn-primary " ng-click="addType()">Create Category</button>
        </div>
     @endif
</div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Category</th>
                    <th>Category Type</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
			<tr ng-if="pagination.total == 0" style="margin-left: 30px;">
				<td colspan="5" style="font-weight: bold;" id="dataCheck">Fetching Data...</td>
			</tr>
            <tr ng-repeat="type in types" class="edit" on-finish-render="ngRepeatFinished">
                <td>@{{type.id}}</td>
                <td>@{{type.name}}</td>
                <td>@{{type.type}}</td>
                <!-- <td>@{{type.description}}</td> -->
                <td ng-bind-html="textFormat(type.description)" style="clear:both;word-wrap: break-word;"></td>
                <input type="hidden" name="society_id" value="@{{type.society_id}}" />
                <td>
                    <a class="glyphicon glyphicon-pencil" ng-click="edit()" href="" title="Edit" ></a>
                    <a class="glyphicon glyphicon-remove" ng-click="delete() "href=""  title="Delete"></a>
                    <input  ng-model="type.id" type="hidden" <input type="hidden" name="id">
                </td>
            </tr>
            </tbody>
        </table>

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
   	@endif
   <!--Category MODAL-->

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" ng-click="closeEdit()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Category</h4>
        </div>
        <div class="modal-body">
            <form name="type_form" ng-submit="submitForm()" novalidate >
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Name</label>
                    <input  ng-model="type.name" type="text" ng-model-options="{debounce:500}"  class="form-control" id="exampleInputEmail1" maxlength="20" name="name"  placeholder="Name" ng-change="duplicate()"required ng-minlength="2">
                    <label class="error"
                        ng-show="type_form.$submitted && type_form.name.$invalid ">
                        Please Enter a Valid Name
                    </label>
                    <label  class="error"
                        ng-show="duplicateType != null">
                            @{{duplicateType}}
                    </label>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Description</label>
                    <input  ng-model="type.description" type="text" class="form-control" id="exampleInputPassword1" name="description" placeholder="Description" maxlength="200" ng-minlength="2">
                    <label class="error"
                       ng-show="type_form.$submitted && type_form.description.$invalid ">
                       Please enter valid Description
                    </label>
                </div>
                <div class="form-group" id="check">
                    <label for="exampleInputPassword1">Mandatory</label>
                        <input type="checkbox" ng-model="type.is_mandatory"
                            ng-true-value="1" ng-false-value="0"
                            ng-checked="type.is_mandatory == 1">
                </div>

                <!--<button type="submit" id="process" ng-disabled="disable" class="btn btn-success" id="load" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing">@{{ type.id != null ? 'Update' : 'Submit'}}</button>-->
                <input type="hidden"  ng-model ="type.type" name="type" />
                    <button type="submit" id="submits" ng-disabled="disable" class="btn btn-primary">@{{ type.id != null ? 'Update' : 'Submit'}}</button>
                    <button type="button" ng-click="closeEdit()" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            </form>

            <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
            <div id="loader" class="loading">Loading&#8230;</div>
        </div>
    </div>
    </div>
</div>
</div>
@stop
