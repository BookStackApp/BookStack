import {register as registerShortcuts} from "./shortcuts";
import {listen as listenForCommonEvents} from "./common-events";
import {scrollToQueryString, fixScrollForMobile} from "./scrolling";
import {listenForDragAndPaste} from "./drop-paste-handling";

import {getPlugin as getCodeeditorPlugin} from "./plugin-codeeditor";
import {getPlugin as getDrawioPlugin} from "./plugin-drawio";
import {getPlugin as getCustomhrPlugin} from "./plugins-customhr";
import {getPlugin as getImagemanagerPlugin} from "./plugins-imagemanager";
import {getPlugin as getAboutPlugin} from "./plugins-about";

const style_formats = [
    {title: "Large Header", format: "h2", preview: 'color: blue;'},
    {title: "Medium Header", format: "h3"},
    {title: "Small Header", format: "h4"},
    {title: "Tiny Header", format: "h5"},
    {title: "Paragraph", format: "p", exact: true, classes: ''},
    {title: "Blockquote", format: "blockquote"},
    {
        title: "Callouts", items: [
            {title: "Information", format: 'calloutinfo'},
            {title: "Success", format: 'calloutsuccess'},
            {title: "Warning", format: 'calloutwarning'},
            {title: "Danger", format: 'calloutdanger'}
        ]
    },
];

const formats = {
    codeeditor: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div'},
    alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-left'},
    aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-center'},
    alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-right'},
    calloutsuccess: {block: 'p', exact: true, attributes: {class: 'callout success'}},
    calloutinfo: {block: 'p', exact: true, attributes: {class: 'callout info'}},
    calloutwarning: {block: 'p', exact: true, attributes: {class: 'callout warning'}},
    calloutdanger: {block: 'p', exact: true, attributes: {class: 'callout danger'}}
};

function file_picker_callback(callback, value, meta) {

    // field_name, url, type, win
    if (meta.filetype === 'file') {
        window.EntitySelectorPopup.show(entity => {
            callback(entity.link, {
                text: entity.name,
                title: entity.name,
            });
        });
    }

    if (meta.filetype === 'image') {
        // Show image manager
        window.ImageManager.show(function (image) {
            callback(image.url, {alt: image.name});
        }, 'gallery');
    }

}

/**
 * @param {WysiwygConfigOptions} options
 * @return {{toolbar: string, groupButtons: Object<string, Object>}}
 */
function buildToolbar(options) {
    const textDirPlugins = options.textDirection === 'rtl' ? 'ltr rtl' : '';

    const groupButtons = {
        formatoverflow: {
            icon: 'more-drawer',
            tooltip: 'More',
            items: 'strikethrough superscript subscript inlinecode removeformat'
        },
        listoverflow: {
            icon: 'more-drawer',
            tooltip: 'More',
            items: 'outdent indent'
        },
        insertoverflow: {
            icon: 'more-drawer',
            tooltip: 'More',
            items: 'hr codeeditor drawio media'
        }
    };

    const toolbar = [
        'undo redo',
        'styleselect',
        'bold italic underline formatoverflow',
        'forecolor backcolor',
        'alignleft aligncenter alignright alignjustify',
        'bullist numlist listoverflow',
        textDirPlugins,
        'link table imagemanager-insert insertoverflow',
        'code about fullscreen'
    ];

    return {
        toolbar: toolbar.filter(row => Boolean(row)).join(' | '),
        groupButtons,
    };
}

/**
 * @param {WysiwygConfigOptions} options
 * @return {string}
 */
function gatherPlugins(options) {
    const plugins = [
        "image",
        "imagetools",
        "table",
        "paste",
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
        options.textDirection === 'rtl' ? 'directionality' : '',
    ];

    window.tinymce.PluginManager.add('codeeditor', getCodeeditorPlugin(options));
    window.tinymce.PluginManager.add('customhr', getCustomhrPlugin(options));
    window.tinymce.PluginManager.add('imagemanager', getImagemanagerPlugin(options));
    window.tinymce.PluginManager.add('about', getAboutPlugin(options));

    if (options.drawioUrl) {
        window.tinymce.PluginManager.add('drawio', getDrawioPlugin(options));
        plugins.push('drawio');
    }

    return plugins.filter(plugin => Boolean(plugin)).join(' ');
}

/**
 * Load custom HTML head content from the settings into the editor.
 * TODO: We should be able to get this from current parent page?
 * @param {Editor} editor
 */
function loadCustomHeadContent(editor) {
    window.$http.get(window.baseUrl('/custom-head-content')).then(resp => {
        if (!resp.data) return;
        let head = editor.getDoc().querySelector('head');
        head.innerHTML += resp.data;
    });
}

/**
 * @param {WysiwygConfigOptions} options
 * @return {function(Editor)}
 */
function getSetupCallback(options) {
    return function(editor) {
        editor.on('ExecCommand change input NodeChange ObjectResized', editorChange);
        listenForCommonEvents(editor);
        registerShortcuts(editor);
        listenForDragAndPaste(editor, options);

        editor.on('init', () => {
            editorChange();
            scrollToQueryString(editor);
            fixScrollForMobile(editor);
            window.editor = editor;
        });

        function editorChange() {
            const content = editor.getContent();
            if (options.darkMode) {
                editor.contentDocument.documentElement.classList.add('dark-mode');
            }
            window.$events.emit('editor-html-change', content);
        }

        // TODO - Update to standardise across both editors
        // Use events within listenForBookStackEditorEvents instead (Different event signature)
        window.$events.listen('editor-html-update', html => {
            editor.setContent(html);
            editor.selection.select(editor.getBody(), true);
            editor.selection.collapse(false);
            editorChange(html);
        });

        // Custom handler hook
        window.$events.emitPublic(options.containerElement, 'editor-tinymce::setup', {editor});

        // Inline code format button
        editor.ui.registry.addButton('inlinecode', {
            tooltip: 'Inline code',
            icon: 'sourcecode',
            onAction() {
                editor.execCommand('mceToggleFormat', false, 'code');
            }
        })
    }
}

/**
 * @param {WysiwygConfigOptions} options
 * @return {Object}
 */
export function build(options) {

    // Set language
    window.tinymce.addI18n(options.language, options.translationMap);

    const {toolbar, groupButtons: toolBarGroupButtons} = buildToolbar(options);

    // Return config object
    return {
        width: '100%',
        height: '100%',
        selector: '#html-editor',
        content_css: [
            window.baseUrl('/dist/styles.css'),
        ],
        branding: false,
        skin: options.darkMode ? 'oxide-dark' : 'oxide',
        body_class: 'page-content',
        browser_spellcheck: true,
        relative_urls: false,
        language: options.language,
        directionality: options.textDirection,
        remove_script_host: false,
        document_base_url: window.baseUrl('/'),
        end_container_on_empty_block: true,
        statusbar: false,
        menubar: false,
        paste_data_images: false,
        extended_valid_elements: 'pre[*],svg[*],div[drawio-diagram]',
        automatic_uploads: false,
        valid_children: "-div[p|h1|h2|h3|h4|h5|h6|blockquote],+div[pre],+div[img]",
        plugins: gatherPlugins(options),
        imagetools_toolbar: 'imageoptions',
        contextmenu: false,
        toolbar: toolbar,
        content_style: `html, body, html.dark-mode {background: ${options.darkMode ? '#222' : '#fff'};} body {padding-left: 15px !important; padding-right: 15px !important; margin:0!important; margin-left:auto!important;margin-right:auto!important;}`,
        style_formats,
        style_formats_merge: false,
        media_alt_source: false,
        media_poster: false,
        formats,
        file_picker_types: 'file image',
        file_picker_callback,
        paste_preprocess(plugin, args) {
            let content = args.content;
            if (content.indexOf('<img src="file://') !== -1) {
                args.content = '';
            }
        },
        init_instance_callback(editor) {
            loadCustomHeadContent(editor);
        },
        setup(editor) {
            for (const [key, config] of Object.entries(toolBarGroupButtons)) {
                editor.ui.registry.addGroupToolbarButton(key, config);
            }
            getSetupCallback(options)(editor);
        },
    };
}

/**
 * @typedef {Object} WysiwygConfigOptions
 * @property {Element} containerElement
 * @property {string} language
 * @property {boolean} darkMode
 * @property {string} textDirection
 * @property {string} drawioUrl
 * @property {int} pageId
 * @property {Object} translations
 * @property {Object} translationMap
 */