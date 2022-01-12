import {schema as basicSchema} from "prosemirror-schema-basic";

const baseMarks = basicSchema.spec.marks;

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

const marks = baseMarks.append({
    underline,
    strike,
    superscript,
    subscript,
    text_color,
});

export default marks;