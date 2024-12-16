import {$getSelection, LexicalEditor} from "lexical";
import {
    appendHtmlToEditor,
    focusEditor,
    insertHtmlIntoEditor,
    prependHtmlToEditor,
    setEditorContentFromHtml
} from "../utils/actions";

type EditorEventContent = {
    html: string;
    markdown: string;
};

function getContentToInsert(eventContent: EditorEventContent): string {
    return eventContent.html || '';
}

export function listen(editor: LexicalEditor): void {
    window.$events.listen<EditorEventContent>('editor::replace', eventContent => {
        const html = getContentToInsert(eventContent);
        setEditorContentFromHtml(editor, html);
    });

    window.$events.listen<EditorEventContent>('editor::append', eventContent => {
        const html = getContentToInsert(eventContent);
        appendHtmlToEditor(editor, html);
    });

    window.$events.listen<EditorEventContent>('editor::prepend', eventContent => {
        const html = getContentToInsert(eventContent);
        prependHtmlToEditor(editor, html);
    });

    window.$events.listen<EditorEventContent>('editor::insert', eventContent => {
        const html = getContentToInsert(eventContent);
        insertHtmlIntoEditor(editor, html);
    });

    window.$events.listen<EditorEventContent>('editor::focus', () => {
        focusEditor(editor);
    });

    let changeFromLoading = true;
    editor.registerUpdateListener(({dirtyElements, dirtyLeaves, editorState, prevEditorState}) => {
        // Emit change event to component system (for draft detection) on actual user content change
        if (dirtyElements.size > 0 || dirtyLeaves.size > 0) {
            if (changeFromLoading) {
                changeFromLoading = false;
            } else {
                window.$events.emit('editor-html-change', '');
            }
        }
    });
}
