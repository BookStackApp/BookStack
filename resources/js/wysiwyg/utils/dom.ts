export function el(tag: string, attrs: Record<string, string | null> = {}, children: (string | HTMLElement)[] = []): HTMLElement {
    const el = document.createElement(tag);
    const attrKeys = Object.keys(attrs);
    for (const attr of attrKeys) {
        if (attrs[attr] !== null) {
            el.setAttribute(attr, attrs[attr] as string);
        }
    }

    for (const child of children) {
        if (typeof child === 'string') {
            el.append(document.createTextNode(child));
        } else {
            el.append(child);
        }
    }

    return el;
}

export function htmlToDom(html: string): Document {
    const parser = new DOMParser();
    return parser.parseFromString(html, 'text/html');
}

export function formatSizeValue(size: number | string, defaultSuffix: string = 'px'): string {
    if (typeof size === 'number' || /^-?\d+$/.test(size)) {
        return `${size}${defaultSuffix}`;
    }

    return size;
}

export function sizeToPixels(size: string): number {
    if (/^-?\d+$/.test(size)) {
        return Number(size);
    }

    if (/^-?\d+\.\d+$/.test(size)) {
        return Math.round(Number(size));
    }

    if (/^-?\d+px\s*$/.test(size)) {
        return Number(size.trim().replace('px', ''));
    }

    return 0;
}

export type StyleMap = Map<string, string>;

/**
 * Creates a map from an element's styles.
 * Uses direct attribute value string handling since attempting to iterate
 * over .style will expand out any shorthand properties (like 'padding')
 * rather than being representative of the actual properties set.
 */
export function extractStyleMapFromElement(element: HTMLElement): StyleMap {
    const styleText= element.getAttribute('style') || '';
    return styleStringToStyleMap(styleText);
}

/**
 * Convert string-formatted styles into a StyleMap.
 */
export function styleStringToStyleMap(styleText: string): StyleMap {
    const map: StyleMap = new Map();

    const rules = styleText.split(';');
    for (const rule of rules) {
        const [name, value] = rule.split(':');
        if (!name || !value) {
            continue;
        }

        map.set(name.trim().toLowerCase(), value.trim());
    }

    return map;
}

/**
 * Convert a StyleMap into inline string style text.
 */
export function styleMapToStyleString(map: StyleMap): string {
    const parts = [];
    for (const [style, value] of map.entries()) {
        parts.push(`${style}:${value}`);
    }
    return parts.join(';');
}

export function setOrRemoveAttribute(element: HTMLElement, name: string, value: string|null|undefined) {
    if (value) {
        element.setAttribute(name, value);
    } else {
        element.removeAttribute(name);
    }
}