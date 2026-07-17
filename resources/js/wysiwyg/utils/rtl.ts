/**
 * Create a viewport relative rect for an element which provides
 * logical property support.
 */
export class RTLRect {
    protected rect: DOMRect;
    protected isRTL: boolean;

    constructor(element: HTMLElement, isRTL = false) {
        this.rect = element.getBoundingClientRect();
        this.isRTL = isRTL;
    }

    get blockStart(): number {
        return this.rect.top;
    }

    get inlineStart(): number {
        if (!this.isRTL) {
            return this.rect.left;
        }

        return window.innerWidth - this.rect.right;
    }

    get blockEnd(): number {
        return this.rect.bottom;
    }

    get inlineEnd(): number {
        if (!this.isRTL) {
            return this.rect.right;
        }

        return window.innerWidth - this.rect.left;
    }

    get width(): number {
        return this.rect.width;
    }

    get height(): number {
        return this.rect.height;
    }
}

export function getViewportRect(): RTLRect {
    return new RTLRect(document.documentElement, false);
}
