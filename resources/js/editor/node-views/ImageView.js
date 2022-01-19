import {positionHandlesAtCorners, removeHandles, renderHandlesAtCorners} from "./node-view-utils";
import {NodeSelection} from "prosemirror-state";

class ImageView {
    /**
     * @param {PmNode} node
     * @param {PmView} view
     * @param {(function(): number)} getPos
     */
    constructor(node, view, getPos) {
        this.dom = document.createElement('div');
        this.dom.classList.add('ProseMirror-imagewrap');

        this.image = document.createElement("img");
        this.image.src = node.attrs.src;
        this.image.alt = node.attrs.alt;
        if (node.attrs.width) {
            this.image.width = node.attrs.width;
        }
        if (node.attrs.height) {
            this.image.height = node.attrs.height;
        }

        this.dom.appendChild(this.image);

        this.handles = [];
        this.handleDragStartInfo = null;
        this.handleDragMoveDimensions = null;
        this.removeHandlesListener = this.removeHandlesListener.bind(this);
        this.handleMouseMove = this.handleMouseMove.bind(this);
        this.handleMouseUp = this.handleMouseUp.bind(this);
        this.handleMouseDown = this.handleMouseDown.bind(this);

        this.dom.addEventListener("click", event => {
            this.showHandles();
        });

        // Show handles if selected
        if (view.state.selection.node === node) {
            window.setTimeout(() => {
                this.showHandles();
            }, 10);
        }

        this.updateImageDimensions = function (width, height) {
            const attrs = Object.assign({}, node.attrs, {width, height});
            let tr = view.state.tr;
            const position = getPos();
            tr = tr.setNodeMarkup(position, null, attrs)
            tr = tr.setSelection(NodeSelection.create(tr.doc, position));
            view.dispatch(tr);
        };

    }

    showHandles() {
        if (this.handles.length === 0) {
            this.image.dataset.showHandles = 'true';
            window.addEventListener('click', this.removeHandlesListener);
            this.handles = renderHandlesAtCorners(this.image);
            for (const handle of this.handles) {
                handle.addEventListener('mousedown', this.handleMouseDown);
            }
        }
    }

    removeHandlesListener(event) {
        if (!this.dom.contains(event.target)) {
            this.removeHandles();
            this.handles = [];
        }
    }

    removeHandles() {
        removeHandles(this.handles);
        window.removeEventListener('click', this.removeHandlesListener);
        delete this.image.dataset.showHandles;
    }

    stopEvent() {
        return false;
    }

    /**
     * @param {MouseEvent} event
     */
    handleMouseDown(event) {
        event.preventDefault();

        const imageBounds = this.image.getBoundingClientRect();
        const handle = event.target;
        this.handleDragStartInfo = {
            x: event.screenX,
            y: event.screenY,
            ratio: imageBounds.width / imageBounds.height,
            bounds: imageBounds,
            handleX: handle.dataset.x,
            handleY: handle.dataset.y,
        };

        this.createDragDummy(imageBounds);
        this.dom.appendChild(this.dragDummy);

        window.addEventListener('mousemove', this.handleMouseMove);
        window.addEventListener('mouseup', this.handleMouseUp);
    }

    /**
     * @param {DOMRect} bounds
     */
    createDragDummy(bounds) {
        this.dragDummy = this.image.cloneNode();
        this.dragDummy.style.opacity = '0.5';
        this.dragDummy.classList.add('ProseMirror-dragdummy');
        this.dragDummy.style.width = bounds.width + 'px';
        this.dragDummy.style.height = bounds.height + 'px';
    }

    /**
     * @param {MouseEvent} event
     */
    handleMouseUp(event) {
        if (this.handleDragMoveDimensions) {
            const {width, height} = this.handleDragMoveDimensions;
            this.updateImageDimensions(String(width), String(height));
        }

        window.removeEventListener('mousemove', this.handleMouseMove);
        window.removeEventListener('mouseup', this.handleMouseUp);
        this.handleDragStartInfo = null;
        this.handleDragMoveDimensions = null;
        this.dragDummy.remove();
        positionHandlesAtCorners(this.image, this.handles);
    }

    /**
     * @param {MouseEvent} event
     */
    handleMouseMove(event) {
        const originalBounds = this.handleDragStartInfo.bounds;

        // Calculate change in x & y, flip amounts depending on handle
        let xChange = event.screenX - this.handleDragStartInfo.x;
        if (this.handleDragStartInfo.handleX === 'left') {
            xChange = -xChange;
        }
        let yChange = event.screenY - this.handleDragStartInfo.y;
        if (this.handleDragStartInfo.handleY === 'top') {
            yChange = -yChange;
        }

        // Prevent images going too small or into negative bounds
        if (originalBounds.width + xChange < 10) {
            xChange = -originalBounds.width + 10;
        }
        if (originalBounds.height + yChange < 10) {
            yChange = -originalBounds.height + 10;
        }

        // Choose the larger dimension change and align the other to keep
        // image aspect ratio, aligning growth/reduction direction
        if (Math.abs(xChange) > Math.abs(yChange)) {
            yChange = Math.floor(xChange * this.handleDragStartInfo.ratio);
            if (yChange * xChange < 0) {
                yChange = -yChange;
            }
        } else {
            xChange = Math.floor(yChange / this.handleDragStartInfo.ratio);
            if (xChange * yChange < 0) {
                xChange = -xChange;
            }
        }

        // Calculate our new sizes
        const newWidth = originalBounds.width + xChange;
        const newHeight = originalBounds.height + yChange;

        // Apply the sizes and positioning to our ghost dummy
        this.dragDummy.style.width = `${newWidth}px`;
        if (this.handleDragStartInfo.handleX === 'left') {
            this.dragDummy.style.left = `${-xChange}px`;
        }
        this.dragDummy.style.height = `${newHeight}px`;
        if (this.handleDragStartInfo.handleY === 'top') {
            this.dragDummy.style.top = `${-yChange}px`;
        }

        // Update corners and track dimension changes for later application
        positionHandlesAtCorners(this.dragDummy, this.handles);
        this.handleDragMoveDimensions = {
            width: newWidth,
            height: newHeight,
        }
    }
}

export default ImageView;