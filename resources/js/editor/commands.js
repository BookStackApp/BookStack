/**
 * @param {String} attrName
 * @param {String} attrValue
 * @return {PmCommandHandler}
 */
export function setBlockAttr(attrName, attrValue) {
    return function (state, dispatch) {
        const ref = state.selection;
        const from = ref.from;
        const to = ref.to;
        let applicable = false;

        state.doc.nodesBetween(from, to, function (node, pos) {
            if (applicable) {
                return false
            }
            if (!node.isTextblock || node.attrs[attrName] === attrValue) {
                return
            }

            applicable = node.attrs[attrName] !== undefined;
        });

        if (!applicable) {
            return false
        }

        if (dispatch) {
            const tr = state.tr;
            tr.doc.nodesBetween(from, to, function (node, pos) {
                const nodeAttrs = Object.assign({}, node.attrs);
                if (node.attrs[attrName] !== undefined) {
                    nodeAttrs[attrName] = attrValue;
                    tr.setBlockType(pos, pos + 1, node.type, nodeAttrs)
                }
            });

            dispatch(tr);
        }

        return true
    }
}

/**
 * @param {PmNodeType} blockType
 * @return {PmCommandHandler}
 */
export function insertBlockBefore(blockType) {
    return function (state, dispatch) {
        const startPosition = state.selection.$from.before(1);

        if (dispatch) {
            dispatch(state.tr.insert(startPosition, blockType.create()));
        }

        return true
    }
}

/**
 * @param {Number} rows
 * @param {Number} columns
 * @return {PmCommandHandler}
 */
export function insertTable(rows, columns) {
    return function (state, dispatch) {
        if (!dispatch) return true;

        const tr = state.tr;
        const nodes = state.schema.nodes;

        const rowNodes = [];
        for (let y = 0; y < rows; y++) {
            const rowCells = [];
            for (let x = 0; x < columns; x++) {
                rowCells.push(nodes.table_cell.create(null));
            }
            rowNodes.push(nodes.table_row.create(null, rowCells));
        }

        const table = nodes.table.create(null, rowNodes);
        tr.replaceSelectionWith(table);
        dispatch(tr);

        return true;
    }
}

/**
 * @return {PmCommandHandler}
 */
export function removeMarks() {
    return function (state, dispatch) {
        if (dispatch) {
            dispatch(state.tr.removeMark(state.selection.from, state.selection.to, null));
        }
        return true;
    }
}