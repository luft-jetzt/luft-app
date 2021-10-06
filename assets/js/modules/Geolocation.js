export default class Geolocation {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init(element);
    }

    init(element) {
        const that = this;
        this.element = element;

        element.addEventListener('click', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(that.success.bind(that), that.error.bind(that), {
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

        const action = this.element.closest('form').getAttribute('action');

        // @todo use this: https://gomakethings.com/how-to-build-a-query-string-from-an-object-with-vanilla-js/

        const coordAction = action + '?latitude=' + coords.latitude + '&longitude=' + coords.longitude;

        window.location = coordAction;
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
