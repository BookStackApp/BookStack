/**
 * @param {Editor} editor
 */
function register(editor) {
    const aboutDialog = {
        title: 'Editor Information & Shortcuts',
        url: window.baseUrl('/help/tinymce'),
    };

    editor.ui.registry.addButton('about', {
        icon: 'help',
        tooltip: 'Editor information & shortcuts',
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
