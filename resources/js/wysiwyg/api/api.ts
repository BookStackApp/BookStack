import {EditorApiUiModule} from "./ui";
import {EditorUiContext} from "../ui/framework/core";
import {EditorApiContentModule} from "./content";

export class EditorApi {

    public ui: EditorApiUiModule;
    public content: EditorApiContentModule;

    constructor(context: EditorUiContext) {
        this.ui = new EditorApiUiModule(context);
        this.content = new EditorApiContentModule(context);
    }
}