import {MarkdownEditorInput, MarkdownEditorInputSelection} from "./interface";
import {MarkdownEditorShortcutMap} from "../shortcuts";
import {MarkdownEditorEventMap} from "../dom-handlers";
import {debounce} from "../../services/util";

type UndoStackEntry = {
    content: string;
    selection: MarkdownEditorInputSelection;
}

class UndoStack {
    protected onChangeDebounced: (callback: () => UndoStackEntry) => void;

    protected stack: UndoStackEntry[] = [];
    protected pointer: number = -1;
    protected lastActionTime: number = 0;

    constructor() {
        this.onChangeDebounced = debounce(this.onChange, 1000, false);
    }

    undo(): UndoStackEntry|null {
        if (this.pointer < 1) {
            return null;
        }

        this.lastActionTime = Date.now();
        this.pointer -= 1;
        return this.stack[this.pointer];
    }

    redo(): UndoStackEntry|null {
        const atEnd = this.pointer === this.stack.length - 1;
        if (atEnd) {
            return null;
        }

        this.lastActionTime = Date.now();
        this.pointer++;
        return this.stack[this.pointer];
    }

    push(getValueCallback: () => UndoStackEntry): void {
        // Ignore changes made via undo/redo actions
        if (Date.now() - this.lastActionTime < 100) {
            return;
        }

        this.onChangeDebounced(getValueCallback);
    }

    protected onChange(getValueCallback: () => UndoStackEntry) {
        // Trim the end of the stack from the pointer since we're branching away
        if (this.pointer !== this.stack.length - 1) {
            this.stack = this.stack.slice(0, this.pointer)
        }

        this.stack.push(getValueCallback());

        // Limit stack size
        if (this.stack.length > 50) {
            this.stack = this.stack.slice(this.stack.length - 50);
        }

        this.pointer = this.stack.length - 1;
    }
}

export class TextareaInput implements MarkdownEditorInput {

    protected input: HTMLTextAreaElement;
    protected shortcuts: MarkdownEditorShortcutMap;
    protected events: MarkdownEditorEventMap;
    protected onChange: () => void;
    protected eventController = new AbortController();
    protected undoStack = new UndoStack();

    protected textSizeCache: {x: number; y: number}|null = null;

    constructor(
        input: HTMLTextAreaElement,
        shortcuts: MarkdownEditorShortcutMap,
        events: MarkdownEditorEventMap,
        onChange: () => void
    ) {
        this.input = input;
        this.shortcuts = shortcuts;
        this.events = events;
        this.onChange = onChange;

        this.onKeyDown = this.onKeyDown.bind(this);
        this.configureLocalShortcuts();
        this.configureListeners();

        this.input.style.removeProperty("display");
        this.undoStack.push(() => ({content: this.getText(), selection: this.getSelection()}));
    }

    teardown() {
        this.eventController.abort('teardown');
    }

    configureLocalShortcuts(): void {
        this.shortcuts['Mod-z'] = () => {
            const undoEntry = this.undoStack.undo();
            if (undoEntry) {
                this.setText(undoEntry.content);
                this.setSelection(undoEntry.selection, false);
            }
        };
        this.shortcuts['Mod-y'] = () => {
            const redoContent = this.undoStack.redo();
            if (redoContent) {
                this.setText(redoContent.content);
                this.setSelection(redoContent.selection, false);
            }
        }
    }

    configureListeners(): void {
        // Keyboard shortcuts
        this.input.addEventListener('keydown', this.onKeyDown, {signal: this.eventController.signal});

        // Shared event listeners
        for (const [name, listener] of Object.entries(this.events)) {
            this.input.addEventListener(name, listener, {signal: this.eventController.signal});
        }

        // Input change handling
        this.input.addEventListener('input', () => {
            this.onChange();
            this.undoStack.push(() => ({content: this.input.value, selection: this.getSelection()}));
        }, {signal: this.eventController.signal});
    }

    onKeyDown(e: KeyboardEvent) {
        const isApple = navigator.platform.startsWith("Mac") || navigator.platform === "iPhone";
        const key = e.key.length > 1 ? e.key : e.key.toLowerCase();
        const keyParts = [
            e.shiftKey ? 'Shift' : null,
            isApple && e.metaKey ? 'Mod' : null,
            !isApple && e.ctrlKey ? 'Mod' : null,
            key,
        ];

        const keyString = keyParts.filter(Boolean).join('-');
        if (this.shortcuts[keyString]) {
            e.preventDefault();
            this.shortcuts[keyString]();
        }
    }

    appendText(text: string): void {
        this.input.value += `\n${text}`;
        this.input.dispatchEvent(new Event('input'));
    }

    eventToPosition(event: MouseEvent): MarkdownEditorInputSelection {
        const eventCoords = this.mouseEventToTextRelativeCoords(event);
        return this.inputPositionToSelection(eventCoords.x, eventCoords.y);
    }

    focus(): void {
        this.input.focus();
    }

    getLineRangeFromPosition(position: number): MarkdownEditorInputSelection {
        const lines = this.getText().split('\n');
        let lineStart = 0;
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const lineEnd = lineStart + line.length;
            if (position <= lineEnd) {
                return {from: lineStart, to: lineEnd};
            }
            lineStart = lineEnd + 1;
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
        const selection = this.inputPositionToSelection(0, scrollTop);
        return this.getSelectionText({from: 0, to: selection.to});
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
        this.input.dispatchEvent(new Event('input'));
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

    protected measureTextSize(): {x: number; y: number} {
        if (this.textSizeCache) {
            return this.textSizeCache;
        }

        const el = document.createElement("div");
        el.textContent = `a\nb`;
        const inputStyles = window.getComputedStyle(this.input)
        el.style.font = inputStyles.font;
        el.style.lineHeight = inputStyles.lineHeight;
        el.style.padding = '0px';
        el.style.display = 'inline-block';
        el.style.visibility = 'hidden';
        el.style.position = 'absolute';
        el.style.whiteSpace = 'pre';
        this.input.after(el);

        const bounds = el.getBoundingClientRect();
        el.remove();
        this.textSizeCache = {
            x: bounds.width,
            y: bounds.height / 2,
        };
        return this.textSizeCache;
    }

    protected measureLineCharCount(textWidth: number): number {
        const inputStyles = window.getComputedStyle(this.input);
        const paddingLeft = Number(inputStyles.paddingLeft.replace('px', ''));
        const paddingRight = Number(inputStyles.paddingRight.replace('px', ''));
        const width = Number(inputStyles.width.replace('px', ''));
        const textSpace = width - (paddingLeft + paddingRight);

        return Math.floor(textSpace / textWidth);
    }

    protected mouseEventToTextRelativeCoords(event: MouseEvent): {x: number; y: number} {
        const inputBounds = this.input.getBoundingClientRect();
        const inputStyles = window.getComputedStyle(this.input);
        const paddingTop = Number(inputStyles.paddingTop.replace('px', ''));
        const paddingLeft = Number(inputStyles.paddingLeft.replace('px', ''));

        const xPos = Math.max(event.clientX - (inputBounds.left + paddingLeft), 0);
        const yPos = Math.max((event.clientY - (inputBounds.top + paddingTop)) + this.input.scrollTop, 0);

        return {x: xPos, y: yPos};
    }

    protected inputPositionToSelection(x: number, y: number): MarkdownEditorInputSelection {
        const textSize = this.measureTextSize();
        const lineWidth = this.measureLineCharCount(textSize.x);

        const lines = this.getText().split('\n');

        let currY = 0;
        let currPos = 0;
        for (const line of lines) {
            let linePos = 0;
            const wrapCount = Math.max(Math.ceil(line.length / lineWidth), 1);
            for (let i = 0; i < wrapCount; i++) {
                currY += textSize.y;
                if (currY > y) {
                    const targetX = Math.floor(x / textSize.x);
                    const maxPos = Math.min(currPos + linePos + targetX, currPos + line.length);
                    return {from: maxPos, to: maxPos};
                }

                linePos += lineWidth;
            }

            currPos += line.length + 1;
        }

        return this.getSelection();
    }
}