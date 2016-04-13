
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

    });
</script>
<div class="col-lg-12" ng-controller="NoticeCtrl">
    <div class="row form-group">



        <div class="col-md-12">
            <a href="" ><strong> PRINT</strong></a>
        </div>

            <div class="col-md-12">
                <a class="btn btn-primary" href="{{ route('notice') }}" ><< Back to notices</a>
            </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
                <a class='btn btn-primary pull-right' ng-show=" ( checkExpiry(notice.expiry_date) && (notice.user_id == {{Session::get('user.user_id')}})) ?  true: false " href="<?php echo route('admin.notice.edit','') ?>/@{{notice.id}}">Edit</a>
                <h3>@{{notice.title}}</h3>
                <p><span class="highlight">By :</span>@{{notice.first_name}} @{{notice.last_name}}</p>
                <p><span class="highlight">Text :</span>@{{notice.text}}</p>
                <div class='clear-both'></div>
                <p><span class="highlight">Posted On :</span>@{{notice.created_at | date:'dd-MMM-yyyy H:mm a' }}</p>
                <p><span class="highlight">Expiry On :</span>@{{notice.expiry_date | date:'dd-MMM-yyyy' }}</p>
                <div class='clear-both'></div>
                <div ng-bind-html='notice.text' style="word-wrap: break-word;"></div>
        </div>
    </div>
</div>
