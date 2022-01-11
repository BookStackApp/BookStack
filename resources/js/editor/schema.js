import {Schema} from "prosemirror-model";

import nodes from "./schema-nodes";
import marks from "./schema-marks";

const index = new Schema({
    nodes,
    marks,
});

export default index;