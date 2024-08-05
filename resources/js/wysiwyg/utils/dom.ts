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