import {showLoading} from '../services/dom';
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
