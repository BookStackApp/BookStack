
class PermissionsTable {

    constructor(elem) {
        this.container = elem;

        // Handle toggle all event
        const toggleAll = elem.querySelector('[permissions-table-toggle-all]');
        toggleAll.addEventListener('click', this.toggleAllClick.bind(this));

        // Handle toggle row event
        const toggleRowElems = elem.querySelectorAll('[permissions-table-toggle-all-in-row]');
        for (let toggleRowElem of toggleRowElems) {
            toggleRowElem.addEventListener('click', this.toggleRowClick.bind(this));
        }
    }

    toggleAllClick(event) {
        event.preventDefault();
        this.toggleAllInElement(this.container);
    }

    toggleRowClick(event) {
        event.preventDefault();
        this.toggleAllInElement(event.target.closest('tr'));
    }

    toggleAllInElement(domElem) {
        const inputsToSelect = domElem.querySelectorAll('input[type=checkbox]');
        const currentState = inputsToSelect.length > 0 ? inputsToSelect[0].checked : false;
        for (let checkbox of inputsToSelect) {
            checkbox.checked = !currentState;
            checkbox.dispatchEvent(new Event('change'));
        }
    }

}

export default PermissionsTable;