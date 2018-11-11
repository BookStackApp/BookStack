
class HomepageControl {

    constructor(elem) {
        this.elem = elem;
        this.typeControl = elem.querySelector('[name="setting-app-homepage-type"]');
        this.pagePickerContainer = elem.querySelector('[page-picker-container]');

        this.typeControl.addEventListener('change', this.controlPagePickerVisibility.bind(this));
        this.controlPagePickerVisibility();
    }

    controlPagePickerVisibility() {
        const showPagePicker = this.typeControl.value === 'page';
        this.pagePickerContainer.style.display = (showPagePicker ? 'block' : 'none');
    }



}

export default HomepageControl;