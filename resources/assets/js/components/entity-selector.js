
class EntitySelector {

    constructor(elem) {
        this.elem = elem;
        this.search = '';
        this.lastClick = 0;
        this.selectedItemData = null;

        const entityTypes = elem.hasAttribute('entity-types') ? elem.getAttribute('entity-types') : 'page,book,chapter';
        const entityPermission = elem.hasAttribute('entity-permission') ? elem.getAttribute('entity-permission') : 'view';
        this.searchUrl = window.baseUrl(`/ajax/search/entities?types=${encodeURIComponent(entityTypes)}&permission=${encodeURIComponent(entityPermission)}`);

        this.input = elem.querySelector('[entity-selector-input]');
        this.searchInput = elem.querySelector('[entity-selector-search]');
        this.loading = elem.querySelector('[entity-selector-loading]');
        this.resultsContainer = elem.querySelector('[entity-selector-results]');
        this.addButton = elem.querySelector('[entity-selector-add-button]');

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

        if (this.addButton) {
            this.addButton.addEventListener('click', event => {
                if (this.selectedItemData) {
                    this.confirmSelection(this.selectedItemData);
                    this.unselectAll();
                }
            });
        }

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

        const link = item.getAttribute('href');
        const name = item.querySelector('.entity-list-item-name').textContent;
        const data = {id: Number(id), name: name, link: link};

        if (isSelected) {
            item.classList.add('selected');
            this.selectedItemData = data;
        } else {
            window.$events.emit('entity-select-change', null)
        }

        if (!isDblClick && !isSelected) return;

        if (isDblClick) {
            this.confirmSelection(data);
        }
        if (isSelected) {
            window.$events.emit('entity-select-change', data)
        }
    }

    confirmSelection(data) {
        window.$events.emit('entity-select-confirm', data);
    }

    unselectAll() {
        let selected = this.elem.querySelectorAll('.selected');
        for (let selectedElem of selected) {
            selectedElem.classList.remove('selected', 'primary-background');
        }
        this.selectedItemData = null;
    }

}

export default EntitySelector;