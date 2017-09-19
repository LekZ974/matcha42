$(document).ready(function ($) {

    var btnLike = $(".btn-like");
    var id = $("#idView").text();

    $('body').on('click', '.btn-like', function () {
        $.post('/like', {likeId : id}, function (data) {
        });
        $('.user-infos').load('/users/view/'+id+' .user-infos > *')
    });

    $('body').on('click', '.report', function () {
        $.post('/report', {id_user : id}, function (data) {
            console.log(id);
        });
    });

    $('body').on('click', '.blockUser', function () {
        $.post('/block', {id_user : id}, function (data) {
            console.log(id);
        });
    })
});