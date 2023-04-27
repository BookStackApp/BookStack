export class Clipboard {

    /**
     * Constructor
     * @param {DataTransfer} clipboardData
     */
    constructor(clipboardData) {
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
     * @return {boolean}
     */
    containsTabularData() {
        const rtfData = this.data.getData('text/rtf');
        return rtfData && rtfData.includes('\\trowd');
    }

    /**
     * Get the images that are in the clipboard data.
     * @return {Array<File>}
     */
    getImages() {
        const {types} = this.data;
        const images = [];

        for (const type of types) {
            if (type.includes('image')) {
                const item = this.data.getData(type);
                images.push(item.getAsFile());
            }
        }

        const imageFiles = this.getFiles().filter(f => f.type.includes('image'));
        images.push(...imageFiles);

        return images;
    }

    /**
     * Get the files included in the clipboard data.
     * @return {File[]}
     */
    getFiles() {
        const {files} = this.data;
        return [...files];
    }

}

export async function copyTextToClipboard(text) {
    if (window.isSecureContext && navigator.clipboard) {
        await navigator.clipboard.writeText(text);
        return;
    }

    // Backup option where we can't use the navigator.clipboard API
    const tempInput = document.createElement('textarea');
    tempInput.style = 'position: absolute; left: -1000px; top: -1000px;';
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
}
