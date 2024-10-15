import {htmlToDom} from '../services/dom.ts';
import {debounce} from '../services/util.ts';
import {KeyboardNavigationHandler} from '../services/keyboard-navigation.ts';
import {Component} from './component';

/**
 * Global (header) search box handling.
 * Mainly to show live results preview.
 */
export class GlobalSearch extends Component {

    setup() {
        this.container = this.$el;
        this.input = this.$refs.input;
        this.suggestions = this.$refs.suggestions;
        this.suggestionResultsWrap = this.$refs.suggestionResults;
        this.loadingWrap = this.$refs.loading;
        this.button = this.$refs.button;

        this.setupListeners();
    }

    setupListeners() {
        const updateSuggestionsDebounced = debounce(this.updateSuggestions.bind(this), 200, false);

        // Handle search input changes
        this.input.addEventListener('input', () => {
            const {value} = this.input;
            if (value.length > 0) {
                this.loadingWrap.style.display = 'block';
                this.suggestionResultsWrap.style.opacity = '0.5';
                updateSuggestionsDebounced(value);
            } else {
                this.hideSuggestions();
            }
        });

        // Allow double click to show auto-click suggestions
        this.input.addEventListener('dblclick', () => {
            this.input.setAttribute('autocomplete', 'on');
            this.button.focus();
            this.input.focus();
        });

        new KeyboardNavigationHandler(this.container, () => {
            this.hideSuggestions();
        });
    }

    /**
     * @param {String} search
     */
    async updateSuggestions(search) {
        const {data: results} = await window.$http.get('/search/suggest', {term: search});
        if (!this.input.value) {
            return;
        }

        const resultDom = htmlToDom(results);

        this.suggestionResultsWrap.innerHTML = '';
        this.suggestionResultsWrap.style.opacity = '1';
        this.loadingWrap.style.display = 'none';
        this.suggestionResultsWrap.append(resultDom);
        if (!this.container.classList.contains('search-active')) {
            this.showSuggestions();
        }
    }

    showSuggestions() {
        this.container.classList.add('search-active');
        window.requestAnimationFrame(() => {
            this.suggestions.classList.add('search-suggestions-animation');
        });
    }

    hideSuggestions() {
        this.container.classList.remove('search-active');
        this.suggestions.classList.remove('search-suggestions-animation');
        this.suggestionResultsWrap.innerHTML = '';
    }

}
