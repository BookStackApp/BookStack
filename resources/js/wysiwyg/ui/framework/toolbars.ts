import {EditorContainerUiElement, EditorUiElement} from "./core";

import {el} from "../../utils/dom";

export type EditorContextToolbarDefinition = {
    selector: string;
    content: () => EditorUiElement[],
    displayTargetLocator?: (originalTarget: HTMLElement) => HTMLElement;
};

export class EditorContextToolbar extends EditorContainerUiElement {

    protected target: HTMLElement;

    constructor(target: HTMLElement, children: EditorUiElement[]) {
        super(children);
        this.target = target;
    }

    protected buildDOM(): HTMLElement {
        const toolbar = el('div', {
            class: 'editor-context-toolbar',
        }, this.getChildren().map(child => child.getDOMElement()));

        // Focus back on the editor on escape press
        toolbar.addEventListener('keydown', (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                event.stopPropagation();
                this.getContext().editor.focus();
            }
        }, {signal: this.abortController.signal});

        return toolbar;
    }

    /**
     * Update the position of the toolbar based on the target element.
     * Takes the bounds of other toolbars processed so far so that they can be considered
     * when positioning the toolbar to help prevent overlaps.
     */
    updatePosition(otherBounds: (DOMRect|null)[] = []): DOMRect|null {
        const context = this.getContext();
        const editorBounds = context.scrollDOM.getBoundingClientRect();
        const targetBounds = this.target.getBoundingClientRect();
        const dom = this.getDOMElement();
        const domBounds = dom.getBoundingClientRect();

        const showing = targetBounds.bottom > editorBounds.top
            && targetBounds.top < editorBounds.bottom;

        dom.hidden = !showing;

        if (!this.target.isConnected) {
            // If our target is no longer in the DOM, tell the manager an update is needed.
            context.manager.triggerFutureStateRefresh();
            return null;
        } else if (!showing) {
            return null;
        }

        const targetMid = targetBounds.left + (targetBounds.width / 2);
        const intendedBounds: DOMRectInit = {
            x: targetMid - (domBounds.width / 2),
            y: targetBounds.bottom + 6,
            width: domBounds.width,
            height: domBounds.height,
        };

        let showAbove: boolean = (
            targetBounds.bottom + 6 + domBounds.height > editorBounds.bottom
            || this.willOverlapWithOthersIfBelow(intendedBounds, otherBounds)
        );
        dom.classList.toggle('is-above', showAbove);
        if (showAbove) {
            intendedBounds.y = targetBounds.top - 6 - domBounds.height;
        }

        dom.style.top = intendedBounds.y + 'px';
        dom.style.left = intendedBounds.x + 'px';

        // Set z-index based on depth, so that the most specific toolbar
        // is bought forward.
        let depth = 1;
        let parent = this.target.parentElement;
        while (parent && parent !== context.editorDOM) {
            parent = parent.parentElement;
            depth++;
        }
        dom.style.zIndex = `${depth}`;

        return dom.getBoundingClientRect();
    }

    insert(children: EditorUiElement[]) {
        this.addChildren(...children);
        const dom = this.getDOMElement();
        dom.append(...children.map(child => child.getDOMElement()));
    }

    protected willOverlapWithOthersIfBelow(intendedBounds: DOMRectInit, otherBounds: (DOMRect|null)[]): boolean {
        for (const bounds of otherBounds) {
            if (bounds === null) continue;

            const iLeft = intendedBounds.x ?? 0;
            const iTop = intendedBounds.y ?? 0;
            const iRight = iLeft + (intendedBounds.width ?? 0);
            const iBottom = iTop + (intendedBounds.height ?? 0);

            const overlaps =  (
                iLeft < bounds.right &&
                iRight > bounds.left &&
                iTop < bounds.bottom &&
                iBottom > bounds.top
            );

            if (overlaps) {
                return true;
            }
        }
        return false;
    }
}
