import Geolocation from "./Geolocation";

export default class TimeAgoInWords {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.start(element);
    }

    start(element) {
        const timestamp = element.dataset.timeAgoTimestamp;

        element.innerHTML = this.timeAgoInWords(timestamp);

        const that = this;

        const interval = setInterval(function() {
            element.innerHTML = that.timeAgoInWords(timestamp);
        }, 15000);
    }

    timeAgoInWords(timestamp) {
        const targetDateTime = new Date(timestamp * 1000);
        const now = new Date();

        const periodsSingular = ['einer Sekunde', 'einer Minute', 'einer Stunde', 'einem Tag', 'einer Woche', 'einem Monat', 'einem Jahr'];
        const periodsPlural = ['Sekunden', 'Minuten', 'Stunden', 'Tagen', 'Wochen', 'Monaten', 'Jahren'];
        const periodLengths = [60, 60, 24, 7, 4.35, 12];

        const direction = (targetDateTime > now) ? 'in' : 'vor';

        let difference = Math.abs(targetDateTime - now) / 1000;

        let i;

        for (i = 0; difference >= periodLengths[i] && i < periodLengths.length; ++i) {
            difference /= periodLengths[i];
        }

        difference = Math.round(difference);

        return (difference === 1) ? direction + ' ' + periodsSingular[i] : direction + ' ' + difference + ' ' + periodsPlural[i];
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const timeAgoList = document.querySelectorAll('[data-time-ago-timestamp]');

    timeAgoList.forEach(function (element) {
        new TimeAgoInWords(element);
    });
});
