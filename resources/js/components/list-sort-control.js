/**
 * ListSortControl
 * Manages the logic for the control which provides list sorting options.
 * @extends {Component}
 */
class ListSortControl {

    setup() {
        this.elem = this.$el;
        this.menu = this.$refs.menu;

        this.sortInput = this.$refs.sort;
        this.orderInput = this.$refs.order;
        this.form = this.$refs.form;

        this.setupListeners();
    }

    setupListeners() {
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
        this.orderInput.value = (currentDir === 'asc') ? 'desc' : 'asc';
        event.preventDefault();
        this.form.submit();
    }

}

export default ListSortControl;