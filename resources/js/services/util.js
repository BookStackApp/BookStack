/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing.
 * @attribution https://davidwalsh.name/javascript-debounce-function
 * @param {Function} func
 * @param {Number} waitMs
 * @param {Boolean} immediate
 * @returns {Function}
 */
export function debounce(func, waitMs, immediate) {
    let timeout;
    return function debouncedWrapper(...args) {
        const context = this;
        const later = function debouncedTimeout() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, waitMs);
        if (callNow) func.apply(context, args);
    };
}

/**
 * Scroll and highlight an element.
 * @param {HTMLElement} element
 */
export function scrollAndHighlightElement(element) {
    if (!element) return;

    const parentDetails = element.closest('details');
    if (parentDetails && !parentDetails.open) {
        parentDetails.open = true;
    }

    element.scrollIntoView({behavior: 'smooth'});

    const highlight = getComputedStyle(document.body).getPropertyValue('--color-link');
    element.style.outline = `2px dashed ${highlight}`;
    element.style.outlineOffset = '5px';
    element.style.transition = null;
    setTimeout(() => {
        element.style.transition = 'outline linear 3s';
        element.style.outline = '2px dashed rgba(0, 0, 0, 0)';
        const listener = () => {
            element.removeEventListener('transitionend', listener);
            element.style.transition = null;
            element.style.outline = null;
            element.style.outlineOffset = null;
        };
        element.addEventListener('transitionend', listener);
    }, 1000);
}

/**
 * Escape any HTML in the given 'unsafe' string.
 * Take from https://stackoverflow.com/a/6234804.
 * @param {String} unsafe
 * @returns {string}
 */
export function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Generate a random unique ID.
 *
 * @returns {string}
 */
export function uniqueId() {
    // eslint-disable-next-line no-bitwise
    const S4 = () => (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    return (`${S4() + S4()}-${S4()}-${S4()}-${S4()}-${S4()}${S4()}${S4()}`);
}

/**
 * Create a promise that resolves after the given time.
 * @param {int} timeMs
 * @returns {Promise}
 */
export function wait(timeMs) {
    return new Promise(res => {
        setTimeout(res, timeMs);
    });
}
