const codeLib = require('../code');

const methods = {
    show() {
        if (!this.editor) this.editor = codeLib.popupEditor(this.$refs.editor, this.language);
        this.$refs.overlay.style.display = 'flex';
    },
    hide() {
        this.$refs.overlay.style.display = 'none';
    },
    updateEditorMode(language) {
        codeLib.setMode(this.editor, language);
    },
    updateLanguage(lang) {
        this.language = lang;
        this.updateEditorMode(lang);
    },
    open(code, language, callback) {
        this.show();
        this.updateEditorMode(language);
        this.language = language;
        codeLib.setContent(this.editor, code);
        this.code = code;
        this.callback = callback;
    },
    save() {
        if (!this.callback) return;
        this.callback(this.editor.getValue(), this.language);
        this.hide();
    }
};

const data = {
    editor: null,
    language: '',
    code: '',
    callback: null
};

module.exports = {
    methods,
    data
};