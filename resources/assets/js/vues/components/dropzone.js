const DropZone = require("dropzone");

const template = `
    <div class="dropzone-container">
        <div class="dz-message">{{placeholder}}</div>
    </div>
`;

const props = ['placeholder', 'uploadUrl', 'uploadedTo'];

// TODO - Remove jQuery usage
function mounted() {
   let container = this.$el;
   let _this = this;
   this._dz = new DropZone(container, {
	addRemoveLinks: true,
	dictRemoveFile: trans('components.image_upload_remove'),
        url: function() {
            return _this.uploadUrl;
        },
        init: function () {
            let dz = this;

            dz.on('sending', function (file, xhr, data) {
                let token = window.document.querySelector('meta[name=token]').getAttribute('content');
                data.append('_token', token);
                let uploadedTo = typeof _this.uploadedTo === 'undefined' ? 0 : _this.uploadedTo;
                data.append('uploaded_to', uploadedTo);
            });

            dz.on('success', function (file, data) {
                _this.$emit('success', {file, data});
                $(file.previewElement).fadeOut(400, function () {
                    dz.removeFile(file);
                });
            });

            dz.on('error', function (file, errorMessage, xhr) {
                _this.$emit('error', {file, errorMessage, xhr});
                console.log(errorMessage);
                console.log(xhr);
                function setMessage(message) {
                    $(file.previewElement).find('[data-dz-errormessage]').text(message);
                }

                if (xhr && xhr.status === 413) setMessage(trans('errors.server_upload_limit'));
                if (errorMessage.file) setMessage(errorMessage.file[0]);
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

module.exports = {
    template,
    props,
    mounted,
    data,
    methods
};
