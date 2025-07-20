import {MarkdownEditor} from "./index.mjs";

export interface HtmlOrMarkdown {
    html: string;
    markdown: string;
}

function getContentToInsert({html, markdown}: {html: string, markdown: string}): string {
    return markdown || html;
}

export function listenToCommonEvents(editor: MarkdownEditor): void {
    window.$events.listen('editor::replace', (eventContent: HtmlOrMarkdown) => {
        const markdown = getContentToInsert(eventContent);
        editor.actions.replaceContent(markdown);
    });

    window.$events.listen('editor::append', (eventContent: HtmlOrMarkdown) => {
        const markdown = getContentToInsert(eventContent);
        editor.actions.appendContent(markdown);
    });

    window.$events.listen('editor::prepend', (eventContent: HtmlOrMarkdown) => {
        const markdown = getContentToInsert(eventContent);
        editor.actions.prependContent(markdown);
    });

    window.$events.listen('editor::insert', (eventContent: HtmlOrMarkdown) => {
        const markdown = getContentToInsert(eventContent);
        editor.actions.insertContent(markdown);
    });

    window.$events.listen('editor::focus', () => {
        editor.actions.focus();
    });
}
