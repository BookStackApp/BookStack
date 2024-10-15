import {provideKeyBindings} from './shortcuts';
import {debounce} from '../services/util.ts';
import {Clipboard} from '../services/clipboard.ts';

/**
 * Initiate the codemirror instance for the markdown editor.
 * @param {MarkdownEditor} editor
 * @returns {Promise<void>}
 */
export async function init(editor) {
    const Code = await window.importVersioned('code');

    /**
     * @param {ViewUpdate} v
     */
    function onViewUpdate(v) {
        if (v.docChanged) {
            editor.actions.updateAndRender();
        }
    }

    const onScrollDebounced = debounce(editor.actions.syncDisplayPosition.bind(editor.actions), 100, false);
    let syncActive = editor.settings.get('scrollSync');
    editor.settings.onChange('scrollSync', val => {
        syncActive = val;
    });

    const domEventHandlers = {
        // Handle scroll to sync display view
        scroll: event => syncActive && onScrollDebounced(event),
        // Handle image & content drag n drop
        drop: event => {
            const templateId = event.dataTransfer.getData('bookstack/template');
            if (templateId) {
                event.preventDefault();
                editor.actions.insertTemplate(templateId, event.pageX, event.pageY);
            }

            const clipboard = new Clipboard(event.dataTransfer);
            const clipboardImages = clipboard.getImages();
            if (clipboardImages.length > 0) {
                event.stopPropagation();
                event.preventDefault();
                editor.actions.insertClipboardImages(clipboardImages, event.pageX, event.pageY);
            }
        },
        // Handle dragover event to allow as drop-target in chrome
        dragover: event => {
            event.preventDefault();
        },
        // Handle image paste
        paste: event => {
            const clipboard = new Clipboard(event.clipboardData || event.dataTransfer);

            // Don't handle the event ourselves if no items exist of contains table-looking data
            if (!clipboard.hasItems() || clipboard.containsTabularData()) {
                return;
            }

            const images = clipboard.getImages();
            for (const image of images) {
                editor.actions.uploadImage(image);
            }
        },
    };

    const cm = Code.markdownEditor(
        editor.config.inputEl,
        onViewUpdate,
        domEventHandlers,
        provideKeyBindings(editor),
    );

    // Add editor view to window for easy access/debugging.
    // Not part of official API/Docs
    window.mdEditorView = cm;

    return cm;
}
