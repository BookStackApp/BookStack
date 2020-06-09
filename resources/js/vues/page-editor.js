import * as Dates from "../services/dates";

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
    this.titleElem = elem.querySelector('input[name=name]');
    this.hasDefaultTitle = this.titleElem.closest('[is-default-value]') !== null;

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

    // Listen to save events from editor
    window.$events.listen('editor-save-draft', this.saveDraft);
    window.$events.listen('editor-save-page', this.savePage);

    // Listen to content changes from the editor
    window.$events.listen('editor-html-change', html => {
        this.editorHTML = html;
    });
    window.$events.listen('editor-markdown-change', markdown => {
        this.editorMarkdown = markdown;
    });

    this.setInitialFocus();
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

    hasDefaultTitle: false,
    titleElem: null,
};

let methods = {

    setInitialFocus() {
        if (this.hasDefaultTitle) {
            this.titleElem.select();
        } else {
            window.setTimeout(() => {
                this.$events.emit('editor::focus', '');
            }, 500);
        }
    },

    startAutoSave() {
        currentContent.title = this.titleElem.value.trim();
        currentContent.html = this.editorHTML;

        autoSave = window.setInterval(() => {
            // Return if manually saved recently to prevent bombarding the server
            if (Date.now() - lastSave < (1000 * autoSaveFrequency)/2) return;
            const newTitle = this.titleElem.value.trim();
            const newHtml = this.editorHTML;

            if (newTitle !== currentContent.title || newHtml !== currentContent.html) {
                currentContent.html = newHtml;
                currentContent.title = newTitle;
                this.saveDraft();
            }

        }, 1000 * autoSaveFrequency);
    },

    saveDraft() {
        if (!this.draftsEnabled) return;

        const data = {
            name: this.titleElem.value.trim(),
            html: this.editorHTML
        };

        if (this.editorType === 'markdown') data.markdown = this.editorMarkdown;

        const url = window.baseUrl(`/ajax/page/${this.pageId}/save-draft`);
        window.$http.put(url, data).then(response => {
            draftErroring = false;
            if (!this.isNewDraft) this.isUpdateDraft = true;
            this.draftNotifyChange(`${response.data.message} ${Dates.utcTimeStampToLocalTime(response.data.timestamp)}`);
            lastSave = Date.now();
        }, errorRes => {
            if (draftErroring) return;
            window.$events.emit('error', trans('errors.page_draft_autosave_fail'));
            draftErroring = true;
        });
    },

    savePage() {
        this.$el.closest('form').submit();
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

            this.titleElem.value = response.data.name;
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

export default {
    mounted, data, methods, computed,
};