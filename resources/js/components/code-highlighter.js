import Code from "../services/code"
class CodeHighlighter {

    constructor(elem) {
        Code.highlightWithin(elem);
    }

}

export default CodeHighlighter;