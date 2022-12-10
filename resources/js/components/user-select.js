import {onChildEvent} from "../services/dom";
import {Component} from "./component";

export class UserSelect extends Component {

    setup() {
        this.container = this.$el;
        this.input = this.$refs.input;
        this.userInfoContainer = this.$refs.userInfo;

        this.initialValue = this.input.value;
        this.initialContent = this.userInfoContainer.innerHTML;

        onChildEvent(this.container, 'a.dropdown-search-item', 'click', this.selectUser.bind(this));
    }

    selectUser(event, userEl) {
        event.preventDefault();
        this.input.value = userEl.getAttribute('data-id');
        this.userInfoContainer.innerHTML = userEl.innerHTML;
        this.input.dispatchEvent(new Event('change', {bubbles: true}));
        this.hide();
    }

    reset() {
        this.input.value = this.initialValue;
        this.userInfoContainer.innerHTML = this.initialContent;
        this.input.dispatchEvent(new Event('change', {bubbles: true}));
        this.hide();
    }

    hide() {
        /** @var {Dropdown} **/
        const dropdown = window.$components.firstOnElement(this.container, 'dropdown');
        dropdown.hide();
    }

}