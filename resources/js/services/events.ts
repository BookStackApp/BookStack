import {HttpError} from "./http";

type Listener = (data: any) => void;

export class EventManager {
    protected listeners: Record<string, Listener[]> = {};
    protected stack: {name: string, data: {}}[] = [];

    /**
     * Emit a custom event for any handlers to pick-up.
     */
    emit(eventName: string, eventData: {} = {}): void {
        this.stack.push({name: eventName, data: eventData});

        const listenersToRun = this.listeners[eventName] || [];
        for (const listener of listenersToRun) {
            listener(eventData);
        }
    }

    /**
     * Listen to a custom event and run the given callback when that event occurs.
     */
    listen<T>(eventName: string, callback: (data: T) => void): void {
        if (typeof this.listeners[eventName] === 'undefined') this.listeners[eventName] = [];
        this.listeners[eventName].push(callback);
    }

    /**
     * Remove an event listener which is using the given callback for the given event name.
     */
    remove(eventName: string, callback: Listener): void {
        const listeners = this.listeners[eventName] || [];
        const index = listeners.indexOf(callback);
        if (index !== -1) {
            listeners.splice(index, 1);
        }
    }

    /**
     * Emit an event for public use.
     * Sends the event via the native DOM event handling system.
     */
    emitPublic(targetElement: Element, eventName: string, eventData: {}): void {
        const event = new CustomEvent(eventName, {
            detail: eventData,
            bubbles: true,
        });
        targetElement.dispatchEvent(event);
    }

    /**
     * Emit a success event with the provided message.
     */
    success(message: string): void {
        this.emit('success', message);
    }

    /**
     * Emit an error event with the provided message.
     */
    error(message: string): void {
        this.emit('error', message);
    }

    /**
     * Notify of standard server-provided validation errors.
     */
    showValidationErrors(responseErr: HttpError): void {
        if (responseErr.status === 422 && responseErr.data) {
            const message = Object.values(responseErr.data).flat().join('\n');
            this.error(message);
        }
    }

    /**
     * Notify standard server-provided error messages.
     */
    showResponseError(responseErr: {status?: number, data?: Record<any, any>}|HttpError): void {
        if (!responseErr.status) return;
        if (responseErr.status >= 400 && typeof responseErr.data === 'object' && responseErr.data.message) {
            this.error(responseErr.data.message);
        }
    }
}
