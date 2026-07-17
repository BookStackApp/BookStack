/**
 *  Translation Manager
 *  Helps with some of the JavaScript side of translating strings
 *  in a way which fits with Laravel.
 */
export class Translator {

    /**
     * Parse the given translation and find the correct plural option
     * to use. Similar format at Laravel's 'trans_choice' helper.
     */
    choice(translation: string, count: number, replacements: Record<string, string> = {}): string {
        replacements = Object.assign({}, {count: String(count)}, replacements);
        const splitText = translation.split('|');
        const exactCountRegex = /^{([0-9]+)}/;
        const rangeRegex = /^\[([0-9]+),([0-9*]+)]/;
        let result = null;

        for (const t of splitText) {
            // Parse exact matches
            const exactMatches = t.match(exactCountRegex);
            if (exactMatches !== null && Number(exactMatches[1]) === count) {
                result = t.replace(exactCountRegex, '').trim();
                break;
            }

            // Parse range matches
            const rangeMatches = t.match(rangeRegex);
            if (rangeMatches !== null) {
                const rangeStart = Number(rangeMatches[1]);
                if (rangeStart <= count && (rangeMatches[2] === '*' || Number(rangeMatches[2]) >= count)) {
                    result = t.replace(rangeRegex, '').trim();
                    break;
                }
            }
        }

        if (result === null && splitText.length > 1) {
            result = (count === 1) ? splitText[0] : splitText[1];
        }

        if (result === null) {
            result = splitText[0];
        }

        return this.performReplacements(result, replacements);
    }

    protected performReplacements(string: string, replacements: Record<string, string>): string {
        const replaceMatches = string.match(/:(\S+)/g);
        if (replaceMatches === null) {
            return string;
        }

        let updatedString = string;

        for (const match of replaceMatches) {
            const key = match.substring(1);
            if (typeof replacements[key] === 'undefined') {
                continue;
            }
            updatedString = updatedString.replace(match, replacements[key]);
        }

        return updatedString;
    }

}
