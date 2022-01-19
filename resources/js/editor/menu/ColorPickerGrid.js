import crel from "crelt"
import {prefix} from "./menu-utils";
import {TextSelection} from "prosemirror-state"
import {expandSelectionToMark} from "../util";


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
                this.onColorSelect(view, color);
            }
        });

        function update(state) {
            return true;
        }

        return {dom: wrap, update}
    }

    onColorSelect(view, color) {
        const attrs = {[this.attrName]: color};
        const selection = view.state.selection;
        const {from, to} = expandSelectionToMark(view.state, selection, this.markType);
        const tr = view.state.tr;

        const currentColorMarks = selection.$from.marksAcross(selection.$to) || [];
        const activeRelevantMark = currentColorMarks.filter(mark => {
            return mark.type === this.markType;
        })[0];
        const colorIsActive = activeRelevantMark && activeRelevantMark.attrs[this.attrName] === color;

        tr.removeMark(from, to, this.markType);
        if (!colorIsActive) {
            tr.addMark(from, to, this.markType.create(attrs));
        }

        tr.setSelection(TextSelection.create(tr.doc, from, to));
        view.dispatch(tr);
    }
}

export default ColorPickerGrid;