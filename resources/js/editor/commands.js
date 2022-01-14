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
                    tr.setBlockType(pos, pos+1, node.type, nodeAttrs)
                }
            });

            dispatch(tr);
        }

        return true
    }
}

export function insertBlockBefore(blockType) {
    return function (state, dispatch) {
        const startPosition = state.selection.$from.before(1);

        if (dispatch) {
            dispatch(state.tr.insert(startPosition, blockType.create()));
        }

        return true
    }
}