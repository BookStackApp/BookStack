import {BaseSelection, LexicalNode,} from "lexical";
import {MouseDragTracker, MouseDragTrackerDistance} from "./mouse-drag-tracker";
import {el} from "../../../utils/dom";
import {$isImageNode} from "@lexical/rich-text/LexicalImageNode";
import {EditorUiContext} from "../core";
import {NodeHasSize} from "lexical/nodes/common";
import {$isMediaNode} from "@lexical/rich-text/LexicalMediaNode";

function isNodeWithSize(node: LexicalNode): node is NodeHasSize&LexicalNode {
    return $isImageNode(node) || $isMediaNode(node);
}

class NodeResizer {
    protected context: EditorUiContext;
    protected resizerDOM: HTMLElement|null = null;
    protected targetNode: LexicalNode|null = null;
    protected scrollContainer: HTMLElement;

    protected mouseTracker: MouseDragTracker|null = null;
    protected activeSelection: string = '';
    protected loadAbortController = new AbortController();

    constructor(context: EditorUiContext) {
        this.context = context;
        this.scrollContainer = context.scrollDOM;

        this.onSelectionChange = this.onSelectionChange.bind(this);
        this.onTargetDOMLoad = this.onTargetDOMLoad.bind(this);

        context.manager.onSelectionChange(this.onSelectionChange);
    }

    onSelectionChange(selection: BaseSelection|null) {
        const nodes = selection?.getNodes() || [];
        if (this.activeSelection) {
            this.hide();
        }

        if (nodes.length === 1 && isNodeWithSize(nodes[0])) {
            const node = nodes[0];
            let nodeDOM = this.getTargetDOM(node)

            if (nodeDOM) {
                this.showForNode(node, nodeDOM);
            }
        }
    }

    protected getTargetDOM(targetNode: LexicalNode|null): HTMLElement|null {
        if (targetNode == null) {
            return null;
        }

        let nodeDOM =  this.context.editor.getElementByKey(targetNode.__key)
        if (nodeDOM && nodeDOM.nodeName === 'SPAN') {
            nodeDOM = nodeDOM.firstElementChild as HTMLElement;
        }
        return nodeDOM;
    }

    protected onTargetDOMLoad(): void {
        this.updateResizerPosition();
    }

    teardown() {
        this.context.manager.offSelectionChange(this.onSelectionChange);
        this.hide();
    }

    protected showForNode(node: NodeHasSize&LexicalNode, targetDOM: HTMLElement) {
        this.resizerDOM = this.buildDOM();
        this.targetNode = node;

        let ghost = el('span', {class: 'editor-node-resizer-ghost'});
        if ($isImageNode(node)) {
            ghost = el('img', {src: targetDOM.getAttribute('src'), class: 'editor-node-resizer-ghost'});
        }
        this.resizerDOM.append(ghost);

        this.context.scrollDOM.append(this.resizerDOM);
        this.updateResizerPosition();

        this.mouseTracker = this.setupTracker(this.resizerDOM, node, targetDOM);
        this.activeSelection = node.getKey();

        if (targetDOM.matches('img, embed, iframe, object')) {
            this.loadAbortController = new AbortController();
            targetDOM.addEventListener('load', this.onTargetDOMLoad, { signal: this.loadAbortController.signal });
        }
    }

    protected updateResizerPosition() {
        const targetDOM = this.getTargetDOM(this.targetNode);
        if (!this.resizerDOM || !targetDOM) {
            return;
        }

        const scrollAreaRect = this.scrollContainer.getBoundingClientRect();
        const nodeRect = targetDOM.getBoundingClientRect();
        const top = nodeRect.top - (scrollAreaRect.top - this.scrollContainer.scrollTop);
        const left = nodeRect.left - scrollAreaRect.left;

        this.resizerDOM.style.top = `${top}px`;
        this.resizerDOM.style.left = `${left}px`;
        this.resizerDOM.style.width = nodeRect.width + 'px';
        this.resizerDOM.style.height = nodeRect.height + 'px';
    }

    protected updateDOMSize(width: number, height: number): void {
        if (!this.resizerDOM) {
            return;
        }

        this.resizerDOM.style.width = width + 'px';
        this.resizerDOM.style.height = height + 'px';
    }

    protected hide() {
        this.mouseTracker?.teardown();
        this.resizerDOM?.remove();
        this.targetNode = null;
        this.activeSelection = '';
        this.loadAbortController.abort();
    }

    protected buildDOM() {
        const handleClasses = ['nw', 'ne', 'se', 'sw'];
        const handleElems = handleClasses.map(c => {
            return el('div', {class: `editor-node-resizer-handle ${c}`});
        });

        return el('div', {
            class: 'editor-node-resizer',
        }, handleElems);
    }

    setupTracker(container: HTMLElement, node: NodeHasSize&LexicalNode, nodeDOM: HTMLElement): MouseDragTracker {
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
            const newHeight = Math.round(newWidth * startingRatio);

            return {width: newWidth, height: newHeight};
        };

        return new MouseDragTracker(container, '.editor-node-resizer-handle', {
            down(event: MouseEvent, handle: HTMLElement) {
                _this.resizerDOM?.classList.add('active');
                _this.context.editor.getEditorState().read(() => {
                    const domRect = nodeDOM.getBoundingClientRect();
                    startingWidth = node.getWidth() || domRect.width;
                    startingHeight = node.getHeight() || domRect.height;
                    if (node.getHeight()) {
                        hasHeight = true;
                    }
                    startingRatio = startingHeight / startingWidth;
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
                }, {
                    onUpdate: () => {
                        requestAnimationFrame(() => {
                            _this.context.manager.triggerLayoutUpdate();
                            _this.updateResizerPosition();
                        });
                    }
                });
                _this.resizerDOM?.classList.remove('active');
            }
        });
    }
}


export function registerNodeResizer(context: EditorUiContext): (() => void) {
    const resizer = new NodeResizer(context);

    return () => {
        resizer.teardown();
    };
}