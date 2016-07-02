@extends('admin::layouts.master')
@section('title'){{ trans('admin.module.header') }}@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ trans('admin.dashboard.header') }}</a>
        </li>
        <li class="active">
            {{ trans('admin.module.breadcrumb') }}
        </li>
    </ol>
@stop

@section('content')
    <div class="list-container">
        @if($modules)
        <ul class="list-group">
            @foreach($modules as $module)
            <div class="panel panel-info panel-custom">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $module->getTitle() }}</h3>
                </div>
                <div class="panel-body">
                    {{ $module->getDescription() }}

                    @if($module->getAuthor())
                    <p>
                        <label>Author:</label> {{ $module->getAuthor() }}
                    </p>
                    @endif
                    <p role="module" data-module="{{ $module->getName() }}">
                        <div class="switch">
                            <input name="remember" id="remember-{{ $module->getName() }}" disabled role="publish" @if(!$module->isStatus(0)){{ 'checked' }}@endif value="1" type="checkbox" />
                            <label for="remember-{{ $module->getName() }}"></label>
                            <i class="active"> Enabled</i>
                        </div>
                    </p>
                </div>
            </div>
            @endforeach
        </ul>

        {!! Form::open([
            'id' => 'module-form',
            'method' => 'PUT', 
            'route' => 'admin.modules.update'
        ]) !!}
            {!! Form::hidden('module', null) !!}
        {!! Form::close() !!}
        @endif
    </div>
@stop
