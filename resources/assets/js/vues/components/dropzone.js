import DropZone from "dropzone";

const template = `
    <div class="dropzone-container">
        <div class="dz-message">{{placeholder}}</div>
    </div>
`;

const props = ['placeholder', 'uploadUrl', 'uploadedTo'];

// TODO - Remove jQuery usage
function mounted() {
   const container = this.$el;
   const _this = this;
   this._dz = new DropZone(container, {
        addRemoveLinks: true,
        dictRemoveFile: trans('components.image_upload_remove'),
        timeout: Number(window.uploadTimeout) || 60000,
        maxFilesize: Number(window.uploadLimit) || 256,
        url: function() {
            return _this.uploadUrl;
        },
        init: function () {
            const dz = this;

            dz.on('sending', function (file, xhr, data) {
                const token = window.document.querySelector('meta[name=token]').getAttribute('content');
                data.append('_token', token);
                const uploadedTo = typeof _this.uploadedTo === 'undefined' ? 0 : _this.uploadedTo;
                data.append('uploaded_to', uploadedTo);

                xhr.ontimeout = function (e) {
                    dz.emit('complete', file);
                    dz.emit('error', file, trans('errors.file_upload_timeout'));
                }
            });

            dz.on('success', function (file, data) {
                _this.$emit('success', {file, data});
                $(file.previewElement).fadeOut(400, function () {
                    dz.removeFile(file);
                });
            });

            dz.on('error', function (file, errorMessage, xhr) {
                _this.$emit('error', {file, errorMessage, xhr});

                function setMessage(message) {
                    $(file.previewElement).find('[data-dz-errormessage]').text(message);
                }

                if (xhr && xhr.status === 413) {
                    setMessage(trans('errors.server_upload_limit'))
                } else if (errorMessage.file) {
                    setMessage(errorMessage.file);
                }

            });
        }
   });
}

function data() {
    return {};
}

const methods = {
    onClose: function () {
        this._dz.removeAllFiles(true);
    }
};

export default {
    template,
    props,
    mounted,
    data,
    methods
};
