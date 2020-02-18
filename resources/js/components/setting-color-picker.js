
class SettingColorPicker {

    constructor(elem) {
        this.elem = elem;
        this.colorInput = elem.querySelector('input[type=color]');
        this.resetButton = elem.querySelector('[setting-color-picker-reset]');
        this.defaultButton = elem.querySelector('[setting-color-picker-default]');
        this.resetButton.addEventListener('click', event => {
            this.colorInput.value = this.colorInput.dataset.current;
        });
        this.defaultButton.addEventListener('click', event => {
          this.colorInput.value = this.colorInput.dataset.default;
        });
    }
}

export default SettingColorPicker;
