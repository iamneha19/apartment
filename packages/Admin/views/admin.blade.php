@section('title', 'Admin Dashboard')
@extends('admin::layouts.admin_layout')
@section('content')
<div class="col-lg-12" >
	
<!-- {{--*/  $modules = Session::get('acl.admin') /*--}}

 <div class="row">
@foreach ($modules as $module)



   @if ( $module['type'] and $module['title'] != 'Billing' )
        <div class="col-sm-4 col-md-3 module-thumbnail">
            <a href="{{ route($module['route'])}}"><div class="thumbnail">
            <div class="center-block" >
                        <i class="fa {{ $module['icon'] }}"></i>
            </div>
            <div class="caption text-center">
              <h3>{{ $module['title'] }}</h3>
            </div>
                </div>
            </a>
        </div>
  @endif
    @endforeach-->
<!--    <div class="col-sm-4 col-md-3 module-thumbnail">
            <a href="{{ route('admin.society_info')}}"><div class="thumbnail">
            <div class="center-block">
                        <i class="fa fa-home"></i>
            </div>
            <div class="caption text-center">
              <h3>Society Info</h3>
            </div>
                </div>
            </a>
        </div>-->
    </div>
</div>
@stop
