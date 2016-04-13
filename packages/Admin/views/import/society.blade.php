@section('title', 'Configuration')
@section('panel_title', 'Configuration')
@section('head')
@stop
@section('content')
<script>
    const society_id = "{!! session()->get('user.society_id') !!}";
</script>
<link rel="stylesheet" href="{!! asset('bower_components/ngprogress/ngProgress.css') !!}">

<script src="{!! asset('bower_components/ngprogress/build/ngprogress.js') !!}"></script>
<script type="text/javascript" src="{!! asset('bower_components/ng-file-upload/ng-file-upload.js') !!}"></script>
<script type="text/javascript" src="{!! asset('js/society-import.js') !!}"></script>

<div ng-controller="SocietyImportController" class="col-lg-12">
    <div class="row">
        
        <form id="society-import-form" novalidate>
            <div class="col-lg-9" id="building-form-wrapper">
                <a href="{!! asset('documents/xc.ods') !!}" download> <strong>Download Sample Excel </strong> </a>
                <div class="alert alert-warning hide">
                    <ul>
                           <li ng-repeat="x in errorBag">
                             @{{ x }}
                           </li>
                     </ul>
                </div>
                <div class="row col-2">
                    <div class="col-lg-4">
                        <h4>Import Society Config</h4>
                    </div>
                    <div class="col-lg-8">
                        <input type="file" class="" ngf-select  ng-model="importFile" ngf-max-size="20MB" />
                        <br />
                        <button class="btn btn-primary" ng-click="import()" id="start-import">Import</button>
                        <strong id="start-import1" > Please Create Chairman To Import configuration File </strong>
                    </div>
                </div>
                <div class="form-group">
                    <strong id="we"> Feedback from chairman</strong>
                    <br>
                    <br>
                    <p id="v1"></p>  </br>            
                    
                    <strong>Notes:</strong>
                    <ul>
                    <li>We will send a notification to the chairman when configuration importes sucessfully</li>
                    <li>Flat,Wings,Building,Menus are available to modify existing records</li>  
                    </ul>
                      <strong>Guidelines for Importing Society Configuration Data :</strong><br>
                      <ul>
                        <li><strong>Society Amenities :</strong>
                    <ol>
                        <li>Society Amenities field name must be same as mentioned in Sample file.</li>
                        <li>Empty/null/Numeric value is not allowed.</li>
                    </ol></li>
                    <li><strong>Society Parking :</strong>
                     <ol>
                        <li>All fields name must be same as mentioned in Sample file irrespective of their order.</li>
                        <li>Parking Category, Initial field must not be empty/null/numeric.</li>
                        <li>Slot field must be numeric and non-empty.</li>
                        <li>Row field must be present and numeric when Parking Category is Multiple.</li>
                        <li>Column field must be present and numeric when Parking Category is Single.</li>
                        <li>Slot and Column field must be same when Parking Category is Single or Multiple.</li>
                     </ol></li>
                    <li><strong>Building Configuration :</strong>
                    <ol>
                        <li>All fields name must be same as mentioned in Sample file irrespective of their order.</li>
                        <li>Building Name, Flat Numbers, Floor, Sq Ft, Flat Type fields must be present.</li>
                        <li>Flat Numbers, Floor, Sq Ft fields must be numeric.</li>
                        <li>Flat Type must be from Flat,Shop,Office.</li>
                        <li>Wing and Building Amenities are optional but if present, must separate with comma(,). Like : Pool, Gym, Club House</li>               
                    </ol></li>
                    <li><strong>Building Parking :</strong>
                     <ol>
                        <li>All fields name must be same as mentioned in Sample file irrespective of their order.</li>
                        <li>Building Name, Parking Category, Initial, Slots fields must be present.</li>
                        <li>Slot field must be numeric and non-empty.</li>
                        <li>Row field must be present and numeric when Parking Category is Multiple.</li>
                        <li>Column field must be present and numeric when Parking Category is Single.</li>
                        <li>Slot and Column field must be same when Parking Category is Single or Multiple.</li>
                     </ol></li>
                      </ul>
                </div>
            </div>
                    
        </form>
    </div>
</div>
@stop

@section('footerCSSAndJS')
    <script type="text/javascript">
    var et = [];
        $("#society-config-form").validate({
        	ignore: [],
            rules: {
                building_count: {
                    required:true,
                    number: true
                }
            },
            errorPlacement: function(error, element) {
                element.parent().append(error);
            }
        });
    </script>
@stop
