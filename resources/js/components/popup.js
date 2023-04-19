import {fadeIn, fadeOut} from '../services/animations';
import {onSelect} from '../services/dom';
import {Component} from './component';

/**
 * Popup window that will contain other content.
 * This component provides the show/hide functionality
 * with the ability for popup@hide child references to close this.
 */
export class Popup extends Component {

    setup() {
        this.container = this.$el;
        this.hideButtons = this.$manyRefs.hide || [];

        this.onkeyup = null;
        this.onHide = null;
        this.setupListeners();
    }

    setupListeners() {
        let lastMouseDownTarget = null;
        this.container.addEventListener('mousedown', event => {
            lastMouseDownTarget = event.target;
        });

        this.container.addEventListener('click', event => {
            if (event.target === this.container && lastMouseDownTarget === this.container) {
                this.hide();
            }
        });

        onSelect(this.hideButtons, () => this.hide());
    }

    hide(onComplete = null) {
        fadeOut(this.container, 120, onComplete);
        if (this.onkeyup) {
            window.removeEventListener('keyup', this.onkeyup);
            this.onkeyup = null;
        }
        if (this.onHide) {
            this.onHide();
        }
    }

    show(onComplete = null, onHide = null) {
        fadeIn(this.container, 120, onComplete);

        this.onkeyup = event => {
            if (event.key === 'Escape') {
                this.hide();
            }
        };
        window.addEventListener('keyup', this.onkeyup);
        this.onHide = onHide;
    }

}
