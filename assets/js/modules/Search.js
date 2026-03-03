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

    init() {
        const form = this.element.closest('form');
        const actionUri = form.action;
        const inputName = this.element.name;
        const originalClasses = this.element.className.replace('typeahead', '').trim();
        const isLarge = this.element.classList.contains('form-control-lg');

        // Create container that replaces the original input
        const container = document.createElement('div');
        container.className = 'autocomplete-container' + (isLarge ? ' autocomplete-container-lg' : '');
        this.element.parentNode.replaceChild(container, this.element);

        // Start prefetch in background (don't await)
        this.prefetchData();

        const self = this;

        autocomplete({
            container: container,
            panelContainer: container,
            placeholder: this.element.placeholder || 'Suchbegriff, Postleitzahl, Stadtname…',
            openOnFocus: true,
            detachedMediaQuery: 'none',
            classNames: {
                form: 'aa-Form',
                input: 'aa-Input ' + originalClasses,
                panel: 'aa-Panel',
                list: 'aa-List',
                item: 'aa-Item',
            },
            onSubmit({ state }) {
                // When user presses Enter without selecting, submit the form
                if (state.query && state.query.length > 0) {
                    // Create hidden input for form submission
                    let hiddenInput = form.querySelector('input[name="' + inputName + '"][type="hidden"]');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = inputName;
                        form.appendChild(hiddenInput);
                    }
                    hiddenInput.value = state.query;
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
