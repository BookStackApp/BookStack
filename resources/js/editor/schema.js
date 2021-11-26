import {Schema} from "prosemirror-model";
import {schema as basicSchema} from "prosemirror-schema-basic";
import {addListNodes} from "prosemirror-schema-list";

const bookstackSchema = new Schema({
    nodes: addListNodes(basicSchema.spec.nodes, "paragraph block*", "block"),
    marks: basicSchema.spec.marks
})

export {bookstackSchema as schema};