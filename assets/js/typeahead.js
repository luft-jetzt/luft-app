$(document).ready(function () {
    const cities = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: Routing.generate('prefetch'),
        cache: false,
    });

    $('.typeahead').typeahead(null, {
        name: 'cities',
        source: cities
    });
});
