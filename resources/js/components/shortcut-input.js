import {Component} from "./component";

/**
 * Keys to ignore when recording shortcuts.
 * @type {string[]}
 */
const ignoreKeys = ['Control', 'Alt', 'Shift', 'Meta', 'Super', ' ', '+', 'Tab', 'Escape'];

export class ShortcutInput extends Component {

    setup() {
        this.input = this.$el;

        this.setupListeners();
    }

    setupListeners() {
        this.listenerRecordKey = this.listenerRecordKey.bind(this);

        this.input.addEventListener('focus', () => {
             this.startListeningForInput();
        });

        this.input.addEventListener('blur', () => {
            this.stopListeningForInput();
        })
    }

    startListeningForInput() {
        this.input.addEventListener('keydown', this.listenerRecordKey)
    }

    /**
     * @param {KeyboardEvent} event
     */
    listenerRecordKey(event) {
        if (ignoreKeys.includes(event.key)) {
            return;
        }

        const keys = [
            event.ctrlKey ? 'Ctrl' : '',
            event.metaKey ? 'Cmd' : '',
            event.key,
        ];

        this.input.value = keys.filter(s => Boolean(s)).join(' + ');
    }

    stopListeningForInput() {
        this.input.removeEventListener('keydown', this.listenerRecordKey);
    }

}