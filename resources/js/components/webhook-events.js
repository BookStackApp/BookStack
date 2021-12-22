
/**
 * Webhook Events
 * Manages dynamic selection control in the webhook form interface.
 * @extends {Component}
 */
class WebhookEvents {

    setup() {
        this.checkboxes = this.$el.querySelectorAll('input[type="checkbox"]');
        this.allCheckbox = this.$el.querySelector('input[type="checkbox"][value="all"]');

        this.$el.addEventListener('change', event => {
            if (event.target.checked && event.target === this.allCheckbox) {
                this.deselectIndividualEvents();
            } else if (event.target.checked) {
                this.allCheckbox.checked = false;
            }
        });
    }

    deselectIndividualEvents() {
        for (const checkbox of this.checkboxes) {
            if (checkbox !== this.allCheckbox) {
                checkbox.checked = false;
            }
        }
    }

}

export default WebhookEvents;