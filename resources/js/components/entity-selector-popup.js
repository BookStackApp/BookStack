/**
 * Entity Selector Popup
 * @extends {Component}
 */
class EntitySelectorPopup {

    setup() {
        this.elem = this.$el;
        this.selectButton = this.$refs.select;

        window.EntitySelectorPopup = this;
        this.selectorEl = this.$refs.selector;

        this.callback = null;
        this.selection = null;

        this.selectButton.addEventListener('click', this.onSelectButtonClick.bind(this));
        window.$events.listen('entity-select-change', this.onSelectionChange.bind(this));
        window.$events.listen('entity-select-confirm', this.handleConfirmedSelection.bind(this));
    }

    show(callback) {
        this.callback = callback;
        this.elem.components.popup.show();
        this.getSelector().focusSearch();
    }

    hide() {
        this.elem.components.popup.hide();
    }

    getSelector() {
        return this.selectorEl.components['entity-selector'];
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

export default EntitySelectorPopup;