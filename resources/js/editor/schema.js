import {Schema} from "prosemirror-model";
import {schema as basicSchema} from "prosemirror-schema-basic";
import {addListNodes} from "prosemirror-schema-list";

const baseNodes = addListNodes(basicSchema.spec.nodes, "paragraph block*", "block");
const baseMarks = basicSchema.spec.marks;

const nodeCallout = {
    attrs: {
        type: {default: 'info'},
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

const markUnderline = {
    parseDOM: [{tag: "u"}, {style: "text-decoration=underline"}],
    toDOM() {
        return ["span", {style: "text-decoration: underline;"}, 0];
    }
};

const markStrike = {
    parseDOM: [{tag: "s"}, {tag: "strike"}, {style: "text-decoration=line-through"}],
    toDOM() {
        return ["span", {style: "text-decoration: line-through;"}, 0];
    }
};

const markSuperscript = {
    parseDOM: [{tag: "sup"}],
    toDOM() {
        return ["sup", 0];
    }
};

const markSubscript = {
    parseDOM: [{tag: "sub"}],
    toDOM() {
        return ["sub", 0];
    }
};

const customNodes = baseNodes.append({
    callout: nodeCallout,
});

const customMarks = baseMarks.append({
    underline: markUnderline,
    strike: markStrike,
    superscript: markSuperscript,
    subscript: markSubscript,
});

const schema = new Schema({
    nodes: customNodes,
    marks: customMarks,
})

export default schema;