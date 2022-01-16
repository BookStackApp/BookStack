import DialogBox from "./DialogBox";
import DialogForm from "./DialogForm";
import DialogInput from "./DialogInput";
import schema from "../schema";

import {MenuItem} from "./menu";
import {icons} from "./icons";


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

function getLinkDialog(submitter, closer) {
    return new DialogBox([
        new DialogForm([
            new DialogInput({
                label: 'URL',
                id: 'href',
                value: getMarkAttribute(schema.marks.link, 'href'),
            }),
            new DialogInput({
                label: 'Title',
                id: 'title',
                value: getMarkAttribute(schema.marks.link, 'title'),
            }),
            new DialogInput({
                label: 'Target',
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

function applyLink(formData, state, dispatch) {
    const selection = state.selection;
    const attrs = Object.fromEntries(formData);
    if (dispatch) {
        const tr = state.tr;

        if (attrs.href) {
            tr.addMark(selection.from, selection.to, schema.marks.link.create(attrs));
        } else {
            tr.removeMark(selection.from, selection.to, schema.marks.link);
        }
        dispatch(tr);
    }
    return true;
}

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

function anchorButtonItem() {
    return new MenuItem({
        title: "Insert/Edit Anchor Link",
        run: onPress,
        enable: state => true,
        icon: icons.link,
    });
}

export default anchorButtonItem;