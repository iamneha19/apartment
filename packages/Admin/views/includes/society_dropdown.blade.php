{{--*/ $socities =  Session::get('socities') /*--}}
<select id='my-societies' class='form-control' style='margin: 8px 0px;'>
    @foreach ($socities as $socitey)
        <option value="{{ $socitey['id'] }}" {{ (Session::get('user.society_id') == $socitey['id']) ? 'selected' : ''}} >{{ $socitey['name'] }}</option>
    @endforeach
</select>
<script>
    $('document').ready(function(){

        $('#my-societies').on('change', function() {
                var society_id= $("#my-societies option:selected").val();

                $.ajax({
                    url: API_URL+'society/switch',
                    method: "POST",
                    data: {access_token:ACCESS_TOKEN,society_id:society_id}
                })
                .success(function(result) {
                     var data = result.response; // to get api result
                    if(data.success){
                        $.ajax({
                            url: '<?php echo route('switch') ?>',
                            method: "POST",
                            dataType:"json",
                            data: {user:data.user,acl:data.acl}
                        })
                        .success(function(data) {
                            if(data.success){
                                window.location= data.redirect_url;
                            }else{
                                console.log('Sessin could not saved');
                            }

                        }).error(function(response){
                            console.log('Store session error');
                        });
                    }else{
                        console.log('switch error');
                    }
                }).error(function(response){
                    console.log('Society switch error');
                });
        });

    });
</script>
