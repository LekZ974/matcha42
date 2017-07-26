$(document).ready(function ($) {

    var btnLike = $(".like-User > button");
    var id = $("#idView").text();

    btnLike.click(function () {
        $.post('/like', {likeId : id}, function (data) {

        })
    })
});