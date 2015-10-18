window.ZeroClipboard = require('zeroclipboard');
window.ZeroClipboard.config({
    swfPath: '/ZeroClipboard.swf'
});

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

function elemExists(selector) {
    return document.querySelector(selector) !== null;
}

// TinyMCE editor
if(elemExists('#html-editor')) {
    var tinyMceOptions = require('./pages/page-form');
    tinymce.init(tinyMceOptions);
}

// Vue JS elements
var Vue = require('vue');
Vue.use(require('vue-resource'));

// Vue Components
Vue.component('image-manager', require('./components/image-manager.vue'));
Vue.component('image-picker', require('./components/image-picker.vue'));
Vue.component('toggle-switch', require('./components/toggle-switch.vue'));

// Vue Controllers
if(elemExists('#book-dashboard')) {
    new Vue(require('./pages/book-show'));
}

// Global Vue Instance
// Needs to be loaded after all components we want to use.
var app = new Vue({
    el: '#app'
});