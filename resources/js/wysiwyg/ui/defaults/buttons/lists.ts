import {$isListNode, ListNode, ListType} from "@lexical/list";
import {EditorButtonDefinition} from "../../framework/buttons";
import {EditorUiContext} from "../../framework/core";
import {
    BaseSelection,
    LexicalNode,
} from "lexical";
import listBulletIcon from "@icons/editor/list-bullet.svg";
import listNumberedIcon from "@icons/editor/list-numbered.svg";
import listCheckIcon from "@icons/editor/list-check.svg";
import indentIncreaseIcon from "@icons/editor/indent-increase.svg";
import indentDecreaseIcon from "@icons/editor/indent-decrease.svg";
import {
    $selectionContainsNodeType,
} from "../../../utils/selection";
import {toggleSelectionAsList} from "../../../utils/formats";
import {$setInsetForSelection} from "../../../utils/lists";


function buildListButton(label: string, type: ListType, icon: string): EditorButtonDefinition {
    return {
        label,
        icon,
        action(context: EditorUiContext) {
            toggleSelectionAsList(context.editor, type);
        },
        isActive(selection: BaseSelection|null): boolean {
            return $selectionContainsNodeType(selection, (node: LexicalNode | null | undefined): boolean => {
                return $isListNode(node) && (node as ListNode).getListType() === type;
            });
        }
    };
}

export const bulletList: EditorButtonDefinition = buildListButton('Bullet list', 'bullet', listBulletIcon);
export const numberList: EditorButtonDefinition = buildListButton('Numbered list', 'number', listNumberedIcon);
export const taskList: EditorButtonDefinition = buildListButton('Task list', 'check', listCheckIcon);

export const indentIncrease: EditorButtonDefinition = {
    label: 'Increase indent',
    icon: indentIncreaseIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $setInsetForSelection(context.editor, 40);
        });
    },
    isActive() {
        return false;
    }
};

export const indentDecrease: EditorButtonDefinition = {
    label: 'Decrease indent',
    icon: indentDecreaseIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $setInsetForSelection(context.editor, -40);
        });
    },
    isActive() {
        return false;
    }
};