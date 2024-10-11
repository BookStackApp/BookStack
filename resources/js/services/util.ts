/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing.
 * @attribution https://davidwalsh.name/javascript-debounce-function
 */
export function debounce(func: Function, waitMs: number, immediate: boolean): Function {
    let timeout: number|null = null;
    return function debouncedWrapper(this: any, ...args: any[]) {
        const context: any = this;
        const later = function debouncedTimeout() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        if (timeout) {
            clearTimeout(timeout);
        }
        timeout = window.setTimeout(later, waitMs);
        if (callNow) func.apply(context, args);
    };
}

function isDetailsElement(element: HTMLElement): element is HTMLDetailsElement {
    return element.nodeName === 'DETAILS';
}

/**
 * Scroll-to and highlight an element.
 */
export function scrollAndHighlightElement(element: HTMLElement): void {
    if (!element) return;

    // Open up parent <details> elements if within
    let parent = element;
    while (parent.parentElement) {
        parent = parent.parentElement;
        if (isDetailsElement(parent) && !parent.open) {
            parent.open = true;
        }
    }

    element.scrollIntoView({behavior: 'smooth'});

    const highlight = getComputedStyle(document.body).getPropertyValue('--color-link');
    element.style.outline = `2px dashed ${highlight}`;
    element.style.outlineOffset = '5px';
    element.style.removeProperty('transition');
    setTimeout(() => {
        element.style.transition = 'outline linear 3s';
        element.style.outline = '2px dashed rgba(0, 0, 0, 0)';
        const listener = () => {
            element.removeEventListener('transitionend', listener);
            element.style.removeProperty('transition');
            element.style.removeProperty('outline');
            element.style.removeProperty('outlineOffset');
        };
        element.addEventListener('transitionend', listener);
    }, 1000);
}

/**
 * Escape any HTML in the given 'unsafe' string.
 * Take from https://stackoverflow.com/a/6234804.
 */
export function escapeHtml(unsafe: string): string {
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Generate a random unique ID.
 */
export function uniqueId(): string {
    // eslint-disable-next-line no-bitwise
    const S4 = () => (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    return (`${S4() + S4()}-${S4()}-${S4()}-${S4()}-${S4()}${S4()}${S4()}`);
}

/**
 * Generate a random smaller unique ID.
 */
export function uniqueIdSmall(): string {
    // eslint-disable-next-line no-bitwise
    const S4 = () => (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    return S4();
}

/**
 * Create a promise that resolves after the given time.
 */
export function wait(timeMs: number): Promise<any> {
    return new Promise(res => {
        setTimeout(res, timeMs);
    });
}

/**
 * Generate a full URL from the given relative URL, using a base
 * URL defined in the head of the page.
 */
export function baseUrl(path: string): string {
    let targetPath = path;
    const baseUrlMeta = document.querySelector('meta[name="base-url"]');
    if (!baseUrlMeta) {
        throw new Error('Could not find expected base-url meta tag in document');
    }

    let basePath = baseUrlMeta.getAttribute('content') || '';
    if (basePath[basePath.length - 1] === '/') {
        basePath = basePath.slice(0, basePath.length - 1);
    }

    if (targetPath[0] === '/') {
        targetPath = targetPath.slice(1);
    }

    return `${basePath}/${targetPath}`;
}

/**
 * Get the current version of BookStack in use.
 * Grabs this from the version query used on app assets.
 */
function getVersion(): string {
    const styleLink = document.querySelector('link[href*="/dist/styles.css?version="]');
    if (!styleLink) {
        throw new Error('Could not find expected style link in document for version use');
    }

    const href = (styleLink.getAttribute('href') || '');
    return href.split('?version=').pop() || '';
}

/**
 * Perform a module import, Ensuring the import is fetched with the current
 * app version as a cache-breaker.
 */
export function importVersioned(moduleName: string): Promise<object> {
    const importPath = window.baseUrl(`dist/${moduleName}.js?version=${getVersion()}`);
    return import(importPath);
}