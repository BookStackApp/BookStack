import {Schema} from "prosemirror-model";
import {schema as basicSchema} from "prosemirror-schema-basic";
import {addListNodes} from "prosemirror-schema-list";

const baseNodes = addListNodes(basicSchema.spec.nodes, "paragraph block*", "block");

const nodeCallout = {
    attrs: {
        type: {default: 'info'},
    },
    content: "inline*",
    group: "block",
    defining: true,
    parseDOM: [
        {tag: 'p.callout.info', attrs: {type: 'info'}},
        {tag: 'p.callout.success', attrs: {type: 'success'}},
        {tag: 'p.callout.danger', attrs: {type: 'danger'}},
        {tag: 'p.callout.warning', attrs: {type: 'warning'}},
        {tag: 'p.callout', attrs: {type: 'info'}},
    ],
    toDOM: function(node) {
        const type = node.attrs.type || 'info';
        return ['p', {class: 'callout ' + type}, 0];
    }
};

const customNodes = baseNodes.prepend({
    callout: nodeCallout,
});

const schema = new Schema({
    nodes: customNodes,
    marks: basicSchema.spec.marks
})

export default schema;