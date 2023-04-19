import * as Dates from '../services/dates';
import {onSelect} from '../services/dom';
import {debounce} from '../services/util';
import {Component} from './component';

export class PageEditor extends Component {

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
        this.changeEditorButtons = this.$manyRefs.changeEditor || [];
        this.switchDialogContainer = this.$refs.switchDialog;

        // Translations
        this.draftText = this.$opts.draftText;
        this.autosaveFailText = this.$opts.autosaveFailText;
        this.editingPageText = this.$opts.editingPageText;
        this.draftDiscardedText = this.$opts.draftDiscardedText;
        this.setChangelogText = this.$opts.setChangelogText;

        // State data
        this.autoSave = {
            interval: null,
            frequency: 30000,
            last: 0,
            pendingChange: false,
        };
        this.shownWarningsCache = new Set();

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
        const onContentChange = () => {
            this.autoSave.pendingChange = true;
        };
        window.$events.listen('editor-html-change', onContentChange);
        window.$events.listen('editor-markdown-change', onContentChange);

        // Listen to changes on the title input
        this.titleElem.addEventListener('input', onContentChange);

        // Changelog controls
        const updateChangelogDebounced = debounce(this.updateChangelogDisplay.bind(this), 300, false);
        this.changelogInput.addEventListener('input', updateChangelogDebounced);

        // Draft Controls
        onSelect(this.saveDraftButton, this.saveDraft.bind(this));
        onSelect(this.discardDraftButton, this.discardDraft.bind(this));

        // Change editor controls
        onSelect(this.changeEditorButtons, this.changeEditor.bind(this));
    }

    setInitialFocus() {
        if (this.hasDefaultTitle) {
            this.titleElem.select();
            return;
        }

        window.setTimeout(() => {
            window.$events.emit('editor::focus', '');
        }, 500);
    }

    startAutoSave() {
        this.autoSave.interval = window.setInterval(this.runAutoSave.bind(this), this.autoSave.frequency);
    }

    runAutoSave() {
        // Stop if manually saved recently to prevent bombarding the server
        const savedRecently = (Date.now() - this.autoSave.last < (this.autoSave.frequency) / 2);
        if (savedRecently || !this.autoSave.pendingChange) {
            return;
        }

        this.saveDraft();
    }

    savePage() {
        this.container.closest('form').submit();
    }

    async saveDraft() {
        const data = {name: this.titleElem.value.trim()};

        const editorContent = this.getEditorComponent().getContent();
        Object.assign(data, editorContent);

        let didSave = false;
        try {
            const resp = await window.$http.put(`/ajax/page/${this.pageId}/save-draft`, data);
            if (!this.isNewDraft) {
                this.toggleDiscardDraftVisibility(true);
            }

            this.draftNotifyChange(`${resp.data.message} ${Dates.utcTimeStampToLocalTime(resp.data.timestamp)}`);
            this.autoSave.last = Date.now();
            if (resp.data.warning && !this.shownWarningsCache.has(resp.data.warning)) {
                window.$events.emit('warning', resp.data.warning);
                this.shownWarningsCache.add(resp.data.warning);
            }

            didSave = true;
            this.autoSave.pendingChange = false;
        } catch (err) {
            // Save the editor content in LocalStorage as a last resort, just in case.
            try {
                const saveKey = `draft-save-fail-${(new Date()).toISOString()}`;
                window.localStorage.setItem(saveKey, JSON.stringify(data));
            } catch (lsErr) {
                console.error(lsErr);
            }

            window.$events.emit('error', this.autosaveFailText);
        }

        return didSave;
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
            console.error(e);
            return;
        }

        if (this.autoSave.interval) {
            window.clearInterval(this.autoSave.interval);
        }

        this.draftDisplay.innerText = this.editingPageText;
        this.toggleDiscardDraftVisibility(false);
        window.$events.emit('editor::replace', {
            html: response.data.html,
            markdown: response.data.markdown,
        });

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
            summary = `${summary.slice(0, 16)}...`;
        }
        this.changelogDisplay.innerText = summary;
    }

    toggleDiscardDraftVisibility(show) {
        this.discardDraftWrap.classList.toggle('hidden', !show);
    }

    async changeEditor(event) {
        event.preventDefault();

        const link = event.target.closest('a').href;
        /** @var {ConfirmDialog} * */
        const dialog = window.$components.firstOnElement(this.switchDialogContainer, 'confirm-dialog');
        const [saved, confirmed] = await Promise.all([this.saveDraft(), dialog.show()]);

        if (saved && confirmed) {
            window.location = link;
        }
    }

    /**
     * @return MarkdownEditor|WysiwygEditor
     */
    getEditorComponent() {
        return window.$components.first('markdown-editor') || window.$components.first('wysiwyg-editor');
    }

}
