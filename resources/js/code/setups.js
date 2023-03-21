
import {keymap, highlightSpecialChars, drawSelection, highlightActiveLine, dropCursor,
    rectangularSelection, lineNumbers, highlightActiveLineGutter} from "@codemirror/view"
import {defaultHighlightStyle, syntaxHighlighting, bracketMatching,
     foldKeymap} from "@codemirror/language"
import {defaultKeymap, history, historyKeymap} from "@codemirror/commands"
import {EditorState} from "@codemirror/state"

import {defaultLight} from "./themes";

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
            ...foldKeymap,
        ]),
        EditorState.readOnly.of(true),
    ];
}