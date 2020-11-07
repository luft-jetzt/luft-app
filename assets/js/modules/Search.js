import 'corejs-typeahead';
import Bloodhound from 'bloodhound-js';
import Handlebars from 'handlebars';

export default class Search {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init();
    }

    init() {
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
                return Bloodhound.tokenizers.whitespace(data.value.stationCode + data.value.title);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: Routing.generate('prefetch_stations'),
            cache: false,
            ttl: 60,
        });

        const remoteQueries = new Bloodhound({
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
                suggestion: renderCity,
            }
        }, {
            name: 'prefetchedStations',
            source: prefetchedStations,
            display: function(data) {
                return data.value.name;
            },
            templates: {
                header: '<strong>Messstationen</strong>',
                suggestion: renderStation,
            }
        }, {
            name: 'remoteQueries',
            source: remoteQueries,
            display: function(data) {
                return data.value.name;
            },
            templates: {
                header: '<strong>Suchergebnisse</strong>',
                suggestion: renderQuery,
            }
        }).on('typeahead:selected', redirect);

        function renderQuery(data) {
            let html = '';

            html += '<a href="' + data.value.url + '">';

            html += '<div class="row">';
            html += '<div class="col-12">';
            html += '<i class="fa fa-map-marker"></i> ';

            if (data.value.name) {
                html += data.value.name;
            }

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

        function renderCity(data) {
            let html = '';

            html += '<a href="' + data.value.url + '">';

            html += '<div class="row">';
            html += '<div class="col-12">';
            html += '<i class="fa fa-university"></i> ';

            html += data.value.name;

            html += '</div>';
            html += '</div>';

            html += '</a>';

            return html;
        }

        function renderStation(data) {
            const source = document.getElementById('render-station-template').innerHTML;
            const template = Handlebars.compile(source);
            const html = template(data.value);

            return html;
        }

        function redirect(event, datum) {
            window.location = datum.value.url;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Search();
});
