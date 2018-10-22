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

    $('.typeahead').typeahead(null, {
        name: 'cities',
        source: cities,
        displayKey: 'value',
        templates: {
            suggestion: function (city) {
                return '<p>' + city.value + '</p>';
            }
        }
    });
});
