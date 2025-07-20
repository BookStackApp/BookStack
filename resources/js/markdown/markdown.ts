import MarkdownIt from 'markdown-it';
// @ts-ignore
import mdTasksLists from 'markdown-it-task-lists';

export class Markdown {
    protected renderer: MarkdownIt;

    constructor() {
        this.renderer = new MarkdownIt({html: true});
        this.renderer.use(mdTasksLists, {label: true});
    }

    /**
     * Get the front-end render used to convert Markdown to HTML.
     */
    getRenderer(): MarkdownIt {
        return this.renderer;
    }

    /**
     * Convert the given Markdown to HTML.
     */
    render(markdown: string): string {
        return this.renderer.render(markdown);
    }

}
