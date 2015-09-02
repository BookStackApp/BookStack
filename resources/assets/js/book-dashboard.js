var bookDashboard = new Vue({
    el: '#book-dashboard',
    data: {
        searching: false,
        searchTerm: '',
        searchResults: ''
    },
    methods: {
        searchBook: function (e) {
            e.preventDefault();
            var term = this.searchTerm;
            if (term.length == 0) return;
            this.searching = true;
            this.searchResults = '';
            var searchUrl = this.$$.form.getAttribute('action');
            searchUrl += '?term=' + encodeURIComponent(term);
            this.$http.get(searchUrl, function (data) {
                this.$set('searchResults', data);
            });
        },
        checkSearchForm: function (e) {
            if (this.searchTerm.length < 1) {
                this.searching = false;
            }
        },
        clearSearch: function(e) {
            this.searching = false;
            this.searchTerm = '';
        }
    }
});