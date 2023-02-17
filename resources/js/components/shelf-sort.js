import Sortable from "sortablejs";
import {Component} from "./component";

/**
 * @type {Object<string, function(HTMLElement, HTMLElement, HTMLElement)>}
 */
const itemActions = {
    move_up(item, shelfBooksList, allBooksList) {
        const list = item.parentNode;
        const index = Array.from(list.children).indexOf(item);
        const newIndex = Math.max(index - 1, 0);
        list.insertBefore(item, list.children[newIndex] || null);
    },
    move_down(item, shelfBooksList, allBooksList) {
        const list = item.parentNode;
        const index = Array.from(list.children).indexOf(item);
        const newIndex = Math.min(index + 2, list.children.length);
        list.insertBefore(item, list.children[newIndex] || null);
    },
    remove(item, shelfBooksList, allBooksList) {
        allBooksList.appendChild(item);
    },
    add(item, shelfBooksList, allBooksList) {
        shelfBooksList.appendChild(item);
    },
};

export class ShelfSort extends Component {

    setup() {
        this.elem = this.$el;
        this.input = this.$refs.input;
        this.shelfBookList = this.$refs.shelfBookList;
        this.allBookList = this.$refs.allBookList;
        this.bookSearchInput = this.$refs.bookSearch;
        this.sortButtonContainer = this.$refs.sortButtonContainer;

        this.lastSort = null;

        this.initSortable();
        this.setupListeners();
    }

    initSortable() {
        const scrollBoxes = this.elem.querySelectorAll('.scroll-box');
        for (const scrollBox of scrollBoxes) {
            new Sortable(scrollBox, {
                group: 'shelf-books',
                ghostClass: 'primary-background-light',
                handle: '.handle',
                animation: 150,
                onSort: this.onChange.bind(this),
            });
        }
    }

    setupListeners() {
        this.elem.addEventListener('click', event => {
            const sortItemAction = event.target.closest('.scroll-box-item button[data-action]');
            if (sortItemAction) {
                this.sortItemActionClick(sortItemAction);
            }
        });

        this.bookSearchInput.addEventListener('input', event => {
            this.filterBooksByName(this.bookSearchInput.value);
        });

        this.sortButtonContainer.addEventListener('click' , event => {
            const button = event.target.closest('button[data-sort]');
            if (button) {
                this.sortShelfBooks(button.dataset.sort);
            }
        });
    }

    /**
     * @param {String} filterVal
     */
    filterBooksByName(filterVal) {

        // Set height on first search, if not already set, to prevent the distraction
        // of the list height jumping around
        if (!this.allBookList.style.height) {
            this.allBookList.style.height = this.allBookList.getBoundingClientRect().height + 'px';
        }

        const books = this.allBookList.children;
        const lowerFilter = filterVal.trim().toLowerCase();

        for (const bookEl of books) {
            const show = !filterVal || bookEl.textContent.toLowerCase().includes(lowerFilter);
            bookEl.style.display = show ? null : 'none';
        }
    }

    /**
     * Called when a sort item action button is clicked.
     * @param {HTMLElement} sortItemAction
     */
    sortItemActionClick(sortItemAction) {
        const sortItem = sortItemAction.closest('.scroll-box-item');
        const action = sortItemAction.dataset.action;

        const actionFunction = itemActions[action];
        actionFunction(sortItem, this.shelfBookList, this.allBookList);

        this.onChange();
    }

    onChange() {
        const shelfBookElems = Array.from(this.shelfBookList.querySelectorAll('[data-id]'));
        this.input.value = shelfBookElems.map(elem => elem.getAttribute('data-id')).join(',');
    }

    sortShelfBooks(sortProperty) {
        const books = Array.from(this.shelfBookList.children);
        const reverse = sortProperty === this.lastSort;

        books.sort((bookA, bookB) => {
            const aProp = bookA.dataset[sortProperty].toLowerCase();
            const bProp = bookB.dataset[sortProperty].toLowerCase();

            if (reverse) {
                return aProp < bProp ? (aProp === bProp ? 0 : 1) : -1;
            }

            return aProp < bProp ? (aProp === bProp ? 0 : -1) : 1;
        });

        for (const book of books) {
            this.shelfBookList.append(book);
        }

        this.lastSort = (this.lastSort === sortProperty) ? null : sortProperty;
        this.onChange();
    }

}