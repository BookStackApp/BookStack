import MarkdownIt from 'markdown-it';
import mdTasksLists from 'markdown-it-task-lists';

export class Markdown {

    constructor() {
        this.renderer = new MarkdownIt({html: true});
        this.renderer.use(mdTasksLists, {label: true});
    }

    /**
     * Get the front-end render used to convert markdown to HTML.
     * @returns {MarkdownIt}
     */
    getRenderer() {
        return this.renderer;
    }

    /**
     * Convert the given Markdown to HTML.
     * @param {String} markdown
     * @returns {String}
     */
    render(markdown) {
        return this.renderer.render(markdown);
    }

}
