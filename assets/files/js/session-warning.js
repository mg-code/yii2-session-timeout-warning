(function ($) {
    $.fn.sessionWarning = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist');
            return false;
        }
    };

    var defaultSettings = {
        cookieUser: '__swuid',
        cookieTimeout: '__swto',
        cookieTimeoutAbsolute: '__swato',
        extendUrl: null,
        userId: null,
        warnBefore: null,
        logoutUrl: null,
        message: null
    };
    var settings = {};

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $e = $(this);
                settings = $.extend({}, defaultSettings, $e.data(), options || {});
                settings.warnBefore = parseInt(settings.warnBefore);

                // Check every 3 seconds
                setInterval(function () {
                    methods._watch.apply($e);
                }, 3000);

                methods._onContinueClick.apply($e);
            });
        },

        /**
         * On continue button click renews user session
         * @private
         */
        _onContinueClick: function () {
            var $e = $(this);
            $e.on('click', '.continue', function () {
                var $dialog = $e.find('.modal-dialog');
                $dialog.addClass('loading');
                $.ajax({
                    method: "GET",
                    url: settings.extendUrl,
                    success: function (data) {
                        if (data.success) {
                            $e.modal('hide');
                        } else {
                            location.reload();
                        }
                    },
                    error: mgcode.helpers.request.ajaxError,
                    complete: function () {
                        $dialog.removeClass('loading');
                    }
                });
            });
        },

        /**
         * Logs out user
         * @private
         */
        _logout: function() {
            if(settings.logoutUrl) {
                window.location = settings.logoutUrl;
            } else {
                location.reload();
            }
        },

        /**
         * Checks and shows warning if needed.
         * @private
         */
        _watch: function () {
            var $e = $(this),
                userId = Cookies.get(settings.cookieUser),
                timeout = Cookies.get(settings.cookieTimeout),
                absoluteTimeout = Cookies.get(settings.cookieTimeoutAbsolute);

            timeout = timeout ? parseInt(timeout) : null;
            absoluteTimeout = absoluteTimeout ? parseInt(absoluteTimeout) : null;

            // Checks whether user is logged in.
            if (!userId || !timeout || userId != settings.userId) {
                methods._logout.apply($e);
                return;
            }

            // Current timestamp
            var timestamp = mgcode.helpers.time.getTimestamp();

            // Absolute timeout reached
            if(absoluteTimeout && (absoluteTimeout - timestamp) < 0) {
                methods._logout.apply($e);
                return;
            }

            // Timeout reached
            if(timeout && (timeout - timestamp) < 0) {
                methods._logout.apply($e);
                return;
            }

            // Calculate timestamp difference
            var difference = timeout - timestamp;

            // If modal is opened, close it.
            if (settings.warnBefore < difference) {
                $e.modal('hide');
                return;
            }

            // Format time and generate warning message
            var formatTime = mgcode.helpers.time.formatTime(timeout),
                message = settings.message.replace('{time}', formatTime);

            // Show warning
            $e.find('.message').html(message);
            if (!$e.hasClass('in')) {
                $e.modal('show');
            }
        }
    };
})(jQuery);