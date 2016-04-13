<!DOCTYPE html>
<html lang="en" ng-app="frontend">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="author" content="Scotch">

        <title>{{env('PROJECT_NAME')}} - @yield('title')</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">


        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js"></script>
        <script>
            var API_URL = {!! "'".Config::get('app.api.request_url')."'" !!};
            var CLIENT_ID = {!! "'".Config::get('app.api.client_id')."'" !!};
        </script>
        <style>
        .footer {
        padding-top:20px;
                bottom: 0;
                width: 100%;
                /* Set the fixed height of the footer here */
                height: 60px;
                background-color: #f5f5f5;
        }

        </style>
    </head>
    <body>
        <div class="container">

            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container">
                  <!-- Brand and toggle get grouped for better mobile display -->
                  <div class="navbar-header">
                    
<!--                    <a class="navbar-brand" href="#">Super Admin SMS</a>-->
                    <a class="navbar-brand" href="#" style="padding:3px;"><img src="{{ asset('img/logo_black.jpg') }}"></a>
                  </div>

                  
                </div><!-- /.container-fluid -->
            </nav>

            <div id="main" class="row">
                <!-- main content -->
                <div id="content" class="col-md-12">
                    <div>

                    </div>

                </div>

            </div>
            <div class="col-md-12">
            <h2>Socities</h2>
            <div class="center-block" style="width: 50%">
                 <form id="login-form" method="post" action="">
                    <div class="form-group">
                      <label class="form-label" >Email</label>
                      <input type="text" class="form-control" name="email"  placeholder="My Email">
                    </div>

                    <div class="form-group">
                      <label class="form-label" >Password</label>
                      <input type="password" id="login-password" class="form-control" name="password"  placeholder="Password">
                    </div>



                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                        <div class="col-md-6">
                            <!--<span class="pull-right"><a href="#" id="fgt_pwd">Forgot Password?</a></span>-->
                        </div>
                    </div>
                </form>   
             </div>
            </div>
            
            
           
            
            
            
           
        </div>
		
    <script>
        $('document').ready(function(){
            
            $('#fgt_pwd').on('click',function(){
                $('#ForgotPwdModal').modal();
                 $('#formLoginModal').modal('hide');
            });
            
            $("#fgt_pwd-form").validate({
                rules: {
                  // simple rule, converted to {required:true}
                  password: "required",
                  // compound rule
                  email: {
                    required: true,
                    email: true
                  }
                }
            }); 
            
            $('#fgt_pwd-cancel').on('click',function(){
                $("#fgt_pwd-form")[0].reset();
                $("#fgt_pwd-form label.error").remove();
                $('#ForgotPwdModal').modal('hide');
                $('#formLoginModal').modal();
            });
            
            $('#frgot_pwd').on('click',function(){
                $("#fgt_pwd-form")[0].reset();
                $("#fgt_pwd-form label.error").remove();
                $('#ForgotPwdModal').modal('hide');
                $('#formLoginModal').modal();
            });
            
            $('#fgt_pwd-form').submit(function(e){
                e.preventDefault();
                var user_email = $('#user_email').val();
                if ($("#fgt_pwd-form").valid()){
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Updating password please wait...');
                    $.ajax({
                       url: API_URL+'society/check_useremail',
                       method: "POST",
                       dataType: "json",
                       data: {email:user_email}
                   })
                    .success(function(response) {
                        $("#fgt_pwd-form").find('button[type=submit]').attr('disabled',false);
                        $("#fgt_pwd-form").find('button[type=submit]').text('Submit');
                      var result = response;
                       if(result.success){
                            isSuccess = true;
                           $( "#user_email" ).after( '<label id="user_email-success" class="text-success" for="user_email">You will be receiving an email for resetting your password shortly. Please check your email!</label>' );
                           setTimeout(function(){ location.reload(); }, 2000);
                       }else{
                           $("label#login-password-error").remove(); 
                           $("label#password-error").remove(); 
                           $( "label#user_email-error" ).remove();
                           isSuccess = false;
                        $( "#user_email" ).after( '<label id="user_email-error" class="error" for="user_email">Invalid Username!</label>' );
                       }
                   }).error(function(response){
                       return false;
                   }).then(function(response){
                       console.log(isSuccess);
                       return isSuccess;
                   });
                }
            
            });

           $("#login-form").validate({
                rules: {
                  // simple rule, converted to {required:true}
                  password: "required",
                  // compound rule
                  email: {
                    required: true,
                    email: true
                  }
                }
            }); 

            $('#login-form').submit(function(e){
                e.preventDefault();
                if ($("#login-form").valid()){
                    var data = $( this ).serializeArray();
                    data.push({name: 'client_id',value:CLIENT_ID});
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('logging in please wait..');
                    $.ajax({
                        url: API_URL+'superadmin/login',
                        method: "POST",
                        data: data
                    })
                    .success(function(data) {
                         $("#login-form").find('button[type=submit]').attr('disabled',false);
                        $("#login-form").find('button[type=submit]').text('Submit');
                        if(data.success){
                            $.ajax({
                                url: '<?php echo route('super_admin.storesession') ?>',
                                method: "POST",
                                dataType:"json",
                                data: {access_token:data.access_token,user:data.user}
                            })
                            .success(function(data) {
                                if(data.success){                                              
                                    window.location="<?php echo route('super_admin.dashboard') ?>";
                                }else{
                                    console.log('Sessin could not saved');
                                }

                            }).error(function(response){
                                console.log('Store session error');
                            });
                        }else{
                                  $("label#password-error").remove();
                                $( "label#login-password-error" ).remove();
                                 $("label#password-error").remove();
                        	$( "#login-password" ).after( '<label id="password-error" class="error" for="password">'+data.msg+'</label>' );
                        }

                    }).error(function(response){
                        $(this).find('button[type=submit]').attr('disabled',false);
                        $(this).find('button[type=submit]').text('submit');

                    });
                }
            });

            
            
           
            
            
            
            
            
            

           


            
        });

    </script>
    <footer class="footer">
            <div class="container">
              <p class="text-muted" style="margin:20px;">© Copyright 2015 {{env('PROJECT_NAME')}}</p>
            </div>
    </footer>
</body>
</html>
