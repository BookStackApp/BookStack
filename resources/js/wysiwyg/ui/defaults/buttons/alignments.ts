import {BaseSelection, LexicalEditor} from "lexical";
import {EditorButtonDefinition} from "../../framework/buttons";
import alignLeftIcon from "@icons/editor/align-left.svg";
import {EditorUiContext} from "../../framework/core";
import alignCenterIcon from "@icons/editor/align-center.svg";
import alignRightIcon from "@icons/editor/align-right.svg";
import alignJustifyIcon from "@icons/editor/align-justify.svg";
import {
    $getBlockElementNodesInSelection,
    $getDecoratorNodesInSelection,
    $selectionContainsAlignment, getLastSelection
} from "../../../utils/selection";
import {CommonBlockAlignment} from "../../../nodes/_common";
import {nodeHasAlignment} from "../../../utils/nodes";


function setAlignmentForSection(editor: LexicalEditor, alignment: CommonBlockAlignment): void {
    const selection = getLastSelection(editor);
    const selectionNodes = selection?.getNodes() || [];
    const decorators = $getDecoratorNodesInSelection(selection);

    // Handle decorator node selection alignment
    if (selectionNodes.length === 1 && decorators.length === 1 && nodeHasAlignment(decorators[0])) {
        decorators[0].setAlignment(alignment);
        console.log('setting for decorator!');
        return;
    }

    // Handle normal block/range alignment
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
        context.editor.update(() => setAlignmentForSection(context.editor, 'left'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'left');
    }
};

export const alignCenter: EditorButtonDefinition = {
    label: 'Align center',
    icon: alignCenterIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSection(context.editor, 'center'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'center');
    }
};

export const alignRight: EditorButtonDefinition = {
    label: 'Align right',
    icon: alignRightIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSection(context.editor, 'right'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'right');
    }
};

export const alignJustify: EditorButtonDefinition = {
    label: 'Align justify',
    icon: alignJustifyIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => setAlignmentForSection(context.editor, 'justify'));
    },
    isActive(selection: BaseSelection|null) {
        return $selectionContainsAlignment(selection, 'justify');
    }
};
