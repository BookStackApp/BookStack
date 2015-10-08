
<template>
    <div class="image-picker">
        <div>
            <img v-if="image && image !== 'none'" v-attr="src: image, class: imageClass" alt="Image Preview">
        </div>
        <button class="button" type="button" v-on="click: showImageManager">Select Image</button>
        <br>
        <button class="text-button" v-on="click: reset" type="button">Reset</button> <span class="sep">|</span> <button class="text-button neg" v-on="click: remove" type="button">Remove</button>
        <input type="hidden" v-attr="name: name, id: name" v-model="image">
    </div>
</template>

<script>
    module.exports = {
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
    }
</script>