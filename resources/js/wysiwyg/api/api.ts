import {EditorApiUiModule} from "./ui";
import {EditorUiContext} from "../ui/framework/core";

export class EditorApi {

    public ui: EditorApiUiModule;

    constructor(context: EditorUiContext) {
        this.ui = new EditorApiUiModule(context);
    }
}