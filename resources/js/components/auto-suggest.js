import {escapeHtml} from "../services/util";
import {onChildEvent} from "../services/dom";
import {Component} from "./component";
import {KeyboardNavigationHandler} from "../services/keyboard-navigation";

const ajaxCache = {};

/**
 * AutoSuggest
 */
export class AutoSuggest extends Component {
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
        const navHandler = new KeyboardNavigationHandler(
            this.list,
            event => {
                this.input.focus();
                setTimeout(() => this.hideSuggestions(), 1);
            },
            event => {
                event.preventDefault();
                this.selectSuggestion(event.target.textContent);
            },
        );
        navHandler.shareHandlingToEl(this.input);

        onChildEvent(this.list, '.text-item', 'click', (event, el) => {
            this.selectSuggestion(el.textContent);
        });

        this.input.addEventListener('input', this.requestSuggestions.bind(this));
        this.input.addEventListener('focus', this.requestSuggestions.bind(this));
        this.input.addEventListener('blur', this.hideSuggestionsIfFocusedLost.bind(this));
        this.input.addEventListener('keydown', event => {
            if (event.key === 'Tab') {
                this.hideSuggestions();
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

        // This used to use <button>s but was changed to div elements since Safari would not focus on buttons
        // on which causes a range of other complexities related to focus handling.
        this.list.innerHTML = suggestions.map(value => `<li><div tabindex="-1" class="text-item">${escapeHtml(value)}</div></li>`).join('');
        this.list.style.display = 'block';
        for (const button of this.list.querySelectorAll('.text-item')) {
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