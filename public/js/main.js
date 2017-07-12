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
        var maxChars = $("#resume");
        var max_length = maxChars.attr('maxlength');
        if (max_length > 0) {
            maxChars.bind('keyup', function(e){
                length = new Number(maxChars.val().length);
                counter = max_length-length;
                $("#sessionNum_counter").text(counter);
            });
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