$(document).ready(function ($) {

    var btnLike = $(".like-user > button");
    var id = $("#idView").text();

    btnLike.click(function () {
        $.post('/like', {likeId : id}, function (data) {
        });
        location.reload();
    })
});