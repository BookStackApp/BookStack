import {showLoading} from '../services/dom.ts';
import {Component} from './component';

export class Attachments extends Component {

    setup() {
        this.container = this.$el;
        // Support both old pageId and new entityId/entityType
        this.entityId = this.$opts.entityId || this.$opts.pageId;
        this.entityType = this.$opts.entityType || 'page';
        this.editContainer = this.$refs.editContainer;
        this.listContainer = this.$refs.listContainer;
        this.linksContainer = this.$refs.linksContainer;
        this.listPanel = this.$refs.listPanel;
        this.attachLinkButton = this.$refs.attachLinkButton;

        // If listPanel exists, use it; otherwise fall back to listContainer
        if (!this.listPanel && this.listContainer) {
            this.listPanel = this.listContainer;
        }

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
            const insertContent = event.target.closest('[data-drag-content]').getAttribute('data-drag-content');
            const contentTypes = JSON.parse(insertContent);
            window.$events.emit('editor::insert', {
                html: contentTypes['text/html'],
                markdown: contentTypes['text/plain'],
            });
        });

        this.attachLinkButton.addEventListener('click', () => {
            this.showSection('links');
        });
    }

    showSection(section) {
        const sectionMap = {
            links: this.linksContainer,
            edit: this.editContainer,
            list: this.listPanel || this.listContainer,
        };

        for (const [name, elem] of Object.entries(sectionMap)) {
            if (elem) {
                elem.toggleAttribute('hidden', name !== section);
            }
        }
    }

    reloadList() {
        this.stopEdit();
        window.$http.get(`/attachments/get/${this.entityType}/${this.entityId}`).then(resp => {
            this.listPanel.innerHTML = resp.data;
            window.$components.init(this.listPanel);
        });
    }

    updateOrder(idOrder) {
        window.$http.put(`/attachments/sort/${this.entityType}/${this.entityId}`, {order: idOrder}).then(resp => {
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
        if (this.listPanel || this.listContainer) {
            this.showSection('list');
        }
        // Hide edit and link containers
        if (this.editContainer) {
            this.editContainer.toggleAttribute('hidden', true);
        }
        if (this.linksContainer) {
            this.linksContainer.toggleAttribute('hidden', true);
        }
    }

}
