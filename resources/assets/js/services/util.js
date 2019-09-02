

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