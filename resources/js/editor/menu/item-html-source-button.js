import DialogBox from "./DialogBox";
import DialogForm from "./DialogForm";
import DialogTextArea from "./DialogTextArea";

import {MenuItem} from "./menu";
import {icons} from "./icons";
import {htmlToDoc, stateToHtml} from "../util";

/**
 * @param {(function(FormData))} submitter
 * @param {Function} closer
 * @return {DialogBox}
 */
function getLinkDialog(submitter, closer) {
    return new DialogBox([
        new DialogForm([
            new DialogTextArea({
                id: 'source',
                value: stateToHtml,
                attrs: {
                    rows: 10,
                    cols: 50,
                }
            }),
        ], {
            canceler: closer,
            action: submitter,
        }),
    ], {
        label: 'View/Edit HTML Source',
        closer: closer,
    });
}

/**
 * @param {FormData} formData
 * @param {PmEditorState} state
 * @param {PmDispatchFunction} dispatch
 * @return {boolean}
 */
function replaceEditorHtml(formData, state, dispatch) {
    const html = formData.get('source');

    if (dispatch) {
        const tr = state.tr;

        const newDoc = htmlToDoc(html);
        tr.replaceWith(0, state.doc.content.size, newDoc.content);
        dispatch(tr);
    }

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
        replaceEditorHtml(data, state, dispatch);
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
function htmlSourceButtonItem() {
    return new MenuItem({
        title: "View HTML Source",
        run: onPress,
        enable: state => true,
        icon: icons.source_code,
    });
}

export default htmlSourceButtonItem;