import 'leaflet';
import 'leaflet-extra-markers';

export default class Map {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.createAllMaps();
    }

    createAllMaps() {
        const cityMap = document.querySelector('.city-map');
        const coordMap = document.querySelector('.coord-map');

        if (cityMap) {
            this.createCityMap(cityMap.id);
        } else if (coordMap) {
            this.createCoordMap(coordMap.id);
        } else {
            const that = this;

            const mapList = document.querySelectorAll('.map');

            mapList.forEach(function (map) {
                const mapContainer = map.parentNode;

                const latitude = mapContainer.dataset.latitude;
                const longitude = mapContainer.dataset.longitude;
                const color = mapContainer.dataset.stationColor;

                that.createMap(map.id, latitude, longitude, color);
            });
        }
    }

    createMap(id, latitude, longitude, color) {
        const centerLatLng = L.latLng([latitude, longitude]);

        const map = L.map(id, {
            zoomControl: false
        }).setView(centerLatLng, 13);

        this.addTileLayer(map);

        const markerIcon = L.ExtraMarkers.icon({
            icon: 'fa-circle-o',
            markerColor: color,
            shape: 'circle',
            prefix: 'fa'
        });

        const marker = L.marker(centerLatLng, {icon: markerIcon}).addTo(map);

        map._handlers.forEach(function (handler) {
            handler.disable();
        });
    }

    createCityMap(id) {
        const map = L.map(id);

        this.addTileLayer(map);

        const markerGroup = new L.featureGroup();

        const that = this;

        const stationList = document.querySelectorAll('.station');

        stationList.forEach(function (station) {
            const stationCode = station.dataset.stationCode;
            const latitude = station.dataset.latitude;
            const longitude = station.dataset.longitude;
            const color = station.dataset.stationColor;

            const markerIcon = that.createIcon('fa-thermometer-half', color);

            const marker = L.marker([latitude, longitude], {icon: markerIcon}).addTo(markerGroup);

            marker.on('click', function() {
                window.location = Routing.generate('station', { stationCode: stationCode });
            });
        });

        markerGroup.addTo(map);
        map.fitBounds(markerGroup.getBounds(), { padding: [15, 15] });
    }

    createCoordMap(id) {
        const map = L.map(id);
        const mapContainer = document.getElementById(id).parentNode;

        this.addTileLayer(map);

        const markerGroup = new L.featureGroup();

        const latitude = mapContainer.dataset.latitude;
        const longitude = mapContainer.dataset.longitude;

        const markerIcon = this.createIcon('fa-user', 'blue');

        L.marker([latitude, longitude], {icon: markerIcon}).addTo(markerGroup);

        const that = this;
        const markerList = [];
        const maxPollutionLevelList = [];

        const boxList = document.querySelectorAll('.box');

        boxList.forEach(function (box) {
            const stationCode = box.dataset.stationCode;
            const showOnMap = box.dataset.stationMap;

            if (!showOnMap) {
                return;
            }

            if (!markerList[stationCode]) {
                const latitude = box.dataset.stationLatitude;
                const longitude = box.dataset.stationLongitude;
                const color = box.dataset.stationColor;
                const pollutionLevel = box.dataset.pollutionLevel;

                const markerIcon = that.createIcon('fa-thermometer-half', color);

                const marker = L.marker([latitude, longitude], {icon: markerIcon}).addTo(markerGroup);

                marker.on('click', function() {
                    window.location = Routing.generate('station', { stationCode: stationCode });
                });

                markerList[stationCode] = marker;
                maxPollutionLevelList[stationCode] = pollutionLevel;
            } else {
                const currentPollutionLevel = maxPollutionLevelList[stationCode];
                const newPollutionLevel = box.dataset.pollutionLevel;

                if (newPollutionLevel > currentPollutionLevel) {
                    const newColor = box.dataset.stationColor;
                    const pollutionLevel = box.dataset.pollutionLevel;
                    const newMarkerIcon = that.createIcon('fa-thermometer-half', newColor);

                    markerList[stationCode].setIcon(newMarkerIcon);
                    maxPollutionLevelList[stationCode] = newPollutionLevel;
                }
            }
        });

        markerGroup.addTo(map);
        map.fitBounds(markerGroup.getBounds(), { padding: [15, 15] });
    }

    addTileLayer(map) {
        L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
            attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        }).addTo(map);
    }

    createIcon(icon, color) {
        return L.ExtraMarkers.icon({
            icon: icon,
            markerColor: color,
            shape: 'circle',
            prefix: 'fa'
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Map();
});
