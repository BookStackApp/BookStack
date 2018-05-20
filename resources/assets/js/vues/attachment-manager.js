const draggable = require('vuedraggable');
const dropzone = require('./components/dropzone');

function mounted() {
    this.pageId = this.$el.getAttribute('page-id');
    this.file = this.newFile();

    this.$http.get(window.baseUrl(`/attachments/get/page/${this.pageId}`)).then(resp => {
        this.files = resp.data;
    }).catch(err => {
        this.checkValidationErrors('get', err);
    });
}

let data = {
    pageId: null,
    files: [],
    fileToEdit: null,
    file: {},
    tab: 'list',
    editTab: 'file',
    errors: {link: {}, edit: {}, delete: {}}
};

const components = {dropzone, draggable};

let methods = {

    newFile() {
        return {page_id: this.pageId};
    },

    getFileUrl(file) {
        if (file.external && file.path.indexOf('http') !== 0) {
            return file.path;
        }
        return window.baseUrl(`/attachments/${file.id}`);
    },

    fileSortUpdate() {
        this.$http.put(window.baseUrl(`/attachments/sort/page/${this.pageId}`), {files: this.files}).then(resp => {
            this.$events.emit('success', resp.data.message);
        }).catch(err => {
            this.checkValidationErrors('sort', err);
        });
    },

    startEdit(file) {
        this.fileToEdit = Object.assign({}, file);
        this.fileToEdit.link = file.external ? file.path : '';
        this.editTab = file.external ? 'link' : 'file';
    },

    deleteFile(file) {
        if (!file.deleting) return file.deleting = true;

        this.$http.delete(window.baseUrl(`/attachments/${file.id}`)).then(resp => {
            this.$events.emit('success', resp.data.message);
            this.files.splice(this.files.indexOf(file), 1);
        }).catch(err => {
            this.checkValidationErrors('delete', err)
        });
    },

    uploadSuccess(upload) {
        this.files.push(upload.data);
        this.$events.emit('success', trans('entities.attachments_file_uploaded'));
    },

    uploadSuccessUpdate(upload) {
        let fileIndex = this.filesIndex(upload.data);
        if (fileIndex === -1) {
            this.files.push(upload.data)
        } else {
            this.files.splice(fileIndex, 1, upload.data);
        }

        if (this.fileToEdit && this.fileToEdit.id === upload.data.id) {
            this.fileToEdit = Object.assign({}, upload.data);
        }
        this.$events.emit('success', trans('entities.attachments_file_updated'));
    },

    checkValidationErrors(groupName, err) {
        if (typeof err.response.data === "undefined" && typeof err.response.data === "undefined") return;
        this.errors[groupName] = err.response.data;
    },

    getUploadUrl(file) {
        let url = window.baseUrl(`/attachments/upload`);
        if (typeof file !== 'undefined') url += `/${file.id}`;
        return url;
    },

    cancelEdit() {
        this.fileToEdit = null;
    },

    attachNewLink(file) {
        file.uploaded_to = this.pageId;
        this.errors.link = {};
        this.$http.post(window.baseUrl('/attachments/link'), file).then(resp => {
            this.files.push(resp.data);
            this.file = this.newFile();
            this.$events.emit('success', trans('entities.attachments_link_attached'));
        }).catch(err => {
            this.checkValidationErrors('link', err);
        });
    },

    updateFile(file) {
        $http.put(window.baseUrl(`/attachments/${file.id}`), file).then(resp => {
            let search = this.filesIndex(resp.data);
            if (search === -1) {
                this.files.push(resp.data);
            } else {
                this.files.splice(search, 1, resp.data);
            }

            if (this.fileToEdit && !file.external) this.fileToEdit.link = '';
            this.fileToEdit = false;

            this.$events.emit('success', trans('entities.attachments_updated_success'));
        }).catch(err => {
            this.checkValidationErrors('edit', err);
        });
    },

    filesIndex(file) {
        for (let i = 0, len = this.files.length; i < len; i++) {
            if (this.files[i].id === file.id) return i;
        }
        return -1;
    }

};

module.exports = {
    data, methods, mounted, components,
};