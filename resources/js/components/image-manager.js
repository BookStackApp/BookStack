import {
    onChildEvent, onSelect, removeLoading, showLoading,
} from '../services/dom';
import {Component} from './component';

export class ImageManager extends Component {

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
        this.uploadButton = this.$refs.uploadButton;
        this.uploadHint = this.$refs.uploadHint;
        this.formContainer = this.$refs.formContainer;
        this.formContainerPlaceholder = this.$refs.formContainerPlaceholder;
        this.dropzoneContainer = this.$refs.dropzoneContainer;
        this.loadMore = this.$refs.loadMore;

        // Instance data
        this.type = 'gallery';
        this.lastSelected = {};
        this.lastSelectedTime = 0;
        this.callback = null;
        this.resetState = () => {
            this.hasData = false;
            this.page = 1;
            this.filter = 'all';
        };
        this.resetState();

        this.setupListeners();
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

        onSelect(this.cancelSearch, () => {
            this.resetListView();
            this.resetSearchView();
            this.loadGallery();
        });

        onChildEvent(this.container, '.load-more button', 'click', async event => {
            const wrapper = event.target.closest('.load-more');
            showLoading(wrapper);
            this.page += 1;
            await this.loadGallery();
        });

        this.listContainer.addEventListener('event-emit-select-image', this.onImageSelectEvent.bind(this));

        this.listContainer.addEventListener('error', event => {
            event.target.src = window.baseUrl('loading_error.png');
        }, true);

        onSelect(this.selectButton, () => {
            if (this.callback) {
                this.callback(this.lastSelected);
            }
            this.hide();
        });

        onChildEvent(this.formContainer, '#image-manager-delete', 'click', () => {
            if (this.lastSelected) {
                this.loadImageEditForm(this.lastSelected.id, true);
            }
        });

        this.formContainer.addEventListener('ajax-form-success', () => {
            this.refreshGallery();
            this.resetEditForm();
        });
        this.container.addEventListener('dropzone-upload-success', this.refreshGallery.bind(this));
    }

    show(callback, type = 'gallery') {
        this.resetAll();

        this.callback = callback;
        this.type = type;
        this.getPopup().show();

        const hideUploads = type !== 'gallery';
        this.dropzoneContainer.classList.toggle('hidden', hideUploads);
        this.uploadButton.classList.toggle('hidden', hideUploads);
        this.uploadHint.classList.toggle('hidden', hideUploads);

        /** @var {Dropzone} * */
        const dropzone = window.$components.firstOnElement(this.container, 'dropzone');
        dropzone.toggleActive(!hideUploads);

        if (!this.hasData) {
            this.loadGallery();
            this.hasData = true;
        }
    }

    hide() {
        this.getPopup().hide();
    }

    /**
     * @returns {Popup}
     */
    getPopup() {
        return window.$components.firstOnElement(this.popupEl, 'popup');
    }

    async loadGallery() {
        const params = {
            page: this.page,
            search: this.searchInput.value || null,
            uploaded_to: this.uploadedTo,
            filter_type: this.filter === 'all' ? null : this.filter,
        };

        const {data: html} = await window.$http.get(`images/${this.type}`, params);
        if (params.page === 1) {
            this.listContainer.innerHTML = '';
        }
        this.addReturnedHtmlElementsToList(html);
        removeLoading(this.listContainer);
    }

    addReturnedHtmlElementsToList(html) {
        const el = document.createElement('div');
        el.innerHTML = html;

        const loadMore = el.querySelector('.load-more');
        if (loadMore) {
            loadMore.remove();
            this.loadMore.innerHTML = loadMore.innerHTML;
        }
        this.loadMore.toggleAttribute('hidden', !loadMore);

        window.$components.init(el);
        for (const child of [...el.children]) {
            this.listContainer.appendChild(child);
        }
    }

    setActiveFilterTab(filterName) {
        for (const tab of this.filterTabs) {
            const selected = tab.dataset.filter === filterName;
            tab.setAttribute('aria-selected', selected ? 'true' : 'false');
        }
    }

    resetAll() {
        this.resetState();
        this.resetListView();
        this.resetSearchView();
        this.resetEditForm();
        this.setActiveFilterTab('all');
        this.selectButton.classList.add('hidden');
    }

    resetSearchView() {
        this.searchInput.value = '';
    }

    resetEditForm() {
        this.formContainer.innerHTML = '';
        this.formContainerPlaceholder.removeAttribute('hidden');
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
        } else {
            this.resetEditForm();
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
        this.formContainerPlaceholder.setAttribute('hidden', '');
        window.$components.init(this.formContainer);
    }

}
