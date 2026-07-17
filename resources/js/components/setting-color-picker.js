import {Component} from './component';

export class SettingColorPicker extends Component {

    setup() {
        this.colorInput = this.$refs.input;
        this.resetButton = this.$refs.resetButton;
        this.defaultButton = this.$refs.defaultButton;
        this.currentColor = this.$opts.current;
        this.defaultColor = this.$opts.default;

        this.resetButton.addEventListener('click', () => this.setValue(this.currentColor));
        this.defaultButton.addEventListener('click', () => this.setValue(this.defaultColor));
    }

    setValue(value) {
        this.colorInput.value = value;
        this.colorInput.dispatchEvent(new Event('change', {bubbles: true}));
    }

}
