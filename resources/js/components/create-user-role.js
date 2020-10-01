class CreateUserRole {

    constructor(elem) {
        const toggle = elem.querySelector('input[name=create_user_role]')

        if (toggle) {
            toggle.addEventListener('change', () => {
                const checked = toggle.value === 'true'
                document.getElementById('create-user-role-input').style.display = checked
                    ? 'block'
                    : 'none'

                if (checked) {
                    const nameTextField = document.getElementById('name')
                    if (nameTextField) {
                        elem.querySelector('[name=user_role_name]').value = nameTextField.value
                    }
                }
            })
        }
    }
}

export default CreateUserRole