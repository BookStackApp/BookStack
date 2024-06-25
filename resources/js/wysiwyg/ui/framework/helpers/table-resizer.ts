import {LexicalEditor} from "lexical";
import {el} from "../../../helpers";
import {MouseDragTracker, MouseDragTrackerDistance} from "./mouse-drag-tracker";

type MarkerDomRecord = {x: HTMLElement, y: HTMLElement};

class TableResizer {
    protected editor: LexicalEditor;
    protected editArea: HTMLElement;
    protected markerDom: MarkerDomRecord|null = null;
    protected mouseTracker: MouseDragTracker|null = null;

    constructor(editor: LexicalEditor, editArea: HTMLElement) {
        this.editor = editor;
        this.editArea = editArea;
        this.setupListeners();
    }

    setupListeners() {
        this.editArea.addEventListener('mousemove', event => {
            const cell = (event.target as HTMLElement).closest('td,th');
            if (cell) {
                this.onCellMouseMove(cell as HTMLElement, event);
            }
        });
    }

    onCellMouseMove(cell: HTMLElement, event: MouseEvent) {
        const rect = cell.getBoundingClientRect();
        const midX = rect.left + (rect.width / 2);
        const midY = rect.top + (rect.height / 2);
        const xMarkerPos = event.clientX <= midX ? rect.left : rect.right;
        const yMarkerPos = event.clientY <= midY ? rect.top : rect.bottom;
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
        this.mouseTracker = new MouseDragTracker(wrapper, '.editor-table-marker', {
            up(event: MouseEvent, marker: HTMLElement, distance: MouseDragTrackerDistance) {
                console.log('up', distance, marker);
                // TODO - Update row/column for distance
            }
        });
    }
}


export function registerTableResizer(editor: LexicalEditor, editorArea: HTMLElement): (() => void) {
    const resizer = new TableResizer(editor, editorArea);

    // TODO - Strip/close down resizer
    return () => {};
}