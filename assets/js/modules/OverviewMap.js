import 'leaflet';
import 'leaflet-extra-markers';
import List from 'list.js';
import 'leaflet-hash';
import 'leaflet.locatecontrol';

export default class OverviewMap {
    map;
    hash;
    stationLayer;
    highlightLayer;
    stationList = [];

    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.createMap();
    }

    createMap() {
        this.stationLayer = L.featureGroup();
        this.highlightLayer = L.featureGroup();

        this.map = L.map('overview-map', {
            zoom: 10,
            layers: [this.stationLayer, this.highlightLayer],
            zoomControl: false,
            attributionControl: false,
            maxZoom: 18,
        });

        L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
            attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        }).addTo(this.map);

        this.map.setView([53.56414, 9.967882]);

        const that = this;

        this.map.on('load', function(loadEvent) {
            that.loadStations();
            that.syncSidebar();
        });

        this.map.on('moveend', function (e) {
            that.loadStations();
            that.syncSidebar();
        });

        this.map.on('zoomend', function (e) {
            that.loadStations();
            that.syncSidebar();
        });

        this.hash = new L.Hash(this.map);

        this.installLocateControl();
        this.installZoomControl();
    }

    loadStations() {
        const bounds = this.map.getBounds();
        const defaultStationIcon = this.createStandardStationIcon();
        const that = this;

        $.ajax({
            url: 'https://luft.wip/api/station',
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

                    if (!(stationCode in that.stationList)) {
                        that.stationList[stationCode] = station;

                        const marker = L.marker([station.latitude, station.longitude], { icon: defaultStationIcon }).addTo(that.stationLayer);

                        marker.station = station;

                        marker.on('click', that.showStationModal);

                        $("#feature-list tbody").append('<tr class="feature-row" id="' + L.stamp(marker) + '" lat="' + marker.getLatLng().lat + '" lng="' +marker.getLatLng().lng + '"><td class="feature-name">' + station.station_code + '</td></tr>');
                    }
                }

                that.syncSidebar();
            },
        });
    }

    createStandardStationIcon() {
        return L.ExtraMarkers.icon({
            icon: 'fa-thermometer',
            markerColor: 'blue-dark',
            shape: 'circle',
            prefix: 'fa'
        });
    }

    showStationModal(e) {
        const $marker = e.target;

        $.get('https://luft.wip/api/' + $marker.station.station_code, {}, function(dataList) {
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

    syncSidebar() {
        const $tbody = $('#feature-list tbody');

        $tbody.empty();

        const that = this;

        this.stationLayer.eachLayer(function (layer) {
            if (that.map.hasLayer(that.stationLayer)) {
                if (that.map.getBounds().contains(layer.getLatLng())) {
                    $tbody.append('<tr class="feature-row" id="' + L.stamp(layer) + '" lat="' + layer.getLatLng().lat + '" lng="' + layer.getLatLng().lng + '"><td class="feature-name">' + layer.station.station_code + '</td><td style="vertical-align: middle;"><i class="fa fa-chevron-right pull-right"></i></td></tr>');
                }
            }
        });

        this.refreshList();
    }

    adjustHeight() {
        const $nav = $('nav.navbar');
        const $container = $('#container');

        const newHeight = $(window).height() - $nav.height() - 15;

        $container.height(newHeight);
    }

    installLocateControl() {
        L.control.locate({
            position: 'topright',
            drawCircle: true,
            follow: true,
            setView: true,
            keepCurrentZoomLevel: true,
            markerStyle: {
                weight: 1,
                opacity: 0.8,
                fillOpacity: 0.8,
            },
            circleStyle: {
                weight: 1,
                clickable: false,
            },
            icon: 'fa fa-location-arrow',
            metric: false,
            strings: {
                title: 'My location',
                popup: 'You are within {distance} {unit} from this point',
                outsideMapBoundsMsg: 'You seem located outside the boundaries of the map',
            },
            locateOptions: {
                maxZoom: 18,
                watch: true,
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000,
            }
        }).addTo(this.map);
    }

    installZoomControl() {
        L.control.zoom({
            position: 'topright'
        }).addTo(this.map);
    }

    /*
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
*/
    animateSidebar() {
        $('#sidebar').animate({
            width: 'toggle'
        }, 350, function() {
            map.invalidateSize();
        });
    }

    sidebarClick(id) {
        const layer = markerClusters.getLayer(id);
        map.setView([layer.getLatLng().lat, layer.getLatLng().lng], 17);

        layer.fire('click');

        if (document.body.clientWidth <= 767) {
            $('#sidebar').hide();
            map.invalidateSize();
        }
    }

    refreshList() {
        const featureList = new List('station-sidebar', {
            valueNames: ['feature-name']
        });

        featureList.sort('feature-name', {
            order: 'asc'
        });
    }

/*

    $(document).on('mouseout', '.feature-row', function() {
        highlightLayer.clearLayers();
    });

    $(document).on('click', '.feature-row', function(e) {
        $(document).off('mouseout', '.feature-row', function () {
            highlightLayer.clearLayers();
        });

        sidebarClick(parseInt($(this).attr('id'), 10));
    });*/
}

document.addEventListener('DOMContentLoaded', () => {
    const mapContainer = document.getElementById('overview-map');

    if (mapContainer) {
        new OverviewMap();
    }
});
