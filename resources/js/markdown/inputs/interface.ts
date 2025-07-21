
export interface MarkdownEditorInputSelection {
    from: number;
    to: number;
}

export interface MarkdownEditorInput {
    /**
     * Focus on the editor.
     */
    focus(): void;

    /**
     * Get the current selection range.
     */
    getSelection(): MarkdownEditorInputSelection;

    /**
     * Get the text of the given (or current) selection range.
     */
    getSelectionText(selection: MarkdownEditorInputSelection|null = null): string;

    /**
     * Set the selection range of the editor.
     */
    setSelection(selection: MarkdownEditorInputSelection, scrollIntoView: boolean = false): void;

    /**
     * Get the full text of the input.
     */
    getText(): string;

    /**
     * Get just the text which is above (out) the current view range.
     * This is used for position estimation.
     */
    getTextAboveView(): string;

    /**
     * Set the full text of the input.
     * Optionally can provide a selection to restore after setting text.
     */
    setText(text: string, selection: MarkdownEditorInputSelection|null = null): void;

    /**
     * Splice in/out text within the input.
     * Optionally can provide a selection to restore after setting text.
     */
    spliceText(from: number, to: number, newText: string, selection: MarkdownEditorInputSelection|null = null): void;

    /**
     * Append text to the end of the editor.
     */
    appendText(text: string): void;

    /**
     * Get the text of the given line number otherwise the text
     * of the current selected line.
     */
    getLineText(lineIndex:number = -1): string;

    /**
     * Wrap the current line in the given start/end contents.
     */
    wrapLine(start: string, end: string): void;

    /**
     * Convert the given screen coords to a selection position within the input.
     */
    coordsToSelection(x: number, y: number): MarkdownEditorInputSelection;
}