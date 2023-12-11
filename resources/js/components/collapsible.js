import {slideDown, slideUp} from '../services/animations';
import {Component} from './component';

/**
 * Collapsible
 * Provides some simple logic to allow collapsible sections.
 */
export class Collapsible extends Component {

    setup() {
        this.container = this.$el;
        this.trigger = this.$refs.trigger;
        this.content = this.$refs.content;

        if (this.trigger) {
            this.trigger.addEventListener('click', this.toggle.bind(this));
            this.openIfContainsError();
        }
    }

    open() {
        this.container.classList.add('open');
        this.trigger.setAttribute('aria-expanded', 'true');
        slideDown(this.content, 300);
    }

    close() {
        this.container.classList.remove('open');
        this.trigger.setAttribute('aria-expanded', 'false');
        slideUp(this.content, 300);
    }

    toggle() {
        if (this.container.classList.contains('open')) {
            this.close();
        } else {
            this.open();
        }
    }

    openIfContainsError() {
        const error = this.content.querySelector('.text-neg.text-small');
        if (error) {
            this.open();
        }
    }

}
