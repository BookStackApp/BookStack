
<div id="image-manager">
    <div class="overlay" v-el="overlay" v-on="click: overlayClick" style="display:none;">
        <div class="image-manager-body">
            <div class="image-manager-content">
                <div class="dropzone-container" v-el="dropZone">
                    <div class="dz-message">Drop files or click here to upload</div>
                </div>
                <div class="image-manager-list">
                    <div v-repeat="image: images">
                        <img v-class="selected: (image==selectedImage)" v-attr="src: image.thumbnail" v-on="click: imageClick(image)" alt="@{{image.name}}">
                    </div>
                    <div class="load-more" v-show="hasMore" v-on="click: fetchData">Load More</div>
                </div>
            </div>
            <div class="image-manager-sidebar">
                <button class="neg button image-manager-close" v-on="click: hide()">x</button>
                <h2>Images</h2>
            </div>
        </div>
    </div>
</div>
