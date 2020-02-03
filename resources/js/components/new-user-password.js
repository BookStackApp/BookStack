
class NewUserPassword {

    constructor(elem) {
        this.elem = elem;
        this.inviteOption = elem.querySelector('input[name=send_invite]');

        if (this.inviteOption) {
            this.inviteOption.addEventListener('change', this.inviteOptionChange.bind(this));
            this.inviteOptionChange();
        }
    }

    inviteOptionChange() {
        const inviting = (this.inviteOption.value === 'true');
        const passwordBoxes = this.elem.querySelectorAll('input[type=password]');
        for (const input of passwordBoxes) {
            input.disabled = inviting;
        }
        const container = this.elem.querySelector('#password-input-container');
        if (container) {
            container.style.display = inviting ? 'none' : 'block';
        }
    }

}

export default NewUserPassword;