import {LexicalEditor} from "lexical";
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
}
