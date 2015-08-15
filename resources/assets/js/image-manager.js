
jQuery.fn.showSuccess = function(message) {
    var elem = $(this);
    var success = $('<div class="text-pos" style="display:none;"><i class="zmdi zmdi-check-circle"></i>'+message+'</div>');
    elem.after(success);
    success.slideDown(400, function() {
        setTimeout(function() {success.slideUp(400, function() {
            success.remove();
        })}, 2000);
    });
};

jQuery.fn.showFailure = function(messageMap) {
    var elem = $(this);
    $.each(messageMap, function(key, messages) {
        var input = elem.find('[name="'+key+'"]').last();
        var fail = $('<div class="text-neg" style="display:none;"><i class="zmdi zmdi-alert-circle"></i>'+messages.join("\n")+'</div>');
        input.after(fail);
        fail.slideDown(400, function() {
            setTimeout(function() {fail.slideUp(400, function() {
                fail.remove();
            })}, 2000);
        });
    });

};

(function() {

    var ImageManager = new Vue({

        el: '#image-manager',

        data: {
            images: [],
            hasMore: false,
            page: 0,
            cClickTime: 0,
            selectedImage: false
        },

        created: function() {
            // Get initial images
            this.fetchData(this.page);
        },

        ready: function() {
            // Create dropzone
            this.setupDropZone();
        },

        methods: {
            fetchData: function() {
                var _this = this;
                $.getJSON('/images/all/' + _this.page, function(data) {
                    _this.images = _this.images.concat(data.images);
                    _this.hasMore = data.hasMore;
                    _this.page++;
                });
            },

            setupDropZone: function() {
                var _this = this;
                var dropZone = new Dropzone(_this.$$.dropZone, {
                    url: '/upload/image',
                    init: function() {
                        var dz = this;
                        this.on("sending", function(file, xhr, data) {
                            data.append("_token", document.querySelector('meta[name=token]').getAttribute('content'));
                        });
                        this.on("success", function(file, data) {
                            _this.images.unshift(data);
                            $(file.previewElement).fadeOut(400, function() {
                                dz.removeFile(file);
                            });
                        });
                    }
                });
            },

            imageClick: function(image) {
                var dblClickTime = 380;
                var cTime = (new Date()).getTime();
                var timeDiff = cTime - this.cClickTime;
                if(this.cClickTime !== 0 && timeDiff < dblClickTime && this.selectedImage === image) {
                    // DoubleClick
                    if(this.callback) {
                        this.callback(image);
                    }
                    this.hide();
                } else {
                    this.selectedImage = (this.selectedImage===image) ? false : image;
                }
                this.cClickTime = cTime;
            },

            selectButtonClick: function() {
                if(this.callback) {
                    this.callback(this.selectedImage);
                }
                this.hide();
            },

            show: function(callback) {
                this.callback = callback;
                this.$$.overlay.style.display = 'block';
            },

            overlayClick: function(e) {
              if(e.target.className==='overlay') {
                  this.hide();
              }
            },

            hide: function() {
              this.$$.overlay.style.display = 'none';
            },

            saveImageDetails: function(e) {
                e.preventDefault();
                var _this = this;
                var form = $(_this.$$.imageForm);
                $.ajax('/images/update/' + _this.selectedImage.id, {
                    method: 'PUT',
                    data: form.serialize()
                }).done(function() {
                    form.showSuccess('Image name updated');
                }).fail(function(jqXHR) {
                    form.showFailure(jqXHR.responseJSON);
                })
            },

            deleteImage: function(e) {
                e.preventDefault();
                var _this = this;
                var form = $(_this.$$.imageDeleteForm);
                $.ajax('/images/' + _this.selectedImage.id, {
                    method: 'DELETE',
                    data: form.serialize()
                }).done(function() {
                    _this.images.splice(_this.images.indexOf(_this.selectedImage), 1);
                    _this.selectedImage = false;
                    $(_this.$$.imageTitle).showSuccess('Image Deleted');
                })
            }

        }

    });

    window.ImageManager = ImageManager;


})();