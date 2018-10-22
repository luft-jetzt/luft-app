$(document).ready(function () {
    const cities = new Bloodhound({
        datumTokenizer: function (data) {
            return Bloodhound.tokenizers.whitespace(data.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: Routing.generate('prefetch'),
        cache: false,
        remote: {
            url: Routing.generate('search') + '?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('.typeahead').typeahead({
        hint: false,
        highlight: true,
        minLength: 3,
        classNames: {
            dataset: 'tt-dataset tt-dataset-results container'
        }
    }, {
        name: 'cities',
        source: cities,
        displayKey: 'value',
        templates: {
            suggestion: renderSuggestion,
        }
    });
});

function renderSuggestion(data) {
    var html = '';

    console.log(data);
    html += '<a href="' + data.url + '">';

    html += '<div class="row">';
    html += '<div class="col-12">';
    html += '<i class="fa fa-university"></i> ';
    html += data.value;
    html += '</div>';
    html += '</div>';

    html += '</a>';

    return html;
}
