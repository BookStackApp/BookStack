const componentMapping = {};

const definitionFiles = require.context('./', false, /\.js$/);
for (const fileName of definitionFiles.keys()) {
    const name = fileName.replace('./', '').split('.')[0];
    if (name !== 'index') {
        componentMapping[name] = definitionFiles(fileName).default;
    }
}

window.components = {};

/**
 * Initialize components of the given name within the given element.
 * @param {String} componentName
 * @param {HTMLElement|Document} parentElement
 */
function searchForComponentInParent(componentName, parentElement) {
    const elems = parentElement.querySelectorAll(`[${componentName}]`);
    for (let j = 0, jLen = elems.length; j < jLen; j++) {
        initComponent(componentName, elems[j]);
    }
}

/**
 * Initialize a component instance on the given dom element.
 * @param {String} name
 * @param {Element} element
 */
function initComponent(name, element) {
    const componentModel = componentMapping[name];
    if (componentModel === undefined) return;

    // Create our component instance
    let instance;
    try {
        instance = new componentModel(element);
        instance.$el = element;
        const allRefs = parseRefs(name, element);
        instance.$refs = allRefs.refs;
        instance.$manyRefs = allRefs.manyRefs;
        instance.$opts = parseOpts(name, element);
        instance.$emit = (eventName, data = {}) => {
            data.from = instance;
            const event = new CustomEvent(`${name}-${eventName}`, {
                bubbles: true,
                detail: data
            });
            instance.$el.dispatchEvent(event);
        };
        if (typeof instance.setup === 'function') {
            instance.setup();
        }
    } catch (e) {
        console.error('Failed to create component', e, name, element);
    }


    // Add to global listing
    if (typeof window.components[name] === "undefined") {
        window.components[name] = [];
    }
    window.components[name].push(instance);

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
    return words[0] + words.slice(1).map(ucFirst).join();
}

/**
 * Initialize all components found within the given element.
 * @param parentElement
 */
function initAll(parentElement) {
    if (typeof parentElement === 'undefined') parentElement = document;

    // Old attribute system
    for (const componentName of Object.keys(componentMapping)) {
        searchForComponentInParent(componentName, parentElement);
    }

    // New component system
    const componentElems = parentElement.querySelectorAll(`[component],[components]`);

    for (const el of componentElems) {
        const componentNames = `${el.getAttribute('component') || ''} ${(el.getAttribute('components'))}`.toLowerCase().split(' ').filter(Boolean);
        for (const name of componentNames) {
            initComponent(name, el);
        }
    }
}

window.components.init = initAll;
window.components.first = (name) => (window.components[name] || [null])[0];

export default initAll;

/**
 * @typedef Component
 * @property {HTMLElement} $el
 * @property {Object<String, HTMLElement>} $refs
 * @property {Object<String, HTMLElement[]>} $manyRefs
 * @property {Object<String, String>} $opts
 * @property {function(string, Object)} $emit
 */