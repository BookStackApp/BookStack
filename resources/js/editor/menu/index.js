import {
    MenuItem, Dropdown, DropdownSubmenu, renderGrouped, icons, joinUpItem, liftItem, selectParentNodeItem,
    undoItem, redoItem, wrapItem, blockTypeItem, setAttrItem,
} from "./menu"

import {toggleMark} from "prosemirror-commands";
import {menuBar} from "./menubar"
import schema from "../schema";


function cmdItem(cmd, options) {
    const passedOptions = {
        label: options.title,
        run: cmd
    };
    for (const prop in options) {
        passedOptions[prop] = options[prop];
    }
    if ((!options.enable || options.enable === true) && !options.select) {
        passedOptions[options.enable ? "enable" : "select"] = function (state) {
            return cmd(state);
        };
    }

    return new MenuItem(passedOptions)
}

function markActive(state, type) {
    const ref = state.selection;
    const from = ref.from;
    const $from = ref.$from;
    const to = ref.to;
    const empty = ref.empty;
    if (empty) {
        return type.isInSet(state.storedMarks || $from.marks())
    } else {
        return state.doc.rangeHasMark(from, to, type)
    }
}

function markItem(markType, options) {
    const passedOptions = {
        active: function active(state) {
            return markActive(state, markType)
        },
        enable: true
    };
    for (const prop in options) {
        passedOptions[prop] = options[prop];
    }

    return cmdItem(toggleMark(markType, passedOptions.attrs), passedOptions)
}

const inlineStyles = [
    markItem(schema.marks.strong, {title: "Bold", icon: icons.strong}),
    markItem(schema.marks.em, {title: "Italic", icon: icons.em}),
    markItem(schema.marks.underline, {title: "Underline", label: 'U'}),
    markItem(schema.marks.strike, {title: "Strikethrough", label: '-S-'}),
    markItem(schema.marks.superscript, {title: "Superscript", label: 'sup'}),
    markItem(schema.marks.subscript, {title: "Subscript", label: 'sub'}),
];

const formats = [
    blockTypeItem(schema.nodes.heading, {
        label: "Header Large",
        attrs: {level: 2}
    }),
    blockTypeItem(schema.nodes.heading, {
        label: "Header Medium",
        attrs: {level: 3}
    }),
    blockTypeItem(schema.nodes.heading, {
        label: "Header Small",
        attrs: {level: 4}
    }),
    blockTypeItem(schema.nodes.heading, {
        label: "Header Tiny",
        attrs: {level: 5}
    }),
    blockTypeItem(schema.nodes.paragraph, {
        label: "Paragraph",
        attrs: {}
    }),
    new DropdownSubmenu([
        blockTypeItem(schema.nodes.callout, {
            label: "Info Callout",
            attrs: {type: 'info'}
        }),
        blockTypeItem(schema.nodes.callout, {
            label: "Danger Callout",
            attrs: {type: 'danger'}
        }),
        blockTypeItem(schema.nodes.callout, {
            label: "Success Callout",
            attrs: {type: 'success'}
        }),
        blockTypeItem(schema.nodes.callout, {
            label: "Warning Callout",
            attrs: {type: 'warning'}
        })
    ], { label: 'Callouts' }),
];

const alignments = [
    setAttrItem('align', 'left', {
        label: 'Align Left'
    }),
    setAttrItem('align', 'right', {
        label: 'Align Right'
    }),
    setAttrItem('align', 'center', {
        label: 'Align Center'
    }),
    setAttrItem('align', 'justify', {
        label: 'Align Justify'
    }),
];

const colors = [
    markItem(schema.marks.text_color, {title: "Green", label: "Green", attrs: {color: '#00FF00'}}),
    markItem(schema.marks.text_color, {title: "Blue", label: "Blue", attrs: {color: '#0000FF'}}),
];

const menu = menuBar({
    floating: false,
    content: [
        [undoItem, redoItem],
        [new DropdownSubmenu(formats, { label: 'Formats' })],
        colors,
        inlineStyles,
        alignments,
    ],
});

export default menu;

// !! This module defines a number of building blocks for ProseMirror
// menus, along with a [menu bar](#menu.menuBar) implementation.

// MenuElement:: interface
// The types defined in this module aren't the only thing you can
// display in your menu. Anything that conforms to this interface can
// be put into a menu structure.
//
//   render:: (pm: EditorView) → {dom: dom.Node, update: (EditorState) → bool}
//   Render the element for display in the menu. Must return a DOM
//   element and a function that can be used to update the element to
//   a new state. The `update` function will return false if the
//   update hid the entire element.
