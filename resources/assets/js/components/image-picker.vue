
<template>
    <div class="image-picker">
        <div>
            <img v-if="image && image !== 'none'" :src="image" :class="imageClass" alt="Image Preview">
            <img v-if="image === '' && defaultImage" :src="defaultImage" :class="imageClass" alt="Image Preview">
        </div>
        <button class="button" type="button" @click="showImageManager">Select Image</button>
        <br>
        <button class="text-button" @click="reset" type="button">Reset</button> <span class="sep">|</span> <button class="text-button neg" v-on:click="remove" type="button">Remove</button>
        <input type="hidden" :name="name" :id="name" v-model="image">
    </div>
</template>

<script>
    module.exports = {
        props: ['currentImage', 'name', 'imageClass', 'defaultImage'],
        data: function() {
            return {
                image: this.currentImage
            }
        },
        methods: {
            showImageManager: function(e) {
                var _this = this;
                ImageManager.show(function(image) {
                    _this.image = image.thumbs.custom || image.url;
                });
            },
            reset: function() {
                this.image = '';
            },
            remove: function() {
                this.image = 'none';
            }
        }
    };
</script>