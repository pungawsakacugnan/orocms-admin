@extends('admin::layouts.master')
@section('title'){{ trans('admin.dashboard.header') }}@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="active">
            <i class="fa fa-dashboard"></i> {{ trans('admin.dashboard.breadcrumb') }}
        </li>
    </ol>
@stop

@section('content')
@stop
