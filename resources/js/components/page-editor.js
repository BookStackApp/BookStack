import * as Dates from "../services/dates";
import {onSelect} from "../services/dom";

/**
 * Page Editor
 * @extends {Component}
 */
class PageEditor {
    setup() {
        // Options
        this.draftsEnabled = this.$opts.draftsEnabled === 'true';
        this.editorType = this.$opts.editorType;
        this.pageId = Number(this.$opts.pageId);
        this.isNewDraft = this.$opts.pageNewDraft === 'true';
        this.hasDefaultTitle = this.$opts.hasDefaultTitle || false;

        // Elements
        this.container = this.$el;
        this.titleElem = this.$refs.titleContainer.querySelector('input');
        this.saveDraftButton = this.$refs.saveDraft;
        this.discardDraftButton = this.$refs.discardDraft;
        this.discardDraftWrap = this.$refs.discardDraftWrap;
        this.draftDisplay = this.$refs.draftDisplay;
        this.draftDisplayIcon = this.$refs.draftDisplayIcon;
        this.changelogInput = this.$refs.changelogInput;
        this.changelogDisplay = this.$refs.changelogDisplay;

        // Translations
        this.draftText = this.$opts.draftText;
        this.autosaveFailText = this.$opts.autosaveFailText;
        this.editingPageText = this.$opts.editingPageText;
        this.draftDiscardedText = this.$opts.draftDiscardedText;
        this.setChangelogText = this.$opts.setChangelogText;

        // State data
        this.editorHTML = '';
        this.editorMarkdown = '';
        this.autoSave = {
            interval: null,
            frequency: 30000,
            last: 0,
        };

        if (this.pageId !== 0 && this.draftsEnabled) {
            window.setTimeout(() => {
                this.startAutoSave();
            }, 1000);
        }
        this.draftDisplay.innerHTML = this.draftText;

        this.setupListeners();
        this.setInitialFocus();
    }

    setupListeners() {
        // Listen to save events from editor
        window.$events.listen('editor-save-draft', this.saveDraft.bind(this));
        window.$events.listen('editor-save-page', this.savePage.bind(this));

        // Listen to content changes from the editor
        window.$events.listen('editor-html-change', html => {
            this.editorHTML = html;
        });
        window.$events.listen('editor-markdown-change', markdown => {
            this.editorMarkdown = markdown;
        });

        // Changelog controls
        this.changelogInput.addEventListener('change', this.updateChangelogDisplay.bind(this));

        // Draft Controls
        onSelect(this.saveDraftButton, this.saveDraft.bind(this));
        onSelect(this.discardDraftButton, this.discardDraft.bind(this));
    }

    setInitialFocus() {
        console.log({'HAS': this.hasDefaultTitle});
        if (this.hasDefaultTitle) {
            return this.titleElem.select();
        }

        window.setTimeout(() => {
            window.$events.emit('editor::focus', '');
        }, 500);
    }

    startAutoSave() {
        let lastContent = this.titleElem.value.trim() + '::' + this.editorHTML;
        this.autoSaveInterval = window.setInterval(() => {
            // Stop if manually saved recently to prevent bombarding the server
            let savedRecently = (Date.now() - this.autoSave.last < (this.autoSave.frequency)/2);
            if (savedRecently) return;
            const newContent = this.titleElem.value.trim() + '::' + this.editorHTML;
            if (newContent !== lastContent) {
                lastContent = newContent;
                this.saveDraft();
            }

        }, this.autoSave.frequency);
    }

    savePage() {
        this.container.closest('form').submit();
    }

    async saveDraft() {
        const data = {
            name: this.titleElem.value.trim(),
            html: this.editorHTML,
        };

        if (this.editorType === 'markdown') {
            data.markdown = this.editorMarkdown;
        }

        try {
            const resp = await window.$http.put(`/ajax/page/${this.pageId}/save-draft`, data);
            if (!this.isNewDraft) {
                this.toggleDiscardDraftVisibility(true);
            }
            this.draftNotifyChange(`${resp.data.message} ${Dates.utcTimeStampToLocalTime(resp.data.timestamp)}`);
            this.autoSave.last = Date.now();
        } catch (err) {
            // Save the editor content in LocalStorage as a last resort, just in case.
            try {
                const saveKey = `draft-save-fail-${(new Date()).toISOString()}`;
                window.localStorage.setItem(saveKey, JSON.stringify(data));
            } catch (err) {}

            window.$events.emit('error', this.autosaveFailText);
        }

    }

    draftNotifyChange(text) {
        this.draftDisplay.innerText = text;
        this.draftDisplayIcon.classList.add('visible');
        window.setTimeout(() => {
            this.draftDisplayIcon.classList.remove('visible');
        }, 2000);
    }

    async discardDraft() {
        let response;
        try {
            response = await window.$http.get(`/ajax/page/${this.pageId}`);
        } catch (e) {
            return console.error(e);
        }

        if (this.autoSave.interval) {
            window.clearInterval(this.autoSave.interval);
        }

        this.draftDisplay.innerText = this.editingPageText;
        this.toggleDiscardDraftVisibility(false);
        window.$events.emit('editor-html-update', response.data.html || '');
        window.$events.emit('editor-markdown-update', response.data.markdown || response.data.html);

        this.titleElem.value = response.data.name;
        window.setTimeout(() => {
            this.startAutoSave();
        }, 1000);
        window.$events.emit('success', this.draftDiscardedText);

    }

    updateChangelogDisplay() {
        let summary = this.changelogInput.value.trim();
        if (summary.length === 0) {
            summary = this.setChangelogText;
        } else if (summary.length > 16) {
            summary = summary.slice(0, 16) + '...';
        }
        this.changelogDisplay.innerText = summary;
    }

    toggleDiscardDraftVisibility(show) {
        this.discardDraftWrap.classList.toggle('hidden', !show);
    }

}

export default PageEditor;