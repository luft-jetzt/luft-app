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

    L.tileLayer('https://api.mapbox.com/styles/v1/maltehuebner/ciz8okvlo001f2spm3bbya1kd/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFsdGVodWVibmVyIiwiYSI6IjB5c2QtNXcifQ.I7OHZr0wtAvqE0wIY_psfg', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>'
    }).addTo(map);

    var marker = L.marker(centerLatLng).addTo(map);

    map._handlers.forEach(function (handler) {
        handler.disable();
    });
}

function createCityMap(id) {
    var map = L.map(id);

    L.tileLayer('https://api.mapbox.com/styles/v1/maltehuebner/ciz8okvlo001f2spm3bbya1kd/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFsdGVodWVibmVyIiwiYSI6IjB5c2QtNXcifQ.I7OHZr0wtAvqE0wIY_psfg', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>'
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
    map.fitBounds(markerGroup.getBounds());
}
