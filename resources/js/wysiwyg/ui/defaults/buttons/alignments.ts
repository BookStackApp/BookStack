import {$getSelection, BaseSelection} from "lexical";
import {EditorButtonDefinition} from "../../framework/buttons";
import alignLeftIcon from "@icons/editor/align-left.svg";
import {EditorUiContext} from "../../framework/core";
import alignCenterIcon from "@icons/editor/align-center.svg";
import alignRightIcon from "@icons/editor/align-right.svg";
import alignJustifyIcon from "@icons/editor/align-justify.svg";
import {$getBlockElementNodesInSelection, $selectionContainsElementFormat} from "../../../utils/selection";
import {CommonBlockAlignment} from "../../../nodes/_common";
import {nodeHasAlignment} from "../../../utils/nodes";


function setAlignmentForSection(alignment: CommonBlockAlignment): void {
    const selection = $getSelection();
    const elements = $getBlockElementNodesInSelection(selection);
    for (const node of elements) {
        if (nodeHasAlignment(node)) {
            node.setAlignment(alignment)
        }
    }
}

export const alignLeft: EditorButtonDefinition = {
    label: 'Align left',
    icon: alignLeftIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSection('left'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsElementFormat(selection, 'left');
    }
};

export const alignCenter: EditorButtonDefinition = {
    label: 'Align center',
    icon: alignCenterIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSection('center'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsElementFormat(selection, 'center');
    }
};

export const alignRight: EditorButtonDefinition = {
    label: 'Align right',
    icon: alignRightIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSection('right'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsElementFormat(selection, 'right');
    }
};

export const alignJustify: EditorButtonDefinition = {
    label: 'Align justify',
    icon: alignJustifyIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSection('justify'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsElementFormat(selection, 'justify');
    }
};
