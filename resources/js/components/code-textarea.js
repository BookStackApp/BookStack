/**
 * A simple component to render a code editor within the textarea
 * this exists upon.
 */
import {Component} from "./component";

export class CodeTextarea extends Component {

    async setup() {
        const mode = this.$opts.mode;
        const Code = await window.importVersioned('code');
        Code.inlineEditor(this.$el, mode);
    }

}