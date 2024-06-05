import {EditorButtonDefinition} from "../framework/buttons";
import {
    $createNodeSelection,
    $createParagraphNode, $getSelection,
    $isParagraphNode, $setSelection,
    BaseSelection, FORMAT_TEXT_COMMAND,
    LexicalNode,
    REDO_COMMAND, TextFormatType,
    UNDO_COMMAND
} from "lexical";
import {
    getNodeFromSelection,
    selectionContainsNodeType,
    selectionContainsTextFormat,
    toggleSelectionBlockNodeType
} from "../../helpers";
import {$createCalloutNode, $isCalloutNodeOfCategory, CalloutCategory} from "../../nodes/callout";
import {
    $createHeadingNode,
    $createQuoteNode,
    $isHeadingNode,
    $isQuoteNode,
    HeadingNode,
    HeadingTagType
} from "@lexical/rich-text";
import {$isLinkNode, $toggleLink, LinkNode} from "@lexical/link";
import {EditorUiContext} from "../framework/core";
import {$isImageNode, ImageNode} from "../../nodes/image";

export const undo: EditorButtonDefinition = {
    label: 'Undo',
    action(context: EditorUiContext) {
        context.editor.dispatchCommand(UNDO_COMMAND, undefined);
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    }
}

export const redo: EditorButtonDefinition = {
    label: 'Redo',
    action(context: EditorUiContext) {
        context.editor.dispatchCommand(REDO_COMMAND, undefined);
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    }
}

function buildCalloutButton(category: CalloutCategory, name: string): EditorButtonDefinition {
    return {
        label: `${name} Callout`,
        action(context: EditorUiContext) {
            toggleSelectionBlockNodeType(
                context.editor,
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
        action(context: EditorUiContext) {
            toggleSelectionBlockNodeType(
                context.editor,
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
    action(context: EditorUiContext) {
        toggleSelectionBlockNodeType(context.editor, $isQuoteNode, $createQuoteNode);
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isQuoteNode);
    }
};

export const paragraph: EditorButtonDefinition = {
    label: 'Paragraph',
    action(context: EditorUiContext) {
        toggleSelectionBlockNodeType(context.editor, $isParagraphNode, $createParagraphNode);
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isParagraphNode);
    }
}

function buildFormatButton(label: string, format: TextFormatType): EditorButtonDefinition {
    return {
        label: label,
        action(context: EditorUiContext) {
            context.editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
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
    action(context: EditorUiContext) {
        const linkModal = context.manager.createModal('link');
        context.editor.getEditorState().read(() => {
            const selection = $getSelection();
            const selectedLink = getNodeFromSelection(selection, $isLinkNode) as LinkNode|null;

            let formDefaults = {};
            if (selectedLink) {
                formDefaults = {
                    url: selectedLink.getURL(),
                    text: selectedLink.getTextContent(),
                    title: selectedLink.getTitle(),
                    target: selectedLink.getTarget(),
                }

                context.editor.update(() => {
                    const selection = $createNodeSelection();
                    selection.add(selectedLink.getKey());
                    $setSelection(selection);
                });
            }

            linkModal.show(formDefaults);
        });
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isLinkNode);
    }
};

export const image: EditorButtonDefinition = {
    label: 'Insert/Edit Image',
    action(context: EditorUiContext) {
        const imageModal = context.manager.createModal('image');
        const selection = context.lastSelection;
        const selectedImage = getNodeFromSelection(selection, $isImageNode) as ImageNode|null;

        context.editor.getEditorState().read(() => {
            let formDefaults = {};
            if (selectedImage) {
                formDefaults = {
                    src: selectedImage.getSrc(),
                    alt: selectedImage.getAltText(),
                    height: selectedImage.getHeight(),
                    width: selectedImage.getWidth(),
                }

                context.editor.update(() => {
                    const selection = $createNodeSelection();
                    selection.add(selectedImage.getKey());
                    $setSelection(selection);
                });
            }

            imageModal.show(formDefaults);
        });
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isImageNode);
    }
};

