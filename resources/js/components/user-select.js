import {onChildEvent} from "../services/dom";

class UserSelect {

    setup() {

        this.input = this.$refs.input;
        this.userInfoContainer = this.$refs.userInfo;

        this.hide = this.$el.components.dropdown.hide;

        onChildEvent(this.$el, 'a.dropdown-search-item', 'click', this.selectUser.bind(this));
    }

    selectUser(event, userEl) {
        const id = userEl.getAttribute('data-id');
        this.input.value = id;
        this.userInfoContainer.innerHTML = userEl.innerHTML;
        this.hide();
    }

}

export default UserSelect;