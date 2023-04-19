import {Component} from './component';

/**
 * Submit on change
 * Simply submits a parent form when this input is changed.
 */
export class SubmitOnChange extends Component {

    setup() {
        this.filter = this.$opts.filter;

        this.$el.addEventListener('change', event => {
            if (this.filter && !event.target.matches(this.filter)) {
                return;
            }

            const form = this.$el.closest('form');
            if (form) {
                form.submit();
            }
        });
    }

}
