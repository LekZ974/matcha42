$(document).ready(function(){
    $('.chat-text').focus();

    var $win = $('.screen-dialogs');
    var $chat = $('.dialogs');

    var scrollChat = function () {
        var chat = document.getElementById("chat");
        chat.scrollTop = chat.scrollHeight;
    };

    scrollChat();

    //detecte user en bas de page et set notif a read
    $(function () {

        $win.scroll(function () {
            var $res = 0;

            if ($win.scrollTop() + Math.round($win.height())
                == $chat.height()) {
                $('.dest').each( function () {
                    console.log($(this).data('id-message'));
                    $.post('/readNotif', {'id': $(this).data('id-message')}, function (data) {
                        $('#unread').html(data.nb);
                    }, 'json');
                });
            }
        });
    });
});

