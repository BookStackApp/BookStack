import {EditorDecorator} from "../framework/decorator";
import {el, selectSingleNode} from "../../helpers";
import {$createNodeSelection, $setSelection} from "lexical";
import {EditorUiContext} from "../framework/core";
import {ImageNode} from "../../nodes/image";
import {MouseDragTracker, MouseDragTrackerDistance} from "../framework/helpers/mouse-drag-tracker";


export class ImageDecorator extends EditorDecorator {
    protected dom: HTMLElement|null = null;
    protected dragLastMouseUp: number = 0;

    buildDOM(context: EditorUiContext) {
        let handleElems: HTMLElement[] = [];
        const decorateEl = el('div', {
            class: 'editor-image-decorator',
        }, []);
        let selected = false;
        let tracker: MouseDragTracker|null = null;

        const windowClick = (event: MouseEvent) => {
            if (!decorateEl.contains(event.target as Node) && (Date.now() - this.dragLastMouseUp > 100)) {
                unselect();
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
            tracker = this.setupTracker(decorateEl, context);

            context.editor.update(() => {
                selectSingleNode(this.getNode());
            });
        };

        const unselect = () => {
            selected = false;
            decorateEl.classList.remove('selected');
            window.removeEventListener('click', windowClick);
            tracker?.teardown();
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

    setupTracker(container: HTMLElement, context: EditorUiContext): MouseDragTracker {
        let startingWidth: number = 0;
        let startingHeight: number = 0;
        let startingRatio: number = 0;
        let hasHeight = false;
        let firstChange = true;
        let node: ImageNode = this.getNode() as ImageNode;
        let _this = this;
        let flipXChange: boolean = false;
        let flipYChange: boolean = false;

        return new MouseDragTracker(container, '.editor-image-decorator-handle', {
            down(event: MouseEvent, handle: HTMLElement) {
                context.editor.getEditorState().read(() => {
                    startingWidth = node.getWidth() || startingWidth;
                    startingHeight = node.getHeight() || startingHeight;
                    if (node.getHeight()) {
                        hasHeight = true;
                    }
                    startingRatio = startingWidth / startingHeight;
                });

                flipXChange = handle.classList.contains('nw') || handle.classList.contains('sw');
                flipYChange = handle.classList.contains('nw') || handle.classList.contains('ne');
            },
            move(event: MouseEvent, handle: HTMLElement, distance: MouseDragTrackerDistance) {
                let xChange = distance.x;
                if (flipXChange) {
                    xChange = 0 - xChange;
                }
                let yChange = distance.y;
                if (flipYChange) {
                    yChange = 0 - yChange;
                }
                const balancedChange = Math.sqrt(Math.pow(Math.abs(xChange), 2) + Math.pow(Math.abs(yChange), 2));
                const increase = xChange + yChange > 0;
                const directedChange = increase ? balancedChange : 0-balancedChange;
                const newWidth = Math.max(5, Math.round(startingWidth + directedChange));
                let newHeight = 0;
                if (hasHeight) {
                    newHeight = newWidth * startingRatio;
                }

                const updateOptions = firstChange ? {} : {tag: 'history-merge'};
                context.editor.update(() => {
                    const node = _this.getNode() as ImageNode;
                    node.setWidth(newWidth);
                    node.setHeight(newHeight);
                }, updateOptions);
                firstChange = false;
            },
            up() {
                _this.dragLastMouseUp = Date.now();
            }
        });
    }

}