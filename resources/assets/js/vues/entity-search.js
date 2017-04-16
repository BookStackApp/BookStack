let data = {
    id: null,
    type: '',
    searching: false,
    searchTerm: '',
    searchResults: '',
};

let computed = {

};

let methods = {

    searchBook() {
        if (this.searchTerm.trim().length === 0) return;
        this.searching = true;
        this.searchResults = '';
        let url = window.baseUrl(`/search/${this.type}/${this.id}`);
        url += `?term=${encodeURIComponent(this.searchTerm)}`;
        this.$http.get(url).then(resp => {
            this.searchResults = resp.data;
        });
    },

    checkSearchForm() {
        this.searching = this.searchTerm > 0;
    },

    clearSearch() {
        this.searching = false;
        this.searchTerm = '';
    }

};

function mounted() {
    this.id = Number(this.$el.getAttribute('entity-id'));
    this.type = this.$el.getAttribute('entity-type');
}

module.exports = {
    data, computed, methods, mounted
};