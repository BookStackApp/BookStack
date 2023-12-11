import {Component} from './component';

export class EntitySelectorPopup extends Component {

    setup() {
        this.container = this.$el;
        this.selectButton = this.$refs.select;
        this.selectorEl = this.$refs.selector;

        this.callback = null;
        this.selection = null;

        this.selectButton.addEventListener('click', this.onSelectButtonClick.bind(this));
        window.$events.listen('entity-select-change', this.onSelectionChange.bind(this));
        window.$events.listen('entity-select-confirm', this.handleConfirmedSelection.bind(this));
    }

    show(callback, searchText = '') {
        this.callback = callback;
        this.getPopup().show();

        if (searchText) {
            this.getSelector().searchText(searchText);
        }

        this.getSelector().focusSearch();
    }

    hide() {
        this.getPopup().hide();
    }

    /**
     * @returns {Popup}
     */
    getPopup() {
        return window.$components.firstOnElement(this.container, 'popup');
    }

    /**
     * @returns {EntitySelector}
     */
    getSelector() {
        return window.$components.firstOnElement(this.selectorEl, 'entity-selector');
    }

    onSelectButtonClick() {
        this.handleConfirmedSelection(this.selection);
    }

    onSelectionChange(entity) {
        this.selection = entity;
        if (entity === null) {
            this.selectButton.setAttribute('disabled', 'true');
        } else {
            this.selectButton.removeAttribute('disabled');
        }
    }

    handleConfirmedSelection(entity) {
        this.hide();
        this.getSelector().reset();
        if (this.callback && entity) this.callback(entity);
    }

}
