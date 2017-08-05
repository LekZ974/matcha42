$(document).ready(function(){

    var $inputText = $('.chat-text');
    var $win = $('.screen-dialogs');
    var $chat = $('.dialogs');

    var scrollChat = function () {
        var chat = document.getElementById("chat");
        chat.scrollTop = chat.scrollHeight;
    };

    $inputText.focus();
    scrollChat();

    //detecte user en bas de page et set notif a read
    $(function () {

        if ($win.scrollTop() + Math.round($win.height())
            >= $chat.height()) {
            $('.dest').each( function () {
                $.post('/readNotif', {'id': $(this).data('id-message')}, function (data) {
                    $('#unread').html(data.nb);
                }, 'json');
            });
        }
        $win.scroll(function () {

            if ($win.scrollTop() + Math.round($win.height())
                >= $chat.height()) {
                console.log($win.scrollTop() + Math.round($win.height()));
                $('.dest').each( function () {
                    console.log($(this).data('id-message'));
                    $.post('/readNotif', {'id': $(this).data('id-message')}, function (data) {
                        $('#unread').html(data.nb);
                    }, 'json');
                });
            }
        });
    });

    $('#send').click(function(e){
        e.preventDefault(); // on empêche le bouton d'envoyer le formulaire

        var idDest = window.location.search.substr(4); // on sécurise les données
        var message = encodeURIComponent( $('.chat-text').val() );


        if(idDest != "" && message != ""){ // on vérifie que les variables ne sont pas vides
            $.ajax({
                url : "/chat/match?id="+idDest, // on donne l'URL du fichier de traitement
                type : "POST", // la requête est de type POST
                data : "id=" + idDest + "&message=" + message, // et on envoie nos données
                success : function (html) {
                    $chat.load('/getMessages/match?id='+idDest, function () {
                        var scrollChat = function () {
                            var chat = document.getElementById("chat");
                            chat.scrollTop = chat.scrollHeight;
                        };
                        scrollChat();
                        $('.chat-text').val("");
                    });
                }
            });

        }
    });

    function loadMessage(){

        setTimeout( function(){

            var idDest = window.location.search.substr(4); // on sécurise les données

            $chat.load('/getMessages/match?id='+idDest, function () {
                var scrollChat = function () {
                    var chat = document.getElementById("chat");
                    chat.scrollTop = chat.scrollHeight;
                };
                scrollChat();
            });
            loadMessage();
        }, 5000);

    }

    loadMessage();

});

$("#chargement").show();
$(".sidelist").load("/chat #list-chat", function(data) {
    $("#chargement").fadeOut('slow', function() {
        $(".sidelist").fadeIn(1000);
        $(".sidelist").removeClass('hidden');
    });
    $("#chargement").remove();
});

function loadList(){

    setTimeout( function(){

        $('.sidelist').load('/chat #list-chat');
        loadList();
    }, 5000);

}

loadList();

