import { autocomplete } from '@algolia/autocomplete-js';

export default class Search {
    constructor(element, options) {
        const defaults = {};
        this.settings = { ...defaults, ...options };
        this.element = element;
        this.citiesData = [];
        this.stationsData = [];

        this.init();
    }

    async init() {
        const form = this.element.closest('form');
        const actionUri = form.action;
        const inputName = this.element.name;

        // Prefetch data
        await this.prefetchData();

        // Create container for autocomplete
        const container = document.createElement('div');
        container.className = 'autocomplete-container';
        this.element.parentNode.insertBefore(container, this.element);
        this.element.style.display = 'none';

        const self = this;
        const originalInput = this.element;

        autocomplete({
            container: container,
            panelContainer: container,
            placeholder: this.element.placeholder || 'Suchbegriff, Postleitzahl, Stadtname…',
            openOnFocus: true,
            detachedMediaQuery: 'none',
            classNames: {
                form: 'aa-Form',
                input: 'aa-Input form-control',
                panel: 'aa-Panel',
                list: 'aa-List',
                item: 'aa-Item',
            },
            onStateChange({ state }) {
                // Sync the autocomplete value to the original input
                originalInput.value = state.query;
            },
            onSubmit({ state }) {
                // When user presses Enter without selecting, submit the form
                if (state.query && state.query.length > 0) {
                    originalInput.value = state.query;
                    form.submit();
                }
            },
            getSources({ query }) {
                if (!query || query.length < 2) {
                    return [];
                }

                const queryLower = query.toLowerCase();

                return [
                    {
                        sourceId: 'cities',
                        getItems() {
                            return self.citiesData.filter(item =>
                                item.value.name.toLowerCase().includes(queryLower)
                            ).slice(0, 5);
                        },
                        templates: {
                            header({ items, html }) {
                                if (items.length === 0) return null;
                                return html`<div class="aa-SourceHeader"><i class="fa fa-university"></i> Städte</div>`;
                            },
                            item({ item, html }) {
                                return html`
                                    <a href="${item.value.url}" class="aa-ItemLink">
                                        <div class="aa-ItemContent">
                                            <span class="aa-ItemTitle">${item.value.name}</span>
                                        </div>
                                    </a>
                                `;
                            },
                        },
                        onSelect({ item }) {
                            window.location = item.value.url;
                        },
                    },
                    {
                        sourceId: 'stations',
                        getItems() {
                            return self.stationsData.filter(item => {
                                const searchStr = (item.value.stationCode + ' ' + (item.value.title || '')).toLowerCase();
                                return searchStr.includes(queryLower);
                            }).slice(0, 5);
                        },
                        templates: {
                            header({ items, html }) {
                                if (items.length === 0) return null;
                                return html`<div class="aa-SourceHeader"><i class="fa fa-thermometer-half"></i> Messstationen</div>`;
                            },
                            item({ item, html }) {
                                return html`
                                    <a href="${item.value.url}" class="aa-ItemLink">
                                        <div class="aa-ItemContent">
                                            <div class="aa-ItemDetails">
                                                ${item.value.title ? html`<span class="aa-ItemTitle">${item.value.title}</span>` : ''}
                                                <span class="aa-ItemCode">${item.value.stationCode}</span>
                                            </div>
                                        </div>
                                    </a>
                                `;
                            },
                        },
                        onSelect({ item }) {
                            window.location = item.value.url;
                        },
                    },
                    {
                        sourceId: 'remote',
                        getItems() {
                            return fetch(Routing.generate('search') + '?query=' + encodeURIComponent(query))
                                .then(response => response.json())
                                .then(data => data.slice(0, 5))
                                .catch(() => []);
                        },
                        templates: {
                            header({ items, html }) {
                                if (items.length === 0) return null;
                                return html`<div class="aa-SourceHeader"><i class="fa fa-map-marker"></i> Orte</div>`;
                            },
                            item({ item, html }) {
                                const url = actionUri + '?latitude=' + item.value.latitude + '&longitude=' + item.value.longitude;
                                return html`
                                    <a href="${url}" class="aa-ItemLink">
                                        <div class="aa-ItemContent">
                                            <div class="aa-ItemDetails">
                                                ${item.value.name ? html`<span class="aa-ItemTitle">${item.value.name}</span>` : ''}
                                                <span class="aa-ItemAddress">
                                                    ${item.value.zipCode || ''} ${item.value.city || ''}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                `;
                            },
                        },
                        onSelect({ item }) {
                            const url = actionUri + '?latitude=' + item.value.latitude + '&longitude=' + item.value.longitude;
                            window.location = url;
                        },
                    },
                ];
            },
        });
    }

    async prefetchData() {
        try {
            const [citiesResponse, stationsResponse] = await Promise.all([
                fetch(Routing.generate('prefetch_cities')),
                fetch(Routing.generate('prefetch_stations')),
            ]);

            this.citiesData = await citiesResponse.json();
            this.stationsData = await stationsResponse.json();
        } catch (error) {
            console.error('Failed to prefetch data:', error);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const typeaheadInputList = document.querySelectorAll('input.typeahead');

    typeaheadInputList.forEach(function (typeaheadInput) {
        new Search(typeaheadInput);
    });
});
