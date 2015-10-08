<template>
    <div id="image-manager">
        <div class="overlay" v-el="overlay" v-on="click: overlayClick" >
            <div class="image-manager-body">
                <div class="image-manager-content">
                    <div class="image-manager-list">
                        <div v-repeat="image: images">
                            <img class="anim fadeIn"
                                 v-class="selected: (image==selectedImage)"
                                 v-attr="src: image.thumbnail, alt: image.name, title: image.name"
                                 v-on="click: imageClick(image)"
                                 v-style="animation-delay: ($index > 26) ? '160ms' : ($index * 25) + 'ms'">
                        </div>
                        <div class="load-more" v-show="hasMore" v-on="click: fetchData">Load More</div>
                    </div>
                </div>
                <button class="neg button image-manager-close" v-on="click: hide()">x</button>
                <div class="image-manager-sidebar">
                    <h2 v-el="imageTitle">Images</h2>
                    <hr class="even">
                    <div class="dropzone-container" v-el="dropZone">
                        <div class="dz-message">Drop files or click here to upload</div>
                    </div>
                    <div class="image-manager-details anim fadeIn" v-show="selectedImage">
                        <hr class="even">
                        <form v-on="submit: saveImageDetails" v-el="imageForm">
                            <div class="form-group">
                                <label for="name">Image Name</label>
                                <input type="text" id="name" name="name" v-model="selectedImage.name">
                            </div>
                        </form>
                        <hr class="even">
                        <div v-show="dependantPages">
                            <p class="text-neg text-small">
                                This image is used in the pages below, Click delete again to confirm you want to delete this image.
                            </p>
                            <ul class="text-neg">
                                <li v-repeat="page: dependantPages">
                                    <a v-attr="href: page.url" target="_blank" class="text-neg">@{{ page.name }}</a>
                                </li>
                            </ul>
                        </div>

                        <form v-on="submit: deleteImage" v-el="imageDeleteForm">
                            <button class="button neg"><i class="zmdi zmdi-delete"></i>Delete Image</button>
                        </form>
                    </div>
                    <div class="image-manager-bottom">
                        <button class="button pos anim fadeIn" v-show="selectedImage" v-on="click:selectButtonClick"><i class="zmdi zmdi-square-right"></i>Select Image</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    var Dropzone = require('dropzone');

    module.exports = {
        data: function(){
            return {
                images: [],
                hasMore: false,
                page: 0,
                cClickTime: 0,
                selectedImage: false,
                dependantPages: false,
                deleteForm: {},
                token: document.querySelector('meta[name=token]').getAttribute('content')
            }
        },

        created: function () {
            // Get initial images
            this.fetchData(this.page);
            window.ImageManager = this;
        },

        ready: function () {
            // Create dropzone
            this.setupDropZone();
        },

        methods: {
            fetchData: function () {
                var _this = this;
                this.$http.get('/images/all/' + _this.page, function (data) {
                    _this.images = _this.images.concat(data.images);
                    _this.hasMore = data.hasMore;
                    _this.page++;
                });
            },

            setupDropZone: function () {
                var _this = this;
                var dropZone = new Dropzone(_this.$$.dropZone, {
                    url: '/upload/image',
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
                this.$$.overlay.style.display = 'block';
            },

            overlayClick: function (e) {
                if (e.target.className === 'overlay') {
                    this.hide();
                }
            },

            hide: function () {
                this.$$.overlay.style.display = 'none';
            },

            saveImageDetails: function (e) {
                e.preventDefault();
                var _this = this;
                _this.selectedImage._token = _this.token;
                var form = $(_this.$$.imageForm);
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
                    $(_this.$$.imageTitle).showSuccess('Image Deleted');
                }).fail(function(jqXHR, textStatus) {
                    // Pages failure
                    if(jqXHR.status === 400) {
                        _this.dependantPages = jqXHR.responseJSON;
                    }
                });
            }

        }

    };
</script>