/**
 * Created by lekz on 24/07/17.
 */
$(document).ready(function () {

    setInterval(function(){
        $.getJSON('/lastNotif', function(data){
            $.fn.notif = function (options) {
                var settings = {
                    html : '<div class="notification animated fadeInLeft {{cls}}">\
            <div class="left">\
            <div class="icon">\
            <a href="{{href}}">{{{icon}}}\
                </div>\
                </div>\
                <div class="right">\
            <h2>{{title}}</h2>\
            <p>{{{content}}}</p>\
            <div class="hidden">{{data}}</div></a> \
            </div>\
            </div>',
                    timeout : false
                };

                var options = $.extend(settings, options);

                if (options.cls == 'message-alert')
                    settings.icon = '<i class="fa fa-envelope fa-2x" aria-hidden="true"></i>';
                else if (options.cls == 'like-alert')
                    settings.icon = '<i class="fa fa-thumbs-o-up fa-2x" aria-hidden="true"></i>';
                else if (options.cls == 'see-alert')
                    settings.icon = '<i class="fa fa-eye fa-2x" aria-hidden="true"></i>';
                else if (options.cls == 'match-alert')
                    settings.icon = '<i class="fa fa-heartbeat fa-2x" aria-hidden="true"></i>';


                return this.each(function () {
                    var $this = $(this);

                    $.getJSON('/unreadNotif', function (data) {
                        var $notifs = $('> .notifications', this);
                        var $notif = $(Mustache.render(options.html, options));
                        var objSize = function(obj) {
                            var count = 0;

                            if (typeof obj == "object") {

                                if (Object.keys) {
                                    count = Object.keys(obj).length;
                                } else if (window._) {
                                    count = _.keys(obj).length;
                                } else if (window.$) {
                                    count = $.map(obj, function() { return 1; }).length;
                                } else {
                                    for (var key in obj) if (obj.hasOwnProperty(key)) count++;
                                }

                            }

                            return count;
                        };

                        var isRead = objSize(data);

                        if (isRead != 0) {
                            if ($notifs.length == 0) {
                                $notifs = $('<div class = "notifications animated flipInX"/>');
                                $this.append($notifs);
                            }
                            $notifs.append($notif);
                            if (options.timeout) {
                                setTimeout(function () {
                                    $notif.trigger('click');
                                }, options.timeout)
                            }
                            $notif.click(function (event) {
                                event.preventDefault();
                                $this = $(this);

                                var idNot = $this.find('.hidden').text();
                                unread(idNot);
                                $(location).attr('href', $(this).find('a').attr('href'));
                            });

                            setTimeout(function () {
                                $notif.addClass('animated fadeOutLeft').slideUp(300, function () {
                                    $notif.remove();
                                    if ($notifs.prevObject == undefined) {
                                        $notifs.remove();
                                    }
                                });
                            }, 7000);
                        }
                    });
                });
            };

            $.each( data, function( key, val ) {
                if (val != null)
                {
                    var options = [];
                    $.each(this, function (key, val) {
                        options.cls = val['type']+'-alert';
                        options.title = val['type'];
                        options.content = "<a href="+val['link']+" ><img width=50 height=50 src="+val['url']+" alt=\"\">"+val['lastname']+" : "+val['message']+"</a>";
                        options.data = val['id'];
                        options.href = val['link'];
                        $('body').notif(options);
                    })
                }
            });
        }, 'html');

        addUnreadNotif();

        // var $newNotif = $('.unread');
        //
        // $newNotif.each(function () {
        //     var type = $(this).data('type');
        //     $(this).addClass(type+'-alert')
        // });

    }, 10000);

    function addUnreadNotif() {
        $.getJSON('/unreadNotif', function (data) {
            $.getJSON('/countNotif', function(data)
            {
                var count = $('#unread').html(data.nb);
            });
            $.each( data, function() {
                $('.menu-notification').remove();
                $.each(this, function (key, val) {
                    var items = [];
                    items.push( "<span class='hidden'>"+val['type']+"</span><li data-id='" + val['id'] + "' class='"+val['type']+"-alert unread'><a href="+val['link']+" ><img width=50 height=50 src="+val['url']+" alt=\"\">"+val['lastname']+" : "+val['message']+"</a></li>" );
                    $( "<div/>", {
                        "class": "menu-notification",
                        html: items.join( "" )
                    }).appendTo( '.menu-notifications' );
                });
            });


        }, 'html');
    };

    addUnreadNotif();

    function unread(id) {
        $.post('/readNotif', {'id': id}, function (data) {
            $('#unread').html(data.nb);
        }, 'json');
    }

    var elem = $('.notifications');

    elem.on("click", ".unread a", function (event) {
        $this = $(this);

        var idNot = $this.parents('.notif-row').data('id');
        unread(idNot);
        addUnreadNotif();
        $this.removeClass();
        $this.addClass('notif-row');
        $(location).attr('href', $(this).find('a').attr('href'));
    });
    $('.read-all').on('click', function (event) {
        event.preventDefault();
        $('.unread').each(function () {
            var idNot = $(this).data('id');
            unread(idNot);

        }).promise().done($(location).attr('href', $(this).attr('href')));
    });

    function addClassAlert() {
        var $newNotif = $('.unread');


        $newNotif.each(function () {
            var type = $(this).data('type');
            $(this).addClass(type+'-alert notif-row');
        });
    }

    addClassAlert();

    $('body').on('click', '.btn-delete', function (e) {
        e.preventDefault();

        var idNotif = $(this).closest(".notif-row").data('id');

        $.post('/delete', { delete : idNotif, type : 'notif' }, function () {
            $('.notif-row[data-id="'+idNotif+'"]').parent().fadeOut('slow');
        })
    });

    $('body').on('click', '[name=multiDelete]', function (e) {
        e.preventDefault();
        $(':checkbox').each(function () {
            if ($(this).is(':checked')){
                var idNotif = $(this).closest(".notif-row").data('id');

                $.post('/delete', { delete : idNotif, type : 'notif' }, function () {
                    $('.notif-row[data-id="'+idNotif+'"]').parent().fadeOut('slow');
                })
            }
        });
    });

    $('body').on('click', '[name=multiUnread]', function (e) {
        e.preventDefault();
        $(':checkbox').each(function () {
            if ($(this).is(':checked')){
                var idNotif = $(this).closest(".unread").data('id');


                if (idNotif != undefined)
                {
                    $.post('/readNotif', { id : idNotif }, function () {
                        $('.unread[data-id="'+idNotif+'"]').removeClass().addClass('notif-row');
                    })
                }
            }
        });
    });
});
