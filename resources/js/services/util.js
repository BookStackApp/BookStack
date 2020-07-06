

/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing.
 * @attribution https://davidwalsh.name/javascript-debounce-function
 * @param func
 * @param wait
 * @param immediate
 * @returns {Function}
 */
export function debounce(func, wait, immediate) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

/**
 * Scroll and highlight an element.
 * @param {HTMLElement} element
 */
export function scrollAndHighlightElement(element) {
    if (!element) return;
    element.scrollIntoView({behavior: 'smooth'});

    const color = document.getElementById('custom-styles').getAttribute('data-color-light');
    const initColor = window.getComputedStyle(element).getPropertyValue('background-color');
    element.style.backgroundColor = color;
    setTimeout(() => {
        element.classList.add('selectFade');
        element.style.backgroundColor = initColor;
    }, 10);
    setTimeout(() => {
        element.classList.remove('selectFade');
        element.style.backgroundColor = '';
    }, 3000);
}

/**
 * Escape any HTML in the given 'unsafe' string.
 * Take from https://stackoverflow.com/a/6234804.
 * @param {String} unsafe
 * @returns {string}
 */
export function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/**
 * Generate a random unique ID.
 *
 * @returns {string}
 */
export function uniqueId() {
    const S4 = () => (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
}