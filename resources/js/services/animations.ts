/**
 * Used in the function below to store references of clean-up functions.
 * Used to ensure only one transitionend function exists at any time.
 */
const animateStylesCleanupMap: WeakMap<object, any> = new WeakMap();

/**
 * Animate the css styles of an element using FLIP animation techniques.
 * Styles must be an object where the keys are style rule names and the values
 * are an array of two items in the format [initialValue, finalValue]
 */
function animateStyles(
    element: HTMLElement,
    styles: Record<string, string[]>,
    animTime: number = 400,
    onComplete: Function | null = null
): void {
    const styleNames = Object.keys(styles);
    for (const style of styleNames) {
        element.style.setProperty(style, styles[style][0]);
    }

    const cleanup = () => {
        for (const style of styleNames) {
            element.style.removeProperty(style);
        }
        element.style.removeProperty('transition');
        element.removeEventListener('transitionend', cleanup);
        animateStylesCleanupMap.delete(element);
        if (onComplete) onComplete();
    };

    setTimeout(() => {
        element.style.transition = `all ease-in-out ${animTime}ms`;
        for (const style of styleNames) {
            element.style.setProperty(style, styles[style][1]);
        }

        element.addEventListener('transitionend', cleanup);
        animateStylesCleanupMap.set(element, cleanup);
    }, 15);
}

/**
 * Run the active cleanup action for the given element.
 */
function cleanupExistingElementAnimation(element: Element) {
    if (animateStylesCleanupMap.has(element)) {
        const oldCleanup = animateStylesCleanupMap.get(element);
        oldCleanup();
    }
}

/**
 * Fade in the given element.
 */
export function fadeIn(element: HTMLElement, animTime: number = 400, onComplete: Function | null = null): void {
    cleanupExistingElementAnimation(element);
    element.style.display = 'block';
    animateStyles(element, {
        'opacity': ['0', '1'],
    }, animTime, () => {
        if (onComplete) onComplete();
    });
}

/**
 * Fade out the given element.
 */
export function fadeOut(element: HTMLElement, animTime: number = 400, onComplete: Function | null = null): void {
    cleanupExistingElementAnimation(element);
    animateStyles(element, {
        'opacity': ['1', '0'],
    }, animTime, () => {
        element.style.display = 'none';
        if (onComplete) onComplete();
    });
}

/**
 * Hide the element by sliding the contents upwards.
 */
export function slideUp(element: HTMLElement, animTime: number = 400) {
    cleanupExistingElementAnimation(element);
    const currentHeight = element.getBoundingClientRect().height;
    const computedStyles = getComputedStyle(element);
    const currentPaddingTop = computedStyles.getPropertyValue('padding-top');
    const currentPaddingBottom = computedStyles.getPropertyValue('padding-bottom');
    const animStyles = {
        'max-height': [`${currentHeight}px`, '0px'],
        'overflow': ['hidden', 'hidden'],
        'padding-top': [currentPaddingTop, '0px'],
        'padding-bottom': [currentPaddingBottom, '0px'],
    };

    animateStyles(element, animStyles, animTime, () => {
        element.style.display = 'none';
    });
}

/**
 * Show the given element by expanding the contents.
 */
export function slideDown(element: HTMLElement, animTime: number = 400) {
    cleanupExistingElementAnimation(element);
    element.style.display = 'block';
    const targetHeight = element.getBoundingClientRect().height;
    const computedStyles = getComputedStyle(element);
    const targetPaddingTop = computedStyles.getPropertyValue('padding-top');
    const targetPaddingBottom = computedStyles.getPropertyValue('padding-bottom');
    const animStyles = {
        'max-height': ['0px', `${targetHeight}px`],
        'overflow': ['hidden', 'hidden'],
        'padding-top': ['0px', targetPaddingTop],
        'padding-bottom': ['0px', targetPaddingBottom],
    };

    animateStyles(element, animStyles, animTime);
}

/**
 * Transition the height of the given element between two states.
 * Call with first state, and you'll receive a function in return.
 * Call the returned function in the second state to animate between those two states.
 * If animating to/from 0-height use the slide-up/slide down as easier alternatives.
 */
export function transitionHeight(element: HTMLElement, animTime: number = 400): () => void {
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
            'height': [`${startHeight}px`, `${targetHeight}px`],
            'overflow': ['hidden', 'hidden'],
            'padding-top': [startPaddingTop, targetPaddingTop],
            'padding-bottom': [startPaddingBottom, targetPaddingBottom],
        };

        animateStyles(element, animStyles, animTime);
    };
}
