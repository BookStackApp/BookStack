import crel from "crelt"
import {prefix} from "./menu-utils";
import {insertTable} from "../commands";

class TableCreatorGrid {

    constructor() {
        this.gridItems = [];
        this.size = 10;
        this.label = null;
    }

    // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
    // Renders the submenu.
    render(view) {

        for (let y = 0; y < this.size; y++) {
            for (let x = 0; x < this.size; x++) {
                const elem = crel("div", {class: prefix + "-table-creator-grid-item"});
                this.gridItems.push(elem);
                elem.addEventListener('mouseenter', event => this.updateGridItemActiveStatus(elem));
            }
        }

        const gridWrap = crel("div", {
            class: prefix + "-table-creator-grid",
            style: `grid-template-columns: repeat(${this.size}, 14px);`,
        }, this.gridItems);

        gridWrap.addEventListener('mouseleave', event => {
            this.updateGridItemActiveStatus(null);
        });
        gridWrap.addEventListener('click', event => {
            if (event.target.classList.contains(prefix + "-table-creator-grid-item")) {
                const {x, y} = this.getPositionOfGridItem(event.target);
                insertTable(y + 1, x + 1)(view.state, view.dispatch);
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
     */
    updateGridItemActiveStatus(newTarget) {
        const {x: xPos, y: yPos} = this.getPositionOfGridItem(newTarget);

        for (let y = 0; y < this.size; y++) {
            for (let x = 0; x < this.size; x++) {
                const active = x <= xPos && y <= yPos;
                const index = (y * this.size) + x;
                this.gridItems[index].classList.toggle(prefix + "-table-creator-grid-item-active", active);
            }
        }

        this.label.textContent = (xPos + yPos < 0) ? '' : `${xPos + 1} x ${yPos + 1}`;
    }

    /**
     * @param {Element} gridItem
     * @return {{x: number, y: number}}
     */
    getPositionOfGridItem(gridItem) {
        const index = this.gridItems.indexOf(gridItem);
        const y = Math.floor(index / this.size);
        const x = index % this.size;
        return {x, y};
    }
}

export default TableCreatorGrid;