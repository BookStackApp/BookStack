/**
 * @extends {Component}
 */
import {htmlToDom} from "../services/dom";

class GlobalSearch {

    setup() {
        this.container = this.$el;
        this.input = this.$refs.input;
        this.suggestions = this.$refs.suggestions;
        this.suggestionResultsWrap = this.$refs.suggestionResults;

        this.setupListeners();
    }

    setupListeners() {
        this.hideOnOuterEventListener = this.hideOnOuterEventListener.bind(this);

        this.input.addEventListener('input', () => {
            const value = this.input.value;
            if (value.length > 0) {
                this.updateSuggestions(value);
            }  else {
                this.hideSuggestions();
            }
        });
    }

    async updateSuggestions(search) {
        const {data: results} = await window.$http.get('/ajax/search/entities', {term: search, count: 5});
        const resultDom = htmlToDom(results);

        const childrenToTrim = Array.from(resultDom.children).slice(9);
        for (const child of childrenToTrim) {
            child.remove();
        }

        this.suggestionResultsWrap.innerHTML = '';
        this.suggestionResultsWrap.append(resultDom);
        if (!this.container.classList.contains('search-active')) {
            this.showSuggestions();
        }
    }

    showSuggestions() {
        this.container.classList.add('search-active');
        document.addEventListener('click', this.hideOnOuterEventListener);
        document.addEventListener('focusin', this.hideOnOuterEventListener);
        window.requestAnimationFrame(() => {
            this.suggestions.classList.add('search-suggestions-animation');
        })
    }

    hideSuggestions() {
        this.container.classList.remove('search-active');
        this.suggestions.classList.remove('search-suggestions-animation');
        this.suggestionResultsWrap.innerHTML = '';
        document.removeEventListener('click', this.hideOnOuterEventListener);
        document.removeEventListener('focusin', this.hideOnOuterEventListener);
    }

    hideOnOuterEventListener(event) {
        if (!this.container.contains(event.target)) {
            this.hideSuggestions();
        }
    };
}

export default GlobalSearch;