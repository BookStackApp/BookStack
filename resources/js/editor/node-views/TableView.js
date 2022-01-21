class TableView {
    /**
     * @param {PmNode} node
     * @param {PmView} view
     * @param {(function(): number)} getPos
     */
    constructor(node, view, getPos) {
        this.dom = document.createElement("div")
        this.dom.className = "ProseMirror-tableWrapper"
        this.table = this.dom.appendChild(document.createElement("table"));
        this.table.setAttribute('style', node.attrs.style);
        this.colgroup = this.table.appendChild(document.createElement("colgroup"));
        this.contentDOM = this.table.appendChild(document.createElement("tbody"));
    }

    ignoreMutation(record) {
        return record.type == "attributes" && (record.target == this.table || this.colgroup.contains(record.target))
    }
}

export default TableView;