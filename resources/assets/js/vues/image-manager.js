const dropzone = require('./components/dropzone');

let page = 0;
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
    view: 'all',
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
        this.$el.children[0].components.overlay.show();

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
        this.$el.children[0].components.overlay.hide();
    },

    fetchData() {
        let url = baseUrl + page;
        let query = {};
        if (this.uploadedTo !== false) query.page_id = this.uploadedTo;
        if (this.searching) query.term = this.searchTerm;

        this.$http.get(url, {params: query}).then(response => {
            this.images = this.images.concat(response.data.images);
            this.hasMore = response.data.hasMore;
            page++;
        });
    },

    setView(viewName) {
        this.view = viewName;
        this.resetState();
        this.fetchData();
    },

    resetState() {
        this.cancelSearch();
        this.images = [];
        this.hasMore = false;
        this.deleteConfirm = false;
        page = 0;
        baseUrl = window.baseUrl(`/images/${this.imageType}/${this.view}/`);
    },

    searchImages() {
        if (this.searchTerm === '') return this.cancelSearch();

        // Cache current settings for later
        if (!this.searching) {
            preSearchImages = this.images;
            preSearchHasMore = this.hasMore;
        }

        this.searching = true;
        this.images = [];
        this.hasMore = false;
        page = 0;
        baseUrl = window.baseUrl(`/images/${this.imageType}/search/`);
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
        let dblClickTime = 300;
        let currentTime = Date.now();
        let timeDiff = currentTime - previousClickTime;
        let isDblClick = timeDiff < dblClickTime && image.id === previousClickImage;

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

    saveImageDetails() {
        let url = window.baseUrl(`/images/update/${this.selectedImage.id}`);
        this.$http.put(url, this.selectedImage).then(response => {
            this.$events.emit('success', trans('components.image_update_success'));
        }).catch(error => {
            if (error.response.status === 422) {
                let errors = error.response.data;
                let message = '';
                Object.keys(errors).forEach((key) => {
                    message += errors[key].join('\n');
                });
                this.$events.emit('error', message);
            }
        });
    },

    deleteImage() {

        if (!this.deleteConfirm) {
            let url = window.baseUrl(`/images/usage/${this.selectedImage.id}`);
            this.$http.get(url).then(resp => {
                this.dependantPages = resp.data;
            }).catch(console.error).then(() => {
                this.deleteConfirm = true;
            });
            return;
        }

        this.$http.delete(`/images/${this.selectedImage.id}`).then(resp => {
            this.images.splice(this.images.indexOf(this.selectedImage), 1);
            this.selectedImage = false;
            this.$events.emit('success', trans('components.image_delete_success'));
            this.deleteConfirm = false;
        });
    },

    getDate(stringDate) {
        return new Date(stringDate);
    },

    uploadSuccess(event) {
        this.images.unshift(event.data);
        this.$events.emit('success', trans('components.image_upload_success'));
    },
};

const computed = {
    uploadUrl() {
        return window.baseUrl(`/images/${this.imageType}/upload`);
    }
};

function mounted() {
    window.ImageManager = this;
    this.imageType = this.$el.getAttribute('image-type');
    this.uploadedTo = this.$el.getAttribute('uploaded-to');
    baseUrl = window.baseUrl('/images/' + this.imageType + '/all/')
}

module.exports = {
    mounted,
    methods,
    data,
    computed,
    components: {dropzone},
};
