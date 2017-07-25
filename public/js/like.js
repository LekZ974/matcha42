function unread(id)
{
    $.post('/readNotif', {'id' : id }, function(data)
    {
        $('#unread').html(data.nb);
    }, 'json');
}

$(document).ready(function ($) {

    var btnLike = $(".like-User > button");
    var id = $("#idView").text();

    btnLike.click(function () {
        $.post('/like', {likeId : id}, function (data) {

        })
    })

    var elem = $('.notification');

    elem.click(function () {
        $elem = $(this);

        var idNot = $elem.find('li').data('id');

        unread(idNot);
    })
});