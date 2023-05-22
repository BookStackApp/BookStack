/**
 * Create a new element with the given attrs and children.
 * Children can be a string for text nodes or other elements.
 * @param {String} tagName
 * @param {Object<String, String>} attrs
 * @param {Element[]|String[]}children
 * @return {*}
 */
export function elem(tagName, attrs = {}, children = []) {
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
 * @param {String} selector
 * @param {Function<Element>} callback
 */
export function forEach(selector, callback) {
    const elements = document.querySelectorAll(selector);
    for (const element of elements) {
        callback(element);
    }
}

/**
 * Helper to listen to multiple DOM events
 * @param {Element} listenerElement
 * @param {Array<String>} events
 * @param {Function<Event>} callback
 */
export function onEvents(listenerElement, events, callback) {
    for (const eventName of events) {
        listenerElement.addEventListener(eventName, callback);
    }
}

/**
 * Helper to run an action when an element is selected.
 * A "select" is made to be accessible, So can be a click, space-press or enter-press.
 * @param {HTMLElement|Array} elements
 * @param {function} callback
 */
export function onSelect(elements, callback) {
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
 * Listen to enter press on the given element(s).
 * @param {HTMLElement|Array} elements
 * @param {function} callback
 */
export function onEnterPress(elements, callback) {
    if (!Array.isArray(elements)) {
        elements = [elements];
    }

    const listener = event => {
        if (event.key === 'Enter') {
            callback(event);
        }
    };

    elements.forEach(e => e.addEventListener('keypress', listener));
}

/**
 * Set a listener on an element for an event emitted by a child
 * matching the given childSelector param.
 * Used in a similar fashion to jQuery's $('listener').on('eventName', 'childSelector', callback)
 * @param {Element} listenerElement
 * @param {String} childSelector
 * @param {String} eventName
 * @param {Function} callback
 */
export function onChildEvent(listenerElement, childSelector, eventName, callback) {
    listenerElement.addEventListener(eventName, event => {
        const matchingChild = event.target.closest(childSelector);
        if (matchingChild) {
            callback.call(matchingChild, event, matchingChild);
        }
    });
}

/**
 * Look for elements that match the given selector and contain the given text.
 * Is case insensitive and returns the first result or null if nothing is found.
 * @param {String} selector
 * @param {String} text
 * @returns {Element}
 */
export function findText(selector, text) {
    const elements = document.querySelectorAll(selector);
    text = text.toLowerCase();
    for (const element of elements) {
        if (element.textContent.toLowerCase().includes(text)) {
            return element;
        }
    }
    return null;
}

/**
 * Show a loading indicator in the given element.
 * This will effectively clear the element.
 * @param {Element} element
 */
export function showLoading(element) {
    element.innerHTML = '<div class="loading-container"><div></div><div></div><div></div></div>';
}

/**
 * Get a loading element indicator element.
 * @returns {Element}
 */
export function getLoading() {
    const wrap = document.createElement('div');
    wrap.classList.add('loading-container');
    wrap.innerHTML = '<div></div><div></div><div></div>';
    return wrap;
}

/**
 * Remove any loading indicators within the given element.
 * @param {Element} element
 */
export function removeLoading(element) {
    const loadingEls = element.querySelectorAll('.loading-container');
    for (const el of loadingEls) {
        el.remove();
    }
}

/**
 * Convert the given html data into a live DOM element.
 * Initiates any components defined in the data.
 * @param {String} html
 * @returns {Element}
 */
export function htmlToDom(html) {
    const wrap = document.createElement('div');
    wrap.innerHTML = html;
    window.$components.init(wrap);
    return wrap.children[0];
}
