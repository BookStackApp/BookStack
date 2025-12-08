import {LexicalEditor, TextNode} from "lexical";


export function registerMentions(editor: LexicalEditor): () => void {

    const unregisterTransform = editor.registerNodeTransform(TextNode, (node: TextNode) =>{
        console.log(node);
        // TODO - If last character is @, show autocomplete selector list of users.
          // Filter list by any extra characters entered.
          // On enter, replace with name mention element.
          // On space/escape, hide autocomplete list.
    });

    return (): void => {
        unregisterTransform();
    };
}