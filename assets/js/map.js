$(document).ready(createAllMaps);

function createAllMaps() {
    var cityMapId = $('.city-map').attr('id');
    var coordMapId = $('.coord-map').attr('id');

    if (cityMapId) {
        createCityMap(cityMapId);
    } else if (coordMapId) {
        createCoordMap(coordMapId);
    } else {
        $('.map').each(function (index) {
            var id = $(this).prop('id');
            var latitude = $(this).parent().data('latitude');
            var longitude = $(this).parent().data('longitude');
            var color = $(this).parent().data('station-color');

            createMap(id, latitude, longitude, color);
        });
    }
}

function createMap(id, latitude, longitude, color) {
    var centerLatLng = L.latLng([latitude, longitude]);

    var map = L.map(id, {
        zoomControl: false
    }).setView(centerLatLng, 13);

    L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
        attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);

    var markerIcon = L.ExtraMarkers.icon({
        icon: 'fa-circle-o',
        markerColor: color,
        shape: 'circle',
        prefix: 'fa'
    });

    var marker = L.marker(centerLatLng, {icon: markerIcon}).addTo(map);

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
        var color = $(this).data('station-color');

        var markerIcon = L.ExtraMarkers.icon({
            icon: 'fa-thermometer-half',
            markerColor: color,
            shape: 'circle',
            prefix: 'fa'
        });

        var marker = L.marker([latitude, longitude], {icon: markerIcon}).addTo(markerGroup);

        marker.on('click', function() {
            window.location = Routing.generate('station', { stationCode: stationCode });
        });
    });

    markerGroup.addTo(map);
    map.fitBounds(markerGroup.getBounds(), { padding: [15, 15] });
}

function createCoordMap(id) {
    var map = L.map(id);
    var $map = $('#' + id);

    L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
        attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);

    var markerGroup = new L.featureGroup();

    var latitude = $map.parent().data('latitude');
    var longitude = $map.parent().data('longitude');

    var markerIcon = L.ExtraMarkers.icon({
        icon: 'fa-user',
        markerColor: 'blue',
        shape: 'circle',
        prefix: 'fa'
    });

    var marker = L.marker([latitude, longitude], {icon: markerIcon}).addTo(markerGroup);

    var knownStations = [];

    $('.box').each(function (index) {
        var stationCode = $(this).data('station-code');
        var showOnMap = $(this).data('station-map');

        if (!showOnMap) {
            return;
        }

        if (!knownStations.includes(stationCode)) {
            var latitude = $(this).data('station-latitude');
            var longitude = $(this).data('station-longitude');
            var color = $(this).data('station-color');

            var markerIcon = L.ExtraMarkers.icon({
                icon: 'fa-thermometer-half',
                markerColor: color,
                shape: 'circle',
                prefix: 'fa'
            });

            var marker = L.marker([latitude, longitude], {icon: markerIcon}).addTo(markerGroup);

            marker.on('click', function() {
                window.location = Routing.generate('station', { stationCode: stationCode });
            });

            knownStations.push(stationCode);
        }
    });

    markerGroup.addTo(map);
    map.fitBounds(markerGroup.getBounds(), { padding: [15, 15] });
}
