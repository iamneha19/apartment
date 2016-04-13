@section('title', 'Notice')
@section('panel_title', 'Notice')
@section('head')
<script src="{{ asset('js/moment.js') }}"></script>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("NoticeCtrl", function($scope,$http,$filter) {
            $scope.notice;
            $scope.getNotice = function(id) {
               var request_url = generateUrl('notice/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.notice = result.response;
//                    $scope.notice.created_at = new Date( $scope.notice.created_at); // Converting to UTC date
//                    $scope.notice.expiry_date = new Date( $scope.notice.expiry_date); // Converting to UTC date

                    $scope.notice.created_at = moment($scope.notice.created_at).toDate(); // For cross-browser compatiblity
                    $scope.notice.expiry_date = moment($scope.notice.expiry_date).toDate(); // For cross-browser compatiblity
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getNotice({{$id}});

            $scope.checkExpiry = function(expiry_date){
                var currentUTC =  moment(new Date());
                if(expiry_date > currentUTC ){
                    return true;
                }else{
                    return false;
                }

            };
			
			$scope.textFormat = function(text){
                 return text.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, "\"").replace(/&#39;/g, "'");
            };

        });
    </script>
    <div class="col-lg-12" ng-controller="NoticeCtrl">
        <div class="row form-group">

            <div class="col-md-12">
             	  <button class="btn btn-primary redirect pull-right" data-url="{{ 'notice/print/' . $id }}" ><strong> PRINT</strong></button>
            </div>
                <div class="col-md-12">
                    <a class="btn btn-primary" href="{{ route('notice') }}" ><< Back to notices</a>
                </div>
        </div>
        <div class="row">
            <div class="col-lg-12" ng-show="notice.title">
                    
                    <h3>@{{notice.title}}</h3>
			        <p style="margin-right:20px;" ng-bind-html="textFormat(notice.text)" ></p>
					<br><br><br>
                    <p><span class="highlight"><strong>By :</strong></span>@{{notice.first_name}} @{{notice.last_name}}</p>
                    <div class='clear-both'></div>
                    <p><span class="highlight"><strong>Posted On :</strong></span>@{{notice.created_at | date:'dd-MMM-yyyy H:mm a' }}</p>
                    <p><span class="highlight"><strong>Expiry On :</strong></span>@{{notice.expiry_date | date:'dd-MMM-yyyy' }}</p>
            </div>
        </div>
    </div>
@stop
