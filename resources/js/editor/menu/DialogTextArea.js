// ::- Represents a submenu wrapping a group of elements that start
// hidden and expand to the right when hovered over or tapped.
import {prefix, randHtmlId} from "./menu-utils";
import crel from "crelt";

class DialogTextArea {
    // :: (?Object)
    // The following options are recognized:
    //
    // **`label`**`: string`
    //   : The label to show for the input.
    // **`id`**`: string`
    //   : The id to use for this input
    // **`attrs`**`: Object`
    //   : The attributes to add to the input element.
    // **`value`**`: function(state) -> string`
    //   : The getter for the input value.
    constructor(options) {
        this.options = options || {};
    }

    // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
    // Renders the submenu.
    render(view) {
        const id = randHtmlId();
        const inputAttrs = Object.assign({type: "text", name: this.options.id, id: this.options.id}, this.options.attrs || {})
        const input = crel("textarea", inputAttrs);
        const label = this.options.label ? crel("label", {for: id}, this.options.label) : null;

        const rowRap = crel("div", {class: prefix + '-dialog-textarea-wrap'}, label, input);

        const update = (state) => {
            input.value = this.options.value(state);
            return true;
        }

        return {dom: rowRap, update}
    }

}

export default DialogTextArea;