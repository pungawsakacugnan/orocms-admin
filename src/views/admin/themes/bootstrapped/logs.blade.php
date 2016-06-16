@extends('admin::layouts.master')
@section('title'){{ trans('admin.logs.header') }}@stop

@section('breadcrumb')
    <ol class="breadcrumb">
        <li>
            <a href="/admin"><i class="fa fa-dashboard"></i> {{ trans('admin.dashboard.breadcrumb') }}</a>
        </li>
        <li class="active">{{ trans('admin.logs.breadcrumb') }}</li>
    </ol>
@stop

@section('content')
    <div class="module section">
        <div id="toolbar">
            <div id="list:type"
                data-toggle="group-select"
                data-type="dropdown"
                data-primary-class="btn btn-default"
                data-data="All Log Levels, |divider, Debug, Info, Notices, |divider, Errors, Alerts, Warnings, |divider, Critical"
                data-value="All Log Levels">
            </div>          
        </div>

        <table id="table-logs"
                class="table table-no-bordered break-word extended"
                
                data-toggle="table"
                data-url="{!! route('admin.logs.index') !!}"
                data-pagination="true"

                data-toolbar="#toolbar">
            <thead>
            <tr>
                <th data-field="date" data-sortable="true" data-width="250" data-switchable="false" data-formatter="BT.formatter.log_date">
                    {{ trans('admin.logs.list.header.date') }}
                </th>
                <th data-field="level" data-width="100" data-align="center" data-sortable="true">
                    {{ trans('admin.logs.list.header.level') }}
                </th>
                <th data-field="message">
                    {{ trans('admin.logs.list.header.message') }}
                </th>
            </tr>
            </thead>
        </table>
    </div>
@stop

@push('jquery-scripts')
    // load bootstrap table
    BT.init('#table-logs', function(table) {
        table.on('click-cell.bs.table', function(t,f,i, row, el) {
            if (f != 'date') return;
            var p = el.closest('tr');
            p.toggleClass('active');

            if (p.next().hasClass('detail-view')) p.next().remove();
            else {
                var tmpl = '<button type="button" class="close-detail close" aria-label="Close"><span aria-hidden="true"><i class="fa fa-ban"></i></span></button><h4>Log Details</h4><span class="u-group"><u><i>Date:</i><b>{date}</b></u><u><i>Log Level:</i><b>{level}</b></u></span><i>Message:</i><b>{message}</b>{stack_trace}{add_info}';
                // parse
                for (var k in row) {
                    var v = row[k];
                    if (v && typeof v=='object') {
                        // additional info
                        k = 'add_info';

                        var data = [];
                        for (var i in v) {
                            data.push(i +': ' + v[i]);
                        }
                        v = '<i>Additional Information:</i><b>{v}</b>'.replace(/{v}/, data.join('<br/>'));
                    }
                    // stack trace
                    else if (k == 'stack_trace' && $.trim(v) != '') {
                        v = '<i>Stack Trace:</i><b style="font-size:12px;color:#676767;max-height:300px;overflow-y:auto;">{v}</b>'.replace(/{v}/, v);
                    }

                    tmpl = tmpl.replace('{'+k+'}', v);
                }

                // clean-up
                tmpl = tmpl.replace(/{(\w+)}/g, '');

                // affix
                $('<td />')
                    .attr('colspan', p.find('td').length || 0)
                    .insertAfter(p)
                    .append(tmpl)
                    .wrap('<tr class="detail-view" />');
            }
        });
    });
    BT.formatter.log_date = function(value, row) {
        return '<a href="javascript:;" class="link primary">' +value+ '</a>';
    };

    App.get_script([
        '{{ asset( theme('admin.assets', 'js/bootstrap-group-select/bootstrap-group-select.min.js') ) }}'
    ], function() {
        $('[id="list:type"]').on('select.bs.group-select', function(e, value) {
            var map = {
                'Debug': 'DEBUG',
                'Info': 'INFO',
                'Notices': 'NOTICE',
                'Alerts': 'ALERT',
                'Warnings': 'WARNING',
                'Errors': 'ERROR',
                'Critical': 'CRITICAL'
            };
            BT.call_method('filterBy', map[value]==undefined ? {} : {level: map[value]});
        });
    });
@endpush
