import {$isListNode, insertList, ListNode, ListType, removeList} from "@lexical/list";
import {EditorButtonDefinition} from "../../framework/buttons";
import {EditorUiContext} from "../../framework/core";
import {$getSelection, BaseSelection, LexicalNode} from "lexical";
import listBulletIcon from "@icons/editor/list-bullet.svg";
import listNumberedIcon from "@icons/editor/list-numbered.svg";
import listCheckIcon from "@icons/editor/list-check.svg";
import {$selectionContainsNodeType} from "../../../utils/selection";


function buildListButton(label: string, type: ListType, icon: string): EditorButtonDefinition {
    return {
        label,
        icon,
        action(context: EditorUiContext) {
            context.editor.getEditorState().read(() => {
                const selection = $getSelection();
                if (this.isActive(selection, context)) {
                    removeList(context.editor);
                } else {
                    insertList(context.editor, type);
                }
            });
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
