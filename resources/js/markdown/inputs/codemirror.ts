import {MarkdownEditorInput, MarkdownEditorInputSelection} from "./interface";
import {EditorView} from "@codemirror/view";
import {ChangeSpec, TransactionSpec} from "@codemirror/state";


export class CodemirrorInput implements MarkdownEditorInput {
    protected cm: EditorView;

    constructor(cm: EditorView) {
        this.cm = cm;
    }

    teardown(): void {
        this.cm.destroy();
    }

    focus(): void {
        if (!this.cm.hasFocus) {
            this.cm.focus();
        }
    }

    getSelection(): MarkdownEditorInputSelection {
        return this.cm.state.selection.main;
    }

    getSelectionText(selection?: MarkdownEditorInputSelection): string {
        selection = selection || this.getSelection();
        return this.cm.state.sliceDoc(selection.from, selection.to);
    }

    setSelection(selection: MarkdownEditorInputSelection, scrollIntoView: boolean = false) {
        this.cm.dispatch({
            selection: {anchor: selection.from, head: selection.to},
            scrollIntoView,
        });
    }

    getText(): string {
        return this.cm.state.doc.toString();
    }

    getTextAboveView(): string {
        const blockInfo = this.cm.lineBlockAtHeight(this.cm.scrollDOM.scrollTop);
        return this.cm.state.sliceDoc(0, blockInfo.from);
    }

    setText(text: string, selection?: MarkdownEditorInputSelection) {
        selection = selection || this.getSelection();
        const newDoc = this.cm.state.toText(text);
        const newSelectFrom = Math.min(selection.from, newDoc.length);
        const scrollTop = this.cm.scrollDOM.scrollTop;
        this.dispatchChange(0, this.cm.state.doc.length, text, newSelectFrom);
        this.focus();
        window.requestAnimationFrame(() => {
            this.cm.scrollDOM.scrollTop = scrollTop;
        });
    }

    spliceText(from: number, to: number, newText: string, selection: Partial<MarkdownEditorInputSelection> | null = null) {
        const end = (selection?.from === selection?.to) ? null : selection?.to;
        this.dispatchChange(from, to, newText, selection?.from, end)
    }

    appendText(text: string) {
        const end = this.cm.state.doc.length;
        this.dispatchChange(end, end, `\n${text}`);
    }

    getLineText(lineIndex: number = -1): string {
        const index = lineIndex > -1 ? lineIndex : this.getSelection().from;
        return this.cm.state.doc.lineAt(index).text;
    }

    eventToPosition(event: MouseEvent): MarkdownEditorInputSelection {
        const cursorPos = this.cm.posAtCoords({x: event.screenX, y: event.screenY}, false);
        return {from: cursorPos, to: cursorPos};
    }

    getLineRangeFromPosition(position: number): MarkdownEditorInputSelection {
        const line = this.cm.state.doc.lineAt(position);
        return {from: line.from, to: line.to};
    }

    searchForLineContaining(text: string): MarkdownEditorInputSelection | null {
        const docText = this.cm.state.doc;
        let lineCount = 1;
        let scrollToLine = -1;
        for (const line of docText.iterLines()) {
            if (line.includes(text)) {
                scrollToLine = lineCount;
                break;
            }
            lineCount += 1;
        }

        if (scrollToLine === -1) {
            return null;
        }

        const line = docText.line(scrollToLine);
        return {from: line.from, to: line.to};
    }

    /**
     * Dispatch changes to the editor.
     */
    protected dispatchChange(from: number, to: number|null = null, text: string|null = null, selectFrom: number|null = null, selectTo: number|null = null): void {
        const change: ChangeSpec = {from};
        if (to) {
            change.to = to;
        }
        if (text) {
            change.insert = text;
        }
        const tr: TransactionSpec = {changes: change};

        if (selectFrom) {
            tr.selection = {anchor: selectFrom};
            if (selectTo) {
                tr.selection.head = selectTo;
            }
        }

        this.cm.dispatch(tr);
    }

}