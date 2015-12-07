<template>
    <div id="image-manager">
        <div class="overlay" v-el:overlay @click="overlayClick">
            <div class="image-manager-body">
                <div class="image-manager-content">
                    <div class="image-manager-list">
                        <div v-for="image in images">
                            <img class="anim fadeIn"
                                 :class="{selected: (image==selectedImage)}"
                                 :src="image.thumbs.gallery" :alt="image.title" :title="image.name"
                                 @click="imageClick(image)"
                                 :style="{animationDelay: ($index > 26) ? '160ms' : ($index * 25) + 'ms'}">
                        </div>
                        <div class="load-more" v-show="hasMore" @click="fetchData">Load More</div>
                    </div>
                </div>
                <button class="neg button image-manager-close" @click="hide">x</button>
                <div class="image-manager-sidebar">
                    <h2 v-el:image-title>Images</h2>
                    <hr class="even">
                    <div class="dropzone-container" v-el:drop-zone>
                        <div class="dz-message">Drop files or click here to upload</div>
                    </div>
                    <div class="image-manager-details anim fadeIn" v-show="selectedImage">
                        <hr class="even">
                        <form @submit="saveImageDetails" v-el:image-form>
                            <div class="form-group">
                                <label for="name">Image Name</label>
                                <input type="text" id="name" name="name" v-model="selectedImage.name">
                            </div>
                        </form>
                        <hr class="even">
                        <div v-show="dependantPages">
                            <p class="text-neg text-small">
                                This image is used in the pages below, Click delete again to confirm you want to delete
                                this image.
                            </p>
                            <ul class="text-neg">
                                <li v-for="page in dependantPages">
                                    <a :href="page.url" target="_blank" class="text-neg">{{ page.name }}</a>
                                </li>
                            </ul>
                        </div>

                        <form @submit="deleteImage" v-el:image-delete-form>
                            <button class="button neg"><i class="zmdi zmdi-delete"></i>Delete Image</button>
                        </form>
                    </div>
                    <div class="image-manager-bottom">
                        <button class="button pos anim fadeIn" v-show="selectedImage" @click="selectButtonClick"><i
                                class="zmdi zmdi-square-right"></i>Select Image
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    var Dropzone = require('dropzone');

    module.exports = {
        data: function () {
            return {
                images: [],
                hasMore: false,
                page: 0,
                cClickTime: 0,
                selectedImage: false,
                dependantPages: false,
                deleteForm: {},
                token: document.querySelector('meta[name=token]').getAttribute('content'),
                dataLoaded: false
            }
        },

        created: function () {
            window.ImageManager = this;
        },

        ready: function () {
            // Create dropzone
            this.setupDropZone();
        },

        methods: {
            fetchData: function () {
                var _this = this;
                this.$http.get('/images/gallery/all/' + _this.page, function (data) {
                    _this.images = _this.images.concat(data.images);
                    _this.hasMore = data.hasMore;
                    _this.page++;
                });
            },

            setupDropZone: function () {
                var _this = this;
                var dropZone = new Dropzone(_this.$els.dropZone, {
                    url: '/images/gallery/upload',
                    init: function () {
                        var dz = this;
                        this.on("sending", function (file, xhr, data) {
                            data.append("_token", _this.token);
                        });
                        this.on("success", function (file, data) {
                            _this.images.unshift(data);
                            $(file.previewElement).fadeOut(400, function () {
                                dz.removeFile(file);
                            });
                        });
                        this.on('error', function (file, errorMessage, xhr) {
                            if (errorMessage.file) {
                                $(file.previewElement).find('[data-dz-errormessage]').text(errorMessage.file[0]);
                            }
                            console.log(errorMessage);
                        });
                    }
                });
            },

            imageClick: function (image) {
                var dblClickTime = 380;
                var cTime = (new Date()).getTime();
                var timeDiff = cTime - this.cClickTime;
                if (this.cClickTime !== 0 && timeDiff < dblClickTime && this.selectedImage === image) {
                    // DoubleClick
                    if (this.callback) {
                        this.callback(image);
                    }
                    this.hide();
                } else {
                    this.selectedImage = (this.selectedImage === image) ? false : image;
                    this.dependantPages = false;
                }
                this.cClickTime = cTime;
            },

            selectButtonClick: function () {
                if (this.callback) {
                    this.callback(this.selectedImage);
                }
                this.hide();
            },

            show: function (callback) {
                this.callback = callback;
                this.$els.overlay.style.display = 'block';
                // Get initial images if they have not yet been loaded in.
                if (!this.dataLoaded) {
                    this.fetchData(this.page);
                    this.dataLoaded = true;
                }
            },

            overlayClick: function (e) {
                if (e.target.className === 'overlay') {
                    this.hide();
                }
            },

            hide: function () {
                this.$els.overlay.style.display = 'none';
            },

            saveImageDetails: function (e) {
                e.preventDefault();
                var _this = this;
                _this.selectedImage._token = _this.token;
                var form = $(_this.$els.imageForm);
                $.ajax('/images/update/' + _this.selectedImage.id, {
                    method: 'PUT',
                    data: _this.selectedImage
                }).done(function () {
                    form.showSuccess('Image name updated');
                }).fail(function (jqXHR) {
                    form.showFailure(jqXHR.responseJSON);
                })
            },

            deleteImage: function (e) {
                e.preventDefault();
                var _this = this;
                _this.deleteForm.force = _this.dependantPages !== false;
                _this.deleteForm._token = _this.token;
                $.ajax('/images/' + _this.selectedImage.id, {
                    method: 'DELETE',
                    data: _this.deleteForm
                }).done(function () {
                    _this.images.splice(_this.images.indexOf(_this.selectedImage), 1);
                    _this.selectedImage = false;
                    $(_this.$els.imageTitle).showSuccess('Image Deleted');
                }).fail(function (jqXHR, textStatus) {
                    // Pages failure
                    if (jqXHR.status === 400) {
                        _this.dependantPages = jqXHR.responseJSON;
                    }
                });
            }

        }

    };
</script>