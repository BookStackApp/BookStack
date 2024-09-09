import {EditorUiElement} from "../core";
import {$getSelection} from "lexical";
import {$patchStyleText} from "@lexical/selection";
import {el} from "../../../utils/dom";

import removeIcon from "@icons/editor/color-clear.svg";

const colorChoices = [
    '#000000',
    '#ffffff',

    '#BFEDD2',
    '#FBEEB8',
    '#F8CAC6',
    '#ECCAFA',
    '#C2E0F4',

    '#2DC26B',
    '#F1C40F',
    '#E03E2D',
    '#B96AD9',
    '#3598DB',

    '#169179',
    '#E67E23',
    '#BA372A',
    '#843FA1',
    '#236FA1',

    '#ECF0F1',
    '#CED4D9',
    '#95A5A6',
    '#7E8C8D',
    '#34495E',
];

export class EditorColorPicker extends EditorUiElement {

    protected styleProperty: string;

    constructor(styleProperty: string) {
        super();
        this.styleProperty = styleProperty;
    }

    buildDOM(): HTMLElement {

        const colorOptions = colorChoices.map(choice => {
            return el('div', {
                class: 'editor-color-select-option',
                style: `background-color: ${choice}`,
                'data-color': choice,
                'aria-label': choice,
            });
        });

        const removeButton = el('div', {
            class: 'editor-color-select-option',
            'data-color': '',
            title: 'Clear color',
        }, []);
        removeButton.innerHTML = removeIcon;
        colorOptions.push(removeButton);

        const colorRows = [];
        for (let i = 0; i < colorOptions.length; i+=5) {
            const options = colorOptions.slice(i, i + 5);
            colorRows.push(el('div', {
                class: 'editor-color-select-row',
            }, options));
        }

        const wrapper = el('div', {
            class: 'editor-color-select',
        }, colorRows);

        wrapper.addEventListener('click', this.onClick.bind(this));

        return wrapper;
    }

    onClick(event: MouseEvent) {
        const colorEl = (event.target as HTMLElement).closest('[data-color]') as HTMLElement;
        if (!colorEl) return;

        const color = colorEl.dataset.color as string;
        this.getContext().editor.update(() => {
            const selection = $getSelection();
            if (selection) {
                $patchStyleText(selection, {[this.styleProperty]: color || null});
            }
        });
    }
}