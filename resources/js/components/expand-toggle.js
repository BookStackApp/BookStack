import {slideUp, slideDown} from "../services/animations";

class ExpandToggle {

    constructor(elem) {
        this.elem = elem;

        // Component state
        this.isOpen = elem.getAttribute('expand-toggle-is-open') === 'yes';
        this.updateEndpoint = elem.getAttribute('expand-toggle-update-endpoint');
        this.selector = elem.getAttribute('expand-toggle');

        // Listener setup
        elem.addEventListener('click', this.click.bind(this));
    }

    open(elemToToggle) {
        slideDown(elemToToggle, 200);
    }

    close(elemToToggle) {
        slideUp(elemToToggle, 200);
    }

    click(event) {
        event.preventDefault();

        const matchingElems = document.querySelectorAll(this.selector);
        for (let match of matchingElems) {
            this.isOpen ?  this.close(match) : this.open(match);
        }

        this.isOpen = !this.isOpen;
        this.updateSystemAjax(this.isOpen);
    }

    updateSystemAjax(isOpen) {
        window.$http.patch(this.updateEndpoint, {
            expand: isOpen ? 'true' : 'false'
        });
    }

}

export default ExpandToggle;