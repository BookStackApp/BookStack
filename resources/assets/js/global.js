"use strict";

// AngularJS - Create application and load components
var angular = require('angular');
var ngResource = require('angular-resource');
var ngAnimate = require('angular-animate');
var ngSanitize = require('angular-sanitize');
require('angular-ui-sortable');

// Url retrieval function
window.baseUrl = function(path) {
    let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
    if (basePath[basePath.length-1] === '/') basePath = basePath.slice(0, basePath.length-1);
    if (path[0] === '/') path = path.slice(1);
    return basePath + '/' + path;
};

var ngApp = angular.module('bookStack', ['ngResource', 'ngAnimate', 'ngSanitize', 'ui.sortable']);

// Global Event System
class EventManager {
    constructor() {
        this.listeners = {};
    }

    emit(eventName, eventData) {
        if (typeof this.listeners[eventName] === 'undefined') return this;
        var eventsToStart = this.listeners[eventName];
        for (let i = 0; i < eventsToStart.length; i++) {
            var event = eventsToStart[i];
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

// Load in angular specific items
import Services from './services';
import Directives from './directives';
import Controllers from './controllers';
Services(ngApp, window.Events);
Directives(ngApp, window.Events);
Controllers(ngApp, window.Events);

//Global jQuery Config & Extensions

// Smooth scrolling
jQuery.fn.smoothScrollTo = function () {
    if (this.length === 0) return;
    let scrollElem = document.documentElement.scrollTop === 0 ?  document.body : document.documentElement;
    $(scrollElem).animate({
        scrollTop: this.offset().top - 60 // Adjust to change final scroll position top margin
    }, 800); // Adjust to change animations speed (ms)
    return this;
};

// Making contains text expression not worry about casing
jQuery.expr[":"].contains = $.expr.createPseudo(function (arg) {
    return function (elem) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

// Global jQuery Elements
$(function () {

    var notifications = $('.notification');
    var successNotification = notifications.filter('.pos');
    var errorNotification = notifications.filter('.neg');
    var warningNotification = notifications.filter('.warning');
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
    var scrollTopShowing = false;
    var scrollTop = document.getElementById('back-to-top');
    var scrollTopBreakpoint = 1200;
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

    // Popup close
    $('.popup-close').click(function() {
        $(this).closest('.overlay').fadeOut(240);
    });
    $('.overlay').click(function(event) {
        if (!$(event.target).hasClass('overlay')) return;
        $(this).fadeOut(240);
    });

    // Prevent markdown display link click redirect
    $('.markdown-display').on('click', 'a', function(event) {
        event.preventDefault();
        window.open($(this).attr('href'));
    });

    // Detect IE for css
    if(navigator.userAgent.indexOf('MSIE')!==-1
        || navigator.appVersion.indexOf('Trident/') > 0
        || navigator.userAgent.indexOf('Safari') !== -1){
        $('body').addClass('flexbox-support');
    }

});

// Page specific items
require('./pages/page-show');
