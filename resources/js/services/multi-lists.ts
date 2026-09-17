/**
 * Service for helping manage common dual-list scenarios.
 * (Shelf book manager, sort set manager, layout-editor).
 */

type ListActionsSet = Record<string, ((item: HTMLElement) => void)>;

export function buildListActions(
    ...lists: HTMLElement[]
): ListActionsSet {
    return {
        move_up(item) {
            const list = item.parentNode as HTMLElement;
            const index = Array.from(list.children).indexOf(item);
            const newIndex = Math.max(index - 1, 0);
            list.insertBefore(item, list.children[newIndex] || null);
        },
        move_down(item) {
            const list = item.parentNode as HTMLElement;
            const index = Array.from(list.children).indexOf(item);
            const newIndex = Math.min(index + 2, list.children.length);
            list.insertBefore(item, list.children[newIndex] || null);
        },
        move_right(item) {
            const list = item.parentNode as HTMLElement;
            const listIndex = lists.indexOf(list);
            const targetListIndex = Math.min(listIndex + 1, lists.length - 1);
            lists[targetListIndex].appendChild(item);
        },
        move_left(item) {
            const list = item.parentNode as HTMLElement;
            const listIndex = lists.indexOf(list);
            const targetListIndex = Math.max(listIndex - 1, 0);
            lists[targetListIndex].appendChild(item);
        },
        remove(item) {
            const otherList = lists.find(list => list !== item.parentNode);
            if (otherList) {
                otherList.appendChild(item);
            }
        },
        add(item) {
            const otherList = lists.find(list => list !== item.parentNode);
            if (otherList) {
                otherList.appendChild(item);
            }
        },
    };
}

export function sortActionClickListener(actions: ListActionsSet, onChange: () => void) {
    return (event: MouseEvent) => {
        const sortItemAction = (event.target as Element).closest('.scroll-box-item button[data-action]') as HTMLElement|null;
        if (sortItemAction) {
            const sortItem = sortItemAction.closest('.scroll-box-item') as HTMLElement;
            const action = sortItemAction.dataset.action;
            if (!action) {
                throw new Error('No action defined for clicked button');
            }

            const actionFunction = actions[action];
            actionFunction(sortItem);

            onChange();
        }
    };
}

