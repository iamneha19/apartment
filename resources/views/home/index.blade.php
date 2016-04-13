<!DOCTYPE html>
<html lang="en" ng-app="frontend">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="author" content="Scotch">

        <title>Sahkari - @yield('title')</title>

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
        .type-radio-group .form-group{margin-bottom: 0px;}

        </style>
    </head>
    <body>
        <div class="container">

            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container">
                  <!-- Brand and toggle get grouped for better mobile display -->
                  <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                      <span class="sr-only">Toggle navigation</span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Sahkari</a>
                  </div>

                  <!-- Collect the nav links, forms, and other content for toggling -->
                  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                      <li><a href="javascript:void(0);" id="login-form-btn">Login</a></li>
                       <li><a href="javascript:void(0);"  id="create-society-btn">Create Society</a></li>
                    </ul>
                  </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>

            <div id="main" class="row" style="padding-top:90px;">
                <!-- main content -->
                <div id="content" class="col-md-12">

                    <div>

                        <!-- Society Form Modal -->
                        <div class="modal fade" id="formSocietyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" id="register-close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Create Society</h4>
                                  </div>
                                  <div class="modal-body">
                                    <form id="society-form" method="post" action="">
                                        <div class="form-group">
                                            <label class="form-label">Society Name</label>
                                            <input type="text" class="form-control" name="name"  placeholder="Society Name">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address" placeholder="Address"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" >Pincode</label>
                                            <input type="text" class="form-control" name="pincode"  placeholder="Pincode">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" >My Block</label>
                                            <input type="text" class="form-control" name="block" maxlength="10"  placeholder="My Block">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" >My Flat</label>
                                            <input type="text" class="form-control" name="flat_no" maxlength="4"  placeholder="My Flat No">
                                        </div>
                                        <div class="form-group type-radio-group">
                                            <label class="form-label">Occupancy</label>
                                            <div class="form-group">
                                                <div class="radio-inline">
                                                    <label>
                                                        <input type="radio" name="relation"   value="owner" >
                                                        Owner
                                                    </label>
                                                </div>
                                                <div class="radio-inline">
                                                    <label>
                                                        <input type="radio" name="relation"   value="tenant" >
                                                        Tenant
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="form-label" >Email</label>
                                          <input type="text" class="form-control" id="registration-email" name="email"  placeholder="My Email">
                                        </div>
                                        <div class="form-group">
                                          <label class="form-label" >First Name</label>
                                          <input type="text" class="form-control" name="first_name"  placeholder="First Name">
                                        </div>
                                        <div class="form-group">
                                          <label class="form-label" >Last Name</label>
                                          <input type="text" class="form-control" name="last_name"  placeholder="Last Name">
                                        </div>
                                        <div class="form-group">
                                          <label class="form-label" >Mobile No</label>
                                          <input type="text" class="form-control" name="contact_no"  placeholder="Contact No">
                                        </div>

                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <button type="button" id="register-cancel" ng-click="closeSocietyForm()" class="btn btn-default">Cancel</button>
                                    </form>
                                  </div>
                                </div>
                            </div>
                        </div>



                    </div>

                </div>

            </div>
            <div class="col-md-12">
            <h2>Socities</h2>
             <div id="society_list">

             </div>
            </div>
            <!-- Login Form Modal -->
            <div class="modal fade" id="formLoginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" id="login_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="societyModalLabel">Login</h4>
                      </div>
                      <div class="modal-body">
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
                                    <span class="pull-right"><a href="#" id="fgt_pwd">Forgot Password?</a></span>
                                </div>
                            </div>
                        </form>
                      </div>
                    </div>
                </div>
            </div>

            <!-- Forgot Password modal--->
            <div class="modal fade" id="ForgotPwdModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" id="frgot_pwd" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="societyModalLabel">Forgot Password!</h4>
                      </div>
                      <div class="modal-body">
                        <form id="fgt_pwd-form" method="post" action="">
                            <div class="form-group">
                              <label class="form-label" >Email</label>
                              <input type="text" class="form-control" name="email" id="user_email"  placeholder="Please enter your email address!">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" id="fgt_pwd-cancel" class="btn btn-default">Cancel</button>
                                </div>
                            </div>
                        </form>
                      </div>
                    </div>
                </div>
            </div>

            <!---->

            <!-- Society Confirmmation Modal -->
            <div class="modal fade" id="emailConfirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="societyModalLabel">Confirmation</h4>
                      </div>
                      <div class="modal-body">
                          <p id="warning-msg">Do you want to continue?</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default confirmed-no">No</button>
                        <button type="button" class="btn btn-primary confirmed-yes">Yes</button>
                      </div>
                    </div>
                </div>
            </div>

            <!-- Successful Society Registration Modal -->
            <div class="modal fade" id="societySuccessModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="societyModalLabel">Thank you for registering society.</h4>
                      </div>
                      <div class="modal-body">
                          <p>Your society has been created successfully! Please check your email for login details.</p>
                      </div>
                    </div>
                </div>
            </div>
        </div>

    <script>
        $('document').ready(function(){
            $('#login-form-btn').on('click',function(){
                $('#formLoginModal').modal();
            });

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
                        url: API_URL+'getAccessToken',
                        method: "POST",
                        data: data
                    })
                    .success(function(data) {
                         $("#login-form").find('button[type=submit]').attr('disabled',false);
                        $("#login-form").find('button[type=submit]').text('Submit');
                        if(data.success){
                            $.ajax({
                                url: '<?php echo route('login') ?>',
                                method: "POST",
                                dataType:"json",
                                data: {access_token:data.access_token,user:data.user,socities:data.socities,acl:data.acl}
                            })
                            .success(function(data) {
                                if(data.success){
                                    window.location="<?php echo route('conversations') ?>";
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
/*                             if(data.deactive)
                            {
                                $( "#login-password" ).after( '<label id="password-error" class="error" for="password">User is deactivate.</label>' );
                            }else{
                                $( "#login-password" ).after( '<label id="password-error" class="error" for="password">Please check email and password.</label>' );
                            }
 */                        }

                    }).error(function(response){
                        $(this).find('button[type=submit]').attr('disabled',false);
                        $(this).find('button[type=submit]').text('submit');

                    });
                }
            });

            $('#create-society-btn').on('click',function(){
                $('#formSocietyModal').modal();
            });

            closeSocietyForm = function(){
                $("#society-form")[0].reset();
                $("#society-form label.error").remove();
                $('#formSocietyModal').modal('hide');
            };

            $('#register-cancel').on('click',function(){
                $("#society-form")[0].reset();
                $("#society-form label.error").remove();
                $('#formSocietyModal').modal('hide');
            });

            $('#register-close').on('click',function(){
                $("#society-form")[0].reset();
                $("#society-form label.error").remove();
                $('#formSocietyModal').modal('hide');
            });

           // varible to check ajax email valiadation is applied or not
           var ajaxEmailValidation = true;
           jQuery.validator.addMethod("validateUserEmail", function(value, element) {
               var isSuccess;
               $.ajax({
                    url: API_URL+'society/checkemail',
                    method: "POST",
                    dataType: "json",
                    data: {email:value}
                })
                .success(function(r) {
                   var result = r.response;
                    if(result.success){
//                        console.log(result.success);
                        isSuccess = true;
//                        return true;
                    }else{
                        $('#warning-msg').html(result.msg);
                        $('#formSocietyModal').modal('hide');
                        $('#emailConfirmModal').modal();
                        isSuccess = false;
//                        return false;
                    }
                }).error(function(response){
                    return false;
                }).then(function(response){
                    console.log(isSuccess);
                    return isSuccess;
                });

//                if(true)
//                {
//                    return true;
//                }else{
//                    $('#formSocietyModal').modal('hide');
//                    $('#emailConfirmModal').modal();
//                    return false;
//                }
            }, 'Email address is already taken');

            $('.confirmed-yes').on('click',function(){
                // Disable ajax validation if user want to continue
                ajaxEmailValidation = false;
//                $( "#registration-email" ).rules( "remove", "validateUserEmail" ); // Remove ajax email validation
                $( "#registration-email" ).rules( "remove", "remote" ); // Remove ajax email validation
                $('#emailConfirmModal').modal('hide');
                $('#formSocietyModal').modal();
                $('#registration-email-error').remove();
                console.log($('#registration-email').rules());
            });

            $('.confirmed-no').on('click',function(){
                $('#emailConfirmModal').modal('hide');
                $('#formSocietyModal').modal();
            });

            $('#emailConfirmModal').on('hide.bs.modal', function () {
                $("#formSocietyModal").css("overflow-y", "auto"); // 'auto' or 'scroll'
            });

            $('#registration-email').focusout(function() {
                if(!ajaxEmailValidation){
//                  $( "#registration-email" ).rules( "add", "validateUserEmail" );
//                  $( "#registration-email" ).rules( "add", {
//                      remote:{
//                            url: API_URL+'society/checkemail',
//                            type: "post",
//                            data: {
//                              email: function() {
//                                return $( "#registration-email" ).val();
//                              }
//                            },
//                            success:function(r) {
//                                var result = r.response;
//                                 if(result.success){
//                                     return true;
//                                 }else{
//                                     $('#warning-msg').html(result.msg);
//                                     $('#formSocietyModal').modal('hide');
//                                     $('#emailConfirmModal').modal();
//                                     return false;
//                                 }
//                             }
//                          }
//                  } );

                }
            });

           $("#society-form").validate({
//                onkeyup: false,
                rules: {
                  // simple rule, converted to {required:true}
                    name: "required",
                    first_name: "required",
                    last_name: "required",
                    relation: "required",
                    address: "required",
                    block : "required",
                    flat_no:{
                        required: true,
                        number: true
                    },
                    contact_no:{
                        required:true,
                        maxlength:10,
                        minlength:10,
                        number:true,
                    },
                    pincode:{
                      required: true,
                      number: true,
                      minlength: 6
                    },
                  // total_flats:{
                    // required: true,
                    // number: true
                  // },

                  // compound rule
                  email: {
                    required: true,
                    email: true,
//                    validateUserEmail:true,
//                    remote:{
//                            url: API_URL+'society/checkemail',
//                            type: "post",
//                            data: {
//                              email: function() {
//                                return $( "#registration-email" ).val();
//                              }
//                            },
//                            success:function(r) {
//                                var result = r.response;
//                                 if(result.success){
//                                     return true;
//                                 }else{
//                                     $('#warning-msg').html(result.msg);
//                                     $('#formSocietyModal').modal('hide');
//                                     $('#emailConfirmModal').modal();
//                                     return false;
//                                 }
//                             }
//                          }
                  }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "relation"  ) {
                        $( ".form-group.type-radio-group" ).append( error );
                    }else {
                      error.insertAfter(element);
                    }
                }
            });

            $('#society-form').submit(function(e){
                e.preventDefault();
                if ($("#society-form").valid()){
                    $(this).find('button[type=submit]').attr('disabled',true);
                    $(this).find('button[type=submit]').text('Registering society  please wait..');
                    var data = $( this ).serializeArray();
                    $.ajax({
                        url: API_URL+'society/create',
                        method: "POST",
                        data: data
                    })
                    .success(function(r) {
                        $('#society-form').find('button[type=submit]').attr('disabled',false);
                        $('#society-form').find('button[type=submit]').text('Submit');
                        var result = r.response;
                        if(result.success){
                            closeSocietyForm();
                            $('#societySuccessModal').modal();

                        }else{
                            console.log('returned false');

                            return false;
                        }


                    }).error(function(response){
                        console.log('Society form error');
                    });

               }
            });


            $.ajax({
                    url:API_URL+'society/list',
                    method:'GET',
                    success:function(r){
                            $.each(r,function(i,r){
                                    $('#society_list').append('<div class="media"><div class="media-body"><a href="#"><h4 class="media-heading">'+r.name+'</h4></a><div>'+r.address+' - '+r.pincode+'</div></div></div>');
                            });

                    }
                });
        });

    </script>
    <footer class="footer">
            <div class="container">
              <p class="text-muted" style="margin:20px;">&copy; Sahkari</p>
            </div>
    </footer>
</body>
</html>
