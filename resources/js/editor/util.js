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
 * @param {PmEditorState} state
 * @return {String}
 */
export function stateToHtml(state) {
    const fragment = DOMSerializer.fromSchema(schema).serializeFragment(state.doc.content);
    const renderDoc = document.implementation.createHTMLDocument();
    renderDoc.body.appendChild(fragment);
    return renderDoc.body.innerHTML;
}

/**
 * @param {Object} object
 * @return {{}}
 */
export function nullifyEmptyValues(object) {
    const clean = {};
    for (const [key, value] of Object.entries(object)) {
        clean[key] = (value === "") ? null : value;
    }
    return clean;
}

/**
 * @param {PmEditorState} state
 * @param {PmMarkType} markType
 * @param {Number} pos
 * @return {{from: Number, to: Number}}
 */
export function markRangeAtPosition(state, markType, pos) {
    const $pos = state.doc.resolve(pos);

    const { parent, parentOffset } = $pos;
    const start = parent.childAfter(parentOffset);
    if (!start.node) return {from: -1, to: -1};

    const mark = start.node.marks.find((mark) => mark.type === markType);
    if (!mark) return {from: -1, to: -1};

    let startIndex = $pos.index();
    let startPos = $pos.start() + start.offset;
    let endIndex = startIndex + 1;
    let endPos = startPos + start.node.nodeSize;
    while (startIndex > 0 && mark.isInSet(parent.child(startIndex - 1).marks)) {
        startIndex -= 1;
        startPos -= parent.child(startIndex).nodeSize;
    }
    while (endIndex < parent.childCount && mark.isInSet(parent.child(endIndex).marks)) {
        endPos += parent.child(endIndex).nodeSize;
        endIndex += 1;
    }
    return { from: startPos, to: endPos };
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