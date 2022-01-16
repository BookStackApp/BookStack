import {Schema} from "prosemirror-model";

import nodes from "./schema-nodes";
import marks from "./schema-marks";

/** @var {PmSchema} schema */
const schema = new Schema({
    nodes,
    marks,
});

export default schema;