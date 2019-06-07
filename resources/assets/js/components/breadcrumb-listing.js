

class BreadcrumbListing {

    constructor(elem) {
        this.elem = elem;
        this.searchInput = elem.querySelector('input');
        this.loadingElem = elem.querySelector('.loading-container');
        this.entityListElem = elem.querySelector('.breadcrumb-listing-entity-list');
        this.toggleElem = elem.querySelector('[dropdown-toggle]');

        // this.loadingElem.style.display = 'none';
        const entityDescriptor = elem.getAttribute('breadcrumb-listing').split(':');
        this.entityType = entityDescriptor[0];
        this.entityId = Number(entityDescriptor[1]);

        this.toggleElem.addEventListener('click', this.onShow.bind(this));
        this.searchInput.addEventListener('input', this.onSearch.bind(this));
        this.elem.addEventListener('keydown', this.keyDown.bind(this));
    }

    keyDown(event) {
        if (event.key === 'ArrowDown') {
            this.listFocusChange(1);
            event.preventDefault();
        } else if  (event.key === 'ArrowUp') {
            this.listFocusChange(-1);
            event.preventDefault();
        }
    }

    listFocusChange(indexChange = 1) {
        const links = Array.from(this.entityListElem.querySelectorAll('a:not(.hidden)'));
        const currentFocused = this.entityListElem.querySelector('a:focus');
        const currentFocusedIndex = links.indexOf(currentFocused);
        const defaultFocus = (indexChange > 0) ? links[0] : this.searchInput;
        const nextElem = links[currentFocusedIndex + indexChange] || defaultFocus;
        nextElem.focus();
    }

    onShow() {
        this.loadEntityView();
    }

    onSearch() {
        const input = this.searchInput.value.toLowerCase().trim();
        const listItems = this.entityListElem.querySelectorAll('.entity-list-item');
        for (let listItem of listItems) {
            const match = !input || listItem.textContent.toLowerCase().includes(input);
            listItem.style.display = match ? 'flex' : 'none';
            listItem.classList.toggle('hidden', !match);
        }
    }

    loadEntityView() {
        this.toggleLoading(true);

        const params = {
            'entity_id': this.entityId,
            'entity_type': this.entityType,
        };

        window.$http.get('/search/entity/siblings', params).then(resp => {
            this.entityListElem.innerHTML = resp.data;
        }).catch(err => {
            console.error(err);
        }).then(() => {
            this.toggleLoading(false);
            this.onSearch();
        });
    }

    toggleLoading(show = false) {
        this.loadingElem.style.display = show ? 'block' : 'none';
    }

}

export default BreadcrumbListing;