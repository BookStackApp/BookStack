"use strict";

// AngularJS - Create application and load components
var angular = require('angular');
var ngResource = require('angular-resource');
var ngAnimate = require('angular-animate');
var ngSanitize = require('angular-sanitize');
require('angular-ui-sortable');

var ngApp = angular.module('bookStack', ['ngResource', 'ngAnimate', 'ngSanitize', 'ui.sortable']);

// Global Event System
var Events = {
    listeners: {},
    emit: function (eventName, eventData) {
        if (typeof this.listeners[eventName] === 'undefined') return this;
        var eventsToStart = this.listeners[eventName];
        for (let i = 0; i < eventsToStart.length; i++) {
            var event = eventsToStart[i];
            event(eventData);
        }
        return this;
    },
    listen: function (eventName, callback) {
        if (typeof this.listeners[eventName] === 'undefined') this.listeners[eventName] = [];
        this.listeners[eventName].push(callback);
        return this;
    }
};
window.Events = Events;

var services = require('./services')(ngApp, Events);
var directives = require('./directives')(ngApp, Events);
var controllers = require('./controllers')(ngApp, Events);

//Global jQuery Config & Extensions

// Smooth scrolling
jQuery.fn.smoothScrollTo = function () {
    if (this.length === 0) return;
    $('body').animate({
        scrollTop: this.offset().top - 60 // Adjust to change final scroll position top margin
    }, 800); // Adjust to change animations speed (ms)
    return this;
};

// Making contains text expression not worry about casing
$.expr[":"].contains = $.expr.createPseudo(function (arg) {
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
        if (!scrollTopShowing && document.body.scrollTop > scrollTopBreakpoint) {
            scrollTop.style.display = 'block';
            scrollTopShowing = true;
            setTimeout(() => {
                scrollTop.style.opacity = 0.4;
            }, 1);
        } else if (scrollTopShowing && document.body.scrollTop < scrollTopBreakpoint) {
            scrollTop.style.opacity = 0;
            scrollTopShowing = false;
            setTimeout(() => {
                scrollTop.style.display = 'none';
            }, 500);
        }
    });

    // Common jQuery actions
    $('[data-action="expand-entity-list-details"]').click(function() {
        $('.entity-list.compact').find('p').slideToggle(240);
    });


});


function elemExists(selector) {
    return document.querySelector(selector) !== null;
}

// Page specific items
require('./pages/page-show');
