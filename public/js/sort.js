$(document).ready(function () {
    $("#sort").change(function () {
        $("#filterSort").trigger("change");
    });
    $("#filterSort").change(function () {
        function sort_age(a, b) {
            if ($("#sort").val() == "asc") {
                return ($(b).data('age')) < ($(a).data('age')) ? 1 : -1;
            }
            else {
                return ($(b).data('age')) > ($(a).data('age')) ? 1 : -1;
            }
        }

        function sort_location(a, b) {
            if ($("#sort").val() == "asc") {
                return ($(b).data('location')) < ($(a).data('location')) ? 1 : -1;
            }
            else {
                return ($(b).data('location')) > ($(a).data('location')) ? 1 : -1;
            }
        }

        function sort_popularity(a, b) {
            if ($("#sort").val() == "asc") {
                return ($(b).data('popularity')) < ($(a).data('popularity')) ? 1 : -1;
            }
            else {
                return ($(b).data('popularity')) > ($(a).data('popularity')) ? 1 : -1;
            }
        }

        function sort_interests(a, b) {
            if ($("#sort").val() == "asc") {
                return ($(b).data('interests')) < ($(a).data('interests')) ? 1 : -1;
            }
            else {
                return ($(b).data('interests')) > ($(a).data('interests')) ? 1 : -1;
            }
        }

        function sort_def(a, b) {
            if ($("#sort").val() == "asc") {
                return ($(b).data('index')) < ($(a).data('index')) ? 1 : -1;
            }
            else {
                return ($(b).data('index')) > ($(a).data('index')) ? 1 : -1;
            }
        }

        if ($(this).val() == "age")
            $(".listusers .user").sort(sort_age).appendTo('.listusers');
        else if ($(this).val() == "location")
            $(".listusers .user").sort(sort_location).appendTo('.listusers');
        else if ($(this).val() == "popularity")
            $(".listusers .user").sort(sort_popularity).appendTo('.listusers');
        else if ($(this).val() == "interests")
            $(".listusers .user").sort(sort_interests).appendTo('.listusers');
        else if ($(this).val() == "default")
            $(".listusers .user").sort(sort_def).appendTo('.listusers');

    });

    $('#filterDistance').slider({
        formatter: function(value) {
            return 'Current value: ' + value;
        }
    });

    $("#filterAge").slider({});
    $("#filterPop").slider({});

    $(".filter").on('change', function ()
    {
        var distance = $("#filterDistance").val();
        var age = $("#filterAge").val();
        var pop = $("#filterPop").val();

        function filter_age(a, b, elem) {
            b = b.split(',');
            if (a > b[1] || a < b[0]) {
                elem.hide();
            }
            else {
                elem.show();
            }
        }

        function filter_location(a, b, elem) {
            if (a >= b) {
                elem.hide();
            }
            else {
                elem.show();
            }
            if (b == 1000)
                elem.show();
        }

        function filter_popularity(a, b, elem) {
            b = b.split(',');
            if (a > b[1] || a < b[0]){
                elem.hide();
            }
            else {
                elem.show();
            }
            if (b == 2000)
                elem.show();
        }

        $("#slidAge").on('change', function () {
            $(".listusers .user").each(function () {
                var $this = $(this);
                filter_age($this.data('age'), age, $this);
            });
        });
        $("#slidDist").on('change', function () {
            $(".listusers .user").each(function () {
                var $this = $(this);
                filter_location($this.data('location'), distance, $this);
            });
        });
        $("#slidPop").on('change', function () {
            $(".listusers .user").each(function () {
                var $this = $(this);
                filter_popularity($this.data('popularity'), pop, $this);
            });
        });
    });
});