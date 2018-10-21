$(document).ready(function () {
    var countries = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: '/search/prefetch'
    });

// passing in `null` for the `options` arguments will result in the default
// options being used
    $('.typeahead').typeahead(null, {
        name: 'countries',
        source: countries
    });
});
