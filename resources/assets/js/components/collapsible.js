import {slideDown, slideUp} from "../services/animations";

/**
 * Collapsible
 * Provides some simple logic to allow collapsible sections.
 */
class Collapsible {

    constructor(elem) {
        this.elem = elem;
        this.trigger = elem.querySelector('[collapsible-trigger]');
        this.content = elem.querySelector('[collapsible-content]');

        if (!this.trigger) return;

        this.trigger.addEventListener('click', this.toggle.bind(this));
    }

    open() {
        this.elem.classList.add('open');
        this.trigger.setAttribute('aria-expanded', 'true');
        slideDown(this.content, 300);
    }

    close() {
        this.elem.classList.remove('open');
        this.trigger.setAttribute('aria-expanded', 'false');
        slideUp(this.content, 300);
    }

    toggle() {
        if (this.elem.classList.contains('open')) {
            this.close();
        } else {
            this.open();
        }
    }

}

export default Collapsible;