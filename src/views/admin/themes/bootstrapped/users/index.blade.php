@extends('admin::layouts.master')
@section('title'){{ trans('admin.user.header') }}@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="active">
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> {{ trans('admin.dashboard.breadcrumb') }}</a>
        </li>
        <li class="active">
            <a href="{{ route('admin.users.index') }}">{{ trans('admin.user.breadcrumb') }}</a>
        </li>
    </ol>
@stop

@push('header')
    <script src="{{ asset( theme('admin.assets', 'js/bootstrap-group-select/bootstrap-group-select.min.js') ) }}"></script>
@endpush

@section('content')
    <div id="content-wrapper" class="list-container module section">
        <div id="toolbar">
            <a role="button" data-toggle="tooltip" data-placement="bottom" title="{{ trans('admin.user.list.button.create') }}" href="{!! route('admin.users.create') !!}" class="btn margin-5x-right btn-lg btn-primary">
                <i class="glyphicon-fw-2x glyphicon glyphicon-plus"></i>
            </a>

            <div class="btn-group">
                <div id="list:type"
                    data-id="visibility"
                    data-toggle="group-select"
                    data-allow-cookie="true"
                    data-type="dropdown"
                    data-bt-role="filters"
                    data-primary-class="btn btn-default">

                    <ul role="group-select-data" class="dropdown-menu">
                        <li class="active">{{ trans('admin.user.list.dropdown.visibility.all') }}</li>
                        <li role="separator"></li>
                        <li data-filters-published="1"><i class="bullet-status status-success"></i> {{ trans('admin.user.list.dropdown.visibility.active') }}</li>
                        <li data-filters-published="0"><i class="bullet-status"></i> {{ trans('admin.user.list.dropdown.visibility.disabled') }}</li>
                        <li role="separator"></li>
                        <li data-filters-deleted="1"><i class="bullet-status status-inactive"></i> {{ trans('admin.user.list.dropdown.visibility.deleted') }}</li>
                    </ul>
                </div>
                <div class="btn-group">
                    <button type="button" 
                        class="btn btn-default dropdown-toggle" 
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                        data-bt-role="toggle"
                        disabled>
                        <i class="fa-fw glyphicon glyphicon-cog"></i><span class="caret"></span>
                    </button>

                    <ul class="dropdown-menu" data-toggle="group-select-link" data-link="visibility">
                        <li data-value="display all|active|disabled"><a href="#"
                            data-bt-action="remove"
                            data-url="{!! route('admin.users.remove') !!}"
                            data-method="DELETE">
                            {{ trans('admin.user.list.button.delete_all') }}</a></li>
                        <li data-value="deleted">
                            <a href="#"
                                role="ajax bt-action" 
                                data-url="{{ route('admin.users.restore') }}"
                                data-method="PATCH">
                                {{ trans('admin.user.list.button.restore') }}</a>
                        </li>
                        <li data-value="deleted" role="separator" class="divider"></li>
                        <li data-value="deleted">
                            <a href="#"
                                data-bt-action="remove"
                                data-params-force_delete="1"
                                data-url="{!! route('admin.users.remove') !!}"
                                data-method="DELETE">
                                {{ trans('admin.user.list.button.purge') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <table id="table-accounts"
                class="table table-no-bordered break-word extended"

                data-sort-order="desc"
                data-url="{!! route('admin.users.index') !!}"
                data-data-field="data"
                data-pagination="true"
                data-side-pagination="server"
                data-columns-hidden="['created_at', 'role']"
                data-page-list="[10, 20, 50, 100]"

                data-toolbar="#toolbar">
            <thead>
            <tr>
                <th data-field="state" data-checkbox="true" data-formatter="BT.formatter.state"></th>
                <th data-field="name" data-sortable="true" data-switchable="false" data-uri="/admin/users/{id}/edit" data-formatter="BT.formatter.username">
                    {{ trans('admin.user.list.header.name') }}
                </th>
                <th data-field="email" data-sortable="true">
                    {{ trans('admin.user.list.header.email') }}
                </th>
                <th data-field="role" data-align="center" data-formatter="BT.formatter.role">
                    {{ trans('admin.user.list.header.role') }}
                </th>
                <th data-field="created_at" data-sortable="true" data-width="180">
                    {{ trans('admin.user.list.header.date_added') }}
                </th>
                <th data-field="published" data-align="center" data-card-view-only="true" data-formatter="BT.formatter.get_status">
                    {{ trans('admin.user.list.header.published') }}
                </th>
                <th data-field="id" data-align="center" data-visible="false" data-width="80" data-sortable="true">
                    {{ trans('admin.user.list.header.id') }}
                </th>
            </tr>
            </thead>
        </table>
    </div>
@stop

@push('jquery-scripts')
    // load bootstrap table
    BT.init('#table-accounts', {
        notification: {
            target: $('#content-wrapper')
        }
    }, function(table) {
        // add popover render callback
        BT.field_callbacks.add('name', 'popover', function(row) {
            var tmpl = '<span class="popup-view" data-pos="relative"><p style="color:red;">This item has been marked as deleted. To <strong>undelete</strong> this record, click the <strong>Restore</strong> button.</p><i>Deleted At:</i><b>{deleted_at}</b><button class="edit btn btn-primary" role="ajax" data-url="/admin/users/{id}" data-method="PATCH">Restore</button>&nbsp;<button role="confirm" data-url="/admin/users/{id}" data-title="Force Delete" data-method="DELETE" data-message="This will be gone forever. Are you really sure you want to delete this account?" data-params-force_delete="1" class="btn btn-default">Delete Forever</button></a></span>';
            // parse
            for (var k in row) tmpl = tmpl.replace(new RegExp('{'+k+'}', 'g'), row[k]);
            return tmpl;
        });

        // event listener
        $('#content-wrapper')
            .on('click', '.popover button, [role*="bt-action"]', function() {
                var el = $(this);
                $('.alert').remove(), $('.popover').popover('hide');

                // events
                el.off('railed.beforeSend railed.onError railed.onComplete')
                    .on('railed.beforeSend', function(r,s) {
                        Preloader.create('.list-container', 'centered', false,true);
                    })
                    .on('railed.onComplete', function(e, result) {
                        console.log('done');

                        $('.alert').remove();
                        Preloader.clear(function() {
                            // check response
                            var success = result.success || result.status,
                                message = result.statusText || (result.message || 'No message returned.');

                            BT.notify(message, BT.options.notification.target, success===true?'info':'warning', 'insertBefore', success===true);

                            BT.target.bootstrapTable('refresh');
                        });
                    });
            });
    });
    $.extend(BT.formatter, {
        // title
        username: function(value, row) {
            var text = BT.formatter.linkable(value, row, this.uri),
                status_type;

            if (row.deleted) {
                status_type = 'inactive';
                text = '<a class="link" role="link" data-toggle="popover" data-trigger="click">{value}</a>'.replace(/{value}/, value);
            }
            else {
                if (row.published) status_type = 'success';
                else return text;
            }

            return ['<span class="bullet-status status-{type}"></span>'
                .replace(/{type}/, status_type), text].join('');
        },
        // format role
        role: function(value, row, index) {
            return row.role || 'user';
        },
        // format publishing
        get_status: function(value, row) {
            var text = +value ?
                    '{{ trans('admin.defaults.list.status.published') }}' :
                    '{{ trans('admin.defaults.list.status.unpublished') }}',
                status_type = +value ? 'primary' : 'bordered'

            // soft deletes
            if (row.deleted_at) {
                status_type = 'inactive';
                text = '{{ trans('admin.defaults.list.status.deleted') }}';
            }

            return '<span class="label label-{type}">{title}</span>'
                .replace(/{type}/, status_type)
                .replace(/{title}/, text);
        },
        state: function(value, row) {
            var list_type = $('[id="list:type"]').groupSelect('getValue');
            if ($.trim(list_type).toLowerCase() == 'deleted') return value;

            return row.deleted_at || row.id=='{!! auth()->user()->id !!}' ? {disabled: true} : value;
        }
    });

    // list option
    $('[id="list:type"]')
        .on('select.bs.group-select', function(e, value) {
            var gs = $(this).data('bs.groupSelect'),
                filters = gs.options.filters || {};

            // update data
            var filter = $.extend(filters[value], {
                page: 1 // reset
            });
            $(this).data('filters', filter);

            BT.call_method('refresh', {query: filter});
        });
@endpush
