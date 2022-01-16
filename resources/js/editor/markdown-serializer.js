import {MarkdownSerializer, defaultMarkdownSerializer, MarkdownSerializerState} from "prosemirror-markdown";
import {docToHtml} from "./util";

const nodes = defaultMarkdownSerializer.nodes;
const marks = defaultMarkdownSerializer.marks;


nodes.callout = function (state, node) {
    writeNodeAsHtml(state, node);
};

function isPlainURL(link, parent, index, side) {
    if (link.attrs.title || !/^\w+:/.test(link.attrs.href)) {
        return false
    }
    const content = parent.child(index + (side < 0 ? -1 : 0));
    if (!content.isText || content.text != link.attrs.href || content.marks[content.marks.length - 1] != link) {
        return false
    }
    if (index == (side < 0 ? 1 : parent.childCount - 1)) {
        return true
    }
    const next = parent.child(index + (side < 0 ? -2 : 1));
    return !link.isInSet(next.marks)
}

marks.link = {
    open(state, mark, parent, index) {
        const attrs = mark.attrs;
        if (attrs.target) {
            return `<a href="${attrs.target}" ${attrs.title ? `title="${attrs.title}"` : ''} target="${attrs.target}">`
        }
        return isPlainURL(mark, parent, index, 1) ? "<" : "["
    },
    close(state, mark, parent, index) {
        if (mark.attrs.target) {
            return `</a>`;
        }
        return isPlainURL(mark, parent, index, -1) ? ">"
            : "](" + state.esc(mark.attrs.href) + (mark.attrs.title ? " " + state.quote(mark.attrs.title) : "") + ")"
    }
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

marks.text_color = {
    open(state, mark, parent, index) {
        return `<span style="color: ${mark.attrs.color};">`
    },
    close: '</span>',
};

marks.background_color = {
    open(state, mark, parent, index) {
        return `<span style="background-color: ${mark.attrs.color};">`
    },
    close: '</span>',
};

/**
 * @param {MarkdownSerializerState} state
 * @param node
 */
function writeNodeAsHtml(state, node) {
    const html = docToHtml({content: [node]});
    state.write(html);
    state.ensureNewLine();
    state.write('\n');
    state.closeBlock();
}

// Update serializers to just write out as HTML if we have an attribute
// or element that cannot be represented in commonmark without losing
// formatting or content.
for (const [nodeType, serializerFunction] of Object.entries(nodes)) {
    nodes[nodeType] = function (state, node, parent, index) {
        if (node.attrs.align) {
            writeNodeAsHtml(state, node);
        } else {
            serializerFunction(state, node, parent, index);
        }
    }
}


const serializer = new MarkdownSerializer(nodes, marks);

export default serializer;