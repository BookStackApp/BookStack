import DialogBox from "./DialogBox";
import DialogForm from "./DialogForm";
import DialogInput from "./DialogInput";
import DialogRadioOptions from "./DialogRadioOptions";
import schema from "../schema";

import {MenuItem} from "./menu";
import {icons} from "./icons";
import {markRangeAtPosition, nullifyEmptyValues} from "../util";

/**
 * @param {PmMarkType} markType
 * @param {String} attribute
 * @return {(function(PmEditorState): (string|null))}
 */
function getMarkAttribute(markType, attribute) {
    return function (state) {
        const marks = state.selection.$head.marks();
        for (const mark of marks) {
            if (mark.type === markType) {
                return mark.attrs[attribute];
            }
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
                label: 'URL',
                id: 'href',
                value: getMarkAttribute(schema.marks.link, 'href'),
            }),
            new DialogInput({
                label: 'Hover Label',
                id: 'title',
                value: getMarkAttribute(schema.marks.link, 'title'),
            }),
            new DialogRadioOptions({
                "Same tab or window": "",
                "New tab or window": "_blank",
            }, {
                label: 'Behaviour',
                id: 'target',
                value: getMarkAttribute(schema.marks.link, 'target'),
            })
        ], {
            canceler: closer,
            action: submitter,
        }),
    ], {
        label: 'Insert Link',
        closer: closer,
    });
}

/**
 * @param {FormData} formData
 * @param {PmEditorState} state
 * @param {PmDispatchFunction} dispatch
 * @return {boolean}
 */
function applyLink(formData, state, dispatch) {
    const selection = state.selection;
    const attrs = nullifyEmptyValues(Object.fromEntries(formData));
    if (!dispatch) return true;

    const tr = state.tr;
    const noRange = (selection.from - selection.to === 0);
    let from = selection.from;
    let to = selection.to;

    if (noRange) {
        const linkRange = markRangeAtPosition(state, schema.marks.link, selection.from);
        if (linkRange.from !== -1) {
            from = linkRange.from;
            to = linkRange.to;
        }
    }

    if (attrs.href) {
        tr.addMark(from, to, schema.marks.link.create(attrs));
    } else {
        tr.removeMark(from, to, schema.marks.link);
    }

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
        applyLink(data, state, dispatch);
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
function anchorButtonItem() {
    return new MenuItem({
        title: "Insert/Edit Anchor Link",
        run: onPress,
        enable: state => true,
        icon: icons.link,
    });
}

export default anchorButtonItem;