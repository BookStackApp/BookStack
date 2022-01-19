import {EditorState} from "prosemirror-state";
import {EditorView} from "prosemirror-view";
import {exampleSetup} from "prosemirror-example-setup";

import {DOMParser} from "prosemirror-model";

import schema from "./schema";
import menu from "./menu";
import nodeViews from "./node-views";
import {stateToHtml} from "./util";

class ProseMirrorView {
    constructor(target, content) {

        // Build DOM from content
        const renderDoc = document.implementation.createHTMLDocument();
        renderDoc.body.innerHTML = content;

        this.view = new EditorView(target, {
            state: EditorState.create({
                doc: DOMParser.fromSchema(schema).parse(renderDoc.body),
                plugins: [
                    ...exampleSetup({schema, menuBar: false}),
                    menu,
                ]
            }),
            nodeViews,
        });
    }

    get content() {
        return stateToHtml(this.view.state);
    }

    focus() {
        this.view.focus()
    }

    destroy() {
        this.view.destroy()
    }
}

export default ProseMirrorView;