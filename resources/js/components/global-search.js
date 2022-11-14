/**
 * @extends {Component}
 */
import {htmlToDom} from "../services/dom";

class GlobalSearch {

    setup() {
        this.input = this.$refs.input;
        this.suggestions = this.$refs.suggestions;
        this.suggestionResultsWrap = this.$refs.suggestionResults;

        this.setupListeners();
    }

    setupListeners() {
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

        this.suggestions.style.display = 'block';
        this.suggestionResultsWrap.innerHTML = '';
        this.suggestionResultsWrap.append(resultDom);
    }

    hideSuggestions() {
        this.suggestions.style.display = null;
        this.suggestionResultsWrap.innerHTML = '';
    }
}

export default GlobalSearch;