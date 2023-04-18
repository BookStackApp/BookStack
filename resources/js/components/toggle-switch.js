import {Component} from './component';

export class ToggleSwitch extends Component {

    setup() {
        this.input = this.$el.querySelector('input[type=hidden]');
        this.checkbox = this.$el.querySelector('input[type=checkbox]');

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
