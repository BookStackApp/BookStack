/**
 * Simple global events manager
 */
class Events {
    constructor() {
        this.listeners = {};
        this.stack = [];
    }

    /**
     * Emit a custom event for any handlers to pick-up.
     * @param {String} eventName
     * @param {*} eventData
     * @returns {Events}
     */
    emit(eventName, eventData) {
        this.stack.push({name: eventName, data: eventData});
        if (typeof this.listeners[eventName] === 'undefined') return this;
        let eventsToStart = this.listeners[eventName];
        for (let i = 0; i < eventsToStart.length; i++) {
            let event = eventsToStart[i];
            event(eventData);
        }
        return this;
    }

    /**
     * Listen to a custom event and run the given callback when that event occurs.
     * @param {String} eventName
     * @param {Function} callback
     * @returns {Events}
     */
    listen(eventName, callback) {
        if (typeof this.listeners[eventName] === 'undefined') this.listeners[eventName] = [];
        this.listeners[eventName].push(callback);
        return this;
    }

    /**
     * Emit an event for public use.
     * Sends the event via the native DOM event handling system.
     * @param {Element} targetElement
     * @param {String} eventName
     * @param {Object} eventData
     */
    emitPublic(targetElement, eventName, eventData) {
        const event = new CustomEvent(eventName, {
            detail: eventData,
            bubbles: true
        });
        targetElement.dispatchEvent(event);
    }
}

export default Events;