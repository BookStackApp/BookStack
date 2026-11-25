import {onSelect} from '../services/dom';
import {debounce} from '../services/util';
import {Component} from './component';
import {utcTimeStampToLocalTime} from '../services/dates';
import {WysiwygEditorTinymce} from "./wysiwyg-editor-tinymce";
import {WysiwygEditor} from "./wysiwyg-editor";
import {MarkdownEditor} from "./markdown-editor";
import {ConfirmDialog} from "./confirm-dialog";
import {HttpError} from "../services/http";

export class PageEditor extends Component {
    protected draftsEnabled!: boolean;
    protected editorType!: string;
    protected pageId!: number;
    protected isNewDraft!: boolean;
    protected hasDefaultTitle!: boolean;

    protected container!: HTMLElement;
    protected titleElem!: HTMLInputElement;
    protected saveDraftButton!: HTMLElement;
    protected discardDraftButton!: HTMLElement;
    protected discardDraftWrap!: HTMLElement;
    protected deleteDraftButton!: HTMLElement;
    protected deleteDraftWrap!: HTMLElement;
    protected draftDisplay!: HTMLElement;
    protected draftDisplayIcon!: HTMLElement;
    protected changelogInput!: HTMLTextAreaElement;
    protected changelogDisplay!: HTMLElement;
    protected changelogCounter!: HTMLElement;
    protected changeEditorButtons!: HTMLElement[];
    protected switchDialogContainer!: HTMLElement;
    protected deleteDraftDialogContainer!: HTMLElement;
    protected sessionRefreshDialogContainer!: HTMLElement;

    protected draftText!: string;
    protected autosaveFailText!: string;
    protected editingPageText!: string;
    protected draftDiscardedText!: string;
    protected draftDeleteText!: string;
    protected draftDeleteFailText!: string;
    protected setChangelogText!: string;
    protected loginSessionRefreshedText!: string;

    // State data
    protected autoSave: { interval: null|number; frequency: number; last: number; pendingChange: boolean; pendingSessionRefresh: boolean } = {
        interval: null,
        frequency: 30000,
        last: 0,
        pendingChange: false,
        pendingSessionRefresh: false,
    };
    protected shownWarningsCache = new Set();

    setup() {
        // Options
        this.draftsEnabled = this.$opts.draftsEnabled === 'true';
        this.editorType = this.$opts.editorType;
        this.pageId = Number(this.$opts.pageId);
        this.isNewDraft = this.$opts.pageNewDraft === 'true';
        this.hasDefaultTitle = this.$opts.hasDefaultTitle === 'true' || false;

        // Elements
        this.container = this.$el;
        this.titleElem = this.$refs.titleContainer.querySelector('input') as HTMLInputElement;
        this.saveDraftButton = this.$refs.saveDraft;
        this.discardDraftButton = this.$refs.discardDraft;
        this.discardDraftWrap = this.$refs.discardDraftWrap;
        this.deleteDraftButton = this.$refs.deleteDraft;
        this.deleteDraftWrap = this.$refs.deleteDraftWrap;
        this.draftDisplay = this.$refs.draftDisplay;
        this.draftDisplayIcon = this.$refs.draftDisplayIcon;
        this.changelogInput = this.$refs.changelogInput as HTMLTextAreaElement;
        this.changelogDisplay = this.$refs.changelogDisplay;
        this.changelogCounter = this.$refs.changelogCounter;
        this.changeEditorButtons = this.$manyRefs.changeEditor || [];
        this.switchDialogContainer = this.$refs.switchDialog;
        this.deleteDraftDialogContainer = this.$refs.deleteDraftDialog;
        this.sessionRefreshDialogContainer = this.$refs.sessionRefreshDialog;

        // Translations
        this.draftText = this.$opts.draftText;
        this.autosaveFailText = this.$opts.autosaveFailText;
        this.editingPageText = this.$opts.editingPageText;
        this.draftDiscardedText = this.$opts.draftDiscardedText;
        this.draftDeleteText = this.$opts.draftDeleteText;
        this.draftDeleteFailText = this.$opts.draftDeleteFailText;
        this.setChangelogText = this.$opts.setChangelogText;
        this.loginSessionRefreshedText = this.$opts.loginSessionRefreshedText;

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
        window.$events.listen('editor-save-page', this.savePage.bind(this));
        if (this.draftsEnabled) {
            window.$events.listen('editor-save-draft', this.saveDraft.bind(this));
        }

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
        const onChangelogInput = () => {
            const count = this.changelogInput.value.length;
            this.changelogCounter.innerText = `${count} / 180`;
            updateChangelogDebounced();
        };
        this.changelogInput.addEventListener('input', onChangelogInput.bind(this));
        onChangelogInput();

        // Draft Controls
        onSelect(this.saveDraftButton, this.saveDraft.bind(this));
        onSelect(this.discardDraftButton, (e) => this.discardDraft());
        onSelect(this.deleteDraftButton, this.deleteDraft.bind(this));

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
        if (savedRecently || !this.autoSave.pendingChange || this.autoSave.pendingSessionRefresh) {
            return;
        }

        this.saveDraft();
    }

    savePage() {
        const form = this.container.closest('form');
        if (form instanceof HTMLFormElement) {
            form.requestSubmit();
        }
    }

    async saveDraft() {
        const data = {
            name: this.titleElem.value.trim(),
            changelog: this.changelogInput.value || '',
        };

        const editorContent = await this.getEditorComponent().getContent();
        Object.assign(data, editorContent);

        let didSave = false;
        try {
            const resp = await window.$http.put(`/ajax/page/${this.pageId}/save-draft`, data);
            if (!this.isNewDraft) {
                this.discardDraftWrap.toggleAttribute('hidden', false);
                this.deleteDraftWrap.toggleAttribute('hidden', false);
            }

            const respData = resp.data as {message: string, timestamp: number, warning: undefined|string};

            this.draftNotifyChange(`${respData.message} ${utcTimeStampToLocalTime(respData.timestamp)}`);
            this.autoSave.last = Date.now();
            if (respData.warning && !this.shownWarningsCache.has(respData.warning)) {
                window.$events.emit('warning', respData.warning);
                this.shownWarningsCache.add(respData.warning);
            }

            didSave = true;
            this.autoSave.pendingChange = false;
        } catch (err) {
            if (err instanceof HttpError && (err.status === 419 || err.status === 401)) {
                this.startSessionRefresh();
                return false;
            }

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

    draftNotifyChange(text: string): void {
        this.draftDisplay.innerText = text;
        this.draftDisplayIcon.classList.add('visible');
        window.setTimeout(() => {
            this.draftDisplayIcon.classList.remove('visible');
        }, 2000);
    }

    async discardDraft(notify = true): Promise<void> {
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

        const respData = response.data as {html: string, markdown: string, name: string};

        this.draftDisplay.innerText = this.editingPageText;
        this.discardDraftWrap.toggleAttribute('hidden', true);
        window.$events.emit('editor::replace', {
            html: respData.html,
            markdown: respData.markdown,
        });

        this.titleElem.value = respData.name;
        window.setTimeout(() => {
            this.startAutoSave();
        }, 1000);

        if (notify) {
            window.$events.success(this.draftDiscardedText);
        }
    }

    async deleteDraft() {
        const dialog = window.$components.firstOnElement(this.deleteDraftDialogContainer, 'confirm-dialog') as ConfirmDialog;
        const confirmed = await dialog.show();
        if (!confirmed) {
            return;
        }

        try {
            const discard = this.discardDraft(false);
            const draftDelete = window.$http.delete(`/page-user-drafts/${this.pageId}`);
            await Promise.all([discard, draftDelete]);
            window.$events.success(this.draftDeleteText);
            this.deleteDraftWrap.toggleAttribute('hidden', true);
        } catch (err) {
            console.error(err);
            window.$events.error(this.draftDeleteFailText);
        }
    }

    async startSessionRefresh() {
        if (this.autoSave.pendingSessionRefresh) {
            return;
        }

        this.autoSave.pendingSessionRefresh = true;
        const dialog = window.$components.firstOnElement(this.sessionRefreshDialogContainer, 'confirm-dialog') as ConfirmDialog;
        const confirmed = await dialog.show();
        if (!confirmed) {
            this.autoSave.pendingSessionRefresh = false;
            return;
        }

        const loginWindow = window.open(window.baseUrl('/session/refresh'));

        window.addEventListener('message', e => {
            if (e.origin !== window.location.origin || e.source !== loginWindow) {
                return;
            }

            if (e.data.csrf_token) {
                const token = e.data.csrf_token;
                const tokenFields = document.querySelectorAll('input[name="_token"]');
                for (const tokenField of tokenFields) {
                    (tokenField as HTMLInputElement).value = token;
                }
                const metaToken = document.querySelector('meta[name="token"]');
                if (metaToken) {
                    metaToken.setAttribute('content', token);
                }

                window.$events.success(this.loginSessionRefreshedText);
                loginWindow?.close();

                this.autoSave.pendingSessionRefresh = false;
                this.saveDraft();
            }
        });
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

    async changeEditor(event: Event) {
        event.preventDefault();

        const link = (event.target as HTMLElement).closest('a')?.href || '';
        const dialog = window.$components.firstOnElement(this.switchDialogContainer, 'confirm-dialog') as ConfirmDialog;
        const [saved, confirmed] = await Promise.all([this.saveDraft(), dialog.show()]);

        if (saved && confirmed) {
            window.location.assign(link);
        }
    }

    getEditorComponent(): MarkdownEditor|WysiwygEditor|WysiwygEditorTinymce {
        return (window.$components.first('markdown-editor') as MarkdownEditor|null)
            || (window.$components.first('wysiwyg-editor') as WysiwygEditor|null)
            || (window.$components.first('wysiwyg-editor-tinymce') as WysiwygEditorTinymce);
    }

}
