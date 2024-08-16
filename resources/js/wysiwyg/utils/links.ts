import {EntitySelectorPopup} from "../../components";

type EditorEntityData = {
    link: string;
    name: string;
};

export function showLinkSelector(callback: (entity: EditorEntityData) => any, selectionText?: string) {
    const selector: EntitySelectorPopup = window.$components.first('entity-selector-popup') as EntitySelectorPopup;
    selector.show((entity: EditorEntityData) => callback(entity), {
        initialValue: selectionText,
        searchEndpoint: '/search/entity-selector',
        entityTypes: 'page,book,chapter,bookshelf',
        entityPermission: 'view',
    });
}