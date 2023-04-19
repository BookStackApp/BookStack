const listeners = {};
const stack = [];

/**
 * Emit a custom event for any handlers to pick-up.
 * @param {String} eventName
 * @param {*} eventData
 */
export function emit(eventName, eventData) {
    stack.push({name: eventName, data: eventData});

    const listenersToRun = listeners[eventName] || [];
    for (const listener of listenersToRun) {
        listener(eventData);
    }
}

/**
 * Listen to a custom event and run the given callback when that event occurs.
 * @param {String} eventName
 * @param {Function} callback
 * @returns {Events}
 */
export function listen(eventName, callback) {
    if (typeof listeners[eventName] === 'undefined') listeners[eventName] = [];
    listeners[eventName].push(callback);
}

/**
 * Emit an event for public use.
 * Sends the event via the native DOM event handling system.
 * @param {Element} targetElement
 * @param {String} eventName
 * @param {Object} eventData
 */
export function emitPublic(targetElement, eventName, eventData) {
    const event = new CustomEvent(eventName, {
        detail: eventData,
        bubbles: true,
    });
    targetElement.dispatchEvent(event);
}

/**
 * Emit a success event with the provided message.
 * @param {String} message
 */
export function success(message) {
    emit('success', message);
}

/**
 * Emit an error event with the provided message.
 * @param {String} message
 */
export function error(message) {
    emit('error', message);
}

/**
 * Notify of standard server-provided validation errors.
 * @param {Object} responseErr
 */
export function showValidationErrors(responseErr) {
    if (!responseErr.status) return;
    if (responseErr.status === 422 && responseErr.data) {
        const message = Object.values(responseErr.data).flat().join('\n');
        error(message);
    }
}

/**
 * Notify standard server-provided error messages.
 * @param {Object} responseErr
 */
export function showResponseError(responseErr) {
    if (!responseErr.status) return;
    if (responseErr.status >= 400 && responseErr.data && responseErr.data.message) {
        error(responseErr.data.message);
    }
}
