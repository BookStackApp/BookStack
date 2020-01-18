import Code from "../services/code"
class DetailsHighlighter {

    constructor(elem) {
        this.elem = elem;
        this.dealtWith = false;
        elem.addEventListener('toggle', this.onToggle.bind(this));
    }

    onToggle() {
        if (this.dealtWith) return;

        Code.highlightWithin(this.elem);
        this.dealtWith = true;
    }
}

export default DetailsHighlighter;