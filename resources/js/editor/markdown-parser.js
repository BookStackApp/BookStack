import schema from "./schema";
import markdownit from "markdown-it";
import {MarkdownParser, defaultMarkdownParser} from "prosemirror-markdown";
import {htmlToDoc} from "./util";

const tokens = defaultMarkdownParser.tokens;

// This is really a placeholder on the object to allow the below
// parser.tokenHandlers.html_block hack to work as desired.
tokens.html_block = {block: "callout", noCloseToken: true};

const tokenizer = markdownit("commonmark", {html: true});
const parser = new MarkdownParser(schema, tokenizer, tokens);

parser.tokenHandlers.html_block = function(state, tok, tokens, i) {
    const contentDoc = htmlToDoc(tok.content || '');
    for (const node of contentDoc.content.content) {
        state.addNode(node.type, node.attrs, node.content);
    }
};

export default parser;