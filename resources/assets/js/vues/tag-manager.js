import draggable from 'vuedraggable';
import autosuggest from './components/autosuggest';

const data = {
    entityId: false,
    entityType: null,
    tags: [],
};

const components = {draggable, autosuggest};
const directives = {};

const methods = {

    addEmptyTag() {
        this.tags.push({name: '', value: '', key: Math.random().toString(36).substring(7)});
    },

    /**
     * When an tag changes check if another empty editable field needs to be added onto the end.
     * @param tag
     */
    tagChange(tag) {
        let tagPos = this.tags.indexOf(tag);
        if (tagPos === this.tags.length-1 && (tag.name !== '' || tag.value !== '')) this.addEmptyTag();
    },

    /**
     * When an tag field loses focus check the tag to see if its
     * empty and therefore could be removed from the list.
     * @param tag
     */
    tagBlur(tag) {
        let isLast = (this.tags.indexOf(tag) === this.tags.length-1);
        if (tag.name !== '' || tag.value !== '' || isLast) return;
        let cPos = this.tags.indexOf(tag);
        this.tags.splice(cPos, 1);
    },

    removeTag(tag) {
        let tagPos = this.tags.indexOf(tag);
        if (tagPos === -1) return;
        this.tags.splice(tagPos, 1);
    },

    getTagFieldName(index, key) {
        return `tags[${index}][${key}]`;
    },
};

function mounted() {
    this.entityId = Number(this.$el.getAttribute('entity-id'));
    this.entityType = this.$el.getAttribute('entity-type');

    let url = window.baseUrl(`/ajax/tags/get/${this.entityType}/${this.entityId}`);
    this.$http.get(url).then(response => {
        let tags = response.data;
        for (let i = 0, len = tags.length; i < len; i++) {
            tags[i].key = Math.random().toString(36).substring(7);
        }
        this.tags = tags;
        this.addEmptyTag();
    });
}

export default {
    data, methods, mounted, components, directives
};