/**
 * Simple global events manager
 */
class Events {
    constructor() {
        this.listeners = {};
        this.stack = [];
    }

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

    listen(eventName, callback) {
        if (typeof this.listeners[eventName] === 'undefined') this.listeners[eventName] = [];
        this.listeners[eventName].push(callback);
        return this;
    }
}

export default Events;