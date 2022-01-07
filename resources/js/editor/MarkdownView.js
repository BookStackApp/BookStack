import schema from "./schema";
import {MarkdownSerializer, MarkdownParser} from "prosemirror-markdown";
import {DOMParser, DOMSerializer} from "prosemirror-model";
import markdownit from "markdown-it";


function listIsTight(tokens, i) {
    while (++i < tokens.length)
    { if (tokens[i].type != "list_item_open") { return tokens[i].hidden } }
    return false
}

// TODO - Need to tweak parser logic
//  so HTML blocks get parsed out using the normal DOMParser logic.
//  Likely need to copy & alter the inner parsing logic

const mdParser = new MarkdownParser(schema, markdownit("commonmark", {html: true}), {
    blockquote: {block: "blockquote"},
    paragraph: {block: "paragraph"},
    html_block: { block: "callout", noCloseToken: true, getAttrs: function(tok) {
        return {
            type: 'info',
        }
    }},
    list_item: {block: "list_item"},
    bullet_list: {block: "bullet_list", getAttrs: function (_, tokens, i) { return ({tight: listIsTight(tokens, i)}); }},
    ordered_list: {block: "ordered_list", getAttrs: function (tok, tokens, i) { return ({
            order: +tok.attrGet("start") || 1,
            tight: listIsTight(tokens, i)
        }); }},
    heading: {block: "heading", getAttrs: function (tok) { return ({level: +tok.tag.slice(1)}); }},
    code_block: {block: "code_block", noCloseToken: true},
    fence: {block: "code_block", getAttrs: function (tok) { return ({params: tok.info || ""}); }, noCloseToken: true},
    hr: {node: "horizontal_rule"},
    image: {node: "image", getAttrs: function (tok) { return ({
            src: tok.attrGet("src"),
            title: tok.attrGet("title") || null,
            alt: tok.children[0] && tok.children[0].content || null
        }); }},
    hardbreak: {node: "hard_break"},

    em: {mark: "em"},
    strong: {mark: "strong"},
    link: {mark: "link", getAttrs: function (tok) { return ({
            href: tok.attrGet("href"),
            title: tok.attrGet("title") || null
        }); }},
    code_inline: {mark: "code", noCloseToken: true}
});

const mdSerializer = new MarkdownSerializer({
    blockquote: function blockquote(state, node) {
        state.wrapBlock("> ", null, node, function () { return state.renderContent(node); });
    },
    callout: function(state, node) {
        state.write(`<p class="callout ${node.attrs.type}">\n`);
        state.text(node.textContent, false);
        state.ensureNewLine();
        state.write(`</p>`);
        state.closeBlock(node);
    },
    code_block: function code_block(state, node) {
        state.write("```" + (node.attrs.params || "") + "\n");
        state.text(node.textContent, false);
        state.ensureNewLine();
        state.write("```");
        state.closeBlock(node);
    },
    heading: function heading(state, node) {
        state.write(state.repeat("#", node.attrs.level) + " ");
        state.renderInline(node);
        state.closeBlock(node);
    },
    horizontal_rule: function horizontal_rule(state, node) {
        state.write(node.attrs.markup || "---");
        state.closeBlock(node);
    },
    bullet_list: function bullet_list(state, node) {
        state.renderList(node, "  ", function () { return (node.attrs.bullet || "*") + " "; });
    },
    ordered_list: function ordered_list(state, node) {
        var start = node.attrs.order || 1;
        var maxW = String(start + node.childCount - 1).length;
        var space = state.repeat(" ", maxW + 2);
        state.renderList(node, space, function (i) {
            var nStr = String(start + i);
            return state.repeat(" ", maxW - nStr.length) + nStr + ". "
        });
    },
    list_item: function list_item(state, node) {
        state.renderContent(node);
    },
    paragraph: function paragraph(state, node) {
        state.renderInline(node);
        state.closeBlock(node);
    },

    image: function image(state, node) {
        state.write("![" + state.esc(node.attrs.alt || "") + "](" + state.esc(node.attrs.src) +
            (node.attrs.title ? " " + state.quote(node.attrs.title) : "") + ")");
    },
    hard_break: function hard_break(state, node, parent, index) {
        for (var i = index + 1; i < parent.childCount; i++)
        { if (parent.child(i).type != node.type) {
            state.write("\\\n");
            return
        } }
    },
    text: function text(state, node) {
        state.text(node.text);
    }
}, {
    em: {open: "*", close: "*", mixable: true, expelEnclosingWhitespace: true},
    strong: {open: "**", close: "**", mixable: true, expelEnclosingWhitespace: true},
    link: {
        open: function open(_state, mark, parent, index) {
            return isPlainURL(mark, parent, index, 1) ? "<" : "["
        },
        close: function close(state, mark, parent, index) {
            return isPlainURL(mark, parent, index, -1) ? ">"
                : "](" + state.esc(mark.attrs.href) + (mark.attrs.title ? " " + state.quote(mark.attrs.title) : "") + ")"
        }
    },
    code: {open: function open(_state, _mark, parent, index) { return backticksFor(parent.child(index), -1) },
        close: function close(_state, _mark, parent, index) { return backticksFor(parent.child(index - 1), 1) },
        escape: false}
});

function backticksFor(node, side) {
    var ticks = /`+/g, m, len = 0;
    if (node.isText) { while (m = ticks.exec(node.text)) { len = Math.max(len, m[0].length); } }
    var result = len > 0 && side > 0 ? " `" : "`";
    for (var i = 0; i < len; i++) { result += "`"; }
    if (len > 0 && side < 0) { result += " "; }
    return result
}

function isPlainURL(link, parent, index, side) {
    if (link.attrs.title || !/^\w+:/.test(link.attrs.href)) { return false }
    var content = parent.child(index + (side < 0 ? -1 : 0));
    if (!content.isText || content.text != link.attrs.href || content.marks[content.marks.length - 1] != link) { return false }
    if (index == (side < 0 ? 1 : parent.childCount - 1)) { return true }
    var next = parent.child(index + (side < 0 ? -2 : 1));
    return !link.isInSet(next.marks)
}

class MarkdownView {
    constructor(target, content) {

        // Build DOM from content
        const renderDoc = document.implementation.createHTMLDocument();
        renderDoc.body.innerHTML = content;

        const htmlDoc = DOMParser.fromSchema(schema).parse(renderDoc.body);
        const markdown = mdSerializer.serialize(htmlDoc);

        this.textarea = target.appendChild(document.createElement("textarea"))
        this.textarea.value = markdown;
    }

    get content() {
        const markdown = this.textarea.value;
        const doc = mdParser.parse(markdown);
        console.log(doc);
        const fragment = DOMSerializer.fromSchema(schema).serializeFragment(doc.content);
        const renderDoc = document.implementation.createHTMLDocument();
        renderDoc.body.appendChild(fragment);
        return renderDoc.body.innerHTML;
    }

    focus() { this.textarea.focus() }
    destroy() { this.textarea.remove() }
}

export default MarkdownView;