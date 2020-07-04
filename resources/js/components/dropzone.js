import DropZoneLib from "dropzone";
import {fadeOut} from "../services/animations";

/**
 * Dropzone
 * @extends {Component}
 */
class Dropzone {
    setup() {
        this.container = this.$el;
        this.url = this.$opts.url;
        this.successMessage = this.$opts.successMessage;
        this.removeMessage = this.$opts.removeMessage;
        this.uploadLimitMessage = this.$opts.uploadLimitMessage;
        this.timeoutMessage = this.$opts.timeoutMessage;

        const _this = this;
        this.dz = new DropZoneLib(this.container, {
            addRemoveLinks: true,
            dictRemoveFile: this.removeMessage,
            timeout: Number(window.uploadTimeout) || 60000,
            maxFilesize: Number(window.uploadLimit) || 256,
            url: this.url,
            withCredentials: true,
            init() {
                this.dz = this;
                this.dz.on('sending', _this.onSending.bind(_this));
                this.dz.on('success', _this.onSuccess.bind(_this));
                this.dz.on('error', _this.onError.bind(_this));
            }
        });
    }

    onSending(file, xhr, data) {

        const token = window.document.querySelector('meta[name=token]').getAttribute('content');
        data.append('_token', token);

        xhr.ontimeout = (e) => {
            this.dz.emit('complete', file);
            this.dz.emit('error', file, this.timeoutMessage);
        }
    }

    onSuccess(file, data) {
        this.container.dispatchEvent(new Event('dropzone'))
        this.$emit('success', {file, data});

        if (this.successMessage) {
            window.$events.emit('success', this.successMessage);
        }

        fadeOut(file.previewElement, 800, () => {
            this.dz.removeFile(file);
        });
    }

    onError(file, errorMessage, xhr) {
        this.$emit('error', {file, errorMessage, xhr});

        const setMessage = (message) => {
            const messsageEl = file.previewElement.querySelector('[data-dz-errormessage]');
            messsageEl.textContent = message;
        }

        if (xhr && xhr.status === 413) {
            setMessage(this.uploadLimitMessage);
        } else if (errorMessage.file) {
            setMessage(errorMessage.file);
        }
    }

    removeAll() {
        this.dz.removeAllFiles(true);
    }
}

export default Dropzone;