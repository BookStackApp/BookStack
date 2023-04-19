import {Component} from './component';

export class CodeHighlighter extends Component {

    setup() {
        const container = this.$el;

        const codeBlocks = container.querySelectorAll('pre');
        if (codeBlocks.length > 0) {
            window.importVersioned('code').then(Code => {
                Code.highlightWithin(container);
            });
        }
    }

}
