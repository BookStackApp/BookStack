import {
    init,
    attributesModule,
    toVNode,
} from 'snabbdom';

let patcher;

/**
 * @returns {Function}
 */
function getPatcher() {
    if (patcher) return patcher;

    patcher = init([
        attributesModule,
    ]);

    return patcher;
}

/**
 * @param {Element} domTarget
 * @param {String} html
 */
export function patchDomFromHtmlString(domTarget, html) {
    const contentDom = document.createElement('div');
    contentDom.innerHTML = html;
    getPatcher()(toVNode(domTarget), toVNode(contentDom));
}
