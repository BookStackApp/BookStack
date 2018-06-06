// Global Polyfills
import "@babel/polyfill"
import "./services/dom-polyfills"

// Url retrieval function
window.baseUrl = function(path) {
    let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
    if (basePath[basePath.length-1] === '/') basePath = basePath.slice(0, basePath.length-1);
    if (path[0] === '/') path = path.slice(1);
    return basePath + '/' + path;
};

// Set events and http services on window
import Events from "./services/events"
import Http from "./services/http"
let httpInstance = Http();
window.$http = httpInstance;
window.$events = new Events();

// Translation setup
// Creates a global function with name 'trans' to be used in the same way as Laravel's translation system
import Translations from "./services/translations"
let translator = new Translations(window.translations);
window.trans = translator.get.bind(translator);
window.trans_choice = translator.getPlural.bind(translator);

// Load in global UI helpers and libraries including jQuery
import "./services/global-ui"

// Set services on Vue
import Vue from "vue"
Vue.prototype.$http = httpInstance;
Vue.prototype.$events = window.$events;

// Load vues and components
import vues from "./vues/vues"
import components from "./components"
vues();
components();