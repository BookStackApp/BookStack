
class CustomCheckbox {

    constructor(elem) {
        this.elem = elem;
        this.checkbox = elem.querySelector('input[type=checkbox]');
        this.display = elem.querySelector('[role="checkbox"]');

        this.checkbox.addEventListener('change', this.stateChange.bind(this));
        this.elem.addEventListener('keydown', this.onKeyDown.bind(this));
    }

    onKeyDown(event) {
        const isEnterOrPress = event.keyCode === 32 || event.keyCode === 13;
        if (isEnterOrPress) {
            event.preventDefault();
            this.toggle();
        }
    }

    toggle() {
        this.checkbox.checked = !this.checkbox.checked;
        this.checkbox.dispatchEvent(new Event('change'));
        this.stateChange();
    }

    stateChange() {
        const checked = this.checkbox.checked ? 'true' : 'false';
        this.display.setAttribute('aria-checked', checked);
    }

}

export default CustomCheckbox;