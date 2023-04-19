import DropZoneLib from 'dropzone';
import {fadeOut} from '../services/animations';
import {Component} from './component';

export class Dropzone extends Component {

    setup() {
        this.container = this.$el;
        this.url = this.$opts.url;
        this.successMessage = this.$opts.successMessage;
        this.removeMessage = this.$opts.removeMessage;
        this.uploadLimit = Number(this.$opts.uploadLimit);
        this.uploadLimitMessage = this.$opts.uploadLimitMessage;
        this.timeoutMessage = this.$opts.timeoutMessage;

        const component = this;
        this.dz = new DropZoneLib(this.container, {
            addRemoveLinks: true,
            dictRemoveFile: this.removeMessage,
            timeout: Number(window.uploadTimeout) || 60000,
            maxFilesize: this.uploadLimit,
            url: this.url,
            withCredentials: true,
            init() {
                this.dz = this;
                this.dz.on('sending', component.onSending.bind(component));
                this.dz.on('success', component.onSuccess.bind(component));
                this.dz.on('error', component.onError.bind(component));
            },
        });
    }

    onSending(file, xhr, data) {
        const token = window.document.querySelector('meta[name=token]').getAttribute('content');
        data.append('_token', token);

        xhr.ontimeout = () => {
            this.dz.emit('complete', file);
            this.dz.emit('error', file, this.timeoutMessage);
        };
    }

    onSuccess(file, data) {
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

        const setMessage = message => {
            const messsageEl = file.previewElement.querySelector('[data-dz-errormessage]');
            messsageEl.textContent = message;
        };

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
