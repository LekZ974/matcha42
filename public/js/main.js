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
});