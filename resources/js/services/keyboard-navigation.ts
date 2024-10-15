import {isHTMLElement} from "./dom";

type OptionalKeyEventHandler = ((e: KeyboardEvent) => any)|null;

/**
 * Handle common keyboard navigation events within a given container.
 */
export class KeyboardNavigationHandler {

    protected containers: HTMLElement[];
    protected onEscape: OptionalKeyEventHandler;
    protected onEnter: OptionalKeyEventHandler;

    constructor(container: HTMLElement, onEscape: OptionalKeyEventHandler = null, onEnter: OptionalKeyEventHandler = null) {
        this.containers = [container];
        this.onEscape = onEscape;
        this.onEnter = onEnter;
        container.addEventListener('keydown', this.#keydownHandler.bind(this));
    }

    /**
     * Also share the keyboard event handling to the given element.
     * Only elements within the original container are considered focusable though.
     */
    shareHandlingToEl(element: HTMLElement) {
        this.containers.push(element);
        element.addEventListener('keydown', this.#keydownHandler.bind(this));
    }

    /**
     * Focus on the next focusable element within the current containers.
     */
    focusNext() {
        const focusable = this.#getFocusable();
        const activeEl = document.activeElement;
        const currentIndex = isHTMLElement(activeEl) ? focusable.indexOf(activeEl) : -1;
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
        const activeEl = document.activeElement;
        const currentIndex = isHTMLElement(activeEl) ? focusable.indexOf(activeEl) : -1;
        let newIndex = currentIndex - 1;
        if (newIndex < 0) {
            newIndex = focusable.length - 1;
        }

        focusable[newIndex].focus();
    }

    #keydownHandler(event: KeyboardEvent) {
        // Ignore certain key events in inputs to allow text editing.
        if (isHTMLElement(event.target) && event.target.matches('input') && (event.key === 'ArrowRight' || event.key === 'ArrowLeft')) {
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
            } else if (isHTMLElement(document.activeElement)) {
                document.activeElement.blur();
            }
        } else if (event.key === 'Enter' && this.onEnter) {
            this.onEnter(event);
        }
    }

    /**
     * Get an array of focusable elements within the current containers.
     */
    #getFocusable(): HTMLElement[] {
        const focusable: HTMLElement[] = [];
        const selector = '[tabindex]:not([tabindex="-1"]),[href],button:not([tabindex="-1"],[disabled]),input:not([type=hidden])';
        for (const container of this.containers) {
            const toAdd = [...container.querySelectorAll(selector)].filter(e => isHTMLElement(e));
            focusable.push(...toAdd);
        }

        return focusable;
    }

}
