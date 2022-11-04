/**
 * The default mapping of unique id to shortcut key.
 * @type {Object<string, string>}
 */
const defaultMap = {
    "edit": "e",
    "global_search": "/",
};

function reverseMap(map) {
    const reversed = {};
    for (const [key, value] of Object.entries(map)) {
        reversed[value] = key;
    }
    return reversed;
}

/**
 * @extends {Component}
 */
class Shortcuts {

    setup() {
        this.container = this.$el;
        this.mapById = defaultMap;
        this.mapByShortcut = reverseMap(this.mapById);

        this.hintsShowing = false;
        // TODO - Allow custom key maps
        // TODO - Allow turning off shortcuts
        // TODO - Roll out to interface elements
        // TODO - Hide hints on focus, scroll, click

        this.setupListeners();
    }

    setupListeners() {
        window.addEventListener('keydown', event => {

            if (event.target.closest('input, select, textarea')) {
                return;
            }

            const shortcutId = this.mapByShortcut[event.key];
            if (shortcutId) {
                const wasHandled = this.runShortcut(shortcutId);
                if (wasHandled) {
                    event.preventDefault();
                }
            }
        });

        window.addEventListener('keydown', event => {
            if (event.key === '?') {
                this.hintsShowing ? this.hideHints() : this.showHints();
                this.hintsShowing = !this.hintsShowing;
            }
        });
    }

    /**
     * Run the given shortcut, and return a boolean to indicate if the event
     * was successfully handled by a shortcut action.
     * @param {String} id
     * @return {boolean}
     */
    runShortcut(id) {
        const el = this.container.querySelector(`[data-shortcut="${id}"]`);
        console.info('Shortcut run', el);
        if (!el) {
            return false;
        }

        if (el.matches('input, textarea, select')) {
            el.focus();
            return true;
        }

        if (el.matches('a, button')) {
            el.click();
            return true;
        }

        console.error(`Shortcut attempted to be ran for element type that does not have handling setup`, el);

        return false;
    }

    showHints() {
        const shortcutEls = this.container.querySelectorAll('[data-shortcut]');
        for (const shortcutEl of shortcutEls) {
            const id = shortcutEl.getAttribute('data-shortcut');
            const key = this.mapById[id];
            this.showHintLabel(shortcutEl, key);
        }
    }

    showHintLabel(targetEl, key) {
        const targetBounds = targetEl.getBoundingClientRect();
        const label = document.createElement('div');
        label.classList.add('shortcut-hint');
        label.textContent = key;
        this.container.append(label);

        const labelBounds = label.getBoundingClientRect();

        label.style.insetInlineStart = `${((targetBounds.x + targetBounds.width) - (labelBounds.width + 12))}px`;
        label.style.insetBlockStart = `${(targetBounds.y + (targetBounds.height - labelBounds.height) / 2)}px`;
    }

    hideHints() {
        const hints = this.container.querySelectorAll('.shortcut-hint');
        for (const hint of hints) {
            hint.remove();
        }
    }
}

export default Shortcuts;