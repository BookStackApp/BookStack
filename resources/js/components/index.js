import addRemoveRows from "./add-remove-rows.js"
import ajaxDeleteRow from "./ajax-delete-row.js"
import ajaxForm from "./ajax-form.js"
import attachments from "./attachments.js"
import attachmentsList from "./attachments-list.js"
import autoSuggest from "./auto-suggest.js"
import backToTop from "./back-to-top.js"
import bookSort from "./book-sort.js"
import chapterContents from "./chapter-contents.js"
import codeEditor from "./code-editor.js"
import codeHighlighter from "./code-highlighter.js"
import codeTextarea from "./code-textarea.js"
import collapsible from "./collapsible.js"
import confirmDialog from "./confirm-dialog"
import customCheckbox from "./custom-checkbox.js"
import detailsHighlighter from "./details-highlighter.js"
import dropdown from "./dropdown.js"
import dropdownSearch from "./dropdown-search.js"
import dropzone from "./dropzone.js"
import editorToolbox from "./editor-toolbox.js"
import entitySearch from "./entity-search.js"
import entitySelector from "./entity-selector.js"
import entitySelectorPopup from "./entity-selector-popup.js"
import eventEmitSelect from "./event-emit-select.js"
import expandToggle from "./expand-toggle.js"
import headerMobileToggle from "./header-mobile-toggle.js"
import homepageControl from "./homepage-control.js"
import imageManager from "./image-manager.js"
import imagePicker from "./image-picker.js"
import listSortControl from "./list-sort-control.js"
import markdownEditor from "./markdown-editor.js"
import newUserPassword from "./new-user-password.js"
import notification from "./notification.js"
import optionalInput from "./optional-input.js"
import pageComments from "./page-comments.js"
import pageDisplay from "./page-display.js"
import pageEditor from "./page-editor.js"
import pagePicker from "./page-picker.js"
import permissionsTable from "./permissions-table.js"
import popup from "./popup.js"
import settingAppColorPicker from "./setting-app-color-picker.js"
import settingColorPicker from "./setting-color-picker.js"
import shelfSort from "./shelf-sort.js"
import sidebar from "./sidebar.js"
import sortableList from "./sortable-list.js"
import submitOnChange from "./submit-on-change.js"
import tabs from "./tabs.js"
import tagManager from "./tag-manager.js"
import templateManager from "./template-manager.js"
import toggleSwitch from "./toggle-switch.js"
import triLayout from "./tri-layout.js"
import userSelect from "./user-select.js"
import webhookEvents from "./webhook-events";
import wysiwygEditor from "./wysiwyg-editor.js"

const componentMapping = {
    "add-remove-rows": addRemoveRows,
    "ajax-delete-row": ajaxDeleteRow,
    "ajax-form": ajaxForm,
    "attachments": attachments,
    "attachments-list": attachmentsList,
    "auto-suggest": autoSuggest,
    "back-to-top": backToTop,
    "book-sort": bookSort,
    "chapter-contents": chapterContents,
    "code-editor": codeEditor,
    "code-highlighter": codeHighlighter,
    "code-textarea": codeTextarea,
    "collapsible": collapsible,
    "confirm-dialog": confirmDialog,
    "custom-checkbox": customCheckbox,
    "details-highlighter": detailsHighlighter,
    "dropdown": dropdown,
    "dropdown-search": dropdownSearch,
    "dropzone": dropzone,
    "editor-toolbox": editorToolbox,
    "entity-search": entitySearch,
    "entity-selector": entitySelector,
    "entity-selector-popup": entitySelectorPopup,
    "event-emit-select": eventEmitSelect,
    "expand-toggle": expandToggle,
    "header-mobile-toggle": headerMobileToggle,
    "homepage-control": homepageControl,
    "image-manager": imageManager,
    "image-picker": imagePicker,
    "list-sort-control": listSortControl,
    "markdown-editor": markdownEditor,
    "new-user-password": newUserPassword,
    "notification": notification,
    "optional-input": optionalInput,
    "page-comments": pageComments,
    "page-display": pageDisplay,
    "page-editor": pageEditor,
    "page-picker": pagePicker,
    "permissions-table": permissionsTable,
    "popup": popup,
    "setting-app-color-picker": settingAppColorPicker,
    "setting-color-picker": settingColorPicker,
    "shelf-sort": shelfSort,
    "sidebar": sidebar,
    "sortable-list": sortableList,
    "submit-on-change": submitOnChange,
    "tabs": tabs,
    "tag-manager": tagManager,
    "template-manager": templateManager,
    "toggle-switch": toggleSwitch,
    "tri-layout": triLayout,
    "user-select": userSelect,
    "webhook-events": webhookEvents,
    "wysiwyg-editor": wysiwygEditor,
};

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
    return words[0] + words.slice(1).map(ucFirst).join('');
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