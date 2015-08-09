
// Dropzone config
Dropzone.options.imageUploadDropzone = {
    uploadMultiple: false,
    previewsContainer: '.image-manager-display .uploads',
    init: function() {
        this.on('success', function(event, image) {
            $('.image-manager-display .uploads').empty();
            var newImage = $('<img />').attr('data-image-id', image.id);
            newImage.attr('title', image.name).attr('src', image.thumbnail);
            newImage.data('imageData', image);
            $('.image-manager-display .uploads').after(newImage);
        });
    }
};

(function() {

    var isInit = false;
    var elem;
    var overlay;
    var display;
    var imageIndexUrl = '/images/all';
    var pageIndex = 0;
    var hasMore = true;
    var isGettingImages = true;

    var ImageManager =  {};
    var action = false;

    ImageManager.show = function(selector, callback) {
        if(isInit) {
            showWindow();
        } else {
            this.init(selector)
            showWindow();
        }

        action = (typeof callback !== 'undefined') ? callback : false;
    };

    ImageManager.init = function(selector) {
        elem = $(selector);
        overlay = elem.closest('.overlay');
        display = elem.find('.image-manager-display').first();
        var uploads = display.find('.uploads');
        var images = display.find('images');
        var loadMore = display.find('.load-more');
        // Get recent images and show
        $.getJSON(imageIndexUrl, showImages);
        function showImages(data) {
            var images = data.images;
            hasMore = data.hasMore;
            pageIndex++;
            isGettingImages = false;
            for(var i = 0; i < images.length; i++) {
                var image = images[i];
                var newImage = $('<img />').attr('data-image-id', image.id);
                newImage.attr('title', image.name).attr('src', image.thumbnail);
                loadMore.before(newImage);
                newImage.data('imageData', image);
            }
            if(hasMore) loadMore.show();
        }

        loadMore.click(function() {
            loadMore.hide();
            if(isGettingImages === false) {
                isGettingImages = true;
                $.getJSON(imageIndexUrl + '/' + pageIndex, showImages);
            }
        });

        // Image grabbing on scroll
        display.on('scroll', function() {
            var displayBottom = display.scrollTop() + display.height();
            var elemTop = loadMore.offset().top;
            if(elemTop < displayBottom && hasMore && isGettingImages === false) {
                isGettingImages = true;
                loadMore.hide();
                $.getJSON(imageIndexUrl + '/' + pageIndex, showImages);
            }
        });

        elem.on('dblclick', '.image-manager-display img', function() {
            var imageElem = $(this);
            var imageData = imageElem.data('imageData');
            closeWindow();
            if(action) {
                action(imageData);
            }
        });

        elem.find('button[data-action="close"]').click(function() {
            closeWindow();
        });

        // Set up dropzone
        elem.find('.image-manager-dropzone').first().dropzone({
            uploadMultiple: false
        });

        isInit = true;
    };

    function showWindow() {
        overlay.closest('body').css('overflow', 'hidden');
        overlay.show();
    }

    function closeWindow() {
        overlay.hide();
        overlay.closest('body').css('overflow', 'auto');
    }

    window.ImageManager = ImageManager;
})();