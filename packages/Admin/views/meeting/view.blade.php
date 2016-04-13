@section('title', 'Meeting')
@section('panel_title', 'Meeting')
@section('content')
    <script type="text/javascript">
        app.controller("MeetingCtrl", function($scope,$http,$filter) {
            $scope.meeting;
            $scope.getMeeting = function(id) {
               var request_url = generateUrl('meeting/'+id);
                $http.get(request_url)
                .success(function(result, status, headers, config) {
                    $scope.meeting = result.response;
					console.log($scope.meeting);
                    $scope.meeting.created_at = new Date( $scope.meeting.created_at); // Converting to UTC date
                }).error(function(data, status, headers, config) {
                    console.log(data);
                });
            };
            $scope.getMeeting({{$id}});
        });
    </script>
    <div class="col-lg-12" ng-controller="MeetingCtrl" >
        <div class="row">
            <div class="col-lg-12">
                    <a class='btn btn-primary pull-right' href='<?php echo route('admin.meeting.edit','')?>/@{{meeting.id}}'>Edit</a>
                    <h3>@{{meeting.title}}</h3>
                    <h4>@{{meeting.date | date:'dd-MM-yyyy'}}</h4>
                    <div class='clear-both'></div>
                    <p><span class="highlight">Posted On :</span>@{{meeting.created_at | date:'dd-MMM-yyyy H:mm a' }}</p>
                    <div class='clear-both'></div>
                    <div ng-bind-html='meeting.description'></div>     
            </div>
        </div>
    </div>     
@stop
