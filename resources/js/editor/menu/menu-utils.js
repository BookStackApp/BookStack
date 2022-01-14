import crel from "crelt";

export const prefix = "ProseMirror-menu";

export function renderDropdownItems(items, view) {
    let rendered = [], updates = []
    for (let i = 0; i < items.length; i++) {
        let {dom, update} = items[i].render(view)
        rendered.push(crel("div", {class: prefix + "-dropdown-item"}, dom))
        updates.push(update)
    }
    return {dom: rendered, update: combineUpdates(updates, rendered)}
}

export function renderItems(items, view) {
    let rendered = [], updates = []
    for (let i = 0; i < items.length; i++) {
        let {dom, update} = items[i].render(view)
        rendered.push(dom);
        updates.push(update)
    }
    return {dom: rendered, update: combineUpdates(updates, rendered)}
}

export function combineUpdates(updates, nodes) {
    return state => {
        let something = false
        for (let i = 0; i < updates.length; i++) {
            let up = updates[i](state)
            nodes[i].style.display = up ? "" : "none"
            if (up) something = true
        }
        return something
    }
}

export function randHtmlId() {
    return Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 9);
}