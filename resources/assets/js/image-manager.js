

window.ImageManager = new Vue({

    el: '#image-manager',

    data: {
        images: [],
        hasMore: false,
        page: 0,
        cClickTime: 0,
        selectedImage: false,
        dependantPages: false,
        deleteForm: {}
    },

    created: function () {
        // Get initial images
        this.fetchData(this.page);
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
                        data.append("_token", document.querySelector('meta[name=token]').getAttribute('content'));
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
            var form = $(_this.$$.imageForm);
            $.ajax('/images/update/' + _this.selectedImage.id, {
                method: 'PUT',
                data: form.serialize()
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

});
