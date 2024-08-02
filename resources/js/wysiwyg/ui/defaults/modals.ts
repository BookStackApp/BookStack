import {EditorFormModalDefinition} from "../framework/modals";
import {image, link, media} from "./forms/objects";
import {source} from "./forms/controls";
import {cellProperties} from "./forms/tables";

export const modals: Record<string, EditorFormModalDefinition> = {
    link: {
        title: 'Insert/Edit link',
        form: link,
    },
    image: {
        title: 'Insert/Edit Image',
        form: image,
    },
    media: {
        title: 'Insert/Edit Media',
        form: media,
    },
    source: {
        title: 'Source code',
        form: source,
    },
    cell_properties: {
        title: 'Cell Properties',
        form: cellProperties,
    },
};