import {BaseSelection,} from "lexical";
import {MouseDragTracker, MouseDragTrackerDistance} from "./mouse-drag-tracker";
import {el} from "../../../utils/dom";
import {$isImageNode, ImageNode} from "../../../nodes/image";
import {EditorUiContext} from "../core";

class ImageResizer {
    protected context: EditorUiContext;
    protected dom: HTMLElement|null = null;
    protected scrollContainer: HTMLElement;

    protected mouseTracker: MouseDragTracker|null = null;
    protected activeSelection: string = '';

    constructor(context: EditorUiContext) {
        this.context = context;
        this.scrollContainer = context.scrollDOM;

        this.onSelectionChange = this.onSelectionChange.bind(this);
        context.manager.onSelectionChange(this.onSelectionChange);
    }

    onSelectionChange(selection: BaseSelection|null) {
        const nodes = selection?.getNodes() || [];
        if (this.activeSelection) {
            this.hide();
        }

        if (nodes.length === 1 && $isImageNode(nodes[0])) {
            const imageNode = nodes[0];
            const nodeKey = imageNode.getKey();
            const imageDOM = this.context.editor.getElementByKey(nodeKey);

            if (imageDOM) {
                this.showForImage(imageNode, imageDOM);
            }
        }
    }

    teardown() {
        this.context.manager.offSelectionChange(this.onSelectionChange);
        this.hide();
    }

    protected showForImage(node: ImageNode, dom: HTMLElement) {
        this.dom = this.buildDOM();

        const ghost = el('img', {src: dom.getAttribute('src'), class: 'editor-image-resizer-ghost'});
        this.dom.append(ghost);

        this.context.scrollDOM.append(this.dom);
        this.updateDOMPosition(dom);

        this.mouseTracker = this.setupTracker(this.dom, node, dom);
        this.activeSelection = node.getKey();
    }

    protected updateDOMPosition(imageDOM: HTMLElement) {
        if (!this.dom) {
            return;
        }

        const imageBounds = imageDOM.getBoundingClientRect();
        this.dom.style.left = imageDOM.offsetLeft + 'px';
        this.dom.style.top = imageDOM.offsetTop + 'px';
        this.dom.style.width = imageBounds.width + 'px';
        this.dom.style.height = imageBounds.height + 'px';
    }

    protected updateDOMSize(width: number, height: number): void {
        if (!this.dom) {
            return;
        }

        this.dom.style.width = width + 'px';
        this.dom.style.height = height + 'px';
    }

    protected hide() {
        this.mouseTracker?.teardown();
        this.dom?.remove();
        this.activeSelection = '';
    }

    protected buildDOM() {
        const handleClasses = ['nw', 'ne', 'se', 'sw'];
        const handleElems = handleClasses.map(c => {
            return el('div', {class: `editor-image-resizer-handle ${c}`});
        });

        return el('div', {
            class: 'editor-image-resizer',
        }, handleElems);
    }

    setupTracker(container: HTMLElement, node: ImageNode, imageDOM: HTMLElement): MouseDragTracker {
        let startingWidth: number = 0;
        let startingHeight: number = 0;
        let startingRatio: number = 0;
        let hasHeight = false;
        let _this = this;
        let flipXChange: boolean = false;
        let flipYChange: boolean = false;

        const calculateSize = (distance: MouseDragTrackerDistance): {width: number, height: number} => {
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
            const newHeight = newWidth * startingRatio;

            return {width: newWidth, height: newHeight};
        };

        return new MouseDragTracker(container, '.editor-image-resizer-handle', {
            down(event: MouseEvent, handle: HTMLElement) {
                _this.dom?.classList.add('active');
                _this.context.editor.getEditorState().read(() => {
                    const imageRect = imageDOM.getBoundingClientRect();
                    startingWidth = node.getWidth() || imageRect.width;
                    startingHeight = node.getHeight() || imageRect.height;
                    if (node.getHeight()) {
                        hasHeight = true;
                    }
                    startingRatio = startingWidth / startingHeight;
                });

                flipXChange = handle.classList.contains('nw') || handle.classList.contains('sw');
                flipYChange = handle.classList.contains('nw') || handle.classList.contains('ne');
            },
            move(event: MouseEvent, handle: HTMLElement, distance: MouseDragTrackerDistance) {
                const size = calculateSize(distance);
                _this.updateDOMSize(size.width, size.height);
            },
            up(event: MouseEvent, handle: HTMLElement, distance: MouseDragTrackerDistance) {
                const size = calculateSize(distance);
                _this.context.editor.update(() => {
                    node.setWidth(size.width);
                    node.setHeight(hasHeight ? size.height : 0);
                    _this.context.manager.triggerLayoutUpdate();
                    requestAnimationFrame(() => {
                        _this.updateDOMPosition(imageDOM);
                    })
                });
                _this.dom?.classList.remove('active');
            }
        });
    }
}


export function registerImageResizer(context: EditorUiContext): (() => void) {
    const resizer = new ImageResizer(context);

    return () => {
        resizer.teardown();
    };
}