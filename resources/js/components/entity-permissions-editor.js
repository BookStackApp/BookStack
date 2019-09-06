
class EntityPermissionsEditor {

  constructor(elem) {
    this.permissionsTable = elem.querySelector('[permissions-table]');

    // Handle toggle all event
    this.restrictedCheckbox = elem.querySelector('[name=restricted]');
    this.restrictedCheckbox.addEventListener('change', this.updateTableVisibility.bind(this));
  }

  updateTableVisibility() {
    this.permissionsTable.style.display =
      this.restrictedCheckbox.checked
        ? null
        : 'none';
  }
}

export default EntityPermissionsEditor;