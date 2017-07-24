$(document).ready(function ($) {

    var btnLike = $(".like-User > button");
    var id = $("#idView").text();

    console.log(id);

    btnLike.click(function () {
        $.post('/like', {likeId : id}, function (data) {
            console.log('tutu');
        })
    })
});