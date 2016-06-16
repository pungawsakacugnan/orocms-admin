@extends('admin::layouts.master')
@section('title'){{ trans('admin.profile.header') }}@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="active">
            <i class="fa fa-dashboard"></i> <a href="{{ route('admin.dashboard') }}">{{ trans('admin.dashboard.header') }}</a>
        </li>
        <li class="active">
            {{ trans('admin.profile.header') }}
        </li>
    </ol>
@stop

@section('content')
    <h1 class="page-header">
        {{ trans('admin.profile.header') }}
    </h1>

    <div class="row">
        <div class="col-lg-7">
            {!! Form::open( array('route'=>'admin.profile', 'method'=>'PUT', 'id'=>'form:profile', 'class'=>'form-default')) !!}
                @if (count($errors) > 0)
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-ban"></i></span></button>
                    <h4>{{ trans('admin.profile.error.header') }}</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (Session::has('status'))
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-ban"></i></span></button>
                    {!! Session::get('status') !!}
                </div>
                @endif

                <div class="form-input-group">
                    <label for="name">{{ trans('admin.profile.form.label.name') }}</label>
                    {!! Form::text('name', old('name')?old('name'):auth()->user()->name, ['id'=>'name','class'=>'form-control','required','autofocus']) !!}
                </div>

                <div class="form-input-group">
                    <label for="email">{{ trans('admin.profile.form.label.email') }}</label>
                    {!! Form::text('email', old('email')?old('email'):auth()->user()->email, ['id'=>'email','class'=>'form-control','required']) !!}
                </div>

                <br />
                <div class="alert alert-info">
                    <p>
                        {{ trans('admin.profile.form.label.password.description') }}
                    </p>

                    <br />
                    <div class="form-input-group">
                        <label for="password">{{ trans('admin.profile.form.label.password') }}</label>
                        <input type="password" name="password" id="password" class="form-control" />
                    </div>

                    <div class="form-input-group">
                        <label for="password_confirmation">{{ trans('admin.profile.form.label.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" />
                    </div>
                </div>

                <p>&nbsp;</p>
                <div class="form-group">
                    {!! Form::submit( trans('admin.profile.form.button.update'), [
                        'class' => 'btn btn-lg btn-primary form-button', 
                        'role' => 'form-button'
                    ]) !!}
                    <span class="lg hidden-xs">
                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin.profile.form.button.cancel') }}</a>
                    </span>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-default btn-block visible-xs ">
                        {{ trans('admin.profile.form.button.cancel') }}
                    </a>
                </div>

            {!! Form::close() !!}
        </div>
    </div>
@stop

@push('jquery-scripts')
    $('form').on('submit', function() {
        App.set('page_exit_confirmation', false);
        return true;
    });
    $('input').on('change', function() {
        App.set('page_exit_confirmation', true);
    });
@endpush