const moment = require('moment');
require('moment/locale/en-gb');
moment.locale('en-gb');

let autoSaveFrequency = 30;

let autoSave = false;
let draftErroring = false;

let currentContent = {
    title: false,
    html: false
};

let lastSave = 0;

function mounted() {
    let elem = this.$el;
    this.draftsEnabled = elem.getAttribute('drafts-enabled') === 'true';
    this.editorType = elem.getAttribute('editor-type');
    this.pageId= Number(elem.getAttribute('page-id'));
    this.isNewDraft = Number(elem.getAttribute('page-new-draft')) === 1;
    this.isUpdateDraft = Number(elem.getAttribute('page-update-draft')) === 1;

    if (this.pageId !== 0 && this.draftsEnabled) {
        window.setTimeout(() => {
            this.startAutoSave();
        }, 1000);
    }

    if (this.isUpdateDraft || this.isNewDraft) {
        this.draftText = trans('entities.pages_editing_draft');
    } else {
        this.draftText = trans('entities.pages_editing_page');
    }

    // Listen to save draft events from editor
    window.$events.listen('editor-save-draft', this.saveDraft);

    // Listen to content changes from the editor
    window.$events.listen('editor-html-change', html => {
        this.editorHTML = html;
    });
    window.$events.listen('editor-markdown-change', markdown => {
        this.editorMarkdown = markdown;
    });
}

let data = {
    draftsEnabled: false,
    editorType: 'wysiwyg',
    pagedId: 0,
    isNewDraft: false,
    isUpdateDraft: false,

    draftText: '',
    draftUpdated : false,
    changeSummary: '',

    editorHTML: '',
    editorMarkdown: '',
};

let methods = {

    startAutoSave() {
        currentContent.title = document.getElementById('name').value.trim();
        currentContent.html = this.editorHTML;

        autoSave = window.setInterval(() => {
            // Return if manually saved recently to prevent bombarding the server
            if (Date.now() - lastSave < (1000 * autoSaveFrequency)/2) return;
            let newTitle = document.getElementById('name').value.trim();
            let newHtml = this.editorHTML;

            if (newTitle !== currentContent.title || newHtml !== currentContent.html) {
                currentContent.html = newHtml;
                currentContent.title = newTitle;
                this.saveDraft();
            }

        }, 1000 * autoSaveFrequency);
    },

    saveDraft() {
        if (!this.draftsEnabled) return;

        let data = {
            name: document.getElementById('name').value.trim(),
            html: this.editorHTML
        };

        if (this.editorType === 'markdown') data.markdown = this.editorMarkdown;

        let url = window.baseUrl(`/ajax/page/${this.pageId}/save-draft`);
        window.$http.put(url, data).then(response => {
            draftErroring = false;
            let updateTime = moment.utc(moment.unix(response.data.timestamp)).toDate();
            if (!this.isNewPageDraft) this.isUpdateDraft = true;
            this.draftNotifyChange(response.data.message + moment(updateTime).format('HH:mm'));
            lastSave = Date.now();
        }, errorRes => {
            if (draftErroring) return;
            window.$events('error', trans('errors.page_draft_autosave_fail'));
            draftErroring = true;
        });
    },


    draftNotifyChange(text) {
        this.draftText = text;
        this.draftUpdated = true;
        window.setTimeout(() => {
            this.draftUpdated = false;
        }, 2000);
    },

    discardDraft() {
        let url = window.baseUrl(`/ajax/page/${this.pageId}`);
        window.$http.get(url).then(response => {
            if (autoSave) window.clearInterval(autoSave);

            this.draftText = trans('entities.pages_editing_page');
            this.isUpdateDraft = false;
            window.$events.emit('editor-html-update', response.data.html);
            window.$events.emit('editor-markdown-update', response.data.markdown || response.data.html);

            document.getElementById('name').value = response.data.name;
            window.setTimeout(() => {
                this.startAutoSave();
            }, 1000);
            window.$events.emit('success', trans('entities.pages_draft_discarded'));
        });
    },

};

let computed = {
    changeSummaryShort() {
        let len = this.changeSummary.length;
        if (len === 0) return trans('entities.pages_edit_set_changelog');
        if (len <= 16) return this.changeSummary;
        return this.changeSummary.slice(0, 16) + '...';
    }
};

module.exports = {
    mounted, data, methods, computed,
};