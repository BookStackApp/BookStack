import MarkdownView from "./editor/MarkdownView";
import ProseMirrorView from "./editor/ProseMirrorView";


const place = document.querySelector("#editor");
let view = new ProseMirrorView(place, document.getElementById('content').innerHTML);

const markdownToggle = document.getElementById('markdown-toggle');
markdownToggle.addEventListener('change', event => {
    const View = markdownToggle.checked ? MarkdownView : ProseMirrorView;
    if (view instanceof View) return
    const content = view.content
    console.log(content);
    view.destroy()
    view = new View(place, content)
    view.focus()
});