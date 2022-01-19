import {
    MenuItem, Dropdown, DropdownSubmenu, renderGrouped, joinUpItem, liftItem, selectParentNodeItem,
    undoItem, redoItem, wrapItem, blockTypeItem, setAttrItem, insertBlockBeforeItem,
} from "./menu"
import {icons} from "./icons";
import ColorPickerGrid from "./ColorPickerGrid";
import TableCreatorGrid from "./TableCreatorGrid";
import {toggleMark} from "prosemirror-commands";
import {menuBar} from "./menubar"
import schema from "../schema";
import {removeMarks} from "../commands";

import itemAnchorButtonItem from "./item-anchor-button";
import itemHtmlSourceButton from "./item-html-source-button";


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
    markItem(schema.marks.underline, {title: "Underline", icon: icons.underline}),
    markItem(schema.marks.strike, {title: "Strikethrough", icon: icons.strike}),
    markItem(schema.marks.superscript, {title: "Superscript", icon: icons.superscript}),
    markItem(schema.marks.subscript, {title: "Subscript", icon: icons.subscript}),
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
    markItem(schema.marks.code, {
        label: "Inline Code",
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
        icon: icons.align_left
    }),
    setAttrItem('align', 'center', {
        icon: icons.align_center
    }),
    setAttrItem('align', 'right', {
        icon: icons.align_right
    }),
    setAttrItem('align', 'justify', {
        icon: icons.align_justify
    }),
];

const colorOptions = ["#000000","#993300","#333300","#003300","#003366","#000080","#333399","#333333","#800000","#FF6600","#808000","#008000","#008080","#0000FF","#666699","#808080","#FF0000","#FF9900","#99CC00","#339966","#33CCCC","#3366FF","#800080","#999999","#FF00FF","#FFCC00","#FFFF00","#00FF00","#00FFFF","#00CCFF","#993366","#FFFFFF","#FF99CC","#FFCC99","#FFFF99","#CCFFCC","#CCFFFF","#99CCFF","#CC99FF"];

const colors = [
    new DropdownSubmenu([
        new ColorPickerGrid(schema.marks.text_color, 'color', colorOptions),
    ], {icon: icons.text_color}),
    new DropdownSubmenu([
        new ColorPickerGrid(schema.marks.background_color, 'color', colorOptions),
    ], {icon: icons.background_color}),
];

const lists = [
    wrapItem(schema.nodes.bullet_list, {
        title: "Bullet List",
        icon: icons.bullet_list,
    }),
    wrapItem(schema.nodes.ordered_list, {
        title: "Ordered List",
        icon: icons.ordered_list,
    }),
];

const inserts = [
    itemAnchorButtonItem(),
    insertBlockBeforeItem(schema.nodes.horizontal_rule, {
        title: "Horizontal Rule",
        icon: icons.horizontal_rule,
    }),
    new DropdownSubmenu([
        new TableCreatorGrid()
    ], {icon: icons.table}),
    itemHtmlSourceButton(),
];

const utilities = [
    new MenuItem({
        title: 'Clear Formatting',
        icon: icons.format_clear,
        run: removeMarks(),
        enable: state => true,
    }),
];

const menu = menuBar({
    floating: false,
    content: [
        [undoItem, redoItem],
        [new DropdownSubmenu(formats, { label: 'Formats' })],
        inlineStyles,
        colors,
        alignments,
        lists,
        inserts,
        utilities,
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
