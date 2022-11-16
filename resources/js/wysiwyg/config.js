import {register as registerShortcuts} from "./shortcuts";
import {listen as listenForCommonEvents} from "./common-events";
import {scrollToQueryString} from "./scrolling";
import {listenForDragAndPaste} from "./drop-paste-handling";
import {getPrimaryToolbar, registerAdditionalToolbars} from "./toolbars";
import {registerCustomIcons} from "./icons";

import {getPlugin as getCodeeditorPlugin} from "./plugin-codeeditor";
import {getPlugin as getDrawioPlugin} from "./plugin-drawio";
import {getPlugin as getCustomhrPlugin} from "./plugins-customhr";
import {getPlugin as getImagemanagerPlugin} from "./plugins-imagemanager";
import {getPlugin as getAboutPlugin} from "./plugins-about";
import {getPlugin as getDetailsPlugin} from "./plugins-details";
import {getPlugin as getTasklistPlugin} from "./plugins-tasklist";

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
    alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-left'},
    aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-center'},
    alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes: 'align-right'},
    calloutsuccess: {block: 'p', exact: true, attributes: {class: 'callout success'}},
    calloutinfo: {block: 'p', exact: true, attributes: {class: 'callout info'}},
    calloutwarning: {block: 'p', exact: true, attributes: {class: 'callout warning'}},
    calloutdanger: {block: 'p', exact: true, attributes: {class: 'callout danger'}}
};

const color_map = [
    '#BFEDD2', '',
    '#FBEEB8', '',
    '#F8CAC6', '',
    '#ECCAFA', '',
    '#C2E0F4', '',

    '#2DC26B', '',
    '#F1C40F', '',
    '#E03E2D', '',
    '#B96AD9', '',
    '#3598DB', '',

    '#169179', '',
    '#E67E23', '',
    '#BA372A', '',
    '#843FA1', '',
    '#236FA1', '',

    '#ECF0F1', '',
    '#CED4D9', '',
    '#95A5A6', '',
    '#7E8C8D', '',
    '#34495E', '',

    '#000000', '',
    '#ffffff', ''
];

function file_picker_callback(callback, value, meta) {

    // field_name, url, type, win
    if (meta.filetype === 'file') {
        /** @type {EntitySelectorPopup} **/
        const selector = window.$components.first('entity-selector-popup');
        selector.show(entity => {
            callback(entity.link, {
                text: entity.name,
                title: entity.name,
            });
        });
    }

    if (meta.filetype === 'image') {
        // Show image manager
        /** @type {ImageManager} **/
        const imageManager = window.$components.first('image-manager');
        imageManager.show(function (image) {
            callback(image.url, {alt: image.name});
        }, 'gallery');
    }

}

/**
 * @param {WysiwygConfigOptions} options
 * @return {string[]}
 */
function gatherPlugins(options) {
    const plugins = [
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
        options.textDirection === 'rtl' ? 'directionality' : '',
    ];

    window.tinymce.PluginManager.add('codeeditor', getCodeeditorPlugin(options));
    window.tinymce.PluginManager.add('customhr', getCustomhrPlugin(options));
    window.tinymce.PluginManager.add('imagemanager', getImagemanagerPlugin(options));
    window.tinymce.PluginManager.add('about', getAboutPlugin(options));
    window.tinymce.PluginManager.add('details', getDetailsPlugin(options));
    window.tinymce.PluginManager.add('tasklist', getTasklistPlugin(options));

    if (options.drawioUrl) {
        window.tinymce.PluginManager.add('drawio', getDrawioPlugin(options));
        plugins.push('drawio');
    }

    return plugins.filter(plugin => Boolean(plugin));
}

/**
 * Fetch custom HTML head content from the parent page head into the editor.
 */
function fetchCustomHeadContent() {
    const headContentLines = document.head.innerHTML.split("\n");
    const startLineIndex = headContentLines.findIndex(line => line.trim() === '<!-- Start: custom user content -->');
    const endLineIndex = headContentLines.findIndex(line => line.trim() === '<!-- End: custom user content -->');
    if (startLineIndex === -1 || endLineIndex === -1) {
        return ''
    }
    return headContentLines.slice(startLineIndex + 1, endLineIndex).join('\n');
}

/**
 * Setup a serializer filter for <br> tags to ensure they're not rendered
 * within code blocks and that we use newlines there instead.
 * @param {Editor} editor
 */
function setupBrFilter(editor) {
    editor.serializer.addNodeFilter('br', function(nodes) {
        for (const node of nodes) {
            if (node.parent && node.parent.name === 'code') {
                const newline = tinymce.html.Node.create('#text');
                newline.value = '\n';
                node.replace(newline);
            }
        }
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
        listenForDragAndPaste(editor, options);

        editor.on('init', () => {
            editorChange();
            scrollToQueryString(editor);
            window.editor = editor;
            registerShortcuts(editor);
        });

        editor.on('PreInit', () => {
            setupBrFilter(editor);
        });

        function editorChange() {
            const content = editor.getContent();
            if (options.darkMode) {
                editor.contentDocument.documentElement.classList.add('dark-mode');
            }
            window.$events.emit('editor-html-change', content);
        }

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
 */
function getContentStyle(options) {
    return `
html, body, html.dark-mode {
    background: ${options.darkMode ? '#222' : '#fff'};
} 
body {
    padding-left: 15px !important;
    padding-right: 15px !important; 
    height: initial !important;
    margin:0!important; 
    margin-left: auto! important;
    margin-right: auto !important;
    overflow-y: hidden !important;
}`.trim().replace('\n', '');
}

/**
 * @param {WysiwygConfigOptions} options
 * @return {Object}
 */
export function build(options) {

    // Set language
    window.tinymce.addI18n(options.language, options.translationMap);

    // BookStack Version
    const version = document.querySelector('script[src*="/dist/app.js"]').getAttribute('src').split('?version=')[1];

    // Return config object
    return {
        width: '100%',
        height: '100%',
        selector: '#html-editor',
        cache_suffix: '?version=' + version,
        content_css: [
            window.baseUrl('/dist/styles.css'),
        ],
        branding: false,
        skin: options.darkMode ? 'tinymce-5-dark' : 'tinymce-5',
        body_class: 'page-content',
        browser_spellcheck: true,
        relative_urls: false,
        language: options.language,
        directionality: options.textDirection,
        remove_script_host: false,
        document_base_url: window.baseUrl('/'),
        end_container_on_empty_block: true,
        remove_trailing_brs: false,
        statusbar: false,
        menubar: false,
        paste_data_images: false,
        extended_valid_elements: 'pre[*],svg[*],div[drawio-diagram],details[*],summary[*],div[*],li[class|checked|style]',
        automatic_uploads: false,
        custom_elements: 'doc-root,code-block',
        valid_children: [
            "-div[p|h1|h2|h3|h4|h5|h6|blockquote|code-block]",
            "+div[pre|img]",
            "-doc-root[doc-root|#text]",
            "-li[details]",
            "+code-block[pre]",
            "+doc-root[p|h1|h2|h3|h4|h5|h6|blockquote|code-block|div]"
        ].join(','),
        plugins: gatherPlugins(options),
        contextmenu: false,
        toolbar: getPrimaryToolbar(options),
        content_style: getContentStyle(options),
        style_formats,
        style_formats_merge: false,
        media_alt_source: false,
        media_poster: false,
        formats,
        table_style_by_css: true,
        table_use_colgroups: true,
        file_picker_types: 'file image',
        color_map,
        file_picker_callback,
        paste_preprocess(plugin, args) {
            const content = args.content;
            if (content.indexOf('<img src="file://') !== -1) {
                args.content = '';
            }
        },
        init_instance_callback(editor) {
            const head = editor.getDoc().querySelector('head');
            head.innerHTML += fetchCustomHeadContent();
        },
        setup(editor) {
            registerCustomIcons(editor);
            registerAdditionalToolbars(editor, options);
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