import {EditorBasicButtonDefinition, EditorButton, EditorButtonDefinition} from "../framework/buttons";
import {
    $createNodeSelection,
    $createParagraphNode, $createTextNode, $getRoot, $getSelection,
    $isParagraphNode, $isTextNode, $setSelection,
    BaseSelection, CAN_REDO_COMMAND, CAN_UNDO_COMMAND, COMMAND_PRIORITY_LOW, ElementNode, FORMAT_TEXT_COMMAND,
    LexicalNode,
    REDO_COMMAND, TextFormatType,
    UNDO_COMMAND
} from "lexical";
import {
    getNodeFromSelection, insertNewBlockNodeAtSelection,
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
import {$isLinkNode, LinkNode} from "@lexical/link";
import {EditorUiContext} from "../framework/core";
import {$isImageNode, ImageNode} from "../../nodes/image";
import {$createDetailsNode, $isDetailsNode} from "../../nodes/details";
import {getEditorContentAsHtml} from "../../actions";
import {$isListNode, insertList, ListNode, ListType, removeList} from "@lexical/list";
import undoIcon from "@icons/editor/undo.svg"
import redoIcon from "@icons/editor/redo.svg"
import boldIcon from "@icons/editor/bold.svg"
import italicIcon from "@icons/editor/italic.svg"
import underlinedIcon from "@icons/editor/underlined.svg"
import textColorIcon from "@icons/editor/text-color.svg";
import highlightIcon from "@icons/editor/highlighter.svg";
import strikethroughIcon from "@icons/editor/strikethrough.svg"
import superscriptIcon from "@icons/editor/superscript.svg"
import subscriptIcon from "@icons/editor/subscript.svg"
import codeIcon from "@icons/editor/code.svg"
import formatClearIcon from "@icons/editor/format-clear.svg"
import listBulletIcon from "@icons/editor/list-bullet.svg"
import listNumberedIcon from "@icons/editor/list-numbered.svg"
import listCheckIcon from "@icons/editor/list-check.svg"
import linkIcon from "@icons/editor/link.svg"
import unlinkIcon from "@icons/editor/unlink.svg"
import tableIcon from "@icons/editor/table.svg"
import imageIcon from "@icons/editor/image.svg"
import horizontalRuleIcon from "@icons/editor/horizontal-rule.svg"
import codeBlockIcon from "@icons/editor/code-block.svg"
import detailsIcon from "@icons/editor/details.svg"
import sourceIcon from "@icons/editor/source-view.svg"
import fullscreenIcon from "@icons/editor/fullscreen.svg"
import {$createHorizontalRuleNode, $isHorizontalRuleNode} from "../../nodes/horizontal-rule";
import {$createCodeBlockNode, $isCodeBlockNode, $openCodeEditorForNode, CodeBlockNode} from "../../nodes/code-block";

export const undo: EditorButtonDefinition = {
    label: 'Undo',
    icon: undoIcon,
    action(context: EditorUiContext) {
        context.editor.dispatchCommand(UNDO_COMMAND, undefined);
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    },
    setup(context: EditorUiContext, button: EditorButton) {
        button.toggleDisabled(true);

        context.editor.registerCommand(CAN_UNDO_COMMAND, (payload: boolean): boolean => {
            button.toggleDisabled(!payload)
            return false;
        }, COMMAND_PRIORITY_LOW);
    }
}

export const redo: EditorButtonDefinition = {
    label: 'Redo',
    icon: redoIcon,
    action(context: EditorUiContext) {
        context.editor.dispatchCommand(REDO_COMMAND, undefined);
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    },
    setup(context: EditorUiContext, button: EditorButton) {
        button.toggleDisabled(true);

        context.editor.registerCommand(CAN_REDO_COMMAND, (payload: boolean): boolean => {
            button.toggleDisabled(!payload)
            return false;
        }, COMMAND_PRIORITY_LOW);
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

function buildFormatButton(label: string, format: TextFormatType, icon: string): EditorButtonDefinition {
    return {
        label: label,
        icon,
        action(context: EditorUiContext) {
            context.editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
        },
        isActive(selection: BaseSelection|null): boolean {
            return selectionContainsTextFormat(selection, format);
        }
    };
}

export const bold: EditorButtonDefinition = buildFormatButton('Bold', 'bold', boldIcon);
export const italic: EditorButtonDefinition = buildFormatButton('Italic', 'italic', italicIcon);
export const underline: EditorButtonDefinition = buildFormatButton('Underline', 'underline', underlinedIcon);
export const textColor: EditorBasicButtonDefinition = {label: 'Text color', icon: textColorIcon};
export const highlightColor: EditorBasicButtonDefinition = {label: 'Highlight color', icon: highlightIcon};

export const strikethrough: EditorButtonDefinition = buildFormatButton('Strikethrough', 'strikethrough', strikethroughIcon);
export const superscript: EditorButtonDefinition = buildFormatButton('Superscript', 'superscript', superscriptIcon);
export const subscript: EditorButtonDefinition = buildFormatButton('Subscript', 'subscript', subscriptIcon);
export const code: EditorButtonDefinition = buildFormatButton('Inline Code', 'code', codeIcon);
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
            return selectionContainsNodeType(selection, (node: LexicalNode | null | undefined): boolean => {
                return $isListNode(node) && (node as ListNode).getListType() === type;
            });
        }
    };
}

export const bulletList: EditorButtonDefinition = buildListButton('Bullet list', 'bullet', listBulletIcon);
export const numberList: EditorButtonDefinition = buildListButton('Numbered list', 'number', listNumberedIcon);
export const taskList: EditorButtonDefinition = buildListButton('Task list', 'check', listCheckIcon);


export const link: EditorButtonDefinition = {
    label: 'Insert/edit link',
    icon: linkIcon,
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

export const unlink: EditorButtonDefinition = {
    label: 'Remove link',
    icon: unlinkIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const selection = context.lastSelection;
            const selectedLink = getNodeFromSelection(selection, $isLinkNode) as LinkNode|null;
            const selectionPoints = selection?.getStartEndPoints();

            if (selectedLink) {
                const newNode = $createTextNode(selectedLink.getTextContent());
                selectedLink.replace(newNode);
                if (selectionPoints?.length === 2) {
                    newNode.select(selectionPoints[0].offset, selectionPoints[1].offset);
                } else {
                    newNode.select();
                }
            }
        });
    },
    isActive(selection: BaseSelection|null): boolean {
        return false;
    }
};

export const table: EditorBasicButtonDefinition = {
    label: 'Table',
    icon: tableIcon,
};

export const image: EditorButtonDefinition = {
    label: 'Insert/Edit Image',
    icon: imageIcon,
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

export const horizontalRule: EditorButtonDefinition = {
    label: 'Insert horizontal line',
    icon: horizontalRuleIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            insertNewBlockNodeAtSelection($createHorizontalRuleNode(), false);
        });
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isHorizontalRuleNode);
    }
};

export const codeBlock: EditorButtonDefinition = {
    label: 'Insert code block',
    icon: codeBlockIcon,
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const selection = $getSelection();
            const codeBlock = getNodeFromSelection(selection, $isCodeBlockNode) as (CodeBlockNode|null);
            if (codeBlock === null) {
                context.editor.update(() => {
                    const codeBlock = $createCodeBlockNode();
                    codeBlock.setCode(selection?.getTextContent() || '');
                    insertNewBlockNodeAtSelection(codeBlock, true);
                    $openCodeEditorForNode(context.editor, codeBlock);
                    codeBlock.selectStart();
                });
            } else {
                $openCodeEditorForNode(context.editor, codeBlock);
            }
        });
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isCodeBlockNode);
    }
};

export const details: EditorButtonDefinition = {
    label: 'Insert collapsible block',
    icon: detailsIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const selection = $getSelection();
            const detailsNode = $createDetailsNode();
            const selectionNodes = selection?.getNodes() || [];
            const topLevels = selectionNodes.map(n => n.getTopLevelElement())
                .filter(n => n !== null) as ElementNode[];
            const uniqueTopLevels = [...new Set(topLevels)];

            if (uniqueTopLevels.length > 0) {
                uniqueTopLevels[0].insertAfter(detailsNode);
            } else {
                $getRoot().append(detailsNode);
            }

            for (const node of uniqueTopLevels) {
                detailsNode.append(node);
            }
        });
    },
    isActive(selection: BaseSelection|null): boolean {
        return selectionContainsNodeType(selection, $isDetailsNode);
    }
}

export const source: EditorButtonDefinition = {
    label: 'Source code',
    icon: sourceIcon,
    async action(context: EditorUiContext) {
        const modal = context.manager.createModal('source');
        const source = await getEditorContentAsHtml(context.editor);
        modal.show({source});
    },
    isActive() {
        return false;
    }
};

export const fullscreen: EditorButtonDefinition = {
    label: 'Fullscreen',
    icon: fullscreenIcon,
    async action(context: EditorUiContext, button: EditorButton) {
        const isFullScreen = context.containerDOM.classList.contains('fullscreen');
        context.containerDOM.classList.toggle('fullscreen', !isFullScreen);
        (context.containerDOM.closest('body') as HTMLElement).classList.toggle('editor-is-fullscreen', !isFullScreen);
        button.setActiveState(!isFullScreen);
    },
    isActive(selection, context: EditorUiContext) {
        return context.containerDOM.classList.contains('fullscreen');
    }
};