import schema from "./schema";
import {DOMParser, DOMSerializer} from "prosemirror-model";

/**
 * @param {String} html
 * @return {PmNode}
 */
export function htmlToDoc(html) {
    const renderDoc = document.implementation.createHTMLDocument();
    renderDoc.body.innerHTML = html;
    return DOMParser.fromSchema(schema).parse(renderDoc.body);
}

/**
 * @param {PmNode} doc
 * @return {string}
 */
export function docToHtml(doc) {
    const fragment = DOMSerializer.fromSchema(schema).serializeFragment(doc.content);
    const renderDoc = document.implementation.createHTMLDocument();
    renderDoc.body.appendChild(fragment);
    return renderDoc.body.innerHTML;
}

/**
 * @class KeyedMultiStack
 * Holds many stacks, seperated via a key, with a simple
 * interface to pop and push values to the stacks.
 */
export class KeyedMultiStack {

    constructor() {
        this.stack = {};
    }

    /**
     * @param {String} key
     * @return {undefined|*}
     */
    pop(key) {
        if (Array.isArray(this.stack[key])) {
            return this.stack[key].pop();
        }
        return undefined;
    }

    /**
     * @param {String} key
     * @param {*} value
     */
    push(key, value) {
        if (this.stack[key] === undefined) {
            this.stack[key] = [];
        }

        this.stack[key].push(value);
    }
}