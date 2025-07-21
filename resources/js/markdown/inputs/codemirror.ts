import {MarkdownEditorInput, MarkdownEditorInputSelection} from "./interface";
import {MarkdownEditor} from "../index.mjs";
import {EditorView} from "@codemirror/view";
import {ChangeSpec, SelectionRange, TransactionSpec} from "@codemirror/state";


export class CodemirrorInput implements MarkdownEditorInput {

    protected editor: MarkdownEditor;
    protected cm: EditorView;

    constructor(cm: EditorView) {
        this.cm = cm;
    }

    focus(): void {
        if (!this.editor.cm.hasFocus) {
            this.editor.cm.focus();
        }
    }

    getSelection(): MarkdownEditorInputSelection {
        return this.editor.cm.state.selection.main;
    }

    getSelectionText(selection: MarkdownEditorInputSelection|null = null): string {
        selection = selection || this.getSelection();
        return this.editor.cm.state.sliceDoc(selection.from, selection.to);
    }

    setSelection(selection: MarkdownEditorInputSelection, scrollIntoView: boolean = false) {
        this.editor.cm.dispatch({
            selection: {anchor: selection.from, head: selection.to},
            scrollIntoView,
        });
    }

    getText(): string {
        return this.editor.cm.state.doc.toString();
    }

    getTextAboveView(): string {
        const blockInfo = this.editor.cm.lineBlockAtHeight(scrollEl.scrollTop);
        return this.editor.cm.state.sliceDoc(0, blockInfo.from);
    }

    setText(text: string, selection: MarkdownEditorInputSelection | null = null) {
        selection = selection || this.getSelection();
        const newDoc = this.editor.cm.state.toText(text);
        const newSelectFrom = Math.min(selection.from, newDoc.length);
        const scrollTop = this.editor.cm.scrollDOM.scrollTop;
        this.dispatchChange(0, this.editor.cm.state.doc.length, text, newSelectFrom);
        this.focus();
        window.requestAnimationFrame(() => {
            this.editor.cm.scrollDOM.scrollTop = scrollTop;
        });
    }

    spliceText(from: number, to: number, newText: string, selection: MarkdownEditorInputSelection | null = null) {
        const end = (selection?.from === selection?.to) ? null : selection?.to;
        this.dispatchChange(from, to, newText, selection?.from, end)
    }

    appendText(text: string) {
        const end = this.editor.cm.state.doc.length;
        this.dispatchChange(end, end, `\n${text}`);
    }

    getLineText(lineIndex: number = -1): string {
        const index = lineIndex > -1 ? lineIndex : this.getSelection().from;
        return this.editor.cm.state.doc.lineAt(index).text;
    }

    wrapLine(start: string, end: string) {
        const selectionRange = this.getSelection();
        const line = this.editor.cm.state.doc.lineAt(selectionRange.from);
        const lineContent = line.text;
        let newLineContent;
        let lineOffset = 0;

        if (lineContent.startsWith(start) && lineContent.endsWith(end)) {
            newLineContent = lineContent.slice(start.length, lineContent.length - end.length);
            lineOffset = -(start.length);
        } else {
            newLineContent = `${start}${lineContent}${end}`;
            lineOffset = start.length;
        }

        this.dispatchChange(line.from, line.to, newLineContent, selectionRange.from + lineOffset);
    }

    coordsToSelection(x: number, y: number): MarkdownEditorInputSelection {
        const cursorPos = this.editor.cm.posAtCoords({x, y}, false);
        return {from: cursorPos, to: cursorPos};
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