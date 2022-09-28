import {escapeHtml} from "../services/util";
import {onChildEvent} from "../services/dom";

const ajaxCache = {};

/**
 * AutoSuggest
 * @extends {Component}
 */
class AutoSuggest {
    setup() {
        this.parent = this.$el.parentElement;
        this.container = this.$el;
        this.type = this.$opts.type;
        this.url = this.$opts.url;
        this.input = this.$refs.input;
        this.list = this.$refs.list;

        this.lastPopulated = 0;
        this.setupListeners();
    }

    setupListeners() {
        this.input.addEventListener('input', this.requestSuggestions.bind(this));
        this.input.addEventListener('focus', this.requestSuggestions.bind(this));
        this.input.addEventListener('keydown', event => {
            if (event.key === 'Tab') {
                this.hideSuggestions();
            }
        });

        this.input.addEventListener('blur', this.hideSuggestionsIfFocusedLost.bind(this));
        this.container.addEventListener('keydown', this.containerKeyDown.bind(this));

        onChildEvent(this.list, 'button', 'click', (event, el) => {
            this.selectSuggestion(el.textContent);
        });
        onChildEvent(this.list, 'button', 'keydown', (event, el) => {
            if (event.key === 'Enter') {
                this.selectSuggestion(el.textContent);
            }
        });

    }

    selectSuggestion(value) {
        this.input.value = value;
        this.lastPopulated = Date.now();
        this.input.focus();
        this.input.dispatchEvent(new Event('input', {bubbles: true}));
        this.input.dispatchEvent(new Event('change', {bubbles: true}));
        this.hideSuggestions();
    }

    containerKeyDown(event) {
        if (event.key === 'Enter') event.preventDefault();
        if (this.list.classList.contains('hidden')) return;

        // Down arrow
        if (event.key === 'ArrowDown') {
            this.moveFocus(true);
            event.preventDefault();
        }
        // Up Arrow
        else if (event.key === 'ArrowUp') {
            this.moveFocus(false);
            event.preventDefault();
        }
        // Escape key
        else if (event.key === 'Escape') {
            this.hideSuggestions();
            event.preventDefault();
        }
    }

    moveFocus(forward = true) {
        const focusables = Array.from(this.container.querySelectorAll('input,button'));
        const index = focusables.indexOf(document.activeElement);
        const newFocus = focusables[index + (forward ? 1 : -1)];
        if (newFocus) {
            newFocus.focus()
        }
    }

    async requestSuggestions() {
        if (Date.now() - this.lastPopulated < 50) {
            return;
        }

        const nameFilter = this.getNameFilterIfNeeded();
        const search = this.input.value.toLowerCase();
        const suggestions = await this.loadSuggestions(search, nameFilter);

        const toShow = suggestions.filter(val => {
            return search === '' || val.toLowerCase().startsWith(search);
        }).slice(0, 10);

        this.displaySuggestions(toShow);
    }

    getNameFilterIfNeeded() {
        if (this.type !== 'value') return null;
        return this.parent.querySelector('input').value;
    }

    /**
     * @param {String} search
     * @param {String|null} nameFilter
     * @returns {Promise<Object|String|*>}
     */
    async loadSuggestions(search, nameFilter = null) {
        // Truncate search to prevent over numerous lookups
        search = search.slice(0, 4);

        const params = {search, name: nameFilter};
        const cacheKey = `${this.url}:${JSON.stringify(params)}`;

        if (ajaxCache[cacheKey]) {
            return ajaxCache[cacheKey];
        }

        const resp = await window.$http.get(this.url, params);
        ajaxCache[cacheKey] = resp.data;
        return resp.data;
    }

    /**
     * @param {String[]} suggestions
     */
    displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            return this.hideSuggestions();
        }

        this.list.innerHTML = suggestions.map(value => `<li><button type="button" class="text-item">${escapeHtml(value)}</button></li>`).join('');
        this.list.style.display = 'block';
        for (const button of this.list.querySelectorAll('button')) {
            button.addEventListener('blur', this.hideSuggestionsIfFocusedLost.bind(this));
        }
    }

    hideSuggestions() {
        this.list.style.display = 'none';
    }

    hideSuggestionsIfFocusedLost(event) {
        if (!this.container.contains(event.relatedTarget)) {
            this.hideSuggestions();
        }
    }
}

export default AutoSuggest;