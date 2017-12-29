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
        $(this.content).slideDown(400);
    }

    close() {
        this.elem.classList.remove('open');
        $(this.content).slideUp(400);
    }

    toggle() {
        if (this.elem.classList.contains('open')) {
            this.close();
        } else {
            this.open();
        }
    }

}

module.exports = Collapsible;