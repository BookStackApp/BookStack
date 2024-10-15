/**
 * Check if the given param is a HTMLElement
 */
export function isHTMLElement(el: any): el is HTMLElement {
    return el instanceof HTMLElement;
}

/**
 * Create a new element with the given attrs and children.
 * Children can be a string for text nodes or other elements.
 */
export function elem(tagName: string, attrs: Record<string, string> = {}, children: Element[]|string[] = []): HTMLElement {
    const el = document.createElement(tagName);

    for (const [key, val] of Object.entries(attrs)) {
        if (val === null) {
            el.removeAttribute(key);
        } else {
            el.setAttribute(key, val);
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

/**
 * Run the given callback against each element that matches the given selector.
 */
export function forEach(selector: string, callback: (el: Element) => any) {
    const elements = document.querySelectorAll(selector);
    for (const element of elements) {
        callback(element);
    }
}

/**
 * Helper to listen to multiple DOM events
 */
export function onEvents(listenerElement: Element, events: string[], callback: (e: Event) => any): void {
    for (const eventName of events) {
        listenerElement.addEventListener(eventName, callback);
    }
}

/**
 * Helper to run an action when an element is selected.
 * A "select" is made to be accessible, So can be a click, space-press or enter-press.
 */
export function onSelect(elements: HTMLElement|HTMLElement[], callback: (e: Event) => any): void {
    if (!Array.isArray(elements)) {
        elements = [elements];
    }

    for (const listenerElement of elements) {
        listenerElement.addEventListener('click', callback);
        listenerElement.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                callback(event);
            }
        });
    }
}

/**
 * Listen to key press on the given element(s).
 */
function onKeyPress(key: string, elements: HTMLElement|HTMLElement[], callback: (e: KeyboardEvent) => any): void {
    if (!Array.isArray(elements)) {
        elements = [elements];
    }

    const listener = (event: KeyboardEvent) => {
        if (event.key === key) {
            callback(event);
        }
    };

    elements.forEach(e => e.addEventListener('keydown', listener));
}

/**
 * Listen to enter press on the given element(s).
 */
export function onEnterPress(elements: HTMLElement|HTMLElement[], callback: (e: KeyboardEvent) => any): void {
    onKeyPress('Enter', elements, callback);
}

/**
 * Listen to escape press on the given element(s).
 */
export function onEscapePress(elements: HTMLElement|HTMLElement[], callback: (e: KeyboardEvent) => any): void {
    onKeyPress('Escape', elements, callback);
}

/**
 * Set a listener on an element for an event emitted by a child
 * matching the given childSelector param.
 * Used in a similar fashion to jQuery's $('listener').on('eventName', 'childSelector', callback)
 */
export function onChildEvent(
    listenerElement: HTMLElement,
    childSelector: string,
    eventName: string,
    callback: (this: HTMLElement, e: Event, child: HTMLElement) => any
): void {
    listenerElement.addEventListener(eventName, (event: Event) => {
        const matchingChild = (event.target as HTMLElement|null)?.closest(childSelector) as HTMLElement;
        if (matchingChild) {
            callback.call(matchingChild, event, matchingChild);
        }
    });
}

/**
 * Look for elements that match the given selector and contain the given text.
 * Is case-insensitive and returns the first result or null if nothing is found.
 */
export function findText(selector: string, text: string): Element|null {
    const elements = document.querySelectorAll(selector);
    text = text.toLowerCase();
    for (const element of elements) {
        if ((element.textContent || '').toLowerCase().includes(text) && isHTMLElement(element)) {
            return element;
        }
    }
    return null;
}

/**
 * Show a loading indicator in the given element.
 * This will effectively clear the element.
 */
export function showLoading(element: HTMLElement): void {
    element.innerHTML = '<div class="loading-container"><div></div><div></div><div></div></div>';
}

/**
 * Get a loading element indicator element.
 */
export function getLoading(): HTMLElement {
    const wrap = document.createElement('div');
    wrap.classList.add('loading-container');
    wrap.innerHTML = '<div></div><div></div><div></div>';
    return wrap;
}

/**
 * Remove any loading indicators within the given element.
 */
export function removeLoading(element: HTMLElement): void {
    const loadingEls = element.querySelectorAll('.loading-container');
    for (const el of loadingEls) {
        el.remove();
    }
}

/**
 * Convert the given html data into a live DOM element.
 * Initiates any components defined in the data.
 */
export function htmlToDom(html: string): HTMLElement {
    const wrap = document.createElement('div');
    wrap.innerHTML = html;
    window.$components.init(wrap);
    const firstChild = wrap.children[0];
    if (!isHTMLElement(firstChild)) {
        throw new Error('Could not find child HTMLElement when creating DOM element from HTML');
    }

    return firstChild;
}
