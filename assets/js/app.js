import 'bootstrap';
import 'leaflet';
import 'leaflet-extra-markers';

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







    function success(pos) {
        var coords = pos.coords;

        var route = Routing.generate(
            'display',
            {
                latitude: coords.latitude,
                longitude: coords.longitude
            }
        );

        window.location = route;
    }

    function error(err) {
        var $message = $('#geolocation-failed');

        $message.removeClass('d-none');
    }

    function installButton() {
        $('#locate-button').on('click', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(success, error, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                });
            } else {
                error();
            }
        });
    }
}

function initSearch()
{
    const prefetchedCities = new Bloodhound({
        datumTokenizer: function (data) {
            return Bloodhound.tokenizers.whitespace(data.value.name);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: Routing.generate('prefetch_cities'),
        cache: false,
        ttl: 60,
    });

    const prefetchedStations = new Bloodhound({
        datumTokenizer: function (data) {
            return Bloodhound.tokenizers.whitespace(data.value.name);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: Routing.generate('prefetch_stations'),
        cache: false,
        ttl: 60,
    });

    const remoteCities = new Bloodhound({
        datumTokenizer: function (data) {
            return Bloodhound.tokenizers.whitespace(data.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        cache: false,
        ttl: 60,
        remote: {
            url: Routing.generate('search') + '?query=%QUERY',
            wildcard: '%QUERY'
        },
    });

    $('.typeahead').typeahead({
        hint: true,
        highlight: true,
        minLength: 2,
        classNames: {
            dataset: 'tt-dataset tt-dataset-results container'
        }
    }, {
        name: 'prefetchedCities',
        source: prefetchedCities,
        display: function(data) {
            return data.value.name;
        },
        templates: {
            header: '<strong>Städte</strong>',
            suggestion: renderSuggestion,
        }
    }, {
        name: 'prefetchedStations',
        source: prefetchedStations,
        display: function(data) {
            return data.value.name;
        },
        templates: {
            header: '<strong>Messstationen</strong>',
            suggestion: renderSuggestion,
        }
    }, {
        name: 'remoteCities',
        source: remoteCities,
        display: function(data) {
            return data.value.name;
        },
        templates: {
            header: '<strong>Suchergebnisse</strong>',
            suggestion: renderSuggestion,
        }
    }).on('typeahead:selected', redirect);
}

function renderSuggestion(data) {
    let html = '';

    console.log(data);
    html += '<a href="' + data.value.url + '">';

    html += '<div class="row">';
    html += '<div class="col-12">';
    html += '<i class="fa fa-' + data.value.icon + '"></i> ';
    html += data.value.name;

    if (data.value.address || data.value.zipCode || data.value.city) {
        html += '<address>';

        if (data.value.address) {
            html += data.value.address;
        }

        if (data.value.address && (data.value.zipCode || data.value.city)) {
            html += '<br />';
        }

        if (data.value.zipCode) {
            html += data.value.zipCode;
        }

        if (data.value.zipCode && data.value.city) {
            html += ' ';
        }

        if (data.value.city) {
            html += data.value.city;
        }

        html += '</address>';

    }


    html += '</div>';
    html += '</div>';

    html += '</a>';

    return html;
}

function redirect(event, datum) {
    window.location = datum.value.url;
}

document.addEventListener('DOMContentLoaded', () => {
    createAllMaps();
    installButton();
    initSearch();
});
