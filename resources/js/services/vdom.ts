import {
    init,
    attributesModule,
    toVNode,
} from 'snabbdom';
import {VNode} from "snabbdom/build/vnode";

type vDomPatcher = (oldVnode: VNode | Element | DocumentFragment, vnode: VNode) => VNode;

let patcher: vDomPatcher;

function getPatcher(): vDomPatcher {
    if (patcher) return patcher;

    patcher = init([
        attributesModule,
    ]);

    return patcher;
}

export function patchDomFromHtmlString(domTarget: Element, html: string): void {
    const contentDom = document.createElement('div');
    contentDom.innerHTML = html;
    getPatcher()(toVNode(domTarget), toVNode(contentDom));
}
