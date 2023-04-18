const listeners = {};
const stack = [];

/**
 * Emit a custom event for any handlers to pick-up.
 * @param {String} eventName
 * @param {*} eventData
 */
function emit(eventName, eventData) {
    stack.push({name: eventName, data: eventData});
    if (typeof listeners[eventName] === 'undefined') return this;
    const eventsToStart = listeners[eventName];
    for (let i = 0; i < eventsToStart.length; i++) {
        const event = eventsToStart[i];
        event(eventData);
    }
}

/**
 * Listen to a custom event and run the given callback when that event occurs.
 * @param {String} eventName
 * @param {Function} callback
 * @returns {Events}
 */
function listen(eventName, callback) {
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
function emitPublic(targetElement, eventName, eventData) {
    const event = new CustomEvent(eventName, {
        detail: eventData,
        bubbles: true,
    });
    targetElement.dispatchEvent(event);
}

/**
 * Notify of standard server-provided validation errors.
 * @param {Object} error
 */
function showValidationErrors(error) {
    if (!error.status) return;
    if (error.status === 422 && error.data) {
        const message = Object.values(error.data).flat().join('\n');
        emit('error', message);
    }
}

/**
 * Notify standard server-provided error messages.
 * @param {Object} error
 */
function showResponseError(error) {
    if (!error.status) return;
    if (error.status >= 400 && error.data && error.data.message) {
        emit('error', error.data.message);
    }
}

export default {
    emit,
    emitPublic,
    listen,
    success: msg => emit('success', msg),
    error: msg => emit('error', msg),
    showValidationErrors,
    showResponseError,
};
