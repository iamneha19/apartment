@extends('admin::layouts.admin_layout')
@section('title', 'Flat Bills list')
@section('panel_title', 'Flat Bills list')

@section('head')
<script type="text/javascript">
    const society_id = "{!! session()->get('user.society_id') !!}";
    const YEAR = "{!! $year !!}";
    const MONTH = "{!! $month !!}";
</script>

@include('admin::partial.bootstrap_head')

<script src="{!! asset('js/billing.js') !!}"></script>
@stop

@section('content')
<div class="col-md-12">
    <div role="tabpanel" class="tab-pane active" id="items" ng-controller="FlatBillsController">
        <div class="pull-left">
            <label for="status">Status: </label>
            <select ng-model="form.status" ng-change="getFlatBills(1, {'status': form.status})" style="height: 30px">
                <option value="">All</option>
                <option value="paid">Paid</option>
                <option value="unpaid">Unpaid</option>
            </select>
        </div>
        <div class="pull-right" style="margin-bottom: 14px;">
            <a href="{!! route('billing.dashboard') !!}" class="btn btn-primary">
                <span class="glyphicon glyphicon-chevron-left"></span> Go to Reports
            </a>
            <button type="button" class="btn btn-primary" ng-click="showBillGeneratorModal()">
                    Generate Flat Bills
            </button>
        </div>
        <div class="clearfix"></div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Flat - Block - Building</th>
                    <th>Month</th>
                    <th>Flat Type - Sq. feet</th>
                    <th>Items</th>
                    <th>Maintenance</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="flatBill in flatBills">
                    <td>@{{ flatBill.select2.text }}</td>
                    <td>@{{ flatBill.month }}</td>
                    <td>@{{ flatBill.flat.type.capitalizeFirstLetter() + ' - ' + (flatBill.flat.square_feet_1 || '1') + ' sq ft' }}</td>
                    <td>
                        <ol style="padding-left: 15px">
                            <li ng-repeat="flatBillItem in flatBill.flat_bill_items">
                                @{{ flatBillItem.item.name }} - @{{ flatBillItem.item.charge }}
                            </li>
                        </ol>
                    </td>
                    <td>@{{ flatBill.charge }}</td>
                    <td>@{{ flatBill.total_charge }}</td>
                    <td>@{{ flatBill.status }}</td>
                    <td>
                        <!-- <a ng-click="publish()" href="javascript:void(0)">Send Mail</a> -->
                        <a ng-click="payment(flatBill.id)" href="javascript:void(0)" class="btn btn-default btn-sm">Clear Payment</a>
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


    @include('Billing::layouts.partial.generate_flat_bill')

    <div id="paymentModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" ng-click="hideBillPayment()" aria-label="Close">
                  <span aria-hidden="true" >&times;</span>
              </button>
              <h4 class="modal-title">Payment details</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning hide">
                </div>
                <form id="billPaymentForm" ng-submit="billPayed()" ng-click="alertBox('hide')">
                    <input  name="id" ng-model="form.flat_id" type="hidden" />
                    <div class="form-group">
                        <label class="control-label form-label" for="item_name">Payment type</label>
                        <select class="payment" id="payment_type" name="payment_type" ng-model="form.payment_type" ng-click="alertBox('hide')">
                            <option value="" selected>Select a type</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="form-group" ng-show="form.payment_type == 'cheque'">
                        <label class="control-label form-label" for="cheque_number">Cheque Number</label>
                        <input type="text" id="cheque_number" name="cheque_number" ng-model="form.cheque_number" ng-click="alertBox()">
                    </div>
                    <div class="modal-footer">
                        <input type="submit" id="submitButton" class="btn btn-primary pull-left"  ng-click="alertBox('hide')" value="Done" />
                        <button type="button" class="btn btn-primary pull-left"  ng-click="alertBox('hide')" data-dismiss="modal"ng-click="alertBox('hide')">Close</button>
                    </div>
                </form>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
</div>
@stop

@section('footerCSSAndJS')
    <script>
        $("#paymentModel").validate({
        	ignore: [],
            rules: {
                payment_type : {
                    required:true
                }
            }
        });
    </script>
    <script src="{!! asset('js/main.js') !!}"></script>
@stop
