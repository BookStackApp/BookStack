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

/**
 * The available move actions.
 * The active function indicates if the action is possible for the given item.
 * The run function performs the move.
 * @type {{up: {active(Element, ?Element, Element): boolean, run(Element, ?Element, Element)}}}
 */
const moveActions = {
    up: {
        active(elem, parent, book) {
            return !(elem.previousElementSibling === null && !parent);
        },
        run(elem, parent, book) {
            const newSibling = elem.previousElementSibling || parent;
            newSibling.insertAdjacentElement('beforebegin', elem);
        }
    },
    down: {
        active(elem, parent, book) {
            return !(elem.nextElementSibling === null && !parent);
        },
        run(elem, parent, book) {
            const newSibling = elem.nextElementSibling || parent;
            newSibling.insertAdjacentElement('afterend', elem);
        }
    },
    next_book: {
        active(elem, parent, book) {
            return book.nextElementSibling !== null;
        },
        run(elem, parent, book) {
            const newList = book.nextElementSibling.querySelector('ul');
            newList.prepend(elem);
        }
    },
    prev_book: {
        active(elem, parent, book) {
            return book.previousElementSibling !== null;
        },
        run(elem, parent, book) {
            const newList = book.previousElementSibling.querySelector('ul');
            newList.appendChild(elem);
        }
    },
    next_chapter: {
        active(elem, parent, book) {
            return elem.dataset.type === 'page' && this.getNextChapter(elem, parent);
        },
        run(elem, parent, book) {
            const nextChapter = this.getNextChapter(elem, parent);
            nextChapter.querySelector('ul').prepend(elem);
        },
        getNextChapter(elem, parent) {
            const topLevel = (parent || elem);
            const topItems = Array.from(topLevel.parentElement.children);
            const index = topItems.indexOf(topLevel);
            return topItems.slice(index + 1).find(elem => elem.dataset.type === 'chapter');
        }
    },
    prev_chapter: {
        active(elem, parent, book) {
            return elem.dataset.type === 'page' && this.getPrevChapter(elem, parent);
        },
        run(elem, parent, book) {
            const prevChapter = this.getPrevChapter(elem, parent);
            prevChapter.querySelector('ul').append(elem);
        },
        getPrevChapter(elem, parent) {
            const topLevel = (parent || elem);
            const topItems = Array.from(topLevel.parentElement.children);
            const index = topItems.indexOf(topLevel);
            return topItems.slice(0, index).reverse().find(elem => elem.dataset.type === 'chapter');
        }
    },
    book_end: {
        active(elem, parent, book) {
            return parent || (parent === null && elem.nextElementSibling);
        },
        run(elem, parent, book) {
            book.querySelector('ul').append(elem);
        }
    },
    book_start: {
        active(elem, parent, book) {
            return parent || (parent === null && elem.previousElementSibling);
        },
        run(elem, parent, book) {
            book.querySelector('ul').prepend(elem);
        }
    },
    before_chapter: {
        active(elem, parent, book) {
            return parent;
        },
        run(elem, parent, book) {
            parent.insertAdjacentElement('beforebegin', elem);
        }
    },
    after_chapter: {
        active(elem, parent, book) {
            return parent;
        },
        run(elem, parent, book) {
            parent.insertAdjacentElement('afterend', elem);
        }
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
        this.setupMoveActions();

        window.$events.listen('entity-select-confirm', this.bookSelect.bind(this));
    }

    /**
     * Set up the handlers for the item-level move buttons.
     */
    setupMoveActions() {
        // Handle move button click
        this.container.addEventListener('click', event => {
            if (event.target.matches('[data-move]')) {
                const action = event.target.getAttribute('data-move');
                const sortItem = event.target.closest('[data-id]');
                this.runSortAction(sortItem, action);
            }
        });

        this.updateMoveActionStateForAll();
    }

    /**
     * Set up the handlers for the preset sort type buttons.
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
            this.updateMoveActionStateForAll();

            const summary = newBookContainer.querySelector('summary');
            summary.focus();
        });
    }

    /**
     * Set up the given book container element to have sortable items.
     * @param {Element} bookContainer
     */
    setupBookSortable(bookContainer) {
        const sortElems = Array.from(bookContainer.querySelectorAll('.sort-list, .sortable-page-sublist'));

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

    /**
     * Run the given sort action up the provided sort item.
     * @param {Element} item
     * @param {String} action
     */
    runSortAction(item, action) {
        const parentItem = item.parentElement.closest('li[data-id]');
        const parentBook = item.parentElement.closest('[data-type="book"]');
        moveActions[action].run(item, parentItem, parentBook);
        this.updateMapInput();
        this.updateMoveActionStateForAll();
        item.scrollIntoView({behavior: 'smooth', block: 'nearest'});
        item.focus();
    }

    /**
     * Update the state of the available move actions on this item.
     * @param {Element} item
     */
    updateMoveActionState(item) {
        const parentItem = item.parentElement.closest('li[data-id]');
        const parentBook = item.parentElement.closest('[data-type="book"]');
        for (const [action, functions] of Object.entries(moveActions)) {
            const moveButton = item.querySelector(`[data-move="${action}"]`);
            moveButton.disabled = !functions.active(item, parentItem, parentBook);
        }
    }

    updateMoveActionStateForAll() {
        const items = this.container.querySelectorAll('[data-type="chapter"],[data-type="page"]');
        for (const item of items) {
            this.updateMoveActionState(item);
        }
    }
}