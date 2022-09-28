import Sortable from "sortablejs";

class ShelfSort {

    setup() {
        this.elem = this.$el;
        this.input = this.$refs.input;
        this.shelfBookList = this.$refs.shelfBookList;
        this.allBookList = this.$refs.allBookList;
        this.bookSearchInput = this.$refs.bookSearch;

        this.initSortable();
        this.setupListeners();
    }

    initSortable() {
        const scrollBoxes = this.elem.querySelectorAll('.scroll-box');
        for (let scrollBox of scrollBoxes) {
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
            const sortItem = event.target.closest('.scroll-box-item');
            if (sortItem) {
                event.preventDefault();
                this.sortItemClick(sortItem);
            }
        });

        this.bookSearchInput.addEventListener('input', event => {
            this.filterBooksByName(this.bookSearchInput.value);
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
        const shelfBookElems = Array.from(this.shelfBookList.querySelectorAll('[data-id]'));
        this.input.value = shelfBookElems.map(elem => elem.getAttribute('data-id')).join(',');
    }

}

export default ShelfSort;