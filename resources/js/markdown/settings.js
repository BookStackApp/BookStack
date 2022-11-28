import {kebabToCamel} from "../services/text";


export class Settings {

    constructor(initialSettings) {
        this.settingMap = {};
        this.changeListeners = {};
        this.merge(initialSettings);
    }

    set(key, value) {
        key = this.normaliseKey(key);
        this.settingMap[key] = value;
        for (const listener of (this.changeListeners[key] || [])) {
            listener(value);
        }
    }

    get(key) {
        return this.settingMap[this.normaliseKey(key)] || null;
    }

    merge(settings) {
        for (const [key, value] of Object.entries(settings)) {
            this.set(key, value);
        }
    }

    onChange(key, callback) {
        key = this.normaliseKey(key);
        const listeners = this.changeListeners[this.normaliseKey(key)] || [];
        listeners.push(callback);
        this.changeListeners[this.normaliseKey(key)] = listeners;
    }

    normaliseKey(key) {
        return kebabToCamel(key.replace('md-', ''));
    }
}