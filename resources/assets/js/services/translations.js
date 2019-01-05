/**
 *  Translation Manager
 *  Handles the JavaScript side of translating strings
 *  in a way which fits with Laravel.
 */
class Translator {

    /**
     * Create an instance, Passing in the required translations
     * @param translations
     */
    constructor(translations) {
        this.store = translations;
    }

    /**
     * Get a translation, Same format as laravel's 'trans' helper
     * @param key
     * @param replacements
     * @returns {*}
     */
    get(key, replacements) {
        let text = this.getTransText(key);
        return this.performReplacements(text, replacements);
    }

    /**
     * Get pluralised text, Dependant on the given count.
     * Same format at laravel's 'trans_choice' helper.
     * @param key
     * @param count
     * @param replacements
     * @returns {*}
     */
    getPlural(key, count, replacements) {
        let text = this.getTransText(key);
        let splitText = text.split('|');
        let result = null;
        let exactCountRegex = /^{([0-9]+)}/;
        let rangeRegex = /^\[([0-9]+),([0-9*]+)]/;

        for (let i = 0, len = splitText.length; i < len; i++) {
            let t = splitText[i];

            // Parse exact matches
            let exactMatches = t.match(exactCountRegex);
            if (exactMatches !== null && Number(exactMatches[1]) === count) {
                result = t.replace(exactCountRegex, '').trim();
                break;
            }

            // Parse range matches
            let rangeMatches = t.match(rangeRegex);
            if (rangeMatches !== null) {
                let rangeStart = Number(rangeMatches[1]);
                if (rangeStart <= count && (rangeMatches[2] === '*' || Number(rangeMatches[2]) >= count)) {
                    result = t.replace(rangeRegex, '').trim();
                    break;
                }
            }
        }

        if (result === null && splitText.length > 1) {
            result = (count === 1) ? splitText[0] : splitText[1];
        }

        if (result === null) result = splitText[0];
        return this.performReplacements(result, replacements);
    }

    /**
     * Fetched translation text from the store for the given key.
     * @param key
     * @returns {String|Object}
     */
    getTransText(key) {
        let splitKey = key.split('.');
        let value = splitKey.reduce((a, b) => {
            return a !== undefined ? a[b] : a;
        }, this.store);

        if (value === undefined) {
            console.log(`Translation with key "${key}" does not exist`);
            value = key;
        }

        return value;
    }

    /**
     * Perform replacements on a string.
     * @param {String} string
     * @param {Object} replacements
     * @returns {*}
     */
    performReplacements(string, replacements) {
        if (!replacements) return string;
        let replaceMatches = string.match(/:([\S]+)/g);
        if (replaceMatches === null) return string;
        replaceMatches.forEach(match => {
            let key = match.substring(1);
            if (typeof replacements[key] === 'undefined') return;
            string = string.replace(match, replacements[key]);
        });
        return string;
    }

}

export default Translator;
