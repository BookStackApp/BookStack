import Sortable from "sortablejs";

/**
 * SortableList
 *
 * Can have data set on the dragged items by setting a 'data-drag-content' attribute.
 * This attribute must contain JSON where the keys are content types and the values are
 * the data to set on the data-transfer.
 *
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
            },
            setData(dataTransferItem, dragEl) {
                const jsonContent = dragEl.getAttribute('data-drag-content');
                if (jsonContent) {
                    const contentByType = JSON.parse(jsonContent);
                    for (const [type, content] of Object.entries(contentByType)) {
                        dataTransferItem.setData(type, content);
                    }
                }
            },
            revertOnSpill: true,
            dropBubble: true,
            dragoverBubble: false,
        });
    }
}

export default SortableList;