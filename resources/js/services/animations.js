/**
 * Used in the function below to store references of clean-up functions.
 * Used to ensure only one transitionend function exists at any time.
 * @type {WeakMap<object, any>}
 */
const animateStylesCleanupMap = new WeakMap();

/**
 * Fade in the given element.
 * @param {Element} element
 * @param {Number} animTime
 * @param {Function|null} onComplete
 */
export function fadeIn(element, animTime = 400, onComplete = null) {
    cleanupExistingElementAnimation(element);
    element.style.display = 'block';
    animateStyles(element, {
        opacity: ['0', '1']
    }, animTime, () => {
        if (onComplete) onComplete();
    });
}

/**
 * Fade out the given element.
 * @param {Element} element
 * @param {Number} animTime
 * @param {Function|null} onComplete
 */
export function fadeOut(element, animTime = 400, onComplete = null) {
    cleanupExistingElementAnimation(element);
    animateStyles(element, {
        opacity: ['1', '0']
    }, animTime, () => {
        element.style.display = 'none';
        if (onComplete) onComplete();
    });
}

/**
 * Hide the element by sliding the contents upwards.
 * @param {Element} element
 * @param {Number} animTime
 */
export function slideUp(element, animTime = 400) {
    cleanupExistingElementAnimation(element);
    const currentHeight = element.getBoundingClientRect().height;
    const computedStyles = getComputedStyle(element);
    const currentPaddingTop = computedStyles.getPropertyValue('padding-top');
    const currentPaddingBottom = computedStyles.getPropertyValue('padding-bottom');
    const animStyles = {
        height: [`${currentHeight}px`, '0px'],
        overflow: ['hidden', 'hidden'],
        paddingTop: [currentPaddingTop, '0px'],
        paddingBottom: [currentPaddingBottom, '0px'],
    };

    animateStyles(element, animStyles, animTime, () => {
        element.style.display = 'none';
    });
}

/**
 * Show the given element by expanding the contents.
 * @param {Element} element - Element to animate
 * @param {Number} animTime - Animation time in ms
 */
export function slideDown(element, animTime = 400) {
    cleanupExistingElementAnimation(element);
    element.style.display = 'block';
    const targetHeight = element.getBoundingClientRect().height;
    const computedStyles = getComputedStyle(element);
    const targetPaddingTop = computedStyles.getPropertyValue('padding-top');
    const targetPaddingBottom = computedStyles.getPropertyValue('padding-bottom');
    const animStyles = {
        height: ['0px', `${targetHeight}px`],
        overflow: ['hidden', 'hidden'],
        paddingTop: ['0px', targetPaddingTop],
        paddingBottom: ['0px', targetPaddingBottom],
    };

    animateStyles(element, animStyles, animTime);
}

/**
 * Transition the height of the given element between two states.
 * Call with first state, and you'll receive a function in return.
 * Call the returned function in the second state to animate between those two states.
 * If animating to/from 0-height use the slide-up/slide down as easier alternatives.
 * @param {Element} element - Element to animate
 * @param {Number} animTime - Animation time in ms
 * @returns {function} - Function to run in second state to trigger animation.
 */
export function transitionHeight(element, animTime = 400) {
    const startHeight = element.getBoundingClientRect().height;
    const initialComputedStyles = getComputedStyle(element);
    const startPaddingTop = initialComputedStyles.getPropertyValue('padding-top');
    const startPaddingBottom = initialComputedStyles.getPropertyValue('padding-bottom');

    return () => {
        cleanupExistingElementAnimation(element);
        const targetHeight = element.getBoundingClientRect().height;
        const computedStyles = getComputedStyle(element);
        const targetPaddingTop = computedStyles.getPropertyValue('padding-top');
        const targetPaddingBottom = computedStyles.getPropertyValue('padding-bottom');
        const animStyles = {
            height: [`${startHeight}px`, `${targetHeight}px`],
            overflow: ['hidden', 'hidden'],
            paddingTop: [startPaddingTop, targetPaddingTop],
            paddingBottom: [startPaddingBottom, targetPaddingBottom],
        };

        animateStyles(element, animStyles, animTime);
    };
}

/**
 * Animate the css styles of an element using FLIP animation techniques.
 * Styles must be an object where the keys are style properties, camelcase, and the values
 * are an array of two items in the format [initialValue, finalValue]
 * @param {Element} element
 * @param {Object} styles
 * @param {Number} animTime
 * @param {Function} onComplete
 */
function animateStyles(element, styles, animTime = 400, onComplete = null) {
    const styleNames = Object.keys(styles);
    for (let style of styleNames) {
        element.style[style] = styles[style][0];
    }

    const cleanup = () => {
        for (let style of styleNames) {
            element.style[style] = null;
        }
        element.style.transition = null;
        element.removeEventListener('transitionend', cleanup);
        animateStylesCleanupMap.delete(element);
        if (onComplete) onComplete();
    };

    setTimeout(() => {
        element.style.transition = `all ease-in-out ${animTime}ms`;
        for (let style of styleNames) {
            element.style[style] = styles[style][1];
        }

        element.addEventListener('transitionend', cleanup);
        animateStylesCleanupMap.set(element, cleanup);
    }, 15);
}

/**
 * Run the active cleanup action for the given element.
 * @param {Element} element
 */
function cleanupExistingElementAnimation(element) {
    if (animateStylesCleanupMap.has(element)) {
        const oldCleanup = animateStylesCleanupMap.get(element);
        oldCleanup();
    }
}