// ::- Represents a submenu wrapping a group of elements that start
// hidden and expand to the right when hovered over or tapped.
import {prefix, randHtmlId} from "./menu-utils";
import crel from "crelt";

class DialogRadioOptions {
    /**
     * Given inputOptions should be keyed by label, with values being values.
     * Values of empty string will be treated as null.
     * @param {Object} inputOptions
     * @param {{label: string, id: string, attrs?: Object, value: function(PmEditorState): string|null}} options
     */
    constructor(inputOptions, options) {
        this.inputOptions = inputOptions;
        this.options = options || {};
    }

    // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
    // Renders the submenu.
    render(view) {

        const inputs = [];
        const optionInputLabels = Object.keys(this.inputOptions).map(label => {
            const inputAttrs = Object.assign({
                type: "radio",
                name: this.options.id,
                value: this.inputOptions[label],
                class: prefix + '-dialog-radio-option',
            }, this.options.attrs || {});
            const input = crel("input", inputAttrs);
            inputs.push(input);
            return crel("label", input, label);
        });

        const optionInputWrap = crel("div", {class: prefix + '-dialog-radio-option-wrap'}, optionInputLabels);

        const label = crel("label", {}, this.options.label);
        const rowRap = crel("div", {class: prefix + '-dialog-form-row'}, label, optionInputWrap);

        const update = (state) => {
            const value = this.options.value(state);
            for (const input of inputs) {
                input.checked = (input.value === value || (value === null && input.value === ""));
            }
            return true;
        }

        return {dom: rowRap, update}
    }

}

export default DialogRadioOptions;