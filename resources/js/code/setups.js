
import {EditorView, keymap, highlightSpecialChars, drawSelection, highlightActiveLine, dropCursor,
    rectangularSelection, lineNumbers, highlightActiveLineGutter} from "@codemirror/view"
import {syntaxHighlighting, bracketMatching} from "@codemirror/language"
import {defaultKeymap, history, historyKeymap} from "@codemirror/commands"
import {EditorState} from "@codemirror/state"

import {defaultLight} from "./themes";
import {getLanguageExtension} from "./languages";

export function viewer() {
    return [
        lineNumbers(),
        highlightActiveLineGutter(),
        highlightSpecialChars(),
        history(),
        drawSelection(),
        dropCursor(),
        syntaxHighlighting(defaultLight, {fallback: true}),
        bracketMatching(),
        rectangularSelection(),
        highlightActiveLine(),
        keymap.of([
            ...defaultKeymap,
            ...historyKeymap,
        ]),
        EditorState.readOnly.of(true),
    ];
}

export function editor(language) {
    return [
        lineNumbers(),
        highlightActiveLineGutter(),
        highlightSpecialChars(),
        history(),
        drawSelection(),
        dropCursor(),
        syntaxHighlighting(defaultLight, {fallback: true}),
        bracketMatching(),
        rectangularSelection(),
        highlightActiveLine(),
        keymap.of([
            ...defaultKeymap,
            ...historyKeymap,
        ]),
        getLanguageExtension(language, ''),
        EditorView.lineWrapping,
    ];
}