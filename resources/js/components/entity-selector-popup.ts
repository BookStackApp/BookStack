import {Component} from './component';
import {EntitySelector, EntitySelectorEntity, EntitySelectorSearchOptions} from "./entity-selector";
import {Popup} from "./popup";

export type EntitySelectorPopupCallback = (entity: EntitySelectorEntity) => void;

export class EntitySelectorPopup extends Component {

    protected container!: HTMLElement;
    protected selectButton!: HTMLElement;
    protected selectorEl!: HTMLElement;

    protected callback: EntitySelectorPopupCallback|null = null;
    protected selection: EntitySelectorEntity|null = null;

    setup() {
        this.container = this.$el;
        this.selectButton = this.$refs.select;
        this.selectorEl = this.$refs.selector;

        this.selectButton.addEventListener('click', this.onSelectButtonClick.bind(this));
        window.$events.listen('entity-select-change', this.onSelectionChange.bind(this));
        window.$events.listen('entity-select-confirm', this.handleConfirmedSelection.bind(this));
    }

    /**
     * Show the selector popup.
     */
    show(callback: EntitySelectorPopupCallback, searchOptions: Partial<EntitySelectorSearchOptions> = {}) {
        this.callback = callback;
        this.getSelector().configureSearchOptions(searchOptions);
        this.getPopup().show();

        this.getSelector().focusSearch();
    }

    hide() {
        this.getPopup().hide();
    }

    getPopup(): Popup {
        return window.$components.firstOnElement(this.container, 'popup') as Popup;
    }

    getSelector(): EntitySelector {
        return window.$components.firstOnElement(this.selectorEl, 'entity-selector') as EntitySelector;
    }

    onSelectButtonClick() {
        this.handleConfirmedSelection(this.selection);
    }

    onSelectionChange(entity: EntitySelectorEntity|{}) {
        this.selection = (entity.hasOwnProperty('id') ? entity : null) as EntitySelectorEntity|null;
        if (!this.selection) {
            this.selectButton.setAttribute('disabled', 'true');
        } else {
            this.selectButton.removeAttribute('disabled');
        }
    }

    handleConfirmedSelection(entity: EntitySelectorEntity|null): void {
        this.hide();
        this.getSelector().reset();
        if (this.callback && entity) this.callback(entity);
    }

}
