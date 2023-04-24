import {Component} from './component';
import {Clipboard} from '../services/clipboard';

export class Dropzone extends Component {

    setup() {
        this.container = this.$el;
        this.url = this.$opts.url;
        this.successMessage = this.$opts.successMessage;
        this.removeMessage = this.$opts.removeMessage;
        this.uploadLimit = Number(this.$opts.uploadLimit); // TODO - Use
        this.uploadLimitMessage = this.$opts.uploadLimitMessage; // TODO - Use
        this.timeoutMessage = this.$opts.timeoutMessage; // TODO - Use
        // window.uploadTimeout // TODO - Use
        // TODO - Click-to-upload buttons/areas
        // TODO - Drop zone highlighting of existing element
        //   (Add overlay via additional temp element).

        this.setupListeners();
    }

    setupListeners() {
        this.container.addEventListener('dragenter', event => {
            this.container.style.border = '1px dotted tomato';
            event.preventDefault();
        });

        this.container.addEventListener('dragover', event => {
            event.preventDefault();
        });

        const reset = () => {
            this.container.style.border = null;
        };

        this.container.addEventListener('dragend', event => {
            reset();
        });

        this.container.addEventListener('dragleave', event => {
            reset();
        });

        this.container.addEventListener('drop', event => {
            event.preventDefault();
            const clipboard = new Clipboard(event.dataTransfer);
            const files = clipboard.getFiles();
            for (const file of files) {
                this.createUploadFromFile(file);
            }
        });
    }

    /**
     * @param {File} file
     * @return {Upload}
     */
    createUploadFromFile(file) {
        const {dom, status} = this.createDomForFile(file);
        this.container.append(dom);

        const formData = new FormData();
        formData.append('file', file, file.name);

        // TODO - Change to XMLHTTPRequest so we can track progress.
        const uploadPromise = window.$http.post(this.url, formData);

        const upload = {
            file,
            dom,
            markError(message) {
                status.setAttribute('data-status', 'error');
                status.textContent = message;
            },
            markSuccess(message) {
                status.setAttribute('data-status', 'success');
                status.textContent = message;
            },
        };

        uploadPromise.then(returnData => {
            upload.markSuccess(returnData.statusText);
        }).catch(err => {
            upload.markError(err?.data?.message || err.message);
        });

        return upload;
    }

    /**
     * @param {File} file
     * @return {{image: Element, dom: Element, progress: Element, label: Element, status: Element}}
     */
    createDomForFile(file) {
        const dom = document.createElement('div');
        const label = document.createElement('div');
        const status = document.createElement('div');
        const progress = document.createElement('div');
        const image = document.createElement('img');

        dom.classList.add('dropzone-file-item');
        status.classList.add('dropzone-file-item-status');
        progress.classList.add('dropzone-file-item-progress');

        image.src = ''; // TODO - file icon
        label.innerText = file.name;

        if (file.type.startsWith('image/')) {
            image.src = URL.createObjectURL(file);
        }

        dom.append(image, label, progress, status);
        return {
            dom, label, image, progress, status,
        };
    }

}

/**
 * @typedef Upload
 * @property {File} file
 * @property {Element} dom
 * @property {function(String)} markError
 * @property {function(String)} markSuccess
 */
