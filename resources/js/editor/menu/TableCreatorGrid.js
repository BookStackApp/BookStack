import crel from "crelt"
import {prefix} from "./menu-utils";
import {insertTable} from "../commands";

class TableCreatorGrid {

    constructor() {
        this.size = 10;
        this.label = null;
    }

    // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
    // Renders the submenu.
    render(view) {

        const gridItems = [];
        for (let y = 0; y < this.size; y++) {
            for (let x = 0; x < this.size; x++) {
                const elem = crel("div", {class: prefix + "-table-creator-grid-item"});
                gridItems.push(elem);
                elem.addEventListener('mouseenter', event => {
                    this.updateGridItemActiveStatus(elem, gridItems);
                });
            }
        }

        const gridWrap = crel("div", {
            class: prefix + "-table-creator-grid",
            style: `grid-template-columns: repeat(${this.size}, 14px);`,
        }, gridItems);

        gridWrap.addEventListener('mouseleave', event => {
            this.updateGridItemActiveStatus(null, gridItems);
        });
        gridWrap.addEventListener('click', event => {
            if (event.target.classList.contains(prefix + "-table-creator-grid-item")) {
                const {x, y} = this.getPositionOfGridItem(event.target, gridItems);
                insertTable(y + 1, x + 1, {
                    style: 'width: 100%;',
                })(view.state, view.dispatch);
            }
        });

        const gridLabel = crel("div", {class: prefix + "-table-creator-grid-label"});
        this.label = gridLabel;
        const wrap = crel("div", {class: prefix + "-table-creator-grid-container"}, [gridWrap, gridLabel]);

        function update(state) {
            return true;
        }

        return {dom: wrap, update}
    }

    /**
     * @param {Element|null} newTarget
     * @param {Element[]} gridItems
     */
    updateGridItemActiveStatus(newTarget, gridItems) {
        const {x: xPos, y: yPos} = this.getPositionOfGridItem(newTarget, gridItems);

        for (let y = 0; y < this.size; y++) {
            for (let x = 0; x < this.size; x++) {
                const active = x <= xPos && y <= yPos;
                const index = (y * this.size) + x;
                gridItems[index].classList.toggle(prefix + "-table-creator-grid-item-active", active);
            }
        }

        this.label.textContent = (xPos + yPos < 0) ? '' : `${xPos + 1} x ${yPos + 1}`;
    }

    /**
     * @param {Element} gridItem
     * @param {Element[]} gridItems
     * @return {{x: number, y: number}}
     */
    getPositionOfGridItem(gridItem, gridItems) {
        const index = gridItems.indexOf(gridItem);
        const y = Math.floor(index / this.size);
        const x = index % this.size;
        return {x, y};
    }
}

export default TableCreatorGrid;