
(function() {

    var isInit = false;
    var elem;
    var overlay;
    var display;
    var imageIndexUrl = '/images/all';

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
        console.log('cat');
        elem = $(selector);
        overlay = elem.closest('.overlay');
        display = elem.find('.image-manager-display').first()

        // Get recent images and show
        $.getJSON(imageIndexUrl, showImages);
        function showImages(images) {
            for(var i = 0; i < images.length; i++) {
                var image = images[i];
                var newImage = $('<img />').attr('data-image-id', image.id);
                newImage.attr('title', image.name).attr('src', image.thumbnail);
                display.append(newImage);
                newImage.data('imageData', image);
            }
        }

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
        })

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