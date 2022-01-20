import {orderedList, bulletList, listItem} from "prosemirror-schema-list";
import {tableNodes} from "prosemirror-tables";

/**
 * @param {HTMLElement} node
 * @return {string|null}
 */
function getAlignAttrFromDomNode(node) {
    const classList = node.classList;
    const styles = node.style || {};
    const alignments = ['right', 'left', 'center', 'justify'];
    for (const alignment of alignments) {
        if (classList.contains('align-' + alignment) || styles.textAlign === alignment) {
            return alignment;
        }
    }
    return null;
}

/**
 * @param node
 * @param {Object} attrs
 * @return {Object}
 */
function addAlignmentAttr(node, attrs) {
    const positions = ['right', 'left', 'center', 'justify'];
    for (const position of positions) {
        if (node.attrs.align === position) {
            return addClassToAttrs('align-' + position, attrs);
        }
    }
    return attrs;
}

function getAttrsParserForAlignment(node) {
    return {
        align: getAlignAttrFromDomNode(node),
    };
}

/**
 * @param {String} className
 * @param {Object} attrs
 * @return {Object}
 */
function addClassToAttrs(className, attrs) {
    return Object.assign({}, attrs, {
        class: attrs.class ? attrs.class + ' ' + className : className,
    });
}

/**
 * @param {String[]} attrNames
 * @return {function(Element): {}}
 */
function domAttrsToAttrsParser(attrNames) {
    return function (node) {
        const attrs = {};
        for (const attr of attrNames) {
            attrs[attr] = node.hasAttribute(attr) ? node.getAttribute(attr) : null;
        }
        return attrs;
    };
}

/**
 * @param {PmNode} node
 * @param {String[]} attrNames
 */
function extractAttrsForDom(node, attrNames) {
    const domAttrs = {};
    for (const attr of attrNames) {
        if (node.attrs[attr]) {
            domAttrs[attr] = node.attrs[attr];
        }
    }
    return domAttrs;
}

const doc = {
    content: "block+",
};

const paragraph = {
    content: "inline*",
    group: "block",
    parseDOM: [
        {
            tag: "p",
            getAttrs: getAttrsParserForAlignment,
        }
    ],
    attrs: {
        align: {
            default: null,
        }
    },
    toDOM(node) {
        return ["p", addAlignmentAttr(node, {}), 0];
    }
};

const blockquote = {
    content: "block+",
    group: "block",
    defining: true,
    parseDOM: [{tag: "blockquote", getAttrs: getAttrsParserForAlignment}],
    attrs: {
        align: {
            default: null,
        }
    },
    toDOM(node) {
        return ["blockquote", addAlignmentAttr(node, {}), 0];
    }
};

const horizontal_rule = {
    group: "block",
    parseDOM: [{tag: "hr"}],
    toDOM() {
        return ["hr"];
    }
};


const headingParseGetAttrs = (level) => {
    return function (node) {
        return {level, align: getAlignAttrFromDomNode(node)};
    };
};
const heading = {
    attrs: {level: {default: 1}, align: {default: null}},
    content: "inline*",
    group: "block",
    defining: true,
    parseDOM: [
        {tag: "h1", getAttrs: headingParseGetAttrs(1)},
        {tag: "h2", getAttrs: headingParseGetAttrs(2)},
        {tag: "h3", getAttrs: headingParseGetAttrs(3)},
        {tag: "h4", getAttrs: headingParseGetAttrs(4)},
        {tag: "h5", getAttrs: headingParseGetAttrs(5)},
        {tag: "h6", getAttrs: headingParseGetAttrs(6)},
    ],
    toDOM(node) {
        return ["h" + node.attrs.level, addAlignmentAttr(node, {}), 0]
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
        title: {default: null},
        height: {default: null},
        width: {default: null},
    },
    group: "inline",
    draggable: true,
    parseDOM: [{
        tag: "img[src]", getAttrs: function getAttrs(dom) {
            return {
                src: dom.getAttribute("src"),
                title: dom.getAttribute("title"),
                alt: dom.getAttribute("alt"),
                height: dom.getAttribute("height"),
                width: dom.getAttribute("width"),
            }
        }
    }],
    toDOM: function toDOM(node) {
        const ref = node.attrs;
        const src = ref.src;
        const alt = ref.alt;
        const title = ref.title;
        const width = ref.width;
        const height = ref.height;
        return ["img", {src, alt, title, width, height}]
    }
};

const iframe = {
    attrs: {
        src: {},
        height: {default: null},
        width: {default: null},
        title: {default: null},
        allow: {default: null},
        sandbox: {default: null},
    },
    group: "block",
    draggable: true,
    parseDOM: [{
        tag: "iframe",
        getAttrs: domAttrsToAttrsParser(["src", "width", "height", "title", "allow", "sandbox"]),
    }],
    toDOM(node) {
        const attrs = extractAttrsForDom(node, ["src", "width", "height", "title", "allow", "sandbox"])
        return ["iframe", attrs];
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


const calloutParseGetAttrs = (type) => {
    return function (node) {
        return {type, align: getAlignAttrFromDomNode(node)};
    };
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
        {tag: 'p.callout.info', getAttrs: calloutParseGetAttrs('info'), priority: 75},
        {tag: 'p.callout.success', getAttrs: calloutParseGetAttrs('success'), priority: 75},
        {tag: 'p.callout.danger', getAttrs: calloutParseGetAttrs('danger'), priority: 75},
        {tag: 'p.callout.warning', getAttrs: calloutParseGetAttrs('warning'), priority: 75},
        {tag: 'p.callout', getAttrs: calloutParseGetAttrs('info'), priority: 75},
    ],
    toDOM(node) {
        const type = node.attrs.type || 'info';
        return ['p', addAlignmentAttr(node, {class: 'callout ' + type}), 0];
    }
};

const ordered_list = Object.assign({}, orderedList, {content: "list_item+", group: "block"});
const bullet_list = Object.assign({}, bulletList, {content: "list_item+", group: "block"});
const list_item = Object.assign({}, listItem, {content: 'paragraph block*'});

const {
    table_row,
    table_cell,
    table_header,
} = tableNodes({
    tableGroup: "block",
    cellContent: "block+"
});

const table = {
    content: "table_row+",
    attrs: {
        style: {default: null},
    },
    tableRole: "table",
    isolating: true,
    group: "block",
    parseDOM: [{tag: "table", getAttrs: domAttrsToAttrsParser(['style'])}],
    toDOM(node) {
        console.log(extractAttrsForDom(node, ['style']));
        return ["table", extractAttrsForDom(node, ['style']), ["tbody", 0]]
    }
};

const nodes = {
    doc,
    paragraph,
    blockquote,
    horizontal_rule,
    heading,
    code_block,
    text,
    image,
    iframe,
    hard_break,
    callout,
    ordered_list,
    bullet_list,
    list_item,
    table,
    table_row,
    table_cell,
    table_header,
};

export default nodes;