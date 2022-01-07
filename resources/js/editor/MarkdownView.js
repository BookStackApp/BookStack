import {htmlToDoc, docToHtml} from "./util";

import parser from "./markdown-parser";
import serializer from "./markdown-serializer";

class MarkdownView {
    constructor(target, content) {
        // Build DOM from content
        const htmlDoc = htmlToDoc(content);
        const markdown = serializer.serialize(htmlDoc);

        this.textarea = target.appendChild(document.createElement("textarea"))
        this.textarea.value = markdown;
    }

    get content() {
        const markdown = this.textarea.value;
        const doc = parser.parse(markdown);
        return docToHtml(doc);
    }

    focus() { this.textarea.focus() }
    destroy() { this.textarea.remove() }
}

export default MarkdownView;