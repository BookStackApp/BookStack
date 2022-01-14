const link = {
    attrs: {
        href: {},
        title: {default: null}
    },
    inclusive: false,
    parseDOM: [{
        tag: "a[href]", getAttrs: function getAttrs(dom) {
            return {href: dom.getAttribute("href"), title: dom.getAttribute("title")}
        }
    }],
    toDOM: function toDOM(node) {
        const ref = node.attrs;
        const href = ref.href;
        const title = ref.title;
        return ["a", {href: href, title: title}, 0]
    }
};

const em = {
    parseDOM: [{tag: "i"}, {tag: "em"}, {style: "font-style=italic"}],
    toDOM() {
        return ["em", 0]
    }
};

const strong = {
    parseDOM: [{tag: "strong"},
        // This works around a Google Docs misbehavior where
        // pasted content will be inexplicably wrapped in `<b>`
        // tags with a font-weight normal.
        {
            tag: "b", getAttrs: function (node) {
                return node.style.fontWeight != "normal" && null;
            }
        },
        {
            style: "font-weight", getAttrs: function (value) {
                return /^(bold(er)?|[5-9]\d{2,})$/.test(value) && null;
            }
        }],
    toDOM() {
        return ["strong", 0]
    }
};

const code = {
    parseDOM: [{tag: "code"}],
    toDOM() {
        return ["code", 0]
    }
};

const underline = {
    parseDOM: [{tag: "u"}, {style: "text-decoration=underline"}],
    toDOM() {
        return ["span", {style: "text-decoration: underline;"}, 0];
    }
};

const strike = {
    parseDOM: [{tag: "s"}, {tag: "strike"}, {style: "text-decoration=line-through"}],
    toDOM() {
        return ["span", {style: "text-decoration: line-through;"}, 0];
    }
};

const superscript = {
    parseDOM: [{tag: "sup"}],
    toDOM() {
        return ["sup", 0];
    }
};

const subscript = {
    parseDOM: [{tag: "sub"}],
    toDOM() {
        return ["sub", 0];
    }
};

const text_color = {
    attrs: {
        color: {},
    },
    parseDOM: [{
        style: 'color',
        getAttrs(color) {
            return {color}
        }
    }],
    toDOM(node) {
        return ['span', {style: `color: ${node.attrs.color};`}, 0];
    }
};

const background_color = {
    attrs: {
        color: {},
    },
    parseDOM: [{
        style: 'background-color',
        getAttrs(color) {
            return {color}
        }
    }],
    toDOM(node) {
        return ['span', {style: `background-color: ${node.attrs.color};`}, 0];
    }
};

const marks = {
    link,
    em,
    strong,
    code,
    underline,
    strike,
    superscript,
    subscript,
    text_color,
    background_color,
};

export default marks;