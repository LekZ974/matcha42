$(document).ready(function ($) {
    $(document).on('click', '[data-toggle="lightbox"][data-gallery="gallery"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox({
            alwaysShowClose: true,
            onShown: function () {
            },
            onNavigate: function (direction, itemIndex) {
            }
        });
    });

    $(document).ready(function(){
        var maxChars = $("#textInput");
        var max_length = maxChars.attr('maxlength');
        if (max_length > 0) {
            maxChars.bind('keyup', function(e){
                length = new Number(maxChars.val().length);
                counter = max_length-length;
                $("#sessionNum_counter").text(counter);
            });
        }
    });

    $('#edit-nav').ready(function () {

        var onglet = window.location.href;

        onglet = onglet.substring(onglet.lastIndexOf("/")+1);
        var onglet1 = document.getElementById('basic');
        var onglet2 = document.getElementById('personnal');
        var onglet3 = document.getElementById('photo');
        var onglet4 = document.getElementById('location');

        console.log(onglet1);
        console.log(onglet);
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
    });


    //TENTATIVE DENVOI image profil FAIL a cause varible FILE vide
    // $(document).on('change', '#avatarUser', function () {
    // //     $('#formSidebar').submit();
    // // })
    //
    // $("#avatarUser").on('change', function(e) {
    //     e.preventDefault();
    //
    //     var file_data = $("#avatarUser").prop("files")[0];
    //     var form_data = new FormData();
    //
    //     console.log(form_data);
    //     console.log(file_data);
    //     form_data.append("file", file_data);
    //     $.ajax({
    //         url: "/home/photo",
    //         cache: false,
    //         contentType: false,
    //         processData: false,
    //         data: form_data,
    //         type: 'post',
    //         success: function () {
    //             if (window.XMLHttpRequest){
    //                 xmlhttp=new XMLHttpRequest();
    //             }
    //
    //             else{
    //                 xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    //             }
    //             xmlhttp.open("FILES", file_data, false);
    //             $('#formSidebar').submit();
    //         },
    //         error: function(){
    //             alert("Erreur lors du téléchargement du fichier");
    //         }
    //     });
    // }); // fin de la fonction clic upload
});