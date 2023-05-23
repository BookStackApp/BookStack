import {
    EditorView, keymap, drawSelection, highlightActiveLine, dropCursor,
    rectangularSelection, lineNumbers, highlightActiveLineGutter,
} from '@codemirror/view';
import {bracketMatching} from '@codemirror/language';
import {
    defaultKeymap, history, historyKeymap, indentWithTab,
} from '@codemirror/commands';
import {Compartment, EditorState} from '@codemirror/state';
import {getTheme} from './themes';

/**
 * @param {Element} parentEl
 * @return {(Extension[]|{extension: Extension}|readonly Extension[])[]}
 */
function common(parentEl) {
    return [
        getTheme(parentEl),
        lineNumbers(),
        drawSelection(),
        dropCursor(),
        bracketMatching(),
        rectangularSelection(),
    ];
}

/**
 * @returns {({extension: Extension}|readonly Extension[])[]}
 */
function getDynamicActiveLineHighlighter() {
    const highlightingCompartment = new Compartment();
    const domEvents = {
        focus(event, view) {
            view.dispatch({
                effects: highlightingCompartment.reconfigure([
                    highlightActiveLineGutter(),
                    highlightActiveLine(),
                ]),
            });
        },
        blur(event, view) {
            view.dispatch({
                effects: highlightingCompartment.reconfigure([]),
            });
        },
    };

    return [
        highlightingCompartment.of([]),
        EditorView.domEventHandlers(domEvents),
    ];
}

/**
 * @param {Element} parentEl
 * @return {*[]}
 */
export function viewerExtensions(parentEl) {
    return [
        ...common(parentEl),
        getDynamicActiveLineHighlighter(),
        keymap.of([
            ...defaultKeymap,
        ]),
        EditorState.readOnly.of(true),
    ];
}

/**
 * @param {Element} parentEl
 * @return {*[]}
 */
export function editorExtensions(parentEl) {
    return [
        ...common(parentEl),
        highlightActiveLineGutter(),
        highlightActiveLine(),
        history(),
        keymap.of([
            ...defaultKeymap,
            ...historyKeymap,
            indentWithTab,
        ]),
        EditorView.lineWrapping,
    ];
}
