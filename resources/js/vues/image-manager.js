import * as Dates from "../services/dates";
import dropzone from "./components/dropzone";

let page = 1;
let previousClickTime = 0;
let previousClickImage = 0;
let dataLoaded = false;
let callback = false;
let baseUrl = '';

let preSearchImages = [];
let preSearchHasMore = false;

const data = {
    images: [],

    imageType: false,
    uploadedTo: false,

    selectedImage: false,
    dependantPages: false,
    showing: false,
    filter: null,
    hasMore: false,
    searching: false,
    searchTerm: '',

    imageUpdateSuccess: false,
    imageDeleteSuccess: false,
    deleteConfirm: false,
};

const methods = {

    show(providedCallback, imageType = null) {
        callback = providedCallback;
        this.showing = true;
        this.$el.children[0].components.popup.show();

        // Get initial images if they have not yet been loaded in.
        if (dataLoaded && imageType === this.imageType) return;
        if (imageType) {
            this.imageType = imageType;
            this.resetState();
        }
        this.fetchData();
        dataLoaded = true;
    },

    hide() {
        if (this.$refs.dropzone) {
            this.$refs.dropzone.onClose();
        }
        this.showing = false;
        this.selectedImage = false;
        this.$el.children[0].components.popup.hide();
    },

    async fetchData() {
        const params = {
            page,
            search: this.searching ? this.searchTerm : null,
            uploaded_to: this.uploadedTo || null,
            filter_type: this.filter,
        };

        const {data} = await this.$http.get(baseUrl, params);
        this.images = this.images.concat(data.images);
        this.hasMore = data.has_more;
        page++;
    },

    setFilterType(filterType) {
        this.filter = filterType;
        this.resetState();
        this.fetchData();
    },

    resetState() {
        this.cancelSearch();
        this.resetListView();
        this.deleteConfirm = false;
        baseUrl = window.baseUrl(`/images/${this.imageType}`);
    },

    resetListView() {
        this.images = [];
        this.hasMore = false;
        page = 1;
    },

    searchImages() {
        if (this.searchTerm === '') return this.cancelSearch();

        // Cache current settings for later
        if (!this.searching) {
            preSearchImages = this.images;
            preSearchHasMore = this.hasMore;
        }

        this.searching = true;
        this.resetListView();
        this.fetchData();
    },

    cancelSearch() {
        if (!this.searching) return;
        this.searching = false;
        this.searchTerm = '';
        this.images = preSearchImages;
        this.hasMore = preSearchHasMore;
    },

    imageSelect(image) {
        const dblClickTime = 300;
        const currentTime = Date.now();
        const timeDiff = currentTime - previousClickTime;
        const isDblClick = timeDiff < dblClickTime && image.id === previousClickImage;

        if (isDblClick) {
            this.callbackAndHide(image);
        } else {
            this.selectedImage = image;
            this.deleteConfirm = false;
            this.dependantPages = false;
        }

        previousClickTime = currentTime;
        previousClickImage = image.id;
    },

    callbackAndHide(imageResult) {
        if (callback) callback(imageResult);
        this.hide();
    },

    async saveImageDetails() {
        let url = window.baseUrl(`/images/${this.selectedImage.id}`);
        try {
            await this.$http.put(url, this.selectedImage)
        } catch (error) {
            if (error.response.status === 422) {
                let errors = error.response.data;
                let message = '';
                Object.keys(errors).forEach((key) => {
                    message += errors[key].join('\n');
                });
                this.$events.emit('error', message);
            }
        }
    },

    async deleteImage() {

        if (!this.deleteConfirm) {
            const url = window.baseUrl(`/images/usage/${this.selectedImage.id}`);
            try {
                const {data} = await this.$http.get(url);
                this.dependantPages = data;
            } catch (error) {
                console.error(error);
            }
            this.deleteConfirm = true;
            return;
        }

        const url = window.baseUrl(`/images/${this.selectedImage.id}`);
        await this.$http.delete(url);
        this.images.splice(this.images.indexOf(this.selectedImage), 1);
        this.selectedImage = false;
        this.$events.emit('success', trans('components.image_delete_success'));
        this.deleteConfirm = false;
    },

    getDate(stringDate) {
        return Dates.formatDateTime(new Date(stringDate));
    },

    uploadSuccess(event) {
        this.images.unshift(event.data);
        this.$events.emit('success', trans('components.image_upload_success'));
    },
};

const computed = {
    uploadUrl() {
        return window.baseUrl(`/images/${this.imageType}`);
    }
};

function mounted() {
    window.ImageManager = this;
    this.imageType = this.$el.getAttribute('image-type');
    this.uploadedTo = this.$el.getAttribute('uploaded-to');
    baseUrl = window.baseUrl('/images/' + this.imageType)
}

export default {
    mounted,
    methods,
    data,
    computed,
    components: {dropzone},
};
