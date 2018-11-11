
class ToggleSwitch {

    constructor(elem) {
        this.elem = elem;
        this.input = elem.querySelector('input');

        this.elem.onclick = this.onClick.bind(this);
    }

    onClick(event) {
        let checked = this.input.value !== 'true';
        this.input.value = checked ? 'true' : 'false';
        checked ? this.elem.classList.add('active') : this.elem.classList.remove('active');
    }

}

export default ToggleSwitch;