import {Component} from './component';

export class CustomCheckbox extends Component {

    setup() {
        this.container = this.$el;
        this.checkbox = this.container.querySelector('input[type=checkbox]');
        this.display = this.container.querySelector('[role="checkbox"]');

        this.checkbox.addEventListener('change', this.stateChange.bind(this));
        this.container.addEventListener('keydown', this.onKeyDown.bind(this));
    }

    onKeyDown(event) {
        const isEnterOrSpace = event.key === ' ' || event.key === 'Enter';
        if (isEnterOrSpace) {
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
