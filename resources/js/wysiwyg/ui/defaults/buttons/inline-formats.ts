import {$getSelection, $isTextNode, BaseSelection, FORMAT_TEXT_COMMAND, TextFormatType} from "lexical";
import {EditorBasicButtonDefinition, EditorButtonDefinition} from "../../framework/buttons";
import {EditorUiContext} from "../../framework/core";
import boldIcon from "@icons/editor/bold.svg";
import italicIcon from "@icons/editor/italic.svg";
import underlinedIcon from "@icons/editor/underlined.svg";
import textColorIcon from "@icons/editor/text-color.svg";
import highlightIcon from "@icons/editor/highlighter.svg";
import strikethroughIcon from "@icons/editor/strikethrough.svg";
import superscriptIcon from "@icons/editor/superscript.svg";
import subscriptIcon from "@icons/editor/subscript.svg";
import codeIcon from "@icons/editor/code.svg";
import formatClearIcon from "@icons/editor/format-clear.svg";
import {$selectionContainsTextFormat} from "../../../utils/selection";
import {$patchStyleText} from "@lexical/selection";

function buildFormatButton(label: string, format: TextFormatType, icon: string): EditorButtonDefinition {
    return {
        label: label,
        icon,
        action(context: EditorUiContext) {
            context.editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
        },
        isActive(selection: BaseSelection|null): boolean {
            return $selectionContainsTextFormat(selection, format);
        }
    };
}

export const bold: EditorButtonDefinition = buildFormatButton('Bold', 'bold', boldIcon);
export const italic: EditorButtonDefinition = buildFormatButton('Italic', 'italic', italicIcon);
export const underline: EditorButtonDefinition = buildFormatButton('Underline', 'underline', underlinedIcon);
export const textColor: EditorBasicButtonDefinition = {label: 'Text color', icon: textColorIcon};
export const highlightColor: EditorBasicButtonDefinition = {label: 'Highlight color', icon: highlightIcon};

function colorAction(context: EditorUiContext, property: string, color: string): void {
    context.editor.update(() => {
        const selection = $getSelection();
        if (selection) {
            $patchStyleText(selection, {[property]: color || null});
        }
    });
}

export const textColorAction = (color: string, context: EditorUiContext) => colorAction(context, 'color', color);
export const highlightColorAction = (color: string, context: EditorUiContext) => colorAction(context, 'background-color', color);

export const strikethrough: EditorButtonDefinition = buildFormatButton('Strikethrough', 'strikethrough', strikethroughIcon);
export const superscript: EditorButtonDefinition = buildFormatButton('Superscript', 'superscript', superscriptIcon);
export const subscript: EditorButtonDefinition = buildFormatButton('Subscript', 'subscript', subscriptIcon);
export const code: EditorButtonDefinition = buildFormatButton('Inline code', 'code', codeIcon);
export const clearFormating: EditorButtonDefinition = {
    label: 'Clear formatting',
    icon: formatClearIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const selection = $getSelection();
            for (const node of selection?.getNodes() || []) {
                if ($isTextNode(node)) {
                    node.setFormat(0);
                    node.setStyle('');
                }
            }
        });
    },
    isActive() {
        return false;
    }
};