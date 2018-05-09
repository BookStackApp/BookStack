
class EntitySelector {

    constructor(elem) {
        this.elem = elem;
        this.search = '';
        this.lastClick = 0;

        let entityTypes = elem.hasAttribute('entity-types') ? elem.getAttribute('entity-types') : 'page,book,chapter';
        let entityPermission = elem.hasAttribute('entity-permission') ? elem.getAttribute('entity-permission') : 'view';
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
        let url = this.searchUrl + `&term=${encodeURIComponent(searchTerm)}`;
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
        let t = event.target;

        if (t.matches('.entity-list-item  *')) {
            event.preventDefault();
            event.stopPropagation();
            let item = t.closest('[data-entity-type]');
            this.selectItem(item);
        } else if (t.matches('[data-entity-type]')) {
            this.selectItem(t)
        }

    }

    selectItem(item) {
        let isDblClick = this.isDoubleClick();
        let type = item.getAttribute('data-entity-type');
        let id = item.getAttribute('data-entity-id');
        let isSelected = !item.classList.contains('selected') || isDblClick;

        this.unselectAll();
        this.input.value = isSelected ? `${type}:${id}` : '';

        if (!isSelected) window.$events.emit('entity-select-change', null);
        if (isSelected) {
            item.classList.add('selected');
            item.classList.add('primary-background');
        }
        if (!isDblClick && !isSelected) return;

        let link = item.querySelector('.entity-list-item-link').getAttribute('href');
        let name = item.querySelector('.entity-list-item-name').textContent;
        let data = {id: Number(id), name: name, link: link};

        if (isDblClick) window.$events.emit('entity-select-confirm', data);
        if (isSelected) window.$events.emit('entity-select-change', data);
    }

    unselectAll() {
        let selected = this.elem.querySelectorAll('.selected');
        for (let i = 0, len = selected.length; i < len; i++) {
            selected[i].classList.remove('selected');
            selected[i].classList.remove('primary-background');
        }
    }

}

module.exports = EntitySelector;