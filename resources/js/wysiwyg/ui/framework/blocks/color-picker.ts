import {EditorUiContext, EditorUiElement} from "../core";
import {el} from "../../../utils/dom";

import removeIcon from "@icons/editor/color-clear.svg";
import selectIcon from "@icons/editor/color-select.svg";
import {uniqueIdSmall} from "../../../../services/util";

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

const storageKey = 'bs-lexical-custom-colors';

export type EditorColorPickerCallback = (color: string, context: EditorUiContext) => void;

export class EditorColorPicker extends EditorUiElement {

    protected callback: EditorColorPickerCallback;

    constructor(callback: EditorColorPickerCallback) {
        super();
        this.callback = callback;
    }

    buildDOM(): HTMLElement {
        const id = uniqueIdSmall();

        const allChoices = [...colorChoices, ...this.getCustomColorChoices()];
        const colorOptions = allChoices.map(choice => {
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
            title: this.getContext().translate('Remove color'),
        }, []);
        removeButton.innerHTML = removeIcon;
        colorOptions.push(removeButton);

        const selectButton = el('label', {
            class: 'editor-color-select-option',
            for: `color-select-${id}`,
            'data-color': '',
            title: this.getContext().translate('Custom color'),
        }, []);
        selectButton.innerHTML = selectIcon;
        colorOptions.push(selectButton);

        const input = el('input', {type: 'color', hidden: 'true', id: `color-select-${id}`}) as HTMLInputElement;
        colorOptions.push(input);
        input.addEventListener('change', e => {
            if (input.value) {
                this.storeCustomColorChoice(input.value);
                this.setColor(input.value);
                this.rebuildDOM();
            }
        });

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

    storeCustomColorChoice(color: string) {
        if (colorChoices.includes(color)) {
            return;
        }

        const customColors: string[] = this.getCustomColorChoices();
        if (customColors.includes(color)) {
            return;
        }

        customColors.push(color);
        window.localStorage.setItem(storageKey, JSON.stringify(customColors));
    }

    getCustomColorChoices(): string[] {
        return JSON.parse(window.localStorage.getItem(storageKey) || '[]');
    }

    onClick(event: MouseEvent) {
        const colorEl = (event.target as HTMLElement).closest('[data-color]') as HTMLElement;
        if (!colorEl) return;

        const color = colorEl.dataset.color as string;
        this.setColor(color);
    }

    setColor(color: string) {
        this.callback(color, this.getContext());
    }
}