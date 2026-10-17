import {Component} from './component';

/**
 * A toggle switch style input which specifically also has true/false submittable states.
 * This is done via a hidden input for the actual value, with a checkbox input for control/UI.
 */
export class ToggleSwitch extends Component {

    protected input!: HTMLInputElement;
    protected checkbox!: HTMLInputElement;

    setup() {
        this.input = this.$el.querySelector('input[type=hidden]') as HTMLInputElement;
        this.checkbox = this.$el.querySelector('input[type=checkbox]') as HTMLInputElement;

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
