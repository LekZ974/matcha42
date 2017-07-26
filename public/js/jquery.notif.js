/**
 * Created by lekz on 24/07/17.
 */
(function ($) {

    setInterval(function(){
        $.get('/lastNotif', function(data){
            $.fn.notif = function (options) {
                var settings = {
                    html : '<div class="notification animated fadeInLeft {{cls}}">\
            <div class="left">\
            <div class="icon">\
            {{{icon}}}\
                </div>\
                </div>\
                <div class="right">\
            <h2>{{title}}</h2>\
            <p>{{{content}}}</p>\
            </div>\
            </div>',
                    timeout : false
                };
                if (options.cls == 'message-alert')
                    settings.icon = '<i class="fa fa-envelope fa-2x" aria-hidden="true"></i>';
                if (options.cls == 'like-alert')
                    settings.icon = '<i class="fa fa-thumbs-o-up fa-2x" aria-hidden="true"></i>';
                if (options.cls == 'see-alert')
                    settings.icon = '<i class="fa fa-eye fa-2x" aria-hidden="true"></i>';
                if (options.cls == 'match-alert')
                    settings.icon = '<i class="fa fa-heartbeat fa-2x" aria-hidden="true"></i>';

                var options = $.extend(settings, options);

                return this.each(function () {
                    var $this = $(this);
                    var $notifs = $('> .notifications', this);
                    var $notif = $(Mustache.render(options.html, options));

                    if ($notifs.length == 0){
                        $notifs = $('<div class = "notifications animated flipInX"/>');
                        $this.append($notifs);
                    }
                    $notifs.append($notif);
                    if (options.timeout){
                        setTimeout(function () {
                            $notif.trigger('click');
                        }, options.timeout)
                    }
                    $notif.click(function (event) {
                        event.preventDefault();
                        $notif.addClass('animated fadeOutLeft').slideUp(300, function () {
                            $notif.remove();
                            if ($notifs.prevObject == undefined){
                                $notifs.remove();
                            }
                        });

                    })
                });
            };

            function getOptions(data) {

                var options = [];

                data.each(function () {
                    options.cls = $(this).find('span').text()+'-alert';
                    options.title = 'you have a '+$(this).find('span').text();
                    options.content = $(this).find('a').html();
                    $('body').notif(options);
                })
            };

            var $data = $(data).find('.last-notification');

            if ($data.length != 0)
            {
                $data.each(getOptions($data));
            }

        }, 'html');

        $.get('/unreadNotif', function (data) {
            $.getJSON('/countNotif', function(data)
            {
                var count = $('#unread').html(data.nb);
            });

            $('#notifications > .notifications').html(data);

        }, 'html');

    }, 10000);

})(jQuery);


    function unread(id) {
        $.post('/readNotif', {'id': id}, function (data) {
            $('#unread').html(data.nb);
        }, 'json');
    }

    var elem = $('.notifications');

    console.log(elem);

    elem.on("click", ".unread", function (event) {
        event.preventDefault();
        $this = $(this);

        var idNot = $this.data('id');
        console.log(idNot);
        unread(idNot);
        $refresh = $('.container-fluid > .notifications');
        $('.container-fluid > .notifications').remove();
        $.get('/allNotif', function (data) {
            $.getJSON('/countNotif', function (data) {
                var count = $('#unread').html(data.nb);
            });
            $('.container-fluid').append(data);
        }, 'html');
        $(location).attr('href', $(this).find('a').attr('href'));
        console.log($(this).find('.unread > a').attr('href'));
    });
