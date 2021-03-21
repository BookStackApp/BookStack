import {onChildEvent} from "../services/dom";

class UserSelect {

    setup() {

        this.input = this.$refs.input;
        this.userInfoContainer = this.$refs.userInfo;

        this.hide = this.$el.components.dropdown.hide;

        onChildEvent(this.$el, 'a.dropdown-search-item', 'click', this.selectUser.bind(this));
    }

    selectUser(event, userEl) {
        event.preventDefault();
        const id = userEl.getAttribute('data-id');
        this.input.value = id;
        this.userInfoContainer.innerHTML = userEl.innerHTML;
        this.input.dispatchEvent(new Event('change', {bubbles: true}));
        this.hide();
    }

}

export default UserSelect;