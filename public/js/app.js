// Prototype
String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

String.prototype.format = function(pattern) {
    if (typeof moment == 'undefined') {
        cl('moment is not loaded.');
        return this.valueOf();
    }

    if (typeof pattern == 'undefined') {
      return this.valueOf();
    }

    if (typeof pattern == 'number') {
      return pattern;
    }

    return (new moment(this.valueOf())).format(pattern);
}

const CROSS_MARK_URL = '%E2%9D%8C';

// General functions
function generateUrl(requested_url,options) {
   var query_string = '?access_token='+ACCESS_TOKEN;
   if (typeof options == 'object') {
       for (var key in options) {
           query_string +='&'+key+'='+options[key];
       }
   }
   return API_URL+requested_url+query_string;
};

/* Gritter */
function hideGrit() {
    $('#gritter').find('.msg').text('');
    $('#gritter').fadeOut();
}

$('#close_grit').click(function(){hideGrit()});

function grit(type, msg, color) {
    gitter = jQuery('#gritter');

    if (typeof color == 'undefined') {
        color = '#AAE2BD';
    }

    gitter.addClass(type);
    gitter.css('background-color', color);
    gitter.find('.msg').text(msg);
    gitter.show();

    setTimeout(function(){
        hideGrit();
    },3000)
}

var alertBox = function (status, text, targedForm) {
    if (typeof targedForm == 'undefined') {
        var box = jQuery('.alert.alert-warning');
    } else {
        var box = jQuery(targedForm + ' .alert.alert-warning');
    }

    box.text(text);

    if (status == 'show') {
       box.removeClass('hide');
    } else {
       box.addClass('hide');
    }
}
/* end gritter */

var app = angular.module("sahkari", ['ngSanitize', 'ngTagsInput'])
                 .constant('URL',API_URL)
                 .constant('ACCESS_TOKEN',ACCESS_TOKEN);

 app.factory('commonServices', function (URL,ACCESS_TOKEN) {
    var common = {};

    return common;
 });

 app.factory('generateFlatBills', function ($http) {
    var entities = {};

    entities.clearFlatBillInput = function() {
        jQuery('#form_month').val('');
        jQuery('#form_publish').removeAttr('checked');
    }

    entities.fire = function (callback, options) {
        var $this = this;

        var submitBtn = jQuery('#submitButton');
        var form = {};
        var options = typeof options == 'undefined' ? {}: options;
        form.month = jQuery('#form_month').val();
        form.publish = jQuery('#form_publish').val();

        submitBtn.attr('disabled', 'disabled').val('Updating, Please wait...');

        $http({
                 'method': 'POST',
                 'url': generateUrl('v1/society/' + society_id + '/generate-bills', options),
                 'data': form
             })
             .then(function (response) {
                 if (response.data.status == 'validation_failed') {
                     alertBox('show', response.data.message);
                 } else {
                    grit('', response.data.message);
                    entities.hide();
                 }

                 submitBtn.removeAttr('disabled').val('Update');

                 if (typeof callback == 'function') {
                     callback();
                 }

                 entities.clearFlatBillInput();
             });

        return this;
    }

   entities.show = function() {
       jQuery('#flatBillModel').modal('show');
       entities.clearFlatBillInput();
   }

   entities.hide = function() {
       jQuery('#flatBillModel').modal('hide');
       entities.clearFlatBillInput();
   }

    return entities;
 });

function makePaginationService() {

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
}

// Make pagination service for first time
makePaginationService();

app.run(function($rootScope, commonServices) {
           $rootScope.common = commonServices;
});

function redirectTo(route, options) {
    if (typeof options !== 'object') {
        options = {};
    }
    window.location = generateUrl(route, options);
}

function cl(text) {
    console.log(text);
}

jQuery(document).ready(function() {
    jQuery('.redirect').on('click', function() {
        console.log(this);
        var url = jQuery(this).attr('data-url');
        redirectTo(url);
    });
});
