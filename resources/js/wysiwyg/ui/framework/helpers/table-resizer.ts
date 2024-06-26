import {$getNearestNodeFromDOMNode, LexicalEditor} from "lexical";
import {el} from "../../../helpers";
import {MouseDragTracker, MouseDragTrackerDistance} from "./mouse-drag-tracker";
import {$getTableColumnWidth, $setTableColumnWidth, CustomTableNode} from "../../../nodes/custom-table";

type MarkerDomRecord = {x: HTMLElement, y: HTMLElement};

class TableResizer {
    protected editor: LexicalEditor;
    protected editArea: HTMLElement;
    protected markerDom: MarkerDomRecord|null = null;
    protected mouseTracker: MouseDragTracker|null = null;
    protected dragging: boolean = false;
    protected targetCell: HTMLElement|null = null;
    protected xMarkerAtStart : boolean = false;
    protected yMarkerAtStart : boolean = false;

    constructor(editor: LexicalEditor, editArea: HTMLElement) {
        this.editor = editor;
        this.editArea = editArea;
        this.setupListeners();
    }

    setupListeners() {
        this.editArea.addEventListener('mousemove', event => {
            const cell = (event.target as HTMLElement).closest('td,th');
            if (cell && !this.dragging) {
                this.onCellMouseMove(cell as HTMLElement, event);
            }
        });
    }

    onCellMouseMove(cell: HTMLElement, event: MouseEvent) {
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

    updateMarkersTo(cell: HTMLElement, xPos: number, yPos: number) {
        const markers: MarkerDomRecord = this.getMarkers();
        const table = cell.closest('table') as HTMLElement;
        const tableRect = table.getBoundingClientRect();

        markers.x.style.left = xPos + 'px';
        markers.x.style.height = tableRect.height + 'px';
        markers.x.style.top = tableRect.top + 'px';

        markers.y.style.top = yPos + 'px';
        markers.y.style.left = tableRect.left + 'px';
        markers.y.style.width = tableRect.width + 'px';
    }

    getMarkers(): MarkerDomRecord {
        if (!this.markerDom) {
            this.markerDom = {
                x: el('div', {class: 'editor-table-marker editor-table-marker-column'}),
                y: el('div', {class: 'editor-table-marker editor-table-marker-row'}),
            }
            const wrapper = el('div', {
                class: 'editor-table-marker-wrap',
            }, [this.markerDom.x, this.markerDom.y]);
            this.editArea.after(wrapper);
            this.watchMarkerMouseDrags(wrapper);
        }

        return this.markerDom;
    }

    watchMarkerMouseDrags(wrapper: HTMLElement) {
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
                console.log('up', distance, marker, markerProp, _this.targetCell);
                const parentTable = _this.targetCell?.closest('table');

                if (markerProp === 'left' && _this.targetCell && parentTable) {
                    const cellIndex = _this.getTargetCellColumnIndex();
                    _this.editor.update(() => {
                        const table = $getNearestNodeFromDOMNode(parentTable);
                        if (table instanceof CustomTableNode) {
                            const originalWidth = $getTableColumnWidth(_this.editor, table, cellIndex);
                            const newWidth = Math.max(originalWidth + distance.x, 10);
                            $setTableColumnWidth(table, cellIndex, newWidth);
                        }
                    });
                }
            }
        });
    }

    getTargetCellColumnIndex(): number {
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


export function registerTableResizer(editor: LexicalEditor, editorArea: HTMLElement): (() => void) {
    const resizer = new TableResizer(editor, editorArea);

    // TODO - Strip/close down resizer
    return () => {};
}