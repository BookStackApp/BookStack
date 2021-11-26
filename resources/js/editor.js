import {EditorState} from "prosemirror-state";
import {EditorView} from "prosemirror-view";
import {exampleSetup} from "prosemirror-example-setup";
import {defaultMarkdownParser,
    defaultMarkdownSerializer} from "prosemirror-markdown";
import {DOMParser, DOMSerializer} from "prosemirror-model";

import {schema} from "./editor/schema";

class MarkdownView {
    constructor(target, content) {

        // Build DOM from content
        const renderDoc = document.implementation.createHTMLDocument();
        renderDoc.body.innerHTML = content;

        const htmlDoc = DOMParser.fromSchema(schema).parse(renderDoc.body);
        const markdown = defaultMarkdownSerializer.serialize(htmlDoc);

        this.textarea = target.appendChild(document.createElement("textarea"))
        this.textarea.value = markdown;
    }

    get content() {
        const markdown = this.textarea.value;
        const doc = defaultMarkdownParser.parse(markdown);
        const fragment = DOMSerializer.fromSchema(schema).serializeFragment(doc.content);
        const renderDoc = document.implementation.createHTMLDocument();
        renderDoc.body.appendChild(fragment);
        return renderDoc.body.innerHTML;
    }

    focus() { this.textarea.focus() }
    destroy() { this.textarea.remove() }
}

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

const place = document.querySelector("#editor");
let view = new ProseMirrorView(place, document.getElementById('content').innerHTML);

const markdownToggle = document.getElementById('markdown-toggle');
markdownToggle.addEventListener('change', event => {
    const View = markdownToggle.checked ? MarkdownView : ProseMirrorView;
    if (view instanceof View) return
    const content = view.content
    console.log(content);
    view.destroy()
    view = new View(place, content)
    view.focus()
});