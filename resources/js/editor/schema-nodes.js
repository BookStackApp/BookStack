import {orderedList, bulletList, listItem} from "prosemirror-schema-list";

const alignAttrFromDomNode = node => {
    if (node.classList.contains('align-right')) {
        return 'right';
    }
    if (node.classList.contains('align-left')) {
        return 'left';
    }
    if (node.classList.contains('align-center')) {
        return 'center';
    }
    if (node.classList.contains('align-justify')) {
        return 'justify';
    }
    return null;
};

const doc = {
    content: "block+",
};

const paragraph = {
    content: "inline*",
    group: "block",
    parseDOM: [
        {
            tag: "p",
            getAttrs(node) {
                return {
                    align: alignAttrFromDomNode(node),
                };
            }
        }
    ],
    attrs: {
        align: {
            default: null,
        }
    },
    toDOM(node) {
        const attrs = {};
        if (node.attrs.align === 'right') {
            attrs['class'] = 'align-right';
        }
        if (node.attrs.align === 'left') {
            attrs['class'] = 'align-left';
        }
        return ["p", attrs, 0];
    }
};

const blockquote = {
    content: "block+",
    group: "block",
    defining: true,
    parseDOM: [{tag: "blockquote"}],
    align: {
        default: null,
    },
    toDOM() {
        return ["blockquote", 0];
    }
};

const horizontal_rule = {
    group: "block",
    parseDOM: [{tag: "hr"}],
    toDOM() {
        return ["hr"];
    }
};

const heading = {
    attrs: {level: {default: 1}, align: {default: null}},
    content: "inline*",
    group: "block",
    defining: true,
    parseDOM: [{tag: "h1", attrs: {level: 1}},
        {tag: "h2", attrs: {level: 2}},
        {tag: "h3", attrs: {level: 3}},
        {tag: "h4", attrs: {level: 4}},
        {tag: "h5", attrs: {level: 5}},
        {tag: "h6", attrs: {level: 6}}],
    toDOM(node) {
        return ["h" + node.attrs.level, 0]
    }
};

const code_block = {
    content: "text*",
    marks: "",
    group: "block",
    code: true,
    defining: true,
    parseDOM: [{tag: "pre", preserveWhitespace: "full"}],
    toDOM() {
        return ["pre", ["code", 0]];
    }
};

const text = {
    group: "inline"
};

const image = {
    inline: true,
    attrs: {
        src: {},
        alt: {default: null},
        title: {default: null}
    },
    group: "inline",
    draggable: true,
    parseDOM: [{
        tag: "img[src]", getAttrs: function getAttrs(dom) {
            return {
                src: dom.getAttribute("src"),
                title: dom.getAttribute("title"),
                alt: dom.getAttribute("alt")
            }
        }
    }],
    toDOM: function toDOM(node) {
        const ref = node.attrs;
        const src = ref.src;
        const alt = ref.alt;
        const title = ref.title;
        return ["img", {src: src, alt: alt, title: title}]
    }
};

const hard_break = {
    inline: true,
    group: "inline",
    selectable: false,
    parseDOM: [{tag: "br"}],
    toDOM() {
        return ["br"];
    }
};

const callout = {
    attrs: {
        type: {default: 'info'},
        align: {default: null},
    },
    content: "inline*",
    group: "block",
    defining: true,
    parseDOM: [
        {tag: 'p.callout.info', attrs: {type: 'info'}, priority: 75,},
        {tag: 'p.callout.success', attrs: {type: 'success'}, priority: 75,},
        {tag: 'p.callout.danger', attrs: {type: 'danger'}, priority: 75,},
        {tag: 'p.callout.warning', attrs: {type: 'warning'}, priority: 75,},
        {tag: 'p.callout', attrs: {type: 'info'}, priority: 75},
    ],
    toDOM(node) {
        const type = node.attrs.type || 'info';
        return ['p', {class: 'callout ' + type}, 0];
    }
};

const ordered_list = Object.assign({}, orderedList, {content: "list_item+", group: "block"});
const bullet_list = Object.assign({}, bulletList, {content: "list_item+", group: "block"});
const list_item = Object.assign({}, listItem, {content: 'paragraph block*'});

const nodes = {
    doc,
    paragraph,
    blockquote,
    horizontal_rule,
    heading,
    code_block,
    text,
    image,
    hard_break,
    callout,
    ordered_list,
    bullet_list,
    list_item,
};

export default nodes;