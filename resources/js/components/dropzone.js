import {Component} from './component';
import {Clipboard} from '../services/clipboard';
import {
    elem, getLoading, onSelect, removeLoading,
} from '../services/dom';

export class Dropzone extends Component {

    setup() {
        this.container = this.$el;
        this.statusArea = this.$refs.statusArea;
        this.dropTarget = this.$refs.dropTarget;
        this.selectButtons = this.$manyRefs.selectButton || [];

        this.url = this.$opts.url;
        this.successMessage = this.$opts.successMessage;
        this.removeMessage = this.$opts.removeMessage;
        this.uploadLimit = Number(this.$opts.uploadLimit); // TODO - Use
        this.uploadLimitMessage = this.$opts.uploadLimitMessage; // TODO - Use
        this.timeoutMessage = this.$opts.timeoutMessage; // TODO - Use
        this.zoneText = this.$opts.zoneText;
        // window.uploadTimeout // TODO - Use

        this.setupListeners();
    }

    setupListeners() {
        onSelect(this.selectButtons, this.manualSelectHandler.bind(this));

        let depth = 0;

        this.dropTarget.addEventListener('dragenter', event => {
            event.preventDefault();
            depth += 1;

            if (depth === 1) {
                this.showOverlay();
            }
        });

        this.dropTarget.addEventListener('dragover', event => {
            event.preventDefault();
        });

        const reset = () => {
            this.hideOverlay();
            depth = 0;
        };

        this.dropTarget.addEventListener('dragend', event => {
            reset();
        });

        this.dropTarget.addEventListener('dragleave', event => {
            depth -= 1;
            if (depth === 0) {
                reset();
            }
        });

        this.dropTarget.addEventListener('drop', event => {
            event.preventDefault();
            reset();
            const clipboard = new Clipboard(event.dataTransfer);
            const files = clipboard.getFiles();
            for (const file of files) {
                this.createUploadFromFile(file);
            }
        });
    }

    manualSelectHandler() {
        const input = elem('input', {type: 'file', style: 'left: -400px; visibility: hidden; position: fixed;'});
        this.container.append(input);
        input.click();
        input.addEventListener('change', event => {
            for (const file of input.files) {
                this.createUploadFromFile(file);
            }
            input.remove();
        });
    }

    showOverlay() {
        const overlay = this.dropTarget.querySelector('.dropzone-overlay');
        if (!overlay) {
            const zoneElem = elem('div', {class: 'dropzone-overlay'}, [this.zoneText]);
            this.dropTarget.append(zoneElem);
        }
    }

    hideOverlay() {
        const overlay = this.dropTarget.querySelector('.dropzone-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    /**
     * @param {File} file
     * @return {Upload}
     */
    createUploadFromFile(file) {
        const {dom, status, progress, dismiss} = this.createDomForFile(file);
        this.statusArea.append(dom);
        const component = this;

        const upload = {
            file,
            dom,
            updateProgress(percentComplete) {
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
                setTimeout(dismiss, 2400);
                component.$emit('upload-success', {
                    name: file.name,
                });
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
     * @return {{image: Element, dom: Element, progress: Element, status: Element, dismiss: function}}
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

        const dismiss = () => {
            dom.classList.add('dismiss');
            dom.addEventListener('animationend', event => {
                dom.remove();
            });
        };

        dom.addEventListener('click', dismiss);

        return {
            dom, progress, status, dismiss,
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
