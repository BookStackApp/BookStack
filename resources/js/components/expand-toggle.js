import {slideUp, slideDown} from '../services/animations';
import {Component} from './component';

export class ExpandToggle extends Component {

    setup(elem) {
        this.targetSelector = this.$opts.targetSelector;
        this.isOpen = this.$opts.isOpen === 'true';
        this.updateEndpoint = this.$opts.updateEndpoint;

        // Listener setup
        this.$el.addEventListener('click', this.click.bind(this));
    }

    open(elemToToggle) {
        slideDown(elemToToggle, 200);
    }

    close(elemToToggle) {
        slideUp(elemToToggle, 200);
    }

    click(event) {
        event.preventDefault();

        const matchingElems = document.querySelectorAll(this.targetSelector);
        for (const match of matchingElems) {
            this.isOpen ? this.close(match) : this.open(match);
        }

        this.isOpen = !this.isOpen;
        this.updateSystemAjax(this.isOpen);
    }

    updateSystemAjax(isOpen) {
        window.$http.patch(this.updateEndpoint, {
            expand: isOpen ? 'true' : 'false',
        });
    }

}
