import {MarkdownSerializer, defaultMarkdownSerializer} from "prosemirror-markdown";
import {docToHtml} from "./util";

const nodes = defaultMarkdownSerializer.nodes;
const marks = defaultMarkdownSerializer.marks;


nodes.callout = function(state, node) {
    writeNodeAsHtml(state, node);
};


marks.underline = {
    open: '<span style="text-decoration: underline;">',
    close: '</span>',
};

marks.strike = {
    open: '<span style="text-decoration: line-through;">',
    close: '</span>',
};

marks.superscript = {
    open: '<sup>',
    close: '</sup>',
};

marks.subscript = {
    open: '<sub>',
    close: '</sub>',
};


function writeNodeAsHtml(state, node) {
    const html = docToHtml({ content: [node] });
    state.write(html);
    state.ensureNewLine();
    state.write('\n');
    state.closeBlock();
}

// Update serializers to just write out as HTML if we have an attribute
// or element that cannot be represented in commonmark without losing
// formatting or content.
for (const [nodeType, serializerFunction] of Object.entries(nodes)) {
    nodes[nodeType] = function(state, node) {
        if (node.attrs.align) {
            writeNodeAsHtml(state, node);
        } else {
            serializerFunction(state, node);
        }
    }
}


const serializer = new MarkdownSerializer(nodes, marks);

export default serializer;