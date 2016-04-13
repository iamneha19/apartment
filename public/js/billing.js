
app.controller('FlatBillsController', function($scope, generateFlatBills, paginationServices, $http, $filter) {

    currentBillId = 0;

    $scope.form = {};

    $scope.getFlatBills = function(page, filters) {
        var page    = page || 1;
        var options = filters || {};
        options.page= page;

        $http.get(generateUrl('v1/society/' + society_id + '/bills/' + YEAR + '/' + MONTH, options))
             .then(function(response) {
                 $scope.flatBills = response.data.results.data;
                 $scope.pagination.total = response.data.results.total;
                 $scope.pagination.pageCount = response.data.results.last_page;
             });
    }

     $scope.getFlatBills(1);

     // Pagination service
     $scope.pagination = paginationServices.getNew(10);

     $scope.$on('pagination:updated', function(event, data) {
         $scope.getFlatBills($scope.pagination.currentPage, {'status': $scope.form.status || '' });
     });

    $scope.generateFlatBills = function() {
        generateFlatBills.fire(function() {
            $scope.getFlatBills();
        });
    };

    $scope.showBillGeneratorModal = function() {
       return generateFlatBills.show();
    }

    $scope.hideBillGeneratorModal = function() {
       return generateFlatBills.hide();
    }

    $scope.billPayed = function() {
        if (currentBillId == 0) {
            console.log('Bill id not set.');
            return;
        }
        
        // dis
//        jQuery('#submitButton').attr('disabled','disabled');
//        jQuery('#submitButton').text('please wait..');

        var form = $scope.form;
        $http
            .post(generateUrl('v1/society/flat/bill/' + currentBillId + '/payment', form))
            .then(function(response) {
                if (response.data.status == 'success') {
                    grit('', response.data.message);
                    $scope.getFlatBills();
                    // en
//                    jQuery('#submitButton').removeAttr('disabled');
//                    jQuery('#submitButton').text('Submit');
                    jQuery('#paymentModel').modal('hide');
                } else {
                    alertBox('show', response.data.message);
                }
            });
    }

    $scope.hideBillPayment = function() {
        jQuery('#payment_type').val('');
        $scope.form.payment_type = 'cash';
        jQuery('#paymentModel').modal('hide');
        $scope.alertBox();
    }

    $scope.payment = function (billId) {
        jQuery('#paymentModel').modal('show');
        $scope.form = {'payment_type': ''};
        currentBillId = billId;
    }

    $scope.alertBox = function (status, text) {
        var box = jQuery('.alert.alert-warning').text(text);

        if (status == 'show') {
           box.removeClass('hide');
        } else {
           box.addClass('hide');
        }
    }
});

app.controller('FlatBillReportController', function($scope, generateFlatBills, paginationServices, $http, $filter) {

    $scope.generateFlatBills = function() {
        return generateFlatBills.fire(function() {
            window.location = window.location.pathname;
        });
    };

    $scope.showBillGeneratorModal = function() {
       return generateFlatBills.show();
    }

    $scope.hideBillGeneratorModal = function() {
       return generateFlatBills.hide();
    }
});

app.controller("BillingCtrl", function(URL,paginationServices,$scope,$http,$filter) {
    $('#create-bill').submit(function(e) {
          e.preventDefault();
          jQuery('input[name=society_id]').val(society_id);
        if ($("#create-bill").valid()){
          var records = $.param($( this ).serializeArray());
          var request_url = generateUrl('v1/billing');
          $http({
                url: request_url,
                method: "POST",
                data: records,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(response) {
                    if(response.data.status=="success"){
                      grit('',response.data.message);
                      jQuery('.alert.alert-warning').addClass('hide');
                      window.location = billingUrl;
                  }else if (response.data.status == 'validation_failed') {
                        // To handle server side validation errors
                       if (typeof response.data.results !== 'undefined') {
                          var errors = response.data.results;
                          for (var key in errors) {
                                var error = errors[key];
                                for (var index in error) {
                                        $('<label id="'+key+'-error" class="error" for="'+key+'" style="display: inline-block;">'+error[index]+'</label>' ).insertAfter('input[name="'+key+'"]');
                                }
                             }
                             //
                            //  jQuery('#create-bill input[type=text]').val('');
                            //  jQuery('#create-bill .select2-selection__rendered, #billing-item-flats, #billing-item-buildings').empty();
                       } else {
                           jQuery('.alert.alert-warning').text(response.data.message).removeClass('hide');
                       }
                    }
                },
                function(response) { // optional
                    alert("fail");
                });
        }
    });

    $scope.warningBox = function (status) {
        if (status == 'hide') {
            jQuery('.alert.alert-warning').addClass('hide');
        } else {
            jQuery('.alert.alert-warning').removeClass('hide');
        }
    }
});


app.controller('ItemController', function(URL, paginationServices, generateFlatBills, $scope, $http, $filter) {
    $scope.bill;
    $scope.flats;
    $scope.flat;
    $scope.activeIndex = null;

    $scope.pagination = paginationServices.getNew(10);

    $scope.getList = function(currentPage) {
        var $this = this;
        var options = {'society_id': society_id};
        options.page = currentPage;
        $http.get(generateUrl('v1/billings', options))
        .then(function(response) {
            $scope.bills = response.data.results.data;
            $scope.pagination.total = response.data.results.total;
            $scope.pagination.pageCount = response.data.results.last_page;
            if ($scope.pagination.total == 0 ){ $("#dataCheck").text("No Data Found."); }
        });
    };

    $scope.$on('pagination:updated', function(event,data) {
        $scope.getList($scope.pagination.currentPage);
    });

   $scope.getList();

    $scope.showItemDetails = function () {
        jQuery('#itemDetails').dialog('show');
    }

    $scope.editBilling = function() {
        $scope.activeIndex = this.$index;
        var $this = this;
        $('#editModal').modal('show');
        $http.get(generateUrl('v1/billing/'+$this.bill.id, {'society_id': society_id}),$scope.bill)
            .then(function(response) {
                $scope.bill = response.data.results;
                $scope.flats = response.data.results.flats;
//                     jQuery('input[name=month]').val($scope.bill.month);
                $scope.buildings = response.data.results.buildings;
            });
    }

    $scope.submitForm = function() {
        if (! jQuery("#editModal form").valid()) {
            return ;
        }

        var $this = this;
        jQuery('input[name=society_id]').val(society_id);
        $scope.bill.month = jQuery('input[name=month]').val();
        console.log($scope.bill);
        $http.post(generateUrl('v1/billing/' + $this.bill.id),$scope.bill)
        .then(function(response) {
            if (response.data.status == 'success') {
                $('#editModal').modal('hide');
                grit('',response.data.message);
                $scope.getList();
            } else if (response.data.status == 'validation_failed') {
                jQuery('#editModal .alert.alert-warning').text(response.data.message).removeClass('hide');
            } else {
                console.log(response);
            }
        });
    }

    $scope.close = function() {
         $('#editModal').modal('hide');
         $scope.billing_form.$setPristine();
         $scope.bill = {};
         $scope.alertBox('hide');
         jQuery('label.error').remove();
    }

    $scope.view = function() {
        $('#editModal1').modal('show');
         $http.get(generateUrl('v1/billing/'+this.bill.id, {'society_id': society_id}))
         .then(function(r){
              $scope.buildings = r.data.results.buildings;
              $scope.flats = r.data.results.flats;
         });
    }

    $scope.deleteBilling = function() {
        if (confirm("Deleted Bill cannot be retrieved")) {
            var $this = this;
            var options = {'society_id': society_id};
            $http.delete(generateUrl('v1/billing/'+this.bill.id,options))
            .then(function(r){
                    grit('',r.data.message);
                    $scope.bills = $filter('filter')($scope.bills, function(value, index) {
                        return value.id != $this.bill.id
                    });

                   $scope.getList();
                });
        }  else {
            return ;
        }
    }

    $scope.getFlats = function() {
        return $http.get(generateUrl('v1/flats', {'society_id': society_id}))
        .then(function(response) {
            return response.data.results;
        });
    }

    $scope.getBuildings = function() {
        return $http.get(generateUrl('v1/buildings', {'society_id': society_id}))
        .then(function(response) {
            return response.data.results;
        });
    }

    $scope.alertBox = function(status) {
        jQuery('#editModal .alert.alert-warning').addClass(status);
    };

    $scope.showAddBillModel = function() {
        jQuery('#AddBillModel').modal('show');
    }

    $scope.hideAddBillModel = function() {
        jQuery('#AddBillModel').modal('hide');
    }

    $scope.showBillGeneratorModal = function(month) {
        generateFlatBills.show();
        jQuery('#form_month').attr('disabled', 'disabled').val(month);
    }

    $scope.hideBillGeneratorModal = function() {
        return generateFlatBills.hide();
    }

    $scope.generateFlatBills = function() {
        generateFlatBills.fire(function() {
            $scope.getList();
        });
    };
});
