
class ToggleSwitch {

    constructor(elem) {
        this.elem = elem;
        this.input = elem.querySelector('input[type=hidden]');
        this.checkbox = elem.querySelector('input[type=checkbox]');

        this.checkbox.addEventListener('change', this.stateChange.bind(this));
    }

    stateChange() {
        this.input.value = (this.checkbox.checked ? 'true' : 'false');

        // Dispatch change event from hidden input so they can be listened to
        // like a normal checkbox.
        const changeEvent = new Event('change');
        this.input.dispatchEvent(changeEvent);
    }

}

export default ToggleSwitch;