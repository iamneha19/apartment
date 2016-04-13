@section('title', 'Change Password Dashboard')
@section('panel_title', 'Change Password')
@section('panel_subtitle', '')
@section('content')
    <script>
    app.controller("ChangePasswordCtrl", function(URL,paginationServices,$scope,$http) {
        $('#change_pwd-form').submit(function(e){
            e.preventDefault();
            var username = $('#username').val();
            if($('#change_pwd-form').valid())
            {
//                var records = $.param($( this ).serializeArray());
                 var data = $(this).serializeArray();
                 data.push({name:'username',value:username});
                  var records = $.param(data);
                var request_url = generateUrl('user/update_pwd');
                $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                .then(function(response) {
                    var result = response.data.response;
                    if(result.success)
                    {
                         grit('','Password updated successfully!');
                         window.location.reload(); 
                    }else{
                            if(result.incorrect_pwd)
                            {
                                $( "#old_pwd" ).after( '<label id="old_pwd-error" class="error" for="old_pwd">Please enter correct passowrd.</label>' );
                            }else{
                                $( "label#old_pwd-error" ).remove();
                            }
                            
                        }
                    
//                    window.location.reload(); 
                }, 
                function(response) { // optional
//                    alert("fail");
                }); 
            }
            
        }); 
        
        
    });
    </script>
    <script>
    $(document).ready(function(){
        $('#task_category-form').validate({
            rules:{
                category_name :'required',
            }
        });
    });
    </script>

    <div class="col-lg-12" ng-controller="ChangePasswordCtrl">
        <div class="row">
            <div class="col-lg-6">
                <form id="change_pwd-form" method="post" action="">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Username: </label>
                        <input class="form-control" id="username" type="text" name="username" disabled value="<?php echo $user_email; ?>">
                       
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Old Password</label>
                        <input class="form-control" id="old_pwd" name="old_password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">New Password</label>
                        <input class="form-control" id="new_pwd" name="new_password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Confirm Password</label>
                        <input class="form-control" id="confirm_pwd" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button class="btn btn-primary" type="button" onclick="javascript:window.location.href='<?php echo route('conversations') ?>'">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script>
    $('document').ready(function(){
        var old_pwd = $('#old_pwd').val();
//        alert(old_pwd);
        var username = $('#username').val();
        $('#change_pwd-form').validate({
            rules:{
                old_password : {
                    required:true,
                    remote:{
                        url: generateUrl('user/check_pwd'),
                        type: "post",
                        dataType:"json",
                        data: {old_password:function() {
                                return $( "#old_pwd" ).val();
                              },email_id:username},
                        success:function(r) {
                                var result = r.response;
//                                alert(result);
                               $( "label#old_pwd-error" ).remove();                        
                                 if(result.success){
                                     $( "label#old_pwd-error" ).remove();
                                     return true;
                                 }else{
                                     $( "label#old_pwd-error" ).remove();
                                     $( "#old_pwd" ).after( '<label id="old_pwd-error" class="error" for="old_pwd">Please enter correct passowrd.</label>' );
                                     return false;
                                 }
                             }
                    }
                },
                new_password:{
                    required:true,
                    
                    new_password:function()
                    {
                        if($("#old_pwd").val() == $("#new_pwd").val())
                        {
                            $( "label#new_pwd-error" ).remove();
                            $( "#new_pwd" ).after( '<label id="new_pwd-error" class="error" for="new_pwd">New password can not be as same as old password!.</label>' );
//                            $('#change_pwd-form').find('button[type=submit]').attr('disabled',true);
//                             $('#change_pwd-form').find('button[type=submit]').text('Updating task please wait..');
                            }else{
                             $( "label#new_pwd-error" ).remove();
                        }
                    },
                    minlength:6,
                },
                confirm_password:{
                    required:true,
                    confirm_password:function()
                    {
                        if($("#new_pwd").val() != $("#confirm_pwd").val())
                        {
                            $( "label#confirm_pwd-error" ).remove();
                            $( "#confirm_pwd" ).after( '<label id="confirm_pwd-error" class="error" for="confirm_pwd">New password does not match with confirm password!.</label>' );
//                            $('#change_pwd-form').find('button[type=submit]').attr('disabled',true);
                        }else{
                            $( "label#confirm_pwd-error" ).remove();
                        }
                    },
                    minlength:6,
                    
                }
            }
        });  
    });
        </script>
@stop
