import {Component} from "./component";

export class TagManager extends Component {
    setup() {
        this.addRemoveComponentEl = this.$refs.addRemove;
        this.container = this.$el;
        this.rowSelector = this.$opts.rowSelector;

        this.setupListeners();
    }

    setupListeners() {
        this.container.addEventListener('change', event => {
            const addRemoveComponent = this.addRemoveComponentEl.components['add-remove-rows'];
            if (!this.hasEmptyRows()) {
                addRemoveComponent.add();
            }
        });
    }

    hasEmptyRows() {
        const rows = this.container.querySelectorAll(this.rowSelector);
        const firstEmpty = [...rows].find(row => {
            return [...row.querySelectorAll('input')].filter(input => input.value).length === 0;
        });
        return firstEmpty !== undefined;
    }
}