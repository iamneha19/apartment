<div id="flatBillModel" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" ng-click="alertBox('hide')" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" >&times;</span>
          </button>
          <h4 class="modal-title">Generate Flat Bills</h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning hide">
            </div>
            <form ng-submit="generateFlatBills()" novalidate>
                <input  name="id" ng-model="form.society_id" type="hidden" />
                <div class="form-group">
                    <label class="control-label form-label" for="item_name">For the month of</label>
                    <input type="text" id="form_month" name="month" class="form-control month-picker" ng-model="form.month" placeholder="" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="publish_bills">Publish Bill's</label>
                    <input type="checkbox" id="form_publish" ng-model="form.publish" ng-click="alertBox('hide')" />
                </div>
                <div class="modal-footer">
                    <input type="submit" id="submitButton" class="btn btn-primary pull-left"  ng-click="alertBox('hide')" value="Generate" />
                  <button type="button" class="btn btn-primary pull-left"  ng-click="alertBox('hide')" data-dismiss="modal"ng-click="alertBox('hide')">Close</button>
                </div>
            </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
