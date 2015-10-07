$(function () {

    // Notification hiding
    $('.notification').click(function () {
        $(this).fadeOut(100);

    });

    // Dropdown toggles
    $('[data-dropdown]').dropDown();

    // Chapter page list toggles
    $('.chapter-toggle').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('open');
        $(this).closest('.chapter').find('.inset-list').slideToggle(180);
    });

});


// Vue Components

Vue.component('image-picker', {
    template: require('./templates/image-picker.html'),
    props: ['currentImage', 'name', 'imageClass'],
    data: function() {
        return {
            image: this.currentImage
        }
    },
    methods: {
        showImageManager: function(e) {
            var _this = this;
            ImageManager.show(function(image) {
                _this.image = image.url;
            });
        },
        reset: function() {
            this.image = '';
        },
        remove: function() {
            this.image = 'none';
        }
    }
});

// Global Vue Instance
var app = new Vue({
    el: '#app'
});