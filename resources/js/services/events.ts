import {HttpError} from "./http";

export class EventManager {
    protected listeners: Record<string, ((data: any) => void)[]> = {};
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
    showValidationErrors(responseErr: {status?: number, data?: object}): void {
        if (!responseErr.status) return;
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
