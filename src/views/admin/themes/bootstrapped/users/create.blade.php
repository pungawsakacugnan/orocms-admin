@extends('admin::layouts.master')
@section('title'){{ trans('admin.user.form.create.header') }}@stop

@section('content')
    <div>
        @include('admin::users.form', 
        	[
	            'header' => trans('admin.user.form.create.header'),
                'disabled' => false,
	            'statuses' => [
	                0 => 'Disabled',
	                1 => 'Enabled'
	            ]
        	]
        )
    </div>
@stop
