// See the "/licenses" URI for full package license details
var __defProp = Object.defineProperty;
var __typeError = (msg) => {
  throw TypeError(msg);
};
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __export = (target, all) => {
  for (var name in all)
    __defProp(target, name, { get: all[name], enumerable: true });
};
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
var __accessCheck = (obj, member, msg) => member.has(obj) || __typeError("Cannot " + msg);
var __privateAdd = (obj, member, value) => member.has(obj) ? __typeError("Cannot add the same private member more than once") : member instanceof WeakSet ? member.add(obj) : member.set(obj, value);
var __privateMethod = (obj, member, method) => (__accessCheck(obj, member, "access private method"), method);

// resources/js/services/events.ts
var EventManager = class {
  constructor() {
    __publicField(this, "listeners", {});
    __publicField(this, "stack", []);
  }
  /**
   * Emit a custom event for any handlers to pick-up.
   */
  emit(eventName, eventData = {}) {
    this.stack.push({ name: eventName, data: eventData });
    const listenersToRun = this.listeners[eventName] || [];
    for (const listener of listenersToRun) {
      listener(eventData);
    }
  }
  /**
   * Listen to a custom event and run the given callback when that event occurs.
   */
  listen(eventName, callback) {
    if (typeof this.listeners[eventName] === "undefined") this.listeners[eventName] = [];
    this.listeners[eventName].push(callback);
  }
  /**
   * Remove an event listener which is using the given callback for the given event name.
   */
  remove(eventName, callback) {
    const listeners = this.listeners[eventName] || [];
    const index2 = listeners.indexOf(callback);
    if (index2 !== -1) {
      listeners.splice(index2, 1);
    }
  }
  /**
   * Emit an event for public use.
   * Sends the event via the native DOM event handling system.
   */
  emitPublic(targetElement, eventName, eventData) {
    const event = new CustomEvent(eventName, {
      detail: eventData,
      bubbles: true
    });
    targetElement.dispatchEvent(event);
  }
  /**
   * Emit a success event with the provided message.
   */
  success(message) {
    this.emit("success", message);
  }
  /**
   * Emit an error event with the provided message.
   */
  error(message) {
    this.emit("error", message);
  }
  /**
   * Notify of standard server-provided validation errors.
   */
  showValidationErrors(responseErr) {
    if (responseErr.status === 422 && responseErr.data) {
      const message = Object.values(responseErr.data).flat().join("\n");
      this.error(message);
    }
  }
  /**
   * Notify standard server-provided error messages.
   */
  showResponseError(responseErr) {
    if (!responseErr.status) return;
    if (responseErr.status >= 400 && typeof responseErr.data === "object" && responseErr.data.message) {
      this.error(responseErr.data.message);
    }
  }
};

// resources/js/services/http.ts
var HttpError = class extends Error {
  constructor(response, content) {
    super(response.statusText);
    __publicField(this, "data");
    __publicField(this, "headers");
    __publicField(this, "original");
    __publicField(this, "redirected");
    __publicField(this, "status");
    __publicField(this, "statusText");
    __publicField(this, "url");
    this.data = content;
    this.headers = response.headers;
    this.redirected = response.redirected;
    this.status = response.status;
    this.statusText = response.statusText;
    this.url = response.url;
    this.original = response;
  }
};
var HttpManager = class {
  /**
   * Get the content from a fetch response.
   * Checks the content-type header to determine the format.
   */
  async getResponseContent(response) {
    if (response.status === 204) {
      return null;
    }
    const responseContentType = response.headers.get("Content-Type") || "";
    const subType = responseContentType.split(";")[0].split("/").pop();
    if (subType === "javascript" || subType === "json") {
      return response.json();
    }
    return response.text();
  }
  createXMLHttpRequest(method, url, events = {}) {
    const csrfToken = document.querySelector("meta[name=token]")?.getAttribute("content");
    const req = new XMLHttpRequest();
    for (const [eventName, callback] of Object.entries(events)) {
      req.addEventListener(eventName, callback.bind(req));
    }
    req.open(method, url);
    req.withCredentials = true;
    req.setRequestHeader("X-CSRF-TOKEN", csrfToken || "");
    return req;
  }
  /**
   * Create a new HTTP request, setting the required CSRF information
   * to communicate with the back-end. Parses & formats the response.
   */
  async request(url, options2 = {}) {
    let requestUrl = url;
    if (!requestUrl.startsWith("http")) {
      requestUrl = window.baseUrl(requestUrl);
    }
    if (options2.params) {
      const urlObj = new URL(requestUrl);
      for (const paramName of Object.keys(options2.params)) {
        const value = options2.params[paramName];
        if (typeof value !== "undefined" && value !== null) {
          urlObj.searchParams.set(paramName, value);
        }
      }
      requestUrl = urlObj.toString();
    }
    const csrfToken = document.querySelector("meta[name=token]")?.getAttribute("content") || "";
    const requestOptions = { ...options2, credentials: "same-origin" };
    requestOptions.headers = {
      ...requestOptions.headers || {},
      baseURL: window.baseUrl(""),
      "X-CSRF-TOKEN": csrfToken
    };
    const response = await fetch(requestUrl, requestOptions);
    const content = await this.getResponseContent(response) || "";
    const returnData = {
      data: content,
      headers: response.headers,
      redirected: response.redirected,
      status: response.status,
      statusText: response.statusText,
      url: response.url,
      original: response
    };
    if (!response.ok) {
      throw new HttpError(response, content);
    }
    return returnData;
  }
  /**
   * Perform a HTTP request to the back-end that includes data in the body.
   * Parses the body to JSON if an object, setting the correct headers.
   */
  async dataRequest(method, url, data) {
    const options2 = {
      method,
      body: data
    };
    if (typeof data === "object" && !(data instanceof FormData)) {
      options2.headers = {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      };
      options2.body = JSON.stringify(data);
    }
    if (data instanceof FormData && method !== "post") {
      data.append("_method", method);
      options2.method = "post";
    }
    return this.request(url, options2);
  }
  /**
   * Perform a HTTP GET request.
   * Can easily pass query parameters as the second parameter.
   */
  async get(url, params = {}) {
    return this.request(url, {
      method: "GET",
      params
    });
  }
  /**
   * Perform a HTTP POST request.
   */
  async post(url, data = null) {
    return this.dataRequest("POST", url, data);
  }
  /**
   * Perform a HTTP PUT request.
   */
  async put(url, data = null) {
    return this.dataRequest("PUT", url, data);
  }
  /**
   * Perform a HTTP PATCH request.
   */
  async patch(url, data = null) {
    return this.dataRequest("PATCH", url, data);
  }
  /**
   * Perform a HTTP DELETE request.
   */
  async delete(url, data = null) {
    return this.dataRequest("DELETE", url, data);
  }
  /**
   * Parse the response text for an error response to a user
   * presentable string. Handles a range of errors responses including
   * validation responses & server response text.
   */
  formatErrorResponseText(text) {
    const data = text.startsWith("{") ? JSON.parse(text) : { message: text };
    if (!data) {
      return text;
    }
    if (data.message || data.error) {
      return data.message || data.error;
    }
    const values = Object.values(data);
    const isValidation = values.every((val) => {
      return Array.isArray(val) && val.every((x) => typeof x === "string");
    });
    if (isValidation) {
      return values.flat().join(" ");
    }
    return text;
  }
};

// resources/js/services/translations.ts
var Translator = class {
  /**
   * Parse the given translation and find the correct plural option
   * to use. Similar format at Laravel's 'trans_choice' helper.
   */
  choice(translation, count, replacements = {}) {
    replacements = Object.assign({}, { count: String(count) }, replacements);
    const splitText = translation.split("|");
    const exactCountRegex = /^{([0-9]+)}/;
    const rangeRegex = /^\[([0-9]+),([0-9*]+)]/;
    let result = null;
    for (const t of splitText) {
      const exactMatches = t.match(exactCountRegex);
      if (exactMatches !== null && Number(exactMatches[1]) === count) {
        result = t.replace(exactCountRegex, "").trim();
        break;
      }
      const rangeMatches = t.match(rangeRegex);
      if (rangeMatches !== null) {
        const rangeStart = Number(rangeMatches[1]);
        if (rangeStart <= count && (rangeMatches[2] === "*" || Number(rangeMatches[2]) >= count)) {
          result = t.replace(rangeRegex, "").trim();
          break;
        }
      }
    }
    if (result === null && splitText.length > 1) {
      result = count === 1 ? splitText[0] : splitText[1];
    }
    if (result === null) {
      result = splitText[0];
    }
    return this.performReplacements(result, replacements);
  }
  performReplacements(string, replacements) {
    const replaceMatches = string.match(/:(\S+)/g);
    if (replaceMatches === null) {
      return string;
    }
    let updatedString = string;
    for (const match of replaceMatches) {
      const key = match.substring(1);
      if (typeof replacements[key] === "undefined") {
        continue;
      }
      updatedString = updatedString.replace(match, replacements[key]);
    }
    return updatedString;
  }
};

// resources/js/components/index.ts
var components_exports = {};
__export(components_exports, {
  AddRemoveRows: () => AddRemoveRows,
  AjaxDeleteRow: () => AjaxDeleteRow,
  AjaxForm: () => AjaxForm,
  Attachments: () => Attachments,
  AttachmentsList: () => AttachmentsList,
  AutoSubmit: () => AutoSubmit,
  AutoSuggest: () => AutoSuggest,
  BackToTop: () => BackToTop,
  BookSort: () => BookSort,
  ChapterContents: () => ChapterContents,
  CodeEditor: () => CodeEditor,
  CodeHighlighter: () => CodeHighlighter,
  CodeTextarea: () => CodeTextarea,
  Collapsible: () => Collapsible,
  ConfirmDialog: () => ConfirmDialog,
  CustomCheckbox: () => CustomCheckbox,
  DetailsHighlighter: () => DetailsHighlighter,
  Dropdown: () => Dropdown,
  DropdownSearch: () => DropdownSearch,
  Dropzone: () => Dropzone,
  EditorToolbox: () => EditorToolbox,
  EntityPermissions: () => EntityPermissions,
  EntitySearch: () => EntitySearch,
  EntitySelector: () => EntitySelector,
  EntitySelectorPopup: () => EntitySelectorPopup,
  EventEmitSelect: () => EventEmitSelect,
  ExpandToggle: () => ExpandToggle,
  GlobalSearch: () => GlobalSearch,
  HeaderMobileToggle: () => HeaderMobileToggle,
  ImageManager: () => ImageManager,
  ImagePicker: () => ImagePicker,
  ListSortControl: () => ListSortControl,
  LoadingButton: () => LoadingButton,
  MarkdownEditor: () => MarkdownEditor,
  NewUserPassword: () => NewUserPassword,
  Notification: () => Notification,
  OptionalInput: () => OptionalInput,
  PageComment: () => PageComment,
  PageCommentReference: () => PageCommentReference,
  PageComments: () => PageComments,
  PageDisplay: () => PageDisplay,
  PageEditor: () => PageEditor,
  PagePicker: () => PagePicker,
  PermissionsTable: () => PermissionsTable,
  Pointer: () => Pointer,
  Popup: () => Popup,
  SettingAppColorScheme: () => SettingAppColorScheme,
  SettingColorPicker: () => SettingColorPicker,
  SettingHomepageControl: () => SettingHomepageControl,
  ShelfSort: () => ShelfSort,
  ShortcutInput: () => ShortcutInput,
  Shortcuts: () => Shortcuts,
  SortRuleManager: () => SortRuleManager,
  SortableList: () => SortableList,
  SubmitOnChange: () => SubmitOnChange,
  Tabs: () => Tabs,
  TagManager: () => TagManager,
  TemplateManager: () => TemplateManager,
  ToggleSwitch: () => ToggleSwitch,
  TriLayout: () => TriLayout,
  UserSelect: () => UserSelect,
  WebhookEvents: () => WebhookEvents,
  WysiwygEditor: () => WysiwygEditor,
  WysiwygEditorTinymce: () => WysiwygEditorTinymce,
  WysiwygInput: () => WysiwygInput
});

// resources/js/services/util.ts
function debounce(func, waitMs, immediate) {
  let timeout = null;
  return function debouncedWrapper(...args) {
    const context = this;
    const later = function debouncedTimeout() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    const callNow = immediate && !timeout;
    if (timeout) {
      clearTimeout(timeout);
    }
    timeout = window.setTimeout(later, waitMs);
    if (callNow) func.apply(context, args);
  };
}
function isDetailsElement(element) {
  return element.nodeName === "DETAILS";
}
function scrollAndHighlightElement(element) {
  if (!element) return;
  let parent = element;
  while (parent.parentElement) {
    parent = parent.parentElement;
    if (isDetailsElement(parent) && !parent.open) {
      parent.open = true;
    }
  }
  element.scrollIntoView({ behavior: "smooth" });
  const highlight = getComputedStyle(document.body).getPropertyValue("--color-link");
  element.style.outline = `2px dashed ${highlight}`;
  element.style.outlineOffset = "5px";
  element.style.removeProperty("transition");
  setTimeout(() => {
    element.style.transition = "outline linear 3s";
    element.style.outline = "2px dashed rgba(0, 0, 0, 0)";
    const listener = () => {
      element.removeEventListener("transitionend", listener);
      element.style.removeProperty("transition");
      element.style.removeProperty("outline");
      element.style.removeProperty("outlineOffset");
    };
    element.addEventListener("transitionend", listener);
  }, 1e3);
}
function escapeHtml(unsafe) {
  return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
function uniqueId() {
  const S4 = () => ((1 + Math.random()) * 65536 | 0).toString(16).substring(1);
  return `${S4() + S4()}-${S4()}-${S4()}-${S4()}-${S4()}${S4()}${S4()}`;
}
function wait(timeMs) {
  return new Promise((res) => {
    setTimeout(res, timeMs);
  });
}
function baseUrl(path) {
  let targetPath = path;
  const baseUrlMeta = document.querySelector('meta[name="base-url"]');
  if (!baseUrlMeta) {
    throw new Error("Could not find expected base-url meta tag in document");
  }
  let basePath = baseUrlMeta.getAttribute("content") || "";
  if (basePath[basePath.length - 1] === "/") {
    basePath = basePath.slice(0, basePath.length - 1);
  }
  if (targetPath[0] === "/") {
    targetPath = targetPath.slice(1);
  }
  return `${basePath}/${targetPath}`;
}
function getVersion() {
  const styleLink = document.querySelector('link[href*="/dist/styles.css?version="]');
  if (!styleLink) {
    throw new Error("Could not find expected style link in document for version use");
  }
  const href = styleLink.getAttribute("href") || "";
  return href.split("?version=").pop() || "";
}
function importVersioned(moduleName) {
  const importPath = window.baseUrl(`dist/${moduleName}.js?version=${getVersion()}`);
  return import(importPath);
}
function cyrb53(str, seed = 0) {
  let h1 = 3735928559 ^ seed, h2 = 1103547991 ^ seed;
  for (let i = 0, ch; i < str.length; i++) {
    ch = str.charCodeAt(i);
    h1 = Math.imul(h1 ^ ch, 2654435761);
    h2 = Math.imul(h2 ^ ch, 1597334677);
  }
  h1 = Math.imul(h1 ^ h1 >>> 16, 2246822507);
  h1 ^= Math.imul(h2 ^ h2 >>> 13, 3266489909);
  h2 = Math.imul(h2 ^ h2 >>> 16, 2246822507);
  h2 ^= Math.imul(h1 ^ h1 >>> 13, 3266489909);
  return String(4294967296 * (2097151 & h2) + (h1 >>> 0));
}

// resources/js/services/dom.ts
function isHTMLElement(el2) {
  return el2 instanceof HTMLElement;
}
function elem(tagName, attrs = {}, children = []) {
  const el2 = document.createElement(tagName);
  for (const [key, val] of Object.entries(attrs)) {
    if (val === null) {
      el2.removeAttribute(key);
    } else {
      el2.setAttribute(key, val);
    }
  }
  for (const child of children) {
    if (typeof child === "string") {
      el2.append(document.createTextNode(child));
    } else {
      el2.append(child);
    }
  }
  return el2;
}
function forEach(selector, callback) {
  const elements = document.querySelectorAll(selector);
  for (const element of elements) {
    callback(element);
  }
}
function onEvents(listenerElement, events, callback) {
  if (listenerElement) {
    for (const eventName of events) {
      listenerElement.addEventListener(eventName, callback);
    }
  }
}
function onSelect(elements, callback) {
  if (!Array.isArray(elements)) {
    elements = [elements];
  }
  for (const listenerElement of elements) {
    listenerElement.addEventListener("click", callback);
    listenerElement.addEventListener("keydown", (event) => {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        callback(event);
      }
    });
  }
}
function onKeyPress(key, elements, callback) {
  if (!Array.isArray(elements)) {
    elements = [elements];
  }
  const listener = (event) => {
    if (event.key === key) {
      callback(event);
    }
  };
  elements.forEach((e) => e.addEventListener("keydown", listener));
}
function onEnterPress(elements, callback) {
  onKeyPress("Enter", elements, callback);
}
function onEscapePress(elements, callback) {
  onKeyPress("Escape", elements, callback);
}
function onChildEvent(listenerElement, childSelector, eventName, callback) {
  listenerElement.addEventListener(eventName, (event) => {
    const matchingChild = event.target?.closest(childSelector);
    if (matchingChild) {
      callback.call(matchingChild, event, matchingChild);
    }
  });
}
function findText(selector, text) {
  const elements = document.querySelectorAll(selector);
  text = text.toLowerCase();
  for (const element of elements) {
    if ((element.textContent || "").toLowerCase().includes(text) && isHTMLElement(element)) {
      return element;
    }
  }
  return null;
}
function showLoading(element) {
  element.innerHTML = '<div class="loading-container"><div></div><div></div><div></div></div>';
}
function getLoading() {
  const wrap2 = document.createElement("div");
  wrap2.classList.add("loading-container");
  wrap2.innerHTML = "<div></div><div></div><div></div>";
  return wrap2;
}
function removeLoading(element) {
  const loadingEls = element.querySelectorAll(".loading-container");
  for (const el2 of loadingEls) {
    el2.remove();
  }
}
function htmlToDom(html) {
  const wrap2 = document.createElement("div");
  wrap2.innerHTML = html;
  window.$components.init(wrap2);
  const firstChild = wrap2.children[0];
  if (!isHTMLElement(firstChild)) {
    throw new Error("Could not find child HTMLElement when creating DOM element from HTML");
  }
  return firstChild;
}
function normalizeNodeTextOffsetToParent(node, offset, parentElement) {
  if (!parentElement.contains(node)) {
    throw new Error("ParentElement must be a prent of element");
  }
  let normalizedOffset = offset;
  let currentNode2 = node.nodeType === Node.TEXT_NODE ? node : node.childNodes[offset];
  while (currentNode2 !== parentElement && currentNode2) {
    if (currentNode2.previousSibling) {
      currentNode2 = currentNode2.previousSibling;
      normalizedOffset += currentNode2.textContent?.length || 0;
    } else {
      currentNode2 = currentNode2.parentNode;
    }
  }
  return normalizedOffset;
}
function findTargetNodeAndOffset(parentNode, offset) {
  if (offset === 0) {
    return { node: parentNode, offset: 0 };
  }
  let currentOffset = 0;
  let currentNode2 = null;
  for (let i = 0; i < parentNode.childNodes.length; i++) {
    currentNode2 = parentNode.childNodes[i];
    if (currentNode2.nodeType === Node.TEXT_NODE) {
      const textLength = (currentNode2.textContent || "").length;
      if (currentOffset + textLength >= offset) {
        return {
          node: currentNode2,
          offset: offset - currentOffset
        };
      }
      currentOffset += textLength;
    } else if (currentNode2.nodeType === Node.ELEMENT_NODE) {
      const elementTextLength = (currentNode2.textContent || "").length;
      if (currentOffset + elementTextLength >= offset) {
        return findTargetNodeAndOffset(currentNode2, offset - currentOffset);
      }
      currentOffset += elementTextLength;
    }
  }
  return null;
}
function hashElement(element) {
  const normalisedElemText = (element.textContent || "").replace(/\s{2,}/g, "");
  return cyrb53(normalisedElemText);
}
function findClosestScrollContainer(start) {
  let el2 = start;
  do {
    const computed = window.getComputedStyle(el2);
    if (computed.overflowY === "scroll") {
      return el2;
    }
    el2 = el2.parentElement;
  } while (el2);
  return document.body;
}

// resources/js/components/component.js
var Component = class {
  constructor() {
    /**
     * The registered name of the component.
     * @type {string}
     */
    __publicField(this, "$name", "");
    /**
     * The element that the component is registered upon.
     * @type {HTMLElement}
     */
    __publicField(this, "$el", null);
    /**
     * Mapping of referenced elements within the component.
     * @type {Object<string, HTMLElement>}
     */
    __publicField(this, "$refs", {});
    /**
     * Mapping of arrays of referenced elements within the component so multiple
     * references, sharing the same name, can be fetched.
     * @type {Object<string, HTMLElement[]>}
     */
    __publicField(this, "$manyRefs", {});
    /**
     * Options passed into this component.
     * @type {Object<String, String>}
     */
    __publicField(this, "$opts", {});
  }
  /**
   * Component-specific setup methods.
   * Use this to assign local variables and run any initial setup or actions.
   */
  setup() {
  }
  /**
   * Emit an event from this component.
   * Will be bubbled up from the dom element this is registered on, as a custom event
   * with the name `<elementName>-<eventName>`, with the provided data in the event detail.
   * @param {String} eventName
   * @param {Object} data
   */
  $emit(eventName, data = {}) {
    data.from = this;
    const componentName = this.$name;
    const event = new CustomEvent(`${componentName}-${eventName}`, {
      bubbles: true,
      detail: data
    });
    this.$el.dispatchEvent(event);
  }
};

// resources/js/components/add-remove-rows.js
var AddRemoveRows = class extends Component {
  setup() {
    this.modelRow = this.$refs.model;
    this.addButton = this.$refs.add;
    this.removeSelector = this.$opts.removeSelector;
    this.rowSelector = this.$opts.rowSelector;
    this.setupListeners();
  }
  setupListeners() {
    this.addButton.addEventListener("click", this.add.bind(this));
    onChildEvent(this.$el, this.removeSelector, "click", (e) => {
      const row = e.target.closest(this.rowSelector);
      row.remove();
    });
  }
  // For external use
  add() {
    const clone2 = this.modelRow.cloneNode(true);
    clone2.classList.remove("hidden");
    this.setClonedInputNames(clone2);
    this.modelRow.parentNode.insertBefore(clone2, this.modelRow);
    window.$components.init(clone2);
  }
  /**
   * Update the HTML names of a clone to be unique if required.
   * Names can use placeholder values. For exmaple, a model row
   * may have name="tags[randrowid][name]".
   * These are the available placeholder values:
   * - randrowid - An random string ID, applied the same across the row.
   * @param {HTMLElement} clone
   */
  setClonedInputNames(clone2) {
    const rowId = uniqueId();
    const randRowIdElems = clone2.querySelectorAll('[name*="randrowid"]');
    for (const elem2 of randRowIdElems) {
      elem2.name = elem2.name.split("randrowid").join(rowId);
    }
  }
};

// resources/js/components/ajax-delete-row.ts
var AjaxDeleteRow = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "row");
    __publicField(this, "url");
    __publicField(this, "deleteButtons", []);
  }
  setup() {
    this.row = this.$el;
    this.url = this.$opts.url;
    this.deleteButtons = this.$manyRefs.delete || [];
    onSelect(this.deleteButtons, this.runDelete.bind(this));
  }
  runDelete() {
    this.row.style.opacity = "0.7";
    this.row.style.pointerEvents = "none";
    window.$http.delete(this.url).then((resp) => {
      if (typeof resp.data === "object" && resp.data.message) {
        window.$events.emit("success", resp.data.message);
      }
      this.row.remove();
    }).catch(() => {
      this.row.style.removeProperty("opacity");
      this.row.style.removeProperty("pointer-events");
    });
  }
};

// resources/js/components/ajax-form.js
var AjaxForm = class extends Component {
  setup() {
    this.container = this.$el;
    this.responseContainer = this.container;
    this.url = this.$opts.url;
    this.method = this.$opts.method || "post";
    this.successMessage = this.$opts.successMessage;
    this.submitButtons = this.$manyRefs.submit || [];
    if (this.$opts.responseContainer) {
      this.responseContainer = this.container.closest(this.$opts.responseContainer);
    }
    this.setupListeners();
  }
  setupListeners() {
    if (this.container.tagName === "FORM") {
      this.container.addEventListener("submit", this.submitRealForm.bind(this));
      return;
    }
    onEnterPress(this.container, (event) => {
      this.submitFakeForm();
      event.preventDefault();
    });
    this.submitButtons.forEach((button) => onSelect(button, this.submitFakeForm.bind(this)));
  }
  submitFakeForm() {
    const fd = new FormData();
    const inputs = this.container.querySelectorAll("[name]");
    for (const input of inputs) {
      fd.append(input.getAttribute("name"), input.value);
    }
    this.submit(fd);
  }
  submitRealForm(event) {
    event.preventDefault();
    const fd = new FormData(this.container);
    this.submit(fd);
  }
  async submit(formData) {
    this.responseContainer.style.opacity = "0.7";
    this.responseContainer.style.pointerEvents = "none";
    try {
      const resp = await window.$http[this.method.toLowerCase()](this.url, formData);
      this.$emit("success", { formData });
      this.responseContainer.innerHTML = resp.data;
      if (this.successMessage) {
        window.$events.emit("success", this.successMessage);
      }
    } catch (err) {
      this.responseContainer.innerHTML = err.data;
    }
    window.$components.init(this.responseContainer);
    this.responseContainer.style.opacity = null;
    this.responseContainer.style.pointerEvents = null;
  }
};

// resources/js/components/attachments.js
var Attachments = class extends Component {
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
    this.container.addEventListener("dropzone-upload-success", reloadListBound);
    this.container.addEventListener("ajax-form-success", reloadListBound);
    this.container.addEventListener("sortable-list-sort", (event) => {
      this.updateOrder(event.detail.ids);
    });
    this.container.addEventListener("event-emit-select-edit", (event) => {
      this.startEdit(event.detail.id);
    });
    this.container.addEventListener("event-emit-select-edit-back", () => {
      this.stopEdit();
    });
    this.container.addEventListener("event-emit-select-insert", (event) => {
      const insertContent = event.target.closest("[data-drag-content]").getAttribute("data-drag-content");
      const contentTypes = JSON.parse(insertContent);
      window.$events.emit("editor::insert", {
        html: contentTypes["text/html"],
        markdown: contentTypes["text/plain"]
      });
    });
    this.attachLinkButton.addEventListener("click", () => {
      this.showSection("links");
    });
  }
  showSection(section) {
    const sectionMap = {
      links: this.linksContainer,
      edit: this.editContainer,
      list: this.listContainer
    };
    for (const [name, elem2] of Object.entries(sectionMap)) {
      elem2.toggleAttribute("hidden", name !== section);
    }
  }
  reloadList() {
    this.stopEdit();
    window.$http.get(`/attachments/get/page/${this.pageId}`).then((resp) => {
      this.listPanel.innerHTML = resp.data;
      window.$components.init(this.listPanel);
    });
  }
  updateOrder(idOrder) {
    window.$http.put(`/attachments/sort/page/${this.pageId}`, { order: idOrder }).then((resp) => {
      window.$events.emit("success", resp.data.message);
    });
  }
  async startEdit(id) {
    this.showSection("edit");
    showLoading(this.editContainer);
    const resp = await window.$http.get(`/attachments/edit/${id}`);
    this.editContainer.innerHTML = resp.data;
    window.$components.init(this.editContainer);
  }
  stopEdit() {
    this.showSection("list");
  }
};

// resources/js/components/attachments-list.js
var AttachmentsList = class extends Component {
  setup() {
    this.container = this.$el;
    this.fileLinks = this.$manyRefs.linkTypeFile;
    this.setupListeners();
  }
  setupListeners() {
    const isExpectedKey = (event) => event.key === "Control" || event.key === "Meta";
    window.addEventListener("keydown", (event) => {
      if (isExpectedKey(event)) {
        this.addOpenQueryToLinks();
      }
    }, { passive: true });
    window.addEventListener("keyup", (event) => {
      if (isExpectedKey(event)) {
        this.removeOpenQueryFromLinks();
      }
    }, { passive: true });
  }
  addOpenQueryToLinks() {
    for (const link of this.fileLinks) {
      if (link.href.split("?")[1] !== "open=true") {
        link.href += "?open=true";
        link.setAttribute("target", "_blank");
      }
    }
  }
  removeOpenQueryFromLinks() {
    for (const link of this.fileLinks) {
      link.href = link.href.split("?")[0];
      link.removeAttribute("target");
    }
  }
};

// resources/js/services/keyboard-navigation.ts
var _KeyboardNavigationHandler_instances, keydownHandler_fn, getFocusable_fn;
var KeyboardNavigationHandler = class {
  constructor(container, onEscape = null, onEnter = null) {
    __privateAdd(this, _KeyboardNavigationHandler_instances);
    __publicField(this, "containers");
    __publicField(this, "onEscape");
    __publicField(this, "onEnter");
    this.containers = [container];
    this.onEscape = onEscape;
    this.onEnter = onEnter;
    container.addEventListener("keydown", __privateMethod(this, _KeyboardNavigationHandler_instances, keydownHandler_fn).bind(this));
  }
  /**
   * Also share the keyboard event handling to the given element.
   * Only elements within the original container are considered focusable though.
   */
  shareHandlingToEl(element) {
    this.containers.push(element);
    element.addEventListener("keydown", __privateMethod(this, _KeyboardNavigationHandler_instances, keydownHandler_fn).bind(this));
  }
  /**
   * Focus on the next focusable element within the current containers.
   */
  focusNext() {
    const focusable = __privateMethod(this, _KeyboardNavigationHandler_instances, getFocusable_fn).call(this);
    const activeEl = document.activeElement;
    const currentIndex = isHTMLElement(activeEl) ? focusable.indexOf(activeEl) : -1;
    let newIndex2 = currentIndex + 1;
    if (newIndex2 >= focusable.length) {
      newIndex2 = 0;
    }
    focusable[newIndex2].focus();
  }
  /**
   * Focus on the previous existing focusable element within the current containers.
   */
  focusPrevious() {
    const focusable = __privateMethod(this, _KeyboardNavigationHandler_instances, getFocusable_fn).call(this);
    const activeEl = document.activeElement;
    const currentIndex = isHTMLElement(activeEl) ? focusable.indexOf(activeEl) : -1;
    let newIndex2 = currentIndex - 1;
    if (newIndex2 < 0) {
      newIndex2 = focusable.length - 1;
    }
    focusable[newIndex2].focus();
  }
};
_KeyboardNavigationHandler_instances = new WeakSet();
keydownHandler_fn = function(event) {
  if (isHTMLElement(event.target) && event.target.matches("input") && (event.key === "ArrowRight" || event.key === "ArrowLeft")) {
    return;
  }
  if (event.key === "ArrowDown" || event.key === "ArrowRight") {
    this.focusNext();
    event.preventDefault();
  } else if (event.key === "ArrowUp" || event.key === "ArrowLeft") {
    this.focusPrevious();
    event.preventDefault();
  } else if (event.key === "Escape") {
    if (this.onEscape) {
      this.onEscape(event);
    } else if (isHTMLElement(document.activeElement)) {
      document.activeElement.blur();
    }
  } else if (event.key === "Enter" && this.onEnter) {
    this.onEnter(event);
  }
};
/**
 * Get an array of focusable elements within the current containers.
 */
getFocusable_fn = function() {
  const focusable = [];
  const selector = '[tabindex]:not([tabindex="-1"]),[href],button:not([tabindex="-1"],[disabled]),input:not([type=hidden])';
  for (const container of this.containers) {
    const toAdd = [...container.querySelectorAll(selector)].filter((e) => isHTMLElement(e));
    focusable.push(...toAdd);
  }
  return focusable;
};

// resources/js/components/auto-suggest.js
var ajaxCache = {};
var AutoSuggest = class extends Component {
  setup() {
    this.parent = this.$el.parentElement;
    this.container = this.$el;
    this.type = this.$opts.type;
    this.url = this.$opts.url;
    this.input = this.$refs.input;
    this.list = this.$refs.list;
    this.lastPopulated = 0;
    this.setupListeners();
  }
  setupListeners() {
    const navHandler = new KeyboardNavigationHandler(
      this.list,
      () => {
        this.input.focus();
        setTimeout(() => this.hideSuggestions(), 1);
      },
      (event) => {
        event.preventDefault();
        const selectionValue = event.target.textContent;
        if (selectionValue) {
          this.selectSuggestion(selectionValue);
        }
      }
    );
    navHandler.shareHandlingToEl(this.input);
    onChildEvent(this.list, ".text-item", "click", (event, el2) => {
      this.selectSuggestion(el2.textContent);
    });
    this.input.addEventListener("input", this.requestSuggestions.bind(this));
    this.input.addEventListener("focus", this.requestSuggestions.bind(this));
    this.input.addEventListener("blur", this.hideSuggestionsIfFocusedLost.bind(this));
    this.input.addEventListener("keydown", (event) => {
      if (event.key === "Tab") {
        this.hideSuggestions();
      }
    });
  }
  selectSuggestion(value) {
    this.input.value = value;
    this.lastPopulated = Date.now();
    this.input.focus();
    this.input.dispatchEvent(new Event("input", { bubbles: true }));
    this.input.dispatchEvent(new Event("change", { bubbles: true }));
    this.hideSuggestions();
  }
  async requestSuggestions() {
    if (Date.now() - this.lastPopulated < 50) {
      return;
    }
    const nameFilter = this.getNameFilterIfNeeded();
    const search = this.input.value.toLowerCase();
    const suggestions = await this.loadSuggestions(search, nameFilter);
    const toShow = suggestions.filter((val) => search === "" || val.toLowerCase().startsWith(search)).slice(0, 10);
    this.displaySuggestions(toShow);
  }
  getNameFilterIfNeeded() {
    if (this.type !== "value") return null;
    return this.parent.querySelector("input").value;
  }
  /**
   * @param {String} search
   * @param {String|null} nameFilter
   * @returns {Promise<Object|String|*>}
   */
  async loadSuggestions(search, nameFilter = null) {
    search = search.slice(0, 4);
    const params = { search, name: nameFilter };
    const cacheKey = `${this.url}:${JSON.stringify(params)}`;
    if (ajaxCache[cacheKey]) {
      return ajaxCache[cacheKey];
    }
    const resp = await window.$http.get(this.url, params);
    ajaxCache[cacheKey] = resp.data;
    return resp.data;
  }
  /**
   * @param {String[]} suggestions
   */
  displaySuggestions(suggestions) {
    if (suggestions.length === 0) {
      this.hideSuggestions();
      return;
    }
    this.list.innerHTML = suggestions.map((value) => `<li><div tabindex="0" class="text-item">${escapeHtml(value)}</div></li>`).join("");
    this.list.style.display = "block";
    for (const button of this.list.querySelectorAll(".text-item")) {
      button.addEventListener("blur", this.hideSuggestionsIfFocusedLost.bind(this));
    }
  }
  hideSuggestions() {
    this.list.style.display = "none";
  }
  hideSuggestionsIfFocusedLost(event) {
    if (!this.container.contains(event.relatedTarget)) {
      this.hideSuggestions();
    }
  }
};

// resources/js/components/auto-submit.js
var AutoSubmit = class extends Component {
  setup() {
    this.form = this.$el;
    this.form.submit();
  }
};

// resources/js/components/back-to-top.js
var BackToTop = class extends Component {
  setup() {
    this.button = this.$el;
    this.targetElem = document.getElementById("header");
    this.showing = false;
    this.breakPoint = 1200;
    if (document.body.classList.contains("flexbox")) {
      this.button.style.display = "none";
      return;
    }
    this.button.addEventListener("click", this.scrollToTop.bind(this));
    window.addEventListener("scroll", this.onPageScroll.bind(this));
  }
  onPageScroll() {
    const scrollTopPos = document.documentElement.scrollTop || document.body.scrollTop || 0;
    if (!this.showing && scrollTopPos > this.breakPoint) {
      this.button.style.display = "block";
      this.showing = true;
      setTimeout(() => {
        this.button.style.opacity = 0.4;
      }, 1);
    } else if (this.showing && scrollTopPos < this.breakPoint) {
      this.button.style.opacity = 0;
      this.showing = false;
      setTimeout(() => {
        this.button.style.display = "none";
      }, 500);
    }
  }
  scrollToTop() {
    const targetTop = this.targetElem.getBoundingClientRect().top;
    const scrollElem = document.documentElement.scrollTop ? document.documentElement : document.body;
    const duration = 300;
    const start = Date.now();
    const scrollStart = this.targetElem.getBoundingClientRect().top;
    function setPos() {
      const percentComplete = 1 - (Date.now() - start) / duration;
      const target = Math.abs(percentComplete * scrollStart);
      if (percentComplete > 0) {
        scrollElem.scrollTop = target;
        requestAnimationFrame(setPos.bind(this));
      } else {
        scrollElem.scrollTop = targetTop;
      }
    }
    requestAnimationFrame(setPos.bind(this));
  }
};

// node_modules/sortablejs/modular/sortable.esm.js
function ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);
  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    if (enumerableOnly) {
      symbols = symbols.filter(function(sym) {
        return Object.getOwnPropertyDescriptor(object, sym).enumerable;
      });
    }
    keys.push.apply(keys, symbols);
  }
  return keys;
}
function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    if (i % 2) {
      ownKeys(Object(source), true).forEach(function(key) {
        _defineProperty(target, key, source[key]);
      });
    } else if (Object.getOwnPropertyDescriptors) {
      Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
    } else {
      ownKeys(Object(source)).forEach(function(key) {
        Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
      });
    }
  }
  return target;
}
function _typeof(obj) {
  "@babel/helpers - typeof";
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function(obj2) {
      return typeof obj2;
    };
  } else {
    _typeof = function(obj2) {
      return obj2 && typeof Symbol === "function" && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
    };
  }
  return _typeof(obj);
}
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }
  return obj;
}
function _extends() {
  _extends = Object.assign || function(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return _extends.apply(this, arguments);
}
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;
  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }
  return target;
}
function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = _objectWithoutPropertiesLoose(source, excluded);
  var key, i;
  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);
    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }
  return target;
}
function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
}
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return _arrayLikeToArray(arr);
}
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}
function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return _arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
}
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;
  for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
  return arr2;
}
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
var version = "1.15.6";
function userAgent(pattern) {
  if (typeof window !== "undefined" && window.navigator) {
    return !!/* @__PURE__ */ navigator.userAgent.match(pattern);
  }
}
var IE11OrLess = userAgent(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i);
var Edge = userAgent(/Edge/i);
var FireFox = userAgent(/firefox/i);
var Safari = userAgent(/safari/i) && !userAgent(/chrome/i) && !userAgent(/android/i);
var IOS = userAgent(/iP(ad|od|hone)/i);
var ChromeForAndroid = userAgent(/chrome/i) && userAgent(/android/i);
var captureMode = {
  capture: false,
  passive: false
};
function on(el2, event, fn) {
  el2.addEventListener(event, fn, !IE11OrLess && captureMode);
}
function off(el2, event, fn) {
  el2.removeEventListener(event, fn, !IE11OrLess && captureMode);
}
function matches(el2, selector) {
  if (!selector) return;
  selector[0] === ">" && (selector = selector.substring(1));
  if (el2) {
    try {
      if (el2.matches) {
        return el2.matches(selector);
      } else if (el2.msMatchesSelector) {
        return el2.msMatchesSelector(selector);
      } else if (el2.webkitMatchesSelector) {
        return el2.webkitMatchesSelector(selector);
      }
    } catch (_) {
      return false;
    }
  }
  return false;
}
function getParentOrHost(el2) {
  return el2.host && el2 !== document && el2.host.nodeType ? el2.host : el2.parentNode;
}
function closest(el2, selector, ctx, includeCTX) {
  if (el2) {
    ctx = ctx || document;
    do {
      if (selector != null && (selector[0] === ">" ? el2.parentNode === ctx && matches(el2, selector) : matches(el2, selector)) || includeCTX && el2 === ctx) {
        return el2;
      }
      if (el2 === ctx) break;
    } while (el2 = getParentOrHost(el2));
  }
  return null;
}
var R_SPACE = /\s+/g;
function toggleClass(el2, name, state) {
  if (el2 && name) {
    if (el2.classList) {
      el2.classList[state ? "add" : "remove"](name);
    } else {
      var className = (" " + el2.className + " ").replace(R_SPACE, " ").replace(" " + name + " ", " ");
      el2.className = (className + (state ? " " + name : "")).replace(R_SPACE, " ");
    }
  }
}
function css(el2, prop, val) {
  var style = el2 && el2.style;
  if (style) {
    if (val === void 0) {
      if (document.defaultView && document.defaultView.getComputedStyle) {
        val = document.defaultView.getComputedStyle(el2, "");
      } else if (el2.currentStyle) {
        val = el2.currentStyle;
      }
      return prop === void 0 ? val : val[prop];
    } else {
      if (!(prop in style) && prop.indexOf("webkit") === -1) {
        prop = "-webkit-" + prop;
      }
      style[prop] = val + (typeof val === "string" ? "" : "px");
    }
  }
}
function matrix(el2, selfOnly) {
  var appliedTransforms = "";
  if (typeof el2 === "string") {
    appliedTransforms = el2;
  } else {
    do {
      var transform = css(el2, "transform");
      if (transform && transform !== "none") {
        appliedTransforms = transform + " " + appliedTransforms;
      }
    } while (!selfOnly && (el2 = el2.parentNode));
  }
  var matrixFn = window.DOMMatrix || window.WebKitCSSMatrix || window.CSSMatrix || window.MSCSSMatrix;
  return matrixFn && new matrixFn(appliedTransforms);
}
function find(ctx, tagName, iterator) {
  if (ctx) {
    var list = ctx.getElementsByTagName(tagName), i = 0, n = list.length;
    if (iterator) {
      for (; i < n; i++) {
        iterator(list[i], i);
      }
    }
    return list;
  }
  return [];
}
function getWindowScrollingElement() {
  var scrollingElement = document.scrollingElement;
  if (scrollingElement) {
    return scrollingElement;
  } else {
    return document.documentElement;
  }
}
function getRect(el2, relativeToContainingBlock, relativeToNonStaticParent, undoScale, container) {
  if (!el2.getBoundingClientRect && el2 !== window) return;
  var elRect, top, left, bottom, right, height, width;
  if (el2 !== window && el2.parentNode && el2 !== getWindowScrollingElement()) {
    elRect = el2.getBoundingClientRect();
    top = elRect.top;
    left = elRect.left;
    bottom = elRect.bottom;
    right = elRect.right;
    height = elRect.height;
    width = elRect.width;
  } else {
    top = 0;
    left = 0;
    bottom = window.innerHeight;
    right = window.innerWidth;
    height = window.innerHeight;
    width = window.innerWidth;
  }
  if ((relativeToContainingBlock || relativeToNonStaticParent) && el2 !== window) {
    container = container || el2.parentNode;
    if (!IE11OrLess) {
      do {
        if (container && container.getBoundingClientRect && (css(container, "transform") !== "none" || relativeToNonStaticParent && css(container, "position") !== "static")) {
          var containerRect = container.getBoundingClientRect();
          top -= containerRect.top + parseInt(css(container, "border-top-width"));
          left -= containerRect.left + parseInt(css(container, "border-left-width"));
          bottom = top + elRect.height;
          right = left + elRect.width;
          break;
        }
      } while (container = container.parentNode);
    }
  }
  if (undoScale && el2 !== window) {
    var elMatrix = matrix(container || el2), scaleX = elMatrix && elMatrix.a, scaleY = elMatrix && elMatrix.d;
    if (elMatrix) {
      top /= scaleY;
      left /= scaleX;
      width /= scaleX;
      height /= scaleY;
      bottom = top + height;
      right = left + width;
    }
  }
  return {
    top,
    left,
    bottom,
    right,
    width,
    height
  };
}
function isScrolledPast(el2, elSide, parentSide) {
  var parent = getParentAutoScrollElement(el2, true), elSideVal = getRect(el2)[elSide];
  while (parent) {
    var parentSideVal = getRect(parent)[parentSide], visible = void 0;
    if (parentSide === "top" || parentSide === "left") {
      visible = elSideVal >= parentSideVal;
    } else {
      visible = elSideVal <= parentSideVal;
    }
    if (!visible) return parent;
    if (parent === getWindowScrollingElement()) break;
    parent = getParentAutoScrollElement(parent, false);
  }
  return false;
}
function getChild(el2, childNum, options2, includeDragEl) {
  var currentChild = 0, i = 0, children = el2.children;
  while (i < children.length) {
    if (children[i].style.display !== "none" && children[i] !== Sortable.ghost && (includeDragEl || children[i] !== Sortable.dragged) && closest(children[i], options2.draggable, el2, false)) {
      if (currentChild === childNum) {
        return children[i];
      }
      currentChild++;
    }
    i++;
  }
  return null;
}
function lastChild(el2, selector) {
  var last = el2.lastElementChild;
  while (last && (last === Sortable.ghost || css(last, "display") === "none" || selector && !matches(last, selector))) {
    last = last.previousElementSibling;
  }
  return last || null;
}
function index(el2, selector) {
  var index2 = 0;
  if (!el2 || !el2.parentNode) {
    return -1;
  }
  while (el2 = el2.previousElementSibling) {
    if (el2.nodeName.toUpperCase() !== "TEMPLATE" && el2 !== Sortable.clone && (!selector || matches(el2, selector))) {
      index2++;
    }
  }
  return index2;
}
function getRelativeScrollOffset(el2) {
  var offsetLeft = 0, offsetTop = 0, winScroller = getWindowScrollingElement();
  if (el2) {
    do {
      var elMatrix = matrix(el2), scaleX = elMatrix.a, scaleY = elMatrix.d;
      offsetLeft += el2.scrollLeft * scaleX;
      offsetTop += el2.scrollTop * scaleY;
    } while (el2 !== winScroller && (el2 = el2.parentNode));
  }
  return [offsetLeft, offsetTop];
}
function indexOfObject(arr, obj) {
  for (var i in arr) {
    if (!arr.hasOwnProperty(i)) continue;
    for (var key in obj) {
      if (obj.hasOwnProperty(key) && obj[key] === arr[i][key]) return Number(i);
    }
  }
  return -1;
}
function getParentAutoScrollElement(el2, includeSelf) {
  if (!el2 || !el2.getBoundingClientRect) return getWindowScrollingElement();
  var elem2 = el2;
  var gotSelf = false;
  do {
    if (elem2.clientWidth < elem2.scrollWidth || elem2.clientHeight < elem2.scrollHeight) {
      var elemCSS = css(elem2);
      if (elem2.clientWidth < elem2.scrollWidth && (elemCSS.overflowX == "auto" || elemCSS.overflowX == "scroll") || elem2.clientHeight < elem2.scrollHeight && (elemCSS.overflowY == "auto" || elemCSS.overflowY == "scroll")) {
        if (!elem2.getBoundingClientRect || elem2 === document.body) return getWindowScrollingElement();
        if (gotSelf || includeSelf) return elem2;
        gotSelf = true;
      }
    }
  } while (elem2 = elem2.parentNode);
  return getWindowScrollingElement();
}
function extend(dst, src) {
  if (dst && src) {
    for (var key in src) {
      if (src.hasOwnProperty(key)) {
        dst[key] = src[key];
      }
    }
  }
  return dst;
}
function isRectEqual(rect1, rect2) {
  return Math.round(rect1.top) === Math.round(rect2.top) && Math.round(rect1.left) === Math.round(rect2.left) && Math.round(rect1.height) === Math.round(rect2.height) && Math.round(rect1.width) === Math.round(rect2.width);
}
var _throttleTimeout;
function throttle(callback, ms) {
  return function() {
    if (!_throttleTimeout) {
      var args = arguments, _this = this;
      if (args.length === 1) {
        callback.call(_this, args[0]);
      } else {
        callback.apply(_this, args);
      }
      _throttleTimeout = setTimeout(function() {
        _throttleTimeout = void 0;
      }, ms);
    }
  };
}
function cancelThrottle() {
  clearTimeout(_throttleTimeout);
  _throttleTimeout = void 0;
}
function scrollBy(el2, x, y) {
  el2.scrollLeft += x;
  el2.scrollTop += y;
}
function clone(el2) {
  var Polymer = window.Polymer;
  var $ = window.jQuery || window.Zepto;
  if (Polymer && Polymer.dom) {
    return Polymer.dom(el2).cloneNode(true);
  } else if ($) {
    return $(el2).clone(true)[0];
  } else {
    return el2.cloneNode(true);
  }
}
function setRect(el2, rect) {
  css(el2, "position", "absolute");
  css(el2, "top", rect.top);
  css(el2, "left", rect.left);
  css(el2, "width", rect.width);
  css(el2, "height", rect.height);
}
function unsetRect(el2) {
  css(el2, "position", "");
  css(el2, "top", "");
  css(el2, "left", "");
  css(el2, "width", "");
  css(el2, "height", "");
}
function getChildContainingRectFromElement(container, options2, ghostEl2) {
  var rect = {};
  Array.from(container.children).forEach(function(child) {
    var _rect$left, _rect$top, _rect$right, _rect$bottom;
    if (!closest(child, options2.draggable, container, false) || child.animated || child === ghostEl2) return;
    var childRect = getRect(child);
    rect.left = Math.min((_rect$left = rect.left) !== null && _rect$left !== void 0 ? _rect$left : Infinity, childRect.left);
    rect.top = Math.min((_rect$top = rect.top) !== null && _rect$top !== void 0 ? _rect$top : Infinity, childRect.top);
    rect.right = Math.max((_rect$right = rect.right) !== null && _rect$right !== void 0 ? _rect$right : -Infinity, childRect.right);
    rect.bottom = Math.max((_rect$bottom = rect.bottom) !== null && _rect$bottom !== void 0 ? _rect$bottom : -Infinity, childRect.bottom);
  });
  rect.width = rect.right - rect.left;
  rect.height = rect.bottom - rect.top;
  rect.x = rect.left;
  rect.y = rect.top;
  return rect;
}
var expando = "Sortable" + (/* @__PURE__ */ new Date()).getTime();
function AnimationStateManager() {
  var animationStates = [], animationCallbackId;
  return {
    captureAnimationState: function captureAnimationState() {
      animationStates = [];
      if (!this.options.animation) return;
      var children = [].slice.call(this.el.children);
      children.forEach(function(child) {
        if (css(child, "display") === "none" || child === Sortable.ghost) return;
        animationStates.push({
          target: child,
          rect: getRect(child)
        });
        var fromRect = _objectSpread2({}, animationStates[animationStates.length - 1].rect);
        if (child.thisAnimationDuration) {
          var childMatrix = matrix(child, true);
          if (childMatrix) {
            fromRect.top -= childMatrix.f;
            fromRect.left -= childMatrix.e;
          }
        }
        child.fromRect = fromRect;
      });
    },
    addAnimationState: function addAnimationState(state) {
      animationStates.push(state);
    },
    removeAnimationState: function removeAnimationState(target) {
      animationStates.splice(indexOfObject(animationStates, {
        target
      }), 1);
    },
    animateAll: function animateAll(callback) {
      var _this = this;
      if (!this.options.animation) {
        clearTimeout(animationCallbackId);
        if (typeof callback === "function") callback();
        return;
      }
      var animating = false, animationTime = 0;
      animationStates.forEach(function(state) {
        var time = 0, target = state.target, fromRect = target.fromRect, toRect = getRect(target), prevFromRect = target.prevFromRect, prevToRect = target.prevToRect, animatingRect = state.rect, targetMatrix = matrix(target, true);
        if (targetMatrix) {
          toRect.top -= targetMatrix.f;
          toRect.left -= targetMatrix.e;
        }
        target.toRect = toRect;
        if (target.thisAnimationDuration) {
          if (isRectEqual(prevFromRect, toRect) && !isRectEqual(fromRect, toRect) && // Make sure animatingRect is on line between toRect & fromRect
          (animatingRect.top - toRect.top) / (animatingRect.left - toRect.left) === (fromRect.top - toRect.top) / (fromRect.left - toRect.left)) {
            time = calculateRealTime(animatingRect, prevFromRect, prevToRect, _this.options);
          }
        }
        if (!isRectEqual(toRect, fromRect)) {
          target.prevFromRect = fromRect;
          target.prevToRect = toRect;
          if (!time) {
            time = _this.options.animation;
          }
          _this.animate(target, animatingRect, toRect, time);
        }
        if (time) {
          animating = true;
          animationTime = Math.max(animationTime, time);
          clearTimeout(target.animationResetTimer);
          target.animationResetTimer = setTimeout(function() {
            target.animationTime = 0;
            target.prevFromRect = null;
            target.fromRect = null;
            target.prevToRect = null;
            target.thisAnimationDuration = null;
          }, time);
          target.thisAnimationDuration = time;
        }
      });
      clearTimeout(animationCallbackId);
      if (!animating) {
        if (typeof callback === "function") callback();
      } else {
        animationCallbackId = setTimeout(function() {
          if (typeof callback === "function") callback();
        }, animationTime);
      }
      animationStates = [];
    },
    animate: function animate(target, currentRect, toRect, duration) {
      if (duration) {
        css(target, "transition", "");
        css(target, "transform", "");
        var elMatrix = matrix(this.el), scaleX = elMatrix && elMatrix.a, scaleY = elMatrix && elMatrix.d, translateX = (currentRect.left - toRect.left) / (scaleX || 1), translateY = (currentRect.top - toRect.top) / (scaleY || 1);
        target.animatingX = !!translateX;
        target.animatingY = !!translateY;
        css(target, "transform", "translate3d(" + translateX + "px," + translateY + "px,0)");
        this.forRepaintDummy = repaint(target);
        css(target, "transition", "transform " + duration + "ms" + (this.options.easing ? " " + this.options.easing : ""));
        css(target, "transform", "translate3d(0,0,0)");
        typeof target.animated === "number" && clearTimeout(target.animated);
        target.animated = setTimeout(function() {
          css(target, "transition", "");
          css(target, "transform", "");
          target.animated = false;
          target.animatingX = false;
          target.animatingY = false;
        }, duration);
      }
    }
  };
}
function repaint(target) {
  return target.offsetWidth;
}
function calculateRealTime(animatingRect, fromRect, toRect, options2) {
  return Math.sqrt(Math.pow(fromRect.top - animatingRect.top, 2) + Math.pow(fromRect.left - animatingRect.left, 2)) / Math.sqrt(Math.pow(fromRect.top - toRect.top, 2) + Math.pow(fromRect.left - toRect.left, 2)) * options2.animation;
}
var plugins = [];
var defaults = {
  initializeByDefault: true
};
var PluginManager = {
  mount: function mount(plugin) {
    for (var option2 in defaults) {
      if (defaults.hasOwnProperty(option2) && !(option2 in plugin)) {
        plugin[option2] = defaults[option2];
      }
    }
    plugins.forEach(function(p) {
      if (p.pluginName === plugin.pluginName) {
        throw "Sortable: Cannot mount plugin ".concat(plugin.pluginName, " more than once");
      }
    });
    plugins.push(plugin);
  },
  pluginEvent: function pluginEvent(eventName, sortable, evt) {
    var _this = this;
    this.eventCanceled = false;
    evt.cancel = function() {
      _this.eventCanceled = true;
    };
    var eventNameGlobal = eventName + "Global";
    plugins.forEach(function(plugin) {
      if (!sortable[plugin.pluginName]) return;
      if (sortable[plugin.pluginName][eventNameGlobal]) {
        sortable[plugin.pluginName][eventNameGlobal](_objectSpread2({
          sortable
        }, evt));
      }
      if (sortable.options[plugin.pluginName] && sortable[plugin.pluginName][eventName]) {
        sortable[plugin.pluginName][eventName](_objectSpread2({
          sortable
        }, evt));
      }
    });
  },
  initializePlugins: function initializePlugins(sortable, el2, defaults2, options2) {
    plugins.forEach(function(plugin) {
      var pluginName = plugin.pluginName;
      if (!sortable.options[pluginName] && !plugin.initializeByDefault) return;
      var initialized = new plugin(sortable, el2, sortable.options);
      initialized.sortable = sortable;
      initialized.options = sortable.options;
      sortable[pluginName] = initialized;
      _extends(defaults2, initialized.defaults);
    });
    for (var option2 in sortable.options) {
      if (!sortable.options.hasOwnProperty(option2)) continue;
      var modified = this.modifyOption(sortable, option2, sortable.options[option2]);
      if (typeof modified !== "undefined") {
        sortable.options[option2] = modified;
      }
    }
  },
  getEventProperties: function getEventProperties(name, sortable) {
    var eventProperties = {};
    plugins.forEach(function(plugin) {
      if (typeof plugin.eventProperties !== "function") return;
      _extends(eventProperties, plugin.eventProperties.call(sortable[plugin.pluginName], name));
    });
    return eventProperties;
  },
  modifyOption: function modifyOption(sortable, name, value) {
    var modifiedValue;
    plugins.forEach(function(plugin) {
      if (!sortable[plugin.pluginName]) return;
      if (plugin.optionListeners && typeof plugin.optionListeners[name] === "function") {
        modifiedValue = plugin.optionListeners[name].call(sortable[plugin.pluginName], value);
      }
    });
    return modifiedValue;
  }
};
function dispatchEvent(_ref) {
  var sortable = _ref.sortable, rootEl2 = _ref.rootEl, name = _ref.name, targetEl = _ref.targetEl, cloneEl2 = _ref.cloneEl, toEl = _ref.toEl, fromEl = _ref.fromEl, oldIndex2 = _ref.oldIndex, newIndex2 = _ref.newIndex, oldDraggableIndex2 = _ref.oldDraggableIndex, newDraggableIndex2 = _ref.newDraggableIndex, originalEvent = _ref.originalEvent, putSortable2 = _ref.putSortable, extraEventProperties = _ref.extraEventProperties;
  sortable = sortable || rootEl2 && rootEl2[expando];
  if (!sortable) return;
  var evt, options2 = sortable.options, onName = "on" + name.charAt(0).toUpperCase() + name.substr(1);
  if (window.CustomEvent && !IE11OrLess && !Edge) {
    evt = new CustomEvent(name, {
      bubbles: true,
      cancelable: true
    });
  } else {
    evt = document.createEvent("Event");
    evt.initEvent(name, true, true);
  }
  evt.to = toEl || rootEl2;
  evt.from = fromEl || rootEl2;
  evt.item = targetEl || rootEl2;
  evt.clone = cloneEl2;
  evt.oldIndex = oldIndex2;
  evt.newIndex = newIndex2;
  evt.oldDraggableIndex = oldDraggableIndex2;
  evt.newDraggableIndex = newDraggableIndex2;
  evt.originalEvent = originalEvent;
  evt.pullMode = putSortable2 ? putSortable2.lastPutMode : void 0;
  var allEventProperties = _objectSpread2(_objectSpread2({}, extraEventProperties), PluginManager.getEventProperties(name, sortable));
  for (var option2 in allEventProperties) {
    evt[option2] = allEventProperties[option2];
  }
  if (rootEl2) {
    rootEl2.dispatchEvent(evt);
  }
  if (options2[onName]) {
    options2[onName].call(sortable, evt);
  }
}
var _excluded = ["evt"];
var pluginEvent2 = function pluginEvent3(eventName, sortable) {
  var _ref = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {}, originalEvent = _ref.evt, data = _objectWithoutProperties(_ref, _excluded);
  PluginManager.pluginEvent.bind(Sortable)(eventName, sortable, _objectSpread2({
    dragEl,
    parentEl,
    ghostEl,
    rootEl,
    nextEl,
    lastDownEl,
    cloneEl,
    cloneHidden,
    dragStarted: moved,
    putSortable,
    activeSortable: Sortable.active,
    originalEvent,
    oldIndex,
    oldDraggableIndex,
    newIndex,
    newDraggableIndex,
    hideGhostForTarget: _hideGhostForTarget,
    unhideGhostForTarget: _unhideGhostForTarget,
    cloneNowHidden: function cloneNowHidden() {
      cloneHidden = true;
    },
    cloneNowShown: function cloneNowShown() {
      cloneHidden = false;
    },
    dispatchSortableEvent: function dispatchSortableEvent(name) {
      _dispatchEvent({
        sortable,
        name,
        originalEvent
      });
    }
  }, data));
};
function _dispatchEvent(info) {
  dispatchEvent(_objectSpread2({
    putSortable,
    cloneEl,
    targetEl: dragEl,
    rootEl,
    oldIndex,
    oldDraggableIndex,
    newIndex,
    newDraggableIndex
  }, info));
}
var dragEl;
var parentEl;
var ghostEl;
var rootEl;
var nextEl;
var lastDownEl;
var cloneEl;
var cloneHidden;
var oldIndex;
var newIndex;
var oldDraggableIndex;
var newDraggableIndex;
var activeGroup;
var putSortable;
var awaitingDragStarted = false;
var ignoreNextClick = false;
var sortables = [];
var tapEvt;
var touchEvt;
var lastDx;
var lastDy;
var tapDistanceLeft;
var tapDistanceTop;
var moved;
var lastTarget;
var lastDirection;
var pastFirstInvertThresh = false;
var isCircumstantialInvert = false;
var targetMoveDistance;
var ghostRelativeParent;
var ghostRelativeParentInitialScroll = [];
var _silent = false;
var savedInputChecked = [];
var documentExists = typeof document !== "undefined";
var PositionGhostAbsolutely = IOS;
var CSSFloatProperty = Edge || IE11OrLess ? "cssFloat" : "float";
var supportDraggable = documentExists && !ChromeForAndroid && !IOS && "draggable" in document.createElement("div");
var supportCssPointerEvents = function() {
  if (!documentExists) return;
  if (IE11OrLess) {
    return false;
  }
  var el2 = document.createElement("x");
  el2.style.cssText = "pointer-events:auto";
  return el2.style.pointerEvents === "auto";
}();
var _detectDirection = function _detectDirection2(el2, options2) {
  var elCSS = css(el2), elWidth = parseInt(elCSS.width) - parseInt(elCSS.paddingLeft) - parseInt(elCSS.paddingRight) - parseInt(elCSS.borderLeftWidth) - parseInt(elCSS.borderRightWidth), child1 = getChild(el2, 0, options2), child2 = getChild(el2, 1, options2), firstChildCSS = child1 && css(child1), secondChildCSS = child2 && css(child2), firstChildWidth = firstChildCSS && parseInt(firstChildCSS.marginLeft) + parseInt(firstChildCSS.marginRight) + getRect(child1).width, secondChildWidth = secondChildCSS && parseInt(secondChildCSS.marginLeft) + parseInt(secondChildCSS.marginRight) + getRect(child2).width;
  if (elCSS.display === "flex") {
    return elCSS.flexDirection === "column" || elCSS.flexDirection === "column-reverse" ? "vertical" : "horizontal";
  }
  if (elCSS.display === "grid") {
    return elCSS.gridTemplateColumns.split(" ").length <= 1 ? "vertical" : "horizontal";
  }
  if (child1 && firstChildCSS["float"] && firstChildCSS["float"] !== "none") {
    var touchingSideChild2 = firstChildCSS["float"] === "left" ? "left" : "right";
    return child2 && (secondChildCSS.clear === "both" || secondChildCSS.clear === touchingSideChild2) ? "vertical" : "horizontal";
  }
  return child1 && (firstChildCSS.display === "block" || firstChildCSS.display === "flex" || firstChildCSS.display === "table" || firstChildCSS.display === "grid" || firstChildWidth >= elWidth && elCSS[CSSFloatProperty] === "none" || child2 && elCSS[CSSFloatProperty] === "none" && firstChildWidth + secondChildWidth > elWidth) ? "vertical" : "horizontal";
};
var _dragElInRowColumn = function _dragElInRowColumn2(dragRect, targetRect, vertical) {
  var dragElS1Opp = vertical ? dragRect.left : dragRect.top, dragElS2Opp = vertical ? dragRect.right : dragRect.bottom, dragElOppLength = vertical ? dragRect.width : dragRect.height, targetS1Opp = vertical ? targetRect.left : targetRect.top, targetS2Opp = vertical ? targetRect.right : targetRect.bottom, targetOppLength = vertical ? targetRect.width : targetRect.height;
  return dragElS1Opp === targetS1Opp || dragElS2Opp === targetS2Opp || dragElS1Opp + dragElOppLength / 2 === targetS1Opp + targetOppLength / 2;
};
var _detectNearestEmptySortable = function _detectNearestEmptySortable2(x, y) {
  var ret;
  sortables.some(function(sortable) {
    var threshold = sortable[expando].options.emptyInsertThreshold;
    if (!threshold || lastChild(sortable)) return;
    var rect = getRect(sortable), insideHorizontally = x >= rect.left - threshold && x <= rect.right + threshold, insideVertically = y >= rect.top - threshold && y <= rect.bottom + threshold;
    if (insideHorizontally && insideVertically) {
      return ret = sortable;
    }
  });
  return ret;
};
var _prepareGroup = function _prepareGroup2(options2) {
  function toFn(value, pull) {
    return function(to, from, dragEl2, evt) {
      var sameGroup = to.options.group.name && from.options.group.name && to.options.group.name === from.options.group.name;
      if (value == null && (pull || sameGroup)) {
        return true;
      } else if (value == null || value === false) {
        return false;
      } else if (pull && value === "clone") {
        return value;
      } else if (typeof value === "function") {
        return toFn(value(to, from, dragEl2, evt), pull)(to, from, dragEl2, evt);
      } else {
        var otherGroup = (pull ? to : from).options.group.name;
        return value === true || typeof value === "string" && value === otherGroup || value.join && value.indexOf(otherGroup) > -1;
      }
    };
  }
  var group = {};
  var originalGroup = options2.group;
  if (!originalGroup || _typeof(originalGroup) != "object") {
    originalGroup = {
      name: originalGroup
    };
  }
  group.name = originalGroup.name;
  group.checkPull = toFn(originalGroup.pull, true);
  group.checkPut = toFn(originalGroup.put);
  group.revertClone = originalGroup.revertClone;
  options2.group = group;
};
var _hideGhostForTarget = function _hideGhostForTarget2() {
  if (!supportCssPointerEvents && ghostEl) {
    css(ghostEl, "display", "none");
  }
};
var _unhideGhostForTarget = function _unhideGhostForTarget2() {
  if (!supportCssPointerEvents && ghostEl) {
    css(ghostEl, "display", "");
  }
};
if (documentExists && !ChromeForAndroid) {
  document.addEventListener("click", function(evt) {
    if (ignoreNextClick) {
      evt.preventDefault();
      evt.stopPropagation && evt.stopPropagation();
      evt.stopImmediatePropagation && evt.stopImmediatePropagation();
      ignoreNextClick = false;
      return false;
    }
  }, true);
}
var nearestEmptyInsertDetectEvent = function nearestEmptyInsertDetectEvent2(evt) {
  if (dragEl) {
    evt = evt.touches ? evt.touches[0] : evt;
    var nearest = _detectNearestEmptySortable(evt.clientX, evt.clientY);
    if (nearest) {
      var event = {};
      for (var i in evt) {
        if (evt.hasOwnProperty(i)) {
          event[i] = evt[i];
        }
      }
      event.target = event.rootEl = nearest;
      event.preventDefault = void 0;
      event.stopPropagation = void 0;
      nearest[expando]._onDragOver(event);
    }
  }
};
var _checkOutsideTargetEl = function _checkOutsideTargetEl2(evt) {
  if (dragEl) {
    dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
  }
};
function Sortable(el2, options2) {
  if (!(el2 && el2.nodeType && el2.nodeType === 1)) {
    throw "Sortable: `el` must be an HTMLElement, not ".concat({}.toString.call(el2));
  }
  this.el = el2;
  this.options = options2 = _extends({}, options2);
  el2[expando] = this;
  var defaults2 = {
    group: null,
    sort: true,
    disabled: false,
    store: null,
    handle: null,
    draggable: /^[uo]l$/i.test(el2.nodeName) ? ">li" : ">*",
    swapThreshold: 1,
    // percentage; 0 <= x <= 1
    invertSwap: false,
    // invert always
    invertedSwapThreshold: null,
    // will be set to same as swapThreshold if default
    removeCloneOnHide: true,
    direction: function direction() {
      return _detectDirection(el2, this.options);
    },
    ghostClass: "sortable-ghost",
    chosenClass: "sortable-chosen",
    dragClass: "sortable-drag",
    ignore: "a, img",
    filter: null,
    preventOnFilter: true,
    animation: 0,
    easing: null,
    setData: function setData(dataTransfer, dragEl2) {
      dataTransfer.setData("Text", dragEl2.textContent);
    },
    dropBubble: false,
    dragoverBubble: false,
    dataIdAttr: "data-id",
    delay: 0,
    delayOnTouchOnly: false,
    touchStartThreshold: (Number.parseInt ? Number : window).parseInt(window.devicePixelRatio, 10) || 1,
    forceFallback: false,
    fallbackClass: "sortable-fallback",
    fallbackOnBody: false,
    fallbackTolerance: 0,
    fallbackOffset: {
      x: 0,
      y: 0
    },
    // Disabled on Safari: #1571; Enabled on Safari IOS: #2244
    supportPointer: Sortable.supportPointer !== false && "PointerEvent" in window && (!Safari || IOS),
    emptyInsertThreshold: 5
  };
  PluginManager.initializePlugins(this, el2, defaults2);
  for (var name in defaults2) {
    !(name in options2) && (options2[name] = defaults2[name]);
  }
  _prepareGroup(options2);
  for (var fn in this) {
    if (fn.charAt(0) === "_" && typeof this[fn] === "function") {
      this[fn] = this[fn].bind(this);
    }
  }
  this.nativeDraggable = options2.forceFallback ? false : supportDraggable;
  if (this.nativeDraggable) {
    this.options.touchStartThreshold = 1;
  }
  if (options2.supportPointer) {
    on(el2, "pointerdown", this._onTapStart);
  } else {
    on(el2, "mousedown", this._onTapStart);
    on(el2, "touchstart", this._onTapStart);
  }
  if (this.nativeDraggable) {
    on(el2, "dragover", this);
    on(el2, "dragenter", this);
  }
  sortables.push(this.el);
  options2.store && options2.store.get && this.sort(options2.store.get(this) || []);
  _extends(this, AnimationStateManager());
}
Sortable.prototype = /** @lends Sortable.prototype */
{
  constructor: Sortable,
  _isOutsideThisEl: function _isOutsideThisEl(target) {
    if (!this.el.contains(target) && target !== this.el) {
      lastTarget = null;
    }
  },
  _getDirection: function _getDirection(evt, target) {
    return typeof this.options.direction === "function" ? this.options.direction.call(this, evt, target, dragEl) : this.options.direction;
  },
  _onTapStart: function _onTapStart(evt) {
    if (!evt.cancelable) return;
    var _this = this, el2 = this.el, options2 = this.options, preventOnFilter = options2.preventOnFilter, type = evt.type, touch = evt.touches && evt.touches[0] || evt.pointerType && evt.pointerType === "touch" && evt, target = (touch || evt).target, originalTarget = evt.target.shadowRoot && (evt.path && evt.path[0] || evt.composedPath && evt.composedPath()[0]) || target, filter = options2.filter;
    _saveInputCheckedState(el2);
    if (dragEl) {
      return;
    }
    if (/mousedown|pointerdown/.test(type) && evt.button !== 0 || options2.disabled) {
      return;
    }
    if (originalTarget.isContentEditable) {
      return;
    }
    if (!this.nativeDraggable && Safari && target && target.tagName.toUpperCase() === "SELECT") {
      return;
    }
    target = closest(target, options2.draggable, el2, false);
    if (target && target.animated) {
      return;
    }
    if (lastDownEl === target) {
      return;
    }
    oldIndex = index(target);
    oldDraggableIndex = index(target, options2.draggable);
    if (typeof filter === "function") {
      if (filter.call(this, evt, target, this)) {
        _dispatchEvent({
          sortable: _this,
          rootEl: originalTarget,
          name: "filter",
          targetEl: target,
          toEl: el2,
          fromEl: el2
        });
        pluginEvent2("filter", _this, {
          evt
        });
        preventOnFilter && evt.preventDefault();
        return;
      }
    } else if (filter) {
      filter = filter.split(",").some(function(criteria) {
        criteria = closest(originalTarget, criteria.trim(), el2, false);
        if (criteria) {
          _dispatchEvent({
            sortable: _this,
            rootEl: criteria,
            name: "filter",
            targetEl: target,
            fromEl: el2,
            toEl: el2
          });
          pluginEvent2("filter", _this, {
            evt
          });
          return true;
        }
      });
      if (filter) {
        preventOnFilter && evt.preventDefault();
        return;
      }
    }
    if (options2.handle && !closest(originalTarget, options2.handle, el2, false)) {
      return;
    }
    this._prepareDragStart(evt, touch, target);
  },
  _prepareDragStart: function _prepareDragStart(evt, touch, target) {
    var _this = this, el2 = _this.el, options2 = _this.options, ownerDocument = el2.ownerDocument, dragStartFn;
    if (target && !dragEl && target.parentNode === el2) {
      var dragRect = getRect(target);
      rootEl = el2;
      dragEl = target;
      parentEl = dragEl.parentNode;
      nextEl = dragEl.nextSibling;
      lastDownEl = target;
      activeGroup = options2.group;
      Sortable.dragged = dragEl;
      tapEvt = {
        target: dragEl,
        clientX: (touch || evt).clientX,
        clientY: (touch || evt).clientY
      };
      tapDistanceLeft = tapEvt.clientX - dragRect.left;
      tapDistanceTop = tapEvt.clientY - dragRect.top;
      this._lastX = (touch || evt).clientX;
      this._lastY = (touch || evt).clientY;
      dragEl.style["will-change"] = "all";
      dragStartFn = function dragStartFn2() {
        pluginEvent2("delayEnded", _this, {
          evt
        });
        if (Sortable.eventCanceled) {
          _this._onDrop();
          return;
        }
        _this._disableDelayedDragEvents();
        if (!FireFox && _this.nativeDraggable) {
          dragEl.draggable = true;
        }
        _this._triggerDragStart(evt, touch);
        _dispatchEvent({
          sortable: _this,
          name: "choose",
          originalEvent: evt
        });
        toggleClass(dragEl, options2.chosenClass, true);
      };
      options2.ignore.split(",").forEach(function(criteria) {
        find(dragEl, criteria.trim(), _disableDraggable);
      });
      on(ownerDocument, "dragover", nearestEmptyInsertDetectEvent);
      on(ownerDocument, "mousemove", nearestEmptyInsertDetectEvent);
      on(ownerDocument, "touchmove", nearestEmptyInsertDetectEvent);
      if (options2.supportPointer) {
        on(ownerDocument, "pointerup", _this._onDrop);
        !this.nativeDraggable && on(ownerDocument, "pointercancel", _this._onDrop);
      } else {
        on(ownerDocument, "mouseup", _this._onDrop);
        on(ownerDocument, "touchend", _this._onDrop);
        on(ownerDocument, "touchcancel", _this._onDrop);
      }
      if (FireFox && this.nativeDraggable) {
        this.options.touchStartThreshold = 4;
        dragEl.draggable = true;
      }
      pluginEvent2("delayStart", this, {
        evt
      });
      if (options2.delay && (!options2.delayOnTouchOnly || touch) && (!this.nativeDraggable || !(Edge || IE11OrLess))) {
        if (Sortable.eventCanceled) {
          this._onDrop();
          return;
        }
        if (options2.supportPointer) {
          on(ownerDocument, "pointerup", _this._disableDelayedDrag);
          on(ownerDocument, "pointercancel", _this._disableDelayedDrag);
        } else {
          on(ownerDocument, "mouseup", _this._disableDelayedDrag);
          on(ownerDocument, "touchend", _this._disableDelayedDrag);
          on(ownerDocument, "touchcancel", _this._disableDelayedDrag);
        }
        on(ownerDocument, "mousemove", _this._delayedDragTouchMoveHandler);
        on(ownerDocument, "touchmove", _this._delayedDragTouchMoveHandler);
        options2.supportPointer && on(ownerDocument, "pointermove", _this._delayedDragTouchMoveHandler);
        _this._dragStartTimer = setTimeout(dragStartFn, options2.delay);
      } else {
        dragStartFn();
      }
    }
  },
  _delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(e) {
    var touch = e.touches ? e.touches[0] : e;
    if (Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1))) {
      this._disableDelayedDrag();
    }
  },
  _disableDelayedDrag: function _disableDelayedDrag() {
    dragEl && _disableDraggable(dragEl);
    clearTimeout(this._dragStartTimer);
    this._disableDelayedDragEvents();
  },
  _disableDelayedDragEvents: function _disableDelayedDragEvents() {
    var ownerDocument = this.el.ownerDocument;
    off(ownerDocument, "mouseup", this._disableDelayedDrag);
    off(ownerDocument, "touchend", this._disableDelayedDrag);
    off(ownerDocument, "touchcancel", this._disableDelayedDrag);
    off(ownerDocument, "pointerup", this._disableDelayedDrag);
    off(ownerDocument, "pointercancel", this._disableDelayedDrag);
    off(ownerDocument, "mousemove", this._delayedDragTouchMoveHandler);
    off(ownerDocument, "touchmove", this._delayedDragTouchMoveHandler);
    off(ownerDocument, "pointermove", this._delayedDragTouchMoveHandler);
  },
  _triggerDragStart: function _triggerDragStart(evt, touch) {
    touch = touch || evt.pointerType == "touch" && evt;
    if (!this.nativeDraggable || touch) {
      if (this.options.supportPointer) {
        on(document, "pointermove", this._onTouchMove);
      } else if (touch) {
        on(document, "touchmove", this._onTouchMove);
      } else {
        on(document, "mousemove", this._onTouchMove);
      }
    } else {
      on(dragEl, "dragend", this);
      on(rootEl, "dragstart", this._onDragStart);
    }
    try {
      if (document.selection) {
        _nextTick(function() {
          document.selection.empty();
        });
      } else {
        window.getSelection().removeAllRanges();
      }
    } catch (err) {
    }
  },
  _dragStarted: function _dragStarted(fallback, evt) {
    awaitingDragStarted = false;
    if (rootEl && dragEl) {
      pluginEvent2("dragStarted", this, {
        evt
      });
      if (this.nativeDraggable) {
        on(document, "dragover", _checkOutsideTargetEl);
      }
      var options2 = this.options;
      !fallback && toggleClass(dragEl, options2.dragClass, false);
      toggleClass(dragEl, options2.ghostClass, true);
      Sortable.active = this;
      fallback && this._appendGhost();
      _dispatchEvent({
        sortable: this,
        name: "start",
        originalEvent: evt
      });
    } else {
      this._nulling();
    }
  },
  _emulateDragOver: function _emulateDragOver() {
    if (touchEvt) {
      this._lastX = touchEvt.clientX;
      this._lastY = touchEvt.clientY;
      _hideGhostForTarget();
      var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
      var parent = target;
      while (target && target.shadowRoot) {
        target = target.shadowRoot.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
        if (target === parent) break;
        parent = target;
      }
      dragEl.parentNode[expando]._isOutsideThisEl(target);
      if (parent) {
        do {
          if (parent[expando]) {
            var inserted = void 0;
            inserted = parent[expando]._onDragOver({
              clientX: touchEvt.clientX,
              clientY: touchEvt.clientY,
              target,
              rootEl: parent
            });
            if (inserted && !this.options.dragoverBubble) {
              break;
            }
          }
          target = parent;
        } while (parent = getParentOrHost(parent));
      }
      _unhideGhostForTarget();
    }
  },
  _onTouchMove: function _onTouchMove(evt) {
    if (tapEvt) {
      var options2 = this.options, fallbackTolerance = options2.fallbackTolerance, fallbackOffset = options2.fallbackOffset, touch = evt.touches ? evt.touches[0] : evt, ghostMatrix = ghostEl && matrix(ghostEl, true), scaleX = ghostEl && ghostMatrix && ghostMatrix.a, scaleY = ghostEl && ghostMatrix && ghostMatrix.d, relativeScrollOffset = PositionGhostAbsolutely && ghostRelativeParent && getRelativeScrollOffset(ghostRelativeParent), dx = (touch.clientX - tapEvt.clientX + fallbackOffset.x) / (scaleX || 1) + (relativeScrollOffset ? relativeScrollOffset[0] - ghostRelativeParentInitialScroll[0] : 0) / (scaleX || 1), dy = (touch.clientY - tapEvt.clientY + fallbackOffset.y) / (scaleY || 1) + (relativeScrollOffset ? relativeScrollOffset[1] - ghostRelativeParentInitialScroll[1] : 0) / (scaleY || 1);
      if (!Sortable.active && !awaitingDragStarted) {
        if (fallbackTolerance && Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) < fallbackTolerance) {
          return;
        }
        this._onDragStart(evt, true);
      }
      if (ghostEl) {
        if (ghostMatrix) {
          ghostMatrix.e += dx - (lastDx || 0);
          ghostMatrix.f += dy - (lastDy || 0);
        } else {
          ghostMatrix = {
            a: 1,
            b: 0,
            c: 0,
            d: 1,
            e: dx,
            f: dy
          };
        }
        var cssMatrix = "matrix(".concat(ghostMatrix.a, ",").concat(ghostMatrix.b, ",").concat(ghostMatrix.c, ",").concat(ghostMatrix.d, ",").concat(ghostMatrix.e, ",").concat(ghostMatrix.f, ")");
        css(ghostEl, "webkitTransform", cssMatrix);
        css(ghostEl, "mozTransform", cssMatrix);
        css(ghostEl, "msTransform", cssMatrix);
        css(ghostEl, "transform", cssMatrix);
        lastDx = dx;
        lastDy = dy;
        touchEvt = touch;
      }
      evt.cancelable && evt.preventDefault();
    }
  },
  _appendGhost: function _appendGhost() {
    if (!ghostEl) {
      var container = this.options.fallbackOnBody ? document.body : rootEl, rect = getRect(dragEl, true, PositionGhostAbsolutely, true, container), options2 = this.options;
      if (PositionGhostAbsolutely) {
        ghostRelativeParent = container;
        while (css(ghostRelativeParent, "position") === "static" && css(ghostRelativeParent, "transform") === "none" && ghostRelativeParent !== document) {
          ghostRelativeParent = ghostRelativeParent.parentNode;
        }
        if (ghostRelativeParent !== document.body && ghostRelativeParent !== document.documentElement) {
          if (ghostRelativeParent === document) ghostRelativeParent = getWindowScrollingElement();
          rect.top += ghostRelativeParent.scrollTop;
          rect.left += ghostRelativeParent.scrollLeft;
        } else {
          ghostRelativeParent = getWindowScrollingElement();
        }
        ghostRelativeParentInitialScroll = getRelativeScrollOffset(ghostRelativeParent);
      }
      ghostEl = dragEl.cloneNode(true);
      toggleClass(ghostEl, options2.ghostClass, false);
      toggleClass(ghostEl, options2.fallbackClass, true);
      toggleClass(ghostEl, options2.dragClass, true);
      css(ghostEl, "transition", "");
      css(ghostEl, "transform", "");
      css(ghostEl, "box-sizing", "border-box");
      css(ghostEl, "margin", 0);
      css(ghostEl, "top", rect.top);
      css(ghostEl, "left", rect.left);
      css(ghostEl, "width", rect.width);
      css(ghostEl, "height", rect.height);
      css(ghostEl, "opacity", "0.8");
      css(ghostEl, "position", PositionGhostAbsolutely ? "absolute" : "fixed");
      css(ghostEl, "zIndex", "100000");
      css(ghostEl, "pointerEvents", "none");
      Sortable.ghost = ghostEl;
      container.appendChild(ghostEl);
      css(ghostEl, "transform-origin", tapDistanceLeft / parseInt(ghostEl.style.width) * 100 + "% " + tapDistanceTop / parseInt(ghostEl.style.height) * 100 + "%");
    }
  },
  _onDragStart: function _onDragStart(evt, fallback) {
    var _this = this;
    var dataTransfer = evt.dataTransfer;
    var options2 = _this.options;
    pluginEvent2("dragStart", this, {
      evt
    });
    if (Sortable.eventCanceled) {
      this._onDrop();
      return;
    }
    pluginEvent2("setupClone", this);
    if (!Sortable.eventCanceled) {
      cloneEl = clone(dragEl);
      cloneEl.removeAttribute("id");
      cloneEl.draggable = false;
      cloneEl.style["will-change"] = "";
      this._hideClone();
      toggleClass(cloneEl, this.options.chosenClass, false);
      Sortable.clone = cloneEl;
    }
    _this.cloneId = _nextTick(function() {
      pluginEvent2("clone", _this);
      if (Sortable.eventCanceled) return;
      if (!_this.options.removeCloneOnHide) {
        rootEl.insertBefore(cloneEl, dragEl);
      }
      _this._hideClone();
      _dispatchEvent({
        sortable: _this,
        name: "clone"
      });
    });
    !fallback && toggleClass(dragEl, options2.dragClass, true);
    if (fallback) {
      ignoreNextClick = true;
      _this._loopId = setInterval(_this._emulateDragOver, 50);
    } else {
      off(document, "mouseup", _this._onDrop);
      off(document, "touchend", _this._onDrop);
      off(document, "touchcancel", _this._onDrop);
      if (dataTransfer) {
        dataTransfer.effectAllowed = "move";
        options2.setData && options2.setData.call(_this, dataTransfer, dragEl);
      }
      on(document, "drop", _this);
      css(dragEl, "transform", "translateZ(0)");
    }
    awaitingDragStarted = true;
    _this._dragStartId = _nextTick(_this._dragStarted.bind(_this, fallback, evt));
    on(document, "selectstart", _this);
    moved = true;
    window.getSelection().removeAllRanges();
    if (Safari) {
      css(document.body, "user-select", "none");
    }
  },
  // Returns true - if no further action is needed (either inserted or another condition)
  _onDragOver: function _onDragOver(evt) {
    var el2 = this.el, target = evt.target, dragRect, targetRect, revert, options2 = this.options, group = options2.group, activeSortable = Sortable.active, isOwner = activeGroup === group, canSort = options2.sort, fromSortable = putSortable || activeSortable, vertical, _this = this, completedFired = false;
    if (_silent) return;
    function dragOverEvent(name, extra) {
      pluginEvent2(name, _this, _objectSpread2({
        evt,
        isOwner,
        axis: vertical ? "vertical" : "horizontal",
        revert,
        dragRect,
        targetRect,
        canSort,
        fromSortable,
        target,
        completed,
        onMove: function onMove(target2, after2) {
          return _onMove(rootEl, el2, dragEl, dragRect, target2, getRect(target2), evt, after2);
        },
        changed
      }, extra));
    }
    function capture() {
      dragOverEvent("dragOverAnimationCapture");
      _this.captureAnimationState();
      if (_this !== fromSortable) {
        fromSortable.captureAnimationState();
      }
    }
    function completed(insertion) {
      dragOverEvent("dragOverCompleted", {
        insertion
      });
      if (insertion) {
        if (isOwner) {
          activeSortable._hideClone();
        } else {
          activeSortable._showClone(_this);
        }
        if (_this !== fromSortable) {
          toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : activeSortable.options.ghostClass, false);
          toggleClass(dragEl, options2.ghostClass, true);
        }
        if (putSortable !== _this && _this !== Sortable.active) {
          putSortable = _this;
        } else if (_this === Sortable.active && putSortable) {
          putSortable = null;
        }
        if (fromSortable === _this) {
          _this._ignoreWhileAnimating = target;
        }
        _this.animateAll(function() {
          dragOverEvent("dragOverAnimationComplete");
          _this._ignoreWhileAnimating = null;
        });
        if (_this !== fromSortable) {
          fromSortable.animateAll();
          fromSortable._ignoreWhileAnimating = null;
        }
      }
      if (target === dragEl && !dragEl.animated || target === el2 && !target.animated) {
        lastTarget = null;
      }
      if (!options2.dragoverBubble && !evt.rootEl && target !== document) {
        dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
        !insertion && nearestEmptyInsertDetectEvent(evt);
      }
      !options2.dragoverBubble && evt.stopPropagation && evt.stopPropagation();
      return completedFired = true;
    }
    function changed() {
      newIndex = index(dragEl);
      newDraggableIndex = index(dragEl, options2.draggable);
      _dispatchEvent({
        sortable: _this,
        name: "change",
        toEl: el2,
        newIndex,
        newDraggableIndex,
        originalEvent: evt
      });
    }
    if (evt.preventDefault !== void 0) {
      evt.cancelable && evt.preventDefault();
    }
    target = closest(target, options2.draggable, el2, true);
    dragOverEvent("dragOver");
    if (Sortable.eventCanceled) return completedFired;
    if (dragEl.contains(evt.target) || target.animated && target.animatingX && target.animatingY || _this._ignoreWhileAnimating === target) {
      return completed(false);
    }
    ignoreNextClick = false;
    if (activeSortable && !options2.disabled && (isOwner ? canSort || (revert = parentEl !== rootEl) : putSortable === this || (this.lastPutMode = activeGroup.checkPull(this, activeSortable, dragEl, evt)) && group.checkPut(this, activeSortable, dragEl, evt))) {
      vertical = this._getDirection(evt, target) === "vertical";
      dragRect = getRect(dragEl);
      dragOverEvent("dragOverValid");
      if (Sortable.eventCanceled) return completedFired;
      if (revert) {
        parentEl = rootEl;
        capture();
        this._hideClone();
        dragOverEvent("revert");
        if (!Sortable.eventCanceled) {
          if (nextEl) {
            rootEl.insertBefore(dragEl, nextEl);
          } else {
            rootEl.appendChild(dragEl);
          }
        }
        return completed(true);
      }
      var elLastChild = lastChild(el2, options2.draggable);
      if (!elLastChild || _ghostIsLast(evt, vertical, this) && !elLastChild.animated) {
        if (elLastChild === dragEl) {
          return completed(false);
        }
        if (elLastChild && el2 === evt.target) {
          target = elLastChild;
        }
        if (target) {
          targetRect = getRect(target);
        }
        if (_onMove(rootEl, el2, dragEl, dragRect, target, targetRect, evt, !!target) !== false) {
          capture();
          if (elLastChild && elLastChild.nextSibling) {
            el2.insertBefore(dragEl, elLastChild.nextSibling);
          } else {
            el2.appendChild(dragEl);
          }
          parentEl = el2;
          changed();
          return completed(true);
        }
      } else if (elLastChild && _ghostIsFirst(evt, vertical, this)) {
        var firstChild = getChild(el2, 0, options2, true);
        if (firstChild === dragEl) {
          return completed(false);
        }
        target = firstChild;
        targetRect = getRect(target);
        if (_onMove(rootEl, el2, dragEl, dragRect, target, targetRect, evt, false) !== false) {
          capture();
          el2.insertBefore(dragEl, firstChild);
          parentEl = el2;
          changed();
          return completed(true);
        }
      } else if (target.parentNode === el2) {
        targetRect = getRect(target);
        var direction = 0, targetBeforeFirstSwap, differentLevel = dragEl.parentNode !== el2, differentRowCol = !_dragElInRowColumn(dragEl.animated && dragEl.toRect || dragRect, target.animated && target.toRect || targetRect, vertical), side1 = vertical ? "top" : "left", scrolledPastTop = isScrolledPast(target, "top", "top") || isScrolledPast(dragEl, "top", "top"), scrollBefore = scrolledPastTop ? scrolledPastTop.scrollTop : void 0;
        if (lastTarget !== target) {
          targetBeforeFirstSwap = targetRect[side1];
          pastFirstInvertThresh = false;
          isCircumstantialInvert = !differentRowCol && options2.invertSwap || differentLevel;
        }
        direction = _getSwapDirection(evt, target, targetRect, vertical, differentRowCol ? 1 : options2.swapThreshold, options2.invertedSwapThreshold == null ? options2.swapThreshold : options2.invertedSwapThreshold, isCircumstantialInvert, lastTarget === target);
        var sibling;
        if (direction !== 0) {
          var dragIndex = index(dragEl);
          do {
            dragIndex -= direction;
            sibling = parentEl.children[dragIndex];
          } while (sibling && (css(sibling, "display") === "none" || sibling === ghostEl));
        }
        if (direction === 0 || sibling === target) {
          return completed(false);
        }
        lastTarget = target;
        lastDirection = direction;
        var nextSibling = target.nextElementSibling, after = false;
        after = direction === 1;
        var moveVector = _onMove(rootEl, el2, dragEl, dragRect, target, targetRect, evt, after);
        if (moveVector !== false) {
          if (moveVector === 1 || moveVector === -1) {
            after = moveVector === 1;
          }
          _silent = true;
          setTimeout(_unsilent, 30);
          capture();
          if (after && !nextSibling) {
            el2.appendChild(dragEl);
          } else {
            target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
          }
          if (scrolledPastTop) {
            scrollBy(scrolledPastTop, 0, scrollBefore - scrolledPastTop.scrollTop);
          }
          parentEl = dragEl.parentNode;
          if (targetBeforeFirstSwap !== void 0 && !isCircumstantialInvert) {
            targetMoveDistance = Math.abs(targetBeforeFirstSwap - getRect(target)[side1]);
          }
          changed();
          return completed(true);
        }
      }
      if (el2.contains(dragEl)) {
        return completed(false);
      }
    }
    return false;
  },
  _ignoreWhileAnimating: null,
  _offMoveEvents: function _offMoveEvents() {
    off(document, "mousemove", this._onTouchMove);
    off(document, "touchmove", this._onTouchMove);
    off(document, "pointermove", this._onTouchMove);
    off(document, "dragover", nearestEmptyInsertDetectEvent);
    off(document, "mousemove", nearestEmptyInsertDetectEvent);
    off(document, "touchmove", nearestEmptyInsertDetectEvent);
  },
  _offUpEvents: function _offUpEvents() {
    var ownerDocument = this.el.ownerDocument;
    off(ownerDocument, "mouseup", this._onDrop);
    off(ownerDocument, "touchend", this._onDrop);
    off(ownerDocument, "pointerup", this._onDrop);
    off(ownerDocument, "pointercancel", this._onDrop);
    off(ownerDocument, "touchcancel", this._onDrop);
    off(document, "selectstart", this);
  },
  _onDrop: function _onDrop(evt) {
    var el2 = this.el, options2 = this.options;
    newIndex = index(dragEl);
    newDraggableIndex = index(dragEl, options2.draggable);
    pluginEvent2("drop", this, {
      evt
    });
    parentEl = dragEl && dragEl.parentNode;
    newIndex = index(dragEl);
    newDraggableIndex = index(dragEl, options2.draggable);
    if (Sortable.eventCanceled) {
      this._nulling();
      return;
    }
    awaitingDragStarted = false;
    isCircumstantialInvert = false;
    pastFirstInvertThresh = false;
    clearInterval(this._loopId);
    clearTimeout(this._dragStartTimer);
    _cancelNextTick(this.cloneId);
    _cancelNextTick(this._dragStartId);
    if (this.nativeDraggable) {
      off(document, "drop", this);
      off(el2, "dragstart", this._onDragStart);
    }
    this._offMoveEvents();
    this._offUpEvents();
    if (Safari) {
      css(document.body, "user-select", "");
    }
    css(dragEl, "transform", "");
    if (evt) {
      if (moved) {
        evt.cancelable && evt.preventDefault();
        !options2.dropBubble && evt.stopPropagation();
      }
      ghostEl && ghostEl.parentNode && ghostEl.parentNode.removeChild(ghostEl);
      if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== "clone") {
        cloneEl && cloneEl.parentNode && cloneEl.parentNode.removeChild(cloneEl);
      }
      if (dragEl) {
        if (this.nativeDraggable) {
          off(dragEl, "dragend", this);
        }
        _disableDraggable(dragEl);
        dragEl.style["will-change"] = "";
        if (moved && !awaitingDragStarted) {
          toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : this.options.ghostClass, false);
        }
        toggleClass(dragEl, this.options.chosenClass, false);
        _dispatchEvent({
          sortable: this,
          name: "unchoose",
          toEl: parentEl,
          newIndex: null,
          newDraggableIndex: null,
          originalEvent: evt
        });
        if (rootEl !== parentEl) {
          if (newIndex >= 0) {
            _dispatchEvent({
              rootEl: parentEl,
              name: "add",
              toEl: parentEl,
              fromEl: rootEl,
              originalEvent: evt
            });
            _dispatchEvent({
              sortable: this,
              name: "remove",
              toEl: parentEl,
              originalEvent: evt
            });
            _dispatchEvent({
              rootEl: parentEl,
              name: "sort",
              toEl: parentEl,
              fromEl: rootEl,
              originalEvent: evt
            });
            _dispatchEvent({
              sortable: this,
              name: "sort",
              toEl: parentEl,
              originalEvent: evt
            });
          }
          putSortable && putSortable.save();
        } else {
          if (newIndex !== oldIndex) {
            if (newIndex >= 0) {
              _dispatchEvent({
                sortable: this,
                name: "update",
                toEl: parentEl,
                originalEvent: evt
              });
              _dispatchEvent({
                sortable: this,
                name: "sort",
                toEl: parentEl,
                originalEvent: evt
              });
            }
          }
        }
        if (Sortable.active) {
          if (newIndex == null || newIndex === -1) {
            newIndex = oldIndex;
            newDraggableIndex = oldDraggableIndex;
          }
          _dispatchEvent({
            sortable: this,
            name: "end",
            toEl: parentEl,
            originalEvent: evt
          });
          this.save();
        }
      }
    }
    this._nulling();
  },
  _nulling: function _nulling() {
    pluginEvent2("nulling", this);
    rootEl = dragEl = parentEl = ghostEl = nextEl = cloneEl = lastDownEl = cloneHidden = tapEvt = touchEvt = moved = newIndex = newDraggableIndex = oldIndex = oldDraggableIndex = lastTarget = lastDirection = putSortable = activeGroup = Sortable.dragged = Sortable.ghost = Sortable.clone = Sortable.active = null;
    savedInputChecked.forEach(function(el2) {
      el2.checked = true;
    });
    savedInputChecked.length = lastDx = lastDy = 0;
  },
  handleEvent: function handleEvent(evt) {
    switch (evt.type) {
      case "drop":
      case "dragend":
        this._onDrop(evt);
        break;
      case "dragenter":
      case "dragover":
        if (dragEl) {
          this._onDragOver(evt);
          _globalDragOver(evt);
        }
        break;
      case "selectstart":
        evt.preventDefault();
        break;
    }
  },
  /**
   * Serializes the item into an array of string.
   * @returns {String[]}
   */
  toArray: function toArray() {
    var order = [], el2, children = this.el.children, i = 0, n = children.length, options2 = this.options;
    for (; i < n; i++) {
      el2 = children[i];
      if (closest(el2, options2.draggable, this.el, false)) {
        order.push(el2.getAttribute(options2.dataIdAttr) || _generateId(el2));
      }
    }
    return order;
  },
  /**
   * Sorts the elements according to the array.
   * @param  {String[]}  order  order of the items
   */
  sort: function sort(order, useAnimation) {
    var items = {}, rootEl2 = this.el;
    this.toArray().forEach(function(id, i) {
      var el2 = rootEl2.children[i];
      if (closest(el2, this.options.draggable, rootEl2, false)) {
        items[id] = el2;
      }
    }, this);
    useAnimation && this.captureAnimationState();
    order.forEach(function(id) {
      if (items[id]) {
        rootEl2.removeChild(items[id]);
        rootEl2.appendChild(items[id]);
      }
    });
    useAnimation && this.animateAll();
  },
  /**
   * Save the current sorting
   */
  save: function save() {
    var store = this.options.store;
    store && store.set && store.set(this);
  },
  /**
   * For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
   * @param   {HTMLElement}  el
   * @param   {String}       [selector]  default: `options.draggable`
   * @returns {HTMLElement|null}
   */
  closest: function closest$1(el2, selector) {
    return closest(el2, selector || this.options.draggable, this.el, false);
  },
  /**
   * Set/get option
   * @param   {string} name
   * @param   {*}      [value]
   * @returns {*}
   */
  option: function option(name, value) {
    var options2 = this.options;
    if (value === void 0) {
      return options2[name];
    } else {
      var modifiedValue = PluginManager.modifyOption(this, name, value);
      if (typeof modifiedValue !== "undefined") {
        options2[name] = modifiedValue;
      } else {
        options2[name] = value;
      }
      if (name === "group") {
        _prepareGroup(options2);
      }
    }
  },
  /**
   * Destroy
   */
  destroy: function destroy() {
    pluginEvent2("destroy", this);
    var el2 = this.el;
    el2[expando] = null;
    off(el2, "mousedown", this._onTapStart);
    off(el2, "touchstart", this._onTapStart);
    off(el2, "pointerdown", this._onTapStart);
    if (this.nativeDraggable) {
      off(el2, "dragover", this);
      off(el2, "dragenter", this);
    }
    Array.prototype.forEach.call(el2.querySelectorAll("[draggable]"), function(el3) {
      el3.removeAttribute("draggable");
    });
    this._onDrop();
    this._disableDelayedDragEvents();
    sortables.splice(sortables.indexOf(this.el), 1);
    this.el = el2 = null;
  },
  _hideClone: function _hideClone() {
    if (!cloneHidden) {
      pluginEvent2("hideClone", this);
      if (Sortable.eventCanceled) return;
      css(cloneEl, "display", "none");
      if (this.options.removeCloneOnHide && cloneEl.parentNode) {
        cloneEl.parentNode.removeChild(cloneEl);
      }
      cloneHidden = true;
    }
  },
  _showClone: function _showClone(putSortable2) {
    if (putSortable2.lastPutMode !== "clone") {
      this._hideClone();
      return;
    }
    if (cloneHidden) {
      pluginEvent2("showClone", this);
      if (Sortable.eventCanceled) return;
      if (dragEl.parentNode == rootEl && !this.options.group.revertClone) {
        rootEl.insertBefore(cloneEl, dragEl);
      } else if (nextEl) {
        rootEl.insertBefore(cloneEl, nextEl);
      } else {
        rootEl.appendChild(cloneEl);
      }
      if (this.options.group.revertClone) {
        this.animate(dragEl, cloneEl);
      }
      css(cloneEl, "display", "");
      cloneHidden = false;
    }
  }
};
function _globalDragOver(evt) {
  if (evt.dataTransfer) {
    evt.dataTransfer.dropEffect = "move";
  }
  evt.cancelable && evt.preventDefault();
}
function _onMove(fromEl, toEl, dragEl2, dragRect, targetEl, targetRect, originalEvent, willInsertAfter) {
  var evt, sortable = fromEl[expando], onMoveFn = sortable.options.onMove, retVal;
  if (window.CustomEvent && !IE11OrLess && !Edge) {
    evt = new CustomEvent("move", {
      bubbles: true,
      cancelable: true
    });
  } else {
    evt = document.createEvent("Event");
    evt.initEvent("move", true, true);
  }
  evt.to = toEl;
  evt.from = fromEl;
  evt.dragged = dragEl2;
  evt.draggedRect = dragRect;
  evt.related = targetEl || toEl;
  evt.relatedRect = targetRect || getRect(toEl);
  evt.willInsertAfter = willInsertAfter;
  evt.originalEvent = originalEvent;
  fromEl.dispatchEvent(evt);
  if (onMoveFn) {
    retVal = onMoveFn.call(sortable, evt, originalEvent);
  }
  return retVal;
}
function _disableDraggable(el2) {
  el2.draggable = false;
}
function _unsilent() {
  _silent = false;
}
function _ghostIsFirst(evt, vertical, sortable) {
  var firstElRect = getRect(getChild(sortable.el, 0, sortable.options, true));
  var childContainingRect = getChildContainingRectFromElement(sortable.el, sortable.options, ghostEl);
  var spacer = 10;
  return vertical ? evt.clientX < childContainingRect.left - spacer || evt.clientY < firstElRect.top && evt.clientX < firstElRect.right : evt.clientY < childContainingRect.top - spacer || evt.clientY < firstElRect.bottom && evt.clientX < firstElRect.left;
}
function _ghostIsLast(evt, vertical, sortable) {
  var lastElRect = getRect(lastChild(sortable.el, sortable.options.draggable));
  var childContainingRect = getChildContainingRectFromElement(sortable.el, sortable.options, ghostEl);
  var spacer = 10;
  return vertical ? evt.clientX > childContainingRect.right + spacer || evt.clientY > lastElRect.bottom && evt.clientX > lastElRect.left : evt.clientY > childContainingRect.bottom + spacer || evt.clientX > lastElRect.right && evt.clientY > lastElRect.top;
}
function _getSwapDirection(evt, target, targetRect, vertical, swapThreshold, invertedSwapThreshold, invertSwap, isLastTarget) {
  var mouseOnAxis = vertical ? evt.clientY : evt.clientX, targetLength = vertical ? targetRect.height : targetRect.width, targetS1 = vertical ? targetRect.top : targetRect.left, targetS2 = vertical ? targetRect.bottom : targetRect.right, invert = false;
  if (!invertSwap) {
    if (isLastTarget && targetMoveDistance < targetLength * swapThreshold) {
      if (!pastFirstInvertThresh && (lastDirection === 1 ? mouseOnAxis > targetS1 + targetLength * invertedSwapThreshold / 2 : mouseOnAxis < targetS2 - targetLength * invertedSwapThreshold / 2)) {
        pastFirstInvertThresh = true;
      }
      if (!pastFirstInvertThresh) {
        if (lastDirection === 1 ? mouseOnAxis < targetS1 + targetMoveDistance : mouseOnAxis > targetS2 - targetMoveDistance) {
          return -lastDirection;
        }
      } else {
        invert = true;
      }
    } else {
      if (mouseOnAxis > targetS1 + targetLength * (1 - swapThreshold) / 2 && mouseOnAxis < targetS2 - targetLength * (1 - swapThreshold) / 2) {
        return _getInsertDirection(target);
      }
    }
  }
  invert = invert || invertSwap;
  if (invert) {
    if (mouseOnAxis < targetS1 + targetLength * invertedSwapThreshold / 2 || mouseOnAxis > targetS2 - targetLength * invertedSwapThreshold / 2) {
      return mouseOnAxis > targetS1 + targetLength / 2 ? 1 : -1;
    }
  }
  return 0;
}
function _getInsertDirection(target) {
  if (index(dragEl) < index(target)) {
    return 1;
  } else {
    return -1;
  }
}
function _generateId(el2) {
  var str = el2.tagName + el2.className + el2.src + el2.href + el2.textContent, i = str.length, sum = 0;
  while (i--) {
    sum += str.charCodeAt(i);
  }
  return sum.toString(36);
}
function _saveInputCheckedState(root) {
  savedInputChecked.length = 0;
  var inputs = root.getElementsByTagName("input");
  var idx = inputs.length;
  while (idx--) {
    var el2 = inputs[idx];
    el2.checked && savedInputChecked.push(el2);
  }
}
function _nextTick(fn) {
  return setTimeout(fn, 0);
}
function _cancelNextTick(id) {
  return clearTimeout(id);
}
if (documentExists) {
  on(document, "touchmove", function(evt) {
    if ((Sortable.active || awaitingDragStarted) && evt.cancelable) {
      evt.preventDefault();
    }
  });
}
Sortable.utils = {
  on,
  off,
  css,
  find,
  is: function is(el2, selector) {
    return !!closest(el2, selector, el2, false);
  },
  extend,
  throttle,
  closest,
  toggleClass,
  clone,
  index,
  nextTick: _nextTick,
  cancelNextTick: _cancelNextTick,
  detectDirection: _detectDirection,
  getChild,
  expando
};
Sortable.get = function(element) {
  return element[expando];
};
Sortable.mount = function() {
  for (var _len = arguments.length, plugins2 = new Array(_len), _key = 0; _key < _len; _key++) {
    plugins2[_key] = arguments[_key];
  }
  if (plugins2[0].constructor === Array) plugins2 = plugins2[0];
  plugins2.forEach(function(plugin) {
    if (!plugin.prototype || !plugin.prototype.constructor) {
      throw "Sortable: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
    }
    if (plugin.utils) Sortable.utils = _objectSpread2(_objectSpread2({}, Sortable.utils), plugin.utils);
    PluginManager.mount(plugin);
  });
};
Sortable.create = function(el2, options2) {
  return new Sortable(el2, options2);
};
Sortable.version = version;
var autoScrolls = [];
var scrollEl;
var scrollRootEl;
var scrolling = false;
var lastAutoScrollX;
var lastAutoScrollY;
var touchEvt$1;
var pointerElemChangedInterval;
function AutoScrollPlugin() {
  function AutoScroll() {
    this.defaults = {
      scroll: true,
      forceAutoScrollFallback: false,
      scrollSensitivity: 30,
      scrollSpeed: 10,
      bubbleScroll: true
    };
    for (var fn in this) {
      if (fn.charAt(0) === "_" && typeof this[fn] === "function") {
        this[fn] = this[fn].bind(this);
      }
    }
  }
  AutoScroll.prototype = {
    dragStarted: function dragStarted2(_ref) {
      var originalEvent = _ref.originalEvent;
      if (this.sortable.nativeDraggable) {
        on(document, "dragover", this._handleAutoScroll);
      } else {
        if (this.options.supportPointer) {
          on(document, "pointermove", this._handleFallbackAutoScroll);
        } else if (originalEvent.touches) {
          on(document, "touchmove", this._handleFallbackAutoScroll);
        } else {
          on(document, "mousemove", this._handleFallbackAutoScroll);
        }
      }
    },
    dragOverCompleted: function dragOverCompleted(_ref2) {
      var originalEvent = _ref2.originalEvent;
      if (!this.options.dragOverBubble && !originalEvent.rootEl) {
        this._handleAutoScroll(originalEvent);
      }
    },
    drop: function drop4() {
      if (this.sortable.nativeDraggable) {
        off(document, "dragover", this._handleAutoScroll);
      } else {
        off(document, "pointermove", this._handleFallbackAutoScroll);
        off(document, "touchmove", this._handleFallbackAutoScroll);
        off(document, "mousemove", this._handleFallbackAutoScroll);
      }
      clearPointerElemChangedInterval();
      clearAutoScrolls();
      cancelThrottle();
    },
    nulling: function nulling() {
      touchEvt$1 = scrollRootEl = scrollEl = scrolling = pointerElemChangedInterval = lastAutoScrollX = lastAutoScrollY = null;
      autoScrolls.length = 0;
    },
    _handleFallbackAutoScroll: function _handleFallbackAutoScroll(evt) {
      this._handleAutoScroll(evt, true);
    },
    _handleAutoScroll: function _handleAutoScroll(evt, fallback) {
      var _this = this;
      var x = (evt.touches ? evt.touches[0] : evt).clientX, y = (evt.touches ? evt.touches[0] : evt).clientY, elem2 = document.elementFromPoint(x, y);
      touchEvt$1 = evt;
      if (fallback || this.options.forceAutoScrollFallback || Edge || IE11OrLess || Safari) {
        autoScroll(evt, this.options, elem2, fallback);
        var ogElemScroller = getParentAutoScrollElement(elem2, true);
        if (scrolling && (!pointerElemChangedInterval || x !== lastAutoScrollX || y !== lastAutoScrollY)) {
          pointerElemChangedInterval && clearPointerElemChangedInterval();
          pointerElemChangedInterval = setInterval(function() {
            var newElem = getParentAutoScrollElement(document.elementFromPoint(x, y), true);
            if (newElem !== ogElemScroller) {
              ogElemScroller = newElem;
              clearAutoScrolls();
            }
            autoScroll(evt, _this.options, newElem, fallback);
          }, 10);
          lastAutoScrollX = x;
          lastAutoScrollY = y;
        }
      } else {
        if (!this.options.bubbleScroll || getParentAutoScrollElement(elem2, true) === getWindowScrollingElement()) {
          clearAutoScrolls();
          return;
        }
        autoScroll(evt, this.options, getParentAutoScrollElement(elem2, false), false);
      }
    }
  };
  return _extends(AutoScroll, {
    pluginName: "scroll",
    initializeByDefault: true
  });
}
function clearAutoScrolls() {
  autoScrolls.forEach(function(autoScroll2) {
    clearInterval(autoScroll2.pid);
  });
  autoScrolls = [];
}
function clearPointerElemChangedInterval() {
  clearInterval(pointerElemChangedInterval);
}
var autoScroll = throttle(function(evt, options2, rootEl2, isFallback) {
  if (!options2.scroll) return;
  var x = (evt.touches ? evt.touches[0] : evt).clientX, y = (evt.touches ? evt.touches[0] : evt).clientY, sens = options2.scrollSensitivity, speed = options2.scrollSpeed, winScroller = getWindowScrollingElement();
  var scrollThisInstance = false, scrollCustomFn;
  if (scrollRootEl !== rootEl2) {
    scrollRootEl = rootEl2;
    clearAutoScrolls();
    scrollEl = options2.scroll;
    scrollCustomFn = options2.scrollFn;
    if (scrollEl === true) {
      scrollEl = getParentAutoScrollElement(rootEl2, true);
    }
  }
  var layersOut = 0;
  var currentParent = scrollEl;
  do {
    var el2 = currentParent, rect = getRect(el2), top = rect.top, bottom = rect.bottom, left = rect.left, right = rect.right, width = rect.width, height = rect.height, canScrollX = void 0, canScrollY = void 0, scrollWidth = el2.scrollWidth, scrollHeight = el2.scrollHeight, elCSS = css(el2), scrollPosX = el2.scrollLeft, scrollPosY = el2.scrollTop;
    if (el2 === winScroller) {
      canScrollX = width < scrollWidth && (elCSS.overflowX === "auto" || elCSS.overflowX === "scroll" || elCSS.overflowX === "visible");
      canScrollY = height < scrollHeight && (elCSS.overflowY === "auto" || elCSS.overflowY === "scroll" || elCSS.overflowY === "visible");
    } else {
      canScrollX = width < scrollWidth && (elCSS.overflowX === "auto" || elCSS.overflowX === "scroll");
      canScrollY = height < scrollHeight && (elCSS.overflowY === "auto" || elCSS.overflowY === "scroll");
    }
    var vx = canScrollX && (Math.abs(right - x) <= sens && scrollPosX + width < scrollWidth) - (Math.abs(left - x) <= sens && !!scrollPosX);
    var vy = canScrollY && (Math.abs(bottom - y) <= sens && scrollPosY + height < scrollHeight) - (Math.abs(top - y) <= sens && !!scrollPosY);
    if (!autoScrolls[layersOut]) {
      for (var i = 0; i <= layersOut; i++) {
        if (!autoScrolls[i]) {
          autoScrolls[i] = {};
        }
      }
    }
    if (autoScrolls[layersOut].vx != vx || autoScrolls[layersOut].vy != vy || autoScrolls[layersOut].el !== el2) {
      autoScrolls[layersOut].el = el2;
      autoScrolls[layersOut].vx = vx;
      autoScrolls[layersOut].vy = vy;
      clearInterval(autoScrolls[layersOut].pid);
      if (vx != 0 || vy != 0) {
        scrollThisInstance = true;
        autoScrolls[layersOut].pid = setInterval(function() {
          if (isFallback && this.layer === 0) {
            Sortable.active._onTouchMove(touchEvt$1);
          }
          var scrollOffsetY = autoScrolls[this.layer].vy ? autoScrolls[this.layer].vy * speed : 0;
          var scrollOffsetX = autoScrolls[this.layer].vx ? autoScrolls[this.layer].vx * speed : 0;
          if (typeof scrollCustomFn === "function") {
            if (scrollCustomFn.call(Sortable.dragged.parentNode[expando], scrollOffsetX, scrollOffsetY, evt, touchEvt$1, autoScrolls[this.layer].el) !== "continue") {
              return;
            }
          }
          scrollBy(autoScrolls[this.layer].el, scrollOffsetX, scrollOffsetY);
        }.bind({
          layer: layersOut
        }), 24);
      }
    }
    layersOut++;
  } while (options2.bubbleScroll && currentParent !== winScroller && (currentParent = getParentAutoScrollElement(currentParent, false)));
  scrolling = scrollThisInstance;
}, 30);
var drop = function drop2(_ref) {
  var originalEvent = _ref.originalEvent, putSortable2 = _ref.putSortable, dragEl2 = _ref.dragEl, activeSortable = _ref.activeSortable, dispatchSortableEvent = _ref.dispatchSortableEvent, hideGhostForTarget = _ref.hideGhostForTarget, unhideGhostForTarget = _ref.unhideGhostForTarget;
  if (!originalEvent) return;
  var toSortable = putSortable2 || activeSortable;
  hideGhostForTarget();
  var touch = originalEvent.changedTouches && originalEvent.changedTouches.length ? originalEvent.changedTouches[0] : originalEvent;
  var target = document.elementFromPoint(touch.clientX, touch.clientY);
  unhideGhostForTarget();
  if (toSortable && !toSortable.el.contains(target)) {
    dispatchSortableEvent("spill");
    this.onSpill({
      dragEl: dragEl2,
      putSortable: putSortable2
    });
  }
};
function Revert() {
}
Revert.prototype = {
  startIndex: null,
  dragStart: function dragStart(_ref2) {
    var oldDraggableIndex2 = _ref2.oldDraggableIndex;
    this.startIndex = oldDraggableIndex2;
  },
  onSpill: function onSpill(_ref3) {
    var dragEl2 = _ref3.dragEl, putSortable2 = _ref3.putSortable;
    this.sortable.captureAnimationState();
    if (putSortable2) {
      putSortable2.captureAnimationState();
    }
    var nextSibling = getChild(this.sortable.el, this.startIndex, this.options);
    if (nextSibling) {
      this.sortable.el.insertBefore(dragEl2, nextSibling);
    } else {
      this.sortable.el.appendChild(dragEl2);
    }
    this.sortable.animateAll();
    if (putSortable2) {
      putSortable2.animateAll();
    }
  },
  drop
};
_extends(Revert, {
  pluginName: "revertOnSpill"
});
function Remove() {
}
Remove.prototype = {
  onSpill: function onSpill2(_ref4) {
    var dragEl2 = _ref4.dragEl, putSortable2 = _ref4.putSortable;
    var parentSortable = putSortable2 || this.sortable;
    parentSortable.captureAnimationState();
    dragEl2.parentNode && dragEl2.parentNode.removeChild(dragEl2);
    parentSortable.animateAll();
  },
  drop
};
_extends(Remove, {
  pluginName: "removeOnSpill"
});
var multiDragElements = [];
var multiDragClones = [];
var lastMultiDragSelect;
var multiDragSortable;
var initialFolding = false;
var folding = false;
var dragStarted = false;
var dragEl$1;
var clonesFromRect;
var clonesHidden;
function MultiDragPlugin() {
  function MultiDrag(sortable) {
    for (var fn in this) {
      if (fn.charAt(0) === "_" && typeof this[fn] === "function") {
        this[fn] = this[fn].bind(this);
      }
    }
    if (!sortable.options.avoidImplicitDeselect) {
      if (sortable.options.supportPointer) {
        on(document, "pointerup", this._deselectMultiDrag);
      } else {
        on(document, "mouseup", this._deselectMultiDrag);
        on(document, "touchend", this._deselectMultiDrag);
      }
    }
    on(document, "keydown", this._checkKeyDown);
    on(document, "keyup", this._checkKeyUp);
    this.defaults = {
      selectedClass: "sortable-selected",
      multiDragKey: null,
      avoidImplicitDeselect: false,
      setData: function setData(dataTransfer, dragEl2) {
        var data = "";
        if (multiDragElements.length && multiDragSortable === sortable) {
          multiDragElements.forEach(function(multiDragElement, i) {
            data += (!i ? "" : ", ") + multiDragElement.textContent;
          });
        } else {
          data = dragEl2.textContent;
        }
        dataTransfer.setData("Text", data);
      }
    };
  }
  MultiDrag.prototype = {
    multiDragKeyDown: false,
    isMultiDrag: false,
    delayStartGlobal: function delayStartGlobal(_ref) {
      var dragged = _ref.dragEl;
      dragEl$1 = dragged;
    },
    delayEnded: function delayEnded() {
      this.isMultiDrag = ~multiDragElements.indexOf(dragEl$1);
    },
    setupClone: function setupClone(_ref2) {
      var sortable = _ref2.sortable, cancel = _ref2.cancel;
      if (!this.isMultiDrag) return;
      for (var i = 0; i < multiDragElements.length; i++) {
        multiDragClones.push(clone(multiDragElements[i]));
        multiDragClones[i].sortableIndex = multiDragElements[i].sortableIndex;
        multiDragClones[i].draggable = false;
        multiDragClones[i].style["will-change"] = "";
        toggleClass(multiDragClones[i], this.options.selectedClass, false);
        multiDragElements[i] === dragEl$1 && toggleClass(multiDragClones[i], this.options.chosenClass, false);
      }
      sortable._hideClone();
      cancel();
    },
    clone: function clone2(_ref3) {
      var sortable = _ref3.sortable, rootEl2 = _ref3.rootEl, dispatchSortableEvent = _ref3.dispatchSortableEvent, cancel = _ref3.cancel;
      if (!this.isMultiDrag) return;
      if (!this.options.removeCloneOnHide) {
        if (multiDragElements.length && multiDragSortable === sortable) {
          insertMultiDragClones(true, rootEl2);
          dispatchSortableEvent("clone");
          cancel();
        }
      }
    },
    showClone: function showClone(_ref4) {
      var cloneNowShown = _ref4.cloneNowShown, rootEl2 = _ref4.rootEl, cancel = _ref4.cancel;
      if (!this.isMultiDrag) return;
      insertMultiDragClones(false, rootEl2);
      multiDragClones.forEach(function(clone2) {
        css(clone2, "display", "");
      });
      cloneNowShown();
      clonesHidden = false;
      cancel();
    },
    hideClone: function hideClone(_ref5) {
      var _this = this;
      var sortable = _ref5.sortable, cloneNowHidden = _ref5.cloneNowHidden, cancel = _ref5.cancel;
      if (!this.isMultiDrag) return;
      multiDragClones.forEach(function(clone2) {
        css(clone2, "display", "none");
        if (_this.options.removeCloneOnHide && clone2.parentNode) {
          clone2.parentNode.removeChild(clone2);
        }
      });
      cloneNowHidden();
      clonesHidden = true;
      cancel();
    },
    dragStartGlobal: function dragStartGlobal(_ref6) {
      var sortable = _ref6.sortable;
      if (!this.isMultiDrag && multiDragSortable) {
        multiDragSortable.multiDrag._deselectMultiDrag();
      }
      multiDragElements.forEach(function(multiDragElement) {
        multiDragElement.sortableIndex = index(multiDragElement);
      });
      multiDragElements = multiDragElements.sort(function(a, b) {
        return a.sortableIndex - b.sortableIndex;
      });
      dragStarted = true;
    },
    dragStarted: function dragStarted2(_ref7) {
      var _this2 = this;
      var sortable = _ref7.sortable;
      if (!this.isMultiDrag) return;
      if (this.options.sort) {
        sortable.captureAnimationState();
        if (this.options.animation) {
          multiDragElements.forEach(function(multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            css(multiDragElement, "position", "absolute");
          });
          var dragRect = getRect(dragEl$1, false, true, true);
          multiDragElements.forEach(function(multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            setRect(multiDragElement, dragRect);
          });
          folding = true;
          initialFolding = true;
        }
      }
      sortable.animateAll(function() {
        folding = false;
        initialFolding = false;
        if (_this2.options.animation) {
          multiDragElements.forEach(function(multiDragElement) {
            unsetRect(multiDragElement);
          });
        }
        if (_this2.options.sort) {
          removeMultiDragElements();
        }
      });
    },
    dragOver: function dragOver2(_ref8) {
      var target = _ref8.target, completed = _ref8.completed, cancel = _ref8.cancel;
      if (folding && ~multiDragElements.indexOf(target)) {
        completed(false);
        cancel();
      }
    },
    revert: function revert(_ref9) {
      var fromSortable = _ref9.fromSortable, rootEl2 = _ref9.rootEl, sortable = _ref9.sortable, dragRect = _ref9.dragRect;
      if (multiDragElements.length > 1) {
        multiDragElements.forEach(function(multiDragElement) {
          sortable.addAnimationState({
            target: multiDragElement,
            rect: folding ? getRect(multiDragElement) : dragRect
          });
          unsetRect(multiDragElement);
          multiDragElement.fromRect = dragRect;
          fromSortable.removeAnimationState(multiDragElement);
        });
        folding = false;
        insertMultiDragElements(!this.options.removeCloneOnHide, rootEl2);
      }
    },
    dragOverCompleted: function dragOverCompleted(_ref10) {
      var sortable = _ref10.sortable, isOwner = _ref10.isOwner, insertion = _ref10.insertion, activeSortable = _ref10.activeSortable, parentEl2 = _ref10.parentEl, putSortable2 = _ref10.putSortable;
      var options2 = this.options;
      if (insertion) {
        if (isOwner) {
          activeSortable._hideClone();
        }
        initialFolding = false;
        if (options2.animation && multiDragElements.length > 1 && (folding || !isOwner && !activeSortable.options.sort && !putSortable2)) {
          var dragRectAbsolute = getRect(dragEl$1, false, true, true);
          multiDragElements.forEach(function(multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            setRect(multiDragElement, dragRectAbsolute);
            parentEl2.appendChild(multiDragElement);
          });
          folding = true;
        }
        if (!isOwner) {
          if (!folding) {
            removeMultiDragElements();
          }
          if (multiDragElements.length > 1) {
            var clonesHiddenBefore = clonesHidden;
            activeSortable._showClone(sortable);
            if (activeSortable.options.animation && !clonesHidden && clonesHiddenBefore) {
              multiDragClones.forEach(function(clone2) {
                activeSortable.addAnimationState({
                  target: clone2,
                  rect: clonesFromRect
                });
                clone2.fromRect = clonesFromRect;
                clone2.thisAnimationDuration = null;
              });
            }
          } else {
            activeSortable._showClone(sortable);
          }
        }
      }
    },
    dragOverAnimationCapture: function dragOverAnimationCapture(_ref11) {
      var dragRect = _ref11.dragRect, isOwner = _ref11.isOwner, activeSortable = _ref11.activeSortable;
      multiDragElements.forEach(function(multiDragElement) {
        multiDragElement.thisAnimationDuration = null;
      });
      if (activeSortable.options.animation && !isOwner && activeSortable.multiDrag.isMultiDrag) {
        clonesFromRect = _extends({}, dragRect);
        var dragMatrix = matrix(dragEl$1, true);
        clonesFromRect.top -= dragMatrix.f;
        clonesFromRect.left -= dragMatrix.e;
      }
    },
    dragOverAnimationComplete: function dragOverAnimationComplete() {
      if (folding) {
        folding = false;
        removeMultiDragElements();
      }
    },
    drop: function drop4(_ref12) {
      var evt = _ref12.originalEvent, rootEl2 = _ref12.rootEl, parentEl2 = _ref12.parentEl, sortable = _ref12.sortable, dispatchSortableEvent = _ref12.dispatchSortableEvent, oldIndex2 = _ref12.oldIndex, putSortable2 = _ref12.putSortable;
      var toSortable = putSortable2 || this.sortable;
      if (!evt) return;
      var options2 = this.options, children = parentEl2.children;
      if (!dragStarted) {
        if (options2.multiDragKey && !this.multiDragKeyDown) {
          this._deselectMultiDrag();
        }
        toggleClass(dragEl$1, options2.selectedClass, !~multiDragElements.indexOf(dragEl$1));
        if (!~multiDragElements.indexOf(dragEl$1)) {
          multiDragElements.push(dragEl$1);
          dispatchEvent({
            sortable,
            rootEl: rootEl2,
            name: "select",
            targetEl: dragEl$1,
            originalEvent: evt
          });
          if (evt.shiftKey && lastMultiDragSelect && sortable.el.contains(lastMultiDragSelect)) {
            var lastIndex = index(lastMultiDragSelect), currentIndex = index(dragEl$1);
            if (~lastIndex && ~currentIndex && lastIndex !== currentIndex) {
              (function() {
                var n, i;
                if (currentIndex > lastIndex) {
                  i = lastIndex;
                  n = currentIndex;
                } else {
                  i = currentIndex;
                  n = lastIndex + 1;
                }
                var filter = options2.filter;
                for (; i < n; i++) {
                  if (~multiDragElements.indexOf(children[i])) continue;
                  if (!closest(children[i], options2.draggable, parentEl2, false)) continue;
                  var filtered = filter && (typeof filter === "function" ? filter.call(sortable, evt, children[i], sortable) : filter.split(",").some(function(criteria) {
                    return closest(children[i], criteria.trim(), parentEl2, false);
                  }));
                  if (filtered) continue;
                  toggleClass(children[i], options2.selectedClass, true);
                  multiDragElements.push(children[i]);
                  dispatchEvent({
                    sortable,
                    rootEl: rootEl2,
                    name: "select",
                    targetEl: children[i],
                    originalEvent: evt
                  });
                }
              })();
            }
          } else {
            lastMultiDragSelect = dragEl$1;
          }
          multiDragSortable = toSortable;
        } else {
          multiDragElements.splice(multiDragElements.indexOf(dragEl$1), 1);
          lastMultiDragSelect = null;
          dispatchEvent({
            sortable,
            rootEl: rootEl2,
            name: "deselect",
            targetEl: dragEl$1,
            originalEvent: evt
          });
        }
      }
      if (dragStarted && this.isMultiDrag) {
        folding = false;
        if ((parentEl2[expando].options.sort || parentEl2 !== rootEl2) && multiDragElements.length > 1) {
          var dragRect = getRect(dragEl$1), multiDragIndex = index(dragEl$1, ":not(." + this.options.selectedClass + ")");
          if (!initialFolding && options2.animation) dragEl$1.thisAnimationDuration = null;
          toSortable.captureAnimationState();
          if (!initialFolding) {
            if (options2.animation) {
              dragEl$1.fromRect = dragRect;
              multiDragElements.forEach(function(multiDragElement) {
                multiDragElement.thisAnimationDuration = null;
                if (multiDragElement !== dragEl$1) {
                  var rect = folding ? getRect(multiDragElement) : dragRect;
                  multiDragElement.fromRect = rect;
                  toSortable.addAnimationState({
                    target: multiDragElement,
                    rect
                  });
                }
              });
            }
            removeMultiDragElements();
            multiDragElements.forEach(function(multiDragElement) {
              if (children[multiDragIndex]) {
                parentEl2.insertBefore(multiDragElement, children[multiDragIndex]);
              } else {
                parentEl2.appendChild(multiDragElement);
              }
              multiDragIndex++;
            });
            if (oldIndex2 === index(dragEl$1)) {
              var update = false;
              multiDragElements.forEach(function(multiDragElement) {
                if (multiDragElement.sortableIndex !== index(multiDragElement)) {
                  update = true;
                  return;
                }
              });
              if (update) {
                dispatchSortableEvent("update");
                dispatchSortableEvent("sort");
              }
            }
          }
          multiDragElements.forEach(function(multiDragElement) {
            unsetRect(multiDragElement);
          });
          toSortable.animateAll();
        }
        multiDragSortable = toSortable;
      }
      if (rootEl2 === parentEl2 || putSortable2 && putSortable2.lastPutMode !== "clone") {
        multiDragClones.forEach(function(clone2) {
          clone2.parentNode && clone2.parentNode.removeChild(clone2);
        });
      }
    },
    nullingGlobal: function nullingGlobal() {
      this.isMultiDrag = dragStarted = false;
      multiDragClones.length = 0;
    },
    destroyGlobal: function destroyGlobal() {
      this._deselectMultiDrag();
      off(document, "pointerup", this._deselectMultiDrag);
      off(document, "mouseup", this._deselectMultiDrag);
      off(document, "touchend", this._deselectMultiDrag);
      off(document, "keydown", this._checkKeyDown);
      off(document, "keyup", this._checkKeyUp);
    },
    _deselectMultiDrag: function _deselectMultiDrag(evt) {
      if (typeof dragStarted !== "undefined" && dragStarted) return;
      if (multiDragSortable !== this.sortable) return;
      if (evt && closest(evt.target, this.options.draggable, this.sortable.el, false)) return;
      if (evt && evt.button !== 0) return;
      while (multiDragElements.length) {
        var el2 = multiDragElements[0];
        toggleClass(el2, this.options.selectedClass, false);
        multiDragElements.shift();
        dispatchEvent({
          sortable: this.sortable,
          rootEl: this.sortable.el,
          name: "deselect",
          targetEl: el2,
          originalEvent: evt
        });
      }
    },
    _checkKeyDown: function _checkKeyDown(evt) {
      if (evt.key === this.options.multiDragKey) {
        this.multiDragKeyDown = true;
      }
    },
    _checkKeyUp: function _checkKeyUp(evt) {
      if (evt.key === this.options.multiDragKey) {
        this.multiDragKeyDown = false;
      }
    }
  };
  return _extends(MultiDrag, {
    // Static methods & properties
    pluginName: "multiDrag",
    utils: {
      /**
       * Selects the provided multi-drag item
       * @param  {HTMLElement} el    The element to be selected
       */
      select: function select(el2) {
        var sortable = el2.parentNode[expando];
        if (!sortable || !sortable.options.multiDrag || ~multiDragElements.indexOf(el2)) return;
        if (multiDragSortable && multiDragSortable !== sortable) {
          multiDragSortable.multiDrag._deselectMultiDrag();
          multiDragSortable = sortable;
        }
        toggleClass(el2, sortable.options.selectedClass, true);
        multiDragElements.push(el2);
      },
      /**
       * Deselects the provided multi-drag item
       * @param  {HTMLElement} el    The element to be deselected
       */
      deselect: function deselect(el2) {
        var sortable = el2.parentNode[expando], index2 = multiDragElements.indexOf(el2);
        if (!sortable || !sortable.options.multiDrag || !~index2) return;
        toggleClass(el2, sortable.options.selectedClass, false);
        multiDragElements.splice(index2, 1);
      }
    },
    eventProperties: function eventProperties() {
      var _this3 = this;
      var oldIndicies = [], newIndicies = [];
      multiDragElements.forEach(function(multiDragElement) {
        oldIndicies.push({
          multiDragElement,
          index: multiDragElement.sortableIndex
        });
        var newIndex2;
        if (folding && multiDragElement !== dragEl$1) {
          newIndex2 = -1;
        } else if (folding) {
          newIndex2 = index(multiDragElement, ":not(." + _this3.options.selectedClass + ")");
        } else {
          newIndex2 = index(multiDragElement);
        }
        newIndicies.push({
          multiDragElement,
          index: newIndex2
        });
      });
      return {
        items: _toConsumableArray(multiDragElements),
        clones: [].concat(multiDragClones),
        oldIndicies,
        newIndicies
      };
    },
    optionListeners: {
      multiDragKey: function multiDragKey(key) {
        key = key.toLowerCase();
        if (key === "ctrl") {
          key = "Control";
        } else if (key.length > 1) {
          key = key.charAt(0).toUpperCase() + key.substr(1);
        }
        return key;
      }
    }
  });
}
function insertMultiDragElements(clonesInserted, rootEl2) {
  multiDragElements.forEach(function(multiDragElement, i) {
    var target = rootEl2.children[multiDragElement.sortableIndex + (clonesInserted ? Number(i) : 0)];
    if (target) {
      rootEl2.insertBefore(multiDragElement, target);
    } else {
      rootEl2.appendChild(multiDragElement);
    }
  });
}
function insertMultiDragClones(elementsInserted, rootEl2) {
  multiDragClones.forEach(function(clone2, i) {
    var target = rootEl2.children[clone2.sortableIndex + (elementsInserted ? Number(i) : 0)];
    if (target) {
      rootEl2.insertBefore(clone2, target);
    } else {
      rootEl2.appendChild(clone2);
    }
  });
}
function removeMultiDragElements() {
  multiDragElements.forEach(function(multiDragElement) {
    if (multiDragElement === dragEl$1) return;
    multiDragElement.parentNode && multiDragElement.parentNode.removeChild(multiDragElement);
  });
}
Sortable.mount(new AutoScrollPlugin());
Sortable.mount(Remove, Revert);
var sortable_esm_default = Sortable;

// resources/js/components/book-sort.js
var sortOperations = {
  name(a, b) {
    const aName = a.getAttribute("data-name").trim().toLowerCase();
    const bName = b.getAttribute("data-name").trim().toLowerCase();
    return aName.localeCompare(bName);
  },
  created(a, b) {
    const aTime = Number(a.getAttribute("data-created"));
    const bTime = Number(b.getAttribute("data-created"));
    return bTime - aTime;
  },
  updated(a, b) {
    const aTime = Number(a.getAttribute("data-updated"));
    const bTime = Number(b.getAttribute("data-updated"));
    return bTime - aTime;
  },
  chaptersFirst(a, b) {
    const aType = a.getAttribute("data-type");
    const bType = b.getAttribute("data-type");
    if (aType === bType) {
      return 0;
    }
    return aType === "chapter" ? -1 : 1;
  },
  chaptersLast(a, b) {
    const aType = a.getAttribute("data-type");
    const bType = b.getAttribute("data-type");
    if (aType === bType) {
      return 0;
    }
    return aType === "chapter" ? 1 : -1;
  }
};
var moveActions = {
  up: {
    active(elem2, parent) {
      return !(elem2.previousElementSibling === null && !parent);
    },
    run(elem2, parent) {
      const newSibling = elem2.previousElementSibling || parent;
      newSibling.insertAdjacentElement("beforebegin", elem2);
    }
  },
  down: {
    active(elem2, parent) {
      return !(elem2.nextElementSibling === null && !parent);
    },
    run(elem2, parent) {
      const newSibling = elem2.nextElementSibling || parent;
      newSibling.insertAdjacentElement("afterend", elem2);
    }
  },
  next_book: {
    active(elem2, parent, book) {
      return book.nextElementSibling !== null;
    },
    run(elem2, parent, book) {
      const newList = book.nextElementSibling.querySelector("ul");
      newList.prepend(elem2);
    }
  },
  prev_book: {
    active(elem2, parent, book) {
      return book.previousElementSibling !== null;
    },
    run(elem2, parent, book) {
      const newList = book.previousElementSibling.querySelector("ul");
      newList.appendChild(elem2);
    }
  },
  next_chapter: {
    active(elem2, parent) {
      return elem2.dataset.type === "page" && this.getNextChapter(elem2, parent);
    },
    run(elem2, parent) {
      const nextChapter = this.getNextChapter(elem2, parent);
      nextChapter.querySelector("ul").prepend(elem2);
    },
    getNextChapter(elem2, parent) {
      const topLevel = parent || elem2;
      const topItems = Array.from(topLevel.parentElement.children);
      const index2 = topItems.indexOf(topLevel);
      return topItems.slice(index2 + 1).find((item) => item.dataset.type === "chapter");
    }
  },
  prev_chapter: {
    active(elem2, parent) {
      return elem2.dataset.type === "page" && this.getPrevChapter(elem2, parent);
    },
    run(elem2, parent) {
      const prevChapter = this.getPrevChapter(elem2, parent);
      prevChapter.querySelector("ul").append(elem2);
    },
    getPrevChapter(elem2, parent) {
      const topLevel = parent || elem2;
      const topItems = Array.from(topLevel.parentElement.children);
      const index2 = topItems.indexOf(topLevel);
      return topItems.slice(0, index2).reverse().find((item) => item.dataset.type === "chapter");
    }
  },
  book_end: {
    active(elem2, parent) {
      return parent || parent === null && elem2.nextElementSibling;
    },
    run(elem2, parent, book) {
      book.querySelector("ul").append(elem2);
    }
  },
  book_start: {
    active(elem2, parent) {
      return parent || parent === null && elem2.previousElementSibling;
    },
    run(elem2, parent, book) {
      book.querySelector("ul").prepend(elem2);
    }
  },
  before_chapter: {
    active(elem2, parent) {
      return parent;
    },
    run(elem2, parent) {
      parent.insertAdjacentElement("beforebegin", elem2);
    }
  },
  after_chapter: {
    active(elem2, parent) {
      return parent;
    },
    run(elem2, parent) {
      parent.insertAdjacentElement("afterend", elem2);
    }
  }
};
var BookSort = class extends Component {
  setup() {
    this.container = this.$el;
    this.sortContainer = this.$refs.sortContainer;
    this.input = this.$refs.input;
    sortable_esm_default.mount(new MultiDragPlugin());
    const initialSortBox = this.container.querySelector(".sort-box");
    this.setupBookSortable(initialSortBox);
    this.setupSortPresets();
    this.setupMoveActions();
    window.$events.listen("entity-select-change", this.bookSelect.bind(this));
  }
  /**
   * Set up the handlers for the item-level move buttons.
   */
  setupMoveActions() {
    this.container.addEventListener("click", (event) => {
      if (event.target.matches("[data-move]")) {
        const action = event.target.getAttribute("data-move");
        const sortItem = event.target.closest("[data-id]");
        this.runSortAction(sortItem, action);
      }
    });
    this.updateMoveActionStateForAll();
  }
  /**
   * Set up the handlers for the preset sort type buttons.
   */
  setupSortPresets() {
    let lastSort = "";
    let reverse = false;
    const reversibleTypes = ["name", "created", "updated"];
    this.sortContainer.addEventListener("click", (event) => {
      const sortButton = event.target.closest(".sort-box-options [data-sort]");
      if (!sortButton) return;
      event.preventDefault();
      const sortLists = sortButton.closest(".sort-box").querySelectorAll("ul");
      const sort2 = sortButton.getAttribute("data-sort");
      reverse = lastSort === sort2 ? !reverse : false;
      let sortFunction = sortOperations[sort2];
      if (reverse && reversibleTypes.includes(sort2)) {
        sortFunction = function reverseSortOperation(a, b) {
          return 0 - sortOperations[sort2](a, b);
        };
      }
      for (const list of sortLists) {
        const directItems = Array.from(list.children).filter((child) => child.matches("li"));
        directItems.sort(sortFunction).forEach((sortedItem) => {
          list.appendChild(sortedItem);
        });
      }
      lastSort = sort2;
      this.updateMapInput();
    });
  }
  /**
   * Handle book selection from the entity selector.
   * @param {Object} entityInfo
   */
  bookSelect(entityInfo) {
    const alreadyAdded = this.container.querySelector(`[data-type="book"][data-id="${entityInfo.id}"]`) !== null;
    if (alreadyAdded) return;
    const entitySortItemUrl = `${entityInfo.link}/sort-item`;
    window.$http.get(entitySortItemUrl).then((resp) => {
      const newBookContainer = htmlToDom(resp.data);
      this.sortContainer.append(newBookContainer);
      this.setupBookSortable(newBookContainer);
      this.updateMoveActionStateForAll();
      const summary = newBookContainer.querySelector("summary");
      summary.focus();
    });
  }
  /**
   * Set up the given book container element to have sortable items.
   * @param {Element} bookContainer
   */
  setupBookSortable(bookContainer) {
    const sortElems = Array.from(bookContainer.querySelectorAll(".sort-list, .sortable-page-sublist"));
    const bookGroupConfig = {
      name: "book",
      pull: ["book", "chapter"],
      put: ["book", "chapter"]
    };
    const chapterGroupConfig = {
      name: "chapter",
      pull: ["book", "chapter"],
      put(toList, fromList, draggedElem) {
        return draggedElem.getAttribute("data-type") === "page";
      }
    };
    for (const sortElem of sortElems) {
      sortable_esm_default.create(sortElem, {
        group: sortElem.classList.contains("sort-list") ? bookGroupConfig : chapterGroupConfig,
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onSort: () => {
          this.ensureNoNestedChapters();
          this.updateMapInput();
          this.updateMoveActionStateForAll();
        },
        dragClass: "bg-white",
        ghostClass: "primary-background-light",
        multiDrag: true,
        multiDragKey: "Control",
        selectedClass: "sortable-selected"
      });
    }
  }
  /**
   * Handle nested chapters by moving them to the parent book.
   * Needed since sorting with multi-sort only checks group rules based on the active item,
   * not all in group, therefore need to manually check after a sort.
   * Must be done before updating the map input.
   */
  ensureNoNestedChapters() {
    const nestedChapters = this.container.querySelectorAll('[data-type="chapter"] [data-type="chapter"]');
    for (const chapter of nestedChapters) {
      const parentChapter = chapter.parentElement.closest('[data-type="chapter"]');
      parentChapter.insertAdjacentElement("afterend", chapter);
    }
  }
  /**
   * Update the input with our sort data.
   */
  updateMapInput() {
    const pageMap = this.buildEntityMap();
    this.input.value = JSON.stringify(pageMap);
  }
  /**
   * Build up a mapping of entities with their ordering and nesting.
   * @returns {Array}
   */
  buildEntityMap() {
    const entityMap = [];
    const lists = this.container.querySelectorAll(".sort-list");
    for (const list of lists) {
      const bookId = list.closest('[data-type="book"]').getAttribute("data-id");
      const directChildren = Array.from(list.children).filter((elem2) => elem2.matches('[data-type="page"], [data-type="chapter"]'));
      for (let i = 0; i < directChildren.length; i++) {
        this.addBookChildToMap(directChildren[i], i, bookId, entityMap);
      }
    }
    return entityMap;
  }
  /**
   * Parse a sort item and add it to a data-map array.
   * Parses sub0items if existing also.
   * @param {Element} childElem
   * @param {Number} index
   * @param {Number} bookId
   * @param {Array} entityMap
   */
  addBookChildToMap(childElem, index2, bookId, entityMap) {
    const type = childElem.getAttribute("data-type");
    const parentChapter = false;
    const childId = childElem.getAttribute("data-id");
    entityMap.push({
      id: childId,
      sort: index2,
      parentChapter,
      type,
      book: bookId
    });
    const subPages = childElem.querySelectorAll('[data-type="page"]');
    for (let i = 0; i < subPages.length; i++) {
      entityMap.push({
        id: subPages[i].getAttribute("data-id"),
        sort: i,
        parentChapter: childId,
        type: "page",
        book: bookId
      });
    }
  }
  /**
   * Run the given sort action up the provided sort item.
   * @param {Element} item
   * @param {String} action
   */
  runSortAction(item, action) {
    const parentItem = item.parentElement.closest("li[data-id]");
    const parentBook = item.parentElement.closest('[data-type="book"]');
    moveActions[action].run(item, parentItem, parentBook);
    this.updateMapInput();
    this.updateMoveActionStateForAll();
    item.scrollIntoView({ behavior: "smooth", block: "nearest" });
    item.focus();
  }
  /**
   * Update the state of the available move actions on this item.
   * @param {Element} item
   */
  updateMoveActionState(item) {
    const parentItem = item.parentElement.closest("li[data-id]");
    const parentBook = item.parentElement.closest('[data-type="book"]');
    for (const [action, functions] of Object.entries(moveActions)) {
      const moveButton = item.querySelector(`[data-move="${action}"]`);
      moveButton.disabled = !functions.active(item, parentItem, parentBook);
    }
  }
  updateMoveActionStateForAll() {
    const items = this.container.querySelectorAll('[data-type="chapter"],[data-type="page"]');
    for (const item of items) {
      this.updateMoveActionState(item);
    }
  }
};

// resources/js/services/animations.ts
var animateStylesCleanupMap = /* @__PURE__ */ new WeakMap();
function animateStyles(element, styles, animTime = 400, onComplete = null) {
  const styleNames = Object.keys(styles);
  for (const style of styleNames) {
    element.style.setProperty(style, styles[style][0]);
  }
  const cleanup = () => {
    for (const style of styleNames) {
      element.style.removeProperty(style);
    }
    element.style.removeProperty("transition");
    element.removeEventListener("transitionend", cleanup);
    animateStylesCleanupMap.delete(element);
    if (onComplete) onComplete();
  };
  setTimeout(() => {
    element.style.transition = `all ease-in-out ${animTime}ms`;
    for (const style of styleNames) {
      element.style.setProperty(style, styles[style][1]);
    }
    element.addEventListener("transitionend", cleanup);
    animateStylesCleanupMap.set(element, cleanup);
  }, 15);
}
function cleanupExistingElementAnimation(element) {
  if (animateStylesCleanupMap.has(element)) {
    const oldCleanup = animateStylesCleanupMap.get(element);
    oldCleanup();
  }
}
function fadeIn(element, animTime = 400, onComplete = null) {
  cleanupExistingElementAnimation(element);
  element.style.display = "block";
  animateStyles(element, {
    "opacity": ["0", "1"]
  }, animTime, () => {
    if (onComplete) onComplete();
  });
}
function fadeOut(element, animTime = 400, onComplete = null) {
  cleanupExistingElementAnimation(element);
  animateStyles(element, {
    "opacity": ["1", "0"]
  }, animTime, () => {
    element.style.display = "none";
    if (onComplete) onComplete();
  });
}
function slideUp(element, animTime = 400) {
  cleanupExistingElementAnimation(element);
  const currentHeight = element.getBoundingClientRect().height;
  const computedStyles = getComputedStyle(element);
  const currentPaddingTop = computedStyles.getPropertyValue("padding-top");
  const currentPaddingBottom = computedStyles.getPropertyValue("padding-bottom");
  const animStyles = {
    "max-height": [`${currentHeight}px`, "0px"],
    "overflow": ["hidden", "hidden"],
    "padding-top": [currentPaddingTop, "0px"],
    "padding-bottom": [currentPaddingBottom, "0px"]
  };
  animateStyles(element, animStyles, animTime, () => {
    element.style.display = "none";
  });
}
function slideDown(element, animTime = 400) {
  cleanupExistingElementAnimation(element);
  element.style.display = "block";
  const targetHeight = element.getBoundingClientRect().height;
  const computedStyles = getComputedStyle(element);
  const targetPaddingTop = computedStyles.getPropertyValue("padding-top");
  const targetPaddingBottom = computedStyles.getPropertyValue("padding-bottom");
  const animStyles = {
    "max-height": ["0px", `${targetHeight}px`],
    "overflow": ["hidden", "hidden"],
    "padding-top": ["0px", targetPaddingTop],
    "padding-bottom": ["0px", targetPaddingBottom]
  };
  animateStyles(element, animStyles, animTime);
}
function transitionHeight(element, animTime = 400) {
  const startHeight = element.getBoundingClientRect().height;
  const initialComputedStyles = getComputedStyle(element);
  const startPaddingTop = initialComputedStyles.getPropertyValue("padding-top");
  const startPaddingBottom = initialComputedStyles.getPropertyValue("padding-bottom");
  return () => {
    cleanupExistingElementAnimation(element);
    const targetHeight = element.getBoundingClientRect().height;
    const computedStyles = getComputedStyle(element);
    const targetPaddingTop = computedStyles.getPropertyValue("padding-top");
    const targetPaddingBottom = computedStyles.getPropertyValue("padding-bottom");
    const animStyles = {
      "height": [`${startHeight}px`, `${targetHeight}px`],
      "overflow": ["hidden", "hidden"],
      "padding-top": [startPaddingTop, targetPaddingTop],
      "padding-bottom": [startPaddingBottom, targetPaddingBottom]
    };
    animateStyles(element, animStyles, animTime);
  };
}

// resources/js/components/chapter-contents.js
var ChapterContents = class extends Component {
  setup() {
    this.list = this.$refs.list;
    this.toggle = this.$refs.toggle;
    this.isOpen = this.toggle.classList.contains("open");
    this.toggle.addEventListener("click", this.click.bind(this));
  }
  open() {
    this.toggle.classList.add("open");
    this.toggle.setAttribute("aria-expanded", "true");
    slideDown(this.list, 180);
    this.isOpen = true;
  }
  close() {
    this.toggle.classList.remove("open");
    this.toggle.setAttribute("aria-expanded", "false");
    slideUp(this.list, 180);
    this.isOpen = false;
  }
  click(event) {
    event.preventDefault();
    if (this.isOpen) {
      this.close();
    } else {
      this.open();
    }
  }
};

// resources/js/components/code-editor.js
var CodeEditor = class extends Component {
  constructor() {
    super(...arguments);
    /**
     * @type {null|SimpleEditorInterface}
     */
    __publicField(this, "editor", null);
    /**
     * @type {?Function}
     */
    __publicField(this, "saveCallback", null);
    /**
     * @type {?Function}
     */
    __publicField(this, "cancelCallback", null);
    __publicField(this, "history", {});
    __publicField(this, "historyKey", "code_history");
  }
  setup() {
    this.container = this.$refs.container;
    this.popup = this.$el;
    this.editorInput = this.$refs.editor;
    this.languageButtons = this.$manyRefs.languageButton;
    this.languageOptionsContainer = this.$refs.languageOptionsContainer;
    this.saveButton = this.$refs.saveButton;
    this.languageInput = this.$refs.languageInput;
    this.historyDropDown = this.$refs.historyDropDown;
    this.historyList = this.$refs.historyList;
    this.favourites = new Set(this.$opts.favourites.split(","));
    this.setupListeners();
    this.setupFavourites();
  }
  setupListeners() {
    this.container.addEventListener("keydown", (event) => {
      if (event.ctrlKey && event.key === "Enter") {
        this.save();
      }
    });
    onSelect(this.languageButtons, (event) => {
      const language = event.target.dataset.lang;
      this.languageInput.value = language;
      this.languageInputChange(language);
    });
    onEnterPress(this.languageInput, () => this.save());
    this.languageInput.addEventListener("input", () => this.languageInputChange(this.languageInput.value));
    onSelect(this.saveButton, () => this.save());
    onChildEvent(this.historyList, "button", "click", (event, elem2) => {
      event.preventDefault();
      const historyTime = elem2.dataset.time;
      if (this.editor) {
        this.editor.setContent(this.history[historyTime]);
      }
    });
  }
  setupFavourites() {
    for (const button of this.languageButtons) {
      this.setupFavouritesForButton(button);
    }
    this.sortLanguageList();
  }
  /**
   * @param {HTMLButtonElement} button
   */
  setupFavouritesForButton(button) {
    const language = button.dataset.lang;
    let isFavorite = this.favourites.has(language);
    button.setAttribute("data-favourite", isFavorite ? "true" : "false");
    onChildEvent(button.parentElement, ".lang-option-favorite-toggle", "click", () => {
      isFavorite = !isFavorite;
      if (isFavorite) {
        this.favourites.add(language);
      } else {
        this.favourites.delete(language);
      }
      button.setAttribute("data-favourite", isFavorite ? "true" : "false");
      window.$http.patch("/preferences/update-code-language-favourite", {
        language,
        active: isFavorite
      });
      this.sortLanguageList();
      if (isFavorite) {
        button.scrollIntoView({ block: "center", behavior: "smooth" });
      }
    });
  }
  sortLanguageList() {
    const sortedParents = this.languageButtons.sort((a, b) => {
      const aFav = a.dataset.favourite === "true";
      const bFav = b.dataset.favourite === "true";
      if (aFav && !bFav) {
        return -1;
      }
      if (bFav && !aFav) {
        return 1;
      }
      return a.dataset.lang > b.dataset.lang ? 1 : -1;
    }).map((button) => button.parentElement);
    for (const parent of sortedParents) {
      this.languageOptionsContainer.append(parent);
    }
  }
  save() {
    if (this.saveCallback) {
      this.saveCallback(this.editor.getContent(), this.languageInput.value);
    }
    this.hide();
  }
  async open(code, language, direction, saveCallback, cancelCallback) {
    this.languageInput.value = language;
    this.saveCallback = saveCallback;
    this.cancelCallback = cancelCallback;
    await this.show();
    this.languageInputChange(language);
    this.editor.setContent(code);
    this.setDirection(direction);
  }
  async show() {
    const Code = await window.importVersioned("code");
    if (!this.editor) {
      this.editor = Code.popupEditor(this.editorInput, this.languageInput.value);
    }
    this.loadHistory();
    this.getPopup().show(() => {
      this.editor.focus();
    }, () => {
      this.addHistory();
      if (this.cancelCallback) {
        this.cancelCallback();
      }
    });
  }
  setDirection(direction) {
    const target = this.editorInput.parentElement;
    if (direction) {
      target.setAttribute("dir", direction);
    } else {
      target.removeAttribute("dir");
    }
  }
  hide() {
    this.getPopup().hide();
    this.addHistory();
  }
  /**
   * @returns {Popup}
   */
  getPopup() {
    return window.$components.firstOnElement(this.popup, "popup");
  }
  async updateEditorMode(language) {
    this.editor.setMode(language, this.editor.getContent());
  }
  languageInputChange(language) {
    this.updateEditorMode(language);
    const inputLang = language.toLowerCase();
    for (const link of this.languageButtons) {
      const lang = link.dataset.lang.toLowerCase().trim();
      const isMatch = inputLang === lang;
      link.classList.toggle("active", isMatch);
      if (isMatch) {
        link.scrollIntoView({ block: "center", behavior: "smooth" });
      }
    }
  }
  loadHistory() {
    this.history = JSON.parse(window.sessionStorage.getItem(this.historyKey) || "{}");
    const historyKeys = Object.keys(this.history).reverse();
    this.historyDropDown.classList.toggle("hidden", historyKeys.length === 0);
    this.historyList.innerHTML = historyKeys.map((key) => {
      const localTime = new Date(parseInt(key, 10)).toLocaleTimeString();
      return `<li><button type="button" data-time="${key}" class="text-item">${localTime}</button></li>`;
    }).join("");
  }
  addHistory() {
    if (!this.editor) return;
    const code = this.editor.getContent();
    if (!code) return;
    const lastHistoryKey = Object.keys(this.history).pop();
    if (this.history[lastHistoryKey] === code) return;
    this.history[String(Date.now())] = code;
    const historyString = JSON.stringify(this.history);
    window.sessionStorage.setItem(this.historyKey, historyString);
  }
};

// resources/js/components/code-highlighter.js
var CodeHighlighter = class extends Component {
  setup() {
    const container = this.$el;
    const codeBlocks = container.querySelectorAll("pre");
    if (codeBlocks.length > 0) {
      window.importVersioned("code").then((Code) => {
        Code.highlightWithin(container);
      });
    }
  }
};

// resources/js/components/code-textarea.js
var CodeTextarea = class extends Component {
  async setup() {
    const { mode } = this.$opts;
    const Code = await window.importVersioned("code");
    Code.inlineEditor(this.$el, mode);
  }
};

// resources/js/components/collapsible.js
var Collapsible = class extends Component {
  setup() {
    this.container = this.$el;
    this.trigger = this.$refs.trigger;
    this.content = this.$refs.content;
    if (this.trigger) {
      this.trigger.addEventListener("click", this.toggle.bind(this));
      this.openIfContainsError();
    }
  }
  open() {
    this.container.classList.add("open");
    this.trigger.setAttribute("aria-expanded", "true");
    slideDown(this.content, 300);
  }
  close() {
    this.container.classList.remove("open");
    this.trigger.setAttribute("aria-expanded", "false");
    slideUp(this.content, 300);
  }
  toggle() {
    if (this.container.classList.contains("open")) {
      this.close();
    } else {
      this.open();
    }
  }
  openIfContainsError() {
    const error = this.content.querySelector(".text-neg.text-small");
    if (error) {
      this.open();
    }
  }
};

// resources/js/components/confirm-dialog.js
var ConfirmDialog = class extends Component {
  setup() {
    this.container = this.$el;
    this.confirmButton = this.$refs.confirm;
    this.res = null;
    onSelect(this.confirmButton, () => {
      this.sendResult(true);
      this.getPopup().hide();
    });
  }
  show() {
    this.getPopup().show(null, () => {
      this.sendResult(false);
    });
    return new Promise((res) => {
      this.res = res;
    });
  }
  /**
   * @returns {Popup}
   */
  getPopup() {
    return window.$components.firstOnElement(this.container, "popup");
  }
  /**
   * @param {Boolean} result
   */
  sendResult(result) {
    if (this.res) {
      this.res(result);
      this.res = null;
    }
  }
};

// resources/js/components/custom-checkbox.js
var CustomCheckbox = class extends Component {
  setup() {
    this.container = this.$el;
    this.checkbox = this.container.querySelector("input[type=checkbox]");
    this.display = this.container.querySelector('[role="checkbox"]');
    this.checkbox.addEventListener("change", this.stateChange.bind(this));
    this.container.addEventListener("keydown", this.onKeyDown.bind(this));
  }
  onKeyDown(event) {
    const isEnterOrSpace = event.key === " " || event.key === "Enter";
    if (isEnterOrSpace) {
      event.preventDefault();
      this.toggle();
    }
  }
  toggle() {
    this.checkbox.checked = !this.checkbox.checked;
    this.checkbox.dispatchEvent(new Event("change"));
    this.stateChange();
  }
  stateChange() {
    const checked = this.checkbox.checked ? "true" : "false";
    this.display.setAttribute("aria-checked", checked);
  }
};

// resources/js/components/details-highlighter.js
var DetailsHighlighter = class extends Component {
  setup() {
    this.container = this.$el;
    this.dealtWith = false;
    this.container.addEventListener("toggle", this.onToggle.bind(this));
  }
  onToggle() {
    if (this.dealtWith) return;
    if (this.container.querySelector("pre")) {
      window.importVersioned("code").then((Code) => {
        Code.highlightWithin(this.container);
      });
    }
    this.dealtWith = true;
  }
};

// resources/js/components/dropdown.js
var Dropdown = class extends Component {
  setup() {
    this.container = this.$el;
    this.menu = this.$refs.menu;
    this.toggle = this.$refs.toggle;
    this.moveMenu = this.$opts.moveMenu;
    this.bubbleEscapes = this.$opts.bubbleEscapes === "true";
    this.direction = document.dir === "rtl" ? "right" : "left";
    this.body = document.body;
    this.showing = false;
    this.hide = this.hide.bind(this);
    this.setupListeners();
  }
  show(event = null) {
    this.hideAll();
    this.menu.style.display = "block";
    this.menu.classList.add("anim", "menuIn");
    this.toggle.setAttribute("aria-expanded", "true");
    const menuOriginalRect = this.menu.getBoundingClientRect();
    let heightOffset = 0;
    const toggleHeight = this.toggle.getBoundingClientRect().height;
    const containerBounds = findClosestScrollContainer(this.menu).getBoundingClientRect();
    const dropUpwards = menuOriginalRect.bottom > containerBounds.bottom;
    const containerRect = this.container.getBoundingClientRect();
    if (this.moveMenu) {
      this.body.appendChild(this.menu);
      this.menu.style.position = "fixed";
      this.menu.style.width = `${menuOriginalRect.width}px`;
      this.menu.style.left = `${menuOriginalRect.left}px`;
      if (dropUpwards) {
        heightOffset = window.innerHeight - menuOriginalRect.top - toggleHeight / 2;
      } else {
        heightOffset = menuOriginalRect.top;
      }
    }
    if (dropUpwards) {
      this.menu.style.top = "initial";
      this.menu.style.bottom = `${heightOffset}px`;
      const maxHeight = window.innerHeight - 40 - (window.innerHeight - containerRect.bottom);
      this.menu.style.maxHeight = `${Math.floor(maxHeight)}px`;
    } else {
      this.menu.style.top = `${heightOffset}px`;
      this.menu.style.bottom = "initial";
      const maxHeight = window.innerHeight - 40 - containerRect.top;
      this.menu.style.maxHeight = `${Math.floor(maxHeight)}px`;
    }
    this.menu.addEventListener("mouseleave", this.hide);
    window.addEventListener("click", (clickEvent) => {
      if (!this.menu.contains(clickEvent.target)) {
        this.hide();
      }
    });
    const input = this.menu.querySelector("input");
    if (input !== null) input.focus();
    this.showing = true;
    const showEvent = new Event("show");
    this.container.dispatchEvent(showEvent);
    if (event) {
      event.stopPropagation();
    }
  }
  hideAll() {
    for (const dropdown of window.$components.get("dropdown")) {
      dropdown.hide();
    }
  }
  hide() {
    this.menu.style.display = "none";
    this.menu.classList.remove("anim", "menuIn");
    this.toggle.setAttribute("aria-expanded", "false");
    this.menu.style.top = "";
    this.menu.style.bottom = "";
    this.menu.style.maxHeight = "";
    if (this.moveMenu) {
      this.menu.style.position = "";
      this.menu.style[this.direction] = "";
      this.menu.style.width = "";
      this.menu.style.left = "";
      this.container.appendChild(this.menu);
    }
    this.showing = false;
  }
  setupListeners() {
    const keyboardNavHandler = new KeyboardNavigationHandler(this.container, (event) => {
      this.hide();
      this.toggle.focus();
      if (!this.bubbleEscapes) {
        event.stopPropagation();
      }
    }, (event) => {
      if (event.target.nodeName === "INPUT") {
        event.preventDefault();
        event.stopPropagation();
      }
      this.hide();
    });
    if (this.moveMenu) {
      keyboardNavHandler.shareHandlingToEl(this.menu);
    }
    this.container.addEventListener("click", (event) => {
      const possibleChildren = Array.from(this.menu.querySelectorAll("a"));
      if (possibleChildren.includes(event.target)) {
        this.hide();
      }
    });
    onSelect(this.toggle, (event) => {
      event.stopPropagation();
      event.preventDefault();
      this.show(event);
      if (event instanceof KeyboardEvent) {
        keyboardNavHandler.focusNext();
      }
    });
  }
};

// resources/js/components/dropdown-search.js
var DropdownSearch = class extends Component {
  setup() {
    this.elem = this.$el;
    this.searchInput = this.$refs.searchInput;
    this.loadingElem = this.$refs.loading;
    this.listContainerElem = this.$refs.listContainer;
    this.localSearchSelector = this.$opts.localSearchSelector;
    this.url = this.$opts.url;
    this.elem.addEventListener("show", this.onShow.bind(this));
    this.searchInput.addEventListener("input", this.onSearch.bind(this));
    this.runAjaxSearch = debounce(this.runAjaxSearch, 300, false);
  }
  onShow() {
    this.loadList();
  }
  onSearch() {
    const input = this.searchInput.value.toLowerCase().trim();
    if (this.localSearchSelector) {
      this.runLocalSearch(input);
    } else {
      this.toggleLoading(true);
      this.listContainerElem.innerHTML = "";
      this.runAjaxSearch(input);
    }
  }
  runAjaxSearch(searchTerm) {
    this.loadList(searchTerm);
  }
  runLocalSearch(searchTerm) {
    const listItems = this.listContainerElem.querySelectorAll(this.localSearchSelector);
    for (const listItem of listItems) {
      const match = !searchTerm || listItem.textContent.toLowerCase().includes(searchTerm);
      listItem.style.display = match ? "flex" : "none";
      listItem.classList.toggle("hidden", !match);
    }
  }
  async loadList(searchTerm = "") {
    this.listContainerElem.innerHTML = "";
    this.toggleLoading(true);
    try {
      const resp = await window.$http.get(this.getAjaxUrl(searchTerm));
      const animate = transitionHeight(this.listContainerElem, 80);
      this.listContainerElem.innerHTML = resp.data;
      animate();
    } catch (err) {
      console.error(err);
    }
    this.toggleLoading(false);
    if (this.localSearchSelector) {
      this.onSearch();
    }
  }
  getAjaxUrl(searchTerm = null) {
    if (!searchTerm) {
      return this.url;
    }
    const joiner = this.url.includes("?") ? "&" : "?";
    return `${this.url}${joiner}search=${encodeURIComponent(searchTerm)}`;
  }
  toggleLoading(show2 = false) {
    this.loadingElem.style.display = show2 ? "block" : "none";
  }
};

// resources/js/services/clipboard.ts
var Clipboard = class {
  constructor(clipboardData) {
    __publicField(this, "data");
    this.data = clipboardData;
  }
  /**
   * Check if the clipboard has any items.
   */
  hasItems() {
    return Boolean(this.data) && Boolean(this.data.types) && this.data.types.length > 0;
  }
  /**
   * Check if the given event has tabular-looking data in the clipboard.
   */
  containsTabularData() {
    const rtfData = this.data.getData("text/rtf");
    return !!rtfData && rtfData.includes("\\trowd");
  }
  /**
   * Get the images that are in the clipboard data.
   */
  getImages() {
    return this.getFiles().filter((f) => f.type.includes("image"));
  }
  /**
   * Get the files included in the clipboard data.
   */
  getFiles() {
    const { files } = this.data;
    return [...files];
  }
};
async function copyTextToClipboard(text) {
  if (window.isSecureContext && navigator.clipboard) {
    await navigator.clipboard.writeText(text);
    return;
  }
  const tempInput = document.createElement("textarea");
  tempInput.setAttribute("style", "position: absolute; left: -1000px; top: -1000px;");
  tempInput.value = text;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
}

// resources/js/components/dropzone.js
var Dropzone = class extends Component {
  setup() {
    this.container = this.$el;
    this.statusArea = this.$refs.statusArea;
    this.dropTarget = this.$refs.dropTarget;
    this.selectButtons = this.$manyRefs.selectButton || [];
    this.isActive = true;
    this.url = this.$opts.url;
    this.method = (this.$opts.method || "post").toUpperCase();
    this.successMessage = this.$opts.successMessage;
    this.errorMessage = this.$opts.errorMessage;
    this.uploadLimitMb = Number(this.$opts.uploadLimit);
    this.uploadLimitMessage = this.$opts.uploadLimitMessage;
    this.zoneText = this.$opts.zoneText;
    this.fileAcceptTypes = this.$opts.fileAccept;
    this.allowMultiple = this.$opts.allowMultiple === "true";
    this.setupListeners();
  }
  /**
   * Public method to allow external disabling/enabling of this drag+drop dropzone.
   * @param {Boolean} active
   */
  toggleActive(active) {
    this.isActive = active;
  }
  setupListeners() {
    onSelect(this.selectButtons, this.manualSelectHandler.bind(this));
    this.setupDropTargetHandlers();
  }
  setupDropTargetHandlers() {
    let depth = 0;
    const reset = () => {
      this.hideOverlay();
      depth = 0;
    };
    this.dropTarget.addEventListener("dragenter", (event) => {
      event.preventDefault();
      depth += 1;
      if (depth === 1 && this.isActive) {
        this.showOverlay();
      }
    });
    this.dropTarget.addEventListener("dragover", (event) => {
      event.preventDefault();
    });
    this.dropTarget.addEventListener("dragend", reset);
    this.dropTarget.addEventListener("dragleave", () => {
      depth -= 1;
      if (depth === 0) {
        reset();
      }
    });
    this.dropTarget.addEventListener("drop", (event) => {
      event.preventDefault();
      reset();
      if (!this.isActive) {
        return;
      }
      const clipboard = new Clipboard(event.dataTransfer);
      const files = clipboard.getFiles();
      for (const file of files) {
        this.createUploadFromFile(file);
      }
    });
  }
  manualSelectHandler() {
    const input = elem("input", {
      type: "file",
      style: "left: -400px; visibility: hidden; position: fixed;",
      accept: this.fileAcceptTypes,
      multiple: this.allowMultiple ? "" : null
    });
    this.container.append(input);
    input.click();
    input.addEventListener("change", () => {
      for (const file of input.files) {
        this.createUploadFromFile(file);
      }
      input.remove();
    });
  }
  showOverlay() {
    const overlay = this.dropTarget.querySelector(".dropzone-overlay");
    if (!overlay) {
      const zoneElem = elem("div", { class: "dropzone-overlay" }, [this.zoneText]);
      this.dropTarget.append(zoneElem);
    }
  }
  hideOverlay() {
    const overlay = this.dropTarget.querySelector(".dropzone-overlay");
    if (overlay) {
      overlay.remove();
    }
  }
  /**
   * @param {File} file
   * @return {Upload}
   */
  createUploadFromFile(file) {
    const {
      dom,
      status,
      progress,
      dismiss
    } = this.createDomForFile(file);
    this.statusArea.append(dom);
    const component = this;
    const upload2 = {
      file,
      dom,
      updateProgress(percentComplete) {
        progress.textContent = `${percentComplete}%`;
        progress.style.width = `${percentComplete}%`;
      },
      markError(message) {
        status.setAttribute("data-status", "error");
        status.textContent = message;
        removeLoading(dom);
        this.updateProgress(100);
      },
      markSuccess(message) {
        status.setAttribute("data-status", "success");
        status.textContent = message;
        removeLoading(dom);
        setTimeout(dismiss, 2400);
        component.$emit("upload-success", {
          name: file.name
        });
      }
    };
    if (file.size > this.uploadLimitMb * 1e6) {
      upload2.markError(this.uploadLimitMessage);
      return upload2;
    }
    this.startXhrForUpload(upload2);
    return upload2;
  }
  /**
   * @param {Upload} upload
   */
  startXhrForUpload(upload2) {
    const formData = new FormData();
    formData.append("file", upload2.file, upload2.file.name);
    if (this.method !== "POST") {
      formData.append("_method", this.method);
    }
    const component = this;
    const req = window.$http.createXMLHttpRequest("POST", this.url, {
      error() {
        upload2.markError(component.errorMessage);
      },
      readystatechange() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
          upload2.markSuccess(component.successMessage);
        } else if (this.readyState === XMLHttpRequest.DONE && this.status >= 400) {
          upload2.markError(window.$http.formatErrorResponseText(this.responseText));
        }
      }
    });
    req.upload.addEventListener("progress", (evt) => {
      const percent = Math.min(Math.ceil(evt.loaded / evt.total * 100), 100);
      upload2.updateProgress(percent);
    });
    req.setRequestHeader("Accept", "application/json");
    req.send(formData);
  }
  /**
   * @param {File} file
   * @return {{image: Element, dom: Element, progress: Element, status: Element, dismiss: function}}
   */
  createDomForFile(file) {
    const image = elem("img", { src: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M9.224 7.373a.924.924 0 0 0-.92.925l-.006 7.404c0 .509.412.925.921.925h5.557a.928.928 0 0 0 .926-.925v-5.553l-2.777-2.776Zm3.239 3.239V8.067l2.545 2.545z' style='fill:%23000;fill-opacity:.75'/%3E%3C/svg%3E" });
    const status = elem("div", { class: "dropzone-file-item-status" }, []);
    const progress = elem("div", { class: "dropzone-file-item-progress" });
    const imageWrap = elem("div", { class: "dropzone-file-item-image-wrap" }, [image]);
    const dom = elem("div", { class: "dropzone-file-item" }, [
      imageWrap,
      elem("div", { class: "dropzone-file-item-text-wrap" }, [
        elem("div", { class: "dropzone-file-item-label" }, [file.name]),
        getLoading(),
        status
      ]),
      progress
    ]);
    if (file.type.startsWith("image/")) {
      image.src = URL.createObjectURL(file);
    }
    const dismiss = () => {
      dom.classList.add("dismiss");
      dom.addEventListener("animationend", () => {
        dom.remove();
      });
    };
    dom.addEventListener("click", dismiss);
    return {
      dom,
      progress,
      status,
      dismiss
    };
  }
};

// resources/js/components/editor-toolbox.ts
var EditorToolbox = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "container");
    __publicField(this, "buttons");
    __publicField(this, "contentElements");
    __publicField(this, "toggleButton");
    __publicField(this, "editorWrapEl");
    __publicField(this, "open", false);
    __publicField(this, "tab", "");
  }
  setup() {
    this.container = this.$el;
    this.buttons = this.$manyRefs.tabButton;
    this.contentElements = this.$manyRefs.tabContent;
    this.toggleButton = this.$refs.toggle;
    this.editorWrapEl = this.container.closest(".page-editor");
    this.setupListeners();
    this.setActiveTab(this.contentElements[0].dataset.tabContent || "");
  }
  setupListeners() {
    this.toggleButton.addEventListener("click", () => this.toggle());
    this.container.addEventListener("click", (event) => {
      const button = event.target.closest("button");
      if (button instanceof HTMLButtonElement && this.buttons.includes(button)) {
        const name = button.dataset.tab || "";
        this.setActiveTab(name, true);
      }
    });
  }
  toggle() {
    this.container.classList.toggle("open");
    const isOpen = this.container.classList.contains("open");
    this.toggleButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
    this.editorWrapEl.classList.toggle("toolbox-open", isOpen);
    this.open = isOpen;
    this.emitState();
  }
  setActiveTab(tabName, openToolbox = false) {
    for (const button of this.buttons) {
      button.classList.remove("active");
      const bName = button.dataset.tab;
      if (bName === tabName) button.classList.add("active");
    }
    for (const contentEl of this.contentElements) {
      contentEl.style.display = "none";
      const cName = contentEl.dataset.tabContent;
      if (cName === tabName) contentEl.style.display = "block";
    }
    if (openToolbox && !this.container.classList.contains("open")) {
      this.toggle();
    }
    this.tab = tabName;
    this.emitState();
  }
  emitState() {
    const data = { tab: this.tab, open: this.open };
    this.$emit("change", data);
  }
};

// resources/js/components/entity-permissions.js
var EntityPermissions = class extends Component {
  setup() {
    this.container = this.$el;
    this.entityType = this.$opts.entityType;
    this.everyoneInheritToggle = this.$refs.everyoneInherit;
    this.roleSelect = this.$refs.roleSelect;
    this.roleContainer = this.$refs.roleContainer;
    this.setupListeners();
  }
  setupListeners() {
    this.everyoneInheritToggle.addEventListener("change", (event) => {
      const inherit = event.target.checked;
      const permissions = document.querySelectorAll('input[name^="permissions[0]["]');
      for (const permission of permissions) {
        permission.disabled = inherit;
        permission.checked = false;
      }
    });
    this.container.addEventListener("click", (event) => {
      const button = event.target.closest("button");
      if (button && button.dataset.roleId) {
        this.removeRowOnButtonClick(button);
      }
    });
    this.roleSelect.addEventListener("change", () => {
      const roleId = this.roleSelect.value;
      if (roleId) {
        this.addRoleRow(roleId);
      }
    });
  }
  async addRoleRow(roleId) {
    this.roleSelect.disabled = true;
    const option2 = this.roleSelect.querySelector(`option[value="${roleId}"]`);
    if (option2) {
      option2.remove();
    }
    const resp = await window.$http.get(`/permissions/form-row/${this.entityType}/${roleId}`);
    const row = htmlToDom(resp.data);
    this.roleContainer.append(row);
    this.roleSelect.disabled = false;
  }
  removeRowOnButtonClick(button) {
    const row = button.closest(".item-list-row");
    const { roleId } = button.dataset;
    const { roleName } = button.dataset;
    const option2 = document.createElement("option");
    option2.value = roleId;
    option2.textContent = roleName;
    this.roleSelect.append(option2);
    row.remove();
  }
};

// resources/js/components/entity-search.js
var EntitySearch = class extends Component {
  setup() {
    this.entityId = this.$opts.entityId;
    this.entityType = this.$opts.entityType;
    this.contentView = this.$refs.contentView;
    this.searchView = this.$refs.searchView;
    this.searchResults = this.$refs.searchResults;
    this.searchInput = this.$refs.searchInput;
    this.searchForm = this.$refs.searchForm;
    this.clearButton = this.$refs.clearButton;
    this.loadingBlock = this.$refs.loadingBlock;
    this.setupListeners();
  }
  setupListeners() {
    this.searchInput.addEventListener("change", this.runSearch.bind(this));
    this.searchForm.addEventListener("submit", (e) => {
      e.preventDefault();
      this.runSearch();
    });
    onSelect(this.clearButton, this.clearSearch.bind(this));
  }
  runSearch() {
    const term = this.searchInput.value.trim();
    if (term.length === 0) {
      this.clearSearch();
      return;
    }
    this.searchView.classList.remove("hidden");
    this.contentView.classList.add("hidden");
    this.loadingBlock.classList.remove("hidden");
    const url = window.baseUrl(`/search/${this.entityType}/${this.entityId}`);
    window.$http.get(url, { term }).then((resp) => {
      this.searchResults.innerHTML = resp.data;
    }).catch(console.error).then(() => {
      this.loadingBlock.classList.add("hidden");
    });
  }
  clearSearch() {
    this.searchView.classList.add("hidden");
    this.contentView.classList.remove("hidden");
    this.loadingBlock.classList.add("hidden");
    this.searchInput.value = "";
  }
};

// resources/js/components/entity-selector.ts
var EntitySelector = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "elem");
    __publicField(this, "input");
    __publicField(this, "searchInput");
    __publicField(this, "loading");
    __publicField(this, "resultsContainer");
    __publicField(this, "searchOptions");
    __publicField(this, "search", "");
    __publicField(this, "lastClick", 0);
  }
  setup() {
    this.elem = this.$el;
    this.input = this.$refs.input;
    this.searchInput = this.$refs.search;
    this.loading = this.$refs.loading;
    this.resultsContainer = this.$refs.results;
    this.searchOptions = {
      entityTypes: this.$opts.entityTypes || "page,book,chapter",
      entityPermission: this.$opts.entityPermission || "view",
      searchEndpoint: this.$opts.searchEndpoint || "",
      initialValue: this.searchInput.value || ""
    };
    this.setupListeners();
    this.showLoading();
    if (this.searchOptions.searchEndpoint) {
      this.initialLoad();
    }
  }
  configureSearchOptions(options2) {
    Object.assign(this.searchOptions, options2);
    this.reset();
    this.searchInput.value = this.searchOptions.initialValue;
  }
  setupListeners() {
    this.elem.addEventListener("click", this.onClick.bind(this));
    let lastSearch = 0;
    this.searchInput.addEventListener("input", () => {
      lastSearch = Date.now();
      this.showLoading();
      setTimeout(() => {
        if (Date.now() - lastSearch < 199) return;
        this.searchEntities(this.searchInput.value);
      }, 200);
    });
    this.searchInput.addEventListener("keydown", (event) => {
      if (event.keyCode === 13) event.preventDefault();
    });
    onChildEvent(this.$el, "[data-entity-type]", "keydown", (event) => {
      if (event.ctrlKey && event.code === "Enter") {
        const form = this.$el.closest("form");
        if (form) {
          form.submit();
          event.preventDefault();
          return;
        }
      }
      if (event.code === "ArrowDown") {
        this.focusAdjacent(true);
      }
      if (event.code === "ArrowUp") {
        this.focusAdjacent(false);
      }
    });
    this.searchInput.addEventListener("keydown", (event) => {
      if (event.code === "ArrowDown") {
        this.focusAdjacent(true);
      }
    });
  }
  focusAdjacent(forward = true) {
    const items = Array.from(this.resultsContainer.querySelectorAll("[data-entity-type]"));
    const selectedIndex = items.indexOf(document.activeElement);
    const newItem = items[selectedIndex + (forward ? 1 : -1)] || items[0];
    if (newItem instanceof HTMLElement) {
      newItem.focus();
    }
  }
  reset() {
    this.searchInput.value = "";
    this.showLoading();
    this.initialLoad();
  }
  focusSearch() {
    this.searchInput.focus();
  }
  showLoading() {
    this.loading.style.display = "block";
    this.resultsContainer.style.display = "none";
  }
  hideLoading() {
    this.loading.style.display = "none";
    this.resultsContainer.style.display = "block";
  }
  initialLoad() {
    if (!this.searchOptions.searchEndpoint) {
      throw new Error("Search endpoint not set for entity-selector load");
    }
    if (this.searchOptions.initialValue) {
      this.searchEntities(this.searchOptions.initialValue);
      return;
    }
    window.$http.get(this.searchUrl()).then((resp) => {
      this.resultsContainer.innerHTML = resp.data;
      this.hideLoading();
    });
  }
  searchUrl() {
    const query = `types=${encodeURIComponent(this.searchOptions.entityTypes)}&permission=${encodeURIComponent(this.searchOptions.entityPermission)}`;
    return `${this.searchOptions.searchEndpoint}?${query}`;
  }
  searchEntities(searchTerm) {
    if (!this.searchOptions.searchEndpoint) {
      throw new Error("Search endpoint not set for entity-selector load");
    }
    this.input.value = "";
    const url = `${this.searchUrl()}&term=${encodeURIComponent(searchTerm)}`;
    window.$http.get(url).then((resp) => {
      this.resultsContainer.innerHTML = resp.data;
      this.hideLoading();
    });
  }
  isDoubleClick() {
    const now = Date.now();
    const answer = now - this.lastClick < 300;
    this.lastClick = now;
    return answer;
  }
  onClick(event) {
    const listItem = event.target.closest("[data-entity-type]");
    if (listItem instanceof HTMLElement) {
      event.preventDefault();
      event.stopPropagation();
      this.selectItem(listItem);
    }
  }
  selectItem(item) {
    const isDblClick = this.isDoubleClick();
    const type = item.getAttribute("data-entity-type");
    const id = item.getAttribute("data-entity-id");
    const isSelected = !item.classList.contains("selected") || isDblClick;
    this.unselectAll();
    this.input.value = isSelected ? `${type}:${id}` : "";
    const link = item.getAttribute("href") || "";
    const name = item.querySelector(".entity-list-item-name")?.textContent || "";
    const data = { id: Number(id), name, link };
    if (isSelected) {
      item.classList.add("selected");
    } else {
      window.$events.emit("entity-select-change");
    }
    if (!isDblClick && !isSelected) return;
    if (isDblClick) {
      this.confirmSelection(data);
    }
    if (isSelected) {
      window.$events.emit("entity-select-change", data);
    }
  }
  confirmSelection(data) {
    window.$events.emit("entity-select-confirm", data);
  }
  unselectAll() {
    const selected = this.elem.querySelectorAll(".selected");
    for (const selectedElem of selected) {
      selectedElem.classList.remove("selected", "primary-background");
    }
  }
};

// resources/js/components/entity-selector-popup.ts
var EntitySelectorPopup = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "container");
    __publicField(this, "selectButton");
    __publicField(this, "selectorEl");
    __publicField(this, "callback", null);
    __publicField(this, "selection", null);
  }
  setup() {
    this.container = this.$el;
    this.selectButton = this.$refs.select;
    this.selectorEl = this.$refs.selector;
    this.selectButton.addEventListener("click", this.onSelectButtonClick.bind(this));
    window.$events.listen("entity-select-change", this.onSelectionChange.bind(this));
    window.$events.listen("entity-select-confirm", this.handleConfirmedSelection.bind(this));
  }
  /**
   * Show the selector popup.
   */
  show(callback, searchOptions = {}) {
    this.callback = callback;
    this.getSelector().configureSearchOptions(searchOptions);
    this.getPopup().show();
    this.getSelector().focusSearch();
  }
  hide() {
    this.getPopup().hide();
  }
  getPopup() {
    return window.$components.firstOnElement(this.container, "popup");
  }
  getSelector() {
    return window.$components.firstOnElement(this.selectorEl, "entity-selector");
  }
  onSelectButtonClick() {
    this.handleConfirmedSelection(this.selection);
  }
  onSelectionChange(entity) {
    this.selection = entity.hasOwnProperty("id") ? entity : null;
    if (!this.selection) {
      this.selectButton.setAttribute("disabled", "true");
    } else {
      this.selectButton.removeAttribute("disabled");
    }
  }
  handleConfirmedSelection(entity) {
    this.hide();
    this.getSelector().reset();
    if (this.callback && entity) this.callback(entity);
  }
};

// resources/js/components/event-emit-select.js
var EventEmitSelect = class extends Component {
  setup() {
    this.container = this.$el;
    this.name = this.$opts.name;
    onSelect(this.$el, () => {
      this.$emit(this.name, this.$opts);
    });
  }
};

// resources/js/components/expand-toggle.js
var ExpandToggle = class extends Component {
  setup() {
    this.targetSelector = this.$opts.targetSelector;
    this.isOpen = this.$opts.isOpen === "true";
    this.updateEndpoint = this.$opts.updateEndpoint;
    this.$el.addEventListener("click", this.click.bind(this));
  }
  open(elemToToggle) {
    slideDown(elemToToggle, 200);
  }
  close(elemToToggle) {
    slideUp(elemToToggle, 200);
  }
  click(event) {
    event.preventDefault();
    const matchingElems = document.querySelectorAll(this.targetSelector);
    for (const match of matchingElems) {
      const action = this.isOpen ? this.close : this.open;
      action(match);
    }
    this.isOpen = !this.isOpen;
    this.updateSystemAjax(this.isOpen);
  }
  updateSystemAjax(isOpen) {
    window.$http.patch(this.updateEndpoint, {
      expand: isOpen ? "true" : "false"
    });
  }
};

// resources/js/components/global-search.js
var GlobalSearch = class extends Component {
  setup() {
    this.container = this.$el;
    this.input = this.$refs.input;
    this.suggestions = this.$refs.suggestions;
    this.suggestionResultsWrap = this.$refs.suggestionResults;
    this.loadingWrap = this.$refs.loading;
    this.button = this.$refs.button;
    this.setupListeners();
  }
  setupListeners() {
    const updateSuggestionsDebounced = debounce(this.updateSuggestions.bind(this), 200, false);
    this.input.addEventListener("input", () => {
      const { value } = this.input;
      if (value.length > 0) {
        this.loadingWrap.style.display = "block";
        this.suggestionResultsWrap.style.opacity = "0.5";
        updateSuggestionsDebounced(value);
      } else {
        this.hideSuggestions();
      }
    });
    this.input.addEventListener("dblclick", () => {
      this.input.setAttribute("autocomplete", "on");
      this.button.focus();
      this.input.focus();
    });
    new KeyboardNavigationHandler(this.container, () => {
      this.hideSuggestions();
    });
  }
  /**
   * @param {String} search
   */
  async updateSuggestions(search) {
    const { data: results } = await window.$http.get("/search/suggest", { term: search });
    if (!this.input.value) {
      return;
    }
    const resultDom = htmlToDom(results);
    this.suggestionResultsWrap.innerHTML = "";
    this.suggestionResultsWrap.style.opacity = "1";
    this.loadingWrap.style.display = "none";
    this.suggestionResultsWrap.append(resultDom);
    if (!this.container.classList.contains("search-active")) {
      this.showSuggestions();
    }
  }
  showSuggestions() {
    this.container.classList.add("search-active");
    window.requestAnimationFrame(() => {
      this.suggestions.classList.add("search-suggestions-animation");
    });
  }
  hideSuggestions() {
    this.container.classList.remove("search-active");
    this.suggestions.classList.remove("search-suggestions-animation");
    this.suggestionResultsWrap.innerHTML = "";
  }
};

// resources/js/components/header-mobile-toggle.js
var HeaderMobileToggle = class extends Component {
  setup() {
    this.elem = this.$el;
    this.toggleButton = this.$refs.toggle;
    this.menu = this.$refs.menu;
    this.open = false;
    this.toggleButton.addEventListener("click", this.onToggle.bind(this));
    this.onWindowClick = this.onWindowClick.bind(this);
    this.onKeyDown = this.onKeyDown.bind(this);
  }
  onToggle(event) {
    this.open = !this.open;
    this.menu.classList.toggle("show", this.open);
    this.toggleButton.setAttribute("aria-expanded", this.open ? "true" : "false");
    if (this.open) {
      this.elem.addEventListener("keydown", this.onKeyDown);
      window.addEventListener("click", this.onWindowClick);
    } else {
      this.elem.removeEventListener("keydown", this.onKeyDown);
      window.removeEventListener("click", this.onWindowClick);
    }
    event.stopPropagation();
  }
  onKeyDown(event) {
    if (event.code === "Escape") {
      this.onToggle(event);
    }
  }
  onWindowClick(event) {
    this.onToggle(event);
  }
};

// resources/js/components/image-manager.js
var ImageManager = class extends Component {
  setup() {
    this.uploadedTo = this.$opts.uploadedTo;
    this.container = this.$el;
    this.popupEl = this.$refs.popup;
    this.searchForm = this.$refs.searchForm;
    this.searchInput = this.$refs.searchInput;
    this.cancelSearch = this.$refs.cancelSearch;
    this.listContainer = this.$refs.listContainer;
    this.filterTabs = this.$manyRefs.filterTabs;
    this.selectButton = this.$refs.selectButton;
    this.uploadButton = this.$refs.uploadButton;
    this.uploadHint = this.$refs.uploadHint;
    this.formContainer = this.$refs.formContainer;
    this.formContainerPlaceholder = this.$refs.formContainerPlaceholder;
    this.dropzoneContainer = this.$refs.dropzoneContainer;
    this.loadMore = this.$refs.loadMore;
    this.type = "gallery";
    this.lastSelected = {};
    this.lastSelectedTime = 0;
    this.callback = null;
    this.resetState = () => {
      this.hasData = false;
      this.page = 1;
      this.filter = "all";
    };
    this.resetState();
    this.setupListeners();
  }
  setupListeners() {
    onSelect(this.filterTabs, (e) => {
      this.resetAll();
      this.filter = e.target.dataset.filter;
      this.setActiveFilterTab(this.filter);
      this.loadGallery();
    });
    this.searchForm.addEventListener("submit", (event) => {
      this.resetListView();
      this.loadGallery();
      this.cancelSearch.toggleAttribute("hidden", !this.searchInput.value);
      event.preventDefault();
    });
    onSelect(this.cancelSearch, () => {
      this.resetListView();
      this.resetSearchView();
      this.loadGallery();
    });
    onChildEvent(this.container, ".load-more button", "click", this.runLoadMore.bind(this));
    this.listContainer.addEventListener("event-emit-select-image", this.onImageSelectEvent.bind(this));
    this.listContainer.addEventListener("error", (event) => {
      event.target.src = window.baseUrl("loading_error.png");
    }, true);
    onSelect(this.selectButton, () => {
      if (this.callback) {
        this.callback(this.lastSelected);
      }
      this.hide();
    });
    onChildEvent(this.formContainer, "#image-manager-delete", "click", () => {
      if (this.lastSelected) {
        this.loadImageEditForm(this.lastSelected.id, true);
      }
    });
    onChildEvent(this.formContainer, "#image-manager-rebuild-thumbs", "click", async (_, button) => {
      button.disabled = true;
      if (this.lastSelected) {
        await this.rebuildThumbnails(this.lastSelected.id);
      }
      button.disabled = false;
    });
    this.formContainer.addEventListener("ajax-form-success", () => {
      this.refreshGallery();
      this.resetEditForm();
    });
    this.container.addEventListener("dropzone-upload-success", this.refreshGallery.bind(this));
    const scrollZone = this.listContainer.parentElement;
    let scrollEvents = [];
    scrollZone.addEventListener("wheel", (event) => {
      const scrollOffset = Math.ceil(scrollZone.scrollHeight - scrollZone.scrollTop);
      const bottomedOut = scrollOffset === scrollZone.clientHeight;
      if (!bottomedOut || event.deltaY < 1) {
        return;
      }
      const secondAgo = Date.now() - 1e3;
      scrollEvents.push(Date.now());
      scrollEvents = scrollEvents.filter((d) => d >= secondAgo);
      if (scrollEvents.length > 5 && this.canLoadMore()) {
        this.runLoadMore();
      }
    });
  }
  /**
   * @param {({ thumbs: { display: string; }; url: string; name: string; }) => void} callback
   * @param {String} type
   */
  show(callback, type = "gallery") {
    this.resetAll();
    this.callback = callback;
    this.type = type;
    this.getPopup().show();
    const hideUploads = type !== "gallery";
    this.dropzoneContainer.classList.toggle("hidden", hideUploads);
    this.uploadButton.classList.toggle("hidden", hideUploads);
    this.uploadHint.classList.toggle("hidden", hideUploads);
    const dropzone = window.$components.firstOnElement(this.container, "dropzone");
    dropzone.toggleActive(!hideUploads);
    if (!this.hasData) {
      this.loadGallery();
      this.hasData = true;
    }
  }
  hide() {
    this.getPopup().hide();
  }
  /**
   * @returns {Popup}
   */
  getPopup() {
    return window.$components.firstOnElement(this.popupEl, "popup");
  }
  async loadGallery() {
    const params = {
      page: this.page,
      search: this.searchInput.value || null,
      uploaded_to: this.uploadedTo,
      filter_type: this.filter === "all" ? null : this.filter
    };
    const { data: html } = await window.$http.get(`images/${this.type}`, params);
    if (params.page === 1) {
      this.listContainer.innerHTML = "";
    }
    this.addReturnedHtmlElementsToList(html);
    removeLoading(this.listContainer);
  }
  addReturnedHtmlElementsToList(html) {
    const el2 = document.createElement("div");
    el2.innerHTML = html;
    const loadMore = el2.querySelector(".load-more");
    if (loadMore) {
      loadMore.remove();
      this.loadMore.innerHTML = loadMore.innerHTML;
    }
    this.loadMore.toggleAttribute("hidden", !loadMore);
    window.$components.init(el2);
    for (const child of [...el2.children]) {
      this.listContainer.appendChild(child);
    }
  }
  setActiveFilterTab(filterName) {
    for (const tab of this.filterTabs) {
      const selected = tab.dataset.filter === filterName;
      tab.setAttribute("aria-selected", selected ? "true" : "false");
    }
  }
  resetAll() {
    this.resetState();
    this.resetListView();
    this.resetSearchView();
    this.resetEditForm();
    this.setActiveFilterTab("all");
    this.selectButton.classList.add("hidden");
  }
  resetSearchView() {
    this.searchInput.value = "";
    this.cancelSearch.toggleAttribute("hidden", true);
  }
  resetEditForm() {
    this.formContainer.innerHTML = "";
    this.formContainerPlaceholder.removeAttribute("hidden");
  }
  resetListView() {
    showLoading(this.listContainer);
    this.page = 1;
  }
  refreshGallery() {
    this.resetListView();
    this.loadGallery();
  }
  async onImageSelectEvent(event) {
    let image = JSON.parse(event.detail.data);
    const isDblClick = image && image.id === this.lastSelected.id && Date.now() - this.lastSelectedTime < 400;
    const alreadySelected = event.target.classList.contains("selected");
    [...this.listContainer.querySelectorAll(".selected")].forEach((el2) => {
      el2.classList.remove("selected");
    });
    if (!alreadySelected && !isDblClick) {
      event.target.classList.add("selected");
      image = await this.loadImageEditForm(image.id);
    } else if (!isDblClick) {
      this.resetEditForm();
    } else if (isDblClick) {
      image = this.lastSelected;
    }
    this.selectButton.classList.toggle("hidden", alreadySelected);
    if (isDblClick && this.callback) {
      this.callback(image);
      this.hide();
    }
    this.lastSelected = image;
    this.lastSelectedTime = Date.now();
  }
  async loadImageEditForm(imageId, requestDelete = false) {
    if (!requestDelete) {
      this.formContainer.innerHTML = "";
    }
    const params = requestDelete ? { delete: true } : {};
    const { data: formHtml } = await window.$http.get(`/images/edit/${imageId}`, params);
    this.formContainer.innerHTML = formHtml;
    this.formContainerPlaceholder.setAttribute("hidden", "");
    window.$components.init(this.formContainer);
    const imageDataEl = this.formContainer.querySelector("#image-manager-form-image-data");
    return JSON.parse(imageDataEl.text);
  }
  runLoadMore() {
    showLoading(this.loadMore);
    this.page += 1;
    this.loadGallery();
  }
  canLoadMore() {
    return this.loadMore.querySelector("button") && !this.loadMore.hasAttribute("hidden");
  }
  async rebuildThumbnails(imageId) {
    try {
      const response = await window.$http.put(`/images/${imageId}/rebuild-thumbnails`);
      window.$events.success(response.data);
      this.refreshGallery();
    } catch (err) {
      window.$events.showResponseError(err);
    }
  }
};

// resources/js/components/image-picker.js
var ImagePicker = class extends Component {
  setup() {
    this.imageElem = this.$refs.image;
    this.imageInput = this.$refs.imageInput;
    this.resetInput = this.$refs.resetInput;
    this.removeInput = this.$refs.removeInput;
    this.resetButton = this.$refs.resetButton;
    this.removeButton = this.$refs.removeButton || null;
    this.defaultImage = this.$opts.defaultImage;
    this.setupListeners();
  }
  setupListeners() {
    this.resetButton.addEventListener("click", this.reset.bind(this));
    if (this.removeButton) {
      this.removeButton.addEventListener("click", this.removeImage.bind(this));
    }
    this.imageInput.addEventListener("change", this.fileInputChange.bind(this));
  }
  fileInputChange() {
    this.resetInput.setAttribute("disabled", "disabled");
    if (this.removeInput) {
      this.removeInput.setAttribute("disabled", "disabled");
    }
    for (const file of this.imageInput.files) {
      this.imageElem.src = window.URL.createObjectURL(file);
    }
    this.imageElem.classList.remove("none");
  }
  reset() {
    this.imageInput.value = "";
    this.imageElem.src = this.defaultImage;
    this.resetInput.removeAttribute("disabled");
    if (this.removeInput) {
      this.removeInput.setAttribute("disabled", "disabled");
    }
    this.imageElem.classList.remove("none");
  }
  removeImage() {
    this.imageInput.value = "";
    this.imageElem.classList.add("none");
    this.removeInput.removeAttribute("disabled");
    this.resetInput.setAttribute("disabled", "disabled");
  }
};

// resources/js/components/list-sort-control.js
var ListSortControl = class extends Component {
  setup() {
    this.elem = this.$el;
    this.menu = this.$refs.menu;
    this.sortInput = this.$refs.sort;
    this.orderInput = this.$refs.order;
    this.form = this.$refs.form;
    this.setupListeners();
  }
  setupListeners() {
    this.menu.addEventListener("click", (event) => {
      if (event.target.closest("[data-sort-value]") !== null) {
        this.sortOptionClick(event);
      }
    });
    this.elem.addEventListener("click", (event) => {
      if (event.target.closest("[data-sort-dir]") !== null) {
        this.sortDirectionClick(event);
      }
    });
  }
  sortOptionClick(event) {
    const sortOption = event.target.closest("[data-sort-value]");
    this.sortInput.value = sortOption.getAttribute("data-sort-value");
    event.preventDefault();
    this.form.submit();
  }
  sortDirectionClick(event) {
    const currentDir = this.orderInput.value;
    this.orderInput.value = currentDir === "asc" ? "desc" : "asc";
    event.preventDefault();
    this.form.submit();
  }
};

// resources/js/wysiwyg/utils/dom.ts
function el(tag, attrs = {}, children = []) {
  const el2 = document.createElement(tag);
  const attrKeys = Object.keys(attrs);
  for (const attr of attrKeys) {
    if (attrs[attr] !== null) {
      el2.setAttribute(attr, attrs[attr]);
    }
  }
  for (const child of children) {
    if (typeof child === "string") {
      el2.append(document.createTextNode(child));
    } else {
      el2.append(child);
    }
  }
  return el2;
}

// resources/js/components/loading-button.ts
var LoadingButton = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "button");
    __publicField(this, "loadingEl", null);
  }
  setup() {
    this.button = this.$el;
    const form = this.button.form;
    const action = () => {
      setTimeout(() => this.showLoadingState(), 10);
    };
    this.button.addEventListener("click", action);
    if (form) {
      form.addEventListener("submit", action);
    }
  }
  showLoadingState() {
    this.button.disabled = true;
    if (!this.loadingEl) {
      this.loadingEl = el("div", { class: "inline block" });
      showLoading(this.loadingEl);
      this.button.after(this.loadingEl);
    }
  }
};

// resources/js/components/markdown-editor.js
var MarkdownEditor = class extends Component {
  setup() {
    this.elem = this.$el;
    this.pageId = this.$opts.pageId;
    this.textDirection = this.$opts.textDirection;
    this.imageUploadErrorText = this.$opts.imageUploadErrorText;
    this.serverUploadLimitText = this.$opts.serverUploadLimitText;
    this.display = this.$refs.display;
    this.input = this.$refs.input;
    this.divider = this.$refs.divider;
    this.displayWrap = this.$refs.displayWrap;
    const { settingContainer } = this.$refs;
    const settingInputs = settingContainer.querySelectorAll('input[type="checkbox"]');
    this.editor = null;
    window.importVersioned("markdown").then((markdown) => {
      return markdown.init({
        pageId: this.pageId,
        container: this.elem,
        displayEl: this.display,
        inputEl: this.input,
        drawioUrl: this.getDrawioUrl(),
        settingInputs: Array.from(settingInputs),
        text: {
          serverUploadLimit: this.serverUploadLimitText,
          imageUploadError: this.imageUploadErrorText
        }
      });
    }).then((editor) => {
      this.editor = editor;
      this.setupListeners();
      this.emitEditorEvents();
      this.scrollToTextIfNeeded();
      this.editor.actions.updateAndRender();
    });
  }
  emitEditorEvents() {
    window.$events.emitPublic(this.elem, "editor-markdown::setup", {
      markdownIt: this.editor.markdown.getRenderer(),
      displayEl: this.display,
      cmEditorView: this.editor.cm
    });
  }
  setupListeners() {
    this.elem.addEventListener("click", (event) => {
      const button = event.target.closest("button[data-action]");
      if (button === null) return;
      const action = button.getAttribute("data-action");
      if (action === "insertImage") this.editor.actions.showImageInsert();
      if (action === "insertLink") this.editor.actions.showLinkSelector();
      if (action === "insertDrawing" && (event.ctrlKey || event.metaKey)) {
        this.editor.actions.showImageManager();
        return;
      }
      if (action === "insertDrawing") this.editor.actions.startDrawing();
      if (action === "fullscreen") this.editor.actions.fullScreen();
    });
    this.elem.addEventListener("click", (event) => {
      const toolbarLabel = event.target.closest(".editor-toolbar-label");
      if (!toolbarLabel) return;
      const currentActiveSections = this.elem.querySelectorAll(".markdown-editor-wrap");
      for (const activeElem of currentActiveSections) {
        activeElem.classList.remove("active");
      }
      toolbarLabel.closest(".markdown-editor-wrap").classList.add("active");
    });
    this.handleDividerDrag();
  }
  handleDividerDrag() {
    this.divider.addEventListener("pointerdown", () => {
      const wrapRect = this.elem.getBoundingClientRect();
      const moveListener = (event) => {
        const xRel = event.pageX - wrapRect.left;
        const xPct = Math.min(Math.max(20, Math.floor(xRel / wrapRect.width * 100)), 80);
        this.displayWrap.style.flexBasis = `${100 - xPct}%`;
        this.editor.settings.set("editorWidth", xPct);
      };
      const upListener = () => {
        window.removeEventListener("pointermove", moveListener);
        window.removeEventListener("pointerup", upListener);
        this.display.style.pointerEvents = null;
        document.body.style.userSelect = null;
      };
      this.display.style.pointerEvents = "none";
      document.body.style.userSelect = "none";
      window.addEventListener("pointermove", moveListener);
      window.addEventListener("pointerup", upListener);
    });
    const widthSetting = this.editor.settings.get("editorWidth");
    if (widthSetting) {
      this.displayWrap.style.flexBasis = `${100 - widthSetting}%`;
    }
  }
  scrollToTextIfNeeded() {
    const queryParams = new URL(window.location).searchParams;
    const scrollText = queryParams.get("content-text");
    if (scrollText) {
      this.editor.actions.scrollToText(scrollText);
    }
  }
  /**
   * Get the URL for the configured drawio instance.
   * @returns {String}
   */
  getDrawioUrl() {
    const drawioAttrEl = document.querySelector("[drawio-url]");
    if (!drawioAttrEl) {
      return "";
    }
    return drawioAttrEl.getAttribute("drawio-url") || "";
  }
  /**
   * Get the content of this editor.
   * Used by the parent page editor component.
   * @return {Promise<{html: String, markdown: String}>}
   */
  async getContent() {
    return this.editor.actions.getContent();
  }
};

// resources/js/components/new-user-password.js
var NewUserPassword = class extends Component {
  setup() {
    this.container = this.$el;
    this.inputContainer = this.$refs.inputContainer;
    this.inviteOption = this.container.querySelector("input[name=send_invite]");
    if (this.inviteOption) {
      this.inviteOption.addEventListener("change", this.inviteOptionChange.bind(this));
      this.inviteOptionChange();
    }
  }
  inviteOptionChange() {
    const inviting = this.inviteOption.value === "true";
    const passwordBoxes = this.container.querySelectorAll("input[type=password]");
    for (const input of passwordBoxes) {
      input.disabled = inviting;
    }
    this.inputContainer.style.display = inviting ? "none" : "block";
  }
};

// resources/js/components/notification.js
var Notification = class extends Component {
  setup() {
    this.container = this.$el;
    this.type = this.$opts.type;
    this.textElem = this.container.querySelector("span");
    this.autoHide = this.$opts.autoHide === "true";
    this.initialShow = this.$opts.show === "true";
    this.container.style.display = "grid";
    window.$events.listen(this.type, (text) => {
      this.show(text);
    });
    this.container.addEventListener("click", this.hide.bind(this));
    if (this.initialShow) {
      setTimeout(() => this.show(this.textElem.textContent), 100);
    }
    this.hideCleanup = this.hideCleanup.bind(this);
  }
  show(textToShow = "") {
    this.container.removeEventListener("transitionend", this.hideCleanup);
    this.textElem.textContent = textToShow;
    this.container.style.display = "grid";
    setTimeout(() => {
      this.container.classList.add("showing");
    }, 1);
    if (this.autoHide) {
      const words = textToShow.split(" ").length;
      const timeToShow = Math.max(2e3, 1e3 + 250 * words);
      setTimeout(this.hide.bind(this), timeToShow);
    }
  }
  hide() {
    this.container.classList.remove("showing");
    this.container.addEventListener("transitionend", this.hideCleanup);
  }
  hideCleanup() {
    this.container.style.display = "none";
    this.container.removeEventListener("transitionend", this.hideCleanup);
  }
};

// resources/js/components/optional-input.js
var OptionalInput = class extends Component {
  setup() {
    this.removeButton = this.$refs.remove;
    this.showButton = this.$refs.show;
    this.input = this.$refs.input;
    this.setupListeners();
  }
  setupListeners() {
    onSelect(this.removeButton, () => {
      this.input.value = "";
      this.input.classList.add("hidden");
      this.removeButton.classList.add("hidden");
      this.showButton.classList.remove("hidden");
    });
    onSelect(this.showButton, () => {
      this.input.classList.remove("hidden");
      this.removeButton.classList.remove("hidden");
      this.showButton.classList.add("hidden");
    });
  }
};

// resources/js/components/page-comment.ts
var PageComment = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "commentId");
    __publicField(this, "commentLocalId");
    __publicField(this, "deletedText");
    __publicField(this, "updatedText");
    __publicField(this, "archiveText");
    __publicField(this, "wysiwygEditor", null);
    __publicField(this, "wysiwygTextDirection");
    __publicField(this, "container");
    __publicField(this, "contentContainer");
    __publicField(this, "form");
    __publicField(this, "formCancel");
    __publicField(this, "editButton");
    __publicField(this, "deleteButton");
    __publicField(this, "replyButton");
    __publicField(this, "archiveButton");
    __publicField(this, "input");
  }
  setup() {
    this.commentId = this.$opts.commentId;
    this.commentLocalId = this.$opts.commentLocalId;
    this.deletedText = this.$opts.deletedText;
    this.updatedText = this.$opts.updatedText;
    this.archiveText = this.$opts.archiveText;
    this.wysiwygTextDirection = this.$opts.wysiwygTextDirection;
    this.container = this.$el;
    this.contentContainer = this.$refs.contentContainer;
    this.form = this.$refs.form;
    this.formCancel = this.$refs.formCancel;
    this.editButton = this.$refs.editButton;
    this.deleteButton = this.$refs.deleteButton;
    this.replyButton = this.$refs.replyButton;
    this.archiveButton = this.$refs.archiveButton;
    this.input = this.$refs.input;
    this.setupListeners();
  }
  setupListeners() {
    if (this.replyButton) {
      const data = {
        id: this.commentLocalId,
        element: this.container
      };
      this.replyButton.addEventListener("click", () => this.$emit("reply", data));
    }
    if (this.editButton) {
      this.editButton.addEventListener("click", this.startEdit.bind(this));
      this.form.addEventListener("submit", this.update.bind(this));
      this.formCancel.addEventListener("click", () => this.toggleEditMode(false));
    }
    if (this.deleteButton) {
      this.deleteButton.addEventListener("click", this.delete.bind(this));
    }
    if (this.archiveButton) {
      this.archiveButton.addEventListener("click", this.archive.bind(this));
    }
  }
  toggleEditMode(show2) {
    this.contentContainer.toggleAttribute("hidden", show2);
    this.form.toggleAttribute("hidden", !show2);
  }
  async startEdit() {
    this.toggleEditMode(true);
    if (this.wysiwygEditor) {
      this.wysiwygEditor.focus();
      return;
    }
    const wysiwygModule = await window.importVersioned("wysiwyg");
    const editorContent = this.input.value;
    const container = el("div", { class: "comment-editor-container" });
    this.input.parentElement?.appendChild(container);
    this.input.hidden = true;
    this.wysiwygEditor = wysiwygModule.createBasicEditorInstance(container, editorContent, {
      darkMode: document.documentElement.classList.contains("dark-mode"),
      textDirection: this.$opts.textDirection,
      translations: window.editor_translations
    });
    this.wysiwygEditor.focus();
  }
  async update(event) {
    event.preventDefault();
    const loading = this.showLoading();
    this.form.toggleAttribute("hidden", true);
    const reqData = {
      html: await this.wysiwygEditor?.getContentAsHtml() || ""
    };
    try {
      const resp = await window.$http.put(`/comment/${this.commentId}`, reqData);
      const newComment = htmlToDom(resp.data);
      this.container.replaceWith(newComment);
      window.$events.success(this.updatedText);
    } catch (err) {
      console.error(err);
      if (err instanceof HttpError) {
        window.$events.showValidationErrors(err);
      }
      this.form.toggleAttribute("hidden", false);
      loading.remove();
    }
  }
  async delete() {
    this.showLoading();
    await window.$http.delete(`/comment/${this.commentId}`);
    this.$emit("delete");
    const branch = this.container.closest(".comment-branch");
    if (branch instanceof HTMLElement) {
      const refs = window.$components.allWithinElement(branch, "page-comment-reference");
      for (const ref of refs) {
        ref.hideMarker();
      }
      branch.remove();
    }
    window.$events.success(this.deletedText);
  }
  async archive() {
    this.showLoading();
    const isArchived = this.archiveButton.dataset.isArchived === "true";
    const action = isArchived ? "unarchive" : "archive";
    const response = await window.$http.put(`/comment/${this.commentId}/${action}`);
    window.$events.success(this.archiveText);
    const eventData = { new_thread_dom: htmlToDom(response.data) };
    this.$emit(action, eventData);
    const branch = this.container.closest(".comment-branch");
    const references = window.$components.allWithinElement(branch, "page-comment-reference");
    for (const reference of references) {
      reference.hideMarker();
    }
    branch.remove();
  }
  showLoading() {
    const loading = getLoading();
    loading.classList.add("px-l");
    this.container.append(loading);
    return loading;
  }
};

// resources/icons/comment.svg
var comment_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4zM18 14H6v-2h12zm0-3H6V9h12zm0-3H6V6h12z"/><path fill="none" d="M0 0h24v24H0z"/></svg>';

// resources/icons/close.svg
var close_default = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';

// resources/js/components/page-comment-reference.ts
var openMarkerClose = null;
var PageCommentReference = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "link");
    __publicField(this, "reference");
    __publicField(this, "markerWrap", null);
    __publicField(this, "viewCommentText");
    __publicField(this, "jumpToThreadText");
    __publicField(this, "closeText");
  }
  setup() {
    this.link = this.$el;
    this.reference = this.$opts.reference;
    this.viewCommentText = this.$opts.viewCommentText;
    this.jumpToThreadText = this.$opts.jumpToThreadText;
    this.closeText = this.$opts.closeText;
    this.showForDisplay();
    window.addEventListener("editor-toolbox-change", (event) => {
      const tabName = event.detail.tab;
      const isOpen = event.detail.open;
      if (tabName === "comments" && isOpen && this.link.checkVisibility()) {
        this.showForEditor();
      } else {
        this.hideMarker();
      }
    });
    window.addEventListener("toggle", (event) => {
      if (event.target instanceof HTMLElement && event.target.contains(this.link)) {
        window.requestAnimationFrame(() => {
          if (this.link.checkVisibility()) {
            this.showForEditor();
          } else {
            this.hideMarker();
          }
        });
      }
    }, { capture: true });
    window.addEventListener("tabs-change", (event) => {
      const sectionId = event.detail.showing;
      if (!sectionId.startsWith("comment-tab-panel")) {
        return;
      }
      const panel = document.getElementById(sectionId);
      if (panel?.contains(this.link)) {
        this.showForDisplay();
      } else {
        this.hideMarker();
      }
    });
  }
  showForDisplay() {
    const pageContentArea = document.querySelector(".page-content");
    if (pageContentArea instanceof HTMLElement && this.link.checkVisibility()) {
      this.updateMarker(pageContentArea);
    }
  }
  showForEditor() {
    const contentWrap = document.querySelector(".editor-content-wrap");
    if (contentWrap instanceof HTMLElement) {
      this.updateMarker(contentWrap);
    }
    const onChange = () => {
      this.hideMarker();
      setTimeout(() => {
        window.$events.remove("editor-html-change", onChange);
      }, 1);
    };
    window.$events.listen("editor-html-change", onChange);
  }
  updateMarker(contentContainer) {
    this.link.classList.remove("outdated", "missing");
    if (this.markerWrap) {
      this.markerWrap.remove();
    }
    const [refId, refHash, refRange] = this.reference.split(":");
    const refEl = document.getElementById(refId);
    if (!refEl) {
      this.link.classList.add("outdated", "missing");
      return;
    }
    const actualHash = hashElement(refEl);
    if (actualHash !== refHash) {
      this.link.classList.add("outdated");
    }
    const marker = el("button", {
      type: "button",
      class: "content-comment-marker",
      title: this.viewCommentText
    });
    marker.innerHTML = comment_default;
    marker.addEventListener("click", (event) => {
      this.showCommentAtMarker(marker);
    });
    this.markerWrap = el("div", {
      class: "content-comment-highlight"
    }, [marker]);
    contentContainer.append(this.markerWrap);
    this.positionMarker(refEl, refRange);
    this.link.href = `#${refEl.id}`;
    this.link.addEventListener("click", (event) => {
      event.preventDefault();
      scrollAndHighlightElement(refEl);
    });
    const debouncedReposition = debounce(() => {
      this.positionMarker(refEl, refRange);
    }, 50, false).bind(this);
    window.addEventListener("resize", debouncedReposition);
  }
  positionMarker(targetEl, range) {
    if (!this.markerWrap) {
      return;
    }
    const markerParent = this.markerWrap.parentElement;
    const parentBounds = markerParent.getBoundingClientRect();
    let targetBounds = targetEl.getBoundingClientRect();
    const [rangeStart, rangeEnd] = range.split("-");
    if (rangeStart && rangeEnd) {
      const range2 = new Range();
      const relStart = findTargetNodeAndOffset(targetEl, Number(rangeStart));
      const relEnd = findTargetNodeAndOffset(targetEl, Number(rangeEnd));
      if (relStart && relEnd) {
        range2.setStart(relStart.node, relStart.offset);
        range2.setEnd(relEnd.node, relEnd.offset);
        targetBounds = range2.getBoundingClientRect();
      }
    }
    const relLeft = targetBounds.left - parentBounds.left;
    const relTop = targetBounds.top - parentBounds.top + markerParent.scrollTop;
    this.markerWrap.style.left = `${relLeft}px`;
    this.markerWrap.style.top = `${relTop}px`;
    this.markerWrap.style.width = `${targetBounds.width}px`;
    this.markerWrap.style.height = `${targetBounds.height}px`;
  }
  hideMarker() {
    if (openMarkerClose) {
      openMarkerClose();
    }
    this.markerWrap?.remove();
    this.markerWrap = null;
  }
  showCommentAtMarker(marker) {
    if (openMarkerClose) {
      openMarkerClose();
    }
    marker.hidden = true;
    const commentBox = this.link.closest(".comment-box");
    const readClone = commentBox.closest(".comment-branch").cloneNode(true);
    const toRemove = readClone.querySelectorAll(".actions, form");
    for (const el2 of toRemove) {
      el2.remove();
    }
    const close2 = el("button", { type: "button", title: this.closeText });
    close2.innerHTML = close_default;
    const jump = el("button", { type: "button", "data-action": "jump" }, [this.jumpToThreadText]);
    const commentWindow = el("div", {
      class: "content-comment-window"
    }, [
      el("div", {
        class: "content-comment-window-actions"
      }, [jump, close2]),
      el("div", {
        class: "content-comment-window-content comment-container-compact comment-container-super-compact"
      }, [readClone])
    ]);
    marker.parentElement?.append(commentWindow);
    const closeAction = () => {
      commentWindow.remove();
      marker.hidden = false;
      window.removeEventListener("click", windowCloseAction);
      openMarkerClose = null;
    };
    const windowCloseAction = (event) => {
      if (!marker.parentElement.contains(event.target)) {
        closeAction();
      }
    };
    window.addEventListener("click", windowCloseAction);
    openMarkerClose = closeAction;
    close2.addEventListener("click", closeAction.bind(this));
    jump.addEventListener("click", () => {
      closeAction();
      commentBox.scrollIntoView({ behavior: "smooth" });
      const highlightTarget = commentBox.querySelector(".header");
      highlightTarget.classList.add("anim-highlight");
      highlightTarget.addEventListener("animationend", () => highlightTarget.classList.remove("anim-highlight"));
    });
    const commentWindowBounds = commentWindow.getBoundingClientRect();
    const contentBounds = document.querySelector(".page-content")?.getBoundingClientRect();
    if (contentBounds && commentWindowBounds.right > contentBounds.right) {
      const diff = commentWindowBounds.right - contentBounds.right;
      commentWindow.style.left = `-${diff}px`;
    }
  }
};

// resources/js/components/tabs.ts
var Tabs = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "container");
    __publicField(this, "tabList");
    __publicField(this, "tabs");
    __publicField(this, "panels");
    __publicField(this, "activeUnder");
    __publicField(this, "active", null);
  }
  setup() {
    this.container = this.$el;
    this.tabList = this.container.querySelector('[role="tablist"]');
    this.tabs = Array.from(this.tabList.querySelectorAll('[role="tab"]'));
    this.panels = Array.from(this.container.querySelectorAll(':scope > [role="tabpanel"], :scope > * > [role="tabpanel"]'));
    this.activeUnder = this.$opts.activeUnder ? Number(this.$opts.activeUnder) : 1e4;
    this.container.addEventListener("click", (event) => {
      const tab = event.target.closest('[role="tab"]');
      if (tab instanceof HTMLElement && this.tabs.includes(tab)) {
        this.show(tab.getAttribute("aria-controls") || "");
      }
    });
    window.addEventListener("resize", this.updateActiveState.bind(this), {
      passive: true
    });
    this.updateActiveState();
  }
  show(sectionId) {
    for (const panel of this.panels) {
      panel.toggleAttribute("hidden", panel.id !== sectionId);
    }
    for (const tab of this.tabs) {
      const tabSection = tab.getAttribute("aria-controls");
      const selected = tabSection === sectionId;
      tab.setAttribute("aria-selected", selected ? "true" : "false");
    }
    const data = { showing: sectionId };
    this.$emit("change", data);
  }
  updateActiveState() {
    const active = window.innerWidth < this.activeUnder;
    if (active === this.active) {
      return;
    }
    if (active) {
      this.activate();
    } else {
      this.deactivate();
    }
    this.active = active;
  }
  activate() {
    const panelToShow = this.panels.find((p) => !p.hasAttribute("hidden")) || this.panels[0];
    this.show(panelToShow.id);
    this.tabList.toggleAttribute("hidden", false);
  }
  deactivate() {
    for (const panel of this.panels) {
      panel.removeAttribute("hidden");
    }
    for (const tab of this.tabs) {
      tab.setAttribute("aria-selected", "false");
    }
    this.tabList.toggleAttribute("hidden", true);
  }
};

// resources/js/components/page-comments.ts
var PageComments = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "elem");
    __publicField(this, "pageId");
    __publicField(this, "container");
    __publicField(this, "commentCountBar");
    __publicField(this, "activeTab");
    __publicField(this, "archivedTab");
    __publicField(this, "addButtonContainer");
    __publicField(this, "archiveContainer");
    __publicField(this, "activeContainer");
    __publicField(this, "replyToRow");
    __publicField(this, "referenceRow");
    __publicField(this, "formContainer");
    __publicField(this, "form");
    __publicField(this, "formInput");
    __publicField(this, "formReplyLink");
    __publicField(this, "formReferenceLink");
    __publicField(this, "addCommentButton");
    __publicField(this, "hideFormButton");
    __publicField(this, "removeReplyToButton");
    __publicField(this, "removeReferenceButton");
    __publicField(this, "wysiwygTextDirection");
    __publicField(this, "wysiwygEditor", null);
    __publicField(this, "createdText");
    __publicField(this, "countText");
    __publicField(this, "archivedCountText");
    __publicField(this, "parentId", null);
    __publicField(this, "contentReference", "");
    __publicField(this, "formReplyText", "");
  }
  setup() {
    this.elem = this.$el;
    this.pageId = Number(this.$opts.pageId);
    this.container = this.$refs.commentContainer;
    this.commentCountBar = this.$refs.commentCountBar;
    this.activeTab = this.$refs.activeTab;
    this.archivedTab = this.$refs.archivedTab;
    this.addButtonContainer = this.$refs.addButtonContainer;
    this.archiveContainer = this.$refs.archiveContainer;
    this.activeContainer = this.$refs.activeContainer;
    this.replyToRow = this.$refs.replyToRow;
    this.referenceRow = this.$refs.referenceRow;
    this.formContainer = this.$refs.formContainer;
    this.form = this.$refs.form;
    this.formInput = this.$refs.formInput;
    this.formReplyLink = this.$refs.formReplyLink;
    this.formReferenceLink = this.$refs.formReferenceLink;
    this.addCommentButton = this.$refs.addCommentButton;
    this.hideFormButton = this.$refs.hideFormButton;
    this.removeReplyToButton = this.$refs.removeReplyToButton;
    this.removeReferenceButton = this.$refs.removeReferenceButton;
    this.wysiwygTextDirection = this.$opts.wysiwygTextDirection;
    this.createdText = this.$opts.createdText;
    this.countText = this.$opts.countText;
    this.archivedCountText = this.$opts.archivedCountText;
    this.formReplyText = this.formReplyLink?.textContent || "";
    this.setupListeners();
  }
  setupListeners() {
    this.elem.addEventListener("page-comment-delete", () => {
      setTimeout(() => {
        this.updateCount();
        this.hideForm();
      }, 1);
    });
    this.elem.addEventListener("page-comment-reply", (event) => {
      this.setReply(event.detail.id, event.detail.element);
    });
    this.elem.addEventListener("page-comment-archive", (event) => {
      this.archiveContainer.append(event.detail.new_thread_dom);
      setTimeout(() => this.updateCount(), 1);
    });
    this.elem.addEventListener("page-comment-unarchive", (event) => {
      this.container.append(event.detail.new_thread_dom);
      setTimeout(() => this.updateCount(), 1);
    });
    if (this.form) {
      this.removeReplyToButton.addEventListener("click", this.removeReplyTo.bind(this));
      this.removeReferenceButton.addEventListener("click", () => this.setContentReference(""));
      this.hideFormButton.addEventListener("click", this.hideForm.bind(this));
      this.addCommentButton.addEventListener("click", this.showForm.bind(this));
      this.form.addEventListener("submit", this.saveComment.bind(this));
    }
  }
  async saveComment(event) {
    event.preventDefault();
    event.stopPropagation();
    const loading = getLoading();
    loading.classList.add("px-l");
    this.form.after(loading);
    this.form.toggleAttribute("hidden", true);
    const reqData = {
      html: await this.wysiwygEditor?.getContentAsHtml() || "",
      parent_id: this.parentId || null,
      content_ref: this.contentReference
    };
    window.$http.post(`/comment/${this.pageId}`, reqData).then((resp) => {
      const newElem = htmlToDom(resp.data);
      if (reqData.parent_id) {
        this.formContainer.after(newElem);
      } else {
        this.container.append(newElem);
      }
      const refs = window.$components.allWithinElement(newElem, "page-comment-reference");
      for (const ref of refs) {
        ref.showForDisplay();
      }
      window.$events.success(this.createdText);
      this.hideForm();
      this.updateCount();
    }).catch((err) => {
      this.form.toggleAttribute("hidden", false);
      window.$events.showValidationErrors(err);
    });
    this.form.toggleAttribute("hidden", false);
    loading.remove();
  }
  updateCount() {
    const activeCount = this.getActiveThreadCount();
    this.activeTab.textContent = window.$trans.choice(this.countText, activeCount);
    const archivedCount = this.getArchivedThreadCount();
    this.archivedTab.textContent = window.$trans.choice(this.archivedCountText, archivedCount);
  }
  resetForm() {
    this.removeEditor();
    this.formInput.value = "";
    this.parentId = null;
    this.replyToRow.toggleAttribute("hidden", true);
    this.container.append(this.formContainer);
    this.setContentReference("");
  }
  showForm() {
    this.removeEditor();
    this.formContainer.toggleAttribute("hidden", false);
    this.addButtonContainer.toggleAttribute("hidden", true);
    this.formContainer.scrollIntoView({ behavior: "smooth", block: "nearest" });
    this.loadEditor();
    const tabs = window.$components.firstOnElement(this.elem, "tabs");
    if (tabs instanceof Tabs && this.formContainer.closest("#comment-tab-panel-active")) {
      tabs.show("comment-tab-panel-active");
    }
  }
  hideForm() {
    this.resetForm();
    this.formContainer.toggleAttribute("hidden", true);
    if (this.getActiveThreadCount() > 0) {
      this.activeContainer.append(this.addButtonContainer);
    } else {
      this.commentCountBar.append(this.addButtonContainer);
    }
    this.addButtonContainer.toggleAttribute("hidden", false);
  }
  async loadEditor() {
    if (this.wysiwygEditor) {
      this.wysiwygEditor.focus();
      return;
    }
    const wysiwygModule = await window.importVersioned("wysiwyg");
    const container = el("div", { class: "comment-editor-container" });
    this.formInput.parentElement?.appendChild(container);
    this.formInput.hidden = true;
    this.wysiwygEditor = wysiwygModule.createBasicEditorInstance(container, "<p></p>", {
      darkMode: document.documentElement.classList.contains("dark-mode"),
      textDirection: this.wysiwygTextDirection,
      translations: window.editor_translations
    });
    this.wysiwygEditor.focus();
  }
  removeEditor() {
    if (this.wysiwygEditor) {
      this.wysiwygEditor.remove();
      this.wysiwygEditor = null;
    }
  }
  getActiveThreadCount() {
    return this.container.querySelectorAll(":scope > .comment-branch:not([hidden])").length;
  }
  getArchivedThreadCount() {
    return this.archiveContainer.querySelectorAll(":scope > .comment-branch").length;
  }
  setReply(commentLocalId, commentElement) {
    const targetFormLocation = commentElement.closest(".comment-branch").querySelector(".comment-branch-children");
    targetFormLocation.append(this.formContainer);
    this.showForm();
    this.parentId = Number(commentLocalId);
    this.replyToRow.toggleAttribute("hidden", false);
    this.formReplyLink.textContent = this.formReplyText.replace("1234", String(this.parentId));
    this.formReplyLink.href = `#comment${this.parentId}`;
  }
  removeReplyTo() {
    this.parentId = null;
    this.replyToRow.toggleAttribute("hidden", true);
    this.container.append(this.formContainer);
    this.showForm();
  }
  startNewComment(contentReference) {
    this.resetForm();
    this.showForm();
    this.setContentReference(contentReference);
  }
  setContentReference(reference) {
    this.contentReference = reference;
    this.referenceRow.toggleAttribute("hidden", !Boolean(reference));
    const [id] = reference.split(":");
    this.formReferenceLink.href = `#${id}`;
    this.formReferenceLink.onclick = function(event) {
      event.preventDefault();
      const el2 = document.getElementById(id);
      if (el2) {
        scrollAndHighlightElement(el2);
      }
    };
  }
};

// resources/js/components/page-display.js
function toggleAnchorHighlighting(elementId, shouldHighlight) {
  forEach(`#page-navigation a[href="#${elementId}"]`, (anchor) => {
    anchor.closest("li").classList.toggle("current-heading", shouldHighlight);
  });
}
function headingVisibilityChange(entries) {
  for (const entry of entries) {
    const isVisible = entry.intersectionRatio === 1;
    toggleAnchorHighlighting(entry.target.id, isVisible);
  }
}
function addNavObserver(headings) {
  const intersectOpts = {
    rootMargin: "0px 0px 0px 0px",
    threshold: 1
  };
  const pageNavObserver = new IntersectionObserver(headingVisibilityChange, intersectOpts);
  for (const heading of headings) {
    pageNavObserver.observe(heading);
  }
}
var PageDisplay = class extends Component {
  setup() {
    this.container = this.$el;
    this.pageId = this.$opts.pageId;
    window.importVersioned("code").then((Code) => Code.highlight());
    this.setupNavHighlighting();
    if (window.location.hash) {
      const text = window.location.hash.replace(/%20/g, " ").substring(1);
      this.goToText(text);
    }
    const sidebarPageNav = document.querySelector(".sidebar-page-nav");
    if (sidebarPageNav) {
      onChildEvent(sidebarPageNav, "a", "click", (event, child) => {
        event.preventDefault();
        window.$components.first("tri-layout").showContent();
        const contentId = child.getAttribute("href").substr(1);
        this.goToText(contentId);
        window.history.pushState(null, null, `#${contentId}`);
      });
    }
  }
  goToText(text) {
    const idElem = document.getElementById(text);
    forEach(".page-content [data-highlighted]", (elem2) => {
      elem2.removeAttribute("data-highlighted");
      elem2.style.backgroundColor = null;
    });
    if (idElem !== null) {
      scrollAndHighlightElement(idElem);
    } else {
      const textElem = findText(".page-content > div > *", text);
      if (textElem) {
        scrollAndHighlightElement(textElem);
      }
    }
  }
  setupNavHighlighting() {
    const pageNav = document.querySelector(".sidebar-page-nav");
    const headings = document.querySelector(".page-content").querySelectorAll("h1, h2, h3, h4, h5, h6");
    if (headings.length > 0 && pageNav !== null) {
      addNavObserver(headings);
    }
  }
};

// resources/js/services/dates.ts
function utcTimeStampToLocalTime(timestamp) {
  const date = new Date(timestamp * 1e3);
  const hours = date.getHours();
  const mins = date.getMinutes();
  return `${(hours > 9 ? "" : "0") + hours}:${(mins > 9 ? "" : "0") + mins}`;
}

// resources/js/components/page-editor.js
var PageEditor = class extends Component {
  setup() {
    this.draftsEnabled = this.$opts.draftsEnabled === "true";
    this.editorType = this.$opts.editorType;
    this.pageId = Number(this.$opts.pageId);
    this.isNewDraft = this.$opts.pageNewDraft === "true";
    this.hasDefaultTitle = this.$opts.hasDefaultTitle || false;
    this.container = this.$el;
    this.titleElem = this.$refs.titleContainer.querySelector("input");
    this.saveDraftButton = this.$refs.saveDraft;
    this.discardDraftButton = this.$refs.discardDraft;
    this.discardDraftWrap = this.$refs.discardDraftWrap;
    this.deleteDraftButton = this.$refs.deleteDraft;
    this.deleteDraftWrap = this.$refs.deleteDraftWrap;
    this.draftDisplay = this.$refs.draftDisplay;
    this.draftDisplayIcon = this.$refs.draftDisplayIcon;
    this.changelogInput = this.$refs.changelogInput;
    this.changelogDisplay = this.$refs.changelogDisplay;
    this.changelogCounter = this.$refs.changelogCounter;
    this.changeEditorButtons = this.$manyRefs.changeEditor || [];
    this.switchDialogContainer = this.$refs.switchDialog;
    this.deleteDraftDialogContainer = this.$refs.deleteDraftDialog;
    this.draftText = this.$opts.draftText;
    this.autosaveFailText = this.$opts.autosaveFailText;
    this.editingPageText = this.$opts.editingPageText;
    this.draftDiscardedText = this.$opts.draftDiscardedText;
    this.draftDeleteText = this.$opts.draftDeleteText;
    this.draftDeleteFailText = this.$opts.draftDeleteFailText;
    this.setChangelogText = this.$opts.setChangelogText;
    this.autoSave = {
      interval: null,
      frequency: 3e4,
      last: 0,
      pendingChange: false
    };
    this.shownWarningsCache = /* @__PURE__ */ new Set();
    if (this.pageId !== 0 && this.draftsEnabled) {
      window.setTimeout(() => {
        this.startAutoSave();
      }, 1e3);
    }
    this.draftDisplay.innerHTML = this.draftText;
    this.setupListeners();
    this.setInitialFocus();
  }
  setupListeners() {
    window.$events.listen("editor-save-draft", this.saveDraft.bind(this));
    window.$events.listen("editor-save-page", this.savePage.bind(this));
    const onContentChange = () => {
      this.autoSave.pendingChange = true;
    };
    window.$events.listen("editor-html-change", onContentChange);
    window.$events.listen("editor-markdown-change", onContentChange);
    this.titleElem.addEventListener("input", onContentChange);
    const updateChangelogDebounced = debounce(this.updateChangelogDisplay.bind(this), 300, false);
    this.changelogInput.addEventListener("input", () => {
      const count = this.changelogInput.value.length;
      this.changelogCounter.innerText = `${count} / 180`;
      updateChangelogDebounced();
    });
    onSelect(this.saveDraftButton, this.saveDraft.bind(this));
    onSelect(this.discardDraftButton, this.discardDraft.bind(this));
    onSelect(this.deleteDraftButton, this.deleteDraft.bind(this));
    onSelect(this.changeEditorButtons, this.changeEditor.bind(this));
  }
  setInitialFocus() {
    if (this.hasDefaultTitle) {
      this.titleElem.select();
      return;
    }
    window.setTimeout(() => {
      window.$events.emit("editor::focus", "");
    }, 500);
  }
  startAutoSave() {
    this.autoSave.interval = window.setInterval(this.runAutoSave.bind(this), this.autoSave.frequency);
  }
  runAutoSave() {
    const savedRecently = Date.now() - this.autoSave.last < this.autoSave.frequency / 2;
    if (savedRecently || !this.autoSave.pendingChange) {
      return;
    }
    this.saveDraft();
  }
  savePage() {
    this.container.closest("form").requestSubmit();
  }
  async saveDraft() {
    const data = { name: this.titleElem.value.trim() };
    const editorContent = await this.getEditorComponent().getContent();
    Object.assign(data, editorContent);
    let didSave = false;
    try {
      const resp = await window.$http.put(`/ajax/page/${this.pageId}/save-draft`, data);
      if (!this.isNewDraft) {
        this.discardDraftWrap.toggleAttribute("hidden", false);
        this.deleteDraftWrap.toggleAttribute("hidden", false);
      }
      this.draftNotifyChange(`${resp.data.message} ${utcTimeStampToLocalTime(resp.data.timestamp)}`);
      this.autoSave.last = Date.now();
      if (resp.data.warning && !this.shownWarningsCache.has(resp.data.warning)) {
        window.$events.emit("warning", resp.data.warning);
        this.shownWarningsCache.add(resp.data.warning);
      }
      didSave = true;
      this.autoSave.pendingChange = false;
    } catch {
      try {
        const saveKey = `draft-save-fail-${(/* @__PURE__ */ new Date()).toISOString()}`;
        window.localStorage.setItem(saveKey, JSON.stringify(data));
      } catch (lsErr) {
        console.error(lsErr);
      }
      window.$events.emit("error", this.autosaveFailText);
    }
    return didSave;
  }
  draftNotifyChange(text) {
    this.draftDisplay.innerText = text;
    this.draftDisplayIcon.classList.add("visible");
    window.setTimeout(() => {
      this.draftDisplayIcon.classList.remove("visible");
    }, 2e3);
  }
  async discardDraft(notify = true) {
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
    this.draftDisplay.innerText = this.editingPageText;
    this.discardDraftWrap.toggleAttribute("hidden", true);
    window.$events.emit("editor::replace", {
      html: response.data.html,
      markdown: response.data.markdown
    });
    this.titleElem.value = response.data.name;
    window.setTimeout(() => {
      this.startAutoSave();
    }, 1e3);
    if (notify) {
      window.$events.success(this.draftDiscardedText);
    }
  }
  async deleteDraft() {
    const dialog = window.$components.firstOnElement(this.deleteDraftDialogContainer, "confirm-dialog");
    const confirmed = await dialog.show();
    if (!confirmed) {
      return;
    }
    try {
      const discard = this.discardDraft(false);
      const draftDelete = window.$http.delete(`/page-revisions/user-drafts/${this.pageId}`);
      await Promise.all([discard, draftDelete]);
      window.$events.success(this.draftDeleteText);
      this.deleteDraftWrap.toggleAttribute("hidden", true);
    } catch (err) {
      console.error(err);
      window.$events.error(this.draftDeleteFailText);
    }
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
  async changeEditor(event) {
    event.preventDefault();
    const link = event.target.closest("a").href;
    const dialog = window.$components.firstOnElement(this.switchDialogContainer, "confirm-dialog");
    const [saved, confirmed] = await Promise.all([this.saveDraft(), dialog.show()]);
    if (saved && confirmed) {
      window.location = link;
    }
  }
  /**
   * @return {MarkdownEditor|WysiwygEditor|WysiwygEditorTinymce}
   */
  getEditorComponent() {
    return window.$components.first("markdown-editor") || window.$components.first("wysiwyg-editor") || window.$components.first("wysiwyg-editor-tinymce");
  }
};

// resources/js/components/page-picker.js
function toggleElem(elem2, show2) {
  elem2.toggleAttribute("hidden", !show2);
}
var PagePicker = class extends Component {
  setup() {
    this.input = this.$refs.input;
    this.resetButton = this.$refs.resetButton;
    this.selectButton = this.$refs.selectButton;
    this.display = this.$refs.display;
    this.defaultDisplay = this.$refs.defaultDisplay;
    this.buttonSep = this.$refs.buttonSeperator;
    this.selectorEndpoint = this.$opts.selectorEndpoint;
    this.value = this.input.value;
    this.setupListeners();
  }
  setupListeners() {
    this.selectButton.addEventListener("click", this.showPopup.bind(this));
    this.display.parentElement.addEventListener("click", this.showPopup.bind(this));
    this.display.addEventListener("click", (e) => e.stopPropagation());
    this.resetButton.addEventListener("click", () => {
      this.setValue("", "");
    });
  }
  showPopup() {
    const selectorPopup = window.$components.first("entity-selector-popup");
    selectorPopup.show((entity) => {
      this.setValue(entity.id, entity.name);
    }, {
      initialValue: "",
      searchEndpoint: this.selectorEndpoint,
      entityTypes: "page",
      entityPermission: "view"
    });
  }
  setValue(value, name) {
    this.value = value;
    this.input.value = value;
    this.controlView(name);
  }
  controlView(name) {
    const hasValue = this.value && this.value !== 0;
    toggleElem(this.resetButton, hasValue);
    toggleElem(this.buttonSep, hasValue);
    toggleElem(this.defaultDisplay, !hasValue);
    toggleElem(this.display, hasValue);
    if (hasValue) {
      const id = this.getAssetIdFromVal();
      this.display.textContent = `#${id}, ${name}`;
      this.display.href = window.baseUrl(`/link/${id}`);
    }
  }
  getAssetIdFromVal() {
    return Number(this.value);
  }
};

// resources/js/components/permissions-table.js
var PermissionsTable = class extends Component {
  setup() {
    this.container = this.$el;
    this.cellSelector = this.$opts.cellSelector || "td,th";
    this.rowSelector = this.$opts.rowSelector || "tr";
    for (const toggleAllElem of this.$manyRefs.toggleAll || []) {
      toggleAllElem.addEventListener("click", this.toggleAllClick.bind(this));
    }
    for (const toggleRowElem of this.$manyRefs.toggleRow || []) {
      toggleRowElem.addEventListener("click", this.toggleRowClick.bind(this));
    }
    for (const toggleColElem of this.$manyRefs.toggleColumn || []) {
      toggleColElem.addEventListener("click", this.toggleColumnClick.bind(this));
    }
  }
  toggleAllClick(event) {
    event.preventDefault();
    this.toggleAllInElement(this.container);
  }
  toggleRowClick(event) {
    event.preventDefault();
    this.toggleAllInElement(event.target.closest(this.rowSelector));
  }
  toggleColumnClick(event) {
    event.preventDefault();
    const tableCell = event.target.closest(this.cellSelector);
    const colIndex = Array.from(tableCell.parentElement.children).indexOf(tableCell);
    const tableRows = this.container.querySelectorAll(this.rowSelector);
    const inputsToToggle = [];
    for (const row of tableRows) {
      const targetCell = row.children[colIndex];
      if (targetCell) {
        inputsToToggle.push(...targetCell.querySelectorAll("input[type=checkbox]"));
      }
    }
    this.toggleAllInputs(inputsToToggle);
  }
  toggleAllInElement(domElem) {
    const inputsToToggle = domElem.querySelectorAll("input[type=checkbox]");
    this.toggleAllInputs(inputsToToggle);
  }
  toggleAllInputs(inputsToToggle) {
    const currentState = inputsToToggle.length > 0 ? inputsToToggle[0].checked : false;
    for (const checkbox of inputsToToggle) {
      checkbox.checked = !currentState;
      checkbox.dispatchEvent(new Event("change"));
    }
  }
};

// resources/js/components/pointer.ts
var Pointer = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "showing", false);
    __publicField(this, "isMakingSelection", false);
    __publicField(this, "targetElement", null);
    __publicField(this, "targetSelectionRange", null);
    __publicField(this, "pointer");
    __publicField(this, "linkInput");
    __publicField(this, "linkButton");
    __publicField(this, "includeInput");
    __publicField(this, "includeButton");
    __publicField(this, "sectionModeButton");
    __publicField(this, "commentButton");
    __publicField(this, "modeToggles");
    __publicField(this, "modeSections");
    __publicField(this, "pageId");
  }
  setup() {
    this.pointer = this.$refs.pointer;
    this.linkInput = this.$refs.linkInput;
    this.linkButton = this.$refs.linkButton;
    this.includeInput = this.$refs.includeInput;
    this.includeButton = this.$refs.includeButton;
    this.sectionModeButton = this.$refs.sectionModeButton;
    this.commentButton = this.$refs.commentButton;
    this.modeToggles = this.$manyRefs.modeToggle;
    this.modeSections = this.$manyRefs.modeSection;
    this.pageId = this.$opts.pageId;
    this.setupListeners();
  }
  setupListeners() {
    this.includeButton.addEventListener("click", () => copyTextToClipboard(this.includeInput.value));
    this.linkButton.addEventListener("click", () => copyTextToClipboard(this.linkInput.value));
    onSelect([this.includeInput, this.linkInput], (event) => {
      event.target.select();
      event.stopPropagation();
    });
    onEvents(this.pointer, ["click", "focus"], (event) => {
      event.stopPropagation();
    });
    onEvents(document.body, ["click", "focus"], () => {
      if (!this.showing || this.isMakingSelection) return;
      this.hidePointer();
    });
    onEscapePress(this.pointer, this.hidePointer.bind(this));
    const pageContent = document.querySelector(".page-content");
    onEvents(pageContent, ["mouseup", "keyup"], (event) => {
      event.stopPropagation();
      const targetEl = event.target.closest('[id^="bkmrk"]');
      if (targetEl instanceof HTMLElement && (window.getSelection() || "").toString().length > 0) {
        const xPos = event instanceof MouseEvent ? event.pageX : 0;
        this.showPointerAtTarget(targetEl, xPos, false);
      }
    });
    onSelect(this.sectionModeButton, this.enterSectionSelectMode.bind(this));
    onSelect(this.modeToggles, (event) => {
      const targetToggle = event.target;
      for (const section of this.modeSections) {
        const show2 = !section.contains(targetToggle);
        section.toggleAttribute("hidden", !show2);
      }
      const otherToggle = this.modeToggles.find((b) => b !== targetToggle);
      otherToggle && otherToggle.focus();
    });
    if (this.commentButton) {
      onSelect(this.commentButton, this.createCommentAtPointer.bind(this));
    }
  }
  hidePointer() {
    this.pointer.style.removeProperty("display");
    this.showing = false;
    this.targetElement = null;
    this.targetSelectionRange = null;
  }
  /**
   * Move and display the pointer at the given element, targeting the given screen x-position if possible.
   */
  showPointerAtTarget(element, xPosition, keyboardMode) {
    this.targetElement = element;
    this.targetSelectionRange = window.getSelection()?.getRangeAt(0) || null;
    this.updateDomForTarget(element);
    this.pointer.style.display = "block";
    const targetBounds = element.getBoundingClientRect();
    const pointerBounds = this.pointer.getBoundingClientRect();
    const xTarget = Math.min(Math.max(xPosition, targetBounds.left), targetBounds.right);
    const xOffset = xTarget - pointerBounds.width / 2;
    const yOffset = targetBounds.top - pointerBounds.height - 16;
    this.pointer.style.left = `${xOffset}px`;
    this.pointer.style.top = `${yOffset}px`;
    this.showing = true;
    this.isMakingSelection = true;
    setTimeout(() => {
      this.isMakingSelection = false;
    }, 100);
    const scrollListener = () => {
      this.hidePointer();
      window.removeEventListener("scroll", scrollListener);
    };
    element.parentElement?.insertBefore(this.pointer, element);
    if (!keyboardMode) {
      window.addEventListener("scroll", scrollListener, { passive: true });
    }
  }
  /**
   * Update the pointer inputs/content for the given target element.
   */
  updateDomForTarget(element) {
    const permaLink = window.baseUrl(`/link/${this.pageId}#${element.id}`);
    const includeTag = `{{@${this.pageId}#${element.id}}}`;
    this.linkInput.value = permaLink;
    this.includeInput.value = includeTag;
    const editAnchor = this.pointer.querySelector("#pointer-edit");
    if (editAnchor instanceof HTMLAnchorElement && element) {
      const { editHref } = editAnchor.dataset;
      const elementId = element.id;
      const queryContent = (element.textContent || "").substring(0, 50);
      editAnchor.href = `${editHref}?content-id=${elementId}&content-text=${encodeURIComponent(queryContent)}`;
    }
  }
  enterSectionSelectMode() {
    const sections = Array.from(document.querySelectorAll('.page-content [id^="bkmrk"]'));
    for (const section of sections) {
      section.setAttribute("tabindex", "0");
    }
    sections[0].focus();
    onEnterPress(sections, (event) => {
      this.showPointerAtTarget(event.target, 0, true);
      this.pointer.focus();
    });
  }
  createCommentAtPointer() {
    if (!this.targetElement) {
      return;
    }
    const refId = this.targetElement.id;
    const hash = hashElement(this.targetElement);
    let range = "";
    if (this.targetSelectionRange) {
      const commonContainer = this.targetSelectionRange.commonAncestorContainer;
      if (this.targetElement.contains(commonContainer)) {
        const start = normalizeNodeTextOffsetToParent(
          this.targetSelectionRange.startContainer,
          this.targetSelectionRange.startOffset,
          this.targetElement
        );
        const end = normalizeNodeTextOffsetToParent(
          this.targetSelectionRange.endContainer,
          this.targetSelectionRange.endOffset,
          this.targetElement
        );
        range = `${start}-${end}`;
      }
    }
    const reference = `${refId}:${hash}:${range}`;
    const pageComments = window.$components.first("page-comments");
    pageComments.startNewComment(reference);
  }
};

// resources/js/components/popup.js
var Popup = class extends Component {
  setup() {
    this.container = this.$el;
    this.hideButtons = this.$manyRefs.hide || [];
    this.onkeyup = null;
    this.onHide = null;
    this.setupListeners();
  }
  setupListeners() {
    let lastMouseDownTarget = null;
    this.container.addEventListener("mousedown", (event) => {
      lastMouseDownTarget = event.target;
    });
    this.container.addEventListener("click", (event) => {
      if (event.target === this.container && lastMouseDownTarget === this.container) {
        this.hide();
      }
    });
    onSelect(this.hideButtons, () => this.hide());
  }
  hide(onComplete = null) {
    fadeOut(this.container, 120, onComplete);
    if (this.onkeyup) {
      window.removeEventListener("keyup", this.onkeyup);
      this.onkeyup = null;
    }
    if (this.onHide) {
      this.onHide();
    }
  }
  show(onComplete = null, onHide = null) {
    fadeIn(this.container, 120, onComplete);
    this.onkeyup = (event) => {
      if (event.key === "Escape") {
        this.hide();
      }
    };
    window.addEventListener("keyup", this.onkeyup);
    this.onHide = onHide;
  }
};

// resources/js/components/setting-app-color-scheme.js
var SettingAppColorScheme = class extends Component {
  setup() {
    this.container = this.$el;
    this.mode = this.$opts.mode;
    this.lightContainer = this.$refs.lightContainer;
    this.darkContainer = this.$refs.darkContainer;
    this.container.addEventListener("tabs-change", (event) => {
      const panel = event.detail.showing;
      const newMode = panel === "color-scheme-panel-light" ? "light" : "dark";
      this.handleModeChange(newMode);
    });
    const onInputChange = (event) => {
      this.updateAppColorsFromInputs();
      if (event.target.name.startsWith("setting-app-color")) {
        this.updateLightForInput(event.target);
      }
    };
    this.container.addEventListener("change", onInputChange);
    this.container.addEventListener("input", onInputChange);
  }
  handleModeChange(newMode) {
    this.mode = newMode;
    const isDark = newMode === "dark";
    document.documentElement.classList.toggle("dark-mode", isDark);
    this.updateAppColorsFromInputs();
  }
  updateAppColorsFromInputs() {
    const inputContainer = this.mode === "dark" ? this.darkContainer : this.lightContainer;
    const inputs = inputContainer.querySelectorAll('input[type="color"]');
    for (const input of inputs) {
      const splitName = input.name.split("-");
      const colorPos = splitName.indexOf("color");
      let cssId = splitName.slice(1, colorPos).join("-");
      if (cssId === "app") {
        cssId = "primary";
      }
      const varName = `--color-${cssId}`;
      document.body.style.setProperty(varName, input.value);
    }
  }
  /**
   * Update the 'light' app color variant for the given input.
   * @param {HTMLInputElement} input
   */
  updateLightForInput(input) {
    const lightName = input.name.replace("-color", "-color-light");
    const hexVal = input.value;
    const rgb = this.hexToRgb(hexVal);
    const rgbLightVal = `rgba(${[rgb.r, rgb.g, rgb.b, "0.15"].join(",")})`;
    const lightColorInput = this.container.querySelector(`input[name="${lightName}"][type="hidden"]`);
    lightColorInput.value = rgbLightVal;
  }
  /**
   * Covert a hex color code to rgb components.
   * @attribution https://stackoverflow.com/a/5624139
   * @param {String} hex
   * @returns {{r: Number, g: Number, b: Number}}
   */
  hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return {
      r: result ? parseInt(result[1], 16) : 0,
      g: result ? parseInt(result[2], 16) : 0,
      b: result ? parseInt(result[3], 16) : 0
    };
  }
};

// resources/js/components/setting-color-picker.js
var SettingColorPicker = class extends Component {
  setup() {
    this.colorInput = this.$refs.input;
    this.resetButton = this.$refs.resetButton;
    this.defaultButton = this.$refs.defaultButton;
    this.currentColor = this.$opts.current;
    this.defaultColor = this.$opts.default;
    this.resetButton.addEventListener("click", () => this.setValue(this.currentColor));
    this.defaultButton.addEventListener("click", () => this.setValue(this.defaultColor));
  }
  setValue(value) {
    this.colorInput.value = value;
    this.colorInput.dispatchEvent(new Event("change", { bubbles: true }));
  }
};

// resources/js/components/setting-homepage-control.js
var SettingHomepageControl = class extends Component {
  setup() {
    this.typeControl = this.$refs.typeControl;
    this.pagePickerContainer = this.$refs.pagePickerContainer;
    this.typeControl.addEventListener("change", this.controlPagePickerVisibility.bind(this));
    this.controlPagePickerVisibility();
  }
  controlPagePickerVisibility() {
    const showPagePicker = this.typeControl.value === "page";
    this.pagePickerContainer.style.display = showPagePicker ? "block" : "none";
  }
};

// resources/js/services/dual-lists.ts
function buildListActions(availableList, configuredList) {
  return {
    move_up(item) {
      const list = item.parentNode;
      const index2 = Array.from(list.children).indexOf(item);
      const newIndex2 = Math.max(index2 - 1, 0);
      list.insertBefore(item, list.children[newIndex2] || null);
    },
    move_down(item) {
      const list = item.parentNode;
      const index2 = Array.from(list.children).indexOf(item);
      const newIndex2 = Math.min(index2 + 2, list.children.length);
      list.insertBefore(item, list.children[newIndex2] || null);
    },
    remove(item) {
      availableList.appendChild(item);
    },
    add(item) {
      configuredList.appendChild(item);
    }
  };
}
function sortActionClickListener(actions, onChange) {
  return (event) => {
    const sortItemAction = event.target.closest(".scroll-box-item button[data-action]");
    if (sortItemAction) {
      const sortItem = sortItemAction.closest(".scroll-box-item");
      const action = sortItemAction.dataset.action;
      if (!action) {
        throw new Error("No action defined for clicked button");
      }
      const actionFunction = actions[action];
      actionFunction(sortItem);
      onChange();
    }
  };
}

// resources/js/components/shelf-sort.js
var ShelfSort = class extends Component {
  setup() {
    this.elem = this.$el;
    this.input = this.$refs.input;
    this.shelfBookList = this.$refs.shelfBookList;
    this.allBookList = this.$refs.allBookList;
    this.bookSearchInput = this.$refs.bookSearch;
    this.sortButtonContainer = this.$refs.sortButtonContainer;
    this.lastSort = null;
    this.initSortable();
    this.setupListeners();
  }
  initSortable() {
    const scrollBoxes = this.elem.querySelectorAll(".scroll-box");
    for (const scrollBox of scrollBoxes) {
      new sortable_esm_default(scrollBox, {
        group: "shelf-books",
        ghostClass: "primary-background-light",
        handle: ".handle",
        animation: 150,
        onSort: this.onChange.bind(this)
      });
    }
  }
  setupListeners() {
    const listActions = buildListActions(this.allBookList, this.shelfBookList);
    const sortActionListener = sortActionClickListener(listActions, this.onChange.bind(this));
    this.elem.addEventListener("click", sortActionListener);
    this.bookSearchInput.addEventListener("input", () => {
      this.filterBooksByName(this.bookSearchInput.value);
    });
    this.sortButtonContainer.addEventListener("click", (event) => {
      const button = event.target.closest("button[data-sort]");
      if (button) {
        this.sortShelfBooks(button.dataset.sort);
      }
    });
  }
  /**
   * @param {String} filterVal
   */
  filterBooksByName(filterVal) {
    if (!this.allBookList.style.height) {
      this.allBookList.style.height = `${this.allBookList.getBoundingClientRect().height}px`;
    }
    const books = this.allBookList.children;
    const lowerFilter = filterVal.trim().toLowerCase();
    for (const bookEl of books) {
      const show2 = !filterVal || bookEl.textContent.toLowerCase().includes(lowerFilter);
      bookEl.style.display = show2 ? null : "none";
    }
  }
  onChange() {
    const shelfBookElems = Array.from(this.shelfBookList.querySelectorAll("[data-id]"));
    this.input.value = shelfBookElems.map((elem2) => elem2.getAttribute("data-id")).join(",");
  }
  sortShelfBooks(sortProperty) {
    const books = Array.from(this.shelfBookList.children);
    const reverse = sortProperty === this.lastSort;
    books.sort((bookA, bookB) => {
      const aProp = bookA.dataset[sortProperty].toLowerCase();
      const bProp = bookB.dataset[sortProperty].toLowerCase();
      if (reverse) {
        return bProp.localeCompare(aProp);
      }
      return aProp.localeCompare(bProp);
    });
    for (const book of books) {
      this.shelfBookList.append(book);
    }
    this.lastSort = this.lastSort === sortProperty ? null : sortProperty;
    this.onChange();
  }
};

// resources/js/components/shortcuts.js
function reverseMap(map) {
  const reversed = {};
  for (const [key, value] of Object.entries(map)) {
    reversed[value] = key;
  }
  return reversed;
}
var Shortcuts = class extends Component {
  setup() {
    this.container = this.$el;
    this.mapById = JSON.parse(this.$opts.keyMap);
    this.mapByShortcut = reverseMap(this.mapById);
    this.hintsShowing = false;
    this.hideHints = this.hideHints.bind(this);
    this.hintAbortController = null;
    this.setupListeners();
  }
  setupListeners() {
    window.addEventListener("keydown", (event) => {
      if (event.target.closest("input, select, textarea, .cm-editor, .editor-container")) {
        return;
      }
      if (event.key === "?") {
        if (this.hintsShowing) {
          this.hideHints();
        } else {
          this.showHints();
        }
        return;
      }
      this.handleShortcutPress(event);
    });
  }
  /**
   * @param {KeyboardEvent} event
   */
  handleShortcutPress(event) {
    const keys = [
      event.ctrlKey ? "Ctrl" : "",
      event.metaKey ? "Cmd" : "",
      event.key
    ];
    const combo = keys.filter((s) => Boolean(s)).join(" + ");
    const shortcutId = this.mapByShortcut[combo];
    if (shortcutId) {
      const wasHandled = this.runShortcut(shortcutId);
      if (wasHandled) {
        event.preventDefault();
      }
    }
  }
  /**
   * Run the given shortcut, and return a boolean to indicate if the event
   * was successfully handled by a shortcut action.
   * @param {String} id
   * @return {boolean}
   */
  runShortcut(id) {
    const el2 = this.container.querySelector(`[data-shortcut="${id}"]`);
    if (!el2) {
      return false;
    }
    if (el2.matches("input, textarea, select")) {
      el2.focus();
      return true;
    }
    if (el2.matches("a, button")) {
      el2.click();
      return true;
    }
    if (el2.matches("div[tabindex]")) {
      el2.click();
      el2.focus();
      return true;
    }
    console.error("Shortcut attempted to be ran for element type that does not have handling setup", el2);
    return false;
  }
  showHints() {
    const wrapper = document.createElement("div");
    wrapper.classList.add("shortcut-container");
    this.container.append(wrapper);
    const shortcutEls = this.container.querySelectorAll("[data-shortcut]");
    const displayedIds = /* @__PURE__ */ new Set();
    for (const shortcutEl of shortcutEls) {
      const id = shortcutEl.getAttribute("data-shortcut");
      if (displayedIds.has(id)) {
        continue;
      }
      const key = this.mapById[id];
      this.showHintLabel(shortcutEl, key, wrapper);
      displayedIds.add(id);
    }
    this.hintAbortController = new AbortController();
    const signal = this.hintAbortController.signal;
    window.addEventListener("scroll", this.hideHints, { signal });
    window.addEventListener("focus", this.hideHints, { signal });
    window.addEventListener("blur", this.hideHints, { signal });
    window.addEventListener("click", this.hideHints, { signal });
    this.hintsShowing = true;
  }
  /**
   * @param {Element} targetEl
   * @param {String} key
   * @param {Element} wrapper
   */
  showHintLabel(targetEl, key, wrapper) {
    const targetBounds = targetEl.getBoundingClientRect();
    const label = document.createElement("div");
    label.classList.add("shortcut-hint");
    label.textContent = key;
    const linkage = document.createElement("div");
    linkage.classList.add("shortcut-linkage");
    linkage.style.left = `${targetBounds.x}px`;
    linkage.style.top = `${targetBounds.y}px`;
    linkage.style.width = `${targetBounds.width}px`;
    linkage.style.height = `${targetBounds.height}px`;
    wrapper.append(label, linkage);
    const labelBounds = label.getBoundingClientRect();
    label.style.insetInlineStart = `${targetBounds.x + targetBounds.width - (labelBounds.width + 6)}px`;
    label.style.insetBlockStart = `${targetBounds.y + (targetBounds.height - labelBounds.height) / 2}px`;
  }
  hideHints() {
    const wrapper = this.container.querySelector(".shortcut-container");
    wrapper.remove();
    this.hintAbortController?.abort();
    this.hintsShowing = false;
  }
};

// resources/js/components/shortcut-input.js
var ignoreKeys = ["Control", "Alt", "Shift", "Meta", "Super", " ", "+", "Tab", "Escape"];
var ShortcutInput = class extends Component {
  setup() {
    this.input = this.$el;
    this.setupListeners();
  }
  setupListeners() {
    this.listenerRecordKey = this.listenerRecordKey.bind(this);
    this.input.addEventListener("focus", () => {
      this.startListeningForInput();
    });
    this.input.addEventListener("blur", () => {
      this.stopListeningForInput();
    });
  }
  startListeningForInput() {
    this.input.addEventListener("keydown", this.listenerRecordKey);
  }
  /**
   * @param {KeyboardEvent} event
   */
  listenerRecordKey(event) {
    if (ignoreKeys.includes(event.key)) {
      return;
    }
    const keys = [
      event.ctrlKey ? "Ctrl" : "",
      event.metaKey ? "Cmd" : "",
      event.key
    ];
    this.input.value = keys.filter((s) => Boolean(s)).join(" + ");
  }
  stopListeningForInput() {
    this.input.removeEventListener("keydown", this.listenerRecordKey);
  }
};

// resources/js/components/sortable-list.js
var SortableList = class extends Component {
  setup() {
    this.container = this.$el;
    this.handleSelector = this.$opts.handleSelector;
    const sortable = new sortable_esm_default(this.container, {
      handle: this.handleSelector,
      animation: 150,
      onSort: () => {
        this.$emit("sort", { ids: sortable.toArray() });
      },
      setData(dataTransferItem, dragEl2) {
        const jsonContent = dragEl2.getAttribute("data-drag-content");
        if (jsonContent) {
          const contentByType = JSON.parse(jsonContent);
          for (const [type, content] of Object.entries(contentByType)) {
            dataTransferItem.setData(type, content);
          }
        }
      },
      revertOnSpill: true,
      dropBubble: true,
      dragoverBubble: false
    });
  }
};

// resources/js/components/sort-rule-manager.ts
var SortRuleManager = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "input");
    __publicField(this, "configuredList");
    __publicField(this, "availableList");
  }
  setup() {
    this.input = this.$refs.input;
    this.configuredList = this.$refs.configuredOperationsList;
    this.availableList = this.$refs.availableOperationsList;
    this.initSortable();
    const listActions = buildListActions(this.availableList, this.configuredList);
    const sortActionListener = sortActionClickListener(listActions, this.onChange.bind(this));
    this.$el.addEventListener("click", sortActionListener);
  }
  initSortable() {
    const scrollBoxes = [this.configuredList, this.availableList];
    for (const scrollBox of scrollBoxes) {
      new sortable_esm_default(scrollBox, {
        group: "sort-rule-operations",
        ghostClass: "primary-background-light",
        handle: ".handle",
        animation: 150,
        onSort: this.onChange.bind(this)
      });
    }
  }
  onChange() {
    const configuredOpEls = Array.from(this.configuredList.querySelectorAll("[data-id]"));
    this.input.value = configuredOpEls.map((elem2) => elem2.getAttribute("data-id")).join(",");
  }
};

// resources/js/components/submit-on-change.js
var SubmitOnChange = class extends Component {
  setup() {
    this.filter = this.$opts.filter;
    this.$el.addEventListener("change", (event) => {
      if (this.filter && !event.target.matches(this.filter)) {
        return;
      }
      const form = this.$el.closest("form");
      if (form) {
        form.submit();
      }
    });
  }
};

// resources/js/components/tag-manager.js
var TagManager = class extends Component {
  setup() {
    this.addRemoveComponentEl = this.$refs.addRemove;
    this.container = this.$el;
    this.rowSelector = this.$opts.rowSelector;
    this.setupListeners();
  }
  setupListeners() {
    this.container.addEventListener("input", (event) => {
      const addRemoveComponent = window.$components.firstOnElement(this.addRemoveComponentEl, "add-remove-rows");
      if (!this.hasEmptyRows() && event.target.value) {
        addRemoveComponent.add();
      }
    });
  }
  hasEmptyRows() {
    const rows = this.container.querySelectorAll(this.rowSelector);
    const firstEmpty = [...rows].find((row) => [...row.querySelectorAll("input")].filter((input) => input.value).length === 0);
    return firstEmpty !== void 0;
  }
};

// resources/js/components/template-manager.js
var TemplateManager = class extends Component {
  setup() {
    this.container = this.$el;
    this.list = this.$refs.list;
    this.searchInput = this.$refs.searchInput;
    this.searchButton = this.$refs.searchButton;
    this.searchCancel = this.$refs.searchCancel;
    this.setupListeners();
  }
  setupListeners() {
    onChildEvent(this.container, "[template-action]", "click", this.handleTemplateActionClick.bind(this));
    onChildEvent(this.container, ".pagination a", "click", this.handlePaginationClick.bind(this));
    onChildEvent(this.container, ".template-item-content", "click", this.handleTemplateItemClick.bind(this));
    onChildEvent(this.container, ".template-item", "dragstart", this.handleTemplateItemDragStart.bind(this));
    this.searchInput.addEventListener("keypress", (event) => {
      if (event.key === "Enter") {
        event.preventDefault();
        this.performSearch();
      }
    });
    this.searchButton.addEventListener("click", () => this.performSearch());
    this.searchCancel.addEventListener("click", () => {
      this.searchInput.value = "";
      this.performSearch();
    });
  }
  handleTemplateItemClick(event, templateItem) {
    const templateId = templateItem.closest("[template-id]").getAttribute("template-id");
    this.insertTemplate(templateId, "replace");
  }
  handleTemplateItemDragStart(event, templateItem) {
    const templateId = templateItem.closest("[template-id]").getAttribute("template-id");
    event.dataTransfer.setData("bookstack/template", templateId);
    event.dataTransfer.setData("text/plain", templateId);
  }
  handleTemplateActionClick(event, actionButton) {
    event.stopPropagation();
    const action = actionButton.getAttribute("template-action");
    const templateId = actionButton.closest("[template-id]").getAttribute("template-id");
    this.insertTemplate(templateId, action);
  }
  async insertTemplate(templateId, action = "replace") {
    const resp = await window.$http.get(`/templates/${templateId}`);
    const eventName = `editor::${action}`;
    window.$events.emit(eventName, resp.data);
  }
  async handlePaginationClick(event, paginationLink) {
    event.preventDefault();
    const paginationUrl = paginationLink.getAttribute("href");
    const resp = await window.$http.get(paginationUrl);
    this.list.innerHTML = resp.data;
  }
  async performSearch() {
    const searchTerm = this.searchInput.value;
    const resp = await window.$http.get("/templates", {
      search: searchTerm
    });
    this.searchCancel.style.display = searchTerm ? "block" : "none";
    this.list.innerHTML = resp.data;
  }
};

// resources/js/components/toggle-switch.js
var ToggleSwitch = class extends Component {
  setup() {
    this.input = this.$el.querySelector("input[type=hidden]");
    this.checkbox = this.$el.querySelector("input[type=checkbox]");
    this.checkbox.addEventListener("change", this.stateChange.bind(this));
  }
  stateChange() {
    this.input.value = this.checkbox.checked ? "true" : "false";
    const changeEvent = new Event("change");
    this.input.dispatchEvent(changeEvent);
  }
};

// resources/js/components/tri-layout.ts
var TriLayout = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "container");
    __publicField(this, "tabs");
    __publicField(this, "sidebarScrollContainers");
    __publicField(this, "lastLayoutType", "none");
    __publicField(this, "onDestroy", null);
    __publicField(this, "scrollCache", {
      content: 0,
      info: 0
    });
    __publicField(this, "lastTabShown", "content");
  }
  setup() {
    this.container = this.$refs.container;
    this.tabs = this.$manyRefs.tab;
    this.sidebarScrollContainers = this.$manyRefs.sidebarScrollContainer;
    this.mobileTabClick = this.mobileTabClick.bind(this);
    this.updateLayout();
    window.addEventListener("resize", () => {
      this.updateLayout();
    }, { passive: true });
    this.setupSidebarScrollHandlers();
  }
  updateLayout() {
    let newLayout = "tablet";
    if (window.innerWidth <= 1e3) newLayout = "mobile";
    if (window.innerWidth > 1400) newLayout = "desktop";
    if (newLayout === this.lastLayoutType) return;
    if (this.onDestroy) {
      this.onDestroy();
      this.onDestroy = null;
    }
    if (newLayout === "desktop") {
      this.setupDesktop();
    } else if (newLayout === "mobile") {
      this.setupMobile();
    }
    this.lastLayoutType = newLayout;
  }
  setupMobile() {
    for (const tab of this.tabs) {
      tab.addEventListener("click", this.mobileTabClick);
    }
    this.onDestroy = () => {
      for (const tab of this.tabs) {
        tab.removeEventListener("click", this.mobileTabClick);
      }
    };
  }
  setupDesktop() {
  }
  /**
   * Action to run when the mobile info toggle bar is clicked/tapped
   */
  mobileTabClick(event) {
    const tab = event.target.dataset.tab || "";
    this.showTab(tab);
  }
  /**
   * Show the content tab.
   * Used by the page-display component.
   */
  showContent() {
    this.showTab("content", false);
  }
  /**
   * Show the given tab
   */
  showTab(tabName, scroll = true) {
    this.scrollCache[this.lastTabShown] = document.documentElement.scrollTop;
    for (const tab of this.tabs) {
      const isActive = tab.dataset.tab === tabName;
      tab.setAttribute("aria-selected", isActive ? "true" : "false");
    }
    const showInfo = tabName === "info";
    this.container.classList.toggle("show-info", showInfo);
    if (scroll) {
      const pageHeader = document.querySelector("header");
      const defaultScrollTop = pageHeader.getBoundingClientRect().bottom;
      document.documentElement.scrollTop = this.scrollCache[tabName] || defaultScrollTop;
      setTimeout(() => {
        document.documentElement.scrollTop = this.scrollCache[tabName] || defaultScrollTop;
      }, 50);
    }
    this.lastTabShown = tabName;
  }
  setupSidebarScrollHandlers() {
    for (const sidebar of this.sidebarScrollContainers) {
      sidebar.addEventListener("scroll", () => this.handleSidebarScroll(sidebar), {
        passive: true
      });
      this.handleSidebarScroll(sidebar);
    }
    window.addEventListener("resize", () => {
      for (const sidebar of this.sidebarScrollContainers) {
        this.handleSidebarScroll(sidebar);
      }
    });
  }
  handleSidebarScroll(sidebar) {
    const scrollable = sidebar.clientHeight !== sidebar.scrollHeight;
    const atTop = sidebar.scrollTop === 0;
    const atBottom = sidebar.scrollTop + sidebar.clientHeight === sidebar.scrollHeight;
    if (sidebar.parentElement) {
      sidebar.parentElement.classList.toggle("scroll-away-from-top", !atTop && scrollable);
      sidebar.parentElement.classList.toggle("scroll-away-from-bottom", !atBottom && scrollable);
    }
  }
};

// resources/js/components/user-select.js
var UserSelect = class extends Component {
  setup() {
    this.container = this.$el;
    this.input = this.$refs.input;
    this.userInfoContainer = this.$refs.userInfo;
    onChildEvent(this.container, "a.dropdown-search-item", "click", this.selectUser.bind(this));
  }
  selectUser(event, userEl) {
    event.preventDefault();
    this.input.value = userEl.getAttribute("data-id");
    this.userInfoContainer.innerHTML = userEl.innerHTML;
    this.input.dispatchEvent(new Event("change", { bubbles: true }));
    this.hide();
  }
  hide() {
    const dropdown = window.$components.firstOnElement(this.container, "dropdown");
    dropdown.hide();
  }
};

// resources/js/components/webhook-events.js
var WebhookEvents = class extends Component {
  setup() {
    this.checkboxes = this.$el.querySelectorAll('input[type="checkbox"]');
    this.allCheckbox = this.$el.querySelector('input[type="checkbox"][value="all"]');
    this.$el.addEventListener("change", (event) => {
      if (event.target.checked && event.target === this.allCheckbox) {
        this.deselectIndividualEvents();
      } else if (event.target.checked) {
        this.allCheckbox.checked = false;
      }
    });
  }
  deselectIndividualEvents() {
    for (const checkbox of this.checkboxes) {
      if (checkbox !== this.allCheckbox) {
        checkbox.checked = false;
      }
    }
  }
};

// resources/js/components/wysiwyg-editor.js
var WysiwygEditor = class extends Component {
  setup() {
    this.elem = this.$el;
    this.editContainer = this.$refs.editContainer;
    this.input = this.$refs.input;
    this.editor = null;
    const translations = {
      ...window.editor_translations,
      imageUploadErrorText: this.$opts.imageUploadErrorText,
      serverUploadLimitText: this.$opts.serverUploadLimitText
    };
    window.importVersioned("wysiwyg").then((wysiwyg) => {
      const editorContent = this.input.value;
      this.editor = wysiwyg.createPageEditorInstance(this.editContainer, editorContent, {
        drawioUrl: this.getDrawIoUrl(),
        pageId: Number(this.$opts.pageId),
        darkMode: document.documentElement.classList.contains("dark-mode"),
        textDirection: this.$opts.textDirection,
        translations
      });
      window.wysiwyg = this.editor;
    });
    let handlingFormSubmit = false;
    this.input.form.addEventListener("submit", (event) => {
      if (!this.editor) {
        return;
      }
      if (!handlingFormSubmit) {
        event.preventDefault();
        handlingFormSubmit = true;
        this.editor.getContentAsHtml().then((html) => {
          this.input.value = html;
          setTimeout(() => {
            this.input.form.requestSubmit();
          }, 5);
        });
      } else {
        handlingFormSubmit = false;
      }
    });
  }
  getDrawIoUrl() {
    const drawioUrlElem = document.querySelector("[drawio-url]");
    if (drawioUrlElem) {
      return drawioUrlElem.getAttribute("drawio-url");
    }
    return "";
  }
  /**
   * Get the content of this editor.
   * Used by the parent page editor component.
   * @return {Promise<{html: String}>}
   */
  async getContent() {
    return {
      html: await this.editor.getContentAsHtml()
    };
  }
};

// resources/js/wysiwyg-tinymce/shortcuts.js
function register(editor) {
  for (let i = 1; i < 5; i++) {
    editor.shortcuts.add(`meta+${i}`, "", ["FormatBlock", false, `h${i + 1}`]);
  }
  editor.shortcuts.add("meta+5", "", ["FormatBlock", false, "p"]);
  editor.shortcuts.add("meta+d", "", ["FormatBlock", false, "p"]);
  editor.shortcuts.add("meta+6", "", ["FormatBlock", false, "blockquote"]);
  editor.shortcuts.add("meta+q", "", ["FormatBlock", false, "blockquote"]);
  editor.shortcuts.add("meta+7", "", ["codeeditor", false, "pre"]);
  editor.shortcuts.add("meta+e", "", ["codeeditor", false, "pre"]);
  editor.shortcuts.add("meta+8", "", ["FormatBlock", false, "code"]);
  editor.shortcuts.add("meta+shift+E", "", ["FormatBlock", false, "code"]);
  editor.shortcuts.add("meta+o", "", "InsertOrderedList");
  editor.shortcuts.add("meta+p", "", "InsertUnorderedList");
  editor.shortcuts.add("meta+S", "", () => {
    window.$events.emit("editor-save-draft");
  });
  editor.shortcuts.add("meta+13", "", () => {
    window.$events.emit("editor-save-page");
  });
  editor.shortcuts.add("meta+9", "", () => {
    const selectedNode = editor.selection.getNode();
    const callout = selectedNode ? selectedNode.closest(".callout") : null;
    const formats2 = ["info", "success", "warning", "danger"];
    const currentFormatIndex = formats2.findIndex((format) => {
      return callout && callout.classList.contains(format);
    });
    const newFormatIndex = (currentFormatIndex + 1) % formats2.length;
    const newFormat = formats2[newFormatIndex];
    editor.formatter.apply(`callout${newFormat}`);
  });
  editor.shortcuts.add("meta+shift+K", "", () => {
    const selectorPopup = window.$components.first("entity-selector-popup");
    const selectionText = editor.selection.getContent({ format: "text" }).trim();
    selectorPopup.show((entity) => {
      if (editor.selection.isCollapsed()) {
        editor.insertContent(editor.dom.createHTML("a", { href: entity.link }, editor.dom.encode(entity.name)));
      } else {
        editor.formatter.apply("link", { href: entity.link });
      }
      editor.selection.collapse(false);
      editor.focus();
    }, {
      initialValue: selectionText,
      searchEndpoint: "/search/entity-selector",
      entityTypes: "page,book,chapter,bookshelf",
      entityPermission: "view"
    });
  });
}

// resources/js/wysiwyg-tinymce/common-events.js
function listen(editor) {
  window.$events.listen("editor::replace", ({ html }) => {
    editor.setContent(html);
  });
  window.$events.listen("editor::append", ({ html }) => {
    const content = editor.getContent() + html;
    editor.setContent(content);
  });
  window.$events.listen("editor::prepend", ({ html }) => {
    const content = html + editor.getContent();
    editor.setContent(content);
  });
  window.$events.listen("editor::insert", ({ html }) => {
    editor.insertContent(html);
  });
  window.$events.listen("editor::focus", () => {
    if (editor.initialized) {
      editor.focus();
    }
  });
}

// resources/js/wysiwyg-tinymce/scrolling.js
function scrollToText(editor, scrollId) {
  const element = editor.dom.get(encodeURIComponent(scrollId).replace(/!/g, "%21"));
  if (!element) {
    return;
  }
  element.scrollIntoView();
  editor.selection.select(element, true);
  editor.selection.collapse(false);
  editor.focus();
}
function scrollToQueryString(editor) {
  const queryParams = new URL(window.location).searchParams;
  const scrollId = queryParams.get("content-id");
  if (scrollId) {
    scrollToText(editor, scrollId);
  }
}

// resources/js/wysiwyg-tinymce/drop-paste-handling.js
var wrap;
var draggedContentEditable;
function hasTextContent(node) {
  return node && !!(node.textContent || node.innerText);
}
async function uploadImageFile(file, pageId) {
  if (file === null || file.type.indexOf("image") !== 0) {
    throw new Error("Not an image file");
  }
  const remoteFilename = file.name || `image-${Date.now()}.png`;
  const formData = new FormData();
  formData.append("file", file, remoteFilename);
  formData.append("uploaded_to", pageId);
  const resp = await window.$http.post(window.baseUrl("/images/gallery"), formData);
  return resp.data;
}
function paste(editor, options2, event) {
  const clipboard = new Clipboard(event.clipboardData || event.dataTransfer);
  if (!clipboard.hasItems() || clipboard.containsTabularData()) {
    return;
  }
  const images = clipboard.getImages();
  for (const imageFile of images) {
    const id = `image-${Math.random().toString(16).slice(2)}`;
    const loadingImage = window.baseUrl("/loading.gif");
    event.preventDefault();
    setTimeout(() => {
      editor.insertContent(`<p><img src="${loadingImage}" id="${id}"></p>`);
      uploadImageFile(imageFile, options2.pageId).then((resp) => {
        const safeName = resp.name.replace(/"/g, "");
        const newImageHtml = `<img src="${resp.thumbs.display}" alt="${safeName}" />`;
        const newEl = editor.dom.create("a", {
          target: "_blank",
          href: resp.url
        }, newImageHtml);
        editor.dom.replace(newEl, id);
      }).catch((err) => {
        editor.dom.remove(id);
        window.$events.error(err?.data?.message || options2.translations.imageUploadErrorText);
        console.error(err);
      });
    }, 10);
  }
}
function dragStart2(editor) {
  const node = editor.selection.getNode();
  if (node.nodeName === "IMG") {
    wrap = editor.dom.getParent(node, ".mceTemp");
    if (!wrap && node.parentNode.nodeName === "A" && !hasTextContent(node.parentNode)) {
      wrap = node.parentNode;
    }
  }
  if (node.hasAttribute("contenteditable") && node.getAttribute("contenteditable") === "false") {
    draggedContentEditable = node;
  }
}
function drop3(editor, options2, event) {
  const { dom } = editor;
  const rng = window.tinymce.dom.RangeUtils.getCaretRangeFromPoint(
    event.clientX,
    event.clientY,
    editor.getDoc()
  );
  const templateId = event.dataTransfer && event.dataTransfer.getData("bookstack/template");
  if (templateId) {
    event.preventDefault();
    window.$http.get(`/templates/${templateId}`).then((resp) => {
      editor.selection.setRng(rng);
      editor.undoManager.transact(() => {
        editor.execCommand("mceInsertContent", false, resp.data.html);
      });
    });
  }
  if (dom.getParent(rng.startContainer, ".mceTemp")) {
    event.preventDefault();
  } else if (wrap) {
    event.preventDefault();
    editor.undoManager.transact(() => {
      editor.selection.setRng(rng);
      editor.selection.setNode(wrap);
      dom.remove(wrap);
    });
  }
  if (!event.isDefaultPrevented() && draggedContentEditable) {
    event.preventDefault();
    editor.undoManager.transact(() => {
      const selectedNode = editor.selection.getNode();
      const range = editor.selection.getRng();
      const selectedNodeRoot = selectedNode.closest("body > *");
      if (range.startOffset > range.startContainer.length / 2) {
        selectedNodeRoot.after(draggedContentEditable);
      } else {
        selectedNodeRoot.before(draggedContentEditable);
      }
    });
  }
  if (!event.isDefaultPrevented()) {
    paste(editor, options2, event);
  }
  wrap = null;
}
function dragOver(editor, event) {
  event.preventDefault();
  editor.focus();
  const rangeUtils = window.tinymce.dom.RangeUtils;
  const range = rangeUtils.getCaretRangeFromPoint(event.clientX ?? 0, event.clientY ?? 0, editor.getDoc());
  editor.selection.setRng(range);
}
function listenForDragAndPaste(editor, options2) {
  editor.on("dragover", (event) => dragOver(editor, event));
  editor.on("dragstart", () => dragStart2(editor));
  editor.on("drop", (event) => drop3(editor, options2, event));
  editor.on("paste", (event) => paste(editor, options2, event));
}

// resources/js/wysiwyg-tinymce/toolbars.js
function getPrimaryToolbar(options2) {
  const textDirPlugins = options2.textDirection === "rtl" ? "ltr rtl" : "";
  const toolbar = [
    "undo redo",
    "styles",
    "bold italic underline forecolor backcolor formatoverflow",
    "alignleft aligncenter alignright alignjustify",
    "bullist numlist listoverflow",
    textDirPlugins,
    "link customtable imagemanager-insert insertoverflow",
    "code about fullscreen"
  ];
  return toolbar.filter((row) => Boolean(row)).join(" | ");
}
function registerPrimaryToolbarGroups(editor) {
  editor.ui.registry.addGroupToolbarButton("formatoverflow", {
    icon: "more-drawer",
    tooltip: "More",
    items: "strikethrough superscript subscript inlinecode removeformat"
  });
  editor.ui.registry.addGroupToolbarButton("listoverflow", {
    icon: "more-drawer",
    tooltip: "More",
    items: "tasklist outdent indent"
  });
  editor.ui.registry.addGroupToolbarButton("insertoverflow", {
    icon: "more-drawer",
    tooltip: "More",
    items: "customhr codeeditor drawio media details"
  });
}
function registerLinkContextToolbar(editor) {
  editor.ui.registry.addContextToolbar("linkcontexttoolbar", {
    predicate(node) {
      return node.closest("a") !== null;
    },
    position: "node",
    scope: "node",
    items: "link unlink openlink"
  });
}
function registerImageContextToolbar(editor) {
  editor.ui.registry.addContextToolbar("imagecontexttoolbar", {
    predicate(node) {
      return node.closest("img") !== null && !node.hasAttribute("data-mce-object");
    },
    position: "node",
    scope: "node",
    items: "image"
  });
}
function registerObjectContextToolbar(editor) {
  editor.ui.registry.addContextToolbar("objectcontexttoolbar", {
    predicate(node) {
      return node.closest("img") !== null && node.hasAttribute("data-mce-object");
    },
    position: "node",
    scope: "node",
    items: "media"
  });
}
function registerAdditionalToolbars(editor) {
  registerPrimaryToolbarGroups(editor);
  registerLinkContextToolbar(editor);
  registerImageContextToolbar(editor);
  registerObjectContextToolbar(editor);
}

// resources/js/wysiwyg-tinymce/icons.js
var icons = {
  "table-delete-column": '<svg width="24" height="24"><path d="M21 19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14c1.1 0 2 .9 2 2zm-2 0V5h-4v2.2h-2V5h-2v2.2H9V5H5v14h4v-2.1h2V19h2v-2.1h2V19Z"/><path d="M14.829 10.585 13.415 12l1.414 1.414c.943.943-.472 2.357-1.414 1.414L12 13.414l-1.414 1.414c-.944.944-2.358-.47-1.414-1.414L10.586 12l-1.414-1.415c-.943-.942.471-2.357 1.414-1.414L12 10.585l1.344-1.343c1.111-1.112 2.2.627 1.485 1.343z" style="fill-rule:nonzero"/></svg>',
  "table-delete-row": '<svg width="24" height="24"><path d="M5 21a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14c0 1.1-.9 2-2 2zm0-2h14v-4h-2.2v-2H19v-2h-2.2V9H19V5H5v4h2.1v2H5v2h2.1v2H5Z"/><path d="M13.415 14.829 12 13.415l-1.414 1.414c-.943.943-2.357-.472-1.414-1.414L10.586 12l-1.414-1.414c-.944-.944.47-2.358 1.414-1.414L12 10.586l1.415-1.414c.942-.943 2.357.471 1.414 1.414L13.415 12l1.343 1.344c1.112 1.111-.627 2.2-1.343 1.485z" style="fill-rule:nonzero"/></svg>',
  "table-insert-column-after": '<svg width="24" height="24"><path d="M16 5h-5v14h5c1.235 0 1.234 2 0 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11c1.229 0 1.236 2 0 2zm-7 6V5H5v6zm0 8v-6H5v6zm11.076-6h-2v2c0 1.333-2 1.333-2 0v-2h-2c-1.335 0-1.335-2 0-2h2V9c0-1.333 2-1.333 2 0v2h1.9c1.572 0 1.113 2 .1 2z"/></svg>',
  "table-insert-column-before": '<svg width="24" height="24"><path d="M8 19h5V5H8C6.764 5 6.766 3 8 3h11a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H8c-1.229 0-1.236-2 0-2zm7-6v6h4v-6zm0-8v6h4V5ZM3.924 11h2V9c0-1.333 2-1.333 2 0v2h2c1.335 0 1.335 2 0 2h-2v2c0 1.333-2 1.333-2 0v-2h-1.9c-1.572 0-1.113-2-.1-2z"/></svg>',
  "table-insert-row-above": '<svg width="24" height="24"><path d="M5 8v5h14V8c0-1.235 2-1.234 2 0v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8C3 6.77 5 6.764 5 8zm6 7H5v4h6zm8 0h-6v4h6zM13 3.924v2h2c1.333 0 1.333 2 0 2h-2v2c0 1.335-2 1.335-2 0v-2H9c-1.333 0-1.333-2 0-2h2v-1.9c0-1.572 2-1.113 2-.1z"/></svg>',
  "table-insert-row-after": '<svg width="24" height="24"><path d="M19 16v-5H5v5c0 1.235-2 1.234-2 0V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v11c0 1.229-2 1.236-2 0zm-6-7h6V5h-6zM5 9h6V5H5Zm6 11.076v-2H9c-1.333 0-1.333-2 0-2h2v-2c0-1.335 2-1.335 2 0v2h2c1.333 0 1.333 2 0 2h-2v1.9c0 1.572-2 1.113-2 .1z"/></svg>',
  table: '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2ZM5 14v5h6v-5zm14 0h-6v5h6zm0-7h-6v5h6zM5 12h6V7H5Z"/></svg>',
  "table-delete-table": '<svg width="24" height="24"><path d="M5 21a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14c0 1.1-.9 2-2 2zm0-2h14V5H5v14z"/><path d="m13.711 15.423-1.71-1.712-1.712 1.712c-1.14 1.14-2.852-.57-1.71-1.712l1.71-1.71-1.71-1.712c-1.143-1.142.568-2.853 1.71-1.71L12 10.288l1.711-1.71c1.141-1.142 2.852.57 1.712 1.71L13.71 12l1.626 1.626c1.345 1.345-.76 2.663-1.626 1.797z" style="fill-rule:nonzero;stroke-width:1.20992"/></svg>'
};
function registerCustomIcons(editor) {
  for (const [name, svg] of Object.entries(icons)) {
    editor.ui.registry.addIcon(name, svg);
  }
}

// resources/js/wysiwyg-tinymce/filters.js
function setupBrFilter(editor) {
  editor.serializer.addNodeFilter("br", (nodes) => {
    for (const node of nodes) {
      if (node.parent && node.parent.name === "code") {
        const newline = window.tinymce.html.Node.create("#text");
        newline.value = "\n";
        node.replace(newline);
      }
    }
  });
}
function setupPointerFilter(editor) {
  editor.parser.addNodeFilter("div", (nodes) => {
    for (const node of nodes) {
      const id = node.attr("id") || "";
      const nodeClass = node.attr("class") || "";
      if (id === "pointer" || nodeClass.includes("pointer")) {
        node.remove();
      }
    }
  });
}
function setupFilters(editor) {
  setupBrFilter(editor);
  setupPointerFilter(editor);
}

// resources/js/wysiwyg-tinymce/plugin-codeeditor.js
function elemIsCodeBlock(elem2) {
  return elem2.tagName.toLowerCase() === "code-block";
}
function showPopup(editor, code, language, direction, callback) {
  const codeEditor = window.$components.first("code-editor");
  const bookMark = editor.selection.getBookmark();
  codeEditor.open(code, language, direction, (newCode, newLang) => {
    callback(newCode, newLang);
    editor.focus();
    editor.selection.moveToBookmark(bookMark);
  }, () => {
    editor.focus();
    editor.selection.moveToBookmark(bookMark);
  });
}
function showPopupForCodeBlock(editor, codeBlock) {
  const direction = codeBlock.getAttribute("dir") || "";
  showPopup(editor, codeBlock.getContent(), codeBlock.getLanguage(), direction, (newCode, newLang) => {
    codeBlock.setContent(newCode, newLang);
  });
}
function defineCodeBlockCustomElement(editor) {
  const doc = editor.getDoc();
  const win = doc.defaultView;
  class CodeBlockElement extends win.HTMLElement {
    constructor() {
      super();
      /**
       * @type {?SimpleEditorInterface}
       */
      __publicField(this, "editor", null);
      this.attachShadow({ mode: "open" });
      const stylesToCopy = document.head.querySelectorAll('link[rel="stylesheet"]:not([media="print"]),style');
      const copiedStyles = Array.from(stylesToCopy).map((styleEl) => styleEl.cloneNode(true));
      const cmContainer = document.createElement("div");
      cmContainer.style.pointerEvents = "none";
      cmContainer.contentEditable = "false";
      cmContainer.classList.add("CodeMirrorContainer");
      cmContainer.classList.toggle("dark-mode", document.documentElement.classList.contains("dark-mode"));
      this.shadowRoot.append(...copiedStyles, cmContainer);
    }
    getLanguage() {
      const getLanguageFromClassList = (classes) => {
        const langClasses = classes.split(" ").filter((cssClass) => cssClass.startsWith("language-"));
        return (langClasses[0] || "").replace("language-", "");
      };
      const code = this.querySelector("code");
      const pre = this.querySelector("pre");
      return getLanguageFromClassList(pre.className) || code && getLanguageFromClassList(code.className) || "";
    }
    setContent(content, language) {
      if (this.editor) {
        this.editor.setContent(content);
        this.editor.setMode(language, content);
      }
      let pre = this.querySelector("pre");
      if (!pre) {
        pre = doc.createElement("pre");
        this.append(pre);
      }
      pre.innerHTML = "";
      const code = doc.createElement("code");
      pre.append(code);
      code.innerText = content;
      code.className = `language-${language}`;
    }
    getContent() {
      const code = this.querySelector("code") || this.querySelector("pre");
      const tempEl = document.createElement("pre");
      tempEl.innerHTML = code.innerHTML.replace(/\ufeff/g, "");
      const brs = tempEl.querySelectorAll("br");
      for (const br of brs) {
        br.replaceWith("\n");
      }
      return tempEl.textContent;
    }
    connectedCallback() {
      const connectedTime = Date.now();
      if (this.editor) {
        return;
      }
      this.cleanChildContent();
      const content = this.getContent();
      const lines = content.split("\n").length;
      const height = lines * 19.2 + 18 + 24;
      this.style.height = `${height}px`;
      const container = this.shadowRoot.querySelector(".CodeMirrorContainer");
      const renderEditor = (Code) => {
        this.editor = Code.wysiwygView(container, this.shadowRoot, content, this.getLanguage());
        setTimeout(() => {
          this.style.height = null;
        }, 12);
      };
      window.importVersioned("code").then((Code) => {
        const timeout = Date.now() - connectedTime < 20 ? 20 : 0;
        setTimeout(() => renderEditor(Code), timeout);
      });
    }
    cleanChildContent() {
      const pre = this.querySelector("pre");
      if (!pre) return;
      for (const preChild of pre.childNodes) {
        if (preChild.nodeName === "#text" && preChild.textContent === "\uFEFF") {
          preChild.remove();
        }
      }
    }
  }
  win.customElements.define("code-block", CodeBlockElement);
}
function register2(editor) {
  editor.ui.registry.addIcon("codeblock", '<svg width="24" height="24"><path d="M4 3h16c.6 0 1 .4 1 1v16c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V4c0-.6.4-1 1-1Zm1 2v14h14V5Z"/><path d="M11.103 15.423c.277.277.277.738 0 .922a.692.692 0 0 1-1.106 0l-4.057-3.78a.738.738 0 0 1 0-1.107l4.057-3.872c.276-.277.83-.277 1.106 0a.724.724 0 0 1 0 1.014L7.6 12.012ZM12.897 8.577c-.245-.312-.2-.675.08-.955.28-.281.727-.27 1.027.033l4.057 3.78a.738.738 0 0 1 0 1.107l-4.057 3.872c-.277.277-.83.277-1.107 0a.724.724 0 0 1 0-1.014l3.504-3.412z"/></svg>');
  editor.ui.registry.addButton("codeeditor", {
    tooltip: "Insert code block",
    icon: "codeblock",
    onAction() {
      editor.execCommand("codeeditor");
    }
  });
  editor.ui.registry.addButton("editcodeeditor", {
    tooltip: "Edit code block",
    icon: "edit-block",
    onAction() {
      editor.execCommand("codeeditor");
    }
  });
  editor.addCommand("codeeditor", () => {
    const selectedNode = editor.selection.getNode();
    const doc = selectedNode.ownerDocument;
    if (elemIsCodeBlock(selectedNode)) {
      showPopupForCodeBlock(editor, selectedNode);
    } else {
      const textContent = editor.selection.getContent({ format: "text" });
      const direction = document.dir === "rtl" ? "ltr" : "";
      showPopup(editor, textContent, "", direction, (newCode, newLang) => {
        const pre = doc.createElement("pre");
        const code = doc.createElement("code");
        code.classList.add(`language-${newLang}`);
        code.innerText = newCode;
        if (direction) {
          pre.setAttribute("dir", direction);
        }
        pre.append(code);
        editor.insertContent(pre.outerHTML);
      });
    }
  });
  editor.on("dblclick", () => {
    const selectedNode = editor.selection.getNode();
    if (elemIsCodeBlock(selectedNode)) {
      showPopupForCodeBlock(editor, selectedNode);
    }
  });
  editor.on("PreInit", () => {
    editor.parser.addNodeFilter("pre", (elms) => {
      for (const el2 of elms) {
        const wrapper = window.tinymce.html.Node.create("code-block", {
          contenteditable: "false"
        });
        const childCodeBlock = el2.children().filter((child) => child.name === "code")[0] || null;
        const direction = el2.attr("dir") || childCodeBlock && childCodeBlock.attr("dir") || "";
        if (direction) {
          wrapper.attr("dir", direction);
        }
        const spans = el2.getAll("span");
        for (const span of spans) {
          span.unwrap();
        }
        el2.attr("style", null);
        el2.wrap(wrapper);
      }
    });
    editor.parser.addNodeFilter("code-block", (elms) => {
      for (const el2 of elms) {
        el2.attr("contenteditable", "false");
      }
    });
    editor.serializer.addNodeFilter("code-block", (elms) => {
      for (const el2 of elms) {
        const direction = el2.attr("dir");
        if (direction && el2.firstChild) {
          el2.firstChild.attr("dir", direction);
        } else if (el2.firstChild) {
          el2.firstChild.attr("dir", null);
        }
        el2.unwrap();
      }
    });
  });
  editor.ui.registry.addContextToolbar("codeeditor", {
    predicate(node) {
      return node.nodeName.toLowerCase() === "code-block";
    },
    items: "editcodeeditor",
    position: "node",
    scope: "node"
  });
  editor.on("PreInit", () => {
    defineCodeBlockCustomElement(editor);
  });
}
function getPlugin() {
  return register2;
}

// node_modules/idb-keyval/dist/index.js
function promisifyRequest(request) {
  return new Promise((resolve, reject) => {
    request.oncomplete = request.onsuccess = () => resolve(request.result);
    request.onabort = request.onerror = () => reject(request.error);
  });
}
function createStore(dbName, storeName) {
  let dbp;
  const getDB = () => {
    if (dbp)
      return dbp;
    const request = indexedDB.open(dbName);
    request.onupgradeneeded = () => request.result.createObjectStore(storeName);
    dbp = promisifyRequest(request);
    dbp.then((db) => {
      db.onclose = () => dbp = void 0;
    }, () => {
    });
    return dbp;
  };
  return (txMode, callback) => getDB().then((db) => callback(db.transaction(storeName, txMode).objectStore(storeName)));
}
var defaultGetStoreFunc;
function defaultGetStore() {
  if (!defaultGetStoreFunc) {
    defaultGetStoreFunc = createStore("keyval-store", "keyval");
  }
  return defaultGetStoreFunc;
}
function get(key, customStore = defaultGetStore()) {
  return customStore("readonly", (store) => promisifyRequest(store.get(key)));
}
function set(key, value, customStore = defaultGetStore()) {
  return customStore("readwrite", (store) => {
    store.put(value, key);
    return promisifyRequest(store.transaction);
  });
}
function del(key, customStore = defaultGetStore()) {
  return customStore("readwrite", (store) => {
    store.delete(key);
    return promisifyRequest(store.transaction);
  });
}

// resources/js/services/drawio.ts
var iFrame = null;
var lastApprovedOrigin;
var onInit;
var onSave;
var saveBackupKey = "last-drawing-save";
function drawPostMessage(data) {
  iFrame?.contentWindow?.postMessage(JSON.stringify(data), lastApprovedOrigin);
}
function drawEventExport(message) {
  set(saveBackupKey, message.data);
  if (onSave) {
    onSave(message.data).then(() => {
      del(saveBackupKey);
    });
  }
}
function drawEventSave(message) {
  drawPostMessage({
    action: "export",
    format: "xmlpng",
    xml: message.xml,
    spin: "Updating drawing"
  });
}
function drawEventInit() {
  if (!onInit) return;
  onInit().then((xml) => {
    drawPostMessage({ action: "load", autosave: 1, xml });
  });
}
function drawEventConfigure() {
  const config = {};
  if (iFrame) {
    window.$events.emitPublic(iFrame, "editor-drawio::configure", { config });
    drawPostMessage({ action: "configure", config });
  }
}
function drawEventClose() {
  window.removeEventListener("message", drawReceive);
  if (iFrame) document.body.removeChild(iFrame);
}
function drawReceive(event) {
  if (!event.data || event.data.length < 1) return;
  if (event.origin !== lastApprovedOrigin) return;
  const message = JSON.parse(event.data);
  if (message.event === "init") {
    drawEventInit();
  } else if (message.event === "exit") {
    drawEventClose();
  } else if (message.event === "save") {
    drawEventSave(message);
  } else if (message.event === "export") {
    drawEventExport(message);
  } else if (message.event === "configure") {
    drawEventConfigure();
  }
}
async function attemptRestoreIfExists() {
  const backupVal = await get(saveBackupKey);
  const dialogEl = document.getElementById("unsaved-drawing-dialog");
  if (!dialogEl) {
    console.error("Missing expected unsaved-drawing dialog");
  }
  if (backupVal && dialogEl) {
    const dialog = window.$components.firstOnElement(dialogEl, "confirm-dialog");
    const restore = await dialog.show();
    if (restore) {
      onInit = async () => backupVal;
    }
  }
}
async function show(drawioUrl, onInitCallback, onSaveCallback) {
  onInit = onInitCallback;
  onSave = onSaveCallback;
  await attemptRestoreIfExists();
  iFrame = document.createElement("iframe");
  iFrame.setAttribute("frameborder", "0");
  window.addEventListener("message", drawReceive);
  iFrame.setAttribute("src", drawioUrl);
  iFrame.setAttribute("class", "fullscreen");
  iFrame.style.backgroundColor = "#FFFFFF";
  document.body.appendChild(iFrame);
  lastApprovedOrigin = new URL(drawioUrl).origin;
}
async function upload(imageData, pageUploadedToId) {
  const data = {
    image: imageData,
    uploaded_to: pageUploadedToId
  };
  const resp = await window.$http.post(window.baseUrl("/images/drawio"), data);
  return resp.data;
}
function close() {
  drawEventClose();
}
async function load(drawingId) {
  try {
    const resp = await window.$http.get(window.baseUrl(`/images/drawio/base64/${drawingId}`));
    const data = resp.data;
    return `data:image/png;base64,${data.content}`;
  } catch (error) {
    if (error instanceof HttpError) {
      window.$events.showResponseError(error);
    }
    close();
    throw error;
  }
}

// resources/js/wysiwyg-tinymce/plugin-drawio.js
var pageEditor = null;
var currentNode = null;
var options = {};
function isDrawing(node) {
  return node.hasAttribute("drawio-diagram");
}
function showDrawingManager(mceEditor, selectedNode = null) {
  pageEditor = mceEditor;
  currentNode = selectedNode;
  const imageManager = window.$components.first("image-manager");
  imageManager.show((image) => {
    if (selectedNode) {
      const imgElem = selectedNode.querySelector("img");
      pageEditor.undoManager.transact(() => {
        pageEditor.dom.setAttrib(imgElem, "src", image.url);
        pageEditor.dom.setAttrib(selectedNode, "drawio-diagram", image.id);
      });
    } else {
      const imgHTML = `<div drawio-diagram="${image.id}" contenteditable="false"><img src="${image.url}"></div>`;
      pageEditor.insertContent(imgHTML);
    }
  }, "drawio");
}
async function updateContent(pngData) {
  const loadingImage = window.baseUrl("/loading.gif");
  const handleUploadError = (error) => {
    if (error.status === 413) {
      window.$events.emit("error", options.translations.serverUploadLimitText);
    } else {
      window.$events.emit("error", options.translations.imageUploadErrorText);
    }
    console.error(error);
  };
  if (currentNode) {
    close();
    const imgElem = currentNode.querySelector("img");
    try {
      const img = await upload(pngData, options.pageId);
      pageEditor.undoManager.transact(() => {
        pageEditor.dom.setAttrib(imgElem, "src", img.url);
        pageEditor.dom.setAttrib(currentNode, "drawio-diagram", img.id);
      });
    } catch (err) {
      handleUploadError(err);
      throw new Error(`Failed to save image with error: ${err}`);
    }
    return;
  }
  await wait(5);
  const id = `drawing-${Math.random().toString(16).slice(2)}`;
  const wrapId = `drawing-wrap-${Math.random().toString(16).slice(2)}`;
  pageEditor.insertContent(`<div drawio-diagram contenteditable="false" id="${wrapId}"><img src="${loadingImage}" id="${id}"></div>`);
  close();
  try {
    const img = await upload(pngData, options.pageId);
    pageEditor.undoManager.transact(() => {
      pageEditor.dom.setAttrib(id, "src", img.url);
      pageEditor.dom.setAttrib(wrapId, "drawio-diagram", img.id);
    });
  } catch (err) {
    pageEditor.dom.remove(wrapId);
    handleUploadError(err);
    throw new Error(`Failed to save image with error: ${err}`);
  }
}
function drawingInit() {
  if (!currentNode) {
    return Promise.resolve("");
  }
  const drawingId = currentNode.getAttribute("drawio-diagram");
  return load(drawingId);
}
function showDrawingEditor(mceEditor, selectedNode = null) {
  pageEditor = mceEditor;
  currentNode = selectedNode;
  show(options.drawioUrl, drawingInit, updateContent);
}
function register3(editor) {
  editor.addCommand("drawio", () => {
    const selectedNode = editor.selection.getNode();
    showDrawingEditor(editor, isDrawing(selectedNode) ? selectedNode : null);
  });
  editor.ui.registry.addIcon("diagram", `<svg width="24" height="24" fill="${options.darkMode ? "#BBB" : "#000000"}" xmlns="http://www.w3.org/2000/svg"><path d="M20.716 7.639V2.845h-4.794v1.598h-7.99V2.845H3.138v4.794h1.598v7.99H3.138v4.794h4.794v-1.598h7.99v1.598h4.794v-4.794h-1.598v-7.99zM4.736 4.443h1.598V6.04H4.736zm1.598 14.382H4.736v-1.598h1.598zm9.588-1.598h-7.99v-1.598H6.334v-7.99h1.598V6.04h7.99v1.598h1.598v7.99h-1.598zm3.196 1.598H17.52v-1.598h1.598zM17.52 6.04V4.443h1.598V6.04zm-4.21 7.19h-2.79l-.582 1.599H8.643l2.717-7.191h1.119l2.724 7.19h-1.302zm-2.43-1.006h2.086l-1.039-3.06z"/></svg>`);
  editor.ui.registry.addSplitButton("drawio", {
    tooltip: "Insert/edit drawing",
    icon: "diagram",
    onAction() {
      editor.execCommand("drawio");
      window.document.body.dispatchEvent(new Event("mousedown", { bubbles: true }));
    },
    fetch(callback) {
      callback([
        {
          type: "choiceitem",
          text: "Drawing manager",
          value: "drawing-manager"
        }
      ]);
    },
    onItemAction(api, value) {
      if (value === "drawing-manager") {
        const selectedNode = editor.selection.getNode();
        showDrawingManager(editor, isDrawing(selectedNode) ? selectedNode : null);
      }
    }
  });
  editor.on("dblclick", () => {
    const selectedNode = editor.selection.getNode();
    if (!isDrawing(selectedNode)) return;
    showDrawingEditor(editor, selectedNode);
  });
  editor.on("SetContent", () => {
    const drawings = editor.dom.select("body > div[drawio-diagram]");
    if (!drawings.length) return;
    editor.undoManager.transact(() => {
      for (const drawing of drawings) {
        drawing.setAttribute("contenteditable", "false");
      }
    });
  });
}
function getPlugin2(providedOptions) {
  options = providedOptions;
  return register3;
}

// resources/js/wysiwyg-tinymce/plugins-customhr.js
function register4(editor) {
  editor.addCommand("InsertHorizontalRule", () => {
    const hrElem = document.createElement("hr");
    const cNode = editor.selection.getNode();
    const { parentNode } = cNode;
    parentNode.insertBefore(hrElem, cNode);
  });
  editor.ui.registry.addButton("customhr", {
    icon: "horizontal-rule",
    tooltip: "Insert horizontal line",
    onAction() {
      editor.execCommand("InsertHorizontalRule");
    }
  });
}
function getPlugin3() {
  return register4;
}

// resources/js/wysiwyg-tinymce/plugins-imagemanager.js
function register5(editor) {
  editor.ui.registry.addButton("imagemanager-insert", {
    title: "Insert image",
    icon: "image",
    tooltip: "Insert image",
    onAction() {
      const imageManager = window.$components.first("image-manager");
      imageManager.show((image) => {
        const imageUrl = image.thumbs?.display || image.url;
        let html = `<a href="${image.url}" target="_blank">`;
        html += `<img src="${imageUrl}" alt="${image.name}">`;
        html += "</a>";
        editor.execCommand("mceInsertContent", false, html);
      }, "gallery");
    }
  });
}
function getPlugin4() {
  return register5;
}

// resources/js/wysiwyg-tinymce/plugins-about.js
function register6(editor) {
  const aboutDialog = {
    title: "About the WYSIWYG Editor",
    url: window.baseUrl("/help/tinymce")
  };
  editor.ui.registry.addButton("about", {
    icon: "help",
    tooltip: "About the editor",
    onAction() {
      window.tinymce.activeEditor.windowManager.openUrl(aboutDialog);
    }
  });
}
function getPlugin5() {
  return register6;
}

// resources/js/wysiwyg-tinymce/util.js
var blockElementTypes = [
  "p",
  "h1",
  "h2",
  "h3",
  "h4",
  "h5",
  "h6",
  "div",
  "blockquote",
  "pre",
  "code-block",
  "details",
  "ul",
  "ol",
  "table",
  "hr"
];

// resources/js/wysiwyg-tinymce/plugins-details.js
function getSelectedDetailsBlock(editor) {
  return editor.selection.getNode().closest("details");
}
function setSummary(editor, summaryContent) {
  const details = getSelectedDetailsBlock(editor);
  if (!details) return;
  editor.undoManager.transact(() => {
    let summary = details.querySelector("summary");
    if (!summary) {
      summary = document.createElement("summary");
      details.prepend(summary);
    }
    summary.textContent = summaryContent;
  });
}
function detailsDialog(editor) {
  return {
    title: "Edit collapsible block",
    body: {
      type: "panel",
      items: [
        {
          type: "input",
          name: "summary",
          label: "Toggle label"
        }
      ]
    },
    buttons: [
      {
        type: "cancel",
        text: "Cancel"
      },
      {
        type: "submit",
        text: "Save",
        primary: true
      }
    ],
    onSubmit(api) {
      const { summary } = api.getData();
      setSummary(editor, summary);
      api.close();
    }
  };
}
function getSummaryTextFromDetails(element) {
  const summary = element.querySelector("summary");
  if (!summary) {
    return "";
  }
  return summary.textContent;
}
function showDetailLabelEditWindow(editor) {
  const details = getSelectedDetailsBlock(editor);
  const dialog = editor.windowManager.open(detailsDialog(editor));
  dialog.setData({ summary: getSummaryTextFromDetails(details) });
}
function unwrapDetailsInSelection(editor) {
  const details = editor.selection.getNode().closest("details");
  const selectionBm = editor.selection.getBookmark();
  if (details) {
    const elements = details.querySelectorAll("details > *:not(summary, doc-root), doc-root > *");
    editor.undoManager.transact(() => {
      for (const element of elements) {
        details.parentNode.insertBefore(element, details);
      }
      details.remove();
    });
  }
  editor.focus();
  editor.selection.moveToBookmark(selectionBm);
}
function unwrapDetailsEditable(detailsEl) {
  detailsEl.attr("contenteditable", null);
  let madeUnwrap = false;
  for (const child of detailsEl.children()) {
    if (child.name === "doc-root") {
      child.unwrap();
      madeUnwrap = true;
    }
  }
  if (madeUnwrap) {
    unwrapDetailsEditable(detailsEl);
  }
}
function ensureDetailsWrappedInEditable(detailsEl) {
  unwrapDetailsEditable(detailsEl);
  detailsEl.attr("contenteditable", "false");
  const rootWrap = window.tinymce.html.Node.create("doc-root", { contenteditable: "true" });
  let previousBlockWrap = null;
  for (const child of detailsEl.children()) {
    if (child.name === "summary") continue;
    const isBlock = blockElementTypes.includes(child.name);
    if (!isBlock) {
      if (!previousBlockWrap) {
        previousBlockWrap = window.tinymce.html.Node.create("p");
        rootWrap.append(previousBlockWrap);
      }
      previousBlockWrap.append(child);
    } else {
      rootWrap.append(child);
      previousBlockWrap = null;
    }
  }
  detailsEl.append(rootWrap);
}
function setupElementFilters(editor) {
  editor.parser.addNodeFilter("details", (elms) => {
    for (const el2 of elms) {
      ensureDetailsWrappedInEditable(el2);
    }
  });
  editor.serializer.addNodeFilter("details", (elms) => {
    for (const el2 of elms) {
      unwrapDetailsEditable(el2);
      el2.attr("open", null);
    }
  });
  editor.serializer.addNodeFilter("doc-root", (elms) => {
    for (const el2 of elms) {
      el2.unwrap();
    }
  });
}
function register7(editor) {
  editor.ui.registry.addIcon("details", '<svg width="24" height="24"><path d="M8.2 9a.5.5 0 0 0-.4.8l4 5.6a.5.5 0 0 0 .8 0l4-5.6a.5.5 0 0 0-.4-.8ZM20.122 18.151h-16c-.964 0-.934 2.7 0 2.7h16c1.139 0 1.173-2.7 0-2.7zM20.122 3.042h-16c-.964 0-.934 2.7 0 2.7h16c1.139 0 1.173-2.7 0-2.7z"/></svg>');
  editor.ui.registry.addIcon("togglefold", '<svg height="24"  width="24"><path d="M8.12 19.3c.39.39 1.02.39 1.41 0L12 16.83l2.47 2.47c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41l-3.17-3.17c-.39-.39-1.02-.39-1.41 0l-3.17 3.17c-.4.38-.4 1.02-.01 1.41zm7.76-14.6c-.39-.39-1.02-.39-1.41 0L12 7.17 9.53 4.7c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.03 0 1.42l3.17 3.17c.39.39 1.02.39 1.41 0l3.17-3.17c.4-.39.4-1.03.01-1.42z"/></svg>');
  editor.ui.registry.addIcon("togglelabel", '<svg height="18" width="18" viewBox="0 0 24 24"><path d="M21.41,11.41l-8.83-8.83C12.21,2.21,11.7,2,11.17,2H4C2.9,2,2,2.9,2,4v7.17c0,0.53,0.21,1.04,0.59,1.41l8.83,8.83 c0.78,0.78,2.05,0.78,2.83,0l7.17-7.17C22.2,13.46,22.2,12.2,21.41,11.41z M6.5,8C5.67,8,5,7.33,5,6.5S5.67,5,6.5,5S8,5.67,8,6.5 S7.33,8,6.5,8z"/></svg>');
  editor.ui.registry.addButton("details", {
    icon: "details",
    tooltip: "Insert collapsible block",
    onAction() {
      editor.execCommand("InsertDetailsBlock");
    }
  });
  editor.ui.registry.addButton("removedetails", {
    icon: "table-delete-table",
    tooltip: "Unwrap",
    onAction() {
      unwrapDetailsInSelection(editor);
    }
  });
  editor.ui.registry.addButton("editdetials", {
    icon: "togglelabel",
    tooltip: "Edit label",
    onAction() {
      showDetailLabelEditWindow(editor);
    }
  });
  editor.on("dblclick", (event) => {
    if (!getSelectedDetailsBlock(editor) || event.target.closest("doc-root")) return;
    showDetailLabelEditWindow(editor);
  });
  editor.ui.registry.addButton("toggledetails", {
    icon: "togglefold",
    tooltip: "Toggle open/closed",
    onAction() {
      const details = getSelectedDetailsBlock(editor);
      details.toggleAttribute("open");
      editor.focus();
    }
  });
  editor.addCommand("InsertDetailsBlock", () => {
    let content = editor.selection.getContent({ format: "html" });
    const details = document.createElement("details");
    const summary = document.createElement("summary");
    const id = `details-${Date.now()}`;
    details.setAttribute("data-id", id);
    details.appendChild(summary);
    if (!content) {
      content = "<p><br></p>";
    }
    details.innerHTML += content;
    editor.insertContent(details.outerHTML);
    editor.focus();
    const domDetails = editor.dom.select(`[data-id="${id}"]`)[0] || null;
    if (domDetails) {
      const firstChild = domDetails.querySelector("doc-root > *");
      if (firstChild) {
        firstChild.focus();
      }
      domDetails.removeAttribute("data-id");
    }
  });
  editor.ui.registry.addContextToolbar("details", {
    predicate(node) {
      return node.nodeName.toLowerCase() === "details";
    },
    items: "editdetials toggledetails removedetails",
    position: "node",
    scope: "node"
  });
  editor.on("PreInit", () => {
    setupElementFilters(editor);
  });
}
function getPlugin6() {
  return register7;
}

// resources/js/wysiwyg-tinymce/plugins-table-additions.js
function register8(editor) {
  editor.ui.registry.addIcon("tableclearformatting", '<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 24 24"><path d="M15.53088 4.64727v-.82364c0-.453-.37063-.82363-.82363-.82363H4.82363C4.37063 3 4 3.37064 4 3.82363v3.29454c0 .453.37064.82364.82363.82364h9.88362c.453 0 .82363-.37064.82363-.82364v-.82363h.82364v3.29454H8.11817v7.4127c0 .453.37064.82364.82364.82364h1.64727c.453 0 .82363-.37064.82363-.82364v-5.76544h6.58907V4.64727Z"/><path d="m18.42672 19.51563-1.54687-1.54688-1.54688 1.54688c-.26751.2675-.70124.2675-.96875 0-.26751-.26752-.26751-.70124 0-.96876L15.9111 17l-1.54688-1.54688c-.26751-.2675-.26751-.70123 0-.96875.26751-.2675.70124-.2675.96875 0l1.54688 1.54688 1.54687-1.54688c.26751-.2675.70124-.2675.96875 0 .26751.26752.26751.70124 0 .96875L17.8486 17l1.54687 1.54688c.26751.2675.26751.70123 0 .96874-.26751.26752-.70124.26752-.96875 0z"/></svg>');
  const tableFirstRowContextSpec = {
    items: " | tablerowheader",
    predicate(elem2) {
      const isTable = elem2.nodeName.toLowerCase() === "table";
      const selectionNode = editor.selection.getNode();
      const parentTable = selectionNode.closest("table");
      if (!isTable || !parentTable) {
        return false;
      }
      const firstRow = parentTable.querySelector("tr");
      return firstRow.contains(selectionNode);
    },
    position: "node",
    scope: "node"
  };
  editor.ui.registry.addContextToolbar("customtabletoolbarfirstrow", tableFirstRowContextSpec);
  editor.addCommand("tableclearformatting", () => {
    const table = editor.dom.getParent(editor.selection.getStart(), "table");
    if (!table) {
      return;
    }
    const attrsToRemove = ["class", "style", "width", "height"];
    const styled = [table, ...table.querySelectorAll(attrsToRemove.map((a) => `[${a}]`).join(","))];
    for (const elem2 of styled) {
      for (const attr of attrsToRemove) {
        elem2.removeAttribute(attr);
      }
    }
  });
  editor.addCommand("tableclearsizes", () => {
    const table = editor.dom.getParent(editor.selection.getStart(), "table");
    if (!table) {
      return;
    }
    const targets = [table, ...table.querySelectorAll("tr,td,th,tbody,thead,tfoot,th>*,td>*")];
    for (const elem2 of targets) {
      elem2.removeAttribute("width");
      elem2.removeAttribute("height");
      elem2.style.height = null;
      elem2.style.width = null;
    }
  });
  const onPreInit = () => {
    const exitingButtons = editor.ui.registry.getAll().buttons;
    editor.ui.registry.addMenuButton("customtable", {
      ...exitingButtons.table,
      fetch: (callback) => callback("inserttable | cell row column | advtablesort | tableprops tableclearformatting tableclearsizes deletetable")
    });
    editor.ui.registry.addMenuItem("tableclearformatting", {
      icon: "tableclearformatting",
      text: "Clear table formatting",
      onSetup: exitingButtons.tableprops.onSetup,
      onAction() {
        editor.execCommand("tableclearformatting");
      }
    });
    editor.ui.registry.addMenuItem("tableclearsizes", {
      icon: "resize",
      text: "Resize to contents",
      onSetup: exitingButtons.tableprops.onSetup,
      onAction() {
        editor.execCommand("tableclearsizes");
      }
    });
    editor.off("PreInit", onPreInit);
  };
  editor.on("PreInit", onPreInit);
}
function getPlugin7() {
  return register8;
}

// resources/js/wysiwyg-tinymce/plugins-tasklist.js
function elementWithinTaskList(element) {
  const listEl = element.closest("li");
  return listEl && listEl.parentNode.nodeName === "UL" && listEl.classList.contains("task-list-item");
}
function handleTaskListItemClick(event, clickedEl, editor) {
  const bounds = clickedEl.getBoundingClientRect();
  const withinBounds = event.clientX <= bounds.right && event.clientX >= bounds.left && event.clientY >= bounds.top && event.clientY <= bounds.bottom;
  if (!withinBounds) {
    editor.undoManager.transact(() => {
      if (clickedEl.hasAttribute("checked")) {
        clickedEl.removeAttribute("checked");
      } else {
        clickedEl.setAttribute("checked", "checked");
      }
    });
  }
}
function parseTaskListNode(node) {
  node.attr("class", "task-list-item");
  for (const child of node.children()) {
    if (child.name === "input") {
      if (child.attr("checked") === "checked") {
        node.attr("checked", "checked");
      }
      child.remove();
    }
  }
}
function serializeTaskListNode(node) {
  const isChecked = node.attr("checked") === "checked";
  node.attr("checked", null);
  const inputAttrs = { type: "checkbox", disabled: "disabled" };
  if (isChecked) {
    inputAttrs.checked = "checked";
  }
  const checkbox = window.tinymce.html.Node.create("input", inputAttrs);
  checkbox.shortEnded = true;
  if (node.firstChild) {
    node.insert(checkbox, node.firstChild, true);
  } else {
    node.append(checkbox);
  }
}
function register9(editor) {
  editor.ui.registry.addIcon("tasklist", '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M22,8c0-0.55-0.45-1-1-1h-7c-0.55,0-1,0.45-1,1s0.45,1,1,1h7C21.55,9,22,8.55,22,8z M13,16c0,0.55,0.45,1,1,1h7 c0.55,0,1-0.45,1-1c0-0.55-0.45-1-1-1h-7C13.45,15,13,15.45,13,16z M10.47,4.63c0.39,0.39,0.39,1.02,0,1.41l-4.23,4.25 c-0.39,0.39-1.02,0.39-1.42,0L2.7,8.16c-0.39-0.39-0.39-1.02,0-1.41c0.39-0.39,1.02-0.39,1.41,0l1.42,1.42l3.54-3.54 C9.45,4.25,10.09,4.25,10.47,4.63z M10.48,12.64c0.39,0.39,0.39,1.02,0,1.41l-4.23,4.25c-0.39,0.39-1.02,0.39-1.42,0L2.7,16.16 c-0.39-0.39-0.39-1.02,0-1.41s1.02-0.39,1.41,0l1.42,1.42l3.54-3.54C9.45,12.25,10.09,12.25,10.48,12.64L10.48,12.64z"/></svg>');
  editor.ui.registry.addToggleButton("tasklist", {
    tooltip: "Task list",
    icon: "tasklist",
    active: false,
    onAction(api) {
      if (api.isActive()) {
        editor.execCommand("RemoveList");
      } else {
        editor.execCommand("InsertUnorderedList", null, {
          "list-item-attributes": {
            class: "task-list-item"
          },
          "list-style-type": "tasklist"
        });
      }
    },
    onSetup(api) {
      editor.on("NodeChange", (event) => {
        const parentListEl = event.parents.find((el2) => el2.nodeName === "LI");
        const inList = parentListEl && parentListEl.classList.contains("task-list-item");
        api.setActive(Boolean(inList));
      });
    }
  });
  const existingBullListButton = editor.ui.registry.getAll().buttons.bullist;
  existingBullListButton.onSetup = function customBullListOnSetup(api) {
    editor.on("NodeChange", (event) => {
      const parentList = event.parents.find((el2) => el2.nodeName === "LI");
      const inTaskList = parentList && parentList.classList.contains("task-list-item");
      const inUlList = parentList && parentList.parentNode.nodeName === "UL";
      api.setActive(Boolean(inUlList && !inTaskList));
    });
  };
  existingBullListButton.onAction = function customBullListOnAction() {
    if (elementWithinTaskList(editor.selection.getNode())) {
      editor.execCommand("InsertOrderedList", null, {
        "list-item-attributes": { class: null }
      });
    }
    editor.execCommand("InsertUnorderedList", null, {
      "list-item-attributes": { class: null }
    });
  };
  const existingNumListButton = editor.ui.registry.getAll().buttons.numlist;
  existingNumListButton.onAction = function customNumListButtonOnAction() {
    editor.execCommand("InsertOrderedList", null, {
      "list-item-attributes": { class: null }
    });
  };
  editor.on("PreInit", () => {
    editor.parser.addNodeFilter("li", (nodes) => {
      for (const node of nodes) {
        if (node.attributes.map.class === "task-list-item") {
          parseTaskListNode(node);
        }
      }
    });
    editor.serializer.addNodeFilter("li", (nodes) => {
      for (const node of nodes) {
        if (node.attributes.map.class === "task-list-item") {
          serializeTaskListNode(node);
        }
      }
    });
  });
  editor.on("click", (event) => {
    const clickedEl = event.target;
    if (clickedEl.nodeName === "LI" && clickedEl.classList.contains("task-list-item")) {
      handleTaskListItemClick(event, clickedEl, editor);
      event.preventDefault();
    }
  });
}
function getPlugin8() {
  return register9;
}

// resources/js/wysiwyg-tinymce/fixes.js
function handleEmbedAlignmentChanges(editor) {
  function updateClassesForPreview(previewElem) {
    const mediaTarget = previewElem.querySelector("iframe, video");
    if (!mediaTarget) {
      return;
    }
    const alignmentClasses = [...mediaTarget.classList.values()].filter((c) => c.startsWith("align-"));
    const previewAlignClasses = [...previewElem.classList.values()].filter((c) => c.startsWith("align-"));
    previewElem.classList.remove(...previewAlignClasses);
    previewElem.classList.add(...alignmentClasses);
  }
  editor.on("SetContent", () => {
    const previewElems = editor.dom.select("span.mce-preview-object");
    for (const previewElem of previewElems) {
      updateClassesForPreview(previewElem);
    }
  });
  editor.on("FormatApply", (event) => {
    const isAlignment = event.format.startsWith("align");
    const isElement = event.node instanceof editor.dom.doc.defaultView.HTMLElement;
    if (!isElement || !isAlignment || !event.node.matches(".mce-preview-object")) {
      return;
    }
    const realTarget = event.node.querySelector("iframe, video");
    if (realTarget) {
      const className = (editor.formatter.get(event.format)[0]?.classes || [])[0];
      const toAdd = !realTarget.classList.contains(className);
      const wrapperClasses = (event.node.getAttribute("data-mce-p-class") || "").split(" ");
      const wrapperClassesFiltered = wrapperClasses.filter((c) => !c.startsWith("align-"));
      if (toAdd) {
        wrapperClassesFiltered.push(className);
      }
      const classesToApply = wrapperClassesFiltered.join(" ");
      event.node.setAttribute("data-mce-p-class", classesToApply);
      realTarget.setAttribute("class", classesToApply);
      editor.formatter.apply(event.format, {}, realTarget);
      updateClassesForPreview(event.node);
    }
  });
}
function cleanChildAlignment(element) {
  const alignedChildren = element.querySelectorAll('[align],[style*="text-align"],.align-center,.align-left,.align-right');
  for (const child of alignedChildren) {
    child.removeAttribute("align");
    child.style.textAlign = null;
    child.classList.remove("align-center", "align-right", "align-left");
  }
}
function cleanElementDirection(element) {
  const directionChildren = element.querySelectorAll('[dir],[style*="direction"]');
  for (const child of directionChildren) {
    child.removeAttribute("dir");
    child.style.direction = null;
  }
  cleanChildAlignment(element);
  element.style.direction = null;
  element.style.textAlign = null;
  element.removeAttribute("align");
}
function handleTableCellRangeEvents(editor) {
  let selectedCells = [];
  editor.on("TableSelectionChange", (event) => {
    selectedCells = (event.cells || []).map((cell) => cell.dom);
  });
  editor.on("TableSelectionClear", () => {
    selectedCells = [];
  });
  const actionByCommand = {
    // TinyMCE does not seem to do a great job on clearing styles in complex
    // scenarios (like copied word content) when a range of table cells
    // are selected. Here we watch for clear formatting events, so some manual
    // cleanup can be performed.
    RemoveFormat: (cell) => {
      const attrsToRemove = ["class", "style", "width", "height", "align"];
      for (const attr of attrsToRemove) {
        cell.removeAttribute(attr);
      }
    },
    // TinyMCE does not apply direction events to table cell range selections
    // so here we hastily patch in that ability by setting the direction ourselves
    // when a direction event is fired.
    mceDirectionLTR: (cell) => {
      cell.setAttribute("dir", "ltr");
      cleanElementDirection(cell);
    },
    mceDirectionRTL: (cell) => {
      cell.setAttribute("dir", "rtl");
      cleanElementDirection(cell);
    },
    // The "align" attribute can exist on table elements so this clears
    // the attribute, and also clears common child alignment properties,
    // when a text direction action is made for a table cell range.
    JustifyLeft: (cell) => {
      cell.removeAttribute("align");
      cleanChildAlignment(cell);
    }
  };
  actionByCommand.JustifyRight = actionByCommand.JustifyLeft;
  actionByCommand.JustifyCenter = actionByCommand.JustifyLeft;
  actionByCommand.JustifyFull = actionByCommand.JustifyLeft;
  editor.on("ExecCommand", (event) => {
    const action = actionByCommand[event.command];
    if (action) {
      for (const cell of selectedCells) {
        action(cell);
      }
    }
  });
}
function handleTextDirectionCleaning(editor) {
  editor.on("ExecCommand", (event) => {
    const command = event.command;
    if (command !== "mceDirectionLTR" && command !== "mceDirectionRTL") {
      return;
    }
    const blocks = editor.selection.getSelectedBlocks();
    for (const block of blocks) {
      cleanElementDirection(block);
    }
  });
}

// resources/js/wysiwyg-tinymce/config.js
var styleFormats = [
  { title: "Large Header", format: "h2", preview: "color: blue;" },
  { title: "Medium Header", format: "h3" },
  { title: "Small Header", format: "h4" },
  { title: "Tiny Header", format: "h5" },
  {
    title: "Paragraph",
    format: "p",
    exact: true,
    classes: ""
  },
  { title: "Blockquote", format: "blockquote" },
  {
    title: "Callouts",
    items: [
      { title: "Information", format: "calloutinfo" },
      { title: "Success", format: "calloutsuccess" },
      { title: "Warning", format: "calloutwarning" },
      { title: "Danger", format: "calloutdanger" }
    ]
  }
];
var formats = {
  alignleft: { selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,iframe,video", classes: "align-left" },
  aligncenter: { selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,iframe,video", classes: "align-center" },
  alignright: { selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,iframe,video", classes: "align-right" },
  calloutsuccess: { block: "p", exact: true, attributes: { class: "callout success" } },
  calloutinfo: { block: "p", exact: true, attributes: { class: "callout info" } },
  calloutwarning: { block: "p", exact: true, attributes: { class: "callout warning" } },
  calloutdanger: { block: "p", exact: true, attributes: { class: "callout danger" } }
};
var colorMap = [
  "#BFEDD2",
  "",
  "#FBEEB8",
  "",
  "#F8CAC6",
  "",
  "#ECCAFA",
  "",
  "#C2E0F4",
  "",
  "#2DC26B",
  "",
  "#F1C40F",
  "",
  "#E03E2D",
  "",
  "#B96AD9",
  "",
  "#3598DB",
  "",
  "#169179",
  "",
  "#E67E23",
  "",
  "#BA372A",
  "",
  "#843FA1",
  "",
  "#236FA1",
  "",
  "#ECF0F1",
  "",
  "#CED4D9",
  "",
  "#95A5A6",
  "",
  "#7E8C8D",
  "",
  "#34495E",
  "",
  "#000000",
  "",
  "#ffffff",
  ""
];
function filePickerCallback(callback, value, meta) {
  if (meta.filetype === "file") {
    const selector = window.$components.first("entity-selector-popup");
    const selectionText = this.selection.getContent({ format: "text" }).trim();
    selector.show((entity) => {
      callback(entity.link, {
        text: entity.name,
        title: entity.name
      });
    }, {
      initialValue: selectionText,
      searchEndpoint: "/search/entity-selector",
      entityTypes: "page,book,chapter,bookshelf",
      entityPermission: "view"
    });
  }
  if (meta.filetype === "image") {
    const imageManager = window.$components.first("image-manager");
    imageManager.show((image) => {
      callback(image.url, { alt: image.name });
    }, "gallery");
  }
}
function gatherPlugins(options2) {
  const plugins2 = [
    "image",
    "table",
    "link",
    "autolink",
    "fullscreen",
    "code",
    "customhr",
    "autosave",
    "lists",
    "codeeditor",
    "media",
    "imagemanager",
    "about",
    "details",
    "tasklist",
    "tableadditions",
    options2.textDirection === "rtl" ? "directionality" : ""
  ];
  window.tinymce.PluginManager.add("codeeditor", getPlugin());
  window.tinymce.PluginManager.add("customhr", getPlugin3());
  window.tinymce.PluginManager.add("imagemanager", getPlugin4());
  window.tinymce.PluginManager.add("about", getPlugin5());
  window.tinymce.PluginManager.add("details", getPlugin6());
  window.tinymce.PluginManager.add("tasklist", getPlugin8());
  window.tinymce.PluginManager.add("tableadditions", getPlugin7());
  if (options2.drawioUrl) {
    window.tinymce.PluginManager.add("drawio", getPlugin2(options2));
    plugins2.push("drawio");
  }
  return plugins2.filter((plugin) => Boolean(plugin));
}
function addCustomHeadContent(editorDoc) {
  const headContentLines = document.head.innerHTML.split("\n");
  const startLineIndex = headContentLines.findIndex((line) => line.trim() === "<!-- Start: custom user content -->");
  const endLineIndex = headContentLines.findIndex((line) => line.trim() === "<!-- End: custom user content -->");
  if (startLineIndex === -1 || endLineIndex === -1) {
    return;
  }
  const customHeadHtml = headContentLines.slice(startLineIndex + 1, endLineIndex).join("\n");
  const el2 = editorDoc.createElement("div");
  el2.innerHTML = customHeadHtml;
  editorDoc.head.append(...el2.children);
}
function getSetupCallback(options2) {
  return function setupCallback(editor) {
    function editorChange() {
      if (options2.darkMode) {
        editor.contentDocument.documentElement.classList.add("dark-mode");
      }
      window.$events.emit("editor-html-change", "");
    }
    editor.on("ExecCommand change input NodeChange ObjectResized", editorChange);
    listen(editor);
    listenForDragAndPaste(editor, options2);
    editor.on("init", () => {
      editorChange();
      scrollToQueryString(editor);
      window.editor = editor;
      register(editor);
    });
    editor.on("PreInit", () => {
      setupFilters(editor);
    });
    handleEmbedAlignmentChanges(editor);
    handleTableCellRangeEvents(editor);
    handleTextDirectionCleaning(editor);
    window.$events.emitPublic(options2.containerElement, "editor-tinymce::setup", { editor });
    editor.ui.registry.addButton("inlinecode", {
      tooltip: "Inline code",
      icon: "sourcecode",
      onAction() {
        editor.execCommand("mceToggleFormat", false, "code");
      }
    });
  };
}
function getContentStyle(options2) {
  return `
html, body, html.dark-mode {
    background: ${options2.darkMode ? "#222" : "#fff"};
} 
body {
    padding-left: 15px !important;
    padding-right: 15px !important; 
    height: initial !important;
    margin:0!important; 
    margin-left: auto! important;
    margin-right: auto !important;
    overflow-y: hidden !important;
}`.trim().replace("\n", "");
}
function buildForEditor(options2) {
  window.tinymce.addI18n(options2.language, options2.translationMap);
  const version2 = document.querySelector('script[src*="/dist/app.js"]').getAttribute("src").split("?version=")[1];
  return {
    width: "100%",
    height: "100%",
    selector: "#html-editor",
    cache_suffix: `?version=${version2}`,
    content_css: [
      window.baseUrl("/dist/styles.css")
    ],
    branding: false,
    skin: options2.darkMode ? "tinymce-5-dark" : "tinymce-5",
    body_class: "page-content",
    browser_spellcheck: true,
    relative_urls: false,
    language: options2.language,
    directionality: options2.textDirection,
    remove_script_host: false,
    document_base_url: window.baseUrl("/"),
    end_container_on_empty_block: true,
    remove_trailing_brs: false,
    statusbar: false,
    menubar: false,
    paste_data_images: false,
    extended_valid_elements: "pre[*],svg[*],div[drawio-diagram],details[*],summary[*],div[*],li[class|checked|style]",
    automatic_uploads: false,
    custom_elements: "doc-root,code-block",
    valid_children: [
      "-div[p|h1|h2|h3|h4|h5|h6|blockquote|code-block]",
      "+div[pre|img]",
      "-doc-root[doc-root|#text]",
      "-li[details]",
      "+code-block[pre]",
      "+doc-root[p|h1|h2|h3|h4|h5|h6|blockquote|code-block|div|hr]"
    ].join(","),
    plugins: gatherPlugins(options2),
    contextmenu: false,
    toolbar: getPrimaryToolbar(options2),
    content_style: getContentStyle(options2),
    style_formats: styleFormats,
    style_formats_merge: false,
    media_alt_source: false,
    media_poster: false,
    formats,
    table_style_by_css: true,
    table_use_colgroups: true,
    file_picker_types: "file image",
    color_map: colorMap,
    file_picker_callback: filePickerCallback,
    paste_preprocess(plugin, args) {
      const { content } = args;
      if (content.indexOf('<img src="file://') !== -1) {
        args.content = "";
      }
    },
    init_instance_callback(editor) {
      addCustomHeadContent(editor.getDoc());
    },
    setup(editor) {
      registerCustomIcons(editor);
      registerAdditionalToolbars(editor);
      getSetupCallback(options2)(editor);
    }
  };
}

// resources/js/components/wysiwyg-editor-tinymce.js
var WysiwygEditorTinymce = class extends Component {
  setup() {
    this.elem = this.$el;
    this.tinyMceConfig = buildForEditor({
      language: this.$opts.language,
      containerElement: this.elem,
      darkMode: document.documentElement.classList.contains("dark-mode"),
      textDirection: this.$opts.textDirection,
      drawioUrl: this.getDrawIoUrl(),
      pageId: Number(this.$opts.pageId),
      translations: {
        imageUploadErrorText: this.$opts.imageUploadErrorText,
        serverUploadLimitText: this.$opts.serverUploadLimitText
      },
      translationMap: window.editor_translations
    });
    window.$events.emitPublic(this.elem, "editor-tinymce::pre-init", { config: this.tinyMceConfig });
    window.tinymce.init(this.tinyMceConfig).then((editors) => {
      this.editor = editors[0];
    });
  }
  getDrawIoUrl() {
    const drawioUrlElem = document.querySelector("[drawio-url]");
    if (drawioUrlElem) {
      return drawioUrlElem.getAttribute("drawio-url");
    }
    return "";
  }
  /**
   * Get the content of this editor.
   * Used by the parent page editor component.
   * @return {Promise<{html: String}>}
   */
  async getContent() {
    return {
      html: this.editor.getContent()
    };
  }
};

// resources/js/components/wysiwyg-input.ts
var WysiwygInput = class extends Component {
  constructor() {
    super(...arguments);
    __publicField(this, "elem");
    __publicField(this, "wysiwygEditor");
    __publicField(this, "textDirection");
  }
  async setup() {
    this.elem = this.$el;
    this.textDirection = this.$opts.textDirection;
    const wysiwygModule = await window.importVersioned("wysiwyg");
    const container = el("div", { class: "basic-editor-container" });
    this.elem.parentElement?.appendChild(container);
    this.elem.hidden = true;
    this.wysiwygEditor = wysiwygModule.createBasicEditorInstance(container, this.elem.value, {
      darkMode: document.documentElement.classList.contains("dark-mode"),
      textDirection: this.textDirection,
      translations: window.editor_translations
    });
    this.wysiwygEditor.onChange(() => {
      this.wysiwygEditor.getContentAsHtml().then((html) => {
        this.elem.value = html;
      });
    });
  }
};

// resources/js/services/text.ts
function kebabToCamel(kebab) {
  const ucFirst = (word) => word.slice(0, 1).toUpperCase() + word.slice(1);
  const words = kebab.split("-");
  return words[0] + words.slice(1).map(ucFirst).join("");
}
function camelToKebab(camelStr) {
  return camelStr.replace(/[A-Z]/g, (str, offset) => (offset > 0 ? "-" : "") + str.toLowerCase());
}

// resources/js/services/components.ts
function parseRefs(name, element) {
  const refs = {};
  const manyRefs = {};
  const prefix = `${name}@`;
  const selector = `[refs*="${prefix}"]`;
  const refElems = [...element.querySelectorAll(selector)];
  if (element.matches(selector)) {
    refElems.push(element);
  }
  for (const el2 of refElems) {
    const refNames = (el2.getAttribute("refs") || "").split(" ").filter((str) => str.startsWith(prefix)).map((str) => str.replace(prefix, "")).map(kebabToCamel);
    for (const ref of refNames) {
      refs[ref] = el2;
      if (typeof manyRefs[ref] === "undefined") {
        manyRefs[ref] = [];
      }
      manyRefs[ref].push(el2);
    }
  }
  return { refs, manyRefs };
}
function parseOpts(componentName, element) {
  const opts = {};
  const prefix = `option:${componentName}:`;
  for (const { name, value } of element.attributes) {
    if (name.startsWith(prefix)) {
      const optName = name.replace(prefix, "");
      opts[kebabToCamel(optName)] = value || "";
    }
  }
  return opts;
}
var ComponentStore = class {
  constructor() {
    /**
     * A mapping of active components keyed by name, with values being arrays of component
     * instances since there can be multiple components of the same type.
     */
    __publicField(this, "components", {});
    /**
     * A mapping of component class models, keyed by name.
     */
    __publicField(this, "componentModelMap", {});
    /**
     * A mapping of active component maps, keyed by the element components are assigned to.
     */
    __publicField(this, "elementComponentMap", /* @__PURE__ */ new WeakMap());
  }
  /**
   * Initialize a component instance on the given dom element.
   */
  initComponent(name, element) {
    const ComponentModel = this.componentModelMap[name];
    if (ComponentModel === void 0) return;
    let instance = null;
    try {
      instance = new ComponentModel();
      instance.$name = name;
      instance.$el = element;
      const allRefs = parseRefs(name, element);
      instance.$refs = allRefs.refs;
      instance.$manyRefs = allRefs.manyRefs;
      instance.$opts = parseOpts(name, element);
      instance.setup();
    } catch (e) {
      console.error("Failed to create component", e, name, element);
    }
    if (!instance) {
      return;
    }
    if (typeof this.components[name] === "undefined") {
      this.components[name] = [];
    }
    this.components[name].push(instance);
    const elComponents = this.elementComponentMap.get(element) || {};
    elComponents[name] = instance;
    this.elementComponentMap.set(element, elComponents);
  }
  /**
   * Initialize all components found within the given element.
   */
  init(parentElement = document) {
    const componentElems = parentElement.querySelectorAll("[component],[components]");
    for (const el2 of componentElems) {
      const componentNames = `${el2.getAttribute("component") || ""} ${el2.getAttribute("components")}`.toLowerCase().split(" ").filter(Boolean);
      for (const name of componentNames) {
        this.initComponent(name, el2);
      }
    }
  }
  /**
   * Register the given component mapping into the component system.
   * @param {Object<String, ObjectConstructor<Component>>} mapping
   */
  register(mapping) {
    const keys = Object.keys(mapping);
    for (const key of keys) {
      this.componentModelMap[camelToKebab(key)] = mapping[key];
    }
  }
  /**
   * Get the first component of the given name.
   */
  first(name) {
    return (this.components[name] || [null])[0];
  }
  /**
   * Get all the components of the given name.
   */
  get(name) {
    return this.components[name] || [];
  }
  /**
   * Get the first component, of the given name, that's assigned to the given element.
   */
  firstOnElement(element, name) {
    const elComponents = this.elementComponentMap.get(element) || {};
    return elComponents[name] || null;
  }
  allWithinElement(element, name) {
    const components = this.get(name);
    return components.filter((c) => element.contains(c.$el));
  }
};

// resources/js/app.ts
window.__DEV__ = false;
window.baseUrl = baseUrl;
window.importVersioned = importVersioned;
window.$http = new HttpManager();
window.$events = new EventManager();
window.$trans = new Translator();
window.$components = new ComponentStore();
window.$components.register(components_exports);
window.$components.init();
/*! Bundled license information:

sortablejs/modular/sortable.esm.js:
  (**!
   * Sortable 1.15.6
   * @author	RubaXa   <trash@rubaxa.org>
   * @author	owenm    <owen23355@gmail.com>
   * @license MIT
   *)
*/
//# sourceMappingURL=app.js.map
