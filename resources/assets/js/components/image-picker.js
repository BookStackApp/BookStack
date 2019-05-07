
class ImagePicker {

    constructor(elem) {
        this.elem = elem;
        this.imageElem = elem.querySelector('img');
        this.imageInput = elem.querySelector('input[type=file]');
        this.resetInput = elem.querySelector('input[data-reset-input]');
        this.removeInput = elem.querySelector('input[data-remove-input]');

        this.defaultImage = elem.getAttribute('data-default-image');

        const resetButton = elem.querySelector('button[data-action="reset-image"]');
        resetButton.addEventListener('click', this.reset.bind(this));

        const removeButton = elem.querySelector('button[data-action="remove-image"]');
        if (removeButton) {
            removeButton.addEventListener('click', this.removeImage.bind(this));
        }

        this.imageInput.addEventListener('change', this.fileInputChange.bind(this));
    }

    fileInputChange() {
        this.resetInput.setAttribute('disabled', 'disabled');
        if (this.removeInput) {
            this.removeInput.setAttribute('disabled', 'disabled');
        }

        for (let file of this.imageInput.files) {
            this.imageElem.src = window.URL.createObjectURL(file);
        }
        this.imageElem.classList.remove('none');
    }

    reset() {
        this.imageInput.value = '';
        this.imageElem.src = this.defaultImage;
        this.resetInput.removeAttribute('disabled');
        if (this.removeInput) {
            this.removeInput.setAttribute('disabled', 'disabled');
        }
        this.imageElem.classList.remove('none');
    }

    removeImage() {
        this.imageInput.value = '';
        this.imageElem.classList.add('none');
        this.removeInput.removeAttribute('disabled');
        this.resetInput.setAttribute('disabled', 'disabled');
    }

}

export default ImagePicker;