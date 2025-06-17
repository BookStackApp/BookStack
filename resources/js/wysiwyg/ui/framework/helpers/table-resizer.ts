import {$getNearestNodeFromDOMNode, LexicalEditor} from "lexical";
import {MouseDragTracker, MouseDragTrackerDistance} from "./mouse-drag-tracker";
import {TableNode, TableRowNode} from "@lexical/table";
import {el} from "../../../utils/dom";
import {$getTableColumnWidth, $setTableColumnWidth} from "../../../utils/tables";

type MarkerDomRecord = {x: HTMLElement, y: HTMLElement};

class TableResizer {
    protected editor: LexicalEditor;
    protected editScrollContainer: HTMLElement;
    protected markerDom: MarkerDomRecord|null = null;
    protected mouseTracker: MouseDragTracker|null = null;
    protected dragging: boolean = false;
    protected targetCell: HTMLElement|null = null;
    protected xMarkerAtStart : boolean = false;
    protected yMarkerAtStart : boolean = false;
    protected activeInTable: boolean = false;

    constructor(editor: LexicalEditor, editScrollContainer: HTMLElement) {
        this.editor = editor;
        this.editScrollContainer = editScrollContainer;

        this.setupListeners();
    }

    teardown() {
        this.editScrollContainer.removeEventListener('mousemove', this.onCellMouseMove);
        window.removeEventListener('scroll', this.onScrollOrResize, {capture: true});
        window.removeEventListener('resize', this.onScrollOrResize);
        if (this.mouseTracker) {
            this.mouseTracker.teardown();
        }
    }

    protected setupListeners() {
        this.onTableMouseOver = this.onTableMouseOver.bind(this);
        this.onCellMouseMove = this.onCellMouseMove.bind(this);
        this.onScrollOrResize = this.onScrollOrResize.bind(this);
        this.editScrollContainer.addEventListener('mouseover', this.onTableMouseOver, { passive: true });
        window.addEventListener('scroll', this.onScrollOrResize, {capture: true, passive: true});
        window.addEventListener('resize', this.onScrollOrResize, {passive: true});
    }

    protected onScrollOrResize(): void {
        this.updateCurrentMarkerTargetPosition();
    }

    protected onTableMouseOver(event: MouseEvent): void {
        if (this.dragging) {
            return;
        }

        const table = (event.target as HTMLElement).closest('table') as HTMLElement|null;

        if (table && !this.activeInTable) {
            this.editScrollContainer.addEventListener('mousemove', this.onCellMouseMove, { passive: true });
            this.onCellMouseMove(event);
            this.activeInTable = true;
        } else if (!table && this.activeInTable) {
            this.editScrollContainer.removeEventListener('mousemove', this.onCellMouseMove);
            this.hideMarkers();
            this.activeInTable = false;
        }
    }

    protected onCellMouseMove(event: MouseEvent) {
        const cell = (event.target as HTMLElement).closest('td,th') as HTMLElement|null;
        if (!cell || this.dragging) {
            return;
        }

        const rect = cell.getBoundingClientRect();
        const midX = rect.left + (rect.width / 2);
        const midY = rect.top + (rect.height / 2);

        this.targetCell = cell;
        this.xMarkerAtStart = event.clientX <= midX;
        this.yMarkerAtStart = event.clientY <= midY;

        const xMarkerPos = this.xMarkerAtStart ? rect.left : rect.right;
        const yMarkerPos = this.yMarkerAtStart ? rect.top : rect.bottom;
        this.updateMarkersTo(cell, xMarkerPos, yMarkerPos);
    }

    protected updateMarkersTo(cell: HTMLElement, xPos: number, yPos: number) {
        const markers: MarkerDomRecord = this.getMarkers();
        const table = cell.closest('table') as HTMLElement;
        const caption: HTMLTableCaptionElement|null = table.querySelector('caption');
        const tableRect = table.getBoundingClientRect();
        const editBounds = this.editScrollContainer.getBoundingClientRect();

        let tableTop = tableRect.top;
        if (caption) {
            tableTop = caption.getBoundingClientRect().bottom;
        }

        const maxTop = Math.max(tableTop, editBounds.top);
        const maxBottom = Math.min(tableRect.bottom, editBounds.bottom);
        const maxHeight = maxBottom - maxTop;
        markers.x.style.left = xPos + 'px';
        markers.x.style.top = maxTop + 'px';
        markers.x.style.height = maxHeight + 'px';

        markers.y.style.top = yPos + 'px';
        markers.y.style.left = tableRect.left + 'px';
        markers.y.style.width = tableRect.width + 'px';

        // Hide markers when out of bounds
        markers.y.hidden = yPos < editBounds.top || yPos > editBounds.bottom;
        markers.x.hidden = tableRect.top > editBounds.bottom || tableRect.bottom < editBounds.top;
    }

    protected hideMarkers(): void {
        if (this.markerDom) {
            this.markerDom.x.hidden = true;
            this.markerDom.y.hidden = true;
        }
    }

    protected updateCurrentMarkerTargetPosition(): void {
        if (!this.targetCell) {
            return;
        }

        const rect = this.targetCell.getBoundingClientRect();
        const xMarkerPos = this.xMarkerAtStart ? rect.left : rect.right;
        const yMarkerPos = this.yMarkerAtStart ? rect.top : rect.bottom;
        this.updateMarkersTo(this.targetCell, xMarkerPos, yMarkerPos);
    }

    protected getMarkers(): MarkerDomRecord {
        if (!this.markerDom) {
            this.markerDom = {
                x: el('div', {class: 'editor-table-marker editor-table-marker-column'}),
                y: el('div', {class: 'editor-table-marker editor-table-marker-row'}),
            }
            const wrapper = el('div', {
                class: 'editor-table-marker-wrap',
            }, [this.markerDom.x, this.markerDom.y]);
            this.editScrollContainer.after(wrapper);
            this.watchMarkerMouseDrags(wrapper);
        }

        return this.markerDom;
    }

    protected watchMarkerMouseDrags(wrapper: HTMLElement) {
        const _this = this;
        let markerStart: number = 0;
        let markerProp: 'left' | 'top' = 'left';

        this.mouseTracker = new MouseDragTracker(wrapper, '.editor-table-marker', {
            down(event: MouseEvent, marker: HTMLElement) {
                marker.classList.add('active');
                _this.dragging = true;

                markerProp = marker.classList.contains('editor-table-marker-column') ? 'left' : 'top';
                markerStart = Number(marker.style[markerProp].replace('px', ''));
            },
            move(event: MouseEvent, marker: HTMLElement, distance: MouseDragTrackerDistance) {
                  marker.style[markerProp] = (markerStart + distance[markerProp === 'left' ? 'x' : 'y']) + 'px';
            },
            up(event: MouseEvent, marker: HTMLElement, distance: MouseDragTrackerDistance) {
                marker.classList.remove('active');
                marker.style.left = '0';
                marker.style.top = '0';

                _this.dragging = false;
                const parentTable = _this.targetCell?.closest('table');

                if (markerProp === 'left' && _this.targetCell && parentTable) {
                    let cellIndex = _this.getTargetCellColumnIndex();
                    let change = distance.x;
                    if (_this.xMarkerAtStart && cellIndex > 0) {
                        cellIndex -= 1;
                    } else if  (_this.xMarkerAtStart && cellIndex === 0) {
                        change = -change;
                    }

                    _this.editor.update(() => {
                        const table = $getNearestNodeFromDOMNode(parentTable);
                        if (table instanceof TableNode) {
                            const originalWidth = $getTableColumnWidth(_this.editor, table, cellIndex);
                            const newWidth = Math.max(originalWidth + change, 10);
                            $setTableColumnWidth(table, cellIndex, newWidth);
                        }
                    });
                }

                if (markerProp === 'top' && _this.targetCell) {
                    const cellElement = _this.targetCell;

                    _this.editor.update(() => {
                        const cellNode = $getNearestNodeFromDOMNode(cellElement);
                        const rowNode = cellNode?.getParent();
                        let rowIndex = rowNode?.getIndexWithinParent() || 0;

                        let change = distance.y;
                        if (_this.yMarkerAtStart && rowIndex > 0) {
                            rowIndex -= 1;
                        } else if  (_this.yMarkerAtStart && rowIndex === 0) {
                            change = -change;
                        }

                        const targetRow = rowNode?.getParent()?.getChildren()[rowIndex];
                        if (targetRow instanceof TableRowNode) {
                            const height  = targetRow.getHeight() || 0;
                            const newHeight = Math.max(height + change, 10);
                            targetRow.setHeight(newHeight);
                        }
                    });
                }
            }
        });
    }

    protected getTargetCellColumnIndex(): number {
        const cell = this.targetCell;
        if (cell === null) {
            return -1;
        }

        let index = 0;
        const row = cell.parentElement;
        for (const rowCell of row?.children || []) {
            let size = Number(rowCell.getAttribute('colspan'));
            if (Number.isNaN(size) || size < 1) {
                size = 1;
            }

            index += size;

            if (rowCell === cell) {
                return index - 1;
            }
        }

        return -1;
    }
}


export function registerTableResizer(editor: LexicalEditor, editScrollContainer: HTMLElement): (() => void) {
    const resizer = new TableResizer(editor, editScrollContainer);

    return () => {
        resizer.teardown();
    };
}