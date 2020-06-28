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
        this.input.focus();
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
        const nameFilter = this.getNameFilterIfNeeded();
        const search = this.input.value.slice(0, 3);
        const suggestions = await this.loadSuggestions(search, nameFilter);
        let toShow = suggestions.slice(0, 6);
        if (search.length > 0) {
            toShow = suggestions.filter(val => {
                return val.toLowerCase().includes(search);
            }).slice(0, 6);
        }

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

        this.list.innerHTML = suggestions.map(value => `<li><button type="button">${escapeHtml(value)}</button></li>`).join('');
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