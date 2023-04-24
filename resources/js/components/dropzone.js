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
        const {dom, status, progress} = this.createDomForFile(file);
        this.container.append(dom);

        const upload = {
            file,
            dom,
            updateProgress(percentComplete) {
                console.log(`progress: ${percentComplete}%`);
                progress.textContent = `${percentComplete}%`;
                progress.style.width = `${percentComplete}%`;
            },
            markError(message) {
                status.setAttribute('data-status', 'error');
                status.textContent = message;
            },
            markSuccess(message) {
                status.setAttribute('data-status', 'success');
                status.textContent = message;
            },
        };

        this.startXhrForUpload(upload);

        return upload;
    }

    /**
     * @param {Upload} upload
     */
    startXhrForUpload(upload) {
        const formData = new FormData();
        formData.append('file', upload.file, upload.file.name);

        const req = window.$http.createXMLHttpRequest('POST', this.url, {
            error() {
                upload.markError('Upload failed'); // TODO - Update text
            },
            readystatechange() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    upload.markSuccess('Finished upload!');
                } else if (this.readyState === XMLHttpRequest.DONE && this.status >= 400) {
                    const content = this.responseText;
                    const data = content.startsWith('{') ? JSON.parse(content) : {message: content};
                    const message = data?.message || content;
                    upload.markError(message);
                }
            },
        });

        req.upload.addEventListener('progress', evt => {
            const percent = Math.min(Math.ceil((evt.loaded / evt.total) * 100), 100);
            upload.updateProgress(percent);
        });

        req.setRequestHeader('Accept', 'application/json');
        req.send(formData);
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
 * @property {function(Number)} updateProgress
 * @property {function(String)} markError
 * @property {function(String)} markSuccess
 */
