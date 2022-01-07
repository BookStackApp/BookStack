import schema from "./schema";
import {DOMParser, DOMSerializer} from "prosemirror-model";


export function htmlToDoc(html) {
    const renderDoc = document.implementation.createHTMLDocument();
    renderDoc.body.innerHTML = html;
    return DOMParser.fromSchema(schema).parse(renderDoc.body);
}

export function docToHtml(doc) {
    const fragment = DOMSerializer.fromSchema(schema).serializeFragment(doc.content);
    const renderDoc = document.implementation.createHTMLDocument();
    renderDoc.body.appendChild(fragment);
    return renderDoc.body.innerHTML;
}