
let componentMapping = {
    'dropdown': require('./dropdown'),
    'overlay': require('./overlay'),
    'back-to-top': require('./back-top-top'),
    'notification': require('./notification'),
    'chapter-toggle': require('./chapter-toggle'),
    'expand-toggle': require('./expand-toggle'),
    'entity-selector-popup': require('./entity-selector-popup'),
    'entity-selector': require('./entity-selector'),
    'sidebar': require('./sidebar'),
    'page-picker': require('./page-picker'),
    'page-comments': require('./page-comments'),
    'wysiwyg-editor': require('./wysiwyg-editor'),
    'markdown-editor': require('./markdown-editor'),
    'editor-toolbox': require('./editor-toolbox'),
    'image-picker': require('./image-picker'),
    'collapsible': require('./collapsible'),
    'toggle-switch': require('./toggle-switch'),
    'page-display': require('./page-display'),
    'shelf-sort': require('./shelf-sort'),
    'homepage-control': require('./homepage-control'),
};

window.components = {};

let componentNames = Object.keys(componentMapping);

/**
 * Initialize components of the given name within the given element.
 * @param {String} componentName
 * @param {HTMLElement|Document} parentElement
 */
function initComponent(componentName, parentElement) {
    let elems = parentElement.querySelectorAll(`[${componentName}]`);
    if (elems.length === 0) return;

    let component = componentMapping[componentName];
    if (typeof window.components[componentName] === "undefined") window.components[componentName] = [];
    for (let j = 0, jLen = elems.length; j < jLen; j++) {
        let instance = new component(elems[j]);
        if (typeof elems[j].components === 'undefined') elems[j].components = {};
        elems[j].components[componentName] = instance;
        window.components[componentName].push(instance);
    }
}

/**
 * Initialize all components found within the given element.
 * @param parentElement
 */
function initAll(parentElement) {
    if (typeof parentElement === 'undefined') parentElement = document;
    for (let i = 0, len = componentNames.length; i < len; i++) {
        initComponent(componentNames[i], parentElement);
    }
}

window.components.init = initAll;

export default initAll;