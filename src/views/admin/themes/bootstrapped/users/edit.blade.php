@extends('admin::layouts.master')
@section('title'){{ trans('admin.user.form.edit.header') }}@stop

@section('content')
    <div>
        @include('admin::users.form',
            [
                'model' => $user,
                'header' => trans('admin.user.form.edit.header'),
                'disabled' => isset($user->deleted_at) && !is_null($user->deleted_at) ? 'disabled' : '',
                'statuses' => [
                    0 => 'Disabled',
                    1 => 'Enabled'
                ]
            ] + compact('role')
        )
    </div>
@stop
