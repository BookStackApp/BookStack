// Configure ZeroClipboard
window.ZeroClipboard = require('zeroclipboard');
window.ZeroClipboard.config({
    swfPath: '/ZeroClipboard.swf'
});

// AngularJS - Create application and load components
var angular = require('angular');
var ngResource = require('angular-resource');
var ngAnimate = require('angular-animate');
var ngSanitize = require('angular-sanitize');

var ngApp = angular.module('bookStack', ['ngResource', 'ngAnimate', 'ngSanitize']);
var services = require('./services')(ngApp);
var directives = require('./directives')(ngApp);
var controllers = require('./controllers')(ngApp);

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

    // Notification hiding
    $('.notification').click(function () {
        $(this).fadeOut(100);
    });

    // Chapter page list toggles
    $('.chapter-toggle').click(function (e) {
        e.preventDefault();
        $(this).toggleClass('open');
        $(this).closest('.chapter').find('.inset-list').slideToggle(180);
    });

});


function elemExists(selector) {
    return document.querySelector(selector) !== null;
}

// TinyMCE editor
if (elemExists('#html-editor')) {
    var tinyMceOptions = require('./pages/page-form');
    tinymce.init(tinyMceOptions);
}