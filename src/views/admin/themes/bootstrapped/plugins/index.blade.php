@extends('admin::layouts.master')
@section('title'){{ trans('admin.plugin.header') }}@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ trans('admin.dashboard.header') }}</a>
        </li>
        <li class="active">
            {{ trans('admin.plugin.breadcrumb') }}
        </li>
    </ol>
@stop

@section('content')
    <div class="list-container">
        @if($plugins)
        <ul class="list-group">
            @foreach($plugins as $plugin)
            <div class="panel panel-info panel-custom">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $plugin->getTitle() }}</h3>
                </div>
                <div class="panel-body">
                    {{ $plugin->getDescription() }}

                    @if($url=$plugin->get('url'))
                    <p>
                        See more at <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                    </p>
                    @endif
                    @if($plugin->getAuthor())
                    <p>
                        <label>Author:</label> {{ $plugin->getAuthor() }}
                    </p>
                    @endif

                    <div role="plugin" data-plugin="{{ $plugin->getName() }}" class="margin-10x-top">
                        <div class="switch">
                            <input name="remember" id="remember" role="publish" @if($plugin->enabled){{ 'checked' }}@endif value="1" type="checkbox" />
                            <label for="remember"></label>
                            <i class="active">Enabled</i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </ul>

        {!! Form::open([
            'id' => 'plugin-form',
            'method' => 'PUT', 
            'route' => 'admin.plugins.update'
        ]) !!}
            {!! Form::hidden('plugin', null) !!}
        {!! Form::close() !!}
        @else
        <div class="alert flash-message alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-ban"></i></button>
            No plugin installed.
        </div>
        @endif
    </div>
@stop

@push('jquery-scripts')
    $('[role="publish"]').on('click', function() {
        var form = $('form#plugin-form'),
            plugin_id = $(this).closest('[role="plugin"]').data('plugin');
        form.find('[name="plugin"]').val(plugin_id);

        form.submit();
    });
@endpush