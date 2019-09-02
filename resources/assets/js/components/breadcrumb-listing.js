

class BreadcrumbListing {

    constructor(elem) {
        this.elem = elem;
        this.searchInput = elem.querySelector('input');
        this.loadingElem = elem.querySelector('.loading-container');
        this.entityListElem = elem.querySelector('.breadcrumb-listing-entity-list');

        // this.loadingElem.style.display = 'none';
        const entityDescriptor = elem.getAttribute('breadcrumb-listing').split(':');
        this.entityType = entityDescriptor[0];
        this.entityId = Number(entityDescriptor[1]);

        this.elem.addEventListener('show', this.onShow.bind(this));
        this.searchInput.addEventListener('input', this.onSearch.bind(this));
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