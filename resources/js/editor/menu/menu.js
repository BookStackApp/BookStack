/**
 * This file originates from https://github.com/ProseMirror/prosemirror-menu
 * and is hence subject to the MIT license found here:
 * https://github.com/ProseMirror/prosemirror-menu/blob/master/LICENSE
 * @copyright Marijn Haverbeke and others
 */

import crel from "crelt"
import {lift, joinUp, selectParentNode, wrapIn, setBlockType, toggleMark} from "prosemirror-commands"
import {undo, redo} from "prosemirror-history"
import {setBlockAttr, insertBlockBefore} from "../commands";

import {getIcon, icons} from "./icons"

const prefix = "ProseMirror-menu"

// ::- An icon or label that, when clicked, executes a command.
export class MenuItem {
  // :: (MenuItemSpec)
  constructor(spec) {
    // :: MenuItemSpec
    // The spec used to create the menu item.
    this.spec = spec
  }

  // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
  // Renders the icon according to its [display
  // spec](#menu.MenuItemSpec.display), and adds an event handler which
  // executes the command when the representation is clicked.
  render(view) {
    let spec = this.spec
    let dom = spec.render ? spec.render(view)
        : spec.icon ? getIcon(spec.icon)
        : spec.label ? crel("div", null, translate(view, spec.label))
        : null
    if (!dom) throw new RangeError("MenuItem without icon or label property")
    if (spec.title) {
      const title = (typeof spec.title === "function" ? spec.title(view.state) : spec.title)
      dom.setAttribute("title", translate(view, title))
    }
    if (spec.class) dom.classList.add(spec.class)
    if (spec.css) dom.style.cssText += spec.css

    dom.addEventListener("mousedown", e => {
      e.preventDefault()
      if (!dom.classList.contains(prefix + "-disabled"))
        spec.run(view.state, view.dispatch, view, e)
    })

    function update(state) {
      if (spec.select) {
        let selected = spec.select(state)
        dom.style.display = selected ? "" : "none"
        if (!selected) return false
      }
      let enabled = true
      if (spec.enable) {
        enabled = spec.enable(state) || false
        setClass(dom, prefix + "-disabled", !enabled)
      }
      if (spec.active) {
        let active = enabled && spec.active(state) || false
        setClass(dom, prefix + "-active", active)
      }
      return true
    }

    return {dom, update}
  }
}

function translate(view, text) {
  return view._props.translate ? view._props.translate(text) : text
}

// MenuItemSpec:: interface
// The configuration object passed to the `MenuItem` constructor.
//
//   run:: (EditorState, (Transaction), EditorView, dom.Event)
//   The function to execute when the menu item is activated.
//
//   select:: ?(EditorState) → bool
//   Optional function that is used to determine whether the item is
//   appropriate at the moment. Deselected items will be hidden.
//
//   enable:: ?(EditorState) → bool
//   Function that is used to determine if the item is enabled. If
//   given and returning false, the item will be given a disabled
//   styling.
//
//   active:: ?(EditorState) → bool
//   A predicate function to determine whether the item is 'active' (for
//   example, the item for toggling the strong mark might be active then
//   the cursor is in strong text).
//
//   render:: ?(EditorView) → dom.Node
//   A function that renders the item. You must provide either this,
//   [`icon`](#menu.MenuItemSpec.icon), or [`label`](#MenuItemSpec.label).
//
//   icon:: ?Object
//   Describes an icon to show for this item. The object may specify
//   an SVG icon, in which case its `path` property should be an [SVG
//   path
//   spec](https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute/d),
//   and `width` and `height` should provide the viewbox in which that
//   path exists. Alternatively, it may have a `text` property
//   specifying a string of text that makes up the icon, with an
//   optional `css` property giving additional CSS styling for the
//   text. _Or_ it may contain `dom` property containing a DOM node.
//
//   label:: ?string
//   Makes the item show up as a text label. Mostly useful for items
//   wrapped in a [drop-down](#menu.Dropdown) or similar menu. The object
//   should have a `label` property providing the text to display.
//
//   title:: ?union<string, (EditorState) → string>
//   Defines DOM title (mouseover) text for the item.
//
//   class:: ?string
//   Optionally adds a CSS class to the item's DOM representation.
//
//   css:: ?string
//   Optionally adds a string of inline CSS to the item's DOM
//   representation.

let lastMenuEvent = {time: 0, node: null}
function markMenuEvent(e) {
  lastMenuEvent.time = Date.now()
  lastMenuEvent.node = e.target
}
function isMenuEvent(wrapper) {
  return Date.now() - 100 < lastMenuEvent.time &&
    lastMenuEvent.node && wrapper.contains(lastMenuEvent.node)
}

// ::- A drop-down menu, displayed as a label with a downwards-pointing
// triangle to the right of it.
export class Dropdown {
  // :: ([MenuElement], ?Object)
  // Create a dropdown wrapping the elements. Options may include
  // the following properties:
  //
  // **`label`**`: string`
  //   : The label to show on the drop-down control.
  //
  // **`title`**`: string`
  //   : Sets the
  //     [`title`](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/title)
  //     attribute given to the menu control.
  //
  // **`class`**`: string`
  //   : When given, adds an extra CSS class to the menu control.
  //
  // **`css`**`: string`
  //   : When given, adds an extra set of CSS styles to the menu control.
  constructor(content, options) {
    this.options = options || {}
    this.content = Array.isArray(content) ? content : [content]
  }

  // :: (EditorView) → {dom: dom.Node, update: (EditorState)}
  // Render the dropdown menu and sub-items.
  render(view) {
    let content = renderDropdownItems(this.content, view)

    let label = crel("div", {class: prefix + "-dropdown " + (this.options.class || ""),
                             style: this.options.css},
                     translate(view, this.options.label))
    if (this.options.title) label.setAttribute("title", translate(view, this.options.title))
    let wrap = crel("div", {class: prefix + "-dropdown-wrap"}, label)
    let open = null, listeningOnClose = null
    let close = () => {
      if (open && open.close()) {
        open = null
        window.removeEventListener("mousedown", listeningOnClose)
      }
    }
    label.addEventListener("mousedown", e => {
      e.preventDefault()
      markMenuEvent(e)
      if (open) {
        close()
      } else {
        open = this.expand(wrap, content.dom)
        window.addEventListener("mousedown", listeningOnClose = () => {
          if (!isMenuEvent(wrap)) close()
        })
      }
    })

    function update(state) {
      let inner = content.update(state)
      wrap.style.display = inner ? "" : "none"
      return inner
    }

    return {dom: wrap, update}
  }

  expand(dom, items) {
    let menuDOM = crel("div", {class: prefix + "-dropdown-menu " + (this.options.class || "")}, items)

    let done = false
    function close() {
      if (done) return
      done = true
      dom.removeChild(menuDOM)
      return true
    }
    dom.appendChild(menuDOM)
    return {close, node: menuDOM}
  }
}

function renderDropdownItems(items, view) {
  let rendered = [], updates = []
  for (let i = 0; i < items.length; i++) {
    let {dom, update} = items[i].render(view)
    rendered.push(crel("div", {class: prefix + "-dropdown-item"}, dom))
    updates.push(update)
  }
  return {dom: rendered, update: combineUpdates(updates, rendered)}
}

function combineUpdates(updates, nodes) {
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

// ::- Represents a submenu wrapping a group of elements that start
// hidden and expand to the right when hovered over or tapped.
export class DropdownSubmenu {
  // :: ([MenuElement], ?Object)
  // Creates a submenu for the given group of menu elements. The
  // following options are recognized:
  //
  // **`label`**`: string`
  //   : The label to show on the submenu.
  constructor(content, options) {
    this.options = options || {}
    this.content = Array.isArray(content) ? content : [content]
  }

  // :: (EditorView) → {dom: dom.Node, update: (EditorState) → bool}
  // Renders the submenu.
  render(view) {
    const items = renderDropdownItems(this.content, view)

    const handleContent = this.options.icon ? getIcon(this.options.icon) : crel("div", {class: prefix + "-submenu-label"}, translate(view, this.options.label));
    const wrap = crel("div", {class: prefix + "-submenu-wrap"}, handleContent,
                   crel("div", {class: prefix + "-submenu"}, items.dom))
    let listeningOnClose = null
    handleContent.addEventListener("mousedown", e => {
      e.preventDefault()
      markMenuEvent(e)
      setClass(wrap, prefix + "-submenu-wrap-active")
      if (!listeningOnClose)
        window.addEventListener("mousedown", listeningOnClose = () => {
          if (!isMenuEvent(wrap)) {
            wrap.classList.remove(prefix + "-submenu-wrap-active")
            window.removeEventListener("mousedown", listeningOnClose)
            listeningOnClose = null
          }
        })
    })

    function update(state) {
      let inner = items.update(state)
      wrap.style.display = inner ? "" : "none"
      return inner
    }
    return {dom: wrap, update}
  }
}

// :: (EditorView, [[MenuElement]]) → {dom: dom.DocumentFragment, update: (EditorState) → bool}
// Render the given, possibly nested, array of menu elements into a
// document fragment, placing separators between them (and ensuring no
// superfluous separators appear when some of the groups turn out to
// be empty).
export function renderGrouped(view, content) {
  let result = document.createDocumentFragment()
  let updates = [], separators = []
  for (let i = 0; i < content.length; i++) {
    let items = content[i], localUpdates = [], localNodes = []
    for (let j = 0; j < items.length; j++) {
      let {dom, update} = items[j].render(view)
      let span = crel("span", {class: prefix + "item"}, dom)
      result.appendChild(span)
      localNodes.push(span)
      localUpdates.push(update)
    }
    if (localUpdates.length) {
      updates.push(combineUpdates(localUpdates, localNodes))
      if (i < content.length - 1)
        separators.push(result.appendChild(separator()))
    }
  }

  function update(state) {
    let something = false, needSep = false
    for (let i = 0; i < updates.length; i++) {
      let hasContent = updates[i](state)
      if (i) separators[i - 1].style.display = needSep && hasContent ? "" : "none"
      needSep = hasContent
      if (hasContent) something = true
    }
    return something
  }
  return {dom: result, update}
}

function separator() {
  return crel("span", {class: prefix + "separator"})
}


// :: MenuItem
// Menu item for the `joinUp` command.
export const joinUpItem = new MenuItem({
  title: "Join with above block",
  run: joinUp,
  select: state => joinUp(state),
  icon: icons.join
})

// :: MenuItem
// Menu item for the `lift` command.
export const liftItem = new MenuItem({
  title: "Lift out of enclosing block",
  run: lift,
  select: state => lift(state),
  icon: icons.lift
})

// :: MenuItem
// Menu item for the `selectParentNode` command.
export const selectParentNodeItem = new MenuItem({
  title: "Select parent node",
  run: selectParentNode,
  select: state => selectParentNode(state),
  icon: icons.selectParentNode
})

// :: MenuItem
// Menu item for the `undo` command.
export let undoItem = new MenuItem({
  title: "Undo last change",
  run: undo,
  enable: state => undo(state),
  icon: icons.undo
})

// :: MenuItem
// Menu item for the `redo` command.
export let redoItem = new MenuItem({
  title: "Redo last undone change",
  run: redo,
  enable: state => redo(state),
  icon: icons.redo
})

// :: (NodeType, Object) → MenuItem
// Build a menu item for wrapping the selection in a given node type.
// Adds `run` and `select` properties to the ones present in
// `options`. `options.attrs` may be an object or a function.
export function wrapItem(nodeType, options) {
  let passedOptions = {
    run(state, dispatch) {
      // FIXME if (options.attrs instanceof Function) options.attrs(state, attrs => wrapIn(nodeType, attrs)(state))
      return wrapIn(nodeType, options.attrs)(state, dispatch)
    },
    select(state) {
      return wrapIn(nodeType, options.attrs instanceof Function ? null : options.attrs)(state)
    }
  }
  for (let prop in options) passedOptions[prop] = options[prop]
  return new MenuItem(passedOptions)
}

// :: (NodeType, Object) → MenuItem
// Build a menu item for changing the type of the textblock around the
// selection to the given type. Provides `run`, `active`, and `select`
// properties. Others must be given in `options`. `options.attrs` may
// be an object to provide the attributes for the textblock node.
export function blockTypeItem(nodeType, options) {
  let command = setBlockType(nodeType, options.attrs)
  let passedOptions = {
    run: command,
    enable(state) { return command(state) },
    active(state) {
      let {$from, to, node} = state.selection
      if (node) return node.hasMarkup(nodeType, options.attrs)
      return to <= $from.end() && $from.parent.hasMarkup(nodeType, options.attrs)
    }
  }
  for (let prop in options) passedOptions[prop] = options[prop]
  return new MenuItem(passedOptions)
}

export function setAttrItem(attrName, attrValue, options) {
  const command = setBlockAttr(attrName, attrValue);
  const passedOptions = {
    run: command,
    enable(state) { return command(state) },
    active(state) {
      const {$from, to, node} = state.selection
      if (node) return node.attrs[attrValue] === attrValue;
      return to <= $from.end() && $from.parent.attrs[attrValue] === attrValue;
    }
  }
  for (const prop in options) passedOptions[prop] = options[prop]
  return new MenuItem(passedOptions)
}

export function insertBlockBeforeItem(blockType, options) {
  const command = insertBlockBefore(blockType);
  const passedOptions = {
    run: command,
    enable(state) { return command(state) },
    active(state) {
      return false;
    }
  }
  for (const prop in options) passedOptions[prop] = options[prop]
  return new MenuItem(passedOptions);
}

// Work around classList.toggle being broken in IE11
function setClass(dom, cls, on) {
  if (on) dom.classList.add(cls)
  else dom.classList.remove(cls)
}
