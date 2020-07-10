import Code from "../services/code";
import {onChildEvent, onEnterPress, onSelect} from "../services/dom";

/**
 * Code Editor
 * @extends {Component}
 */
class CodeEditor {

    setup() {
        this.container = this.$refs.container;
        this.popup = this.$el;
        this.editorInput = this.$refs.editor;
        this.saveButton = this.$refs.saveButton;
        this.languageSelect = this.$refs.languageSelect;
        this.historyDropDown = this.$refs.historyDropDown;
        this.historyList = this.$refs.historyList;

        this.callback = null;
        this.editor = null;
        this.history = {};
        this.historyKey = 'code_history';
        this.setupListeners();
    }

    setupListeners() {
        this.container.addEventListener('keydown', event => {
            if (event.ctrlKey && event.key === 'Enter') {
                this.save();
            }
        });

        onSelect(this.languageSelect, event => {
            this.updateEditorMode(this.getLanguageValue());
        });

        onSelect(this.saveButton, e => this.save());

        onChildEvent(this.historyList, 'button', 'click', (event, elem) => {
            event.preventDefault();
            const historyTime = elem.dataset.time;
            if (this.editor) {
                this.editor.setValue(this.history[historyTime]);
            }
        });
    }

    save() {
        if (this.callback) {
            this.callback(this.editor.getValue(), this.getLanguageValue());
        }
        this.hide();
    }

    open(code, language, callback) {
        this.languageSelect.value = language;
        this.callback = callback;

        this.show();
        this.updateEditorMode(language);

        Code.setContent(this.editor, code);
    }

    show() {
        if (!this.editor) {
            this.editor = Code.popupEditor(this.editorInput, this.getLanguageValue());
        }
        this.loadHistory();
        this.popup.components.popup.show(() => {
            Code.updateLayout(this.editor);
            this.editor.focus();
        }, () => {
            this.addHistory()
        });
    }

    hide() {
        this.popup.components.popup.hide();
        this.addHistory();
    }

    updateEditorMode(language) {
        Code.setMode(this.editor, language, this.editor.getValue());
    }

    loadHistory() {
        this.history = JSON.parse(window.sessionStorage.getItem(this.historyKey) || '{}');
        const historyKeys = Object.keys(this.history).reverse();
        this.historyDropDown.classList.toggle('hidden', historyKeys.length === 0);
        this.historyList.innerHTML = historyKeys.map(key => {
             const localTime = (new Date(parseInt(key))).toLocaleTimeString();
             return `<li><button type="button" data-time="${key}">${localTime}</button></li>`;
        }).join('');
    }

    addHistory() {
        if (!this.editor) return;
        const code = this.editor.getValue();
        if (!code) return;

        // Stop if we'd be storing the same as the last item
        const lastHistoryKey = Object.keys(this.history).pop();
        if (this.history[lastHistoryKey] === code) return;

        this.history[String(Date.now())] = code;
        const historyString = JSON.stringify(this.history);
        window.sessionStorage.setItem(this.historyKey, historyString);
    }

    getLanguageValue() {
        const selectedIndex = this.languageSelect.selectedIndex;
        return this.languageSelect.options[selectedIndex].value;
    }

}

export default CodeEditor;