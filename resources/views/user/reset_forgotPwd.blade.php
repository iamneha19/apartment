@section('title', 'Reset Password')
@section('panel_title', 'Please Update your password!')
@section('panel_subtitle', '')
@section('content')
    <script>
    app.controller("ChangePasswordCtrl", function(URL,paginationServices,$scope,$http) {

        $('#reset_pwd-form').submit(function(e){
            e.preventDefault();
            var username = $('#username').val();
            if($('#reset_pwd-form').valid())
            {
                $(this).find('button[type=submit]').attr('disabled',true);
                $(this).find('button[type=submit]').text('Updating password please wait..');
                var records = $.param($( this ).serializeArray());
                 var data = $(this).serializeArray();
                 data.push({name:'username',value:username});
                  var records = $.param(data);
                var request_url = generateUrl('user/reset_forgotpwd');
                $http({
                        url: request_url,
                        method: "POST",
                        data: records,
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    })
                .then(function(response) {
                    $('#reset_pwd-form').find('button[type=submit]').attr('disabled',false);
                    $('#reset_pwd-form').find('button[type=submit]').text('Update');
                    var result = response.data.response;
                    if(result.success)
                    {
                        $.ajax({
                                url: '<?php echo route('reset_password_session') ?>',
                                method: "POST",
                                dataType:"json",
                                data: {change_password:1}
                            })
                            .success(function(data) {
                                if(data.success){   
                                    
                                    grit('','Password updated successfully! Please Login Again.');
                                    setTimeout(
                                                function () {
                                                                window.location="<?php echo route('logout') ?>"; 
                                                            },
                                              3000);
                                    
//                                    window.location="<?php echo route('conversations') ?>";
                                }else{
                                    console.log('Sessin could not saved');
                                }

                            }).error(function(response){
                                console.log('Store session error');
                            });
                    }else{
                            console.log("Some error occurred!");
                           window.location.reload(); 
                            
                       }
                }, 
                function(response) { // optional
//                   alert("fail");
                }); 
            }
            
        }); 
        
        
    });
    </script>

    <div class="col-lg-12" ng-controller="ChangePasswordCtrl">
        <div class="row">
            <div class="col-lg-6">
                <form id="reset_pwd-form" method="post" action="">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Username: </label>
                        <input class="form-control" id="username" type="text" name="username" disabled value="<?php echo $user_email; ?>">
                       
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1" class="form-label">New Password</label>
                        <input type ="password" class="form-control" id="new_pwd" name="new_password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1" class="form-label">Confirm Password</label>
                        <input type ="password" class="form-control" id="confirm_pwd" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
    <script>
    $('document').ready(function(){
        var username = $('#username').val();
        $('#reset_pwd-form').validate({
            rules:{
                new_password:{
                    required:true,
                    
//                    new_password:function()
//                    {
//                        if($("#old_pwd").val() == $("#new_pwd").val())
//                        {
//                            $( "label#new_pwd-error" ).remove();
//                            $( "#new_pwd" ).after( '<label id="new_pwd-error" class="error" for="new_pwd">New password can not be as same as old password!.</label>' );
//                        }else{
//                             $( "label#new_pwd-error" ).remove();
//                        }
//                    },
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