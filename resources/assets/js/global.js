"use strict";
require("babel-polyfill");
require('./dom-polyfills');

// Url retrieval function
window.baseUrl = function(path) {
    let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
    if (basePath[basePath.length-1] === '/') basePath = basePath.slice(0, basePath.length-1);
    if (path[0] === '/') path = path.slice(1);
    return basePath + '/' + path;
};

// Global Event System
class EventManager {
    constructor() {
        this.listeners = {};
        this.stack = [];
    }

    emit(eventName, eventData) {
        this.stack.push({name: eventName, data: eventData});
        if (typeof this.listeners[eventName] === 'undefined') return this;
        let eventsToStart = this.listeners[eventName];
        for (let i = 0; i < eventsToStart.length; i++) {
            let event = eventsToStart[i];
            event(eventData);
        }
        return this;
    }

    listen(eventName, callback) {
        if (typeof this.listeners[eventName] === 'undefined') this.listeners[eventName] = [];
        this.listeners[eventName].push(callback);
        return this;
    }
}

window.$events = new EventManager();

const Vue = require("vue");
const axios = require("axios");

let axiosInstance = axios.create({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=token]').getAttribute('content'),
        'baseURL': window.baseUrl('')
    }
});
axiosInstance.interceptors.request.use(resp => {
    return resp;
}, err => {
    if (typeof err.response === "undefined" || typeof err.response.data === "undefined") return Promise.reject(err);
    if (typeof err.response.data.error !== "undefined") window.$events.emit('error', err.response.data.error);
    if (typeof err.response.data.message !== "undefined") window.$events.emit('error', err.response.data.message);
});
window.$http = axiosInstance;

Vue.prototype.$http = axiosInstance;
Vue.prototype.$events = window.$events;


// AngularJS - Create application and load components
const angular = require("angular");
require("angular-resource");
require("angular-animate");
require("angular-sanitize");
require("angular-ui-sortable");

let ngApp = angular.module('bookStack', ['ngResource', 'ngAnimate', 'ngSanitize', 'ui.sortable']);

// Translation setup
// Creates a global function with name 'trans' to be used in the same way as Laravel's translation system
const Translations = require("./translations");
let translator = new Translations(window.translations);
window.trans = translator.get.bind(translator);
window.trans_choice = translator.getPlural.bind(translator);


require("./vues/vues");
require("./components");

// Load in angular specific items
const Directives = require('./directives');
const Controllers = require('./controllers');
Directives(ngApp, window.$events);
Controllers(ngApp, window.$events);

//Global jQuery Config & Extensions

// Smooth scrolling
jQuery.fn.smoothScrollTo = function () {
    if (this.length === 0) return;
    $('html, body').animate({
        scrollTop: this.offset().top - 60 // Adjust to change final scroll position top margin
    }, 300); // Adjust to change animations speed (ms)
    return this;
};

// Making contains text expression not worry about casing
jQuery.expr[":"].contains = $.expr.createPseudo(function (arg) {
    return function (elem) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

// Detect IE for css
if(navigator.userAgent.indexOf('MSIE')!==-1
    || navigator.appVersion.indexOf('Trident/') > 0
    || navigator.userAgent.indexOf('Safari') !== -1){
    document.body.classList.add('flexbox-support');
}

// Page specific items
require("./pages/page-show");
