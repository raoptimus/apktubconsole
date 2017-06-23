var acc = {
    main: new function () {
        this.inputLen = function () {
            $('form input[type=text],textarea').each(function () {
                var $this = $(this);
                var max = +($this.data("val-length-max") || -1);
                var min = +($this.data("val-length-min") || -1);
                if (max == -1 && min == -1) {
                    return;
                }
                var $label = $this.parent().find("label:eq(0)");
                if ($label.length == 0) {
                    $label = $this.parent().parent().find("label:eq(0)");
                    if ($label.length == 0) {
                        return;
                    }
                }
                var $sup = $("<sup>");
                $label.prepend($sup);

                $this.keyup(function () {
                    var len = $this.val().length;
                    $sup.text(len);

                    $sup.removeAttr("class");
                    if (len < min) {
                        $sup.addClass("text-danger");
                    } else if (len > max) {
                        $sup.addClass("text-warning");
                    } else {
                        $sup.addClass("text-success");
                    }
                }).keyup();
            });
        }
    },
    jira: new function () {
        this.send = function (el) {
            var tr = el.parent().parent();
            var tds = tr.find('td');
            var key = $($(tds[0]).find('.glyphicon')[0]).attr('data-level');
            if (key < 2) {
                key = 1;
            } else if (key < 4) {
                key = 2;
            } else if (key < 5) {
                key = 3;
            } else if (key < 7) {
                key = 4;
            } else {
                key = 5;
            }
            var time = $(tds[1]).html();
            var server = $(tds[2]).html();
            var tag = $(tds[3]).html();
            var msg = $(tds[4]).html();
            var url = 'https://workflowboard.com/jira/secure/CreateIssueDetails!init.jspa?pid=10501$reporter=ra&issuetype=1&summary=' + encodeURIComponent('Ошибка в компоненте ' + tag) + '&priority=' + key + '&description=' + encodeURIComponent(time + " Произошла ошибка \"" + msg + "\" В компоненте " + tag + " На сервере " + server);
            //actually bad way to do it
            var win = window.open(url, '_blank');
            win.focus();
/*            $('#hidden-link').attr('href',url);
            $('#hidden-link').show();
            $('#hidden-link').trigger('click');*/
/*good way to do, but need CORS setup
            var data = {
                "fields": {
                    "project":
                    {
                        "key": ""
                    },
                    "summary": "Ошибка в компоненте " + tag,
                    "priority": key,
                    "description": time + " Произошла ошибка \"" + msg + "\" В компоненте " + tag + " На сервере " + server,
                    "issuetype": {
                        "id": "1"
                    }
                }
            };
            $.post('https://workflowboard.com/jira/rest/api/2/issue/', data).done(function( response ) {
                console.log(response);
            });*/
        };
    },
    sortable: new function () {
        this.deleteFile = function (el) {
            url = el.attr('data-url');
            $.get(url).done(function( data ) {
                if (data.type == 'success') {
                    el.parent().parent().hide(300, function() {
                        el.parent().parent().remove();
                    });
                } else {
                    acc.alert.danger(data.message)
                }
            });
        };
        this.sort = function (url) {
            var tmp_array = [];
            $('.image-sorter').each(function(k,v) {
                tmp_array.push($(v).attr('data-id'));
            });
            $.get(url, {o: JSON.stringify(tmp_array)});
        }
    },
    alert: new function () {
        this.info = function (message) {
            show(message, "info");
        };
        this.success = function (message) {
            show(message, "success");
        };
        this.warning = function (message) {
            show(message, "warning");
        };
        this.danger = function (message) {
            show(message, "danger");
        };
        this.show = function(type, message) {
            switch (type) {
                case "success":
                case "warning":
                case "danger":
                    break;
                case "error":
                    type = "danger";
                    break;
                default: type = "info";
            }
            show(message, type);
        };
        function show(message, type) {
            $alert.removeAttr("class");
            $alert.addClass("alert alert-" + type);
            $alert.text(message).fadeIn();

            setTimeout(function () {
                $alert.fadeOut();
            }, 1000);
        }

        var $alert = $("#alert");
        if ($alert.length == 0) {
            $alert = $('<div id="alert" class="alert" role="alert" />').css({
                "display": "none"
            });
            $("body").prepend($alert);
        }

    },
    logger: new function () {
        window['DEBUG'] = /BetaTester/.test(document.cookie.toString());

        this.log = function (a, b, c, d) {
            callLog(console.log, arguments);
        };
        this.warn = function (a, b, c, d) {
            callLog(console.warn, arguments);
        };
        this.error = function (a, b, c, d) {
            callLog(console.error, arguments);
        };
        function callLog(call, args) {
            if (!DEBUG) {
                return;
            }
            if (!('console' in window)) {
                if (JSON && JSON.stringify) {
                    alert(JSON.stringify(args));
                }
                return;
            }
            if (args.callee.caller) {
                [].unshift.call(args, "TTU ");
            }

            ((Function.prototype.bind)
                ? Function.prototype.bind.call(call, console)
                : Function.prototype.apply.call(call, console, args))
                .apply(this, args);
        }
    },
    ready: function () {
        $(document).ajaxComplete(function() {
            $("img.lazy").lazyload();
        });
        $("img.lazy").lazyload();

        $('form[autocomplete=on]').each(function () {
            var $form = $(this);
            $form.find("input[type=submit]").remove();
            $form.find('input,select,textarea,radio').on('change', function () {
                $form.submit();
            });
        });
        acc.main.inputLen();
        $('a[title]').tooltip();

        $('a.grid-action').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();

            var $this = $(this);
            var f = $this.data("action") && $this.data("action") == "post" ? $.post : $.get;
            var href = $this.prop("href");

            f(href).done(function (data) {
                acc.alert.show(data.type, data.message);
                if (data.type == "success") {
                    var ops = data.options || {};
                    var after = ops.after || "";

                    switch (after) {
                        case "reload": {
                            var pjId = $this.closest('.pjax').prop('id');
                            $.pjax.reload('#' + pjId);
                            acc.ready();
                        }
                            break;
                        case "toggleActive": {
                            if ($this.hasClass("active")) {
                                $this.removeClass("active");
                            } else {
                                $this.addClass("active");
                            }
                        }
                            break;
                    }

                }
            }).fail(function () {
                acc.alert.danger("Server return error");
            });

            return false;
        });

        $("li.nav-tab-lang").click(function() {
            var $this = $(this);
            $this.parent().closest('li').before($this);
        });

        $('.translitTrigger').on('change paste input', function() {
            var lang = $(this).attr('data-lang');
            $.ajax({
                url: $(this).attr('data-url'),
                data: {
                    'q'     : $(this).val(),
                    'lang'  : lang
                },
                success: function(data) {
                    $('.translitGetter_' + lang).val(data);
                }
            });

        });

        $('.sorter-image-delete').on('click', function() {
            acc.sortable.deleteFile($(this));
        });

        $('.delete-confirm').on('click', function(e) {
            if (confirm("Are you sure?")) {
                var url = $(this).attr('href');

                $.post( url, function( data ) {
                    if (data.type == 'success') {
                        acc.alert.success(data.message);
                        location.reload();
                    } else {
                        acc.alert.warning(data.message);
                    }
                });
            }
            return false;
        });
        $('.jira-link').on('click', function(e) {
            e.preventDefault();
            acc.jira.send($(this));
        });

        $('#storage-storagetype').on('change',function(){
            if ($(this).val() != 'swift') {
                $('.swift-fields').hide(300);
                $('.non-swift-fields').show(300);
            } else {
                $('.non-swift-fields').hide(300);
                $('.swift-fields').show(300);
            }
        });

        $('#sortableGrid').on('sortableSuccess',function(){
            location.reload();
        });


        $('#countriesCheckboxes input[data-region-checkbox]').change(function() {
            $('#collapseRegion' + $(this).data('region-checkbox') + ' :checkbox').prop('checked', $(this).is(':checked'));
        });

        $('#importCountriesModal button[data-save=true]').click(function() {
            var countries = $('#importCountriesModal textarea').val().split(/[\r\n,.\s]+/);
            if (countries) {
                $('#countriesCheckboxes input:checkbox:checked').prop('checked', false);
            }
            var errors = [];

            for (var k in countries) {
                var country = countries[ k ].toUpperCase();
                if (!country) {
                    continue;
                }

                var checkbox = $('#countriesCheckboxes input:checkbox[data-country=' + country + ']');
                if (checkbox.length == 1) {
                    checkbox.prop('checked', true);

                    var region = checkbox.parents('div.collapse');
                    if (region.length == 1) {
                        if (! region.hasClass('in')) {
                            region.collapse('show')
                        }
                        var regionCheckbox = region.prev().find('input:checkbox[data-region-checkbox]')
                        if (! regionCheckbox.is(':checked')) {
                            regionCheckbox.prop('checked', true)
                        }
                    }
                } else {
                    errors.push(country);
                }
            }

            $('#importCountriesModal div.help-block').html(
                errors.length > 0
                    ? 'not found: ' + errors.join(', ')
                    : ''
            );

            // всё ок, можно скрыть
            if (errors.length == 0) {
                $('#importCountriesModal').modal('hide')
            }
        });
    }
};

$(document).ready(acc.ready);
