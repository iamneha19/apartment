@section('title', 'My Flat')
@section('panel_title', 'My Flat')
@section('content')
   <?php  $user = Session::get('user',null); ?>

    <div class="col-md-12">
      Please refer members.blade.php 
    </div>
@stop
