import crel from "crelt"
import {prefix} from "./menu-utils";
import {toggleMark} from "prosemirror-commands";

class ColorPickerGrid {

    constructor(markType, attrName, colors) {
        this.markType = markType;
        this.colors = colors
        this.attrName = attrName;
    }

    // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
    // Renders the submenu.
    render(view) {

        const colorElems = [];
        for (const color of this.colors) {
            const elem = crel("div", {class: prefix + "-color-grid-item", style: `background-color: ${color};`});
            colorElems.push(elem);
        }

        const wrap = crel("div", {class: prefix + "-color-grid-container"}, colorElems);
        wrap.addEventListener('click', event => {
            if (event.target.classList.contains(prefix + "-color-grid-item")) {
                const color = event.target.style.backgroundColor;
                const attrs = {[this.attrName]: color};
                toggleMark(this.markType, attrs)(view.state, view.dispatch, view, event);
            }
        });

        function update(state) {
            return true;
        }

        return {dom: wrap, update}
    }
}

export default ColorPickerGrid;