import schema from "./schema";
import markdownit from "markdown-it";
import {MarkdownParser, defaultMarkdownParser} from "prosemirror-markdown";
import {htmlToDoc, KeyedMultiStack} from "./util";

const tokens = defaultMarkdownParser.tokens;

// These are really a placeholder on the object to allow the below
// parser.tokenHandlers.html_[block/inline] hacks to work as desired.
tokens.html_block = {block: "callout", noCloseToken: true};
tokens.html_inline = {mark: "underline"};

const tokenizer = markdownit("commonmark", {html: true});
const parser = new MarkdownParser(schema, tokenizer, tokens);

// When we come across HTML blocks we use the document schema to parse them
// into nodes then re-add those back into the parser state.
parser.tokenHandlers.html_block = function(state, tok, tokens, i) {
    const contentDoc = htmlToDoc(tok.content || '');
    for (const node of contentDoc.content.content) {
        state.addNode(node.type, node.attrs, node.content);
    }
};

// When we come across inline HTML we parse out the tag and keep track of
// that in a stack, along with the marks they parse out to.
// We open/close the marks within the state depending on the tag open/close type.
const tagStack = new KeyedMultiStack();
parser.tokenHandlers.html_inline = function(state, tok, tokens, i) {
    const isClosing = tok.content.startsWith('</');
    const isSelfClosing = tok.content.endsWith('/>');
    const tagName = parseTagNameFromHtmlTokenContent(tok.content);

    if (!isClosing) {
        const completeTag = isSelfClosing ?  tok.content : `${tok.content}a</${tagName}>`;
        const marks = extractMarksFromHtml(completeTag);
        tagStack.push(tagName, marks);
        for (const mark of marks) {
            state.openMark(mark);
        }
    }

    if (isSelfClosing || isClosing) {
        const marks = (tagStack.pop(tagName) || []).reverse();
        for (const mark of marks) {
            state.closeMark(mark);
        }
    }
}

/**
 * @param {String} html
 * @return {PmMark[]}
 */
function extractMarksFromHtml(html) {
    const contentDoc = htmlToDoc('<p>' + (html || '') + '</p>');
    const marks = contentDoc?.content?.content?.[0]?.content?.content?.[0]?.marks;
    return marks || [];
}

/**
 * @param {string} tokenContent
 * @return {string}
 */
function parseTagNameFromHtmlTokenContent(tokenContent) {
    return tokenContent.split(' ')[0].replace(/[<>\/]/g, '').toLowerCase();
}

export default parser;