import {EditorButtonDefinition} from "./editor-button";
import {
    $createParagraphNode,
    $isParagraphNode,
    BaseSelection, FORMAT_TEXT_COMMAND,
    LexicalEditor,
    LexicalNode,
    REDO_COMMAND, TextFormatType,
    UNDO_COMMAND
} from "lexical";
import {selectionContainsNodeType, selectionContainsTextFormat, toggleSelectionBlockNodeType} from "../helpers";
import {$createCalloutNode, $isCalloutNodeOfCategory, CalloutCategory} from "../nodes/callout";
import {
    $createHeadingNode,
    $createQuoteNode,
    $isHeadingNode,
    $isQuoteNode,
    HeadingNode,
    HeadingTagType
} from "@lexical/rich-text";

export const undoButton: EditorButtonDefinition = {
    label: 'Undo',
    action(editor: LexicalEditor) {
        editor.dispatchCommand(UNDO_COMMAND);
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    }
}

export const redoButton: EditorButtonDefinition = {
    label: 'Redo',
    action(editor: LexicalEditor) {
        editor.dispatchCommand(REDO_COMMAND);
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    }
}

function buildCalloutButton(category: CalloutCategory, name: string): EditorButtonDefinition {
    return {
        label: `${name} Callout`,
        action(editor: LexicalEditor) {
            toggleSelectionBlockNodeType(
                editor,
                (node) => $isCalloutNodeOfCategory(node, category),
                () => $createCalloutNode(category),
            )
        },
        isActive(selection: BaseSelection|null): boolean {
            return selectionContainsNodeType(selection, (node) => $isCalloutNodeOfCategory(node, category));
        }
    };
}

export const infoCalloutButton: EditorButtonDefinition = buildCalloutButton('info', 'Info');
export const dangerCalloutButton: EditorButtonDefinition = buildCalloutButton('danger', 'Danger');
export const warningCalloutButton: EditorButtonDefinition = buildCalloutButton('warning', 'Warning');
export const successCalloutButton: EditorButtonDefinition = buildCalloutButton('success', 'Success');

const isHeaderNodeOfTag = (node: LexicalNode | null | undefined, tag: HeadingTagType) => {
      return $isHeadingNode(node) && (node as HeadingNode).getTag() === tag;
};

function buildHeaderButton(tag: HeadingTagType, name: string): EditorButtonDefinition {
    return {
        label: name,
        action(editor: LexicalEditor) {
            toggleSelectionBlockNodeType(
                editor,
                (node) => isHeaderNodeOfTag(node, tag),
                () => $createHeadingNode(tag),
            )
        },
        isActive(selection: BaseSelection|null): boolean {
            return selectionContainsNodeType(selection, (node) => isHeaderNodeOfTag(node, tag));
        }
    };
}

export const h2Button: EditorButtonDefinition = buildHeaderButton('h2', 'Large Header');
export const h3Button: EditorButtonDefinition = buildHeaderButton('h3', 'Medium Header');
export const h4Button: EditorButtonDefinition = buildHeaderButton('h4', 'Small Header');
export const h5Button: EditorButtonDefinition = buildHeaderButton('h5', 'Tiny Header');

export const blockquoteButton: EditorButtonDefinition = {
    label: 'Blockquote',
    action(editor: LexicalEditor) {
        toggleSelectionBlockNodeType(editor, $isQuoteNode, $createQuoteNode);
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isQuoteNode);
    }
};

export const paragraphButton: EditorButtonDefinition = {
    label: 'Paragraph',
    action(editor: LexicalEditor) {
        toggleSelectionBlockNodeType(editor, $isParagraphNode, $createParagraphNode);
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isParagraphNode);
    }
}

function buildFormatButton(label: string, format: TextFormatType): EditorButtonDefinition {
    return {
        label: label,
        action(editor: LexicalEditor) {
            editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
        },
        isActive(selection: BaseSelection|null): boolean {
            return selectionContainsTextFormat(selection, format);
        }
    };
}

export const boldButton: EditorButtonDefinition = buildFormatButton('Bold', 'bold');
export const italicButton: EditorButtonDefinition = buildFormatButton('Italic', 'italic');
export const underlineButton: EditorButtonDefinition = buildFormatButton('Underline', 'underline');
// Todo - Text color
// Todo - Highlight color
export const strikethroughButton: EditorButtonDefinition = buildFormatButton('Strikethrough', 'strikethrough');
export const superscriptButton: EditorButtonDefinition = buildFormatButton('Superscript', 'superscript');
export const subscriptButton: EditorButtonDefinition = buildFormatButton('Subscript', 'subscript');
export const codeButton: EditorButtonDefinition = buildFormatButton('Inline Code', 'code');
// Todo - Clear formatting