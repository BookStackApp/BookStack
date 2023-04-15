import {getLanguageExtension} from "./languages"
import {HighlightStyle, syntaxHighlighting} from "@codemirror/language";
import {Compartment} from "@codemirror/state"
import {EditorView} from "@codemirror/view"
import {oneDarkTheme, oneDarkHighlightStyle} from "@codemirror/theme-one-dark"
import {tags} from "@lezer/highlight"

const viewLangCompartments = new WeakMap();

/**
 * Create a new editor view.
 *
 * @param {{parent: Element, doc: String, extensions: Array}} config
 * @returns {EditorView}
 */
export function createView(config) {
    const langCompartment = new Compartment();
    config.extensions.push(langCompartment.of([]));
    config.extensions.push(getTheme(config.parent));

    const ev = new EditorView(config);

    viewLangCompartments.set(ev, langCompartment);

    return ev;
}

/**
 * Get the theme extension to use for editor view instance.
 * @returns {Extension[]}
 */
function getTheme(viewParentEl) {
    const darkMode = document.documentElement.classList.contains('dark-mode');
    let viewTheme = darkMode ? oneDarkTheme : [];
    let highlightStyle = darkMode ? oneDarkHighlightStyle : null;

    const eventData = {
        darkModeActive: darkMode,
        registerViewTheme(builder) {
            const spec = builder();
            if (spec) {
                viewTheme = EditorView.theme(spec);
            }
        },
        registerHighlightStyle(builder) {
            const tagStyles = builder(tags) || [];
            console.log('called', tagStyles);
            if (tagStyles.length) {
                highlightStyle = HighlightStyle.define(tagStyles);
            }
        }
    };

    window.$events.emitPublic(viewParentEl, 'library-cm6::configure-theme', eventData);

    return [viewTheme, highlightStyle ? syntaxHighlighting(highlightStyle) : []];
}

/**
 * Set the language mode of an EditorView.
 *
 * @param {EditorView} ev
 * @param {string} modeSuggestion
 * @param {string} content
 */
export async function updateViewLanguage(ev, modeSuggestion, content) {
    const compartment = viewLangCompartments.get(ev);
    const language = await getLanguageExtension(modeSuggestion, content);

    ev.dispatch({
        effects: compartment.reconfigure(language ? language : [])
    })
}