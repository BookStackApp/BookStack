/**
 * ListSortControl
 * Manages the logic for the control which provides list sorting options.
 */
class ListSortControl {

    constructor(elem) {
        this.elem = elem;
        this.menu = elem.querySelector('ul');

        this.sortInput = elem.querySelector('[name="sort"]');
        this.orderInput = elem.querySelector('[name="order"]');
        this.form = elem.querySelector('form');

        this.menu.addEventListener('click', event => {
            if (event.target.closest('[data-sort-value]') !== null) {
                this.sortOptionClick(event);
            }
        });

        this.elem.addEventListener('click', event => {
            if (event.target.closest('[data-sort-dir]') !== null) {
                this.sortDirectionClick(event);
            }
        });
    }

    sortOptionClick(event) {
        const sortOption = event.target.closest('[data-sort-value]');
        this.sortInput.value = sortOption.getAttribute('data-sort-value');
        event.preventDefault();
        this.form.submit();
    }

    sortDirectionClick(event) {
        const currentDir = this.orderInput.value;
        const newDir = (currentDir === 'asc') ? 'desc' : 'asc';
        this.orderInput.value = newDir;
        event.preventDefault();
        this.form.submit();
    }

}

export default ListSortControl;