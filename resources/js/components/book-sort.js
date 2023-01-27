import Sortable, {MultiDrag} from "sortablejs";
import {Component} from "./component";
import {htmlToDom} from "../services/dom";

// Auto sort control
const sortOperations = {
    name: function(a, b) {
        const aName = a.getAttribute('data-name').trim().toLowerCase();
        const bName = b.getAttribute('data-name').trim().toLowerCase();
        return aName.localeCompare(bName);
    },
    created: function(a, b) {
        const aTime = Number(a.getAttribute('data-created'));
        const bTime = Number(b.getAttribute('data-created'));
        return bTime - aTime;
    },
    updated: function(a, b) {
        const aTime = Number(a.getAttribute('data-updated'));
        const bTime = Number(b.getAttribute('data-updated'));
        return bTime - aTime;
    },
    chaptersFirst: function(a, b) {
        const aType = a.getAttribute('data-type');
        const bType = b.getAttribute('data-type');
        if (aType === bType) {
            return 0;
        }
        return (aType === 'chapter' ? -1 : 1);
    },
    chaptersLast: function(a, b) {
        const aType = a.getAttribute('data-type');
        const bType = b.getAttribute('data-type');
        if (aType === bType) {
            return 0;
        }
        return (aType === 'chapter' ? 1 : -1);
    },
};

export class BookSort extends Component {

    setup() {
        this.container = this.$el;
        this.sortContainer = this.$refs.sortContainer;
        this.input = this.$refs.input;

        Sortable.mount(new MultiDrag());

        const initialSortBox = this.container.querySelector('.sort-box');
        this.setupBookSortable(initialSortBox);
        this.setupSortPresets();

        window.$events.listen('entity-select-confirm', this.bookSelect.bind(this));
    }

    /**
     * Setup the handlers for the preset sort type buttons.
     */
    setupSortPresets() {
        let lastSort = '';
        let reverse = false;
        const reversibleTypes = ['name', 'created', 'updated'];

        this.sortContainer.addEventListener('click', event => {
            const sortButton = event.target.closest('.sort-box-options [data-sort]');
            if (!sortButton) return;

            event.preventDefault();
            const sortLists = sortButton.closest('.sort-box').querySelectorAll('ul');
            const sort = sortButton.getAttribute('data-sort');

            reverse = (lastSort === sort) ? !reverse : false;
            let sortFunction = sortOperations[sort];
            if (reverse && reversibleTypes.includes(sort)) {
                sortFunction = function(a, b) {
                    return 0 - sortOperations[sort](a, b)
                };
            }

            for (let list of sortLists) {
                const directItems = Array.from(list.children).filter(child => child.matches('li'));
                directItems.sort(sortFunction).forEach(sortedItem => {
                    list.appendChild(sortedItem);
                });
            }

            lastSort = sort;
            this.updateMapInput();
        });
    }

    /**
     * Handle book selection from the entity selector.
     * @param {Object} entityInfo
     */
    bookSelect(entityInfo) {
        const alreadyAdded = this.container.querySelector(`[data-type="book"][data-id="${entityInfo.id}"]`) !== null;
        if (alreadyAdded) return;

        const entitySortItemUrl = entityInfo.link + '/sort-item';
        window.$http.get(entitySortItemUrl).then(resp => {
            const newBookContainer = htmlToDom(resp.data);
            this.sortContainer.append(newBookContainer);
            this.setupBookSortable(newBookContainer);
        });
    }

    /**
     * Set up the given book container element to have sortable items.
     * @param {Element} bookContainer
     */
    setupBookSortable(bookContainer) {
        const sortElems = [bookContainer.querySelector('.sort-list')];
        sortElems.push(...bookContainer.querySelectorAll('.entity-list-item + ul'));

        const bookGroupConfig = {
            name: 'book',
            pull: ['book', 'chapter'],
            put: ['book', 'chapter'],
        };

        const chapterGroupConfig = {
            name: 'chapter',
            pull: ['book', 'chapter'],
            put: function(toList, fromList, draggedElem) {
                return draggedElem.getAttribute('data-type') === 'page';
            }
        };

        for (const sortElem of sortElems) {
            Sortable.create(sortElem, {
                group: sortElem.classList.contains('sort-list') ? bookGroupConfig : chapterGroupConfig,
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onSort: this.updateMapInput.bind(this),
                dragClass: 'bg-white',
                ghostClass: 'primary-background-light',
                multiDrag: true,
                multiDragKey: 'Control',
                selectedClass: 'sortable-selected',
            });
        }
    }

    /**
     * Update the input with our sort data.
     */
    updateMapInput() {
        const pageMap = this.buildEntityMap();
        this.input.value = JSON.stringify(pageMap);
    }

    /**
     * Build up a mapping of entities with their ordering and nesting.
     * @returns {Array}
     */
    buildEntityMap() {
        const entityMap = [];
        const lists = this.container.querySelectorAll('.sort-list');

        for (let list of lists) {
            const bookId = list.closest('[data-type="book"]').getAttribute('data-id');
            const directChildren = Array.from(list.children)
                .filter(elem => elem.matches('[data-type="page"], [data-type="chapter"]'));
            for (let i = 0; i < directChildren.length; i++) {
                this.addBookChildToMap(directChildren[i], i, bookId, entityMap);
            }
        }

        return entityMap;
    }

    /**
     * Parse a sort item and add it to a data-map array.
     * Parses sub0items if existing also.
     * @param {Element} childElem
     * @param {Number} index
     * @param {Number} bookId
     * @param {Array} entityMap
     */
    addBookChildToMap(childElem, index, bookId, entityMap) {
        const type = childElem.getAttribute('data-type');
        const parentChapter = false;
        const childId = childElem.getAttribute('data-id');

        entityMap.push({
            id: childId,
            sort: index,
            parentChapter: parentChapter,
            type: type,
            book: bookId
        });

        const subPages = childElem.querySelectorAll('[data-type="page"]');
        for (let i = 0; i < subPages.length; i++) {
            entityMap.push({
                id: subPages[i].getAttribute('data-id'),
                sort: i,
                parentChapter: childId,
                type: 'page',
                book: bookId
            });
        }
    }

}