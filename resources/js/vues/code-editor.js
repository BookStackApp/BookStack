import codeLib from "../services/code";

const methods = {
    show() {
        if (!this.editor) this.editor = codeLib.popupEditor(this.$refs.editor, this.language);
        this.$refs.overlay.components.overlay.show(() => {
            codeLib.updateLayout(this.editor);
            this.editor.focus();
        });
    },
    hide() {
        this.$refs.overlay.components.overlay.hide();
    },
    updateEditorMode(language) {
        codeLib.setMode(this.editor, language, this.editor.getValue());
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

export default {
    methods,
    data
};