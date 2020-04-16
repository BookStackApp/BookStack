
const template = `
    <div>
        <input :value="value" :autosuggest-type="type" ref="input"
            :placeholder="placeholder" :name="name"
            type="text"
            @input="inputUpdate($event.target.value)" @focus="inputUpdate($event.target.value)"
            @blur="inputBlur"
            @keydown="inputKeydown"
            :aria-label="placeholder"
        />
        <ul class="suggestion-box" v-if="showSuggestions">
            <li v-for="(suggestion, i) in suggestions"
                @click="selectSuggestion(suggestion)"
                :class="{active: (i === active)}">{{suggestion}}</li>
        </ul>
    </div>
`;

function data() {
    return {
        suggestions: [],
        showSuggestions: false,
        active: 0,
    };
}

const ajaxCache = {};

const props = ['url', 'type', 'value', 'placeholder', 'name'];

function getNameInputVal(valInput) {
    let parentRow = valInput.parentNode.parentNode;
    let nameInput = parentRow.querySelector('[autosuggest-type="name"]');
    return (nameInput === null) ? '' : nameInput.value;
}

const methods = {

    inputUpdate(inputValue) {
        this.$emit('input', inputValue);
        let params = {};

        if (this.type === 'value') {
            let nameVal = getNameInputVal(this.$el);
            if (nameVal !== "") params.name = nameVal;
        }

        this.getSuggestions(inputValue.slice(0, 3), params).then(suggestions => {
            if (inputValue.length === 0) {
                this.displaySuggestions(suggestions.slice(0, 6));
                return;
            }
            // Filter to suggestions containing searched term
            suggestions = suggestions.filter(item => {
                return item.toLowerCase().indexOf(inputValue.toLowerCase()) !== -1;
            }).slice(0, 4);
            this.displaySuggestions(suggestions);
        });
    },

    inputBlur() {
        setTimeout(() => {
            this.$emit('blur');
            this.showSuggestions = false;
        }, 100);
    },

    inputKeydown(event) {
        if (event.key === 'Enter') event.preventDefault();
        if (!this.showSuggestions) return;

        // Down arrow
        if (event.key === 'ArrowDown') {
            this.active = (this.active === this.suggestions.length - 1) ? 0 : this.active+1;
        }
        // Up Arrow
        else if (event.key === 'ArrowUp') {
            this.active = (this.active === 0) ? this.suggestions.length - 1 : this.active-1;
        }
        // Enter key
        else if ((event.key === 'Enter') && !event.shiftKey) {
            this.selectSuggestion(this.suggestions[this.active]);
        }
        // Escape key
        else if (event.key === 'Escape') {
            this.showSuggestions = false;
        }
    },

    displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            this.suggestions = [];
            this.showSuggestions = false;
            return;
        }

        this.suggestions = suggestions;
        this.showSuggestions = true;
        this.active = 0;
    },

    selectSuggestion(suggestion) {
        this.$refs.input.value = suggestion;
        this.$refs.input.focus();
        this.$emit('input', suggestion);
        this.showSuggestions = false;
    },

    /**
     * Get suggestions from BookStack. Store and use local cache if already searched.
     * @param {String} input
     * @param {Object} params
     */
    getSuggestions(input, params) {
        params.search = input;
        const cacheKey = `${this.url}:${JSON.stringify(params)}`;

        if (typeof ajaxCache[cacheKey] !== "undefined") {
            return Promise.resolve(ajaxCache[cacheKey]);
        }

        return this.$http.get(this.url, params).then(resp => {
            ajaxCache[cacheKey] = resp.data;
            return resp.data;
        });
    }

};

export default {template, data, props, methods};