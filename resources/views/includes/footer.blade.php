    <!-- Login Form Modal -->
    <div class="modal fade" id="formLoginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="societyModalLabel">Login</h4>
              </div>
              <div class="modal-body">
                <form id="login-form" method="post" action="">
                    <div class="form-group">
                      <label >Email</label>
                      <input type="text" class="form-control" name="email"  placeholder="My Email">
                    </div>

                    <div class="form-group">
                      <label >Password</label>
                      <input type="password" id="login-password" class="form-control" name="password"  placeholder="Password">
                    </div>

                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
              </div>
            </div>
        </div>
    </div>
<div id="copyright text-right"> &copy; Copyright 2016 Society Management System</div>
<script>
        $('document').ready(function(){
            $('#login-form-btn').on('click',function(){
                $('#formLoginModal').modal();
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
                    $.ajax({
                        url: API_URL+'getAccessToken',
                        method: "POST",
                        data: data
                    })
                    .success(function(data) {
                        if(data.success){
                            $.ajax({
                                url: '<?php echo url('login', $parameters = [], $secure = null) ?>',
                                method: "POST",
                                dataType:"json",
                                data: {access_token:data.access_token}
                            })
                            .success(function(data) {
                                if(data.success){
                                    window.location="<?php echo url('/myflat', $parameters = [], $secure = null) ?>";
                                }else{
                                    console.log('Sessin could not saved');
                                }

                            }).error(function(response){
                                console.log('Store session error');
                            });
                        }else{
                            $( "#login-password" ).after( '<label id="password-error" class="error" for="password">Please check email and password.</label>' );
                        }

                    }).error(function(response){
                        console.log('Login form error');
                    });
               }
            });
        });
    </script>
