import {$isElementNode, BaseSelection, LexicalEditor} from "lexical";
import {EditorButtonDefinition} from "../../framework/buttons";
import alignLeftIcon from "@icons/editor/align-left.svg";
import {EditorUiContext} from "../../framework/core";
import alignCenterIcon from "@icons/editor/align-center.svg";
import alignRightIcon from "@icons/editor/align-right.svg";
import alignJustifyIcon from "@icons/editor/align-justify.svg";
import ltrIcon from "@icons/editor/direction-ltr.svg";
import rtlIcon from "@icons/editor/direction-rtl.svg";
import {
    $getBlockElementNodesInSelection,
    $selectionContainsAlignment, $selectionContainsDirection, $selectSingleNode, $toggleSelection, getLastSelection
} from "../../../utils/selection";
import {CommonBlockAlignment} from "../../../nodes/_common";
import {nodeHasAlignment} from "../../../utils/nodes";


function setAlignmentForSelection(editor: LexicalEditor, alignment: CommonBlockAlignment): void {
    const selection = getLastSelection(editor);
    const selectionNodes = selection?.getNodes() || [];

    // Handle inline node selection alignment
    if (selectionNodes.length === 1 && $isElementNode(selectionNodes[0]) && selectionNodes[0].isInline() && nodeHasAlignment(selectionNodes[0])) {
        selectionNodes[0].setAlignment(alignment);
        $selectSingleNode(selectionNodes[0]);
        $toggleSelection(editor);
        return;
    }

    // Handle normal block/range alignment
    const elements = $getBlockElementNodesInSelection(selection);
    for (const node of elements) {
        if (nodeHasAlignment(node)) {
            node.setAlignment(alignment)
        }
    }
    $toggleSelection(editor);
}

function setDirectionForSelection(editor: LexicalEditor, direction: 'ltr' | 'rtl'): void {
    const selection = getLastSelection(editor);

    const elements = $getBlockElementNodesInSelection(selection);
    for (const node of elements) {
        console.log('setting direction', node);
        node.setDirection(direction);
    }
}

export const alignLeft: EditorButtonDefinition = {
    label: 'Align left',
    icon: alignLeftIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSelection(context.editor, 'left'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'left');
    }
};

export const alignCenter: EditorButtonDefinition = {
    label: 'Align center',
    icon: alignCenterIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSelection(context.editor, 'center'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'center');
    }
};

export const alignRight: EditorButtonDefinition = {
    label: 'Align right',
    icon: alignRightIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSelection(context.editor, 'right'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'right');
    }
};

export const alignJustify: EditorButtonDefinition = {
    label: 'Align justify',
    icon: alignJustifyIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSelection(context.editor, 'justify'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'justify');
    }
};

export const directionLTR: EditorButtonDefinition = {
    label: 'Left to right',
    icon: ltrIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setDirectionForSelection(context.editor, 'ltr'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsDirection(selection, 'ltr');
    }
};

export const directionRTL: EditorButtonDefinition = {
    label: 'Right to left',
    icon: rtlIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setDirectionForSelection(context.editor, 'rtl'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsDirection(selection, 'rtl');
    }
};