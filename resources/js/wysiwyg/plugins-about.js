/**
 * @param {Editor} editor
 * @param {String} url
 */
function register(editor, url) {

    const aboutDialog = {
        title: 'About the WYSIWYG Editor',
        url: window.baseUrl('/help/wysiwyg'),
    };

    editor.ui.registry.addButton('about', {
        icon: 'help',
        tooltip: 'About the editor',
        onAction() {
            tinymce.activeEditor.windowManager.openUrl(aboutDialog);
        }
    });

}


/**
 * @param {WysiwygConfigOptions} options
 * @return {register}
 */
export function getPlugin(options) {
    return register;
}