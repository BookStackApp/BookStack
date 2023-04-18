import {Component} from './component';

export class SettingAppColorScheme extends Component {

    setup() {
        this.container = this.$el;
        this.mode = this.$opts.mode;
        this.lightContainer = this.$refs.lightContainer;
        this.darkContainer = this.$refs.darkContainer;

        this.container.addEventListener('tabs-change', event => {
            const panel = event.detail.showing;
            const newMode = (panel === 'color-scheme-panel-light') ? 'light' : 'dark';
            this.handleModeChange(newMode);
        });

        const onInputChange = event => {
            this.updateAppColorsFromInputs();

            if (event.target.name.startsWith('setting-app-color')) {
                this.updateLightForInput(event.target);
            }
        };
        this.container.addEventListener('change', onInputChange);
        this.container.addEventListener('input', onInputChange);
    }

    handleModeChange(newMode) {
        this.mode = newMode;
        const isDark = (newMode === 'dark');

        document.documentElement.classList.toggle('dark-mode', isDark);
        this.updateAppColorsFromInputs();
    }

    updateAppColorsFromInputs() {
        const inputContainer = this.mode === 'dark' ? this.darkContainer : this.lightContainer;
        const inputs = inputContainer.querySelectorAll('input[type="color"]');
        for (const input of inputs) {
            const splitName = input.name.split('-');
            const colorPos = splitName.indexOf('color');
            let cssId = splitName.slice(1, colorPos).join('-');
            if (cssId === 'app') {
                cssId = 'primary';
            }

            const varName = `--color-${cssId}`;
            document.body.style.setProperty(varName, input.value);
        }
    }

    /**
     * Update the 'light' app color variant for the given input.
     * @param {HTMLInputElement} input
     */
    updateLightForInput(input) {
        const lightName = input.name.replace('-color', '-color-light');
        const hexVal = input.value;
        const rgb = this.hexToRgb(hexVal);
        const rgbLightVal = `rgba(${[rgb.r, rgb.g, rgb.b, '0.15'].join(',')})`;

        console.log(input.name, lightName, hexVal, rgbLightVal);
        const lightColorInput = this.container.querySelector(`input[name="${lightName}"][type="hidden"]`);
        lightColorInput.value = rgbLightVal;
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
            b: result ? parseInt(result[3], 16) : 0,
        };
    }

}
