class IframeView {
    /**
     * @param {PmNode} node
     * @param {PmView} view
     * @param {(function(): number)} getPos
     */
    constructor(node, view, getPos) {
        this.dom = document.createElement('div');
        this.dom.classList.add('ProseMirror-iframewrap');

        this.iframe = document.createElement("iframe");
        for (const [key, value] of Object.entries(node.attrs)) {
            if (value) {
                this.iframe.setAttribute(key, value);
            }
        }

        this.dom.appendChild(this.iframe);
    }

    stopEvent() {
        return false;
    }
}

export default IframeView;