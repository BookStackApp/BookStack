/**
 * Handle common keyboard navigation events within a given container.
 */
export class KeyboardNavigationHandler {

    /**
     * @param {Element} container
     * @param {Function|null} onEscape
     * @param {Function|null} onEnter
     */
    constructor(container, onEscape = null, onEnter = null) {
        this.containers = [container];
        this.onEscape = onEscape;
        this.onEnter = onEnter;
        container.addEventListener('keydown', this.#keydownHandler.bind(this));
    }

    /**
     * Also share the keyboard event handling to the given element.
     * Only elements within the original container are considered focusable though.
     * @param {Element} element
     */
    shareHandlingToEl(element) {
        this.containers.push(element);
        element.addEventListener('keydown', this.#keydownHandler.bind(this));
    }

    /**
     * Focus on the next focusable element within the current containers.
     */
    focusNext() {
        const focusable = this.#getFocusable();
        const currentIndex = focusable.indexOf(document.activeElement);
        let newIndex = currentIndex + 1;
        if (newIndex >= focusable.length) {
            newIndex = 0;
        }

        focusable[newIndex].focus();
    }

    /**
     * Focus on the previous existing focusable element within the current containers.
     */
    focusPrevious() {
        const focusable = this.#getFocusable();
        const currentIndex = focusable.indexOf(document.activeElement);
        let newIndex = currentIndex - 1;
        if (newIndex < 0) {
            newIndex = focusable.length - 1;
        }

        focusable[newIndex].focus();
    }

    /**
     * @param {KeyboardEvent} event
     */
    #keydownHandler(event) {
        // Ignore certain key events in inputs to allow text editing.
        if (event.target.matches('input') && (event.key === 'ArrowRight' || event.key === 'ArrowLeft')) {
            return;
        }

        if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
            this.focusNext();
            event.preventDefault();
        } else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
            this.focusPrevious();
            event.preventDefault();
        } else if (event.key === 'Escape') {
            if (this.onEscape) {
                this.onEscape(event);
            } else if (document.activeElement) {
                document.activeElement.blur();
            }
        } else if (event.key === 'Enter' && this.onEnter) {
            this.onEnter(event);
        }
    }

    /**
     * Get an array of focusable elements within the current containers.
     * @returns {Element[]}
     */
    #getFocusable() {
        const focusable = [];
        const selector = '[tabindex]:not([tabindex="-1"]),[href],button:not([tabindex="-1"],[disabled]),input:not([type=hidden])';
        for (const container of this.containers) {
            focusable.push(...container.querySelectorAll(selector));
        }
        return focusable;
    }

}
