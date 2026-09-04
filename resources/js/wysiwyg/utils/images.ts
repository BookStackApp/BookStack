import {ImageManager} from "../../components";
import {$createImageNode, $isImageNode} from "@lexical/rich-text/LexicalImageNode";
import {$createLinkNode, $isLinkNode, LinkNode} from "@lexical/link";
import {$isElementNode, $isTextNode, LexicalNode} from "lexical";

export type EditorImageData = {
    id: string;
    url: string;
    thumbs?: {display: string};
    name: string;
};

export function showImageManager(callback: (image: EditorImageData) => any) {
    const imageManager: ImageManager = window.$components.first('image-manager') as ImageManager;
    imageManager.show((image: EditorImageData) => {
        callback(image);
    }, 'gallery');
}

export function $createLinkedImageNodeFromImageData(image: EditorImageData): LinkNode {
    const url = image.thumbs?.display || image.url;
    const linkNode = $createLinkNode(url, {target: '_blank'});
    const imageNode = $createImageNode(url, {
        alt: image.name
    });
    linkNode.append(imageNode);
    return linkNode;
}

/**
 * Check if the given image node represents a simple linked image, where no other content
 * exists within the link which wraps the image.
 */
export function $isLinkedImageNode(imageNode: LexicalNode): boolean {
    if (!$isImageNode(imageNode)) {
        return false;
    }

    const parent = imageNode.getParent();
    if (!$isLinkNode(parent)) {
        return false;
    }

    const linkChildren = parent.getChildren();
    const withContent = linkChildren.filter(child => {
        return child.getKey() !== imageNode.getKey() && (
            $isElementNode(child)
            || $isTextNode(child) && child.getTextContent().trim().length > 0
        );
    });

    return withContent.length === 0;
}

/**
 * Upload an image file to the server
 */
export async function uploadImageFile(file: File, pageId: string): Promise<EditorImageData> {
    if (file === null || file.type.indexOf('image') !== 0) {
        throw new Error('Not an image file');
    }

    const remoteFilename = file.name || `image-${Date.now()}.png`;
    const formData = new FormData();
    formData.append('file', file, remoteFilename);
    formData.append('uploaded_to', pageId);

    const resp = await window.$http.post('/images/gallery', formData);
    return resp.data as EditorImageData;
}