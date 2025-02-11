import Sortable from 'sortablejs';
import {Component} from './component';
import {buildListActions, sortActionClickListener} from '../services/dual-lists.ts';

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
        const listActions = buildListActions(this.allBookList, this.shelfBookList);
        const sortActionListener = sortActionClickListener(listActions, this.onChange.bind(this));
        this.elem.addEventListener('click', sortActionListener);

        this.bookSearchInput.addEventListener('input', () => {
            this.filterBooksByName(this.bookSearchInput.value);
        });

        this.sortButtonContainer.addEventListener('click', event => {
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
            this.allBookList.style.height = `${this.allBookList.getBoundingClientRect().height}px`;
        }

        const books = this.allBookList.children;
        const lowerFilter = filterVal.trim().toLowerCase();

        for (const bookEl of books) {
            const show = !filterVal || bookEl.textContent.toLowerCase().includes(lowerFilter);
            bookEl.style.display = show ? null : 'none';
        }
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
                return bProp.localeCompare(aProp);
            }

            return aProp.localeCompare(bProp);
        });

        for (const book of books) {
            this.shelfBookList.append(book);
        }

        this.lastSort = (this.lastSort === sortProperty) ? null : sortProperty;
        this.onChange();
    }

}
