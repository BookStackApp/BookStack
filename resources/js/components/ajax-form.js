import {onEnterPress, onSelect} from "../services/dom";

/**
 * Ajax Form
 * Will handle button clicks or input enter press events and submit
 * the data over ajax. Will always expect a partial HTML view to be returned.
 * Fires an 'ajax-form-success' event when submitted successfully.
 * @extends {Component}
 */
class AjaxForm {
    setup() {
        this.container = this.$el;
        this.url = this.$opts.url;
        this.method = this.$opts.method || 'post';
        this.successMessage = this.$opts.successMessage;
        this.submitButtons = this.$manyRefs.submit || [];

        this.setupListeners();
    }

    setupListeners() {
        onEnterPress(this.container, event => {
            this.submit();
            event.preventDefault();
        });

        this.submitButtons.forEach(button => onSelect(button, this.submit.bind(this)));
    }

    async submit() {
        const fd = new FormData();
        const inputs = this.container.querySelectorAll(`[name]`);
        console.log(inputs);
        for (const input of inputs) {
            fd.append(input.getAttribute('name'), input.value);
        }

        this.container.style.opacity = '0.7';
        this.container.style.pointerEvents = 'none';
        try {
            const resp = await window.$http[this.method.toLowerCase()](this.url, fd);
            this.container.innerHTML = resp.data;
            this.$emit('success', {formData: fd});
            if (this.successMessage) {
                window.$events.emit('success', this.successMessage);
            }
        } catch (err) {
            this.container.innerHTML = err.data;
        }

        window.components.init(this.container);
        this.container.style.opacity = null;
        this.container.style.pointerEvents = null;
    }

}

export default AjaxForm;