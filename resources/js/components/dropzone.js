import {Component} from './component';
import {Clipboard} from '../services/clipboard';
import {
    elem, getLoading, removeLoading,
} from '../services/dom';

export class Dropzone extends Component {

    setup() {
        this.container = this.$el;
        this.statusArea = this.$refs.statusArea;

        this.url = this.$opts.url;
        this.successMessage = this.$opts.successMessage;
        this.removeMessage = this.$opts.removeMessage;
        this.uploadLimit = Number(this.$opts.uploadLimit); // TODO - Use
        this.uploadLimitMessage = this.$opts.uploadLimitMessage; // TODO - Use
        this.timeoutMessage = this.$opts.timeoutMessage; // TODO - Use
        this.zoneText = this.$opts.zoneText;
        // window.uploadTimeout // TODO - Use
        // TODO - Click-to-upload buttons/areas
        // TODO - Drop zone highlighting of existing element
        //   (Add overlay via additional temp element).

        this.setupListeners();
    }

    setupListeners() {
        let depth = 0;

        this.container.addEventListener('dragenter', event => {
            event.preventDefault();
            depth += 1;

            if (depth === 1) {
                this.showOverlay();
            }
        });

        this.container.addEventListener('dragover', event => {
            event.preventDefault();
        });

        const reset = () => {
            this.hideOverlay();
            depth = 0;
        };

        this.container.addEventListener('dragend', event => {
            reset();
        });

        this.container.addEventListener('dragleave', event => {
            depth -= 1;
            if (depth === 0) {
                reset();
            }
        });

        this.container.addEventListener('drop', event => {
            event.preventDefault();
            reset();
            const clipboard = new Clipboard(event.dataTransfer);
            const files = clipboard.getFiles();
            for (const file of files) {
                this.createUploadFromFile(file);
            }
        });
    }

    showOverlay() {
        const overlay = this.container.querySelector('.dropzone-overlay');
        if (!overlay) {
            const zoneElem = elem('div', {class: 'dropzone-overlay'}, [this.zoneText]);
            this.container.append(zoneElem);
        }
    }

    hideOverlay() {
        const overlay = this.container.querySelector('.dropzone-overlay');
        if (overlay) {
            overlay.remove();
        }
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
                removeLoading(dom);
            },
            markSuccess(message) {
                status.setAttribute('data-status', 'success');
                status.textContent = message;
                removeLoading(dom);
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
        const image = elem('img', {src: ''});
        const status = elem('div', {class: 'dropzone-file-item-status'}, []);
        const progress = elem('div', {class: 'dropzone-file-item-progress'});
        const imageWrap = elem('div', {class: 'dropzone-file-item-image-wrap'}, [image]);

        const dom = elem('div', {class: 'dropzone-file-item'}, [
            imageWrap,
            elem('div', {class: 'dropzone-file-item-text-wrap'}, [
                elem('div', {class: 'dropzone-file-item-label'}, [file.name]),
                getLoading(),
                status,
            ]),
            progress,
        ]);

        if (file.type.startsWith('image/')) {
            image.src = URL.createObjectURL(file);
        }

        return {
            dom, progress, status,
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
