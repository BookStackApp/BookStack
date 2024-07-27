import {
    DOMConversion,
    DOMConversionMap, DOMConversionOutput,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    SerializedElementNode, Spread
} from 'lexical';
import type {EditorConfig} from "lexical/LexicalEditor";
import {el} from "../helpers";

export type MediaNodeTag = 'iframe' | 'embed' | 'object' | 'video' | 'audio';
export type MediaNodeSource = {
    src: string;
    type: string;
};

export type SerializedMediaNode = Spread<{
    tag: MediaNodeTag;
    attributes: Record<string, string>;
    sources: MediaNodeSource[];
}, SerializedElementNode>

const attributeAllowList = [
    'id', 'width', 'height', 'style', 'title', 'name',
    'src', 'allow', 'allowfullscreen', 'loading', 'sandbox',
    'type', 'data', 'controls', 'autoplay', 'controlslist', 'loop',
    'muted', 'playsinline', 'poster', 'preload'
];

function filterAttributes(attributes: Record<string, string>): Record<string, string> {
    const filtered: Record<string, string> = {};
    for (const key of Object.keys(attributes)) {
        if (attributeAllowList.includes(key)) {
            filtered[key] = attributes[key];
        }
    }
    return filtered;
}

function domElementToNode(tag: MediaNodeTag, element: Element): MediaNode {
    const node = $createMediaNode(tag);

    const attributes: Record<string, string> = {};
    for (const attribute of element.attributes) {
        attributes[attribute.name] = attribute.value;
    }
    node.setAttributes(attributes);

    const sources: MediaNodeSource[] = [];
    if (tag === 'video' || tag === 'audio') {
        for (const child of element.children) {
            if (child.tagName === 'SOURCE') {
                const src = child.getAttribute('src');
                const type = child.getAttribute('type');
                if (src && type) {
                    sources.push({ src, type });
                }
            }
        }
        node.setSources(sources);
    }

    return node;
}

export class MediaNode extends ElementNode {

    __tag: MediaNodeTag;
    __attributes: Record<string, string> = {};
    __sources: MediaNodeSource[] = [];

    static getType() {
        return 'media';
    }

    static clone(node: MediaNode) {
        return new MediaNode(node.__tag, node.__key);
    }

    constructor(tag: MediaNodeTag, key?: string) {
        super(key);
        this.__tag = tag;
    }

    setTag(tag: MediaNodeTag) {
        const self = this.getWritable();
        self.__tag = tag;
    }

    getTag(): MediaNodeTag {
        const self = this.getLatest();
        return self.__tag;
    }

    setAttributes(attributes: Record<string, string>) {
        const self = this.getWritable();
        self.__attributes = filterAttributes(attributes);
    }

    getAttributes(): Record<string, string> {
        const self = this.getLatest();
        return self.__attributes;
    }

    setSources(sources: MediaNodeSource[]) {
        const self = this.getWritable();
        self.__sources = sources;
    }

    getSources(): MediaNodeSource[] {
        const self = this.getLatest();
        return self.__sources;
    }

    setSrc(src: string): void {
        const attrs = Object.assign({}, this.getAttributes());
        if (this.__tag ==='object') {
            attrs.data = src;
        } else {
            attrs.src = src;
        }
        this.setAttributes(attrs);
    }

    setWidthAndHeight(width: string, height: string): void {
        const attrs = Object.assign(
            {},
            this.getAttributes(),
            {width, height},
        );
        this.setAttributes(attrs);
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const sources = (this.__tag === 'video' || this.__tag === 'audio') ? this.__sources : [];
        const sourceEls = sources.map(source => el('source', source));

        return el(this.__tag, this.__attributes, sourceEls);
    }

    updateDOM(prevNode: unknown, dom: HTMLElement) {
        return true;
    }

    static importDOM(): DOMConversionMap|null {

        const buildConverter = (tag: MediaNodeTag) => {
            return (node: HTMLElement): DOMConversion|null => {
                return {
                    conversion: (element: HTMLElement): DOMConversionOutput|null => {
                        return {
                            node: domElementToNode(tag, element),
                        };
                    },
                    priority: 3,
                };
            };
        };

        return {
            iframe: buildConverter('iframe'),
            embed: buildConverter('embed'),
            object: buildConverter('object'),
            video: buildConverter('video'),
            audio: buildConverter('audio'),
        };
    }

    exportJSON(): SerializedMediaNode {
        return {
            ...super.exportJSON(),
            type: 'media',
            version: 1,
            tag: this.__tag,
            attributes: this.__attributes,
            sources: this.__sources,
        };
    }

    static importJSON(serializedNode: SerializedMediaNode): MediaNode {
        return $createMediaNode(serializedNode.tag);
    }

}

export function $createMediaNode(tag: MediaNodeTag) {
    return new MediaNode(tag);
}

export function $createMediaNodeFromHtml(html: string): MediaNode | null {
    const parser = new DOMParser();
    const doc = parser.parseFromString(`<body>${html}</body>`, 'text/html');

    const el = doc.body.children[0];
    if (!el) {
        return null;
    }

    const tag = el.tagName.toLowerCase();
    const validTypes = ['embed', 'iframe', 'video', 'audio', 'object'];
    if (!validTypes.includes(tag)) {
        return null;
    }

    return domElementToNode(tag as MediaNodeTag, el);
}

const videoExtensions = ['mp4', 'mpeg', 'm4v', 'm4p', 'mov'];
const audioExtensions = ['3gp', 'aac', 'flac', 'mp3', 'm4a', 'ogg', 'wav', 'webm'];
const iframeExtensions = ['html', 'htm', 'php', 'asp', 'aspx'];

export function $createMediaNodeFromSrc(src: string): MediaNode {
    let nodeTag: MediaNodeTag = 'iframe';
    const srcEnd = src.split('?')[0].split('/').pop() || '';
    const extension = (srcEnd.split('.').pop() || '').toLowerCase();
    if (videoExtensions.includes(extension)) {
        nodeTag = 'video';
    } else if (audioExtensions.includes(extension)) {
        nodeTag = 'audio';
    } else if (extension && !iframeExtensions.includes(extension)) {
        nodeTag = 'embed';
    }

    return new MediaNode(nodeTag);
}

export function $isMediaNode(node: LexicalNode | null | undefined) {
    return node instanceof MediaNode;
}

export function $isMediaNodeOfTag(node: LexicalNode | null | undefined, tag: MediaNodeTag) {
    return node instanceof MediaNode && (node as MediaNode).getTag() === tag;
}