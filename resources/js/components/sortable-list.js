import Sortable from "sortablejs";

/**
 * SortableList
 * @extends {Component}
 */
class SortableList {
    setup() {
        this.container = this.$el;
        this.handleSelector = this.$opts.handleSelector;

        const sortable = new Sortable(this.container, {
            handle: this.handleSelector,
            animation: 150,
            onSort: () => {
                this.$emit('sort', {ids: sortable.toArray()});
            }
        });
    }
}

export default SortableList;