import {EditorDecorator} from "../framework/decorator";
import {el} from "../../helpers";
import {$createNodeSelection, $setSelection} from "lexical";
import {EditorUiContext} from "../framework/core";
import {ImageNode} from "../../nodes/image";


export class ImageDecorator extends EditorDecorator {
    protected dom: HTMLElement|null = null;

    buildDOM(context: EditorUiContext) {
        const handleClasses = ['nw', 'ne', 'se', 'sw'];
        const handleEls = handleClasses.map(c => {
            return el('div', {class: `editor-image-decorator-handle ${c}`});
        });

        const decorateEl = el('div', {
            class: 'editor-image-decorator',
        }, handleEls);

        const windowClick = (event: MouseEvent) => {
            if (!decorateEl.contains(event.target as Node)) {
                unselect();
            }
        };

        const select = () => {
            decorateEl.classList.add('selected');
            window.addEventListener('click', windowClick);
        };

        const unselect = () => {
            decorateEl.classList.remove('selected');
            window.removeEventListener('click', windowClick);
        };

        decorateEl.addEventListener('click', (event) => {
            context.editor.update(() => {
                const nodeSelection = $createNodeSelection();
                nodeSelection.add(this.getNode().getKey());
                $setSelection(nodeSelection);
            });

            select();
        });

        decorateEl.addEventListener('mousedown', (event: MouseEvent) => {
            const handle = (event.target as Element).closest('.editor-image-decorator-handle');
            if (handle) {
                this.startHandlingResize(handle, event, context);
            }
        });

        return decorateEl;
    }

    render(context: EditorUiContext): HTMLElement {
        if (this.dom) {
            return this.dom;
        }

        this.dom = this.buildDOM(context);
        return this.dom;
    }

    startHandlingResize(element: Node, event: MouseEvent, context: EditorUiContext) {
        const startingX = event.screenX;
        const startingY = event.screenY;

        const mouseMoveListener = (event: MouseEvent) => {
            const xChange = event.screenX - startingX;
            const yChange = event.screenY - startingY;
            console.log({ xChange, yChange });

            context.editor.update(() => {
                const node = this.getNode() as ImageNode;
                node.setWidth(node.getWidth() + xChange);
                node.setHeight(node.getHeight() + yChange);
            });
        };

        const mouseUpListener = (event: MouseEvent) => {
            window.removeEventListener('mousemove', mouseMoveListener);
            window.removeEventListener('mouseup', mouseUpListener);
        }

        window.addEventListener('mousemove', mouseMoveListener);
        window.addEventListener('mouseup', mouseUpListener);
    }

}