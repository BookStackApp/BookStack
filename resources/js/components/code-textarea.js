/**
 * A simple component to render a code editor within the textarea
 * this exists upon.
 * @extends {Component}
 */
class CodeTextarea {

    async setup() {
        const mode = this.$opts.mode;
        const Code = await window.importVersioned('code');
        Code.inlineEditor(this.$el, mode);
    }

}

export default CodeTextarea;