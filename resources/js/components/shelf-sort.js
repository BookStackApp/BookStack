import Sortable from "sortablejs";

class ShelfSort {

    constructor(elem) {
        this.elem = elem;
        this.input = document.getElementById('books-input');
        this.shelfBooksList = elem.querySelector('[shelf-sort-assigned-books]');

        this.initSortable();
        this.setupListeners();
    }

    initSortable() {
        const scrollBoxes = this.elem.querySelectorAll('.scroll-box');
        for (let scrollBox of scrollBoxes) {
            new Sortable(scrollBox, {
                group: 'shelf-books',
                ghostClass: 'primary-background-light',
                animation: 150,
                onSort: this.onChange.bind(this),
            });
        }
    }

    setupListeners() {
        this.elem.addEventListener('click', event => {
            const sortItem = event.target.closest('.scroll-box-item:not(.instruction)');
            if (sortItem) {
                event.preventDefault();
                this.sortItemClick(sortItem);
            }
        });
    }

    /**
     * Called when a sort item is clicked.
     * @param {Element} sortItem
     */
    sortItemClick(sortItem) {
        const lists = this.elem.querySelectorAll('.scroll-box');
        const newList = Array.from(lists).filter(list => sortItem.parentElement !== list);
        if (newList.length > 0) {
            newList[0].appendChild(sortItem);
        }
        this.onChange();
    }

    onChange() {
        const shelfBookElems = Array.from(this.shelfBooksList.querySelectorAll('[data-id]'));
        this.input.value = shelfBookElems.map(elem => elem.getAttribute('data-id')).join(',');
    }

}

export default ShelfSort;