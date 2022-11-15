const components = {};
const componentMap = {};

/**
 * Initialize a component instance on the given dom element.
 * @param {String} name
 * @param {Element} element
 */
function initComponent(name, element) {
    /** @type {Function<Component>|undefined} **/
    const componentModel = componentMap[name];
    if (componentModel === undefined) return;

    // Create our component instance
    /** @type {Component} **/
    let instance;
    try {
        instance = new componentModel();
        instance.$name = name;
        instance.$el = element;
        const allRefs = parseRefs(name, element);
        instance.$refs = allRefs.refs;
        instance.$manyRefs = allRefs.manyRefs;
        instance.$opts = parseOpts(name, element);
        instance.setup();
    } catch (e) {
        console.error('Failed to create component', e, name, element);
    }

    // Add to global listing
    if (typeof components[name] === "undefined") {
        components[name] = [];
    }
    components[name].push(instance);

    // Add to element listing
    if (typeof element.components === 'undefined') {
        element.components = {};
    }
    element.components[name] = instance;
}

/**
 * Parse out the element references within the given element
 * for the given component name.
 * @param {String} name
 * @param {Element} element
 */
function parseRefs(name, element) {
    const refs = {};
    const manyRefs = {};

    const prefix = `${name}@`
    const selector = `[refs*="${prefix}"]`;
    const refElems = [...element.querySelectorAll(selector)];
    if (element.matches(selector)) {
        refElems.push(element);
    }

    for (const el of refElems) {
        const refNames = el.getAttribute('refs')
            .split(' ')
            .filter(str => str.startsWith(prefix))
            .map(str => str.replace(prefix, ''))
            .map(kebabToCamel);
        for (const ref of refNames) {
            refs[ref] = el;
            if (typeof manyRefs[ref] === 'undefined') {
                manyRefs[ref] = [];
            }
            manyRefs[ref].push(el);
        }
    }
    return {refs, manyRefs};
}

/**
 * Parse out the element component options.
 * @param {String} name
 * @param {Element} element
 * @return {Object<String, String>}
 */
function parseOpts(name, element) {
    const opts = {};
    const prefix = `option:${name}:`;
    for (const {name, value} of element.attributes) {
        if (name.startsWith(prefix)) {
            const optName = name.replace(prefix, '');
            opts[kebabToCamel(optName)] = value || '';
        }
    }
    return opts;
}

/**
 * Convert a kebab-case string to camelCase
 * @param {String} kebab
 * @returns {string}
 */
function kebabToCamel(kebab) {
    const ucFirst = (word) => word.slice(0,1).toUpperCase() + word.slice(1);
    const words = kebab.split('-');
    return words[0] + words.slice(1).map(ucFirst).join('');
}

/**
 * Initialize all components found within the given element.
 * @param {Element|Document} parentElement
 */
export function init(parentElement = document) {
    const componentElems = parentElement.querySelectorAll(`[component],[components]`);

    for (const el of componentElems) {
        const componentNames = `${el.getAttribute('component') || ''} ${(el.getAttribute('components'))}`.toLowerCase().split(' ').filter(Boolean);
        for (const name of componentNames) {
            initComponent(name, el);
        }
    }
}

/**
 * Register the given component mapping into the component system.
 * @param {Object<String, ObjectConstructor<Component>>} mapping
 */
export function register(mapping) {
    const keys = Object.keys(mapping);
    for (const key of keys) {
        componentMap[camelToKebab(key)] = mapping[key];
    }
}

/**
 * Get the first component of the given name.
 * @param {String} name
 * @returns {Component|null}
 */
export function first(name) {
    return (components[name] || [null])[0];
}

/**
 * Get all the components of the given name.
 * @param {String} name
 * @returns {Component[]}
 */
export function get(name = '') {
    return components[name] || [];
}

function camelToKebab(camelStr) {
    return camelStr.replace(/[A-Z]/g, (str, offset) =>  (offset > 0 ? '-' : '') + str.toLowerCase());
}