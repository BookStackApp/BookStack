
class ToggleSwitch {

    constructor(elem) {
        this.elem = elem;
        this.input = elem.querySelector('input[type=hidden]');
        this.checkbox = elem.querySelector('input[type=checkbox]');

        this.checkbox.addEventListener('change', this.onClick.bind(this));
    }

    onClick(event) {
        let checked = this.checkbox.checked;
        this.input.value = checked ? 'true' : 'false';
    }

}

export default ToggleSwitch;