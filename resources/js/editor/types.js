/**
 * @typedef {Object} PmEditorState
 * @property {PmNode} doc
 * @property {PmSelection} selection
 * @property {PmMark[]|null} storedMarks
 * @property {PmSchema} schema
 * @property {PmTransaction} tr
 */

/**
 * @typedef {Object} PmNode
 * @property {PmNodeType} type
 * @property {Object} attrs
 * @property {PmFragment} content
 * @property {PmMark[]} marks
 * @property {String|null} text
 * @property {Number} nodeSize
 * @property {Number} childCount
 */

/**
 * @typedef {Object} PmNodeType
 */

/**
 * @typedef {Object} PmMark
 * @property {PmMarkType} type
 * @property {Object} attrs
 */

/**
 * @typedef {Object} PmMarkType
 * @property {String} name
 * @property {PmSchema} schema
 * @property {PmMarkSpec} spec
 */

/**
 * @typedef {Object} PmMarkSpec
 */

/**
 * @typedef {Object} PmSchema
 * @property {PmSchema} schema
 * @property {Object<PmNodeType>} nodes
 * @property {Object<PmMarkType>} marks
 * @property {PmNodeType} topNodeType
 * @property {Object} cached
 */

/**
 * @typedef {Object} PmSelection
 * @property {PmSelectionRange[]} ranges
 * @property {PmResolvedPos} $anchor
 * @property {PmResolvedPos} $head
 * @property {Number} anchor
 * @property {Number} head
 * @property {Number} from
 * @property {Number} to
 * @property {PmResolvedPos} $from
 * @property {PmResolvedPos} $to
 * @property {Boolean} empty
 */

/**
 * @typedef {Object} PmResolvedPos
 * @property {Number} pos
 * @property {Number} depth
 * @property {Number} parentOffset
 * @property {PmNode} parent
 * @property {PmNode} doc
 */

/**
 * @typedef {Object} PmSelectionRange
 */

/**
 * @typedef {Object} PmTransaction
 * @property {Number} time
 * @property {PmMark[]|null} storedMarks
 * @property {PmSelection} selection
 */

/**
 * @typedef {Object} PmFragment
 */

/**
 * @typedef {Function} PmCommandHandler
 * @param {PmEditorState} state
 * @param {PmDispatchFunction} dispatch
 */

/**
 * @typedef {Function} PmDispatchFunction
 * @param {PmTransaction} tr
 */

/**
 * @typedef {Object} PmView
 * @param {PmEditorState} state
 * @param {Element} dom
 * @param {Boolean} editable
 * @param {Boolean} composing
 */