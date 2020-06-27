import {onChildEvent} from "../services/dom";

/**
 * AddRemoveRows
 * Allows easy row add/remove controls onto a table.
 * Needs a model row to use when adding a new row.
 * @extends {Component}
 */
class AddRemoveRows {
    setup() {
        this.modelRow = this.$refs.model;
        this.addButton = this.$refs.add;
        this.removeSelector = this.$opts.removeSelector;
        this.setupListeners();
    }

    setupListeners() {
        this.addButton.addEventListener('click', e => {
            const clone = this.modelRow.cloneNode(true);
            clone.classList.remove('hidden');
            this.modelRow.parentNode.insertBefore(clone, this.modelRow);
        });

        onChildEvent(this.$el, this.removeSelector, 'click', (e) => {
            const row = e.target.closest('tr');
            row.remove();
        });
    }
}

export default AddRemoveRows;