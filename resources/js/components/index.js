import dropdown from "./dropdown";
import overlay from "./overlay";
import backToTop from "./back-to-top";
import notification from "./notification";
import chapterToggle from "./chapter-toggle";
import expandToggle from "./expand-toggle";
import entitySelectorPopup from "./entity-selector-popup";
import entitySelector from "./entity-selector";
import sidebar from "./sidebar";
import pagePicker from "./page-picker";
import pageComments from "./page-comments";
import wysiwygEditor from "./wysiwyg-editor";
import markdownEditor from "./markdown-editor";
import editorToolbox from "./editor-toolbox";
import imagePicker from "./image-picker";
import collapsible from "./collapsible";
import toggleSwitch from "./toggle-switch";
import pageDisplay from "./page-display";
import shelfSort from "./shelf-sort";
import homepageControl from "./homepage-control";
import headerMobileToggle from "./header-mobile-toggle";
import listSortControl from "./list-sort-control";
import triLayout from "./tri-layout";
import breadcrumbListing from "./breadcrumb-listing";
import permissionsTable from "./permissions-table";
import customCheckbox from "./custom-checkbox";
import bookSort from "./book-sort";
import settingAppColorPicker from "./setting-app-color-picker";
import settingColorPicker from "./setting-color-picker";
import entityPermissionsEditor from "./entity-permissions-editor";
import templateManager from "./template-manager";
import newUserPassword from "./new-user-password";
import detailsHighlighter from "./details-highlighter";
import codeHighlighter from "./code-highlighter";

const componentMapping = {
    'dropdown': dropdown,
    'overlay': overlay,
    'back-to-top': backToTop,
    'notification': notification,
    'chapter-toggle': chapterToggle,
    'expand-toggle': expandToggle,
    'entity-selector-popup': entitySelectorPopup,
    'entity-selector': entitySelector,
    'sidebar': sidebar,
    'page-picker': pagePicker,
    'page-comments': pageComments,
    'wysiwyg-editor': wysiwygEditor,
    'markdown-editor': markdownEditor,
    'editor-toolbox': editorToolbox,
    'image-picker': imagePicker,
    'collapsible': collapsible,
    'toggle-switch': toggleSwitch,
    'page-display': pageDisplay,
    'shelf-sort': shelfSort,
    'homepage-control': homepageControl,
    'header-mobile-toggle': headerMobileToggle,
    'list-sort-control': listSortControl,
    'tri-layout': triLayout,
    'breadcrumb-listing': breadcrumbListing,
    'permissions-table': permissionsTable,
    'custom-checkbox': customCheckbox,
    'book-sort': bookSort,
    'setting-app-color-picker': settingAppColorPicker,
    'setting-color-picker': settingColorPicker,
    'entity-permissions-editor': entityPermissionsEditor,
    'template-manager': templateManager,
    'new-user-password': newUserPassword,
    'details-highlighter': detailsHighlighter,
    'code-highlighter': codeHighlighter,
};

window.components = {};

const componentNames = Object.keys(componentMapping);

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
