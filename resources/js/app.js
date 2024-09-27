import {EventManager} from './services/events.ts';
import {HttpManager} from './services/http.ts';
import Translations from './services/translations';
import * as componentMap from './components';
import {ComponentStore} from './services/components.ts';

// eslint-disable-next-line no-underscore-dangle
window.__DEV__ = false;

// Url retrieval function
window.baseUrl = function baseUrl(path) {
    let targetPath = path;
    let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
    if (basePath[basePath.length - 1] === '/') basePath = basePath.slice(0, basePath.length - 1);
    if (targetPath[0] === '/') targetPath = targetPath.slice(1);
    return `${basePath}/${targetPath}`;
};

window.importVersioned = function importVersioned(moduleName) {
    const version = document.querySelector('link[href*="/dist/styles.css?version="]').href.split('?version=').pop();
    const importPath = window.baseUrl(`dist/${moduleName}.js?version=${version}`);
    return import(importPath);
};

// Set events and http services on window
window.$http = new HttpManager();
window.$events = new EventManager();

// Translation setup
// Creates a global function with name 'trans' to be used in the same way as the Laravel translation system
const translator = new Translations();
window.trans = translator.get.bind(translator);
window.trans_choice = translator.getPlural.bind(translator);
window.trans_plural = translator.parsePlural.bind(translator);

// Load & initialise components
window.$components = new ComponentStore();
window.$components.register(componentMap);
window.$components.init();
