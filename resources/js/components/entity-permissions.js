import {htmlToDom} from "../services/dom";
import {Component} from "./component";

export class EntityPermissions extends Component {

    setup() {
        this.container = this.$el;
        this.entityType = this.$opts.entityType;

        this.everyoneInheritToggle = this.$refs.everyoneInherit;
        this.roleSelect = this.$refs.roleSelect;
        this.roleContainer = this.$refs.roleContainer;

        this.setupListeners();
    }

    setupListeners() {
        // "Everyone Else" inherit toggle
        this.everyoneInheritToggle.addEventListener('change', event => {
            const inherit = event.target.checked;
            const permissions = document.querySelectorAll('input[name^="permissions[0]["]');
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
    }

    async addRoleRow(roleId) {
        this.roleSelect.disabled = true;

        // Remove option from select
        const option = this.roleSelect.querySelector(`option[value="${roleId}"]`);
        if (option) {
            option.remove();
        }

        // Get and insert new row
        const resp = await window.$http.get(`/permissions/form-row/${this.entityType}/${roleId}`);
        const row = htmlToDom(resp.data);
        this.roleContainer.append(row);

        this.roleSelect.disabled = false;
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
        // TODO - User role!
        row.remove();
    }

}