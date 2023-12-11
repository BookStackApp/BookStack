import {Compartment, EditorState} from '@codemirror/state';
import {EditorView} from '@codemirror/view';
import {getLanguageExtension} from './languages';

const viewLangCompartments = new WeakMap();

/**
 * Create a new editor view.
 *
 * @param {String} usageType
 * @param {{parent: Element, doc: String, extensions: Array}} config
 * @returns {EditorView}
 */
export function createView(usageType, config) {
    const langCompartment = new Compartment();
    config.extensions.push(langCompartment.of([]));

    const commonEventData = {
        usage: usageType,
        editorViewConfig: config,
        libEditorView: EditorView,
        libEditorState: EditorState,
        libCompartment: Compartment,
    };

    // Emit a pre-init public event so the user can tweak the config before instance creation
    window.$events.emitPublic(config.parent, 'library-cm6::pre-init', commonEventData);

    const ev = new EditorView(config);
    viewLangCompartments.set(ev, langCompartment);

    // Emit a post-init public event so the user can gain a reference to the EditorView
    window.$events.emitPublic(config.parent, 'library-cm6::post-init', {editorView: ev, ...commonEventData});

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
