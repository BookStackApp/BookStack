import {onEnterPress, onSelect} from '../services/dom';
import {Component} from './component';

/**
 * Ajax Form
 * Will handle button clicks or input enter press events and submit
 * the data over ajax. Will always expect a partial HTML view to be returned.
 * Fires an 'ajax-form-success' event when submitted successfully.
 *
 * Will handle a real form if that's what the component is added to
 * otherwise will act as a fake form element.
 */
export class AjaxForm extends Component {

    setup() {
        this.container = this.$el;
        this.responseContainer = this.container;
        this.url = this.$opts.url;
        this.method = this.$opts.method || 'post';
        this.successMessage = this.$opts.successMessage;
        this.submitButtons = this.$manyRefs.submit || [];

        if (this.$opts.responseContainer) {
            this.responseContainer = this.container.closest(this.$opts.responseContainer);
        }

        this.setupListeners();
    }

    setupListeners() {
        if (this.container.tagName === 'FORM') {
            this.container.addEventListener('submit', this.submitRealForm.bind(this));
            return;
        }

        onEnterPress(this.container, event => {
            this.submitFakeForm();
            event.preventDefault();
        });

        this.submitButtons.forEach(button => onSelect(button, this.submitFakeForm.bind(this)));
    }

    submitFakeForm() {
        const fd = new FormData();
        const inputs = this.container.querySelectorAll('[name]');
        for (const input of inputs) {
            fd.append(input.getAttribute('name'), input.value);
        }
        this.submit(fd);
    }

    submitRealForm(event) {
        event.preventDefault();
        const fd = new FormData(this.container);
        this.submit(fd);
    }

    async submit(formData) {
        this.responseContainer.style.opacity = '0.7';
        this.responseContainer.style.pointerEvents = 'none';

        try {
            const resp = await window.$http[this.method.toLowerCase()](this.url, formData);
            this.$emit('success', {formData});
            this.responseContainer.innerHTML = resp.data;
            if (this.successMessage) {
                window.$events.emit('success', this.successMessage);
            }
        } catch (err) {
            this.responseContainer.innerHTML = err.data;
        }

        window.$components.init(this.responseContainer);
        this.responseContainer.style.opacity = null;
        this.responseContainer.style.pointerEvents = null;
    }

}
