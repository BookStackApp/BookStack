
class EntitySelectorPopup {

    constructor(elem) {
        this.elem = elem;
        window.EntitySelectorPopup = this;

        this.callback = null;
        this.selection = null;

        this.selectButton = elem.querySelector('.entity-link-selector-confirm');
        this.selectButton.addEventListener('click', this.onSelectButtonClick.bind(this));

        window.$events.listen('entity-select-change', this.onSelectionChange.bind(this));
        window.$events.listen('entity-select-confirm', this.onSelectionConfirm.bind(this));
    }

    show(callback) {
        this.callback = callback;
        this.elem.components.overlay.show();
    }

    hide() {
        this.elem.components.overlay.hide();
    }

    onSelectButtonClick() {
        this.hide();
        if (this.selection !== null && this.callback) this.callback(this.selection);
    }

    onSelectionConfirm(entity) {
        this.hide();
        if (this.callback && entity) this.callback(entity);
    }

    onSelectionChange(entity) {
        this.selection = entity;
        if (entity === null) {
            this.selectButton.setAttribute('disabled', 'true');
        } else {
            this.selectButton.removeAttribute('disabled');
        }
    }
}

module.exports = EntitySelectorPopup;