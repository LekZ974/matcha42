$(document).ready(function ($) {

    var btnLike = $(".btn-like");
    var id = $("#idView").text();

    $('body').on('click', '.btn-like', function () {
        $.post('/like', {likeId : id}, function (data) {
        });
        $('.user-infos').load('/users/view/'+id+' .user-infos > *')
    })
});