/**
 * Run the given callback against each element that matches the given selector.
 * @param {String} selector
 * @param {Function<Element>} callback
 */
export function forEach(selector, callback) {
    const elements = document.querySelectorAll(selector);
    for (let element of elements) {
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
    for (let eventName of events) {
        listenerElement.addEventListener(eventName, callback);
    }
}

/**
 * Helper to run an action when an element is selected.
 * A "select" is made to be accessible, So can be a click, space-press or enter-press.
 * @param listenerElement
 * @param callback
 */
export function onSelect(listenerElement, callback) {
    listenerElement.addEventListener('click', callback);
    listenerElement.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            callback(event);
        }
    });
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
    listenerElement.addEventListener(eventName, function(event) {
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
    for (let element of elements) {
        if (element.textContent.toLowerCase().includes(text)) {
            return element;
        }
    }
    return null;
}