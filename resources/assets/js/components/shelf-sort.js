import "jquery-sortable";

class ShelfSort {

    constructor(elem) {
        this.elem = elem;
        this.sortGroup = this.initSortable();
        this.input = document.getElementById('books-input');
        this.setupListeners();
    }

    initSortable() {
        const placeHolderContent = this.getPlaceholderHTML();
        // TODO - Load sortable at this point
        return $('.scroll-box').sortable({
            group: 'shelf-books',
            exclude: '.instruction,.scroll-box-placeholder',
            containerSelector: 'div.scroll-box',
            itemSelector: '.scroll-box-item',
            placeholder: placeHolderContent,
            onDrop: this.onDrop.bind(this)
        });
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

    onDrop($item, container, _super) {
        this.onChange();
        _super($item, container);
    }

    onChange() {
        const data = this.sortGroup.sortable('serialize').get();
        this.input.value = data[0].map(item => item.id).join(',');
        const instruction = this.elem.querySelector('.scroll-box-item.instruction');
        instruction.parentNode.insertBefore(instruction, instruction.parentNode.children[0]);
    }

    getPlaceholderHTML() {
        const placeHolder = document.querySelector('.scroll-box-placeholder');
        placeHolder.style.display = 'block';
        const placeHolderContent = placeHolder.outerHTML;
        placeHolder.style.display = 'none';
        return placeHolderContent;
    }


}

export default ShelfSort;