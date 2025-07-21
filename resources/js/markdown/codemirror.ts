import {provideKeyBindings} from './shortcuts';
import {debounce} from '../services/util';
import {Clipboard} from '../services/clipboard';
import {EditorView, ViewUpdate} from "@codemirror/view";
import {MarkdownEditor} from "./index.mjs";
import {CodeModule} from "../global";

/**
 * Initiate the codemirror instance for the MarkDown editor.
 */
export function init(editor: MarkdownEditor, Code: CodeModule): EditorView {
    function onViewUpdate(v: ViewUpdate) {
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
        scroll: (event: Event) => syncActive && onScrollDebounced(event),
        // Handle image & content drag n drop
        drop: (event: DragEvent) => {
            if (!event.dataTransfer) {
                return;
            }

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
        dragover: (event: DragEvent) => {
            event.preventDefault();
        },
        // Handle image paste
        paste: (event: ClipboardEvent) => {
            if (!event.clipboardData) {
                return;
            }

            const clipboard = new Clipboard(event.clipboardData);

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

    // Add editor view to the window for easy access/debugging.
    // Not part of official API/Docs
    // @ts-ignore
    window.mdEditorView = cm;

    return cm;
}
