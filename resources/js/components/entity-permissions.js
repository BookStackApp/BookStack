import {htmlToDom} from "../services/dom";
import {Component} from "./component";

export class EntityPermissions extends Component {

    setup() {
        this.container = this.$el;
        this.entityType = this.$opts.entityType;

        this.everyoneInheritToggle = this.$refs.everyoneInherit;
        this.roleSelect = this.$refs.roleSelect;
        this.roleContainer = this.$refs.roleContainer;
        this.userContainer = this.$refs.userContainer;
        this.userSelectContainer = this.$refs.userSelectContainer;

        this.setupListeners();
    }

    setupListeners() {
        // "Everyone Else" inherit toggle
        this.everyoneInheritToggle.addEventListener('change', event => {
            const inherit = event.target.checked;
            const permissions = document.querySelectorAll('input[name^="permissions[fallback]"]');
            for (const permission of permissions) {
                permission.disabled = inherit;
                permission.checked = false;
            }
        });

        // Remove role row button click
        this.container.addEventListener('click', event => {
            const button = event.target.closest('button');
            if (button && button.dataset.modelType) {
                this.removeRowOnButtonClick(button)
            }
        });

        // Role select change
        this.roleSelect.addEventListener('change', event => {
            const roleId = this.roleSelect.value;
            if (roleId) {
                this.addRoleRow(roleId);
            }
        });

        // User select change
        this.userSelectContainer.querySelector('input[name="user_select"]').addEventListener('change', event => {
            const userId = event.target.value;
            if (userId) {
                this.addUserRow(userId);
            }
        });
    }

    async addRoleRow(roleId) {
        this.roleSelect.disabled = true;

        // Remove option from select
        const option = this.roleSelect.querySelector(`option[value="${roleId}"]`);
        if (option) {
            option.remove();
        }

        // Get and insert new row
        const resp = await window.$http.get(`/permissions/role-form-row/${this.entityType}/${roleId}`);
        const row = htmlToDom(resp.data);
        this.roleContainer.append(row);

        this.roleSelect.disabled = false;
    }

    async addUserRow(userId) {
        const exists = this.userContainer.querySelector(`[name^="permissions[user][${userId}]"]`) !== null;
        if (exists) {
            return;
        }

        const toggle = this.userSelectContainer.querySelector('.dropdown-search-toggle-select');
        toggle.classList.add('disabled');
        this.userContainer.style.pointerEvents = 'none';

        // Get and insert new row
        const resp = await window.$http.get(`/permissions/user-form-row/${this.entityType}/${userId}`);
        const row = htmlToDom(resp.data);
        this.userContainer.append(row);

        toggle.classList.remove('disabled');
        this.userContainer.style.pointerEvents = null;

        /** @var {UserSelect} **/
        const userSelect = window.$components.firstOnElement(this.userSelectContainer.querySelector('.dropdown-search'), 'user-select');
        userSelect.reset();
    }

    removeRowOnButtonClick(button) {
        const row = button.closest('.item-list-row');
        const modelId = button.dataset.modelId;
        const modelName = button.dataset.modelName;
        const modelType = button.dataset.modelType;

        const option = document.createElement('option');
        option.value = modelId;
        option.textContent = modelName;

        if (modelType === 'role') {
            this.roleSelect.append(option);
        }

        row.remove();
    }

}