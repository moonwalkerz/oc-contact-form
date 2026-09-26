/**
 * MoonWalkerz Contact - captcha loader.
 *
 * Renders reCAPTCHA v2/v3/Enterprise, hCaptcha and Turnstile widgets
 * explicitly, runs invisible/score widgets before the October AJAX
 * framework submits the form, and resets widgets after each request
 * (provider tokens and math questions are single use).
 */
(function () {
    'use strict';

    var widgets = [];
    var apiReady = false;
    var lastSubmitter = null;

    var REFRESH_MS = 100000; // Provider tokens expire after ~120 seconds.

    function resolveApi(path) {
        if (!path) {
            return null;
        }

        return path.split('.').reduce(function (carrier, part) {
            return carrier ? carrier[part] : null;
        }, window);
    }

    function wrapperOf(widget) {
        return widget.el.closest('.mm-captcha-field');
    }

    function setToken(widget, token) {
        widget.token = token || '';
        widget.tokenTime = token ? Date.now() : 0;

        var wrapper = wrapperOf(widget);
        var field = wrapper && wrapper.querySelector('.mm-captcha-token');

        if (field) {
            field.value = widget.token;
        }
    }

    function tokenIsFresh(widget) {
        return widget.token && Date.now() - widget.tokenTime < REFRESH_MS;
    }

    function collect() {
        document.querySelectorAll('[data-mm-captcha]').forEach(function (el) {
            if (el.getAttribute('data-mm-initialised')) {
                return;
            }

            var cfg;

            try {
                cfg = JSON.parse(el.getAttribute('data-mm-captcha'));
            } catch (e) {
                return;
            }

            el.setAttribute('data-mm-initialised', '1');

            var widget = {
                el: el,
                cfg: cfg,
                form: el.closest('form'),
                id: null,
                token: '',
                tokenTime: 0,
                queued: null,
                bypass: false
            };

            widgets.push(widget);

            if (cfg.mode !== 'math' && apiReady) {
                renderWidget(widget);
            }
        });
    }

    function widgetForForm(form) {
        for (var i = 0; i < widgets.length; i++) {
            if (widgets[i].form === form) {
                return widgets[i];
            }
        }

        return null;
    }

    /* ------------------------------------------------------------------ *
     * Math captcha
     * ------------------------------------------------------------------ */

    function refreshMath(widget) {
        var wrapper = wrapperOf(widget);
        var handler = wrapper && wrapper.getAttribute('data-mm-refresh');

        if (!handler) {
            return;
        }

        var options = {
            success: function (data) {
                if (!data || !data.id) {
                    return;
                }

                var question = widget.el.querySelector('.mm-captcha-math-question');
                var cid = widget.el.querySelector('.mm-captcha-math-id');
                var input = widget.el.querySelector('.mm-captcha-math-input');

                if (question) {
                    question.textContent = data.question;
                }
                if (cid) {
                    cid.value = data.id;
                }
                if (input) {
                    input.value = '';
                }
            }
        };

        if (window.oc && window.oc.ajax) {
            window.oc.ajax(handler, options);
        } else if (window.jQuery && window.jQuery.request) {
            window.jQuery.request(handler, options);
        }
    }

    /* ------------------------------------------------------------------ *
     * Token providers
     * ------------------------------------------------------------------ */

    function renderWidget(widget) {
        var api = resolveApi(widget.cfg.api);

        if (!api || widget.id !== null) {
            return;
        }

        if (widget.cfg.mode === 'score') {
            // reCAPTCHA v3 / Enterprise: nothing to draw, just execute.
            widget.id = 'score';
            execute(widget);
            widget.timer = window.setInterval(function () {
                execute(widget);
            }, REFRESH_MS);
            return;
        }

        var params = {};

        Object.keys(widget.cfg.params || {}).forEach(function (key) {
            params[key] = widget.cfg.params[key];
        });

        params.callback = function (token) {
            setToken(widget, token);

            if (widget.queued !== null) {
                var submitter = widget.queued;
                widget.queued = null;
                submitForm(widget, submitter);
            }
        };

        params['expired-callback'] = function () {
            setToken(widget, '');
        };

        params['error-callback'] = function () {
            widget.queued = null;
            setToken(widget, '');
        };

        try {
            widget.id = api.render(widget.el, params);
        } catch (e) {
            widget.id = null;
        }
    }

    function execute(widget, onDone) {
        var api = resolveApi(widget.cfg.api);
        var done = onDone || function () {};

        if (!api) {
            done(false);
            return;
        }

        if (widget.cfg.mode === 'score') {
            var run = function () {
                api.execute(widget.cfg.params.sitekey, { action: widget.cfg.params.action })
                    .then(function (token) {
                        setToken(widget, token);
                        done(true);
                    })
                    .catch(function () {
                        done(false);
                    });
            };

            if (api.ready) {
                api.ready(run);
            } else {
                run();
            }

            return;
        }

        if (widget.id === null) {
            done(false);
            return;
        }

        try {
            api.execute(widget.id);
        } catch (e) {
            done(false);
        }
    }

    function needsExecute(widget) {
        if (widget.cfg.mode === 'score' || widget.cfg.mode === 'invisible') {
            return true;
        }

        return (widget.cfg.params && widget.cfg.params.appearance) === 'execute';
    }

    function submitForm(widget, submitter) {
        var form = widget.form;

        widget.bypass = true;

        if (form.requestSubmit) {
            form.requestSubmit(submitter && form.contains(submitter) ? submitter : undefined);
        } else {
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        }
    }

    // Capture phase on document: runs before October's delegated
    // [data-request] submit handler, so the request can wait for the token.
    document.addEventListener('submit', function (event) {
        var widget = widgetForForm(event.target);

        if (!widget || widget.cfg.mode === 'math') {
            return;
        }

        if (widget.bypass) {
            widget.bypass = false;
            return;
        }

        if (!needsExecute(widget) || tokenIsFresh(widget)) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        var submitter = event.submitter || lastSubmitter;

        if (widget.cfg.mode === 'score') {
            execute(widget, function (ok) {
                if (ok) {
                    submitForm(widget, submitter);
                }
            });
            return;
        }

        widget.queued = submitter || false;
        execute(widget);
    }, true);

    document.addEventListener('mousedown', function (event) {
        if (event.target && event.target.closest) {
            lastSubmitter = event.target.closest('button, input[type="submit"], input[type="image"]');
        }
    }, true);

    /* ------------------------------------------------------------------ *
     * Reset after AJAX requests
     * ------------------------------------------------------------------ */

    function reset(widget) {
        if (widget.cfg.mode === 'math') {
            refreshMath(widget);
            return;
        }

        var api = resolveApi(widget.cfg.api);

        setToken(widget, '');

        if (widget.cfg.mode === 'score') {
            execute(widget);
        } else if (api && api.reset && widget.id !== null) {
            try {
                api.reset(widget.id);
            } catch (e) {
                /* nothing to do */
            }
        }
    }

    function onAjaxAlways(target) {
        if (!target || target.tagName !== 'FORM') {
            return;
        }

        widgets.forEach(function (widget) {
            if (widget.form === target) {
                reset(widget);
            }
        });
    }

    // Dispatched by the October CMS AJAX framework on the requesting form.
    document.addEventListener('ajax:always', function (event) {
        onAjaxAlways(event.target);
    });

    /* ------------------------------------------------------------------ *
     * Boot
     * ------------------------------------------------------------------ */

    window.mmCaptchaOnload = function () {
        apiReady = true;

        widgets.forEach(function (widget) {
            if (widget.cfg.mode !== 'math') {
                renderWidget(widget);
            }
        });
    };

    function boot() {
        collect();

        if (window.MutationObserver) {
            new window.MutationObserver(function (mutations) {
                for (var i = 0; i < mutations.length; i++) {
                    if (mutations[i].addedNodes.length) {
                        collect();
                        return;
                    }
                }
            }).observe(document.documentElement, { childList: true, subtree: true });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    window.mmCaptcha = {
        reset: function () {
            widgets.forEach(reset);
        },
        rescan: collect,
        widgets: widgets
    };
}());
