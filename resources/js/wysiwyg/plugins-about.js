/**
 * @param {Editor} editor
 */
function register(editor) {
    const aboutDialog = {
        title: 'About the WYSIWYG Editor',
        url: window.baseUrl('/help/wysiwyg'),
    };

    editor.ui.registry.addButton('about', {
        icon: 'help',
        tooltip: 'About the editor',
        onAction() {
            window.tinymce.activeEditor.windowManager.openUrl(aboutDialog);
        },
    });
}

/**
 * @return {register}
 */
export function getPlugin() {
    return register;
}
