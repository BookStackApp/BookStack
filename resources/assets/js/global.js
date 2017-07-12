"use strict";

// Url retrieval function
window.baseUrl = function(path) {
    let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
    if (basePath[basePath.length-1] === '/') basePath = basePath.slice(0, basePath.length-1);
    if (path[0] === '/') path = path.slice(1);
    return basePath + '/' + path;
};

const Vue = require("vue");
const axios = require("axios");

let axiosInstance = axios.create({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=token]').getAttribute('content'),
        'baseURL': window.baseUrl('')
    }
});
window.$http = axiosInstance;

Vue.prototype.$http = axiosInstance;

require("./vues/vues");


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

// Global Event System
class EventManager {
    constructor() {
        this.listeners = {};
    }

    emit(eventName, eventData) {
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

window.Events = new EventManager();
Vue.prototype.$events = window.Events;

// Load in angular specific items
const Services = require('./services');
const Directives = require('./directives');
const Controllers = require('./controllers');
Services(ngApp, window.Events);
Directives(ngApp, window.Events);
Controllers(ngApp, window.Events);

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

// Global jQuery Elements
let notifications = $('.notification');
let successNotification = notifications.filter('.pos');
let errorNotification = notifications.filter('.neg');
let warningNotification = notifications.filter('.warning');
// Notification Events
window.Events.listen('success', function (text) {
    successNotification.hide();
    successNotification.find('span').text(text);
    setTimeout(() => {
        successNotification.show();
    }, 1);
});
window.Events.listen('warning', function (text) {
    warningNotification.find('span').text(text);
    warningNotification.show();
});
window.Events.listen('error', function (text) {
    errorNotification.find('span').text(text);
    errorNotification.show();
});

// Notification hiding
notifications.click(function () {
    $(this).fadeOut(100);
});

// Chapter page list toggles
$('.chapter-toggle').click(function (e) {
    e.preventDefault();
    $(this).toggleClass('open');
    $(this).closest('.chapter').find('.inset-list').slideToggle(180);
});

// Back to top button
$('#back-to-top').click(function() {
     $('#header').smoothScrollTo();
});
let scrollTopShowing = false;
let scrollTop = document.getElementById('back-to-top');
let scrollTopBreakpoint = 1200;
window.addEventListener('scroll', function() {
    let scrollTopPos = document.documentElement.scrollTop || document.body.scrollTop || 0;
    if (!scrollTopShowing && scrollTopPos > scrollTopBreakpoint) {
        scrollTop.style.display = 'block';
        scrollTopShowing = true;
        setTimeout(() => {
            scrollTop.style.opacity = 0.4;
        }, 1);
    } else if (scrollTopShowing && scrollTopPos < scrollTopBreakpoint) {
        scrollTop.style.opacity = 0;
        scrollTopShowing = false;
        setTimeout(() => {
            scrollTop.style.display = 'none';
        }, 500);
    }
});

// Common jQuery actions
$('[data-action="expand-entity-list-details"]').click(function() {
    $('.entity-list.compact').find('p').not('.empty-text').slideToggle(240);
});

// Toggle thumbnails
$(document).ready(function(){
   $('[data-action="expand-thumbnail"]').click(function(){
     $('.galleryItem').toggleClass("collapse").find('img').slideToggle(50);
   });
});

// Popup close
$('.popup-close').click(function() {
    $(this).closest('.overlay').fadeOut(240);
});
$('.overlay').click(function(event) {
    if (!$(event.target).hasClass('overlay')) return;
    $(this).fadeOut(240);
});

// Detect IE for css
if(navigator.userAgent.indexOf('MSIE')!==-1
    || navigator.appVersion.indexOf('Trident/') > 0
    || navigator.userAgent.indexOf('Safari') !== -1){
    $('body').addClass('flexbox-support');
}

// Page specific items
require("./pages/page-show");
