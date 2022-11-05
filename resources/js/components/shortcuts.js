/**
 * The default mapping of unique id to shortcut key.
 * @type {Object<string, string>}
 */
const defaultMap = {
    // Header actions
    "home": "1",
    "shelves_view": "2",
    "books_view": "3",
    "settings_view": "4",
    "favorites_view": "5",
    "profile_view": "6",
    "global_search": "/",
    "logout": "0",

    // Generic actions
    "edit": "e",
    "new": "n",
    "copy": "c",
    "delete": "d",
    "favorite": "f",
    "export": "x",
    "sort": "s",
    "permissions": "p",
    "move": "m",
    "revisions": "r",

    // Navigation
    "next": "ArrowRight",
    "prev": "ArrowLeft",
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

        this.hideHints = this.hideHints.bind(this);
        // TODO - Allow custom key maps
        // TODO - Allow turning off shortcuts

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

        if (el.matches('div[tabindex]')) {
            el.click();
            el.focus();
            return true;
        }

        console.error(`Shortcut attempted to be ran for element type that does not have handling setup`, el);

        return false;
    }

    showHints() {
        const shortcutEls = this.container.querySelectorAll('[data-shortcut]');
        const displayedIds = new Set();
        for (const shortcutEl of shortcutEls) {
            const id = shortcutEl.getAttribute('data-shortcut');
            if (displayedIds.has(id)) {
                continue;
            }

            const key = this.mapById[id];
            this.showHintLabel(shortcutEl, key);
            displayedIds.add(id);
        }

        window.addEventListener('scroll', this.hideHints);
        window.addEventListener('focus', this.hideHints);
        window.addEventListener('blur', this.hideHints);
        window.addEventListener('click', this.hideHints);

        this.hintsShowing = true;
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

        window.removeEventListener('scroll', this.hideHints);
        window.removeEventListener('focus', this.hideHints);
        window.removeEventListener('blur', this.hideHints);
        window.removeEventListener('click', this.hideHints);

        this.hintsShowing = false;
    }
}

export default Shortcuts;