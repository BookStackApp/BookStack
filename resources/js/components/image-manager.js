import {onChildEvent, onSelect, removeLoading, showLoading} from "../services/dom";

/**
 * ImageManager
 * @extends {Component}
 */
class ImageManager {

    setup() {

        // Options
        this.uploadedTo = this.$opts.uploadedTo;

        // Element References
        this.container = this.$el;
        this.popupEl = this.$refs.popup;
        this.searchForm = this.$refs.searchForm;
        this.searchInput = this.$refs.searchInput;
        this.cancelSearch = this.$refs.cancelSearch;
        this.listContainer = this.$refs.listContainer;
        this.filterTabs = this.$manyRefs.filterTabs;
        this.selectButton = this.$refs.selectButton;
        this.formContainer = this.$refs.formContainer;
        this.dropzoneContainer = this.$refs.dropzoneContainer;

        // Instance data
        this.type = 'gallery';
        this.lastSelected = {};
        this.lastSelectedTime = 0;
        this.resetState = () => {
            this.callback = null;
            this.hasData = false;
            this.page = 1;
            this.filter = 'all';
        };
        this.resetState();

        this.setupListeners();

        window.ImageManager = this;
    }

    setupListeners() {
        onSelect(this.filterTabs, e => {
            this.resetAll();
            this.filter = e.target.dataset.filter;
            this.setActiveFilterTab(this.filter);
            this.loadGallery();
        });

        this.searchForm.addEventListener('submit', event => {
            this.resetListView();
            this.loadGallery();
            event.preventDefault();
        });

        onSelect(this.cancelSearch, event => {
            this.resetListView();
            this.resetSearchView();
            this.loadGallery();
            this.cancelSearch.classList.remove('active');
        });

        this.searchInput.addEventListener('input', event => {
            this.cancelSearch.classList.toggle('active', this.searchInput.value.trim());
        });

        onChildEvent(this.listContainer, '.load-more', 'click', async event => {
            showLoading(event.target);
            this.page++;
            await this.loadGallery();
            event.target.remove();
        });

        this.listContainer.addEventListener('event-emit-select-image', this.onImageSelectEvent.bind(this));

        onSelect(this.selectButton, () => {
            if (this.callback) {
                this.callback(this.lastSelected);
            }
            this.hide();
        });

        onChildEvent(this.formContainer, '#image-manager-delete', 'click', event => {
            if (this.lastSelected) {
                this.loadImageEditForm(this.lastSelected.id, true);
            }
        });

        this.formContainer.addEventListener('ajax-form-success', this.refreshGallery.bind(this));
        this.container.addEventListener('dropzone-success', this.refreshGallery.bind(this));
    }

    show(callback, type = 'gallery') {
        this.resetAll();

        this.callback = callback;
        this.type = type;
        this.popupEl.components.popup.show();
        this.dropzoneContainer.classList.toggle('hidden', type !== 'gallery');

        if (!this.hasData) {
            this.loadGallery();
            this.hasData = true;
        }
    }

    hide() {
        this.popupEl.components.popup.hide();
    }

    async loadGallery() {
        const params = {
            page: this.page,
            search: this.searchInput.value || null,
            uploaded_to: this.uploadedTo,
            filter_type: this.filter === 'all' ? null : this.filter,
        };

        const {data: html} = await window.$http.get(`images/${this.type}`, params);
        this.addReturnedHtmlElementsToList(html);
        removeLoading(this.listContainer);
    }

    addReturnedHtmlElementsToList(html) {
        const el = document.createElement('div');
        el.innerHTML = html;
        window.components.init(el);
        for (const child of [...el.children]) {
            this.listContainer.appendChild(child);
        }
    }

    setActiveFilterTab(filterName) {
        this.filterTabs.forEach(t => t.classList.remove('selected'));
        const activeTab = this.filterTabs.find(t => t.dataset.filter === filterName);
        if (activeTab) {
            activeTab.classList.add('selected');
        }
    }

    resetAll() {
        this.resetState();
        this.resetListView();
        this.resetSearchView();
        this.formContainer.innerHTML = '';
        this.setActiveFilterTab('all');
    }

    resetSearchView() {
        this.searchInput.value = '';
    }

    resetListView() {
        showLoading(this.listContainer);
        this.page = 1;
    }

    refreshGallery() {
        this.resetListView();
        this.loadGallery();
    }

    onImageSelectEvent(event) {
        const image = JSON.parse(event.detail.data);
        const isDblClick = ((image && image.id === this.lastSelected.id)
            && Date.now() - this.lastSelectedTime < 400);
        const alreadySelected = event.target.classList.contains('selected');
        [...this.listContainer.querySelectorAll('.selected')].forEach(el => {
            el.classList.remove('selected');
        });

        if (!alreadySelected) {
            event.target.classList.add('selected');
            this.loadImageEditForm(image.id);
        }
        this.selectButton.classList.toggle('hidden', alreadySelected);

        if (isDblClick && this.callback) {
            this.callback(image);
            this.hide();
        }

        this.lastSelected = image;
        this.lastSelectedTime = Date.now();
    }

    async loadImageEditForm(imageId, requestDelete = false) {
        if (!requestDelete) {
            this.formContainer.innerHTML = '';
        }

        const params = requestDelete ? {delete: true} : {};
        const {data: formHtml} = await window.$http.get(`/images/edit/${imageId}`, params);
        this.formContainer.innerHTML = formHtml;
        window.components.init(this.formContainer);
    }

}

export default ImageManager;