
class EntitySelector {

    constructor(elem) {
        this.elem = elem;
        this.search = '';
        this.lastClick = 0;

        const entityTypes = elem.hasAttribute('entity-types') ? elem.getAttribute('entity-types') : 'page,book,chapter';
        const entityPermission = elem.hasAttribute('entity-permission') ? elem.getAttribute('entity-permission') : 'view';
        this.searchUrl = window.baseUrl(`/ajax/search/entities?types=${encodeURIComponent(entityTypes)}&permission=${encodeURIComponent(entityPermission)}`);

        this.input = elem.querySelector('[entity-selector-input]');
        this.searchInput = elem.querySelector('[entity-selector-search]');
        this.loading = elem.querySelector('[entity-selector-loading]');
        this.resultsContainer = elem.querySelector('[entity-selector-results]');

        this.elem.addEventListener('click', this.onClick.bind(this));

        let lastSearch = 0;
        this.searchInput.addEventListener('input', event => {
            lastSearch = Date.now();
            this.showLoading();
            setTimeout(() => {
                if (Date.now() - lastSearch < 199) return;
                this.searchEntities(this.searchInput.value);
            }, 200);
        });

        this.searchInput.addEventListener('keydown', event => {
            if (event.keyCode === 13) event.preventDefault();
        });

        this.showLoading();
        this.initialLoad();
    }

    showLoading() {
        this.loading.style.display = 'block';
        this.resultsContainer.style.display = 'none';
    }

    hideLoading() {
        this.loading.style.display = 'none';
        this.resultsContainer.style.display = 'block';
    }

    initialLoad() {
        window.$http.get(this.searchUrl).then(resp => {
            this.resultsContainer.innerHTML = resp.data;
            this.hideLoading();
        })
    }

    searchEntities(searchTerm) {
        this.input.value = '';
        let url = `${this.searchUrl}&term=${encodeURIComponent(searchTerm)}`;
        window.$http.get(url).then(resp => {
            this.resultsContainer.innerHTML = resp.data;
            this.hideLoading();
        });
    }

    isDoubleClick() {
        let now = Date.now();
        let answer = now - this.lastClick < 300;
        this.lastClick = now;
        return answer;
    }

    onClick(event) {
        const listItem = event.target.closest('[data-entity-type]');
        if (listItem) {
            event.preventDefault();
            event.stopPropagation();
            this.selectItem(listItem);
        }
    }

    selectItem(item) {
        const isDblClick = this.isDoubleClick();
        const type = item.getAttribute('data-entity-type');
        const id = item.getAttribute('data-entity-id');
        const isSelected = (!item.classList.contains('selected') || isDblClick);

        this.unselectAll();
        this.input.value = isSelected ? `${type}:${id}` : '';

        if (isSelected) {
            item.classList.add('selected');
        } else {
            window.$events.emit('entity-select-change', null)
        }

        if (!isDblClick && !isSelected) return;

        const link = item.getAttribute('href');
        const name = item.querySelector('.entity-list-item-name').textContent;
        const data = {id: Number(id), name: name, link: link};

        if (isDblClick) {
            window.$events.emit('entity-select-confirm', data)
        }
        if (isSelected) {
            window.$events.emit('entity-select-change', data)
        }
    }

    unselectAll() {
        let selected = this.elem.querySelectorAll('.selected');
        for (let selectedElem of selected) {
            selectedElem.classList.remove('selected', 'primary-background');
        }
    }

}

export default EntitySelector;