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
        let splitKey = key.split('.');
        let value = splitKey.reduce((a, b) => {
            return a != undefined ? a[b] : a;
        }, this.store);

        if (value === undefined) {
            console.log(`Translation with key "${key}" does not exist`);
            value = key;
        }

        if (replacements === undefined) return value;

        let replaceMatches = value.match(/:([\S]+)/g);
        if (replaceMatches === null) return value;
        replaceMatches.forEach(match => {
            let key = match.substring(1);
            if (typeof replacements[key] === 'undefined') return;
            value = value.replace(match, replacements[key]);
        });
        return value;
    }

}

export default Translator
