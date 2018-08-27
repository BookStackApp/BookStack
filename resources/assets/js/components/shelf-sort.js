
class ShelfSort {

    constructor(elem) {
        this.elem = elem;
        this.sortGroup = this.initSortable();
        this.input = document.getElementById('books-input');
    }

    initSortable() {
        const sortable = require('jquery-sortable');
        const placeHolderContent = this.getPlaceholderHTML();

        return $('.scroll-box').sortable({
            group: 'shelf-books',
            exclude: '.instruction,.scroll-box-placeholder',
            containerSelector: 'div.scroll-box',
            itemSelector: '.scroll-box-item',
            placeholder: placeHolderContent,
            onDrop: this.onDrop.bind(this)
        });
    }

    onDrop($item, container, _super) {
        const data = this.sortGroup.sortable('serialize').get();
        this.input.value = data[0].map(item => item.id).join(',');
        _super($item, container);
    }

    getPlaceholderHTML() {
        const placeHolder = document.querySelector('.scroll-box-placeholder');
        placeHolder.style.display = 'block';
        const placeHolderContent = placeHolder.outerHTML;
        placeHolder.style.display = 'none';
        return placeHolderContent;
    }


}

module.exports = ShelfSort;