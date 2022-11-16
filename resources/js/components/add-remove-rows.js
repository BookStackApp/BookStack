import {onChildEvent} from "../services/dom";
import {uniqueId} from "../services/util";
import {Component} from "./component";

/**
 * AddRemoveRows
 * Allows easy row add/remove controls onto a table.
 * Needs a model row to use when adding a new row.
 */
export class AddRemoveRows extends Component {
    setup() {
        this.modelRow = this.$refs.model;
        this.addButton = this.$refs.add;
        this.removeSelector = this.$opts.removeSelector;
        this.rowSelector = this.$opts.rowSelector;
        this.setupListeners();
    }

    setupListeners() {
        this.addButton.addEventListener('click', this.add.bind(this));

        onChildEvent(this.$el, this.removeSelector, 'click', (e) => {
            const row = e.target.closest(this.rowSelector);
            row.remove();
        });
    }

    // For external use
    add() {
        const clone = this.modelRow.cloneNode(true);
        clone.classList.remove('hidden');
        this.setClonedInputNames(clone);
        this.modelRow.parentNode.insertBefore(clone, this.modelRow);
        window.$components.init(clone);
    }

    /**
     * Update the HTML names of a clone to be unique if required.
     * Names can use placeholder values. For exmaple, a model row
     * may have name="tags[randrowid][name]".
     * These are the available placeholder values:
     * - randrowid - An random string ID, applied the same across the row.
     * @param {HTMLElement} clone
     */
    setClonedInputNames(clone) {
        const rowId = uniqueId();
        const randRowIdElems = clone.querySelectorAll(`[name*="randrowid"]`);
        for (const elem of randRowIdElems) {
            elem.name = elem.name.split('randrowid').join(rowId);
        }
    }
}