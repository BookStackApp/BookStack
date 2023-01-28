import {onChildEvent} from "../services/dom";
import {Component} from "./component";

/**
 * Entity Selector
 */
export class EntitySelector extends Component {

    setup() {
        this.elem = this.$el;
        this.entityTypes = this.$opts.entityTypes || 'page,book,chapter';
        this.entityPermission = this.$opts.entityPermission || 'view';

        this.input = this.$refs.input;
        this.searchInput = this.$refs.search;
        this.loading = this.$refs.loading;
        this.resultsContainer = this.$refs.results;

        this.search = '';
        this.lastClick = 0;
        this.selectedItemData = null;

        this.setupListeners();
        this.showLoading();
        this.initialLoad();
    }

    setupListeners() {
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

        // Keyboard navigation
        onChildEvent(this.$el, '[data-entity-type]', 'keydown', (e, el) => {
            if (e.ctrlKey && e.code === 'Enter') {
                const form = this.$el.closest('form');
                if (form) {
                    form.submit();
                    e.preventDefault();
                    return;
                }
            }

            if (e.code === 'ArrowDown') {
                this.focusAdjacent(true);
            }
            if (e.code === 'ArrowUp') {
                this.focusAdjacent(false);
            }
        });

        this.searchInput.addEventListener('keydown', e => {
            if (e.code === 'ArrowDown') {
                this.focusAdjacent(true);
            }
        })
    }

    focusAdjacent(forward = true) {
        const items = Array.from(this.resultsContainer.querySelectorAll('[data-entity-type]'));
        const selectedIndex = items.indexOf(document.activeElement);
        const newItem = items[selectedIndex+ (forward ? 1 : -1)] || items[0];
        if (newItem) {
            newItem.focus();
        }
    }

    reset() {
        this.searchInput.value = '';
        this.showLoading();
        this.initialLoad();
    }

    focusSearch() {
        this.searchInput.focus();
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
        window.$http.get(this.searchUrl()).then(resp => {
            this.resultsContainer.innerHTML = resp.data;
            this.hideLoading();
        })
    }

    searchUrl() {
        return `/search/entity-selector?types=${encodeURIComponent(this.entityTypes)}&permission=${encodeURIComponent(this.entityPermission)}`;
    }

    searchEntities(searchTerm) {
        this.input.value = '';
        const url = `${this.searchUrl()}&term=${encodeURIComponent(searchTerm)}`;
        window.$http.get(url).then(resp => {
            this.resultsContainer.innerHTML = resp.data;
            this.hideLoading();
        });
    }

    isDoubleClick() {
        const now = Date.now();
        const answer = now - this.lastClick < 300;
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
        const selected = this.elem.querySelectorAll('.selected');
        for (const selectedElem of selected) {
            selectedElem.classList.remove('selected', 'primary-background');
        }
        this.selectedItemData = null;
    }

}