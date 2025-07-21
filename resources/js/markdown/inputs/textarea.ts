import {MarkdownEditorInput, MarkdownEditorInputSelection} from "./interface";
import {MarkdownEditorShortcutMap} from "../shortcuts";
import {MarkdownEditorEventMap} from "../dom-handlers";


export class TextareaInput implements MarkdownEditorInput {

    protected input: HTMLTextAreaElement;
    protected shortcuts: MarkdownEditorShortcutMap;
    protected events: MarkdownEditorEventMap;

    constructor(input: HTMLTextAreaElement, shortcuts: MarkdownEditorShortcutMap, events: MarkdownEditorEventMap) {
        this.input = input;
        this.shortcuts = shortcuts;
        this.events = events;

        this.onKeyDown = this.onKeyDown.bind(this);
        this.configureListeners();
    }

    configureListeners(): void {
        // TODO - Teardown handling
        this.input.addEventListener('keydown', this.onKeyDown);

        for (const [name, listener] of Object.entries(this.events)) {
            this.input.addEventListener(name, listener);
        }
    }

    onKeyDown(e: KeyboardEvent) {
        const isApple = navigator.platform.startsWith("Mac") || navigator.platform === "iPhone";
        const keyParts = [
            e.shiftKey ? 'Shift' : null,
            isApple && e.metaKey ? 'Mod' : null,
            !isApple && e.ctrlKey ? 'Mod' : null,
            e.key,
        ];

        const keyString = keyParts.filter(Boolean).join('-');
        if (this.shortcuts[keyString]) {
            e.preventDefault();
            this.shortcuts[keyString]();
        }
    }

    appendText(text: string): void {
        this.input.value += `\n${text}`;
    }

    coordsToSelection(x: number, y: number): MarkdownEditorInputSelection {
        // TODO
        return this.getSelection();
    }

    focus(): void {
        this.input.focus();
    }

    getLineRangeFromPosition(position: number): MarkdownEditorInputSelection {
        const lines = this.getText().split('\n');
        let lineStart = 0;
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const newEnd = lineStart + line.length + 1;
            if (position < newEnd) {
                return {from: lineStart, to: newEnd};
            }
            lineStart = newEnd;
        }

        return {from: 0, to: 0};
    }

    getLineText(lineIndex: number): string {
        const text = this.getText();
        const lines = text.split("\n");
        return lines[lineIndex] || '';
    }

    getSelection(): MarkdownEditorInputSelection {
        return {from: this.input.selectionStart, to: this.input.selectionEnd};
    }

    getSelectionText(selection?: MarkdownEditorInputSelection): string {
        const text = this.getText();
        const range = selection || this.getSelection();
        return text.slice(range.from, range.to);
    }

    getText(): string {
        return this.input.value;
    }

    getTextAboveView(): string {
        const scrollTop = this.input.scrollTop;
        const computedStyles = window.getComputedStyle(this.input);
        const lines = this.getText().split('\n');
        const paddingTop = Number(computedStyles.paddingTop.replace('px', ''));
        const paddingBottom = Number(computedStyles.paddingBottom.replace('px', ''));

        const avgLineHeight = (this.input.scrollHeight - paddingBottom - paddingTop) / lines.length;
        const roughLinePos = Math.max(Math.floor((scrollTop - paddingTop) / avgLineHeight), 0);
        const linesAbove = this.getText().split('\n').slice(0, roughLinePos);
        return linesAbove.join('\n');
    }

    searchForLineContaining(text: string): MarkdownEditorInputSelection | null {
        const textPosition = this.getText().indexOf(text);
        if (textPosition > -1) {
            return this.getLineRangeFromPosition(textPosition);
        }

        return null;
    }

    setSelection(selection: MarkdownEditorInputSelection, scrollIntoView: boolean): void {
        this.input.selectionStart = selection.from;
        this.input.selectionEnd = selection.to;
    }

    setText(text: string, selection?: MarkdownEditorInputSelection): void {
        this.input.value = text;
        if (selection) {
            this.setSelection(selection, false);
        }
    }

    spliceText(from: number, to: number, newText: string, selection: Partial<MarkdownEditorInputSelection> | null): void {
        const text = this.getText();
        const updatedText = text.slice(0, from) + newText + text.slice(to);
        this.setText(updatedText);
        if (selection && selection.from) {
            const newSelection = {from: selection.from, to: selection.to || selection.from};
            this.setSelection(newSelection, false);
        }
    }
}