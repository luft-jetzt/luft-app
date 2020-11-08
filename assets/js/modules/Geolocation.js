export default class Geolocation {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init(element);
    }

    init(element) {
        const that = this;

        element.addEventListener('click', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(that.success, that.error, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                });
            } else {
                that.error();
            }
        });
    }

    success(pos) {
        const coords = pos.coords;

        const route = Routing.generate(
            'display',
            {
                latitude: coords.latitude,
                longitude: coords.longitude
            }
        );

        window.location = route;
    }

    error(err) {
        const errorMessageContainer = document.querySelector('#geolocation-failed');

        errorMessageContainer.classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const locateButton = document.querySelector('#locate-button');

    if (locateButton) {
        new Geolocation(locateButton);
    }
});
