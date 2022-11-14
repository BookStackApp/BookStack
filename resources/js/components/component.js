export class Component {

    /**
     * The registered name of the component.
     * @type {string}
     */
    $name = '';

    /**
     * The element that the component is registered upon.
     * @type {Element}
     */
    $el = null;

    /**
     * Mapping of referenced elements within the component.
     * @type {Object<string, Element>}
     */
    $refs = {};

    /**
     * Mapping of arrays of referenced elements within the component so multiple
     * references, sharing the same name, can be fetched.
     * @type {Object<string, Element[]>}
     */
    $manyRefs = {};

    /**
     * Options passed into this component.
     * @type {Object<String, String>}
     */
    $opts = {};

    /**
     * Component-specific setup methods.
     * Use this to assign local variables and run any initial setup or actions.
     */
    setup() {
        //
    }

    /**
     * Emit an event from this component.
     * Will be bubbled up from the dom element this is registered on, as a custom event
     * with the name `<elementName>-<eventName>`, with the provided data in the event detail.
     * @param {String} eventName
     * @param {Object} data
     */
    $emit(eventName, data = {}) {
        data.from = this;
        const componentName = this.$name;
        const event = new CustomEvent(`${componentName}-${eventName}`, {
            bubbles: true,
            detail: data
        });
        this.$el.dispatchEvent(event);
    }
}