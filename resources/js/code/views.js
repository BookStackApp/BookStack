import {Compartment} from '@codemirror/state';
import {EditorView} from '@codemirror/view';
import {getLanguageExtension} from './languages';

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

    const ev = new EditorView(config);

    viewLangCompartments.set(ev, langCompartment);

    return ev;
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
        effects: compartment.reconfigure(language || []),
    });
}
