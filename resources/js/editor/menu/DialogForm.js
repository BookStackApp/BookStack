// ::- Represents a submenu wrapping a group of elements that start
// hidden and expand to the right when hovered over or tapped.
import {prefix, renderItems} from "./menu-utils";
import crel from "crelt";

class DialogForm {
    // :: ([MenuElement], ?Object)
    // The following options are recognized:
    //
    // **`action`**`: function(FormData)`
    //   : The submission action to run when the form is submitted.
    // **`canceler`**`: function`
    //   : The cancel action to run when the form is cancelled.
    constructor(content, options) {
        this.options = options || {};
        this.content = Array.isArray(content) ? content : [content];
    }

    // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
    // Renders the submenu.
    render(view) {
        const items = renderItems(this.content, view)

        const formButtonCancel = crel("button", {class: prefix + "-dialog-button", type: "button"}, "Cancel");
        const formButtonSave = crel("button", {class: prefix + "-dialog-button", type: "submit"}, "Save");
        const footer = crel("div", {class: prefix + "-dialog-footer"}, formButtonCancel, formButtonSave);
        const form = crel("form", {class: prefix + "-dialog-form", action: '#'}, items.dom, footer);

        form.addEventListener('submit', event => {
            event.preventDefault();
            if (this.options.action) {
                this.options.action(new FormData(form));
            }
        });

        formButtonCancel.addEventListener('click', event => {
            if (this.options.canceler) {
                this.options.canceler();
            }
        });

        function update(state) {
            return items.update(state);
        }

        return {dom: form, update}
    }

}

export default DialogForm;