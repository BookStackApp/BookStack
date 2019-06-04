
class ToggleSwitch {

    constructor(elem) {
        this.elem = elem;
        this.input = elem.querySelector('input[type=hidden]');
        this.checkbox = elem.querySelector('input[type=checkbox]');

        this.checkbox.addEventListener('change', this.stateChange.bind(this));
    }

    stateChange() {
        this.input.value = (this.checkbox.checked ? 'true' : 'false');
    }

}

export default ToggleSwitch;