import 'bootstrap';
import Geocoding from './modules/Geocoding';
import Map from './modules/Map';
import Search from './modules/Search';

export {Geocoding, Map, Search}

let map;
let stationLayer = L.featureGroup();
let stationList = [];
let featureList;
let isCollapsedif = document.body.clientWidth <= 767;
let highlightLayer = L.geoJson(null);
let highlightStyle = {
    stroke: false,
    fillColor: "#00FFFF",
    fillOpacity: 0.7,
    radius: 10
};

function loadStations() {
    let bounds = map.getBounds();
    let defaultStationIcon = this.createStandardStationIcon();

    $.ajax({
        url: 'http://luft.ct/api/station',
        data: {
            north: bounds.getNorth(),
            east: bounds.getEast(),
            south: bounds.getSouth(),
            west: bounds.getWest(),
            remember_stations: true,
            provider_identifier: 'uba_de',
        },
        success: function (result) {
            let i;

            for (i = 0; i < result.length; ++i) {
                const station = result[i];
                const stationCode = station.station_code;

                if (!(stationCode in stationList)) {
                    stationList[stationCode] = station;

                    const marker = L.marker([station.latitude, station.longitude], { icon: defaultStationIcon }).addTo(stationLayer);

                    marker.station = station;

                    marker.on('click', showStationModal);

                    $("#feature-list tbody").append('<tr class="feature-row" id="' + L.stamp(marker) + '" lat="' + marker.getLatLng().lat + '" lng="' +marker.getLatLng().lng + '"><td class="feature-name">' + station.station_code + '</td></tr>');
                }
            }
        },
    });
}

function createStandardStationIcon() {
    return L.ExtraMarkers.icon({
        icon: 'fa-thermometer',
        markerColor: 'blue-dark',
        shape: 'circle',
        prefix: 'fa'
    });
}

function showStationModal(e) {
    const $marker = e.target;

    $.get('http://luft.ct/api/' + $marker.station.station_code, {}, function(dataList) {
        let content = '<table class="table table-striped table-bordered table-condensed">';

        for (let i = 0; i < dataList.length; ++i) {
            const data = dataList[i];

            content += '<tr><td>' + data.pollutant.short_name_html +'</td><td>' + data.data.value + ' ' + data.pollutant.unit_html + '</td></tr>';
        }
        content += '</table>';

        $('#feature-title').html($marker.station.station_code);
        $('#feature-info').html(content);
        $('#featureModal').modal('show');

        highlightLayer.clearLayers().addLayer(L.circleMarker($marker.getLatLng(), highlightStyle));
    });
}

function syncSidebar() {
    const $tbody = $('#feature-list tbody');

    $tbody.empty();

    stationLayer.eachLayer(function (layer) {
        if (map.hasLayer(stationLayer)) {
            if (map.getBounds().contains(layer.getLatLng())) {
                $tbody.append('<tr class="feature-row" id="' + L.stamp(layer) + '" lat="' + layer.getLatLng().lat + '" lng="' + layer.getLatLng().lng + '"><td class="feature-name">' + layer.station.station_code + '</td><td style="vertical-align: middle;"><i class="fa fa-chevron-right pull-right"></i></td></tr>');
            }
        }
    });

    refreshList();
}

function createMap() {
    map = L.map('map', {
        zoom: 10,
        layers: [stationLayer, highlightLayer],
        zoomControl: false,
        attributionControl: false,
        maxZoom: 18,
    });

    L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
        attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);

    map.setView([53.56414, 9.967882]);

    let hash = new L.Hash(map);
}

function adjustHeight() {
    const $nav = $('nav.navbar');
    const $container = $('#container');

    const newHeight = $(window).height() - $nav.height() - 15;

    $container.height(newHeight);
}

function installLocateControl() {
    L.control.locate({
        position: 'topright',
        drawCircle: true,
        follow: true,
        setView: true,
        keepCurrentZoomLevel: true,
        markerStyle: {
            weight: 1,
            opacity: 0.8,
            fillOpacity: 0.8
        },
        circleStyle: {
            weight: 1,
            clickable: false
        },
        icon: 'fa fa-location-arrow',
        metric: false,
        strings: {
            title: "My location",
            popup: "You are within {distance} {unit} from this point",
            outsideMapBoundsMsg: "You seem located outside the boundaries of the map"
        },
        locateOptions: {
            maxZoom: 18,
            watch: true,
            enableHighAccuracy: true,
            maximumAge: 10000,
            timeout: 10000
        }
    }).addTo(map);
}

function installZoomControl() {
    L.control.zoom({
        position: 'topright'
    }).addTo(map);
}

$('#list-btn').click(function() {
    animateSidebar();
    return false;
});

$('#nav-btn').click(function() {
    $('.navbar-collapse').collapse('toggle');
    return false;
});

$('#sidebar-hide-btn').click(function() {
    animateSidebar();
    return false;
});

function animateSidebar() {
    $('#sidebar').animate({
        width: 'toggle'
    }, 350, function() {
        map.invalidateSize();
    });
}

function sidebarClick(id) {
    const layer = markerClusters.getLayer(id);
    map.setView([layer.getLatLng().lat, layer.getLatLng().lng], 17);

    layer.fire('click');

    if (document.body.clientWidth <= 767) {
        $('#sidebar').hide();
        map.invalidateSize();
    }
}

function refreshList() {
    featureList = new List('station-sidebar', {
        valueNames: ['feature-name']
    });
    featureList.sort('feature-name', {
        order: 'asc'
    });
}

createMap();
adjustHeight();
loadStations();
installLocateControl();
installZoomControl();

map.on('load', function(loadEvent) {
    loadStations();
    syncSidebar();
});

map.on('moveend', function (e) {
    loadStations();
    syncSidebar();
});

map.on('zoomend', function (e) {
    loadStations();
    syncSidebar();
});

$(document).on('mouseout', '.feature-row', function() {
    highlightLayer.clearLayers();
});

$(document).on('click', '.feature-row', function(e) {
    $(document).off('mouseout', '.feature-row', function () {
        highlightLayer.clearLayers();
    });

    sidebarClick(parseInt($(this).attr('id'), 10));
});
