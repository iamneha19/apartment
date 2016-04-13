@extends('admin::layouts.admin_layout')
@section('title', 'Flat Bill Report')
@section('panel_title', 'Flat Bill Report')

@section('head')
<link rel="stylesheet" href="{{ asset('bower_components/bootstrap-calendar/css/calendar.min.css') }}">
<script type="text/javascript" src="{{ asset('bower_components/underscore/underscore-min.js') }}"></script>
<script type="text/javascript" src="{{ asset('bower_components/bootstrap-calendar/js/calendar.js') }}"></script>
<script type="text/javascript">
    const society_id = "{!! session()->get('user.society_id') !!}";
</script>

@include('admin::partial.bootstrap_head')

<script src="{!! asset('js/billing.js') !!}"></script>
@stop

@section('content')
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="items" ng-controller="FlatBillReportController">
            <div id="calendar"></div>
            <hr />
            <div class="col-md-offset-8">
                <button type="button" class="btn btn-primary" ng-click="showBillGeneratorModal()">
                        Generate Flat Bills
                </button>
                <div class="btn-group">
        			<button class="btn btn-primary" data-calendar-nav="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span> Prev
                    </button>
        			<button class="btn btn-primary" data-calendar-nav="next">
                        Next <span class="glyphicon glyphicon-chevron-right"></span>
                    </button>
        		</div>
            </div>
            <hr />
            @include('Billing::layouts.partial.generate_flat_bill')
        </div>
    </div>

@stop

@section('footerCSSAndJS')
    <script type="text/javascript">
        var calendar = $("#calendar").calendar({
            view: 'year',
            tmpl_path: "{!! getenv('ROOT_PATH') !!}" + "/tmpls/",
            events_source:
            // 'https://raw.githubusercontent.com/Serhioromano/bootstrap-calendar/master/events.json.php',
            function(startDate) {
                var year = moment(startDate).format('YYYY');
                var billReports = getBillReports(year);

                jQuery(billReports).each(function(index, billReport) {
                    var date = billReport.month.split(' ');
                    billReport.start = (new Date(date[0] + ' 01, ' + date[1] + ' 12:00:00')).getTime();
                    billReport.end = billReport.start + 1;
                    billReport.url = '/dashboard/admin/bills/' + date[1] + '/' + date[0];
                });

                return billReports;
            },
    		onAfterViewLoad: function(view) {
    			$('.page-header').text('Flat Bill Report ' + this.getTitle());
    			$('.btn-group button').removeClass('active');
    			$('button[data-calendar-view="' + view + '"]').addClass('active');
    		},
    		classes: {
    			months: {
    				general: 'label'
    			}
    		}
        });

    	$('.btn-group button[data-calendar-nav]').each(function() {
    		var $this = $(this);
    		$this.click(function() {
    			calendar.navigate($this.data('calendar-nav'));
    		});
    	});

        function getBillReports(year) {
            var events = [];
            $.ajax({
                url:      generateUrl('v1/society/' + society_id + '/bill/report', {'year': year}),
                dataType: 'json',
                type:     'GET',
                async:    false
            }).done(function(json) {
                if(json.results) {
                    events = json.results;
                }
            });

            return events;
        }
    </script>
    <script src="{!! asset('js/main.js') !!}"></script>
@stop
