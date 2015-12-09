
<template>
    <div class="image-picker">
        <div>
            <img v-if="image && image !== 'none'" :src="image" :class="imageClass" alt="Image Preview">
            <img v-if="image === '' && defaultImage" :src="defaultImage" :class="imageClass" alt="Image Preview">
        </div>
        <button class="button" type="button" @click="showImageManager">Select Image</button>
        <br>
        <button class="text-button" @click="reset" type="button">Reset</button> <span v-show="showRemove" class="sep">|</span> <button v-show="showRemove" class="text-button neg" @click="remove" type="button">Remove</button>
        <input type="hidden" :name="name" :id="name" v-model="value">
    </div>
</template>

<script>
    module.exports = {
        props: {
            currentImage: {
                required: true,
                type: String
            },
            currentId: {
                required: false,
                default: 'false',
                type: String
            },
            name: {
                required: true,
                type: String
            },
            defaultImage: {
                required: true,
                type: String
            },
            imageClass: {
                required: true,
                type: String
            },
            resizeWidth: {
                type: String
            },
            resizeHeight: {
                type: String
            },
            resizeCrop: {
                type: Boolean
            },
            showRemove: {
                type: Boolean,
                default: 'true'
            }
        },
        data: function() {
            return {
                image: this.currentImage,
                value: false
            }
        },
        compiled: function() {
            this.value = this.currentId === 'false' ? this.currentImage : this.currentId;
        },
        methods: {
            setCurrentValue: function(imageModel, imageUrl) {
                this.image = imageUrl;
                this.value = this.currentId === 'false' ?  imageUrl : imageModel.id;
            },
            showImageManager: function(e) {
                var _this = this;
                ImageManager.show(function(image) {
                    _this.updateImageFromModel(image);
                });
            },
            reset: function() {
                this.setCurrentValue({id: 0}, this.defaultImage);
            },
            remove: function() {
                this.image = 'none';
            },
            updateImageFromModel: function(model) {
                var _this = this;
                var isResized = _this.resizeWidth && _this.resizeHeight;

                if (!isResized) {
                    _this.setCurrentValue(model, model.url);
                    return;
                }

                var cropped = _this.resizeCrop ? 'true' : 'false';
                var requestString = '/images/thumb/' + model.id + '/' + _this.resizeWidth + '/' + _this.resizeHeight + '/' + cropped;
                _this.$http.get(requestString, function(data) {
                    _this.setCurrentValue(model, data.url);
                });
            }
        }
    };
</script>