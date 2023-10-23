import {Component} from './component';

function reverseMap(map) {
    const reversed = {};
    for (const [key, value] of Object.entries(map)) {
        reversed[value] = key;
    }
    return reversed;
}

export class Shortcuts extends Component {

    setup() {
        this.container = this.$el;
        this.mapById = JSON.parse(this.$opts.keyMap);
        this.mapByShortcut = reverseMap(this.mapById);

        this.hintsShowing = false;

        this.hideHints = this.hideHints.bind(this);
        this.hintAbortController = null;

        this.setupListeners();
    }

    setupListeners() {
        window.addEventListener('keydown', event => {
            if (event.target.closest('input, select, textarea, .cm-editor')) {
                return;
            }

            if (event.key === '?') {
                if (this.hintsShowing) {
                    this.hideHints();
                } else {
                    this.showHints();
                }
                return;
            }

            this.handleShortcutPress(event);
        });
    }

    /**
     * @param {KeyboardEvent} event
     */
    handleShortcutPress(event) {
        const keys = [
            event.ctrlKey ? 'Ctrl' : '',
            event.metaKey ? 'Cmd' : '',
            event.key,
        ];

        const combo = keys.filter(s => Boolean(s)).join(' + ');

        const shortcutId = this.mapByShortcut[combo];
        if (shortcutId) {
            const wasHandled = this.runShortcut(shortcutId);
            if (wasHandled) {
                event.preventDefault();
            }
        }
    }

    /**
     * Run the given shortcut, and return a boolean to indicate if the event
     * was successfully handled by a shortcut action.
     * @param {String} id
     * @return {boolean}
     */
    runShortcut(id) {
        const el = this.container.querySelector(`[data-shortcut="${id}"]`);
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

        console.error('Shortcut attempted to be ran for element type that does not have handling setup', el);

        return false;
    }

    showHints() {
        const wrapper = document.createElement('div');
        wrapper.classList.add('shortcut-container');
        this.container.append(wrapper);

        const shortcutEls = this.container.querySelectorAll('[data-shortcut]');
        const displayedIds = new Set();
        for (const shortcutEl of shortcutEls) {
            const id = shortcutEl.getAttribute('data-shortcut');
            if (displayedIds.has(id)) {
                continue;
            }

            const key = this.mapById[id];
            this.showHintLabel(shortcutEl, key, wrapper);
            displayedIds.add(id);
        }

        this.hintAbortController = new AbortController();
        const signal = this.hintAbortController.signal;
        window.addEventListener('scroll', this.hideHints, {signal});
        window.addEventListener('focus', this.hideHints, {signal});
        window.addEventListener('blur', this.hideHints, {signal});
        window.addEventListener('click', this.hideHints, {signal});

        this.hintsShowing = true;
    }

    /**
     * @param {Element} targetEl
     * @param {String} key
     * @param {Element} wrapper
     */
    showHintLabel(targetEl, key, wrapper) {
        const targetBounds = targetEl.getBoundingClientRect();

        const label = document.createElement('div');
        label.classList.add('shortcut-hint');
        label.textContent = key;

        const linkage = document.createElement('div');
        linkage.classList.add('shortcut-linkage');
        linkage.style.left = `${targetBounds.x}px`;
        linkage.style.top = `${targetBounds.y}px`;
        linkage.style.width = `${targetBounds.width}px`;
        linkage.style.height = `${targetBounds.height}px`;

        wrapper.append(label, linkage);

        const labelBounds = label.getBoundingClientRect();

        label.style.insetInlineStart = `${((targetBounds.x + targetBounds.width) - (labelBounds.width + 6))}px`;
        label.style.insetBlockStart = `${(targetBounds.y + (targetBounds.height - labelBounds.height) / 2)}px`;
    }

    hideHints() {
        const wrapper = this.container.querySelector('.shortcut-container');
        wrapper.remove();
        this.hintAbortController?.abort();
        this.hintsShowing = false;
    }

}
