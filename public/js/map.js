$(document).ready(createAllMaps);

function createAllMaps() {
    var cityMapId = $('.city-map').attr('id');

    if (cityMapId) {
        createCityMap(cityMapId);
    } else {
        $('.map').each(function (index) {
            var id = $(this).prop('id');
            var latitude = $(this).parent().data('latitude');
            var longitude = $(this).parent().data('longitude');

            createMap(id, latitude, longitude);
        });
    }
}

function createMap(id, latitude, longitude) {
    var centerLatLng = L.latLng([latitude, longitude]);

    var map = L.map(id, {
        zoomControl: false
    }).setView(centerLatLng, 13);

    L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
        attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);

    var marker = L.marker(centerLatLng).addTo(map);

    map._handlers.forEach(function (handler) {
        handler.disable();
    });
}

function createCityMap(id) {
    var map = L.map(id);

    L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
        attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);

    var markerGroup = new L.featureGroup();

    $('.station').each(function (index) {
        var stationCode = $(this).data('station-code');
        var latitude = $(this).data('latitude');
        var longitude = $(this).data('longitude');

        var marker = L.marker([latitude, longitude]).addTo(markerGroup);

        marker.on('click', function() {
            window.location = Routing.generate('station', { stationCode: stationCode });
        });
    });

    markerGroup.addTo(map);
    map.fitBounds(markerGroup.getBounds(), { padding: [15, 15] });
}
