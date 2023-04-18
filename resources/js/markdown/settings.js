export class Settings {

    constructor(settingInputs) {
        this.settingMap = {
            scrollSync: true,
            showPreview: true,
            editorWidth: 50,
        };
        this.changeListeners = {};
        this.loadFromLocalStorage();
        this.applyToInputs(settingInputs);
        this.listenToInputChanges(settingInputs);
    }

    applyToInputs(inputs) {
        for (const input of inputs) {
            const name = input.getAttribute('name').replace('md-', '');
            input.checked = this.settingMap[name];
        }
    }

    listenToInputChanges(inputs) {
        for (const input of inputs) {
            input.addEventListener('change', event => {
                const name = input.getAttribute('name').replace('md-', '');
                this.set(name, input.checked);
            });
        }
    }

    loadFromLocalStorage() {
        const lsValString = window.localStorage.getItem('md-editor-settings');
        if (!lsValString) {
            return;
        }

        const lsVals = JSON.parse(lsValString);
        for (const [key, value] of Object.entries(lsVals)) {
            if (value !== null && this.settingMap[key] !== undefined) {
                this.settingMap[key] = value;
            }
        }
    }

    set(key, value) {
        this.settingMap[key] = value;
        window.localStorage.setItem('md-editor-settings', JSON.stringify(this.settingMap));
        for (const listener of (this.changeListeners[key] || [])) {
            listener(value);
        }
    }

    get(key) {
        return this.settingMap[key] || null;
    }

    onChange(key, callback) {
        const listeners = this.changeListeners[key] || [];
        listeners.push(callback);
        this.changeListeners[key] = listeners;
    }

}
