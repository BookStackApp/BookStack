/**
 * Submit on change
 * Simply submits a parent form when this input is changed.
 * @extends {Component}
 */
class SubmitOnChange {

    setup() {
        this.$el.addEventListener('change', () => {
            const form = this.$el.closest('form');
            if (form) {
                form.submit();
            }
        });
    }

}

export default SubmitOnChange;