$(document).ready(createAllMaps);

function createAllMaps() {
    $('.map').each(function (index) {
        var id = $(this).prop('id');
        var latitude = $(this).data('latitude');
        var longitude = $(this).data('longitude');

        createMap(id, latitude, longitude);
    });
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
