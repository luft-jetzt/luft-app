import 'corejs-typeahead';
import Bloodhound from 'bloodhound-js';
import Handlebars from 'handlebars/lib/handlebars';

export default class Search {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init(element);
    }
    init(element) {
        const form = element.closest('form');
        const actionUri = form.action;

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

        $('#' + element.id).typeahead({
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

        function buildUri(data) {
            return actionUri + '?latitude=' + data.value.latitude + '&longitude=' + data.value.longitude;
        }

        function renderQuery(data) {
            const source = document.getElementById('render-query-template').innerHTML;
            const template = Handlebars.compile(source);

            data.value.url = buildUri(data);

            return template(data.value);
        }

        function renderCity(data) {
            const source = document.getElementById('render-city-template').innerHTML;
            const template = Handlebars.compile(source);

            return template(data.value);
        }

        function renderStation(data) {
            const source = document.getElementById('render-station-template').innerHTML;
            const template = Handlebars.compile(source);

            return template(data.value);
        }

        function redirect(event, datum) {
            window.location = datum.value.url;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const typeaheadInputList = document.querySelectorAll('input.typeahead');

    typeaheadInputList.forEach(function (typeaheadInput) {
        new Search(typeaheadInput);
    });


});
