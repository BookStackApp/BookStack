// Configure ZeroClipboard
window.ZeroClipboard = require('zeroclipboard');
window.ZeroClipboard.config({
    swfPath: '/ZeroClipboard.swf'
});

// AngularJS - Create application and load components
var angular = require('angular');
var angularResource = require('angular-resource');
var app = angular.module('bookStack', ['ngResource']);
var directives = require('./directives')(app);

// Global jQuery Elements
$(function () {

    // Notification hiding
    $('.notification').click(function () {
        $(this).fadeOut(100);
    });

    // Dropdown toggles
    $('[data-dropdown]').dropDown();

    // Chapter page list toggles
    $('.chapter-toggle').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('open');
        $(this).closest('.chapter').find('.inset-list').slideToggle(180);
    });

});

// TinyMCE editor
if(elemExists('#html-editor')) {
    var tinyMceOptions = require('./pages/page-form');
    tinymce.init(tinyMceOptions);
}