/**
 * Backend JS
 */

var App, // app helper
    BT; // Bootstrap-table helper
+function ($) {
    'use strict';

    /**
     * Custom plugins
     */
    $.fn.groupSelectLink = function() {
        function init(ul, target) {
            var value = target.groupSelect('getValue');

            toggle_visibility(ul, value);

            target.on('select.bs.group-select', function(e, value) {
                toggle_visibility(ul, value);
            });
        }

        function toggle_visibility(ul, value) {
            var value = $.trim(value).toLowerCase();

            ul.find('li').each(function(i, li) {
                var data = $(li).data('value') || '',
                    values = data.split('|');

                $(li)[~values.indexOf(value) ? 'show' : 'hide']();
            });
        }

        return $(this).each(function(i, el) {
            if (el.tagName != 'UL') return;
            var target = $('[data-id="{id}"]'.replace(/{id}/, $(el).data('link')));

            if (target.length < 1 || typeof $.fn.groupSelect != 'function') return;
            init($(el), target);
        });
    };
     
    /**
     * Main app helper
     */
    App = App || {
        // simple mobile detector agent
        is_mobile: function() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
        },

        // setter
        set: function() {
            var k = arguments[0] || false,
                v = arguments[1] || null;
            if (!k) return false;

            if (k == 'page_exit_confirmation') {
                window.onbeforeunload = (v===true) ? window.page_exit_confirmation : null;
            }

            this[k] = v;
        },

        // helpers
        cookie: function(name) {
            var today = new Date(),
                expire = new Date(),
                value = arguments[1] || false,
                days = arguments[2] || false;

            // getter
            if (value === false) {
                var context = name + "=",
                    cookies = document.cookie.split(';');

                for (var i = 0; i < cookies.length; i++) {
                    var c = cookies[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(context) == 0) return c.substring(context.length, c.length);
                }

                return null;
            }

            if (days == null || days == 0) days = 1;

            expire.setTime(today.getTime() + 36e5*24*days );
            document.cookie = name+"="+escape(value) + ";expires="+expire.toGMTString();
        },

        // load scripts
        get_script: function() {
            var a = arguments[0] || false,
                fn = arguments[1] || false,
                sync = arguments[2] || false,
                list = [];
            if (!a) return false;
            if (typeof a=='object') list = a;
            else list.push(a);

            // cache
            if (this._script_cache == undefined) this._script_cache = [];
            for (var i=0; i<list.length; i++) {
                var script = list[i],
                    ndx = this._script_cache.indexOf(script);
                if (~ndx) {
                    list.splice(ndx, 1);
                    i--;
                    continue;
                }

                this._script_cache.push(script);
            }

            // add to list
            if (sync) {
                for (var i in list) {
                    if (list[i] in document.documentElement) continue;
                    $('<script />')
                        .attr('src', list[i])
                        .appendTo($('head'));
                }
                typeof fn=='function' && fn.call(null, this, deferred_list);
            }
            else {
                var deferred_list = [];
                for (var i in list) deferred_list.push($.getScript(list[i]));

                $.when.apply($, deferred_list).done(function() {
                    typeof fn=='function' && fn.call(null, this, deferred_list);
                });
            }
        },

        // set browserstate
        browserify: function() {
            var self = this,
                arg = arguments[0] || false;
            if (!arg) return;
            var container = typeof arg=='object' ? arg : $(arg);
            container.on('click', '[role~="defer"]', function(e) {
                var a = $(this),
                    url = a.attr('href'),
                    role = a.attr('role');
                e.preventDefault();

                // on edit
                if (App.page_exit_confirmation === true) {
                    Notify.confirm({
                        options: {
                            message: 'Your most recent changes have not been saved. If you leave before saving, your changes will be lost.',
                            buttons: {
                                ok: 'Leave this page',
                                cancel: 'Please stay!'
                            },
                            callback: function() {
                                // add to browser state
                                History.pushState({dom_uid: role}, '', url);
                            }
                        }
                    });
                }
                else {
                    // add to browser state
                    History.pushState({dom_uid: role}, '', url);
                }
            });
        },

        // init
        init: function() {
            var self = this,
                callback = arguments[0] || false;

            //
            // Event listeners
            //
            $(document).ready(function() {
                var wrapper = $('body');

                // add browserstate
                self.browserify(wrapper);

                wrapper
                    // confirmations
                    .on('click', '[role~="confirm"]', function(e) {
                        var el = $(this),
                            role = el.attr('role'),
                            f;
                        e.preventDefault();

                        // get roles
                        try {
                            var roles = role.split(' ');
                            // has form?
                            if (~roles.indexOf('form-button') || ~roles.indexOf('form-control')) f = el.closest('form');

                            // ask App
                            Notify.confirm({
                                options: el.dom_fire_data(),
                                element: el,
                                form: f
                            });
                        }
                        catch(err) {}
                    })

                    // ajax calls
                    .on('click', '[role~="ajax"]', function(e) {
                        Railed.send($(this));
                    });

                // call-back :)
                typeof callback =='function' && callback.call(null, self);
            }); // document ready
        },
    };


    /**
     * Browserstate
     */
    var History = window.History;
    History.Adapter.bind(window, 'statechange', function() {
        var State = History.getState();

        if (State.url) {
            var options = {
                url: State.url,
                async: true,
                cache: false,
                beforeSend: function() {
                    App.progressor && App.progressor.start();
                },
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            var percentage = evt.loaded / evt.total;
                            App.progressor && App.progressor.set(percentage);
                        }
                    }, false);

                    return xhr;
                }
            };

            $.ajax(options)
                .done(function(data) {
                    $('#page-wrapper').html(data);
                })
                .error(function(err) {
                    console.log(err);
                    $('#page-wrapper').html('<div class="ajax-error"><h1>{error}</h1></div>'.replace(/{error}/, err.statusText));

                    // check if has redirect
                    var data = err.responseJSON || false;
                    if (data && data.redirect != undefined) document.location.href = data.redirect;
                })
                .always(function(result) {
                    // clear page exit confirmation
                    App.set('page_exit_confirmation', false);

                    // end progress bar
                    App.progressor && App.progressor.done();

                    // get roles from data
                    if (State.data && State.data.dom_uid) {
                        //
                        // Set active nav
                        //
                        var roles = State.data.dom_uid.split(' ');
                        if (~roles.indexOf('nav')) {
                            var a = $('[role="{id}"]'.replace(/{id}/, State.data.dom_uid));
                            a.closest('ul.nav').find('li.active').removeClass('active');
                            // set parent as active
                            a.closest('li[role="child-li"]').addClass('active');

                            // close dropdown menu nav when in mobile
                            $('.navbar-collapse').attr('aria-expanded', 'false')
                                .removeClass('in');
                        }
                    }
                });
        }

        //History.log(State.data, State.title, State.url);
    });


    /**
     * Bootstrap-table for listing
     */
    BT = BT || {
        options: {
            defer: false, // don't use browserstate

            // bootstrap defaults
            defaults: {
                striped: true,
                search: true,
                showColumns: true,
                silentSort: true,

                // mobile ready
                mobileResponsive: true,

                // custom query params
                queryParamsType: '',
                queryParams: function(params) {
                    if (params.pageSize != undefined) {
                        $.extend(params, {
                            limit: params.pageSize,
                            page: params.pageNumber,
                            sort: params.sortName,
                            order: params.sortOrder,
                            search: params.searchText
                        });

                        // unset
                        if (params.pageSize != undefined) delete params.pageSize;
                        if (params.pageNumber != undefined) delete params.pageNumber;
                        if (params.sortName != undefined) delete params.sortName;
                        if (params.sortOrder != undefined) delete params.sortOrder;
                        if (params.searchText != undefined) delete params.searchText;
                    }

                    // add data-bt-role doms
                    var custom_params = $.extend(params, {});
                    $('[data-bt-role*="filters"]').each(function() {
                        var data = $(this).data('filters') || {};
                        $.extend(custom_params, data);
                    });

                    return params;
                },

                // cardview builder
                cardViewBuilder: function(column, v,p, text) {
                    if (column.field=='state') return false;
                    else {
                        var prefix = '',
                            suffix = '';
                        if (p.index == 1) prefix = '<div class="card-view">';
                        else if (p.index >= p.total-1) suffix = '</div>';

                        return [prefix, v, '<br>', suffix].join('');
                    }
                }
            },
        },
        listeners: {
            add: function() {
                if (typeof arguments[0] == 'object') {
                    for (var k in arguments[0]) this.add(k, arguments[0][k]);
                    return this;
                }

                var k = arguments[0] || false;
                if (!k || typeof arguments[1] != 'function') return false;

                if (this.list == undefined) this.list = {};
                this.list[k] = arguments[1];

                return this;
            }
        },
        field_callbacks: {
            add: function() {
                var k = arguments[0] || false,
                    f = arguments[1] || false;
                if (!k) return false;

                // init
                this[k] = {};
                this[k][f] = arguments[2] || false;
            }
        },

        /*
         * Helpers
         */
        queryParams: function(params) {
            $.extend(params, {
                limit: params.pageSize,
                page: params.pageNumber,
                sort: params.sortName,
                order: params.sortOrder,
                search: params.searchText
            });

            // unset
            if (params.pageSize != undefined) delete params.pageSize;
            if (params.pageNumber != undefined) delete params.pageNumber;
            if (params.sortName != undefined) delete params.sortName;
            if (params.sortOrder != undefined) delete params.sortOrder;
            if (params.searchText != undefined) delete params.searchText;

            // add data-bt-role doms
            var custom_params = $.extend(params, {});
            $('[data-bt-role*="filters"]').each(function() {
                var data = $(this).data('filters') || {};
                $.extend(custom_params, data);
            });

            return params;
        },

        /*
         * bootstrap table initializer
         */
        init: function() {
            var self = this,
                target = arguments[0] || false,
                callback = null;
            if (!target) return;

            // dig arguments
            for (var i in arguments) {
                switch (typeof arguments[i]) {
                    // set last known callback
                    case 'function':
                        callback = arguments[i];
                        break;

                    // could be the options, who knows ;)
                    case 'object':
                        var opt = arguments[i];

                        if (opt.defaults != undefined) $.extend(this.options.defaults, opt.defaults);
                        $.extend(self.options, opt);
                        break;
                }
            }

            var _table = $(target);
            // sanity check
            try {
                var options = self.options || {};
                var _bootstrap_table = _table.bootstrapTable(options.defaults || {});
            }
            catch(err) {
                return console.log('Either Bootstrap table not found or this error -->', err);
            }

            // add to self
            this.target = _table;
            this.initialized = true;

            //
            // Custom toolbar buttons
            //
            _table.closest('.bootstrap-table').find('[data-bt-action*="remove"]').click(function () {
                var self = this,
                    options = $(this).dom_fire_data();
                bootbox.dialog({
                    animate: false,
                    title: 'Confirm Delete',
                    message: options.message || 'This will delete the selected items. Do you wish to continue?',
                    buttons: {
                        ok: {
                            label: 'Yes, Delete Selected',
                            callback: function() {
                                // id params
                                options.data.id = BT.selections;

                                // clear alerts
                                $('.alert').remove();
                                Preloader.create('.list-container', 'centered', false,true);

                                var request = Railed.send(options);
                                request.always(function(result, v) {
                                    // check response
                                    var success = result.success || result.status,
                                        message = result.statusText || (result.message || 'No message returned.');

                                    if (success) {
                                        $(self).prop('disabled', true);
                                        _table.bootstrapTable('refresh');
                                    }

                                    // notify the world!
                                    Preloader.clear(function() {
                                        // get notification options
                                        var args = BT.options.notification || {},
                                            target = args.target || '.bootstrap-table';

                                        BT.notify(message, target, success===true?'info':'warning', 'insertBefore', success===true);
                                    });
                                });
                            }
                        },
                        no: {
                            label: 'Cancel',
                            className: 'btn-primary'
                        }
                    }
                });
            });

            //
            // Event listeners
            //
            // toggle custom buttons
            _table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function() {
                $('[data-bt-role*="toggle"]').prop('disabled', !_table.bootstrapTable('getSelections').length);

                // set data
                $('[role*="bt-action"]').data('params-id', BT.selections);
            })
            // set active data field/row on clickable row
            .on('click-cell.bs.table a', function(t,f,i, row) {
                self.__active_field = f;
                self.__active_field_data = row;
                return false;
            })
            
            // additional listeners
            .on('post-body.bs.table loaded.bs.table', function(e) {
                if (e.type == 'loaded') {
                    // set display header on column field toggle dropdown
                    if (!$(this).data('__bt-col-header-init')) {
                        $('<li class="dropdown-header">Toggle Columns</li>').prependTo($('[title="Columns"] .dropdown-menu'));
                        $(this).data('__bt-col-header-init', true);
                    }
                }
                else {
                    $('[data-bt-role*="toggle"]').prop({disabled: !_table.bootstrapTable('getSelections').length});
                }

                // toggle custom aethetics
                switch ($(this).data('custom-checkbox-type')) {
                    case 'switchy':
                        $(this).find('input[type="checkbox"]').switchy({class: 'mini inline'});
                        break;
                }
            })

            // add clear button for toolbar search
            .on('search.bs.table', function(t, query) {
                var input = $(t.target).closest('.bootstrap-table').find('.fixed-table-toolbar .search input[type="text"]');
                if (input.length && query != '') {
                    var p = input.parent();
                    if (p.find('.query-clear').length < 1) {
                        $('<a />')
                            .html('<i class="fa fa-ban"></i>')
                            .addClass('query-clear')
                            .off('click').on('click', function(e) {
                                e.preventDefault();
                                $(this).parent().find('input')
                                    .val('')
                                    .trigger('keyup');

                                $(this).fadeOut('fast', function() {
                                    $(this).remove();
                                });
                            })
                            .appendTo(p);
                    }
                }
            })
            // click sink
            .off('click', 'a[data-toggle="popover"]')
            .on('click', 'a[data-toggle="popover"]', function(e) {
                e.preventDefault();

                if ($(window).width() <= 767) return false; // responsive-ish

                var row = self.__active_field_data || false,
                    f = $(this).data('field') || (self.__active_field || false),    // get data-field first, BT will get incorrect field index
                                                                                    // when you have a non-switchable/hidden field placed before the called field
                    a = $(this);
                if (!row) return;
                if (a.hasClass('po-init')) return;

                // get template parsing from known popover reference
                var tmpl = false;
                if (self.field_callbacks[f] != undefined) {
                    if (typeof self.field_callbacks[f].popover == 'function') {
                        tmpl = self.field_callbacks[f].popover(row);
                    }
                }
                if (tmpl === false) {
                    tmpl = '';
                    // parse
                    for (var k in row) {
                        if (typeof row[k]=='object' || k == 'state') continue;
                        tmpl += '<i>{title}</i><b>{value}</b>'.replace(/{title}/, k.replace('_',' ').capitalize()).replace(/{value}/, row[k]);
                    }
                    tmpl = '<span class="popup-view">' + tmpl + '</span>';
                }

                // clean up
                tmpl = tmpl.replace(/{(\w+)}/g, '');

                // init
                a.attr({
                    'data-animation': true,
                    'data-title': 'Info',
                    'data-content': tmpl,
                    'data-html': true
                });

                // pop now!
                a.data('init', true)
                    .addClass('po-init');
                a.popover('show');
            })
            // close button
            .off('click', 'button.close-detail')
            .on('click', 'button.close-detail', function() {
                _table.find('tr.active').removeClass('active');
                $(this).closest('tr.detail-view').remove();
            });

            //
            // attach custom table event listeners
            //
            if (self.listeners.list != undefined) {
                for (var k in self.listeners.list) {
                    if (k=='add') continue;

                    _table.on(k, self.listeners.list[k]);
                }
            }

            // clean up
            _table.trigger('loaded.bs.table');

            // run callback
            if (callback) callback.call(this, _table);

            return this;
        },

        /**
         * Execute Bootstrap table methods
         */
        call_method: function() {
            if (BT.initialized !== true) return;
            var method = arguments[0] || false,
                args = arguments[1] || null;
            if (!method) return;

            BT.target.bootstrapTable(method, args);
        },

        /**
         * Formatters
         */
        formatter: {
            linkable: function(value, row) {
                var uri = this.uri || (arguments[2] || false);
                if (!uri) return value;

                // get key
                var s = /{(.*?)}/.exec(uri),
                    k = s ? s[1].split('|') : false,
                    r = k ? k[0] : false;

                if (!r || row[r]==undefined) return value;
                var v = row[r];

                // map value
                if (typeof $[k[1]] == 'function') v = $[k[1]](v);

                var is_defer = BT.option('defer');
                return ['<a {role} class="link primary" href="', uri.replace(s[0], v), '">', value, '</a>']
                    .join('')
                    .replace(/{role}/, is_defer?'role="defer"' : '');
            },

            // Default "published" field formatter
            published: function(value, row, index) {
                var fmt = '<a href="javascript:void(0)" title="{title}"><i class="fa fa-{icon}"></i></a>';
                return fmt
                    .replace(/{title}/, value=='1' ? 'Disable' : 'Enable')
                    .replace(/{icon}/, value=='1' ? 'toggle-on' : 'toggle-off');
            },

            // datetime
            date: function(value, row) {
                if (typeof moment == 'function') return moment(value).calendar(new Date);
                return value;
            }
        },

        /**
         * Helpers
         */
        // notifiers :)
        notify: function() {
            var auto_close = arguments[4] || false,
                alert_id = new Date().getTime(),
                tmpl = '<div id="alert_{id}" class="alert {status_type} alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="fa fa-ban"></i></span></button><span>{content}</span></div>';

            // params
            var status_type = 'alert-' + (arguments[2] || 'info'),
                container = arguments[1] || 'body',
                pos = arguments[3] || 'prependTo';
            // add
            $(tmpl.replace(/{status_type}/, status_type)
                    .replace(/{id}/, alert_id)
                    .replace(/{content}/, arguments[0]||'No message provided!'))
                [pos]($(container));

            // auto close alert
            auto_close===true && setTimeout(function(id) {
                $('.alert#alert_' + id).fadeOut('slow', function() {
                    $(this).remove();
                })
            },5e3, alert_id);
        },
        // options getter/setter
        option: function(key) {
            if (!this.initialized) return false;

            var options = this.target.bootstrapTable('getOptions');
            // apply default
            $.extend(options, this.options);

            if (arguments[1] != undefined) return options[key] = arguments[1];
            else if (typeof arguments[1] == 'object') $.extend(options, arguments[1]);

            return options[key];
        },

        // selection
        get selections() {
            if (!this.initialized) return [];
            return $.map(this.target.bootstrapTable('getSelections'), function(row) {
                return row['id'];
            });
        }
    };


    /**
     * Bootstrap helpers
     */
    // remove popovers, the hard-way (data-trigger="click")
    $('html').on('mouseup', function(e) {
        if (!$(e.target).closest('.popover').length) {
            $('.popover').each(function() {
                var el = $(this.previousSibling);
                el.data('trigger')=='click' && el.popover('hide');
            });
        }
    });

    /**
     * Initialisations
     */
    App.init(function(self) {
        //
        // custom plugin initializations
        //
        $(function() {
            $('[data-toggle="group-select-link"]').groupSelectLink();
        });

        try {
            //
            // override Bootstrap preloading effects
            //
            var BootstrapTable = $.fn.bootstrapTable.Constructor;
            BootstrapTable.prototype.showLoading = function () {
                this.$tableLoading
                    .css({textIndent: -9999}) // hide preloader text
                    .show();

                Preloader.create('.list-container', 'centered', false,true);
            };
            BootstrapTable.prototype.hideLoading = function () {
                this.$tableLoading.hide();
                Preloader.clear(true);
            };
            BootstrapTable.prototype.load = function (data) {
                var fixedScroll = false;

                if (this.options.sidePagination === 'server') {
                    var totalRows = data.total || 0;

                    // league/fractal paginator
                    var meta = data.meta || {},
                        pagination = meta.pagination || false;
                    if (typeof pagination == 'object') {
                        totalRows = pagination.total || totalRows;
                    }

                    this.options.totalRows = totalRows;

                    fixedScroll = data.fixedScroll;
                    data = data[this.options.dataField];
                } else if (!$.isArray(data)) {
                    fixedScroll = data.fixedScroll;
                    data = data.data;
                }

                this.initData(data);
                this.initSearch();
                this.initPagination();
                this.initBody(fixedScroll);
            };

            //
            // Override groupselect formatting
            //
            var GroupSelect = $.fn.groupSelect.Constructor;
            GroupSelect.prototype.initData = function() {
                var _ = this.$el.find('[role="group-select-data"]'),
                    that = this,
                    // build data
                    items = {
                        data: [],
                        contexts: []
                    };

                if (_.length) {
                    // override formatter
                    this.options.formatter = function(value) {
                        try {
                            var _text = that.options.contexts[value];
                            if (typeof _text == 'object') _text = _text.text || '';
                            return _text;
                        } catch(e) {
                            return value;
                        }
                    };

                    _.find('li').each(function(i, el) {
                        var text = $(this).text();

                        if ($(el).attr('role') == 'separator' || $(el).hasClass('divider')) text = '|divider';
                        items.data.push(text);

                        // get value
                        items.contexts[text] = {
                            dom: el,
                            text: $(el).html()
                        };

                        if ($(el).hasClass('active')) that.options.value = text;
                    });

                    this.options.contexts = items.contexts;
                }

                //
                // get filters
                //
                this.options.filters = this.getFilters();

                //
                // Get stored value in cookie
                //
                if (this.options.allowCookie != undefined) {
                    var id = this.options.id || this.$el[0].id;

                    if ((this.options.allowCookie == '1' || this.options.allowCookie == 'true') 
                        && typeof App.cookie == 'function') {
                        var value = App.cookie('_gs-' + id);

                        if (value) {
                            value = decodeURIComponent(value);
                            // set value
                            this.options.value = value;

                            //
                            // check if filters are set in the dom element
                            //
                            var filter = this.options.filters[value] || false;
                            if (typeof filter == 'object') this.$el.data('filters', filter);
                        }
                    } // allowCookie
                }                

                if (this.options.type !== 'dropdown') {
                    this.initButtonList(items.data);
                } 
                else {
                    this.initDropdownList(items.data);
                }
            };
            GroupSelect.prototype.getFilters = function() {
                var _ = this.$el.find('[role="group-select-data"]'),
                    filters = [];

                if (_.length < 1) return {};
                _.find('li').each(function(i, el) {
                    var data = $(el).data() || {},
                        text = $(el).text();

                    if ($.trim(text) == '') return;

                    var _params = {};
                    for (var k in data) {
                        if (/filters(.*?)/.test(k)) _params[k.toLowerCase().replace(/filters/,'')] = data[k];
                    }

                    filters[text] = _params;
                });

                return filters;
            };

            var _GS_oldTrigger = GroupSelect.prototype.trigger;
            GroupSelect.prototype.trigger = function () {
                _GS_oldTrigger.apply(this, Array.prototype.slice.apply(arguments));

                if (this.options.allowCookie != undefined) {
                    var id = this.options.id || this.$el[0].id;
                    if ((this.options.allowCookie == '1' || this.options.allowCookie == 'true') 
                        && typeof App.cookie == 'function') App.cookie('_gs-' + id, this.options.value);
                }                
            };
        }
        catch(e) {}

        //
        // add progress bar implementation
        //
        self.set('progressor', typeof NProgress=='object' ? NProgress : null);

        // datetime
        $('[role*="datepicker"]').each(function() {
            var el = $(this),
                data = el.data() || {},
                params = {
                    useCurrent: false,
                    format: 'YYYY-MM-DD hh:mm A',
                    showTodayButton: true,
                };

            var opt = {};
            for (var k in data) {
                if (/params(.*?)/.test(k)) opt[k.toLowerCase().replace(/params/,'')] = data[k];
            }

            // render
            el.datetimepicker( $.extend(params, opt) );

            // events
            el.off('dp.change').on('dp.change', function(e) {
                var data = $(this).data() || false;
                if (!data) return;

                if (data.roleLink || false) {
                    var target = $('[id="{id}"]'.replace(/{id}/, data.roleLink));

                    if (target.length < 1) return;
                    switch (data.roleType) {
                        case 'min':
                            target.data('DateTimePicker').minDate(e.date);
                            break;
                        case 'max':
                            target.data('DateTimePicker').maxDate(e.date);
                            break;
                    }
                }
            });

            // update
            // setTimeout(function() {
            //     el.trigger('dp.change');
            // }, 1e3);
        });
        $('[role*="date-display"]').each(function() {
            var el = $(this),
                value = el.data('value') || false;

            if (!value) return;
            if (typeof moment == "undefined") return;

            var d = moment(value),
                now = new Date(),
                is_today = d.isSame(now, "day");

            if (d.isValid() === false) return '';
            el.text(is_today ? d.calendar(now) : d.fromNow());
        });

        // explicit form submission trigger
        $('[role*="form-submit"]').on('click', function(e) {
            e.preventDefault();

            var el = $(this),
                form = el.closest('form'),
                target = $(el.data('target') || ''), // target
                next = el.data('next') || ''; 

            if (target.length) form = target;
            if (next) {
                var next_dom = form.find('input[name="next"]');
                if (next_dom.length < 1) {
                    next_dom = $('<input type="hidden" />')
                        .prop({name: 'next'})
                        .appendTo(form);
                }

                next_dom.val(next);
            }

            form.submit();
        });

        /**
         * Sidebar listener
         */
        $(document).on('mouseleave', '.side-nav>[role*="parent"]>a', function() {
            var _ = $(window),
                is_tablet = (_.width() >= 768) && (_.width() <= 1024);
            if (is_tablet) $(this).blur();
        });
    });
  }(jQuery);

/**
 * Page edit navigate away confirmation
 */
var page_exit_confirmation = function(e) {
    e = e || window.event;
    var message = 'Your most recent changes have not been saved. If you leave before saving, your changes will be lost.';
    if (e) e.returnValue = message;

    // For Chrome, Safari, IE8+ and Opera 12+
    return message;
};
