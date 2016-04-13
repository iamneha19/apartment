@section('title', 'Edit Personal Info')
@section('panel_title', 'Edit Personal Info')
@section('head')
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
@stop
@section('content')
    <script type="text/javascript">
        app.controller("UserInfoCtrl", function($scope,$http,$filter) {
            $scope.user;
            $scope.getUserInfo = function(id) {
//                console.log(id);
                var request_url = generateUrl('user/info');
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.user = result.response.data;
                console.log($scope.user);
                }).error(function(data, status, headers, config) {
//                        console.log(data);
                });
            };
            $scope.getUserInfo({{$user_id}});
            
            $scope.formatDateTime = function(date,time){
                var dateArray = date.split("-");
                if(time){
                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]+' '+time);
                    return $filter('date')(dateUTC, 'yyyy-MM-dd HH:mm:ss');
                }else{
                    var dateUTC =  new Date(dateArray[2]+'-'+dateArray[1]+'-'+dateArray[0]);
                    return $filter('date')(dateUTC, 'yyyy-MM-dd'); 
                }
            };
            
            $('#user-form').submit(function(e){
                e.preventDefault();
                if ($("#user-form").valid()){
                   $(this).find('button[type=submit]').attr('disabled',true);
                   $(this).find('button[type=submit]').text('Updating please wait..');
                    var  enteredDate =  $('#dob').val();
                    if(enteredDate != '' ){
                        var formatedDate = $scope.formatDateTime(enteredDate);
                        $('#dob').val(formatedDate); // Change format to Y-M-D H:i:s 
                    }
                    
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('user/edit_info/'+$scope.user.id);
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("#user-form").find('button[type=submit]').attr('disabled',false);
                        $("#user-form").find('button[type=submit]').text('Update');
                       var result = response.data.response; // to get api result 
                        if(result.success){
                              grit('','User updated successfully!');
                            window.location.reload();
                        }else{
                            console.log("some error occured!");
                        } 
                       
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });
//            
         jQuery.validator.addMethod("domain", function(value, element) {
            var  voter_id =  $('#voter_id').val();
            if(voter_id!='')
            {
                if(voter_id != voter_id.match(/^[a-zA-Z0-9]+$/))
                {
                    return false;
              }else{
                     return true;
               }
            }else{
                return true;
            }
        
          });
          
          jQuery.validator.addMethod("unique", function(value, element) {
            var unique_id =  $('#unique_id').val();
            if(unique_id!='')
            {
                if(unique_id != unique_id.match(/^[a-zA-Z0-9]+$/))
                {
                    return false;
              }else{
                     return true;
               }
            }else{
                return true;
            }
        
          });
        });
    </script>
    <style>.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #fff;
    opacity: 1;</style>
    <div class="col-lg-12" ng-controller="UserInfoCtrl" >
        <div class="row">
            <div class="col-lg-6">
                <form id="user-form" method="post" action="">
                    <div class="form-group">
                         <label class="form-label">First Name</label>
                         <input type="text" class="form-control" name = "first_name" maxlength="20" value="@{{user.first_name}}"  placeholder="First Name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name = "last_name" maxlength="20" value="@{{user.last_name}}"  placeholder="Last Name">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email address</label>
                        <input type="text" class="form-control" name = "email" maxlength="40" value="@{{user.email}}"  placeholder="Email Address">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile No</label>
                        <input type="text" class="form-control" name = "contact_no" value="@{{user.contact_no}}"  placeholder="Contact Number">
                    </div>
                     <div class="form-group">
                        <label>Dob</label>
                        <input id='dob' type="text" class="form-control" name="dob" value="@{{user.dob|date:'dd-MM-yyyy'}}"  placeholder="Date of birth" readonly="readonly">
                    </div>
                    <div class="form-group">
                        <label>Voter ID</label>
                        <input type="text" class="form-control" id="voter_id" maxlength="10" name = "voter_id" value="@{{user.voter_id}}"  placeholder="Voter ID">
                    </div>
                    <div class="form-group">
                        <label>Unique ID</label>
                        <input type="text" class="form-control" id="unique_id" maxlength="10" name = "unique_id" value="@{{user.unique_id}}"  placeholder="Unique ID">
                    </div>
                        
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{route('conversations')}}"><button class="btn btn-primary" type="button"  >Cancel</button></a>
                </form>
            </div>
        </div>
    </div>
    <script>
    $('document').ready(function(){
        $('#dob').datetimepicker({
                    useCurrent : true,
                    format: 'DD-MM-YYYY',
                    maxDate:moment(new Date()),
                    ignoreReadonly : true,
                    widgetPositioning: {
                            horizontal: 'left',
                            vertical:'bottom'
                         }
                 
            });
    $("#user-form").validate({
                rules: {
                      // simple rule, converted to {required:true}
                    first_name: "required",
                    last_name: "required",
                    // email: "required",
//                    contact_no : "required",
                    voter_id : {
                      minlength:6,domain:true
                    },
                     unique_id : {
                      minlength:6,unique:true
//                      alpha_numeric:true,
                    },
                    contact_no : {
                        required:true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                     },
                     email: {
                        required: true,
                        email: true
                    },
                },
                messages: {
                    voter_id: {
//                        required: "We need your email address to contact you",
                        domain: "Please enter valid voter id!"
                    },
                    unique_id: {
//                        required: "We need your email address to contact you",
                        unique: "Please enter valid unique id!"
                    }
                }
                 });
             });
             </script>
@stop