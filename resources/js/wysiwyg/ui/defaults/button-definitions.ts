import {EditorButtonDefinition} from "../framework/buttons";
import {
    $createParagraphNode,
    $isParagraphNode,
    BaseSelection, FORMAT_TEXT_COMMAND,
    LexicalEditor,
    LexicalNode,
    REDO_COMMAND, TextFormatType,
    UNDO_COMMAND
} from "lexical";
import {selectionContainsNodeType, selectionContainsTextFormat, toggleSelectionBlockNodeType} from "../../helpers";
import {$createCalloutNode, $isCalloutNodeOfCategory, CalloutCategory} from "../../nodes/callout";
import {
    $createHeadingNode,
    $createQuoteNode,
    $isHeadingNode,
    $isQuoteNode,
    HeadingNode,
    HeadingTagType
} from "@lexical/rich-text";
import {$isLinkNode, $toggleLink} from "@lexical/link";

export const undo: EditorButtonDefinition = {
    label: 'Undo',
    action(editor: LexicalEditor) {
        editor.dispatchCommand(UNDO_COMMAND, undefined);
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    }
}

export const redo: EditorButtonDefinition = {
    label: 'Redo',
    action(editor: LexicalEditor) {
        editor.dispatchCommand(REDO_COMMAND, undefined);
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

export const infoCallout: EditorButtonDefinition = buildCalloutButton('info', 'Info');
export const dangerCallout: EditorButtonDefinition = buildCalloutButton('danger', 'Danger');
export const warningCallout: EditorButtonDefinition = buildCalloutButton('warning', 'Warning');
export const successCallout: EditorButtonDefinition = buildCalloutButton('success', 'Success');

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

export const h2: EditorButtonDefinition = buildHeaderButton('h2', 'Large Header');
export const h3: EditorButtonDefinition = buildHeaderButton('h3', 'Medium Header');
export const h4: EditorButtonDefinition = buildHeaderButton('h4', 'Small Header');
export const h5: EditorButtonDefinition = buildHeaderButton('h5', 'Tiny Header');

export const blockquote: EditorButtonDefinition = {
    label: 'Blockquote',
    action(editor: LexicalEditor) {
        toggleSelectionBlockNodeType(editor, $isQuoteNode, $createQuoteNode);
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isQuoteNode);
    }
};

export const paragraph: EditorButtonDefinition = {
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

export const bold: EditorButtonDefinition = buildFormatButton('Bold', 'bold');
export const italic: EditorButtonDefinition = buildFormatButton('Italic', 'italic');
export const underline: EditorButtonDefinition = buildFormatButton('Underline', 'underline');
// Todo - Text color
// Todo - Highlight color
export const strikethrough: EditorButtonDefinition = buildFormatButton('Strikethrough', 'strikethrough');
export const superscript: EditorButtonDefinition = buildFormatButton('Superscript', 'superscript');
export const subscript: EditorButtonDefinition = buildFormatButton('Subscript', 'subscript');
export const code: EditorButtonDefinition = buildFormatButton('Inline Code', 'code');
// Todo - Clear formatting


export const link: EditorButtonDefinition = {
    label: 'Insert/edit link',
    action(editor: LexicalEditor) {
        editor.update(() => {
            $toggleLink('http://example.com');
        })
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isLinkNode);
    }
};

