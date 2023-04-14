
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
        const rtfData = this.data.getData( 'text/rtf');
        return rtfData && rtfData.includes('\\trowd');
    }

    /**
     * Get the images that are in the clipboard data.
     * @return {Array<File>}
     */
    getImages() {
        const types = this.data.types;
        const files = this.data.files;
        const images = [];

        for (const type of types) {
            if (type.includes('image')) {
                const item = this.data.getData(type);
                images.push(item.getAsFile());
            }
        }

        for (const file of files) {
            if (file.type.includes('image')) {
                images.push(file);
            }
        }

        return images;
    }
}

export function copyTextToClipboard(text) {
    return navigator.clipboard.writeText(text);
}

export default Clipboard;