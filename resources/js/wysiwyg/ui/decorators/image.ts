import {EditorDecorator} from "../framework/decorator";
import {el} from "../../helpers";
import {$createNodeSelection, $setSelection} from "lexical";
import {EditorUiContext} from "../framework/core";
import {ImageNode} from "../../nodes/image";


export class ImageDecorator extends EditorDecorator {
    protected dom: HTMLElement|null = null;
    protected dragLastMouseUp: number = 0;

    buildDOM(context: EditorUiContext) {
        let handleElems: HTMLElement[] = [];
        const decorateEl = el('div', {
            class: 'editor-image-decorator',
        }, []);
        let selected = false;

        const windowClick = (event: MouseEvent) => {
            if (!decorateEl.contains(event.target as Node) && (Date.now() - this.dragLastMouseUp > 100)) {
                unselect();
            }
        };

        const mouseDown = (event: MouseEvent) => {
            const handle = (event.target as HTMLElement).closest('.editor-image-decorator-handle') as HTMLElement|null;
            if (handle) {
                // handlingResize = true;
                this.startHandlingResize(handle, event, context);
            }
        };

        const select = () => {
            if (selected) {
                return;
            }

            selected = true;
            decorateEl.classList.add('selected');
            window.addEventListener('click', windowClick);

            const handleClasses = ['nw', 'ne', 'se', 'sw'];
            handleElems = handleClasses.map(c => {
                return el('div', {class: `editor-image-decorator-handle ${c}`});
            });
            decorateEl.append(...handleElems);
            decorateEl.addEventListener('mousedown', mouseDown);

            context.editor.update(() => {
                const nodeSelection = $createNodeSelection();
                nodeSelection.add(this.getNode().getKey());
                $setSelection(nodeSelection);
            });
        };

        const unselect = () => {
            selected = false;
            // handlingResize = false;
            decorateEl.classList.remove('selected');
            window.removeEventListener('click', windowClick);
            decorateEl.removeEventListener('mousedown', mouseDown);
            for (const el of handleElems) {
                el.remove();
            }
        };

        decorateEl.addEventListener('click', (event) => {
            select();
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

    startHandlingResize(element: HTMLElement, event: MouseEvent, context: EditorUiContext) {
        const startingX = event.screenX;
        const startingY = event.screenY;
        const node = this.getNode() as ImageNode;
        let startingWidth = element.clientWidth;
        let startingHeight = element.clientHeight;
        let startingRatio = startingWidth / startingHeight;
        let hasHeight = false;
        context.editor.getEditorState().read(() => {
            startingWidth = node.getWidth() || startingWidth;
            startingHeight = node.getHeight() || startingHeight;
            if (node.getHeight()) {
                hasHeight = true;
            }
            startingRatio = startingWidth / startingHeight;
        });

        const flipXChange = element.classList.contains('nw') || element.classList.contains('sw');
        const flipYChange = element.classList.contains('nw') || element.classList.contains('ne');

        const mouseMoveListener = (event: MouseEvent) => {
            let xChange = event.screenX - startingX;
            if (flipXChange) {
                xChange = 0 - xChange;
            }
            let yChange = event.screenY - startingY;
            if (flipYChange) {
                yChange = 0 - yChange;
            }
            const balancedChange = Math.sqrt(Math.pow(xChange, 2) + Math.pow(yChange, 2));
            const increase = xChange + yChange > 0;
            const directedChange = increase ? balancedChange : 0-balancedChange;
            const newWidth = Math.max(5, Math.round(startingWidth + directedChange));
            let newHeight = 0;
            if (hasHeight) {
                newHeight = newWidth * startingRatio;
            }

            context.editor.update(() => {
                const node = this.getNode() as ImageNode;
                node.setWidth(newWidth);
                node.setHeight(newHeight);
            });
        };

        const mouseUpListener = (event: MouseEvent) => {
            window.removeEventListener('mousemove', mouseMoveListener);
            window.removeEventListener('mouseup', mouseUpListener);
            this.dragLastMouseUp = Date.now();
        };

        window.addEventListener('mousemove', mouseMoveListener);
        window.addEventListener('mouseup', mouseUpListener);
    }

}