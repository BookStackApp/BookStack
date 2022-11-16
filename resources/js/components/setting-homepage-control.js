import {Component} from "./component";

export class SettingHomepageControl extends Component {

    setup() {
        this.typeControl = this.$refs.typeControl;
        this.pagePickerContainer = this.$refs.pagePickerContainer;

        this.typeControl.addEventListener('change', this.controlPagePickerVisibility.bind(this));
        this.controlPagePickerVisibility();
    }

    controlPagePickerVisibility() {
        const showPagePicker = this.typeControl.value === 'page';
        this.pagePickerContainer.style.display = (showPagePicker ? 'block' : 'none');
    }
}