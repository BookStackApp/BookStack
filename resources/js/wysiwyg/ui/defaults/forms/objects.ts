import {
    EditorFormDefinition,
    EditorFormField,
    EditorFormTabs,
    EditorSelectFormFieldDefinition
} from "../../framework/forms";
import {EditorUiContext} from "../../framework/core";
import {$createNodeSelection, $getSelection, $insertNodes, $setSelection} from "lexical";
import {$isImageNode, ImageNode} from "@lexical/rich-text/LexicalImageNode";
import {LinkNode} from "@lexical/link";
import {$createMediaNodeFromHtml, $createMediaNodeFromSrc, $isMediaNode, MediaNode} from "@lexical/rich-text/LexicalMediaNode";
import {$getNodeFromSelection, getLastSelection} from "../../../utils/selection";
import {EditorFormModal} from "../../framework/modals";
import {EditorActionField} from "../../framework/blocks/action-field";
import {EditorButton} from "../../framework/buttons";
import {showImageManager} from "../../../utils/images";
import searchImageIcon from "@icons/editor/image-search.svg";
import searchIcon from "@icons/search.svg";
import {showLinkSelector} from "../../../utils/links";
import {LinkField} from "../../framework/blocks/link-field";
import {insertOrUpdateLink} from "../../../utils/formats";
import {$isDetailsNode, DetailsNode} from "@lexical/rich-text/LexicalDetailsNode";

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
            const selection = getLastSelection(context.editor);
            const selectedImage = $getNodeFromSelection(selection, $isImageNode);
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

export function $showLinkForm(link: LinkNode|null, context: EditorUiContext) {
    const linkModal = context.manager.createModal('link');

    if (link) {
        const formDefaults: Record<string, string> = {
            url: link.getURL(),
            text: link.getTextContent(),
            title: link.getTitle() || '',
            target: link.getTarget() || '',
        }

        context.editor.update(() => {
            const selection = $createNodeSelection();
            selection.add(link.getKey());
            $setSelection(selection);
        });

        linkModal.show(formDefaults);
    } else {
        context.editor.getEditorState().read(() => {
            const selection = $getSelection();
            const text = selection?.getTextContent() || '';
            const formDefaults = {text};
            linkModal.show(formDefaults);
        });
    }
}

export const link: EditorFormDefinition = {
    submitText: 'Apply',
    async action(formData, context: EditorUiContext) {
        insertOrUpdateLink(context.editor, {
            url: formData.get('url')?.toString() || '',
            title: formData.get('title')?.toString() || '',
            target: formData.get('target')?.toString() || '',
            text: formData.get('text')?.toString() || '',
        });
        return true;
    },
    fields: [
        {
            build() {
                return new EditorActionField(
                    new LinkField(new EditorFormField({
                        label: 'URL',
                        name: 'url',
                        type: 'text',
                    })),
                    new EditorButton({
                        label: 'Browse links',
                        icon: searchIcon,
                        action(context: EditorUiContext) {
                            showLinkSelector(entity => {
                                const modal =  context.manager.getActiveModal('link');
                                if (modal) {
                                    modal.getForm().setValues({
                                        url: entity.link,
                                        text: entity.name,
                                        title: entity.name,
                                    });
                                }
                            });
                        }
                    }),
                );
            },
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

export function $showMediaForm(media: MediaNode|null, context: EditorUiContext): void {
    const mediaModal = context.manager.createModal('media');

    let formDefaults = {};
    if (media) {
        const nodeAttrs = media.getAttributes();
        const nodeDOM = media.exportDOM(context.editor).element;
        const nodeHtml = (nodeDOM instanceof HTMLElement) ? nodeDOM.outerHTML : '';

        formDefaults = {
            src: nodeAttrs.src || nodeAttrs.data || media.getSources()[0]?.src || '',
            width: nodeAttrs.width,
            height: nodeAttrs.height,
            embed: nodeHtml,

            // This is used so we can check for edits against the embed field on submit
            embed_check: nodeHtml,
        }
    }

    mediaModal.show(formDefaults);
}

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
        const embedCheck = (formData.get('embed_check') || '').toString().trim();
        if (embedCode && embedCode !== embedCheck) {
            context.editor.update(() => {
                const node = $createMediaNodeFromHtml(embedCode);
                if (selectedNode && node) {
                    selectedNode.replace(node)
                } else if (node) {
                    $insertNodes([node]);
                }
            });

            return true;
        }

        context.editor.update(() => {
            const src = (formData.get('src') || '').toString().trim();
            const height = (formData.get('height') || '').toString().trim();
            const width = (formData.get('width') || '').toString().trim();

            // Update existing
            if (selectedNode) {
                selectedNode.setSrc(src);
                selectedNode.setWidthAndHeight(width, height);
                context.manager.triggerFutureStateRefresh();
                return;
            }

            // Insert new
            const node = $createMediaNodeFromSrc(src);
            if (width || height) {
                node.setWidthAndHeight(width, height);
            }
            $insertNodes([node]);
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
                            {
                                label: '',
                                name: 'embed_check',
                                type: 'hidden',
                            },
                        ],
                    }
                ])
            }
        },
    ],
};

export function $showDetailsForm(details: DetailsNode|null, context: EditorUiContext) {
    const linkModal = context.manager.createModal('details');
    if (!details) {
        return;
    }

    linkModal.show({
        summary: details.getSummary()
    });
}

export const details: EditorFormDefinition = {
    submitText: 'Save',
    async action(formData, context: EditorUiContext) {
        context.editor.update(() => {
            const node = $getNodeFromSelection($getSelection(), $isDetailsNode);
            const summary = (formData.get('summary') || '').toString().trim();
            if ($isDetailsNode(node)) {
                node.setSummary(summary);
            }
        });

        return true;
    },
    fields: [
        {
            label: 'Toggle label',
            name: 'summary',
            type: 'text',
        },
    ],
};