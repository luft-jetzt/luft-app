$(document).ready(installButton());

var options = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};

function success(pos) {
    var coords = pos.coords;

    var route = Routing.generate(
        'display',
        {
            latitude: coords.latitude,
            longitude: coords.longitude
        }
    );

    var coordsString = coords.latitude + ',' + coords.longitude;

    _paq.push(['trackEvent', 'Search', 'coords', coordsString]);

    window.location = route;
}

function error(err) {
    var $message = $('#geolocation-failed');

    $message.removeClass('d-none');
}

function installButton() {
    $('#locate-button').on('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(success, error, options);
        } else {
            error();
        }
    });
}
