$(document).ready(function () {

    var onglet = window.location.href;

    onglet = onglet.substring(onglet.lastIndexOf("/")+1);
    var onglet1 = document.getElementById('basic');
    var onglet2 = document.getElementById('personnal');
    var onglet3 = document.getElementById('photo');
    var onglet4 = document.getElementById('location');

    switch (onglet){
        case onglet1.id :
            onglet1.className="active";
            onglet2.className="inactive";
            onglet3.className="inactive";
            onglet4.className="inactive";
            break;

        case onglet2.id :
            onglet1.className="inactive";
            onglet2.className="active";
            onglet3.className="inactive";
            onglet4.className="inactive";
            break;

        case onglet3.id :
            onglet1.className="inactive";
            onglet2.className="inactive";
            onglet3.className="active";
            onglet4.className="inactive";
            break;

        case onglet4.id :
            onglet1.className="inactive";
            onglet2.className="inactive";
            onglet3.className="inactive";
            onglet4.className="active";
            break;
    }

    $('body').on('click', '.interest', function (e) {
        $.post('/home/personnal', { deleteInterest : $(this).text() }, function () {
        });

        $(this).fadeOut(1000, function () {
            $(this).remove();
        });
    })
});