function generateUrl(requested_url,options) {
    var query_string = '?access_token='+ACCESS_TOKEN;
    for (var key in options) {
        query_string += '&'+key+'='+options[key];
    }
    return API_URL+requested_url+query_string;
};


var app = angular.module("resident", ['ngSanitize']).constant('URL',API_URL).constant('ACCESS_TOKEN',ACCESS_TOKEN);

app.factory('commonServices', function (URL,ACCESS_TOKEN) {
    var common = {};
    common.getUrl = function(requested_url,options) {
        var query_string = '?access_token='+ACCESS_TOKEN;
        for (var key in options) {
            query_string = query_string+'&'+key+'='+options[key];
        }
        return URL+requested_url+query_string;
    };
    return common;
});

app.factory('paginationServices', function ($rootScope) {
    var pagination = {};

    pagination.getNew = function(perPage) {

      perPage = perPage === undefined ? 5 : perPage;

      var paginator = {
        total: 0,
        offset: 0,
        itemsPerPage: perPage,
        currentPage:1,
        pageCount:1
      };
      
        paginator.range = function() {
            var ret = [];
            for (var i=1; i<=paginator.pageCount; i++) {
              ret.push(i);
            }
            return ret;
        };
        
         paginator.setPage = function(n) {
             paginator.currentPage = n;
            paginator.offset = (n-1) * paginator.itemsPerPage;
            $rootScope.$broadcast('pagination:updated');

        };
        
        paginator.nextPage = function() {
            if (paginator.currentPage < paginator.pageCount) {
              paginator.currentPage++;
                paginator.setPage( paginator.currentPage);
            }
        };
        
        paginator.nextPageDisabled = function() {
            return paginator.currentPage === paginator.pageCount ? "disabled" : "";
        };
        
        paginator.prevPage = function() {
            if (paginator.currentPage > 1) {
              paginator.currentPage--;
             paginator.setPage( paginator.currentPage);
            }
        };
        
        paginator.prevPageDisabled = function() {
            return paginator.currentPage === 1 ? "disabled" : "";
        };
       
      return paginator;
    };

    return pagination;
});

app.run(function($rootScope, commonServices) {
            $rootScope.common = commonServices;
});

app.controller("HomeCtrl", function(URL,commonServices,$scope,$rootScope,$http) {
    
//    $scope.common = commonServices;
    $scope.openSocietyForm = function(){
	$('#formSocietyModal').modal();
    };
    
    
//    $scope.openLoginForm = function(){
//	$('#formLoginModal').modal();
//    };

    $('#society-form').submit(function(e){
        e.preventDefault();
        if ($("#society-form").valid()){

            var records = $.param($( this ).serializeArray());
            $http({
                url: URL+'society/create',
                method: "POST",
                data: records,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(function(response) {
                // console.log(response);
				alert("Society created successfully");
				  window.location.reload(); 
				
            }, 
            function(response) { // optional
                alert("fail");
            });
       }
    });
    
});



