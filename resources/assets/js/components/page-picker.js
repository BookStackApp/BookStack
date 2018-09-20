
class PagePicker {

    constructor(elem) {
        this.elem = elem;
        this.input = elem.querySelector('input');
        this.resetButton = elem.querySelector('[page-picker-reset]');
        this.selectButton = elem.querySelector('[page-picker-select]');
        this.display = elem.querySelector('[page-picker-display]');
        this.defaultDisplay = elem.querySelector('[page-picker-default]');
        this.buttonSep = elem.querySelector('span.sep');

        this.value = this.input.value;
        this.setupListeners();
    }

    setupListeners() {
        this.selectButton.addEventListener('click', this.showPopup.bind(this));
        this.display.parentElement.addEventListener('click', this.showPopup.bind(this));

        this.resetButton.addEventListener('click', event => {
            this.setValue('', '');
        });
    }

    showPopup() {
        window.EntitySelectorPopup.show(entity => {
            this.setValue(entity.id, entity.name);
        });
    }

    setValue(value, name) {
        this.value = value;
        this.input.value = value;
        this.controlView(name);
    }

    controlView(name) {
        let hasValue = this.value && this.value !== 0;
        toggleElem(this.resetButton, hasValue);
        toggleElem(this.buttonSep, hasValue);
        toggleElem(this.defaultDisplay, !hasValue);
        toggleElem(this.display, hasValue);
        if (hasValue) {
            let id = this.getAssetIdFromVal();
            this.display.textContent = `#${id}, ${name}`;
            this.display.href = window.baseUrl(`/link/${id}`);
        }
    }

    getAssetIdFromVal() {
        return Number(this.value);
    }

}

function toggleElem(elem, show) {
    let display = (elem.tagName === 'BUTTON' || elem.tagName === 'SPAN') ? 'inline-block' : 'block';
    elem.style.display = show ? display : 'none';
}

module.exports = PagePicker;