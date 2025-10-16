import {showLoading} from '../services/dom.ts';
import {Component} from './component';

export class Attachments extends Component {

    setup() {
        this.container = this.$el;
        this.pageId = this.$opts.pageId;
        this.editContainer = this.$refs.editContainer;
        this.listContainer = this.$refs.listContainer;
        this.linksContainer = this.$refs.linksContainer;
        this.listPanel = this.$refs.listPanel;
        this.attachLinkButton = this.$refs.attachLinkButton;

        this.setupListeners();
    }

    setupListeners() {
        const reloadListBound = this.reloadList.bind(this);
        this.container.addEventListener('dropzone-upload-success', reloadListBound);
        this.container.addEventListener('ajax-form-success', reloadListBound);

        this.container.addEventListener('sortable-list-sort', event => {
            this.updateOrder(event.detail.ids);
        });

        this.container.addEventListener('event-emit-select-edit', event => {
            this.startEdit(event.detail.id);
        });

        this.container.addEventListener('event-emit-select-edit-back', () => {
            this.stopEdit();
        });

        this.container.addEventListener('event-emit-select-insert', event => {
            this.insertContentFromCard(event, 'data-drag-content');
        });

        this.container.addEventListener('event-emit-select-insert-preview', event => {
            this.insertContentFromCard(event, 'data-preview-content');
        });

        this.attachLinkButton.addEventListener('click', () => {
            this.showSection('links');
        });
    }

    showSection(section) {
        const sectionMap = {
            links: this.linksContainer,
            edit: this.editContainer,
            list: this.listContainer,
        };

        for (const [name, elem] of Object.entries(sectionMap)) {
            elem.toggleAttribute('hidden', name !== section);
        }
    }

    reloadList() {
        this.stopEdit();
        window.$http.get(`/attachments/get/page/${this.pageId}`).then(resp => {
            this.listPanel.innerHTML = resp.data;
            window.$components.init(this.listPanel);
        });
    }

    insertContentFromCard(event, attributeName) {
        const card = event.target.closest(`[${attributeName}]`);
        if (!card) {
            return;
        }

        const insertContent = card.getAttribute(attributeName);
        if (!insertContent) {
            return;
        }

        const contentTypes = JSON.parse(insertContent);
        const html = contentTypes['text/html'] ?? '';
        const markdown = contentTypes['text/plain'] ?? html;

        if (!html && !markdown) {
            return;
        }

        window.$events.emit('editor::insert', {html, markdown});
    }

    updateOrder(idOrder) {
        window.$http.put(`/attachments/sort/page/${this.pageId}`, {order: idOrder}).then(resp => {
            window.$events.emit('success', resp.data.message);
        });
    }

    async startEdit(id) {
        this.showSection('edit');

        showLoading(this.editContainer);
        const resp = await window.$http.get(`/attachments/edit/${id}`);
        this.editContainer.innerHTML = resp.data;
        window.$components.init(this.editContainer);
    }

    stopEdit() {
        this.showSection('list');
    }

}
