import {provide as provideShortcuts} from "./shortcuts";
import {debounce} from "../services/util";
import Clipboard from "../services/clipboard";

/**
 * Initiate the codemirror instance for the markdown editor.
 * @param {MarkdownEditor} editor
 * @returns {Promise<void>}
 */
export async function init(editor) {
    const Code = await window.importVersioned('code');
    const cm = Code.markdownEditor(editor.config.inputEl);

    // Will force to remain as ltr for now due to issues when HTML is in editor.
    cm.setOption('direction', 'ltr');
    // Register shortcuts
    cm.setOption('extraKeys', provideShortcuts(editor, Code.getMetaKey()));


    // Register codemirror events

    // Update data on content change
    cm.on('change', (instance, changeObj) => editor.actions.updateAndRender());

    // Handle scroll to sync display view
    const onScrollDebounced = debounce(editor.actions.syncDisplayPosition.bind(editor.actions), 100, false);
    let syncActive = editor.settings.get('scrollSync');
    editor.settings.onChange('scrollSync', val => syncActive = val);
    cm.on('scroll', instance => {
        if (syncActive) {
            onScrollDebounced(instance);
        }
    });

    // Handle image paste
    cm.on('paste', (cm, event) => {
        const clipboard = new Clipboard(event.clipboardData || event.dataTransfer);

        // Don't handle the event ourselves if no items exist of contains table-looking data
        if (!clipboard.hasItems() || clipboard.containsTabularData()) {
            return;
        }

        const images = clipboard.getImages();
        for (const image of images) {
            editor.actions.uploadImage(image);
        }
    });

    // Handle image & content drag n drop
    cm.on('drop', (cm, event) => {

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
            editor.actions.insertClipboardImages(clipboardImages);
        }

    });

    return cm;
}