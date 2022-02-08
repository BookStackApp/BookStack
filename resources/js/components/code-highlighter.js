class CodeHighlighter {

    constructor(elem) {
        const codeBlocks = elem.querySelectorAll('pre');
        if (codeBlocks.length > 0) {
            window.importVersioned('code').then(Code => {
               Code.highlightWithin(elem);
            });
        }
    }

}

export default CodeHighlighter;