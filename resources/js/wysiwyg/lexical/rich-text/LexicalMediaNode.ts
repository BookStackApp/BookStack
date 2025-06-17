import {
    DOMConversion,
    DOMConversionMap, DOMConversionOutput, DOMExportOutput,
    ElementNode,
    LexicalEditor,
    LexicalNode,
    Spread
} from 'lexical';
import type {EditorConfig} from "lexical/LexicalEditor";

import {el, setOrRemoveAttribute, sizeToPixels, styleMapToStyleString, styleStringToStyleMap} from "../../utils/dom";
import {
    CommonBlockAlignment, deserializeCommonBlockNode,
    setCommonBlockPropsFromElement,
    updateElementWithCommonBlockProps
} from "lexical/nodes/common";
import {SerializedCommonBlockNode} from "lexical/nodes/CommonBlockNode";

export type MediaNodeTag = 'iframe' | 'embed' | 'object' | 'video' | 'audio';
export type MediaNodeSource = {
    src: string;
    type: string;
};

export type SerializedMediaNode = Spread<{
    tag: MediaNodeTag;
    attributes: Record<string, string>;
    sources: MediaNodeSource[];
}, SerializedCommonBlockNode>

const attributeAllowList = [
    'width', 'height', 'style', 'title', 'name',
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

function removeStyleFromAttributes(attributes: Record<string, string>, styleName: string): Record<string, string> {
    const attrCopy = Object.assign({}, attributes);
    if (!attributes.style) {
        return attrCopy;
    }

    const map = styleStringToStyleMap(attributes.style);
    map.delete(styleName);

    attrCopy.style = styleMapToStyleString(map);
    return attrCopy;
}

function domElementToNode(tag: MediaNodeTag, element: HTMLElement): MediaNode {
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

    setCommonBlockPropsFromElement(element, node);

    return node;
}

export class MediaNode extends ElementNode {
    __id: string = '';
    __alignment: CommonBlockAlignment = '';
    __tag: MediaNodeTag;
    __attributes: Record<string, string> = {};
    __sources: MediaNodeSource[] = [];
    __inset: number = 0;

    static getType() {
        return 'media';
    }

    static clone(node: MediaNode) {
        const newNode = new MediaNode(node.__tag, node.__key);
        newNode.__attributes = Object.assign({}, node.__attributes);
        newNode.__sources = node.__sources.map(s => Object.assign({}, s));
        newNode.__id = node.__id;
        newNode.__alignment = node.__alignment;
        newNode.__inset = node.__inset;
        return newNode;
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
        return Object.assign({}, self.__attributes);
    }

    setSources(sources: MediaNodeSource[]) {
        const self = this.getWritable();
        self.__sources = sources;
    }

    getSources(): MediaNodeSource[] {
        const self = this.getLatest();
        return self.__sources.map(s => Object.assign({}, s))
    }

    setSrc(src: string): void {
        const attrs = this.getAttributes();
        const sources = this.getSources();

        if (this.__tag ==='object') {
            attrs.data = src;
        } if (this.__tag === 'video' && sources.length > 0) {
            sources[0].src = src;
            delete attrs.src;
            if (sources.length > 1) {
                sources.splice(1, sources.length - 1);
            }
            this.setSources(sources);
        } else {
            attrs.src = src;
        }

        this.setAttributes(attrs);
    }

    setWidthAndHeight(width: string, height: string): void {
        let attrs: Record<string, string> = Object.assign(
            this.getAttributes(),
            {width, height},
        );

        attrs = removeStyleFromAttributes(attrs, 'width');
        attrs = removeStyleFromAttributes(attrs, 'height');
        this.setAttributes(attrs);
    }

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    setAlignment(alignment: CommonBlockAlignment) {
        const self = this.getWritable();
        self.__alignment = alignment;
    }

    getAlignment(): CommonBlockAlignment {
        const self = this.getLatest();
        return self.__alignment;
    }

    setInset(size: number) {
        const self = this.getWritable();
        self.__inset = size;
    }

    getInset(): number {
        const self = this.getLatest();
        return self.__inset;
    }

    setHeight(height: number): void {
        if (!height) {
            return;
        }

        const attrs = Object.assign(this.getAttributes(), {height});
        this.setAttributes(removeStyleFromAttributes(attrs, 'height'));
    }

    getHeight(): number {
        const self = this.getLatest();
        return sizeToPixels(self.__attributes.height || '0');
    }

    setWidth(width: number): void {
        const existingAttrs = this.getAttributes();
        const attrs: Record<string, string> = Object.assign(existingAttrs, {width});
        this.setAttributes(removeStyleFromAttributes(attrs, 'width'));
    }

    getWidth(): number {
        const self = this.getLatest();
        return sizeToPixels(self.__attributes.width || '0');
    }

    isInline(): boolean {
        return true;
    }

    isParentRequired(): boolean {
        return true;
    }

    createInnerDOM() {
        const sources = (this.__tag === 'video' || this.__tag === 'audio') ? this.__sources : [];
        const sourceEls = sources.map(source => el('source', source));
        const element = el(this.__tag, this.__attributes, sourceEls);
        updateElementWithCommonBlockProps(element, this);
        return element;
    }

    createDOM(_config: EditorConfig, _editor: LexicalEditor) {
        const media = this.createInnerDOM();
        return el('span', {
            class: media.className + ' editor-media-wrap',
        }, [media]);
    }

    updateDOM(prevNode: MediaNode, dom: HTMLElement): boolean {
        if (prevNode.__tag !== this.__tag) {
            return true;
        }

        if (JSON.stringify(prevNode.__sources) !== JSON.stringify(this.__sources)) {
            return true;
        }

        if (JSON.stringify(prevNode.__attributes) !== JSON.stringify(this.__attributes)) {
            return true;
        }

        const mediaEl = dom.firstElementChild as HTMLElement;

        if (prevNode.__id !== this.__id) {
            setOrRemoveAttribute(mediaEl, 'id', this.__id);
        }

        if (prevNode.__alignment !== this.__alignment) {
            if (prevNode.__alignment) {
                dom.classList.remove(`align-${prevNode.__alignment}`);
                mediaEl.classList.remove(`align-${prevNode.__alignment}`);
            }
            if (this.__alignment) {
                dom.classList.add(`align-${this.__alignment}`);
                mediaEl.classList.add(`align-${this.__alignment}`);
            }
        }

        if (prevNode.__inset !== this.__inset) {
            dom.style.paddingLeft = `${this.__inset}px`;
        }

        return false;
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

    exportDOM(editor: LexicalEditor): DOMExportOutput {
        const element = this.createInnerDOM();
        return { element };
    }

    exportJSON(): SerializedMediaNode {
        return {
            ...super.exportJSON(),
            type: 'media',
            version: 1,
            id: this.__id,
            alignment: this.__alignment,
            inset: this.__inset,
            tag: this.__tag,
            attributes: this.__attributes,
            sources: this.__sources,
        };
    }

    static importJSON(serializedNode: SerializedMediaNode): MediaNode {
        const node = $createMediaNode(serializedNode.tag);
        deserializeCommonBlockNode(serializedNode, node);
        return node;
    }

}

export function $createMediaNode(tag: MediaNodeTag) {
    return new MediaNode(tag);
}

export function $createMediaNodeFromHtml(html: string): MediaNode | null {
    const parser = new DOMParser();
    const doc = parser.parseFromString(`<body>${html}</body>`, 'text/html');

    const el = doc.body.children[0];
    if (!(el instanceof HTMLElement)) {
        return null;
    }

    const tag = el.tagName.toLowerCase();
    const validTypes = ['embed', 'iframe', 'video', 'audio', 'object'];
    if (!validTypes.includes(tag)) {
        return null;
    }

    return domElementToNode(tag as MediaNodeTag, el);
}

interface UrlPattern {
    readonly regex: RegExp;
    readonly w: number;
    readonly h: number;
    readonly url: string;
}

/**
 * These patterns originate from the tinymce/tinymce project.
 * https://github.com/tinymce/tinymce/blob/release/6.6/modules/tinymce/src/plugins/media/main/ts/core/UrlPatterns.ts
 * License: MIT Copyright (c) 2022 Ephox Corporation DBA Tiny Technologies, Inc.
 * License Link: https://github.com/tinymce/tinymce/blob/584a150679669859a528828e5d2910a083b1d911/LICENSE.TXT
 */
const urlPatterns: UrlPattern[] = [
    {
        regex: /.*?youtu\.be\/([\w\-_\?&=.]+)/i,
        w: 560, h: 314,
        url: 'https://www.youtube.com/embed/$1',
    },
    {
        regex: /.*youtube\.com(.+)v=([^&]+)(&([a-z0-9&=\-_]+))?.*/i,
        w: 560, h: 314,
        url: 'https://www.youtube.com/embed/$2?$4',
    },
    {
        regex: /.*youtube.com\/embed\/([a-z0-9\?&=\-_]+).*/i,
        w: 560, h: 314,
        url: 'https://www.youtube.com/embed/$1',
    },
];

const videoExtensions = ['mp4', 'mpeg', 'm4v', 'm4p', 'mov'];
const audioExtensions = ['3gp', 'aac', 'flac', 'mp3', 'm4a', 'ogg', 'wav', 'webm'];
const iframeExtensions = ['html', 'htm', 'php', 'asp', 'aspx', ''];

export function $createMediaNodeFromSrc(src: string): MediaNode {

    for (const pattern of urlPatterns) {
        const match = src.match(pattern.regex);
        if (match) {
            const newSrc = src.replace(pattern.regex, pattern.url);
            const node = new MediaNode('iframe');
            node.setSrc(newSrc);
            node.setHeight(pattern.h);
            node.setWidth(pattern.w);
            return node;
        }
    }

    let nodeTag: MediaNodeTag = 'iframe';
    const srcEnd = src.split('?')[0].split('/').pop() || '';
    const srcEndSplit = srcEnd.split('.');
    const extension = (srcEndSplit.length > 1 ? srcEndSplit[srcEndSplit.length - 1] : '').toLowerCase();
    if (videoExtensions.includes(extension)) {
        nodeTag = 'video';
    } else if (audioExtensions.includes(extension)) {
        nodeTag = 'audio';
    } else if (extension && !iframeExtensions.includes(extension)) {
        nodeTag = 'embed';
    }

    const node = new MediaNode(nodeTag);
    node.setSrc(src);
    return node;
}

export function $isMediaNode(node: LexicalNode | null | undefined): node is MediaNode {
    return node instanceof MediaNode;
}

export function $isMediaNodeOfTag(node: LexicalNode | null | undefined, tag: MediaNodeTag): boolean {
    return node instanceof MediaNode && (node as MediaNode).getTag() === tag;
}