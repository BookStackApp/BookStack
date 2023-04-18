import {Component} from './component';

export class ImagePicker extends Component {

    setup() {
        this.imageElem = this.$refs.image;
        this.imageInput = this.$refs.imageInput;
        this.resetInput = this.$refs.resetInput;
        this.removeInput = this.$refs.removeInput;
        this.resetButton = this.$refs.resetButton;
        this.removeButton = this.$refs.removeButton || null;

        this.defaultImage = this.$opts.defaultImage;

        this.setupListeners();
    }

    setupListeners() {
        this.resetButton.addEventListener('click', this.reset.bind(this));

        if (this.removeButton) {
            this.removeButton.addEventListener('click', this.removeImage.bind(this));
        }

        this.imageInput.addEventListener('change', this.fileInputChange.bind(this));
    }

    fileInputChange() {
        this.resetInput.setAttribute('disabled', 'disabled');
        if (this.removeInput) {
            this.removeInput.setAttribute('disabled', 'disabled');
        }

        for (const file of this.imageInput.files) {
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
