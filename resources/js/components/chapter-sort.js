import Sortable from "sortablejs";

// Auto sort control
const sortOperations = {
    name: function(a, b) {
        const aName = a.getAttribute('data-name').trim().toLowerCase();
        const bName = b.getAttribute('data-name').trim().toLowerCase();
        return aName.localeCompare(bName);
    },
    created: function(a, b) {
        const aTime = Number(a.getAttribute('data-created'));
        const bTime = Number(b.getAttribute('data-created'));
        return bTime - aTime;
    },
    updated: function(a, b) {
        const aTime = Number(a.getAttribute('data-updated'));
        const bTime = Number(b.getAttribute('data-updated'));
        return bTime - aTime;
    },
    pagesFirst: function(a, b) {
        const aType = a.getAttribute('data-type');
        const bType = b.getAttribute('data-type');
        return (aType === 'page' ? -1 : 1);
    },
    pagesLast: function(a, b) {
        const aType = a.getAttribute('data-type');
        const bType = b.getAttribute('data-type');
        return (aType === 'page' ? 1 : -1);
    },
};

class ChapterSort {

    constructor(elem) {
        this.elem = elem;
        this.sortContainer = elem.querySelector('[chapter-sort-boxes]');
        this.input = elem.querySelector('[chapter-sort-input]');
        const initialSortBox = elem.querySelector('.sort-box');
        this.setupChapterSortable(initialSortBox);
        this.setupSortPresets();

        window.$events.listen('entity-select-confirm', this.chapterSelect.bind(this));
    }

    /**
     * Setup the handlers for the preset sort type buttons.
     */
    setupSortPresets() {
        let lastSort = '';
        let reverse = false;
        const reversibleTypes = ['name', 'created', 'updated'];

        this.sortContainer.addEventListener('click', event => {
            const sortButton = event.target.closest('.sort-box-options [data-sort]');
            if (!sortButton) return;

            event.preventDefault();
            const sortLists = sortButton.closest('.sort-box').querySelectorAll('ul');
            const sort = sortButton.getAttribute('data-sort');
            reverse = (lastSort === sort) ? !reverse : false;
            let sortFunction = sortOperations[sort];
            if (reverse && reversibleTypes.includes(sort)) {
                sortFunction = function(a, b) {
                    return 0 - sortOperations[sort](a, b)
                };
            }

            for (let list of sortLists) {
                const directItems = Array.from(list.children).filter(child => child.matches('li'));
                directItems.sort(sortFunction).forEach(sortedItem => {
                    list.appendChild(sortedItem);
                });
            }

            lastSort = sort;
            this.updateMapInput();
        });
    }

    /**
     * Handle book selection from the entity selector.
     * @param {Object} entityInfo
     */
     chapterSelect(entityInfo) {
        const alreadyAdded = this.elem.querySelector(`[data-type="chapter"][data-id="${entityInfo.id}"]`) !== null;
        if (alreadyAdded) return;

        const entitySortItemUrl = entityInfo.link + '/sort-item';
        window.$http.get(entitySortItemUrl).then(resp => {
            const wrap = document.createElement('div');
            wrap.innerHTML = resp.data;
            const newChapterContainer = wrap.children[0];
            this.sortContainer.append(newChapterContainer);
            this.setupChapterSortable(newChapterContainer);
        });
    }

    /**
     * Setup the given book container element to have sortable items.
     * @param {Element} chapterContainer
     */
    setupChapterSortable(chapterContainer) {
        const sortElems = [chapterContainer.querySelector('.sort-list')];
        sortElems.push(...chapterContainer.querySelectorAll('.sortable-page-list'));

        const bookGroupConfig = {
            name: 'chapter',
            pull: ['chapter', 'page'],
            put: ['chapter', 'page'],
        };

        const chapterGroupConfig = {
            name: 'chapter',
            pull: ['chapter', 'page'],
            put: function(toList, fromList, draggedElem) {
                return draggedElem.getAttribute('data-type') === 'page';
            }
        };

        for (let sortElem of sortElems) {
            new Sortable(sortElem, {
                group: sortElem.classList.contains('sort-list') ? bookGroupConfig : chapterGroupConfig,
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onSort: this.updateMapInput.bind(this),
                dragClass: 'bg-white',
                ghostClass: 'primary-background-light',
                multiDrag: true,
                multiDragKey: 'CTRL',
                selectedClass: 'sortable-selected',
            });
        }
    }

    /**
     * Update the input with our sort data.
     */
    updateMapInput() {
        const pageMap = this.buildEntityMap();
        this.input.value = JSON.stringify(pageMap);
    }

    /**
     * Build up a mapping of entities with their ordering and nesting.
     * @returns {Array}
     */
    buildEntityMap() {
        const entityMap = [];
        const lists = this.elem.querySelectorAll('.sort-list');

        for (let list of lists) {
            const chapterId = list.closest('[data-type="chapter"]').getAttribute('data-id');
            const directChildren = Array.from(list.children)
                .filter(elem => elem.matches('[data-type="page"]'));
            for (let i = 0; i < directChildren.length; i++) {
                this.addChapterChildToMap(directChildren[i], i, chapterId, entityMap);
            }
        }

        return entityMap;
    }

    /**
     * Parse a sort item and add it to a data-map array.
     * Parses sub0items if existing also.
     * @param {Element} childElem
     * @param {Number} index
     * @param {Number} chapterId
     * @param {Array} entityMap
     */
    addChapterChildToMap(childElem, index, chapterId, entityMap) {
        const type = childElem.getAttribute('data-type');
        const parentChapter = false;
        const childId = childElem.getAttribute('data-id');

        entityMap.push({
            id: childId,
            sort: index,
            parentChapter: parentChapter,
            type: type,
            chapter: chapterId
        });

    }

}

export default ChapterSort;