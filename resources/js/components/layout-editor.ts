import Sortable from "sortablejs";
import {Component} from "./component";
import {buildListActions, sortActionClickListener} from "../services/multi-lists";


export class LayoutEditor extends Component {
    protected input!: HTMLInputElement;
    protected columns!: HTMLElement[];

    setup(): void {
        this.input = this.$refs.input as HTMLInputElement;
        this.columns = this.$manyRefs.column || [];

        this.initSortable();

        const unusedColumn = this.columns.find(column => column.dataset.column === 'unused') as HTMLElement;
        const listActions = buildListActions(...this.columns);
        listActions.add = this.addBlockToLeastPopulated.bind(this);
        listActions.remove = function (item: HTMLElement) {
            unusedColumn.appendChild(item);
        };
        const sortActionListener = sortActionClickListener(listActions, this.onChange.bind(this));
        this.$el.addEventListener('click', sortActionListener);
    }

    protected initSortable(): void {
        const sortAction = this.onChange.bind(this);

        for (const column of this.columns) {
            new Sortable(column, {
                group: 'layout-editor-blocks',
                ghostClass: 'primary-background-light',
                handle: '.handle',
                animation: 150,
                onSort: sortAction,
            });
        }
    }

    protected onChange(): void {
        const configured: Record<string, string[]> = {};
        for (const column of this.columns) {
            const location = column.dataset.column || '';
            if (!location) continue;
            configured[location] = [];

            const blockNodes = column.children;
            for (let i = 0; i < blockNodes.length; i++) {
                const blockNode = blockNodes[i] as HTMLElement;
                const blockId = blockNode.dataset.blockId || '';
                if (!blockId) continue;
                configured[location].push(blockId);
            }
        }

        this.input.value = JSON.stringify(configured, null, 2);
    }

    protected addBlockToLeastPopulated(item: HTMLElement): void {
        let populationCount = 100;
        let leastPopulated: null|HTMLElement = null
        for (const column of this.columns) {
            const blockCount = column.children.length;
            if (column.dataset.column === 'unused') {
                continue;
            }

            if (blockCount < populationCount) {
                leastPopulated = column;
                populationCount = blockCount;
            }
        }

        if (leastPopulated) {
            leastPopulated.appendChild(item);
        }
    }
}