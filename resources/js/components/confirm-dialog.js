import {onSelect} from "../services/dom";

/**
 * Custom equivalent of window.confirm() using our popup component.
 * Is promise based so can be used like so:
 * `const result = await dialog.show()`
 * @extends {Component}
 */
class ConfirmDialog {

    setup() {
        this.container = this.$el;
        this.confirmButton = this.$refs.confirm;

        this.res = null;

        onSelect(this.confirmButton, () => {
            this.sendResult(true);
            this.getPopup().hide();
        });
    }

    show() {
        this.getPopup().show(null, () => {
            this.sendResult(false);
        });

        return new Promise((res, rej) => {
           this.res = res;
        });
    }

    /**
     * @returns {Popup}
     */
    getPopup() {
        return this.container.components.popup;
    }

    /**
     * @param {Boolean} result
     */
    sendResult(result) {
        if (this.res) {
            console.log('sending result', result);
            this.res(result)
            this.res = null;
        }
    }

}

export default ConfirmDialog;