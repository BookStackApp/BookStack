type ChangeListener = (value: boolean|number) => void;

export class Settings {
    protected changeListeners: Record<string, ChangeListener[]> = {};

    protected settingMap: Record<string, boolean|number> = {
        scrollSync: true,
        showPreview: true,
        editorWidth: 50,
        plainEditor: false,
    };

    constructor(settingInputs: HTMLInputElement[]) {
        this.loadFromLocalStorage();
        this.applyToInputs(settingInputs);
        this.listenToInputChanges(settingInputs);
    }

    protected applyToInputs(inputs: HTMLInputElement[]): void {
        for (const input of inputs) {
            const name = input.getAttribute('name')?.replace('md-', '');
            if (name && name in this.settingMap) {
                const value = this.settingMap[name];
                if (typeof value === 'boolean') {
                    input.checked = value;
                } else {
                    input.value = value.toString();
                }
            }
        }
    }

    protected listenToInputChanges(inputs: HTMLInputElement[]): void {
        for (const input of inputs) {
            input.addEventListener('change', () => {
                const name = input.getAttribute('name')?.replace('md-', '');
                if (name && name in this.settingMap) {
                    let value = (input.type === 'checkbox') ? input.checked : Number(input.value);
                    this.set(name, value);
                }
            });
        }
    }

    protected loadFromLocalStorage(): void {
        const lsValString = window.localStorage.getItem('md-editor-settings');
        if (!lsValString) {
            return;
        }

        try {
            const lsVals = JSON.parse(lsValString);
            for (const [key, value] of Object.entries(lsVals)) {
                if (value !== null && value !== undefined && key in this.settingMap) {
                    this.settingMap[key] = value as boolean|number;
                }
            }
        } catch (error) {
            console.warn('Failed to parse settings from localStorage:', error);
        }
    }

    public set(key: string, value: boolean|number): void {
        this.settingMap[key] = value;
        window.localStorage.setItem('md-editor-settings', JSON.stringify(this.settingMap));

        const listeners = this.changeListeners[key] || [];
        for (const listener of listeners) {
            listener(value);
        }
    }

    public get(key: string): number|boolean|null {
        return this.settingMap[key] ?? null;
    }

    public onChange(key: string, callback: ChangeListener): void {
        const listeners = this.changeListeners[key] || [];
        listeners.push(callback);
        this.changeListeners[key] = listeners;
    }
}