import {Component} from "./component.js";
import Sortable from "sortablejs";
import {buildListActions, sortActionClickListener} from "../services/dual-lists";


export class SortRuleManager extends Component {

    protected input!: HTMLInputElement;
    protected configuredList!: HTMLElement;
    protected availableList!: HTMLElement;

    setup() {
        this.input = this.$refs.input as HTMLInputElement;
        this.configuredList = this.$refs.configuredOperationsList;
        this.availableList = this.$refs.availableOperationsList;

        this.initSortable();

        const listActions = buildListActions(this.availableList, this.configuredList);
        const sortActionListener = sortActionClickListener(listActions, this.onChange.bind(this));
        this.$el.addEventListener('click', sortActionListener);
    }

    initSortable() {
        const scrollBoxes = [this.configuredList, this.availableList];
        for (const scrollBox of scrollBoxes) {
            new Sortable(scrollBox, {
                group: 'sort-rule-operations',
                ghostClass: 'primary-background-light',
                handle: '.handle',
                animation: 150,
                onSort: this.onChange.bind(this),
            });
        }
    }

    onChange() {
        const configuredOpEls = Array.from(this.configuredList.querySelectorAll('[data-id]'));
        this.input.value = configuredOpEls.map(elem => elem.getAttribute('data-id')).join(',');
    }
}