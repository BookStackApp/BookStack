import {Component} from "./component";

export class SettingAppColorPicker extends Component {

    setup() {
        this.colorInput = this.$refs.input;
        this.lightColorInput = this.$refs.lightInput;

        this.colorInput.addEventListener('change', this.updateColor.bind(this));
        this.colorInput.addEventListener('input', this.updateColor.bind(this));
    }

    /**
     * Update the app colors as a preview, and create a light version of the color.
     */
    updateColor() {
        const hexVal = this.colorInput.value;
        const rgb = this.hexToRgb(hexVal);
        const rgbLightVal = 'rgba('+ [rgb.r, rgb.g, rgb.b, '0.15'].join(',') +')';

        this.lightColorInput.value = rgbLightVal;

        const customStyles = document.getElementById('custom-styles');
        const oldColor = customStyles.getAttribute('data-color');
        const oldColorLight = customStyles.getAttribute('data-color-light');

        customStyles.innerHTML = customStyles.innerHTML.split(oldColor).join(hexVal);
        customStyles.innerHTML = customStyles.innerHTML.split(oldColorLight).join(rgbLightVal);

        customStyles.setAttribute('data-color', hexVal);
        customStyles.setAttribute('data-color-light', rgbLightVal);
    }

    /**
     * Covert a hex color code to rgb components.
     * @attribution https://stackoverflow.com/a/5624139
     * @param {String} hex
     * @returns {{r: Number, g: Number, b: Number}}
     */
    hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return {
            r: result ? parseInt(result[1], 16) : 0,
            g: result ? parseInt(result[2], 16) : 0,
            b: result ? parseInt(result[3], 16) : 0
        };
    }

}
