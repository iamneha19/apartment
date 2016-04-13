@extends('layouts.default')
@section('title', 'Home Page')
@section('content')

    i am the home page
    <div>
        
        <button type="button" class="btn btn-primary" id="create-society-btn">Create Society</button>
       
        <!-- Society Form Modal -->
        <div class="modal fade" id="formSocietyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create Society</h4>
                  </div>
                  <div class="modal-body">
                    <form id="society-form" method="post" action="">
                        <div class="form-group">
                          <label >Society Name</label>
                          <input type="text" class="form-control" name="name"  placeholder="Society Name">
                        </div>
                        <div class="form-group">
                          <label >Address</label>
                          <textarea class="form-control" name="address" placeholder="Address"></textarea>
                        </div>
                        <div class="form-group">
                          <label >Pincode</label>
                          <input type="text" class="form-control" name="pincode"  placeholder="Pincode">
                        </div>
<!--                        <div class="form-group">
                          <label >Blocks</label>
                          <input type="text" class="form-control" name="blocks"  placeholder="Comma seperated blocks">
                        </div>-->
                        <div class="form-group">
                          <label >My Block</label>
                          <input type="text" class="form-control" name="block"  placeholder="My Block">
                        </div>
                        <div class="form-group">
                          <label >My Flat</label>
                          <input type="text" class="form-control" name="flat_no"  placeholder="My Flat No">
                        </div>
                        <div class="form-group">
                          <label >Name</label>
                          <input type="text" class="form-control" name="first_name"  placeholder="My Name">
                        </div>
                        <div class="form-group">
                          <label >Email</label>
                          <input type="text" class="form-control" name="email"  placeholder="My Email">
                        </div>
                        
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                  </div>
                </div>
            </div>
        </div>
        
        
        
    </div>
    <script>
        $('document').ready(function(){
         
            $('#create-society-btn').on('click',function(){
                $('#formSocietyModal').modal();
            });
            
           $("#society-form").validate({
                rules: {
                  // simple rule, converted to {required:true}
                    first_name: "required",
                    society_name: "required",
                    address: "required",
                    block : "required",
                    flat_no : "required",
                  pincode:{
                    required: true,
                    number: true
                  },
                  // total_flats:{
                    // required: true,
                    // number: true
                  // },
                  
                  // compound rule
                  email: {
                    required: true,
                    email: true
                  }
                }
            }); 
            
            $('#society-form').submit(function(e){
                e.preventDefault();
                if ($("#society-form").valid()){
                    var data = $( this ).serializeArray();
                    $.ajax({
                        url: API_URL+'society/create',
                        method: "POST",
                        data: data
                    })
                    .success(function(data) {
                        location.reload(); 
                    }).error(function(response){
                        console.log('Society form error');
                    }); 
                   
               }
            });
        });
    </script>
@stop

