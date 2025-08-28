import {EditorButtonDefinition} from "../../framework/buttons";
import linkIcon from "@icons/editor/link.svg";
import {EditorUiContext} from "../../framework/core";
import {
    $getRoot,
    $getSelection, $insertNodes,
    BaseSelection,
    ElementNode
} from "lexical";
import {$isLinkNode, LinkNode} from "@lexical/link";
import unlinkIcon from "@icons/editor/unlink.svg";
import imageIcon from "@icons/editor/image.svg";
import {$isImageNode, ImageNode} from "@lexical/rich-text/LexicalImageNode";
import horizontalRuleIcon from "@icons/editor/horizontal-rule.svg";
import {$createHorizontalRuleNode, $isHorizontalRuleNode} from "@lexical/rich-text/LexicalHorizontalRuleNode";
import codeBlockIcon from "@icons/editor/code-block.svg";
import {$isCodeBlockNode} from "@lexical/rich-text/LexicalCodeBlockNode";
import editIcon from "@icons/edit.svg";
import diagramIcon from "@icons/editor/diagram.svg";
import {$createDiagramNode, DiagramNode} from "@lexical/rich-text/LexicalDiagramNode";
import detailsIcon from "@icons/editor/details.svg";
import detailsToggleIcon from "@icons/editor/details-toggle.svg";
import tableDeleteIcon from "@icons/editor/table-delete.svg";
import tagIcon from "@icons/tag.svg";
import mediaIcon from "@icons/editor/media.svg";
import {$createDetailsNode, $isDetailsNode} from "@lexical/rich-text/LexicalDetailsNode";
import {$isMediaNode, MediaNode} from "@lexical/rich-text/LexicalMediaNode";
import {
    $getNodeFromSelection,
    $insertNewBlockNodeAtSelection,
    $selectionContainsNodeType, getLastSelection
} from "../../../utils/selection";
import {$isDiagramNode, $openDrawingEditorForNode, showDiagramManagerForInsert} from "../../../utils/diagrams";
import {$createLinkedImageNodeFromImageData, showImageManager} from "../../../utils/images";
import {$showDetailsForm, $showImageForm, $showLinkForm, $showMediaForm} from "../forms/objects";
import {formatCodeBlock} from "../../../utils/formats";
import {$unwrapDetailsNode} from "../../../utils/details";

export const link: EditorButtonDefinition = {
    label: 'Insert/edit link',
    icon: linkIcon,
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const selectedLink = $getNodeFromSelection($getSelection(), $isLinkNode) as LinkNode | null;
            $showLinkForm(selectedLink, context);
        });
    },
    isActive(selection: BaseSelection | null): boolean {
        return $selectionContainsNodeType(selection, $isLinkNode);
    }
};

export const unlink: EditorButtonDefinition = {
    label: 'Remove link',
    icon: unlinkIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const selection = getLastSelection(context.editor);
            const selectedLink = $getNodeFromSelection(selection, $isLinkNode) as LinkNode | null;

            if (selectedLink) {
                const contents = selectedLink.getChildren().reverse();
                for (const child of contents) {
                    selectedLink.insertAfter(child);
                }
                selectedLink.remove();

                contents[contents.length - 1].selectStart();

                context.manager.triggerFutureStateRefresh();
            }
        });
    },
    isActive(selection: BaseSelection | null): boolean {
        return false;
    }
};


export const image: EditorButtonDefinition = {
    label: 'Insert/Edit Image',
    icon: imageIcon,
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const selection = getLastSelection(context.editor);
            const selectedImage = $getNodeFromSelection(selection, $isImageNode) as ImageNode | null;
            if (selectedImage) {
                $showImageForm(selectedImage, context);
                return;
            }

            showImageManager((image) => {
                context.editor.update(() => {
                    const link = $createLinkedImageNodeFromImageData(image);
                    $insertNodes([link]);
                    link.select();
                });
            })
        });
    },
    isActive(selection: BaseSelection | null): boolean {
        return $selectionContainsNodeType(selection, $isImageNode);
    }
};

export const horizontalRule: EditorButtonDefinition = {
    label: 'Insert horizontal line',
    icon: horizontalRuleIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            $insertNewBlockNodeAtSelection($createHorizontalRuleNode(), false);
        });
    },
    isActive(selection: BaseSelection | null): boolean {
        return $selectionContainsNodeType(selection, $isHorizontalRuleNode);
    }
};

export const codeBlock: EditorButtonDefinition = {
    label: 'Insert code block',
    icon: codeBlockIcon,
    action(context: EditorUiContext) {
        formatCodeBlock(context.editor);
    },
    isActive(selection: BaseSelection | null): boolean {
        return $selectionContainsNodeType(selection, $isCodeBlockNode);
    }
};

export const editCodeBlock: EditorButtonDefinition = Object.assign({}, codeBlock, {
    label: 'Edit code block',
    icon: editIcon,
});

export const diagram: EditorButtonDefinition = {
    label: 'Insert/edit drawing',
    icon: diagramIcon,
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const selection = getLastSelection(context.editor);
            const diagramNode = $getNodeFromSelection(selection, $isDiagramNode) as (DiagramNode | null);
            if (diagramNode === null) {
                context.editor.update(() => {
                    const diagram = $createDiagramNode();
                    $insertNewBlockNodeAtSelection(diagram, true);
                    $openDrawingEditorForNode(context, diagram);
                    diagram.selectStart();
                });
            } else {
                $openDrawingEditorForNode(context, diagramNode);
            }
        });
    },
    isActive(selection: BaseSelection | null): boolean {
        return $selectionContainsNodeType(selection, $isDiagramNode);
    }
};

export const diagramManager: EditorButtonDefinition = {
    label: 'Drawing manager',
    action(context: EditorUiContext) {
        showDiagramManagerForInsert(context);
    },
    isActive(): boolean {
        return false;
    }
};

export const media: EditorButtonDefinition = {
    label: 'Insert/edit media',
    icon: mediaIcon,
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const selection = $getSelection();
            const selectedNode = $getNodeFromSelection(selection, $isMediaNode) as MediaNode | null;

            $showMediaForm(selectedNode, context);
        });
    },
    isActive(selection: BaseSelection | null): boolean {
        return $selectionContainsNodeType(selection, $isMediaNode);
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

            detailsNode.setOpen(true);

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
    isActive(selection: BaseSelection | null): boolean {
        return $selectionContainsNodeType(selection, $isDetailsNode);
    }
}

export const detailsEditLabel: EditorButtonDefinition = {
    label: 'Edit label',
    icon: tagIcon,
    action(context: EditorUiContext) {
        context.editor.getEditorState().read(() => {
            const details = $getNodeFromSelection($getSelection(), $isDetailsNode);
            if ($isDetailsNode(details)) {
                $showDetailsForm(details, context);
            }
        })
    },
    isActive(selection: BaseSelection | null): boolean {
        return false;
    }
}

export const detailsToggle: EditorButtonDefinition = {
    label: 'Toggle open/closed',
    icon: detailsToggleIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const details = $getNodeFromSelection($getSelection(), $isDetailsNode);
            if ($isDetailsNode(details)) {
                details.setOpen(!details.getOpen());
                context.manager.triggerLayoutUpdate();
            }
        })
    },
    isActive(selection: BaseSelection | null): boolean {
        return false;
    }
}

export const detailsUnwrap: EditorButtonDefinition = {
    label: 'Unwrap',
    icon: tableDeleteIcon,
    action(context: EditorUiContext) {
        context.editor.update(() => {
            const details = $getNodeFromSelection($getSelection(), $isDetailsNode);
            if ($isDetailsNode(details)) {
                $unwrapDetailsNode(details);
                context.manager.triggerLayoutUpdate();
            }
        })
    },
    isActive(selection: BaseSelection | null): boolean {
        return false;
    }
}