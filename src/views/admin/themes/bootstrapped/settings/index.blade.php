@extends('admin::layouts.master')
@section('title'){{ trans('admin.plugin.header') }}@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ trans('admin.dashboard.header') }}</a>
        </li>
        <li class="active">
            {{ trans('admin.settings.breadcrumb') }}
        </li>
    </ol>
@stop

@section('content')
<div class="row" id="settings-page">
    @if(count($errors))
        <div class="col-lg-12">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" id="button:save-settings"><span aria-hidden="true"><i class="fa fa-ban"></i></span></button>
                <span>Unable to save.</span>
            </div>
        </div>
    @endif

    <div class="col-lg-12">
        <div class="header-group">
            <div>
                <h1 class="page-header">
                    {{ trans('admin.settings.header') }}
                </h1>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="module tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#general" aria-controls="content" role="tab" data-toggle="tab">General</a></li>
                <li role="presentation"><a href="#plugins" aria-controls="content" role="tab" data-toggle="tab">Plugins</a></li>
                <li role="presentation"><a href="#modules" aria-controls="content" role="tab" data-toggle="tab">Modules</a></li>
            </ul>

            <div class="tab-content">
                <!-- General Settings //-->
                <div role="tabpanel" class="tab-pane active" id="general">
                    {!! Form::open([
                        'class' => 'form-default',
                        'method' => 'PUT',
                        'route' => 'admin.settings.update'
                    ]) !!}
                        <div class="section">
                            {{--
                            // Get all themes
                            --}}
                            @define $themes = Theme::all();

                            @if($themes)
                            <div class="form-group">
                                {!! Form::label('site_theme', 'Site Theme') !!}
                                {!! Form::select('settings[site_theme]', $themes, $settings->site_theme, ['id' => 'site_theme', 'class' => 'form-control']) !!}
                                {!! $errors->first('site_theme', '<ul class="text-danger"><li>:message</li></ul>') !!}
                            </div>
                            @endif

                            <br />
                            {!! Form::submit( trans('admin.settings.form.button.save'), [
                                'class' => 'btn btn-lg btn-success form-button',
                                'role' => 'form-button'
                            ]) !!}
                        </div>
                    {!! Form::close() !!}
                </div>

                <!-- Plugins //-->
                <div role="tabpanel" class="tab-pane" id="plugins">
                    <div class="section">
                        {{--
                        // Get all plugins
                        --}}
                        @define $plugins = Plugin::all();

                        @if($plugins)
                        <div class="list-group">
                            @foreach($plugins as $plugin)
                            <div class="panel panel-info panel-custom" id="plugin{{ $plugin->getName() }}">
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

                                    <ul class="list-inline margin-10x-top" role="component" data-role-type="plugin" data-plugin="{{ $plugin->getName() }}">
                                        @if($plugin->installed())
                                        <li class="pull-left no-padding">
                                            <a href="javascript:;" role="button uninstall" class="btn btn-default">{{ trans('admin.settings.form.button.uninstall') }}</a>
                                        </li>
                                        <li class="pull-left margin-10x-left">
                                            <div class="switch">
                                                <input name="remember" id="remember-{{ $plugin->getName() }}" role="publish" @if($plugin->enabled){{ 'checked' }}@endif value="1" type="checkbox" />
                                                <label for="remember-{{ $plugin->getName() }}"></label>
                                                <i class="active">Enabled</i>
                                            </div>
                                        </li>
                                        @else
                                        <li class="no-padding">
                                            <a href="javascript:;" role="button install" class="btn btn-primary">{{ trans('admin.settings.form.button.install') }}</a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="alert flash-message alert-warning alert-dismissable">
                            No plugins has ever been installed.
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Modules //-->
                <div role="tabpanel" class="tab-pane" id="modules">
                    <div class="section">
                        {{--
                        // Get all modules
                        --}}
                        @define $modules = Module::all();

                        @if($modules)
                        <ul class="list-group">
                            @foreach($modules as $module)
                            <div class="panel panel-info panel-custom" id="module{{ $module->getName() }}">
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

                                    <ul class="list-inline margin-10x-top" role="component" data-role-type="module" data-module="{{ $module->getName() }}">
                                        @if($module->installed())
                                        <li class="pull-left no-padding">
                                            <a href="javascript:;" role="button uninstall" class="btn btn-default">{{ trans('admin.settings.form.button.uninstall') }}</a>
                                        </li>
                                        <li class="pull-left margin-10x-left">
                                            <div class="switch">
                                                <input name="remember" id="remember-{{ $module->getName() }}" role="publish" @if($module->enabled){{ 'checked' }}@endif value="1" type="checkbox" />
                                                <label for="remember-{{ $module->getName() }}"></label>
                                                <i class="active">Enabled</i>
                                            </div>
                                        </li>
                                        @else
                                        <li class="no-padding">
                                            <a href="javascript:;" role="button install" class="btn btn-primary">{{ trans('admin.settings.form.button.install') }}</a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @endforeach
                        </ul>
                        @else
                        <div class="alert flash-message alert-warning alert-dismissable">
                            No modules installed. So sad :(
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('jquery-scripts')
    var loadComponent = function(url, data) {
        var $container = $('.module.tabs');
        var callback = arguments[2] || false;
        var save_button = $('[id="button:save-settings"]');
        var d = $.Deferred();

        Railed.send({
            url: url,
            data: data,
            method: 'PUT',
            data_only: false,
            callbacks: {
                beforeSend: function() {
                    Alerts.clear(true);
                    save_button.prop('disabled', true);
                    Preloader.create($container, 'centered', false,true);
                },
                afterSend: function(res) {
                    Alerts.show(res.message || '{{ trans('admin.settings.message.settings_updated') }}',
                        $('#settings-page'), 'info', 'insertBefore', res.success===true);
                },
                onError: function(err) {
                    Alerts.show(err.statusText, $('#settings-page'), 'warning', 'insertBefore');
                },
                onComplete: function(status) {
                    d.resolve(status);

                    save_button.prop('disabled', false);
                    Preloader.clear(true);
                }
            }
        });

        return d.promise();
    };

    $('#settings-page').on('click', '[role="publish"], [role*="install"]', function() {
        var $component = $(this).closest('[role~="component"]');
        var type = $component.data('roleType');

        if (!type) return;

        var target_uris = {
            'plugin': '{{ route('admin.plugins.update') }}',
            'module': '{{ route('admin.modules.update') }}'
        };
        var data = {
            redirect: '{{ route('admin.settings.index') }}'
        };
        // add type
        data[type] = $component.data(type);

        // get role
        var role = $(this).attr('role') || '';
        var action = role.match(/\buninstall|install\b/);
        var uninstalling;
        var fn;

        if (action) {
            data['action'] = action[0];

            if (/\buninstall\b/.test(role)) {
                uninstalling = true;

                fn = (function() {
                    var d = $.Deferred();
                    Notify.confirm({
                        options: {
                            title: 'Uninstall',
                            message: ['This will uninstall the selected', type, ' and associated data will be forever lost.<br><br>Do you wish to continue?'].join(' '),
                            callback: function() {
                                loadComponent(target_uris[type], data)
                                    .then(function(s) {
                                        d.resolve(s);
                                    });
                            }
                        }
                    });

                    return d.promise();
                })();
            }
        }

        if (!uninstalling) fn = loadComponent(target_uris[type], data);
        fn.then(function(status) {
            var target = ['#',type,data[type]].join('');

            $.get('{{ route('admin.settings.index') }}', function(response) {
                var $response = $(response);
                $(target).replaceWith($response.find(target));

                // update nav
                if (type == 'module') $('.side-nav').replaceWith($response.find('.side-nav'));
            });
        });
    });
@endpush
