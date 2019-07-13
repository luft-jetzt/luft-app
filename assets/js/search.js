$(document).ready(installButton());

function installButton() {
    $('#search-button').on('click', function () {
        var searchPhrase = $('input#query').val();
        
        _paq.push(['trackEvent', 'Search', 'searchPhrase', searchPhrase]);
    });
}
