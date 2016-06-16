/**
 * JS Helpers
 */

// module extensions
String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};
String.prototype.str_repeat = function() {
    var r = '',
        n = arguments[0] || 1;
    for (var i=0;i<(n?n:1);i++) r += this;
    return r;
};

var Railed, // railed
    Preloader, // preloader
    Notify; // notification
+function ($) {
    'use strict';

    // return a formatted DOM element data, ala-rails
    $.fn.dom_fire_data = function() {
        var result = {
                data: {}
            },
            params = $(this).data() || {};
        if (params) {
            var map = {
                method: 'type'
            };

            for (var k in params) {
                var v = params[k];
                k = map[k] || k;
                if (/params/.test(k)) {
                    k = k.replace(/params/g, '').replace(/\-/, '');
                    result.data[k.toLowerCase()] = v;
                }
                else result[k] = v;
            }
        }

        return result;
    };


    /**
     * Notification helper
     */
    Notify = Notify || {
        // show confirmation box
        confirm: function(param) {
            if (typeof param!='object') return;
            var options = param.options || {},
                buttons = options.buttons || {};

            bootbox.dialog({
                animate: false,
                title: options.title || 'Confirmation',
                message: options.message || 'Do you wish to continue?',
                buttons: {
                    ok: {
                        label: buttons.ok || 'Continue',
                        callback: function() {
                            var fn = options.callback || false;
                            if (typeof fn=='function') fn.call(null, options);
                            else if (typeof window[fn]=='function') window[fn].call(null, options);
                            else if (param.form) {
                                // attach
                                if (options.data) {
                                    for (var k in options.data) {
                                        $('<input type="hidden" />')
                                            .prop('name', k)
                                            .val(options.data[k])
                                            .appendTo(param.form);
                                    }
                                }
                                // change method
                                if (options.method) $('[name="_method"]').val(options.method);

                                param.form.submit();
                            }
                            // is ajax request?
                            else if (options.url) {
                                param.element && Railed.send(param.element);
                            }
                        }
                    },
                    no: {
                        label: buttons.cancel || 'Cancel',
                        className: 'btn-primary'
                    }
                }
            });
        },

        // notification box
        alert: function(param) {
            if (typeof param!='object') return;
            var options = param.options || {},
                buttons = options.buttons || {};

            bootbox.dialog({
                animate: false,
                title: options.title || 'Notification',
                message: options.message || 'Well, i\'m being called here. What\'s up?',
                buttons: {
                    ok: {
                        label: buttons.ok || 'Close',
                        callback: function() {
                            var fn = options.callback || false;
                            if (typeof fn=='function') fn.call(null, options);
                            else if (typeof window[fn]=='function') window[fn].call(null, options);
                        }
                    }
                }
            });
        },
    };

    /**
     * Preloader helper
     */
    Preloader = Preloader || {
        // constructor
        create: function() {
            var target = arguments[0] || false, // target dom element context
                cls_fxs = arguments[1] || '', // additional classes
                pos = arguments[2] || 'appendTo', // dom insert method
                can_disable = arguments[3] || false; // wrap with a disabled container

            if (can_disable===true) cls_fxs += ' has-disabled';
            var _preloader = $('<div />')
                .append('<i></i>'.str_repeat(4))
                .addClass('preloader ' + cls_fxs);

            if (can_disable===true)
                _preloader = $('<div class="control-disabled" />').append(_preloader);

            if (!target) return _preloader;
            _preloader[pos]($(target));
        },
        // destroy
        clear: function() {
            var _preloader = $('.preloader');
            if (_preloader.hasClass('has-disabled')) _preloader = _preloader.closest('.control-disabled');
            if (arguments[0]===true) return _preloader.remove(); // remove 'ora mismo'!
            setTimeout(function(fn) {
                typeof fn=='function' && fn.call(null, this);
                _preloader.fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5e2, arguments[0]);
        }
    };

    /**
	 * rails.js like implementation
	 */
	Railed = Railed || {
		send: function(src) {
			if (!src) return false;

			var is_dom = src[0] || false, // assumes this object key is attributed to as a DOM element
				options = is_dom ? src.dom_fire_data() : src,
				url = options.url || false,
				is_form = is_dom ? src[0].tagName=='FORM' : false,
				//
				// assumes if src is not a DOM object, request is data only (unless overidden with option.data_only)
				//
				data_only = is_dom ? false : (options.data_only==undefined ? true : options.data_only),
				callbacks = options.callbacks || false;

			if (!url && is_form) url = options.url = src.attr('action');
			if (!url) return false;

			// check if target element is set
			if (options.src || false) {
				if (options.src.length) {
					src = options.src;
					data_only = false;
				}
			}

			// pre-post event
			if (is_dom && !data_only) src.trigger('railed.beforeSend', options);
			else if (callbacks && typeof callbacks.beforeSend == 'function') callbacks.beforeSend(options);

			// set type
			options.dataType = $.ajaxSettings && $.ajaxSettings.dataType;
			// set headers
	        options.headers = {
                'X-CSRF-TOKEN': $('meta[property="orocms:uuid"]').attr('content')
	        };

	        // set data if form and data attribs are empty
	        if (is_form && Object.keys(options.data).length < 1) {
	        	var data = src.serializeArray();
	        	try {
		        	for (var k in data) {
		        		var o = data[k];
		        		options.data[o.name] = o.value;
		        	}
		        }
		        catch(err) {}
	        }

			// post action
			var request = $.ajax(options);
			if (data_only) return request;
			request.done(function(data, statusText, xhr) {
					if (is_dom) src.trigger('railed.afterSend', data, statusText, xhr, options);
					else if (callbacks && typeof callbacks.afterSend == 'function') callbacks.afterSend(data, statusText, xhr, options);
				})
				.error(function(err) {
					if (is_dom) src.trigger('railed.onError', err);
					else if (callbacks && typeof callbacks.onError == 'function') callbacks.onError(err);
				})
				.always(function(data, statusText, xhr) {
					// after-post event
					if (is_dom) src.trigger('railed.onComplete', data, statusText, xhr, options);
					else if (callbacks && typeof callbacks.onComplete == 'function') callbacks.onComplete(data, statusText, xhr, options);
				});
		}
	};

    /**
     * Custom plugins
     */
    //
    // Create a pretty toggle for checkbox
    //
    $.fn.switchy = function() {
        var defaults = {
                class: ''
            },
            args = arguments[0];
        if (typeof args == 'object') $.extend(defaults, args);

        function _switch(dom) {
            var _ = $(dom);

            if (arguments[1] == 'remove') {
                if (_.attr('data-switchy-status') == 'on') {
                    var c = _.closest('.switch'),
                        input = c.find('input[type="checkbox"]').detach();

                    if (input.length) input.removeAttr('data-switchy-status');
                    c.replaceWith(input);
                }

                return;
            }

            var _id = dom.id=='' ? dom.name + (Math.random() * (new Date).getMilliseconds()) : dom.id,
                p = _.prev()
                c = $('<div />')
                    .addClass('switch ' + defaults.class)
                    .append(
                        $('<label />')
                            .attr('for', _id)
                    )
                    [p.length < 1 ? 'prependTo' : 'insertBefore'](p.length < 1 ? _.parent() : _);

            _.prop({id: _id})
                .attr('data-switchy-status', 'on')
                .detach().prependTo(c);
        }

        return $(this).each(function() {
            // check if checkbox
            if ($(this).attr('type') == 'checkbox') {
                _switch(this, args);
            }
            else {
                $(this).find('input[type="checkbox"]').each(function(i) {
                    _switch(this, args);
                });
            }
        });
    };
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
