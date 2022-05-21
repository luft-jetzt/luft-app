export default class Geolocation {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init(element);
    }

    init(element) {
        const that = this;
        this.element = element;

        element.addEventListener('click', (event) => {
            if (navigator.geolocation) {
                that.disableButton(event.target);

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

    disableButton(target) {
        const button = target.closest('button');
        button.querySelector('i').remove();
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        button.disabled = true;
    }

    enableButton(target) {
        const button = target.closest('button');
        button.querySelector('i').remove();
        button.innerHTML = '<i class="fa fa-location-arrow"></i>';
        button.disabled = false;
    }

    success(pos) {
        const coords = pos.coords;

        const closestForm = this.element.closest('form');
        let action;

        if (closestForm) {
            action = closestForm.getAttribute('action');
        } else {
            action = '/display';
        }

        // @todo use this: https://gomakethings.com/how-to-build-a-query-string-from-an-object-with-vanilla-js/

        const coordAction = action + '?latitude=' + coords.latitude + '&longitude=' + coords.longitude;

        window.location = coordAction;
    }

    error(err) {
        const that = this;
        const errorMessageContainer = document.querySelector('#geolocation-failed');

        errorMessageContainer.classList.remove('d-none');

        document.querySelectorAll('button.locate-button').forEach((button) => {
            that.enableButton(button);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const locateButtonList = document.querySelectorAll('.locate-button');

    locateButtonList.forEach(function (locateButton) {
        new Geolocation(locateButton);
    });
});
