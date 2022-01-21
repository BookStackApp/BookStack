/**
 * This file originates from https://github.com/ProseMirror/prosemirror-tables
 * and is hence subject to the MIT license found here:
 * https://github.com/ProseMirror/prosemirror-menu/blob/master/LICENSE
 * @copyright Marijn Haverbeke and others
 */

import {Plugin, PluginKey} from "prosemirror-state"
import {Decoration, DecorationSet} from "prosemirror-view"
import {
    cellAround,
    pointsAtCell,
    setAttr,
    TableMap,
} from "prosemirror-tables";

export const key = new PluginKey("tableColumnResizing")

export function columnResizing(options = {}) {
    const {
        handleWidth, cellMinWidth, lastColumnResizable
    } = Object.assign({
        handleWidth: 5,
        cellMinWidth: 25,
        lastColumnResizable: true
    }, options);

    let plugin = new Plugin({
        key,
        state: {
            init(_, state) {
                return new ResizeState(-1, false)
            },
            apply(tr, prev) {
                return prev.apply(tr)
            }
        },
        props: {
            attributes(state) {
                let pluginState = key.getState(state)
                return pluginState.activeHandle > -1 ? {class: "resize-cursor"} : null
            },

            handleDOMEvents: {
                mousemove(view, event) {
                    handleMouseMove(view, event, handleWidth, cellMinWidth, lastColumnResizable)
                },
                mouseleave(view) {
                    handleMouseLeave(view)
                },
                mousedown(view, event) {
                    handleMouseDown(view, event, cellMinWidth)
                }
            },

            decorations(state) {
                let pluginState = key.getState(state)
                if (pluginState.activeHandle > -1) return handleDecorations(state, pluginState.activeHandle)
            },

            nodeViews: {}
        }
    })
    return plugin
}

class ResizeState {
    constructor(activeHandle, dragging) {
        this.activeHandle = activeHandle
        this.dragging = dragging
    }

    apply(tr) {
        let state = this, action = tr.getMeta(key)
        if (action && action.setHandle != null)
            return new ResizeState(action.setHandle, null)
        if (action && action.setDragging !== undefined)
            return new ResizeState(state.activeHandle, action.setDragging)
        if (state.activeHandle > -1 && tr.docChanged) {
            let handle = tr.mapping.map(state.activeHandle, -1)
            if (!pointsAtCell(tr.doc.resolve(handle))) handle = null
            state = new ResizeState(handle, state.dragging)
        }
        return state
    }
}

function handleMouseMove(view, event, handleWidth, cellMinWidth, lastColumnResizable) {
    let pluginState = key.getState(view.state)

    if (!pluginState.dragging) {
        let target = domCellAround(event.target), cell = -1
        if (target) {
            let {left, right} = target.getBoundingClientRect()
            if (event.clientX - left <= handleWidth)
                cell = edgeCell(view, event, "left")
            else if (right - event.clientX <= handleWidth)
                cell = edgeCell(view, event, "right")
        }

        if (cell != pluginState.activeHandle) {
            if (!lastColumnResizable && cell !== -1) {
                let $cell = view.state.doc.resolve(cell)
                let table = $cell.node(-1), map = TableMap.get(table), start = $cell.start(-1)
                let col = map.colCount($cell.pos - start) + $cell.nodeAfter.attrs.colspan - 1

                if (col == map.width - 1) {
                    return
                }
            }

            updateHandle(view, cell)
        }
    }
}

function handleMouseLeave(view) {
    let pluginState = key.getState(view.state)
    if (pluginState.activeHandle > -1 && !pluginState.dragging) updateHandle(view, -1)
}

function handleMouseDown(view, event, cellMinWidth) {
    let pluginState = key.getState(view.state)
    if (pluginState.activeHandle == -1 || pluginState.dragging) return false

    let cell = view.state.doc.nodeAt(pluginState.activeHandle)
    let width = currentColWidth(view, pluginState.activeHandle, cell.attrs)
    view.dispatch(view.state.tr.setMeta(key, {setDragging: {startX: event.clientX, startWidth: width}}))

    function finish(event) {
        window.removeEventListener("mouseup", finish)
        window.removeEventListener("mousemove", move)
        let pluginState = key.getState(view.state)
        if (pluginState.dragging) {
            updateColumnWidth(view, pluginState.activeHandle, draggedWidth(pluginState.dragging, event, cellMinWidth))
            view.dispatch(view.state.tr.setMeta(key, {setDragging: null}))
        }
    }

    function move(event) {
        if (!event.which) return finish(event)
        let pluginState = key.getState(view.state)
        let dragged = draggedWidth(pluginState.dragging, event, cellMinWidth)
        displayColumnWidth(view, pluginState.activeHandle, dragged, cellMinWidth)
    }

    window.addEventListener("mouseup", finish)
    window.addEventListener("mousemove", move)
    event.preventDefault()
    return true
}

function currentColWidth(view, cellPos, {colspan, colwidth}) {
    let width = colwidth && colwidth[colwidth.length - 1]
    if (width) return width
    let dom = view.domAtPos(cellPos)
    let node = dom.node.childNodes[dom.offset]
    let domWidth = node.offsetWidth, parts = colspan
    if (colwidth) for (let i = 0; i < colspan; i++) if (colwidth[i]) {
        domWidth -= colwidth[i]
        parts--
    }
    return domWidth / parts
}

function domCellAround(target) {
    while (target && target.nodeName != "TD" && target.nodeName != "TH")
        target = target.classList.contains("ProseMirror") ? null : target.parentNode
    return target
}

function edgeCell(view, event, side) {
    let found = view.posAtCoords({left: event.clientX, top: event.clientY})
    if (!found) return -1
    let {pos} = found
    let $cell = cellAround(view.state.doc.resolve(pos))
    if (!$cell) return -1
    if (side == "right") return $cell.pos
    let map = TableMap.get($cell.node(-1)), start = $cell.start(-1)
    let index = map.map.indexOf($cell.pos - start)
    return index % map.width == 0 ? -1 : start + map.map[index - 1]
}

function draggedWidth(dragging, event, cellMinWidth) {
    let offset = event.clientX - dragging.startX
    return Math.max(cellMinWidth, dragging.startWidth + offset)
}

function updateHandle(view, value) {
    view.dispatch(view.state.tr.setMeta(key, {setHandle: value}))
}

function updateColumnWidth(view, cell, width) {
    let $cell = view.state.doc.resolve(cell);
    let table = $cell.node(-1);
    let map = TableMap.get(table);
    let start = $cell.start(-1);
    let col = map.colCount($cell.pos - start) + $cell.nodeAfter.attrs.colspan - 1;
    let tr = view.state.tr;

    for (let row = 0; row < map.height; row++) {
        let mapIndex = row * map.width + col;
        // Rowspanning cell that has already been handled
        if (row && map.map[mapIndex] == map.map[mapIndex - map.width]) continue
        let pos = map.map[mapIndex]
        let {attrs} = table.nodeAt(pos);
        const newWidth = (attrs.colspan * width) + 'px';

        tr.setNodeMarkup(start + pos, null, setAttr(attrs, "width",  newWidth));
    }

    if (tr.docChanged) view.dispatch(tr)
}

function displayColumnWidth(view, cell, width, cellMinWidth) {
    const $cell = view.state.doc.resolve(cell)
    const table = $cell.node(-1);
    const start = $cell.start(-1);
    const col = TableMap.get(table).colCount($cell.pos - start) + $cell.nodeAfter.attrs.colspan - 1
    let dom = view.domAtPos($cell.start(-1)).node
    while (dom.nodeName !== "TABLE") {
        dom = dom.parentNode
    }
    updateColumnsOnResize(view, table, dom, cellMinWidth, col, width)
}


function updateColumnsOnResize(view, tableNode, tableDom, cellMinWidth, overrideCol, overrideValue) {
    console.log({tableNode, tableDom, cellMinWidth, overrideCol, overrideValue});
    let totalWidth = 0;
    let fixedWidth = true;
    const rows = tableDom.querySelectorAll('tr');

    for (let y = 0; y < rows.length; y++) {
        const row = rows[y];
        const cell = row.children[overrideCol];
        cell.style.width = `${overrideValue}px`;
        if (y === 0) {
            for (let x = 0; x < row.children.length; x++) {
                const cell = row.children[x];
                if (cell.style.width) {
                    const width = Number(cell.style.width.replace('px', ''));
                    totalWidth += width || cellMinWidth;
                } else {
                    fixedWidth = false;
                    totalWidth += cellMinWidth;
                }
            }
        }
    }

    console.log(totalWidth);
    if (fixedWidth) {
        tableDom.style.width = totalWidth + "px"
        tableDom.style.minWidth = ""
    } else {
        tableDom.style.width = ""
        tableDom.style.minWidth = totalWidth + "px"
    }
}

function zeroes(n) {
    let result = []
    for (let i = 0; i < n; i++) result.push(0)
    return result
}

function handleDecorations(state, cell) {
    let decorations = []
    let $cell = state.doc.resolve(cell)
    let table = $cell.node(-1), map = TableMap.get(table), start = $cell.start(-1)
    let col = map.colCount($cell.pos - start) + $cell.nodeAfter.attrs.colspan
    for (let row = 0; row < map.height; row++) {
        let index = col + row * map.width - 1
        // For positions that are have either a different cell or the end
        // of the table to their right, and either the top of the table or
        // a different cell above them, add a decoration
        if ((col == map.width || map.map[index] != map.map[index + 1]) &&
            (row == 0 || map.map[index - 1] != map.map[index - 1 - map.width])) {
            let cellPos = map.map[index]
            let pos = start + cellPos + table.nodeAt(cellPos).nodeSize - 1
            let dom = document.createElement("div")
            dom.className = "column-resize-handle"
            decorations.push(Decoration.widget(pos, dom))
        }
    }
    return DecorationSet.create(state.doc, decorations)
}
