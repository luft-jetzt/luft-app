import 'leaflet';
import 'leaflet-extra-markers';

export default class OverviewMap {
    map;

    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.createMap();
    }

    createMap() {
        this.map = L.map('overview-map').setView([53, 10], 10);

        L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
            attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        }).addTo(this.map);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const mapContainer = document.getElementById('overview-map');

    if (mapContainer) {
        new OverviewMap();
    }
});
