@extends('admin::layouts.master')
@section('title')
    500
@stop

@section('content-header')
    <h1 class="page-header">
        500
    </h1>
@stop

@section('content')
    {{ $exception->getMessage() }}
@stop
