/**
 * Convert a kebab-case string to camelCase
 * @param {String} kebab
 * @returns {string}
 */
export function kebabToCamel(kebab) {
    const ucFirst = (word) => word.slice(0,1).toUpperCase() + word.slice(1);
    const words = kebab.split('-');
    return words[0] + words.slice(1).map(ucFirst).join('');
}

/**
 * Convert a camelCase string to a kebab-case string.
 * @param {String} camelStr
 * @returns {String}
 */
export function camelToKebab(camelStr) {
    return camelStr.replace(/[A-Z]/g, (str, offset) =>  (offset > 0 ? '-' : '') + str.toLowerCase());
}