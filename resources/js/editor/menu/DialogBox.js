// ::- Represents a submenu wrapping a group of elements that start
// hidden and expand to the right when hovered over or tapped.
import {prefix, renderItems} from "./menu-utils";
import crel from "crelt";
import {getIcon, icons} from "./icons";

class DialogBox {
    // :: ([MenuElement], ?Object)
    // The following options are recognized:
    //
    // **`label`**`: string`
    //   : The label to show on the dialog.
    // **`closer`**`: function`
    //   : The function to run when the dialog should close.
    constructor(content, options) {
        this.options = options || {};
        this.content = Array.isArray(content) ? content : [content];

        this.closeMouseDownListener = null;
        this.wrap = null;
    }

    // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
    // Renders the submenu.
    render(view) {
        const items = renderItems(this.content, view)

        const titleText = crel("div", {class: prefix + "-dialog-title-text"}, this.options.label);
        const titleClose = crel("button", {class: prefix + "-dialog-title-close primary-background", type: "button"}, getIcon(icons.close));
        const titleContent = crel("div", {class: prefix + "-dialog-title"}, titleText, titleClose);
        const dialog = crel("div", {class: prefix + "-dialog"}, titleContent,
            crel("div", {class: prefix + "-dialog-content"}, items.dom));
        const wrap = crel("div", {class: prefix + "-dialog-wrap"}, dialog);
        this.wrap = wrap;

        this.closeMouseDownListener = (event) => {
            if (!dialog.contains(event.target) || titleClose.contains(event.target)) {
                this.close();
            }
        }

        wrap.addEventListener("click", this.closeMouseDownListener);

        function update(state) {
            let inner = items.update(state)
            wrap.style.display = inner ? "" : "none"
            return inner;
        }
        return {dom: wrap, update}
    }

    close() {
        if (this.options.closer) {
            this.options.closer();
        }
    }
}

export default DialogBox;