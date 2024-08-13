import {
    EditorFormDefinition,
    EditorFormField,
    EditorFormTabs,
    EditorSelectFormFieldDefinition
} from "../../framework/forms";
import {EditorUiContext} from "../../framework/core";
import {$createTextNode, $getSelection} from "lexical";
import {$isImageNode, ImageNode} from "../../../nodes/image";
import {$createLinkNode} from "@lexical/link";
import {$createMediaNodeFromHtml, $createMediaNodeFromSrc, $isMediaNode, MediaNode} from "../../../nodes/media";
import {$insertNodeToNearestRoot} from "@lexical/utils";
import {$getNodeFromSelection} from "../../../utils/selection";
import {EditorFormModal} from "../../framework/modals";
import {EditorActionField} from "../../framework/blocks/action-field";
import {EditorButton} from "../../framework/buttons";
import {showImageManager} from "../../../utils/images";
import searchImageIcon from "@icons/editor/image-search.svg";

export function $showImageForm(image: ImageNode, context: EditorUiContext) {
    const imageModal: EditorFormModal = context.manager.createModal('image');
    const height = image.getHeight();
    const width = image.getWidth();

    const formData = {
        src: image.getSrc(),
        alt: image.getAltText(),
        height: height === 0 ? '' : String(height),
        width: width === 0 ? '' : String(width),
    };

    imageModal.show(formData);
}

export const image: EditorFormDefinition = {
    submitText: 'Apply',
    async action(formData, context: EditorUiContext) {
        context.editor.update(() => {
            const selectedImage = $getNodeFromSelection(context.lastSelection, $isImageNode);
            if ($isImageNode(selectedImage)) {
                selectedImage.setSrc(formData.get('src')?.toString() || '');
                selectedImage.setAltText(formData.get('alt')?.toString() || '');

                selectedImage.setWidth(Number(formData.get('width')?.toString() || '0'));
                selectedImage.setHeight(Number(formData.get('height')?.toString() || '0'));
            }
        });
        return true;
    },
    fields: [
        {
            build() {
                return new EditorActionField(
                    new EditorFormField({
                        label: 'Source',
                        name: 'src',
                        type: 'text',
                    }),
                    new EditorButton({
                        label: 'Browse files',
                        icon: searchImageIcon,
                        action(context: EditorUiContext) {
                            showImageManager((image) => {
                                 const modal =  context.manager.getActiveModal('image');
                                 if (modal) {
                                     modal.getForm().setValues({
                                         src: image.thumbs?.display || image.url,
                                         alt: image.name,
                                     });
                                 }
                            });
                        }
                    }),
                );
            },
        },
        {
            label: 'Alternative description',
            name: 'alt',
            type: 'text',
        },
        {
            label: 'Width',
            name: 'width',
            type: 'text',
        },
        {
            label: 'Height',
            name: 'height',
            type: 'text',
        },
    ],
};

export const link: EditorFormDefinition = {
    submitText: 'Apply',
    async action(formData, context: EditorUiContext) {
        context.editor.update(() => {

            const selection = $getSelection();

            const linkNode = $createLinkNode(formData.get('url')?.toString() || '', {
                title: formData.get('title')?.toString() || '',
                target: formData.get('target')?.toString() || '',
            });
            linkNode.append($createTextNode(formData.get('text')?.toString() || ''));

            selection?.insertNodes([linkNode]);
        });
        return true;
    },
    fields: [
        {
            label: 'URL',
            name: 'url',
            type: 'text',
        },
        {
            label: 'Text to display',
            name: 'text',
            type: 'text',
        },
        {
            label: 'Title',
            name: 'title',
            type: 'text',
        },
        {
            label: 'Open link in...',
            name: 'target',
            type: 'select',
            valuesByLabel: {
                'Current window': '',
                'New window': '_blank',
            }
        } as EditorSelectFormFieldDefinition,
    ],
};

export const media: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        const selectedNode: MediaNode|null = await (new Promise((res, rej) => {
            context.editor.getEditorState().read(() => {
                const node = $getNodeFromSelection($getSelection(), $isMediaNode);
                res(node as MediaNode|null);
            });
        }));

        const embedCode = (formData.get('embed') || '').toString().trim();
        if (embedCode) {
            context.editor.update(() => {
                const node = $createMediaNodeFromHtml(embedCode);
                if (selectedNode && node) {
                    selectedNode.replace(node)
                } else if (node) {
                    $insertNodeToNearestRoot(node);
                }
            });

            return true;
        }

        context.editor.update(() => {
            const src = (formData.get('src') || '').toString().trim();
            const height = (formData.get('height') || '').toString().trim();
            const width = (formData.get('width') || '').toString().trim();

            const updateNode = selectedNode || $createMediaNodeFromSrc(src);
            updateNode.setSrc(src);
            updateNode.setWidthAndHeight(width, height);
            if (!selectedNode) {
                $insertNodeToNearestRoot(updateNode);
            }
        });

        return true;
    },
    fields: [
        {
            build() {
                return new EditorFormTabs([
                    {
                        label: 'General',
                        contents: [
                            {
                                label: 'Source',
                                name: 'src',
                                type: 'text',
                            },
                            {
                                label: 'Width',
                                name: 'width',
                                type: 'text',
                            },
                            {
                                label: 'Height',
                                name: 'height',
                                type: 'text',
                            },
                        ],
                    },
                    {
                        label: 'Embed',
                        contents: [
                            {
                                label: 'Paste your embed code below:',
                                name: 'embed',
                                type: 'textarea',
                            },
                        ],
                    }
                ])
            }
        },
    ],
};