import {Component} from './component';

export class NewUserPassword extends Component {

    setup() {
        this.container = this.$el;
        this.inputContainer = this.$refs.inputContainer;
        this.inviteOption = this.container.querySelector('input[name=send_invite]');

        if (this.inviteOption) {
            this.inviteOption.addEventListener('change', this.inviteOptionChange.bind(this));
            this.inviteOptionChange();
        }
    }

    inviteOptionChange() {
        const inviting = (this.inviteOption.value === 'true');
        const passwordBoxes = this.container.querySelectorAll('input[type=password]');
        for (const input of passwordBoxes) {
            input.disabled = inviting;
        }

        this.inputContainer.style.display = inviting ? 'none' : 'block';
    }

}
