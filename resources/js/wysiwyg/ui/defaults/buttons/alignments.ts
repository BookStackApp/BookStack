import {$isElementNode, BaseSelection} from "lexical";
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
    $selectionContainsAlignment, $selectionContainsDirection, $selectSingleNode, getLastSelection
} from "../../../utils/selection";
import {CommonBlockAlignment} from "lexical/nodes/common";
import {nodeHasAlignment} from "../../../utils/nodes";


function setAlignmentForSelection(context: EditorUiContext, alignment: CommonBlockAlignment): void {
    const selection = getLastSelection(context.editor);
    const selectionNodes = selection?.getNodes() || [];

    // Handle inline node selection alignment
    if (selectionNodes.length === 1 && $isElementNode(selectionNodes[0]) && selectionNodes[0].isInline() && nodeHasAlignment(selectionNodes[0])) {
        selectionNodes[0].setAlignment(alignment);
        $selectSingleNode(selectionNodes[0]);
        context.manager.triggerFutureStateRefresh();
        return;
    }

    // Handle normal block/range alignment
    const elements = $getBlockElementNodesInSelection(selection);
    const alignmentNodes = elements.filter(n => nodeHasAlignment(n));
    const allAlreadyAligned = alignmentNodes.every(n => n.getAlignment() === alignment);
    const newAlignment = allAlreadyAligned ? '' : alignment;
    for (const node of alignmentNodes) {
        node.setAlignment(newAlignment);
    }

    context.manager.triggerFutureStateRefresh();
}

function setDirectionForSelection(context: EditorUiContext, direction: 'ltr' | 'rtl'): void {
    const selection = getLastSelection(context.editor);

    const elements = $getBlockElementNodesInSelection(selection);
    for (const node of elements) {
        node.setDirection(direction);
    }

    context.manager.triggerFutureStateRefresh();
}

export const alignLeft: EditorButtonDefinition = {
    label: 'Align left',
    icon: alignLeftIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSelection(context, 'left'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'left');
    }
};

export const alignCenter: EditorButtonDefinition = {
    label: 'Align center',
    icon: alignCenterIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSelection(context, 'center'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'center');
    }
};

export const alignRight: EditorButtonDefinition = {
    label: 'Align right',
    icon: alignRightIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSelection(context, 'right'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'right');
    }
};

export const alignJustify: EditorButtonDefinition = {
    label: 'Justify',
    icon: alignJustifyIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSelection(context, 'justify'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'justify');
    }
};

export const directionLTR: EditorButtonDefinition = {
    label: 'Left to right',
    icon: ltrIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setDirectionForSelection(context, 'ltr'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsDirection(selection, 'ltr');
    }
};

export const directionRTL: EditorButtonDefinition = {
    label: 'Right to left',
    icon: rtlIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setDirectionForSelection(context, 'rtl'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsDirection(selection, 'rtl');
    }
};