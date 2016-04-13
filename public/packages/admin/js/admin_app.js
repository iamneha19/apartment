var app = angular.module("admin", []).constant('URL',API_URL).constant('access_token',ACCESS_TOKEN);
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



// meeting controller




