"use strict";

// AngularJS - Create application and load components
import angular from "angular";
import "angular-resource";
import "angular-animate";
import "angular-sanitize";
import "angular-ui-sortable";

// Url retrieval function
window.baseUrl = function(path) {
    let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
    if (basePath[basePath.length-1] === '/') basePath = basePath.slice(0, basePath.length-1);
    if (path[0] === '/') path = path.slice(1);
    return basePath + '/' + path;
};

let ngApp = angular.module('bookStack', ['ngResource', 'ngAnimate', 'ngSanitize', 'ui.sortable']);

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

    // Toggle Switches
    let $switches = $('[toggle-switch]');
    if ($switches.length > 0) {
        $switches.click(event => {
           let $switch = $(event.target);
           let input = $switch.find('input').first()[0];
           let checked = input.value !== 'true';
           input.value = checked ? 'true' : 'false';
           $switch.toggleClass('active', checked);
        });
    }

    // Image pickers
    $('.image-picker').on('click', 'button', event => {
        let button = event.target;
        let picker = $(button).closest('.image-picker')[0];
        let action = button.getAttribute('data-action');
        let resize = picker.getAttribute('data-resize-height') && picker.getAttribute('data-resize-width');
        let usingIds = picker.getAttribute('data-current-id') !== '';
        let resizeCrop = picker.getAttribute('data-resize-crop') !== '';
        let imageElem = picker.querySelector('img');
        let input = picker.querySelector('input');

        function setImage(image) {

            if (image === 'none') {
                imageElem.src = picker.getAttribute('data-default-image');
                imageElem.classList.add('none');
                input.value = 'none';
                return;
            }

            imageElem.src = image.url;
            input.value = usingIds ? image.id : image.url;
            imageElem.classList.remove('none');
        }

        if (action === 'show-image-manager') {
            window.ImageManager.showExternal((image) => {
                if (!resize) {
                    setImage(image);
                    return;
                }
                let requestString = '/images/thumb/' + image.id + '/' + picker.getAttribute('data-resize-width') + '/' + picker.getAttribute('data-resize-height') + '/' + (resizeCrop ? 'true' : 'false');
                $.get(window.baseUrl(requestString), resp => {
                    image.url = resp.url;
                    setImage(image);
                });
            });
        } else if (action === 'reset-image') {
            setImage({id: 0, url: picker.getAttribute('data-default-image')});
        } else if (action === 'remove-image') {
            setImage('none');
        }

    });

    // Detect IE for css
    if(navigator.userAgent.indexOf('MSIE')!==-1
        || navigator.appVersion.indexOf('Trident/') > 0
        || navigator.userAgent.indexOf('Safari') !== -1){
        $('body').addClass('flexbox-support');
    }

});

// Page specific items
import "./pages/page-show";
