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

        window.location = Routing.generate(
            'display',
            {
                latitude: coords.latitude,
                longitude: coords.longitude
            }
        );
    }

    error(err) {
        const errorMessageContainer = document.querySelector('#geolocation-failed');

        errorMessageContainer.classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const locateButtonList = document.querySelectorAll('.locate-button');

    locateButtonList.forEach(function (locateButton) {
        new Geolocation(locateButton);
    });
});
