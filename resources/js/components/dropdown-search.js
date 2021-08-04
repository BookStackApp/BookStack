import {debounce} from "../services/util";

class DropdownSearch {

    setup() {
        this.elem = this.$el;
        this.searchInput = this.$refs.searchInput;
        this.loadingElem = this.$refs.loading;
        this.listContainerElem = this.$refs.listContainer;

        this.localSearchSelector = this.$opts.localSearchSelector;
        this.url = this.$opts.url;

        this.elem.addEventListener('show', this.onShow.bind(this));
        this.searchInput.addEventListener('input', this.onSearch.bind(this));

        this.runAjaxSearch = debounce(this.runAjaxSearch, 300, false);
    }

    onShow() {
        this.loadList();
    }

    onSearch() {
        const input = this.searchInput.value.toLowerCase().trim();
        if (this.localSearchSelector) {
            this.runLocalSearch(input);
        } else {
            this.toggleLoading(true);
            this.listContainerElem.innerHTML = '';
            this.runAjaxSearch(input);
        }
    }

    runAjaxSearch(searchTerm) {
        this.loadList(searchTerm);
    }

    runLocalSearch(searchTerm) {
        const listItems = this.listContainerElem.querySelectorAll(this.localSearchSelector);
        for (let listItem of listItems) {
            const match = !searchTerm || listItem.textContent.toLowerCase().includes(searchTerm);
            listItem.style.display = match ? 'flex' : 'none';
            listItem.classList.toggle('hidden', !match);
        }
    }

    async loadList(searchTerm = '') {
        this.listContainerElem.innerHTML = '';
        this.toggleLoading(true);

        try {
            const resp = await window.$http.get(this.getAjaxUrl(searchTerm));
            this.listContainerElem.innerHTML = resp.data;
        } catch (err) {
            console.error(err);
        }

        this.toggleLoading(false);
        if (this.localSearchSelector) {
            this.onSearch();
        }
    }

    getAjaxUrl(searchTerm = null) {
        if (!searchTerm) {
            return this.url;
        }

        const joiner = this.url.includes('?') ? '&' : '?';
        return `${this.url}${joiner}search=${encodeURIComponent(searchTerm)}`;
    }

    toggleLoading(show = false) {
        this.loadingElem.style.display = show ? 'block' : 'none';
    }

}

export default DropdownSearch;