import {Component} from "./component";

export class DetailsHighlighter extends Component {

    setup() {
        this.container = this.$el;
        this.dealtWith = false;

        this.container.addEventListener('toggle', this.onToggle.bind(this));
    }

    onToggle() {
        if (this.dealtWith) return;

        if (this.container.querySelector('pre')) {
            window.importVersioned('code').then(Code => {
                Code.highlightWithin(this.container);
            });
        }
        this.dealtWith = true;
    }
}