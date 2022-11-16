import {onSelect} from "../services/dom";
import {Component} from "./component";

/**
 * Custom equivalent of window.confirm() using our popup component.
 * Is promise based so can be used like so:
 * `const result = await dialog.show()`
 */
export class ConfirmDialog extends Component {

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
        return window.$components.firstOnElement(this.container, 'popup');
    }

    /**
     * @param {Boolean} result
     */
    sendResult(result) {
        if (this.res) {
            this.res(result)
            this.res = null;
        }
    }

}