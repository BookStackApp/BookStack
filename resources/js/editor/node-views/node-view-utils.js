import crel from "crelt";

/**
 * Render grab handles at the corners of the given element.
 * @param {Element} elem
 * @return {Element[]}
 */
export function renderHandlesAtCorners(elem) {
    const handles = [];
    const baseClass = 'ProseMirror-grabhandle';

    for (let i = 0; i < 4; i++) {
        const y = (i < 2) ? 'top' : 'bottom';
        const x = (i === 0 || i === 3) ? 'left' : 'right';
        const handle = crel('div', {
            class: `${baseClass} ${baseClass}-${x}-${y}`,
        });
        handle.dataset.y = y;
        handle.dataset.x = x;
        handles.push(handle);
        elem.parentNode.appendChild(handle);
    }

    positionHandlesAtCorners(elem, handles);
    return handles;
}

/**
 * @param {Element[]} handles
 */
export function removeHandles(handles) {
    for (const handle of handles) {
        handle.remove();
    }
}

/**
 *
 * @param {Element} element
 * @param {[Element, Element, Element, Element]}handles
 */
export function positionHandlesAtCorners(element, handles) {
    const bounds = element.getBoundingClientRect();
    const parentBounds = element.parentElement.getBoundingClientRect();
    const positions = [
        {x: bounds.left - parentBounds.left, y: bounds.top - parentBounds.top},
        {x: bounds.right - parentBounds.left, y: bounds.top - parentBounds.top},
        {x: bounds.right - parentBounds.left, y: bounds.bottom - parentBounds.top},
        {x: bounds.left - parentBounds.left, y: bounds.bottom - parentBounds.top},
    ];

    for (let i = 0; i < 4; i++) {
        const {x, y} = positions[i];
        const handle = handles[i];
        handle.style.left = (x - 6) + 'px';
        handle.style.top = (y - 6) + 'px';
    }
}