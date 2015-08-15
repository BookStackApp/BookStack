
<div id="image-manager">
    <div class="overlay" v-el="overlay" v-on="click: overlayClick" style="display:none;">
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
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name">Image Name</label>
                            <input type="text" id="name" name="name" v-model="selectedImage.name">
                        </div>
                    </form>
                    <hr class="even">
                    <form v-on="submit: deleteImage" v-el="imageDeleteForm">
                        {{ csrf_field() }}
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
