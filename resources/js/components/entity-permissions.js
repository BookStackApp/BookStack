

class EntityPermissions {

    setup() {
        this.everyoneInheritToggle = this.$refs.everyoneInherit;

        this.setupListeners();
    }

    setupListeners() {
        this.everyoneInheritToggle.addEventListener('change', event => {
            const inherit = event.target.checked;
            const permissions = document.querySelectorAll('input[type="checkbox"][name^="restrictions[0]["]');
            for (const permission of permissions) {
                permission.disabled = inherit;
                permission.checked = false;
            }
        })
    }

}

export default EntityPermissions;