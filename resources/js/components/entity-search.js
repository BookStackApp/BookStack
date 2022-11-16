import {onSelect} from "../services/dom";
import {Component} from "./component";

export class EntitySearch extends Component {
    setup() {
        this.entityId = this.$opts.entityId;
        this.entityType = this.$opts.entityType;

        this.contentView = this.$refs.contentView;
        this.searchView = this.$refs.searchView;
        this.searchResults = this.$refs.searchResults;
        this.searchInput = this.$refs.searchInput;
        this.searchForm = this.$refs.searchForm;
        this.clearButton = this.$refs.clearButton;
        this.loadingBlock = this.$refs.loadingBlock;

        this.setupListeners();
    }

    setupListeners() {
        this.searchInput.addEventListener('change', this.runSearch.bind(this));
        this.searchForm.addEventListener('submit', e => {
            e.preventDefault();
            this.runSearch();
        });

        onSelect(this.clearButton, this.clearSearch.bind(this));
    }

    runSearch() {
        const term = this.searchInput.value.trim();
        if (term.length === 0) {
            return this.clearSearch();
        }

        this.searchView.classList.remove('hidden');
        this.contentView.classList.add('hidden');
        this.loadingBlock.classList.remove('hidden');

        const url = window.baseUrl(`/search/${this.entityType}/${this.entityId}`);
        window.$http.get(url, {term}).then(resp => {
            this.searchResults.innerHTML = resp.data;
        }).catch(console.error).then(() => {
            this.loadingBlock.classList.add('hidden');
        });
    }

    clearSearch() {
        this.searchView.classList.add('hidden');
        this.contentView.classList.remove('hidden');
        this.loadingBlock.classList.add('hidden');
        this.searchInput.value = '';
    }
}