
(function() {

    var ImageManager = new Vue({

        el: '#image-manager',

        data: {
            images: [],
            hasMore: false,
            page: 0,
            cClickTime: 0
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
                if(this.cClickTime !== 0 && timeDiff < dblClickTime) {
                    // DoubleClick
                    if(this.callback) {
                        this.callback(image);
                    }
                    this.hide();
                } else {
                    // Single Click
                }
                this.cClickTime = cTime;
            },

            show: function(callback) {
                this.callback = callback;
                this.$$.overlay.style.display = 'block';
            },

            hide: function() {
              this.$$.overlay.style.display = 'none';
            }

        }

    });

    window.ImageManager = ImageManager;


})();