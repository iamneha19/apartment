@extends('layouts.sidebar')
@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@stop
@section('content')
    i am the about page
@stop

