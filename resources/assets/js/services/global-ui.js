// Global jQuery Config & Extensions

import jQuery from "jquery"
window.jQuery = window.$ = jQuery;

/**
 * Scroll the view to a specific element.
 * @param {HTMLElement} element
 */
window.scrollToElement = function(element) {
    if (!element) return;
    let offset = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
    let top = element.getBoundingClientRect().top + offset;
    $('html, body').animate({
        scrollTop: top - 60 // Adjust to change final scroll position top margin
    }, 300);
};

/**
 * Scroll and highlight an element.
 * @param {HTMLElement} element
 */
window.scrollAndHighlight = function(element) {
    if (!element) return;
    window.scrollToElement(element);
    let color = document.getElementById('custom-styles').getAttribute('data-color-light');
    let initColor = window.getComputedStyle(element).getPropertyValue('background-color');
    element.style.backgroundColor = color;
    setTimeout(() => {
        element.classList.add('selectFade');
        element.style.backgroundColor = initColor;
    }, 10);
    setTimeout(() => {
        element.classList.remove('selectFade');
        element.style.backgroundColor = '';
    }, 3000);
};

// Smooth scrolling
jQuery.fn.smoothScrollTo = function () {
    if (this.length === 0) return;
    window.scrollToElement(this[0]);
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