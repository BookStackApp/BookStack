import {ImageManager} from "../../components";
import {$createImageNode} from "../nodes/image";
import {$createLinkNode, LinkNode} from "@lexical/link";

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