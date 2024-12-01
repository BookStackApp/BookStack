import {Component} from "./component.js";
import {showLoading} from "../services/dom";
import {el} from "../wysiwyg/utils/dom";

/**
 * Loading button.
 * Shows a loading indicator and disables the button when the button is clicked,
 * or when the form attached to the button is submitted.
 */
export class LoadingButton extends Component {

    protected button!: HTMLButtonElement;
    protected loadingEl: HTMLDivElement|null = null;

    setup() {
        this.button = this.$el as HTMLButtonElement;
        const form = this.button.form;

        const action = () => {
            setTimeout(() => this.showLoadingState(), 10)
        };

        this.button.addEventListener('click', action);
        if (form) {
            form.addEventListener('submit', action);
        }
    }

    showLoadingState() {
        this.button.disabled = true;

        if (!this.loadingEl) {
            this.loadingEl = el('div', {class: 'inline block'}) as HTMLDivElement;
            showLoading(this.loadingEl);
            this.button.after(this.loadingEl);
        }
    }
}