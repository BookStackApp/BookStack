
class ImagePicker {

    constructor(elem) {
        this.elem = elem;
        this.imageElem = elem.querySelector('img');
        this.input = elem.querySelector('input');

        this.isUsingIds = elem.getAttribute('data-current-id') !== '';
        this.isResizing = elem.getAttribute('data-resize-height') && elem.getAttribute('data-resize-width');
        this.isResizeCropping = elem.getAttribute('data-resize-crop') !== '';

        let selectButton = elem.querySelector('button[data-action="show-image-manager"]');
        selectButton.addEventListener('click', this.selectImage.bind(this));

        let resetButton = elem.querySelector('button[data-action="reset-image"]');
        resetButton.addEventListener('click', this.reset.bind(this));

        let removeButton = elem.querySelector('button[data-action="remove-image"]');
        if (removeButton) {
            removeButton.addEventListener('click', this.removeImage.bind(this));
        }
    }

    selectImage() {
        window.ImageManager.show(image => {
            if (!this.isResizing) {
                this.setImage(image);
                return;
            }

            let requestString = '/images/thumb/' + image.id + '/' + this.elem.getAttribute('data-resize-width') + '/' + this.elem.getAttribute('data-resize-height') + '/' + (this.isResizeCropping ? 'true' : 'false');

            window.$http.get(window.baseUrl(requestString)).then(resp => {
                image.url = resp.data.url;
                this.setImage(image);
            });
        });
    }

    reset() {
        this.setImage({id: 0, url: this.elem.getAttribute('data-default-image')});
    }

    setImage(image) {
        this.imageElem.src = image.url;
        this.input.value = this.isUsingIds ? image.id : image.url;
        this.imageElem.classList.remove('none');
    }

    removeImage() {
        this.imageElem.src = this.elem.getAttribute('data-default-image');
        this.imageElem.classList.add('none');
        this.input.value = 'none';
    }

}

module.exports = ImagePicker;