import {EditorContainerUiElement} from "../core";
import {el} from "../../../utils/dom";
import {EditorFormField} from "../forms";
import {$getAllNodesOfType} from "../../../utils/nodes";
import {uniqueIdSmall} from "../../../../services/util";
import {$isHeadingNode, HeadingNode} from "@lexical/rich-text/LexicalHeadingNode";

export class LinkField extends EditorContainerUiElement {
    protected input: EditorFormField;
    protected headerMap = new Map<string, HeadingNode>();

    constructor(input: EditorFormField) {
        super([input]);

        this.input = input;
    }

    buildDOM(): HTMLElement {
        const listId = 'editor-form-datalist-' + this.input.getName() + '-' + Date.now();
        const inputOuterDOM = this.input.getDOMElement();
        const inputFieldDOM = inputOuterDOM.querySelector('input');
        inputFieldDOM?.setAttribute('list', listId);
        inputFieldDOM?.setAttribute('autocomplete', 'off');
        const datalist = el('datalist', {id: listId});

        const container = el('div', {
            class: 'editor-link-field-container',
        }, [inputOuterDOM, datalist]);

        inputFieldDOM?.addEventListener('focusin', () => {
            this.updateDataList(datalist);
        });

        inputFieldDOM?.addEventListener('input', () => {
            const value = inputFieldDOM.value;
            const header = this.headerMap.get(value);
            if (header) {
                this.updateFormFromHeader(header);
            }
        });

        return container;
    }

    updateFormFromHeader(header: HeadingNode) {
        this.getHeaderIdAndText(header).then(({id, text}) => {
            const modal =  this.getContext().manager.getActiveModal('link');
            if (modal) {
                modal.getForm().setValues({
                    url: `#${id}`,
                    text: text,
                    title: text,
                });
            }
        });
    }

    getHeaderIdAndText(header: HeadingNode): Promise<{id: string, text: string}> {
        return new Promise((res) => {
            this.getContext().editor.update(() => {
                let id = header.getId();
                if (!id) {
                    id = 'header-' + uniqueIdSmall();
                    header.setId(id);
                }

                const text = header.getTextContent();
                res({id, text});
            });
        });
    }

    updateDataList(listEl: HTMLElement) {
        this.getContext().editor.getEditorState().read(() => {
            const headers = $getAllNodesOfType($isHeadingNode) as HeadingNode[];

            this.headerMap.clear();
            const listEls: HTMLElement[] = [];

            for (const header of headers) {
                const key = 'header-' + header.getKey();
                this.headerMap.set(key, header);
                listEls.push(el('option', {
                    value: key,
                    label: header.getTextContent().substring(0, 54),
                }));
            }

            listEl.innerHTML = '';
            listEl.append(...listEls);
        });
    }
}
