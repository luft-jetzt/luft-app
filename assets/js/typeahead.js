$(document).ready(function () {
    var $input = $('.typeahead');

    $('.typeahead').typeahead({
        source: function (query, process) {
            console.log(query);
            return $.getJSON(
                'url-to-file.php',
                { query: query },
                function (data) {
                    console.log(data)
                    return process(data);
                })
        }
    });

});
