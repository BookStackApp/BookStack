export class Clipboard {

    protected data: DataTransfer;

    constructor(clipboardData: DataTransfer) {
        this.data = clipboardData;
    }

    /**
     * Check if the clipboard has any items.
     */
    hasItems(): boolean {
        return Boolean(this.data) && Boolean(this.data.types) && this.data.types.length > 0;
    }

    /**
     * Check if the given event has tabular-looking data in the clipboard.
     */
    containsTabularData(): boolean {
        const rtfData = this.data.getData('text/rtf');
        return !!rtfData && rtfData.includes('\\trowd');
    }

    /**
     * Get the images that are in the clipboard data.
     */
    getImages(): File[] {
        return this.getFiles().filter(f => f.type.includes('image'));
    }

    /**
     * Get the files included in the clipboard data.
     */
    getFiles(): File[] {
        const {files} = this.data;
        return [...files];
    }
}

export async function copyTextToClipboard(text: string) {
    if (window.isSecureContext && navigator.clipboard) {
        await navigator.clipboard.writeText(text);
        return;
    }

    // Backup option where we can't use the navigator.clipboard API
    const tempInput = document.createElement('textarea');
    tempInput.setAttribute('style', 'position: absolute; left: -1000px; top: -1000px;');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
}
