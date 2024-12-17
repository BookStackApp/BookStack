import {EditorUiElement} from "../core";
import {el} from "../../../utils/dom";

export class ExternalContent extends EditorUiElement {

    /**
     * The URL for HTML to be loaded from.
     */
    protected url: string = '';

    constructor(url: string) {
        super();
        this.url = url;
    }

    buildDOM(): HTMLElement {
        const wrapper = el('div', {
            class: 'editor-external-content',
        });

        window.$http.get(this.url).then(resp => {
            if (typeof resp.data === 'string') {
                wrapper.innerHTML = resp.data;
            }
        });

        return wrapper;
    }
}
