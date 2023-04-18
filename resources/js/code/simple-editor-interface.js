import {updateViewLanguage} from "./views";


export class SimpleEditorInterface {
    /**
     * @param {EditorView} editorView
     */
    constructor(editorView) {
        this.ev = editorView;
    }

    /**
     * Get the contents of an editor instance.
     * @return {string}
     */
    getContent() {
        return this.ev.state.doc.toString();
    }

    /**
     * Set the contents of an editor instance.
     * @param content
     */
    setContent(content) {
        const doc = this.ev.state.doc;
        this.ev.dispatch({
            changes: {from: 0, to: doc.length, insert: content}
        });
    }

    /**
     * Return focus to the editor instance.
     */
    focus() {
        this.ev.focus();
    }

    /**
     * Set the language mode of the editor instance.
     * @param {String} mode
     * @param {String} content
     */
    setMode(mode, content = '') {
        updateViewLanguage(this.ev, mode, content);
    }
}