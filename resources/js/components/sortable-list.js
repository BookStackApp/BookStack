import Sortable from "sortablejs";

/**
 * SortableList
 * @extends {Component}
 */
class SortableList {
    setup() {
        this.container = this.$el;
        this.handleSelector = this.$opts.handleSelector;

        new Sortable(this.container, {
            handle: this.handleSelector,
            animation: 150,
        });
    }
}

export default SortableList;