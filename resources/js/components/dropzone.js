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
        this.errorMessage = this.$opts.errorMessage;
        this.uploadLimitMb = Number(this.$opts.uploadLimit);
        this.uploadLimitMessage = this.$opts.uploadLimitMessage;
        this.zoneText = this.$opts.zoneText;
        this.fileAcceptTypes = this.$opts.fileAccept;

        this.setupListeners();
    }

    setupListeners() {
        onSelect(this.selectButtons, this.manualSelectHandler.bind(this));
        this.setupDropTargetHandlers();
    }

    setupDropTargetHandlers() {
        let depth = 0;

        const reset = () => {
            this.hideOverlay();
            depth = 0;
        };

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

        this.dropTarget.addEventListener('dragend', reset);
        this.dropTarget.addEventListener('dragleave', () => {
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
        const input = elem('input', {type: 'file', style: 'left: -400px; visibility: hidden; position: fixed;', accept: this.fileAcceptTypes});
        this.container.append(input);
        input.click();
        input.addEventListener('change', () => {
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
        const {
            dom, status, progress, dismiss,
        } = this.createDomForFile(file);
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
                this.updateProgress(100);
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

        // Enforce early upload filesize limit
        if (file.size > (this.uploadLimitMb * 1000000)) {
            upload.markError(this.uploadLimitMessage);
            return upload;
        }

        this.startXhrForUpload(upload);

        return upload;
    }

    /**
     * @param {Upload} upload
     */
    startXhrForUpload(upload) {
        const formData = new FormData();
        formData.append('file', upload.file, upload.file.name);
        const component = this;

        const req = window.$http.createXMLHttpRequest('POST', this.url, {
            error() {
                upload.markError(component.errorMessage);
            },
            readystatechange() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    upload.markSuccess(component.successMessage);
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
        const image = elem('img', {src: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M9.224 7.373a.924.924 0 0 0-.92.925l-.006 7.404c0 .509.412.925.921.925h5.557a.928.928 0 0 0 .926-.925v-5.553l-2.777-2.776Zm3.239 3.239V8.067l2.545 2.545z' style='fill:%23000;fill-opacity:.75'/%3E%3C/svg%3E"});
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
            dom.addEventListener('animationend', () => {
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
