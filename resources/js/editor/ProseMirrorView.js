import {EditorState} from "prosemirror-state";
import {EditorView} from "prosemirror-view";
import {exampleSetup} from "prosemirror-example-setup";

import {DOMParser, DOMSerializer} from "prosemirror-model";

import schema from "./schema";

class ProseMirrorView {
    constructor(target, content) {

        // Build DOM from content
        const renderDoc = document.implementation.createHTMLDocument();
        renderDoc.body.innerHTML = content;

        this.view = new EditorView(target, {
            state: EditorState.create({
                doc: DOMParser.fromSchema(schema).parse(renderDoc.body),
                plugins: exampleSetup({schema})
            })
        });
    }

    get content() {
        const fragment = DOMSerializer.fromSchema(schema).serializeFragment(this.view.state.doc.content);
        const renderDoc = document.implementation.createHTMLDocument();
        renderDoc.body.appendChild(fragment);
        return renderDoc.body.innerHTML;
    }
    focus() { this.view.focus() }
    destroy() { this.view.destroy() }
}

export default ProseMirrorView;