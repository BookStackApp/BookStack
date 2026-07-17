import {Clipboard} from "../services/clipboard";
import {MarkdownEditor} from "./index.mjs";
import {debounce} from "../services/util";


export type MarkdownEditorEventMap = Record<string, (event: any) => void>;

export function getMarkdownDomEventHandlers(editor: MarkdownEditor): MarkdownEditorEventMap {

    const onScrollDebounced = debounce(editor.actions.syncDisplayPosition.bind(editor.actions), 100, false);
    let syncActive = editor.settings.get('scrollSync');
    editor.settings.onChange('scrollSync', val => {
        syncActive = val;
    });

    return {
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
                editor.actions.insertTemplate(templateId, event);
            }

            const clipboard = new Clipboard(event.dataTransfer);
            const clipboardImages = clipboard.getImages();
            if (clipboardImages.length > 0) {
                event.stopPropagation();
                event.preventDefault();
                editor.actions.insertClipboardImages(clipboardImages, event);
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
}