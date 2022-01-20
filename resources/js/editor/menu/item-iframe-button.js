import DialogBox from "./DialogBox";
import DialogForm from "./DialogForm";
import DialogInput from "./DialogInput";
import schema from "../schema";

import {MenuItem} from "./menu";
import {icons} from "./icons";
import {nullifyEmptyValues} from "../util";

/**
 * @param {PmNodeType} nodeType
 * @param {String} attribute
 * @return {(function(PmEditorState): (string|null))}
 */
function getNodeAttribute(nodeType, attribute) {
    return function (state) {
        const node = state.selection.node;
        if (node && node.type === nodeType) {
            return node.attrs[attribute];
        }

        return null;
    };
}

/**
 * @param {(function(FormData))} submitter
 * @param {Function} closer
 * @return {DialogBox}
 */
function getLinkDialog(submitter, closer) {
    return new DialogBox([
        new DialogForm([
            new DialogInput({
                label: 'Source URL',
                id: 'src',
                value: getNodeAttribute(schema.nodes.iframe, 'src'),
            }),
            new DialogInput({
                label: 'Hover Label',
                id: 'title',
                value: getNodeAttribute(schema.nodes.iframe, 'title'),
            }),
            new DialogInput({
                label: 'Width',
                id: 'width',
                value: getNodeAttribute(schema.nodes.iframe, 'width'),
            }),
            new DialogInput({
                label: 'Height',
                id: 'height',
                value: getNodeAttribute(schema.nodes.iframe, 'height'),
            }),
        ], {
            canceler: closer,
            action: submitter,
        }),
    ], {
        label: 'Insert Embedded Content',
        closer: closer,
    });
}

/**
 * @param {FormData} formData
 * @param {PmEditorState} state
 * @param {PmDispatchFunction} dispatch
 * @return {boolean}
 */
function applyIframe(formData, state, dispatch) {
    const attrs = nullifyEmptyValues(Object.fromEntries(formData));
    if (!dispatch) return true;

    const tr = state.tr;
    const currentNodeAttrs = state.selection?.nodes?.attrs || {};
    const newAttrs = Object.assign({}, currentNodeAttrs, attrs);
    tr.replaceSelectionWith(schema.nodes.iframe.create(newAttrs));

    dispatch(tr);
    return true;
}

/**
 * @param {PmEditorState} state
 * @param {PmDispatchFunction} dispatch
 * @param {PmView} view
 * @param {Event} e
 */
function onPress(state, dispatch, view, e) {
    const dialog = getLinkDialog((data) => {
        applyIframe(data, state, dispatch);
        dom.remove();
    }, () => {
        dom.remove();
    })

    const {dom, update} = dialog.render(view);
    update(state);
    document.body.appendChild(dom);
}

/**
 * @return {MenuItem}
 */
function iframeButtonItem() {
    return new MenuItem({
        title: "Embed Content",
        run: onPress,
        enable: state => true,
        active: state => (state.selection.node || {type: ''}).type === schema.nodes.iframe,
        icon: icons.iframe,
    });
}

export default iframeButtonItem;