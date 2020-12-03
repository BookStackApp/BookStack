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
        this.store = new Map();
        this.parseTranslations();
    }

    /**
     * Parse translations out of the page and place into the store.
     */
    parseTranslations() {
        const translationMetaTags = document.querySelectorAll('meta[name="translation"]');
        for (let tag of translationMetaTags) {
            const key = tag.getAttribute('key');
            const value = tag.getAttribute('value');
            this.store.set(key, value);
        }
    }

    /**
     * Get a translation, Same format as laravel's 'trans' helper
     * @param key
     * @param replacements
     * @returns {*}
     */
    get(key, replacements) {
        const text = this.getTransText(key);
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
        const text = this.getTransText(key);
        return this.parsePlural(text, count, replacements);
    }

    /**
     * Parse the given translation and find the correct plural option
     * to use. Similar format at laravel's 'trans_choice' helper.
     * @param {String} translation
     * @param {Number} count
     * @param {Object} replacements
     * @returns {String}
     */
    parsePlural(translation, count, replacements) {
        const splitText = translation.split('|');
        const exactCountRegex = /^{([0-9]+)}/;
        const rangeRegex = /^\[([0-9]+),([0-9*]+)]/;
        let result = null;

        for (let t of splitText) {
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

    /**
     * Fetched translation text from the store for the given key.
     * @param key
     * @returns {String|Object}
     */
    getTransText(key) {
        const value = this.store.get(key);

        if (value === undefined) {
            console.warn(`Translation with key "${key}" does not exist`);
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
        const replaceMatches = string.match(/:([\S]+)/g);
        if (replaceMatches === null) return string;
        replaceMatches.forEach(match => {
            const key = match.substring(1);
            if (typeof replacements[key] === 'undefined') return;
            string = string.replace(match, replacements[key]);
        });
        return string;
    }

}

export default Translator;
